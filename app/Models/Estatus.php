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
        $prevQuery = self::where('id', '<', $this->id)->orderBy('id', 'desc');
        $nextQuery = self::where('id', '>', $this->id)->orderBy('id', 'asc');

        if (!$incluirFinales) {
            $prevQuery->where('final', false);
            $nextQuery->where('final', false);
        }

        $prev = $prevQuery->first();
        $next = $nextQuery->first();

        return collect([$prev, $next])->filter();
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

    public function permisos()
    {
        return $this->belongsToMany(PermisoExt::class, 'tbl_estatus_permiso', 'estatus_id', 'permiso_id');
    }
}
