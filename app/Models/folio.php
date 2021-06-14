<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class folio extends Model
{
    //
    protected $table = 'folios';

    protected $fillable = [
        'id_folios','numero_presupuesto','folio_validacion','iva','importe_hora','importe_total',
        'id_supre','id_cursos','status','comentario','cancelo'
    ];

    protected $primaryKey = 'id_folios';

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function supre()
    {
        return $this->belongsTo(supre::class);
    }
    public function curso()
    {
        return $this->belongsTo(tbl_curso::class);
    }
}
