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
        'id', 'unidad_capacitacion', 'no_memo', 'fecha','nombre_para','puesto_para','nombre_remitente','puesto_remitente',
        'nombre_valida','puesto_valida','nombre_elabora','puesto_elabora','nombre_ccp1','puesto_ccp1','nombre_ccp2','puesto_ccp2',
        'status','observacion','folio_validacion','fecha_validacion','nombre,firmante','puesto_firmante','val_ccp1','val_ccpp1',
        'val_ccp2','val_ccpp2','val_ccp3','val_ccpp3','val_ccp4','val_ccpp4'
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

    public function contratos()
    {
        return $this->hasMany(contratos::class, 'id_supre');
    }
}
