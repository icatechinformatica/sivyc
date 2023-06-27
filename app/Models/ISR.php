<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ISR extends Model
{
    protected $table = 'isr';

    protected $fillable = [
        'id','vigencia','limite_inferior','limite,superior','cuota_fija','porcentaje'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
