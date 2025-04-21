<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\RH\RHController;

class DescargarNube extends Command
{
    protected $signature = 'descarga:nube';
    protected $description = 'Ejecuta la función descarga_nube en RHController a las 9:31AM y 8PM';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $controller = new RHController();
        $controller->descarga_nube();

        $this->info('Tarea de descarga completada.');
    }
}
