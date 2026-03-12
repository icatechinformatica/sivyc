<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VwCrosschexRecord extends Model
{
    protected $connection = 'pgsql'; // tu conexión a PG
    protected $table      = 'vw_crosschex_records';

    protected $primaryKey = 'history_id';
    public $incrementing = false;
    public $timestamps   = false;

    protected $fillable = [
        'history_id',
        'cod_unidad',
        'id_organismo',
        'unidad',
        'workno',
        'first_name',
        'last_name',
        'check_time_utc',
        'check_time_local',
        'check_type',
    ];

    // Muy importante para que $record->check_time_local sea Carbon
    protected $casts = [
        'check_time_local' => 'datetime',
        'check_time_utc'   => 'datetime',
    ];

    // 👇 Aquí le decimos a Eloquent que queremos exponer el atributo "puesto"
    protected $appends = ['puesto'];

    /* ===========================
     *  RELACIONES
     * =========================== */

    // workno = clave_empleado en tbl_funcionario
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'workno', 'clave_empleado');
    }

    // Accedemos a unidad a través del funcionario (id_unidad → tbl_unidades)
    // Esta parte se configura en el modelo Funcionario (ver siguiente punto).

    /* ===========================
     *  ACCESSORS
     * =========================== */

    public function getPuestoAttribute()
    {
        // 1) Si funcionario tiene nombre_adscripcion, lo usamos
        if ($this->funcionario && !empty($this->funcionario->nombre_adscripcion)) {
            return $this->funcionario->nombre_adscripcion;
        }

        // 2) Si no, usamos la unidad relacionada
        if ($this->funcionario && $this->funcionario->unidad) {
            return $this->funcionario->unidad->unidad;
        }

        // 3) Fallback
        return 'SIN ADSCRIPCIÓN';
    }
}
