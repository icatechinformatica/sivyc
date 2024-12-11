<?php

namespace App\Models\cat;

use Illuminate\Database\Eloquent\Model;
use App\Models\Funcionarios;

class Cargos extends Model
{
    //
    protected $table = 'cat_cargos';
    protected $fillable = ['id', 'cargo'];
    protected $hidden = ['created_at', 'updated_at'];

     /**
     * Get all of the comments for the Cargos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionarios::class, 'cargo_id', 'id');
    }
}
