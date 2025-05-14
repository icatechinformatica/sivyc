<?php

namespace App\Models\Reportes;

// date_default_timezone_set('Etc/GMT+6');

use Illuminate\Database\Eloquent\Model;


class Rf001Model extends Model
{
    //
    protected $table = 'view_rf';

     public $timestamps = false; // desactiva manejo de created_at y updated_at

    protected $fillable = [
        'id',
        'memorandum',
        'estado',
        'movimientos',
        'id_unidad',
        'envia',
        'dirigido',
        'archivos',
        'unidad',
        'periodo_inicio',
        'periodo_fin',
        'realiza',
        'movimiento',
        'tipo',
        'confirmed',
    ];

    // Opcional: si solo vas a leer datos
    public $incrementing = false; // evita confusiones si la vista no tiene autoincremento

    protected $primaryKey = 'id'; // asegúrate de que 'id' esté presente y sea único

}
