<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class pago extends Model
{
    //
    protected $table = 'pagos';

    protected $fillable = [
        'id','no_memo','fecha','nombre_ccp1','puesto_ccp1','nombre_ccp2','puesto_ccp2','nombre_ccp3',
        'puesto_ccp3','elaboro','id_contrato','nombre_para','puesto_para','no_pago','descripcion',
        'observacion','fecha_status','liquido','solicitud_fecha','chk_rechazado','fecha_rechazo',
        'fecha_validado','arch_pago'
    ];

    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['fecha_rechazo' => 'array'];

    /**
     * obtener el instructor que pertenece al perfil
     */
}
