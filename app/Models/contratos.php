<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contratos extends Model
{
    //
    protected $table = 'contratos';

    protected $fillable = ['id_contrato', 'numero_contrato', 'folio_ine', 'cantidad_letras', 'lugar_expedicion', 'fecha_firma',
                            'testigo_icatech', 'testigo_instructor', 'municipio', 'id_supre'];

    protected $hidden = ['created_at', 'updated_at'];

    public function supre()
    {
        return $this->belongsTo(supre::class, 'id_supre');
    }
}
