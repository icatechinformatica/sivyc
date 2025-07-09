<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WspCola extends Model
{
    //
    protected $table = 'tbl_wsp_cola';

    protected $fillable = [
        'id', 'telefono', 'mensaje', 'estatus', 'sent_at', 'id_user_sent'
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
