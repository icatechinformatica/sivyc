<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrosschexHistory extends Model
{
    protected $connection = 'pgsql';   // conexión a PostgreSQL
    protected $table      = 'crosschex_history';
    protected $primaryKey = 'id';

    public $timestamps = true; // usa created_at / updated_at

    protected $casts = [
        'headers'     => 'array',
        'payload'     => 'array',
        'received_at' => 'datetime',
    ];

    protected $fillable = [
        'headers',
        'payload',
        'ip',
        'user_agent',
        'unidad',
        'received_at',
    ];
}
