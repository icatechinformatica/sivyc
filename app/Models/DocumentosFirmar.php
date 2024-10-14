<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentosFirmar extends Model {
    protected $table = 'documentos_firmar';

    protected $fillable = [
        'obj_documento', 'status', 'link_pdf', 'documento','link_verificacion','num_oficio', 'body_html'
    ];

    protected $casts = [
        'obj_documento' => 'jsonb',
        'documento' => 'xml',
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
