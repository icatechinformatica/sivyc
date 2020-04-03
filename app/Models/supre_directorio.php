<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class supre_directorio extends Model
{
    // tabla de convenios
    protected $table = 'supre_directorio';

    protected $fillable = ['id','supre_dest','supre_rem','supre_valida','supre_elabora','supre_ccp1',
    'supre_ccp2','val_firmante','val_ccp1','val_ccp2','val_ccp3','val_ccp4','id_supre'];

    protected $hidden = ['created_at', 'updated_at'];
}
