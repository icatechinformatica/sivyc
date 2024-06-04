<?php

namespace App\Models\Reportes;

use Illuminate\Database\Eloquent\Model;
use App\Models\cat\CatConcepto;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recibo extends Model
{
    //
    protected $table = 'tbl_recibos';

    protected $fillable = ['id', 'num_recibo', 'importe', 'importe_letra', 'status_folio', 'fecha_status', 'movimientos', 'id_curso', 'folio_grupo', 'fecha_expedicion', 'recibio', 'unidad', 'iduser_created', 'iduser_updated', 'recibide', 'file_pdf', 'folio_recibo', 'status_recibo', 'motivo', 'observaciones', 'id_concepto', 'descripcion', 'matricula', 'cantidad', 'constancias', 'depositos'];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the user that owns the Recibo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function concepto(): BelongsTo
    {
        return $this->belongsTo(CatConcepto::class, 'id_concepto');
    }
}
