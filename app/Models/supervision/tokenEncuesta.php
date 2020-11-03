<?php

namespace App\Models\supervision;

use Illuminate\Database\Eloquent\Model;

class tokenEncuesta extends Model
{
    protected $table = 'encuestas_token';
    protected $fillable = ['id','url_token','tmp_token','ttl','cantidad_usuarios'];
    protected $hidden = [ 'updated_at','created_at'];
}
