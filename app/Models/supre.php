<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class supre extends Model
{
    //
    protected $table = 'tabla_supre';

    protected $fillable = [
        'id_supre', 'unidad_capacitacion', 'no_memo', 'fecha','nombre_para','puesto_para','nombre_remitente','puesto remitente',
        'nombre_ccp1','puesto_ccp1','nombre_ccp2','puesto_ccp2','nombre_valida','puesto_valida','nombre_elabora','puesto_elabora','status'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function tabla_supre()
    {
        return $this->hasMany(tablaFolio::class);
    }
    public function folios(){
        return $this->hasMany(folio::class);
    }
}
