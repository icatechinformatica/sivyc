<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaAdscripcion extends Model
{
    //
    protected $table = 'area_adscripcion';

    protected $fillable = [
        'id', 'area', 'organo_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the post that owns organo administrativo.
     */
    public function organo()
    {
        return $this->belongsTo(OrganoAdministrativo::class);
    }

    public function directorio()
    {
        return $this->hasMany(Personal::class, 'area_adscripcion_id');
    }
}
