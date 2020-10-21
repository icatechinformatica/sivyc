<?php

namespace App\Models\supervision;

use Illuminate\Database\Eloquent\Model;

class token extends Model
{
    protected $table = 'supervision_tokens';
    protected $fillable = ['id','url_token','tmp_token','ttl'];
    protected $hidden = [ 'updated_at','created_at'];
}
