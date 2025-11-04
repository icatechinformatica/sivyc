<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Add5mWindowDedupToCrosschexLive extends Migration
{
    public function up()
    {
        // 1) workno desde JSON (GENERATED OK: inmutable)
        DB::statement("
          ALTER TABLE crosschex_live
          ADD COLUMN IF NOT EXISTS workno text
          GENERATED ALWAYS AS (
            COALESCE(
              payload->'records'->0->'employee'->>'workno',
              payload->'employee'->>'workno'
            )
          ) STORED
        ");

        // 2) check_time_utc normal (se llenará por trigger)
        DB::statement("
          ALTER TABLE crosschex_live
          ADD COLUMN IF NOT EXISTS check_time_utc timestamptz NULL
        ");

        // 3) window_5m_id normal (se llenará por trigger)
        DB::statement("
          ALTER TABLE crosschex_live
          ADD COLUMN IF NOT EXISTS window_5m_id BIGINT NULL
        ");

        // 4) Función trigger para rellenar check_time_utc y window_5m_id
        DB::statement("
          CREATE OR REPLACE FUNCTION crosschex_live_fill_times()
          RETURNS trigger
          LANGUAGE plpgsql
          AS $$
          DECLARE
            v_txt text;
          BEGIN
            -- Derivar check_time_utc del payload si viene NULL
            IF NEW.check_time_utc IS NULL THEN
              v_txt := COALESCE(
                NULLIF(NEW.payload->'records'->0->>'check_time',''),
                NULLIF(NEW.payload->>'check_time','')
              );
              IF v_txt IS NOT NULL THEN
                NEW.check_time_utc := v_txt::timestamptz; -- parse ISO8601 (con offset)
              END IF;
            END IF;

            -- Calcular SIEMPRE la ventana de 5 minutos en UTC
            IF NEW.check_time_utc IS NOT NULL THEN
              NEW.window_5m_id := floor(extract(epoch from NEW.check_time_utc) / 300)::bigint;
            ELSE
              NEW.window_5m_id := NULL;
            END IF;

            RETURN NEW;
          END
          $$;
        ");

        // 5) Trigger BEFORE INSERT/UPDATE (idempotente)
        DB::statement("
          DO $$
          BEGIN
            IF NOT EXISTS (
              SELECT 1 FROM pg_trigger WHERE tgname = 'trg_crosschex_live_fill_times'
            ) THEN
              CREATE TRIGGER trg_crosschex_live_fill_times
              BEFORE INSERT OR UPDATE ON crosschex_live
              FOR EACH ROW
              EXECUTE FUNCTION crosschex_live_fill_times();
            END IF;
          END
          $$;
        ");

        // 6) Backfill de existentes (una sola pasada)
        // 6.a) check_time_utc desde JSON donde esté NULL
        DB::statement("
          UPDATE crosschex_live
          SET check_time_utc = COALESCE(
            NULLIF(payload->'records'->0->>'check_time','')::timestamptz,
            NULLIF(payload->>'check_time','')::timestamptz
          )
          WHERE check_time_utc IS NULL
        ");

        // 6.b) window_5m_id a partir de check_time_utc
        DB::statement("
          UPDATE crosschex_live
          SET window_5m_id = floor(extract(epoch from check_time_utc) / 300)::bigint
          WHERE check_time_utc IS NOT NULL
            AND window_5m_id IS NULL
        ");

        // 7) Limpieza de duplicados (conserva el más antiguo por id)
        DB::statement("
          WITH d AS (
            SELECT ctid,
                   row_number() OVER (
                     PARTITION BY workno, window_5m_id
                     ORDER BY id
                   ) AS rn
            FROM crosschex_live
            WHERE workno IS NOT NULL
              AND window_5m_id IS NOT NULL
          )
          DELETE FROM crosschex_live
          WHERE ctid IN (SELECT ctid FROM d WHERE rn > 1)
        ");

        // 8) Índice único parcial para evitar duplicados futuros
        DB::statement("
          CREATE UNIQUE INDEX IF NOT EXISTS crosschex_live_unique_workno_5m
          ON crosschex_live (workno, window_5m_id)
          WHERE workno IS NOT NULL AND window_5m_id IS NOT NULL
        ");
    }

    public function down()
    {
        // Borrar índice único
        DB::statement("DROP INDEX IF EXISTS crosschex_live_unique_workno_5m");

        // Borrar trigger y función
        DB::statement("
          DO $$
          BEGIN
            IF EXISTS (SELECT 1 FROM pg_trigger WHERE tgname = 'trg_crosschex_live_fill_times') THEN
              DROP TRIGGER trg_crosschex_live_fill_times ON crosschex_live;
            END IF;
          END
          $$;
        ");
        DB::statement("DROP FUNCTION IF EXISTS crosschex_live_fill_times()");

        // Quitar columnas (opcional mantener workno)
        DB::statement("ALTER TABLE crosschex_live DROP COLUMN IF EXISTS window_5m_id");
        DB::statement("ALTER TABLE crosschex_live DROP COLUMN IF EXISTS check_time_utc");
        // Si quieres revertir también workno, descomenta:
        // DB::statement("ALTER TABLE crosschex_live DROP COLUMN IF EXISTS workno");
    }
}
