<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Agenda;
use Carbon\Carbon;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'tbl_grupos';

    protected $fillable = [
        'id_unidad',
        'id_instructor',
        'clave_grupo',
        'id_modalidad',
        'id_curso',
        'id_organismo_publico',
        'id_municipio',
        'id_localidad',
        'programa',
        'efisico',
        'cespecifico',
        'fecha_cespecifico',
        'id_tipo_exoneracion',
        'medio_virtual',
        'link_virtual',
        'id_cerss',
        'asis_finalizado',
        'calif_finalizado',
        'num_revision',
        'num_revision_arc02',
        'evidencia_fotografica',
        'vb_dg',
        'id_imparticion',
        'organismo_representante',
        'organismo_telefono_representante',
        'nombre_lugar',
        'colonia',
        'calle_numero',
        'codigo_postal',
        'referencias',
        'id_servicio',
    'seccion_captura',
    ];

    public $timestamps = false;

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'id_instructor');
    }

    public function curso()
    {
        // El modelo definido es App\Models\curso (minúsculas). Ajustamos la referencia.
        return $this->belongsTo(curso::class, 'id_curso');
    }

    public function estatus()
    {
        return $this->belongsToMany(Estatus::class, 'tbl_grupo_estatus', 'id_grupo', 'id_estatus')->withPivot('observaciones', 'memorandum', 'ruta_documento', 'fecha_cambio', 'es_ultimo_estatus');
    }

    /**
     * Obtener el estatus actual (más reciente) del grupo
     */
    public function estatusActual()
    {
        return $this->estatus()
            ->orderBy('tbl_grupo_estatus.updated_at', 'desc')
            ->orderBy('tbl_grupo_estatus.created_at', 'desc')
            ->first();
    }

    /**
     * Verificar si el grupo tiene un estatus específico
     */
    public function tieneEstatus($nombreEstatus)
    {
        return $this->estatus()->where('estatus', $nombreEstatus)->exists();
    }

    public function tipoImparticion()
    {
        return $this->belongsTo(ImparticionCurso::class, 'id_tipo_imparticion');
    }

    public function modalidad()
    {
        return $this->belongsTo(ModalidadCurso::class, 'id_modalidad_curso');
    }

    public function fechasAgenda()
    {
        return $this->hasMany(Agenda::class, 'id_grupo');
    }

    public function horasTotales()
    {
        // Para cada evento, si abarca varios días, contar solo las horas por día
        $minutos = $this->fechasAgenda()
            ->get()
            ->reduce(function ($carry, $agenda) {
                if (empty($agenda->fecha_inicio) || empty($agenda->fecha_fin) || empty($agenda->hora_inicio) || empty($agenda->hora_fin)) {
                    return $carry;
                }

                $horaIni = Carbon::createFromFormat('H:i:s', $agenda->hora_inicio);
                $horaFin = Carbon::createFromFormat('H:i:s', $agenda->hora_fin);
                if ($horaFin->lessThanOrEqualTo($horaIni)) {
                    return $carry; // Horario inválido
                }

                $minPorDia = $horaIni->diffInMinutes($horaFin);

                $fIni = Carbon::parse($agenda->fecha_inicio)->startOfDay();
                $fFin = Carbon::parse($agenda->fecha_fin)->startOfDay();
                if ($fFin->lessThan($fIni)) {
                    return $carry; // Rango inválido
                }

                // Días inclusivos
                $dias = $fIni->diffInDays($fFin) + 1;
                return $carry + ($dias * $minPorDia);
            }, 0);

        return $minutos / 60; // Horas totales (puede ser decimal)
    }

    public function fechasSeleccionadas()
    {
        return $this->hasMany(Agenda::class, 'id_grupo')
            ->whereNotNull('fecha_inicio')
            ->whereNotNull('fecha_fin')
            ->whereNotNull('hora_inicio')
            ->whereNotNull('hora_fin');
    }

    public function contarFechasSeleccionadas()
    {
        // Suma de días (inclusive) por cada periodo
        return $this->fechasAgenda()->get()->reduce(function ($carry, $agenda) {
            if (empty($agenda->fecha_inicio) || empty($agenda->fecha_fin)) return $carry;
            $fIni = Carbon::parse($agenda->fecha_inicio)->startOfDay();
            $fFin = Carbon::parse($agenda->fecha_fin)->startOfDay();
            if ($fFin->lessThan($fIni)) return $carry;
            return $carry + ($fIni->diffInDays($fFin) + 1);
        }, 0);
    }

    /**
     * Relación N:M con Alumnos mediante la tabla pivote tbl_alumno_grupo
     */
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'tbl_alumno_grupo', 'grupo_id', 'alumno_id')
            ->withPivot('costo', 'comprobante_pago', 'tinscripcion', 'abrinscri', 'folio_pago', 'fecha_pago', 'id_folio');
    }
}
