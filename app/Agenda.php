<?php

namespace App;

use App\Models\Grupo;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'tbl_grupo_agenda';

    protected $fillable = ['id','id_grupo', 'fecha_inicio', 'fecha_fin'];

    public $timestamps = false;

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

}
