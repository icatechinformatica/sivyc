<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contratos extends Model
{
    //
    protected $table = 'contratos';
    protected $primaryKey = 'id_contrato';

    protected $fillable = ['id_contrato','numero_contrato','cantidad_letras1','fecha_firma','municipio',
    'id_folios','instructor_perfilid','unidad_capacitacion','docs','observacion','cantidad_numero','arch_factura','fecha_status'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function supre()
    {
        return $this->belongsTo(supre::class, 'id_supre');
    }
    public function perfil_instructor()
    {
        return $this->belongsTo(InstructorPerfil::class, 'id_folios');
    }
}
