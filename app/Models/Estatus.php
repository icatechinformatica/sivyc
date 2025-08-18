<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    use HasFactory;

    protected $table = 'tbl_aux_estatus';

    protected $fillable = [
        'estatus',
        'turnado_a',
        'final',
    ];

    protected $casts = [
        'final' => 'boolean',
    ];

    // Relación N:M con Alumno
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'tbl_alumno_estatus', 'id_estatus', 'id_alumno');
    }

    // Relación N:M con Grupo
    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'tbl_grupo_estatus', 'id_estatus', 'id_grupo');
    }

    /**
     * Devuelve los estatus adyacentes según la regla de IDs contiguos (+/- 1).
     * @param bool $incluirFinales Si false, excluye estatus finales.
     */
    public function adyacentes(bool $incluirFinales = true)
    {
        $ids = [max(1, $this->id - 1), $this->id + 1];
        $query = self::whereIn('id', $ids);
        if (!$incluirFinales) {
            $query->where('final', false);
        }
        return $query->get();
    }

    /**
     * Helper estático para obtener adyacentes a partir de un ID.
     */
    public static function adyacentesDeId(int $id, bool $incluirFinales = true)
    {
        $estatus = self::find($id);
        if (!$estatus) return collect();
        return $estatus->adyacentes($incluirFinales);
    }
}
