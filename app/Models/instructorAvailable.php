<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class instructorAvailable extends Model
{
    //
    protected $table = 'instructor_available';

    protected $fillable = [
        'id','instructor_id','CHK_TUXTLA','CHK_TAPACHULA','CHK_COMITAN','CHK_REFORMA','CHK_TONALA','CHK_VILLAFLORES',
        'CHK_JIQUIPILAS','CHK_CATAZAJA','CHK_YAJALON','CHK_SAN_CRISTOBAL','CHK_CHIAPA_DE_CORZO','CHK_MOTOZINTLA',
        'CHK_BERRIOZABAL','CHK_PIJIJIAPAN','CHK_JITOTOL','CHK_LA_CONCORDIA','CHK_VENUSTIANO_CARRANZA','CHK_TILA',
        'CHK_TEOPISCA','CHK_OCOSINGO','CHK_CINTALAPA','CHK_COPAINALA','CHK_SOYALO','CHK_ANGEL_ALBINO_CORZO','CHK_ARRIAGA',
        'CHK_PICHUCALCO','CHK_JUAREZ','CHK_SIMOJOVEL','CHK_MAPASTEPEC','CHK_VILLA_CORZO','CHK_CACAHOATAN',
        'CHK_ONCE_DE_ABRIL','CHK_TUXTLA_CHICO','CHK_OXCHUC','CHK_CHAMULA','CHK_OSTUACAN','CHK_PALENQUE'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
