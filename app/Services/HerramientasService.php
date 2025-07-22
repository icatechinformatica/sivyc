<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class HerramientasService
{
    public function define_genero($plantilla,$sexo)
    {
        if ($sexo == 'MASCULINO') {
            $plantilla = str_replace(['(a)'], [''], $plantilla);
        } else { //instructor#
            $plantilla = str_replace(['o(a)','r(a)'], ['a','ra'], $plantilla);
        }

        return $plantilla;
    }
}
