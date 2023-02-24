<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class instructor_history extends Model
{
    protected $table = 'instructores_history';

    protected $fillable = ['id','id_instructor','nrevision','id_user','movimiento','status','turnado'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'data_instructor' => 'array',
        'data_perfil' => 'array',
        'data_especialidad' => 'array'
    ];

    /**
     * método slug
     */
    protected function getSlugAttribute($value): string {
        return Str::slug($value, '-');
    }

    /**
     * obtener los perfiles del instructor
     */
}

