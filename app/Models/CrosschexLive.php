<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrosschexLive extends Model
{
    protected $table = 'crosschex_live';

    protected $fillable = [
        'headers',
        'payload',
        'ip',
        'user_agent',
        'received_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'received_at' => 'datetime',
    ];
}
