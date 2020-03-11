<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contratos extends Model
{
    //
    protected $table = 'contratos';

    protected $fillable = ['id_contrato','numero_contrato','cantidad_letras1','cantidad_letras2','numero_circular','nombre_director',
    'unidad_capacitacion','municipio','testigo1','puesto_testigo1','testigo2','puesto_testigo2','fecha_firma','id_folios'];

    protected $hidden = ['created_at', 'updated_at'];

    public function supre()
    {
        return $this->belongsTo(supre::class, 'id_supre');
    }
}
