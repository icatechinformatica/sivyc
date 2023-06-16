<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class HistoryPat extends Model
{
    protected $table = 'history_pat';

    protected $fillable = ['id', 'id_org', 'direccion', 'area', 'fecha', 'iduser_created'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = ['meta_json' => 'json', 'avance_json' => 'json'];
}
