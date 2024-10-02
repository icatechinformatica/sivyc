<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FirmaAdministrativo extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $indice;
    public $cadenaOriginal;
    public $baseXml;
    public $tokenData;
    public $id;
    public $curpFirmante;

    public function __construct($indice, $cadenaOriginal, $baseXml, $tokenData, $id, $curpFirmante)
    {
        //
        $this->indice = $indice;
        $this->cadenaOriginal = $cadenaOriginal;
        $this->baseXml = $baseXml;
        $this->tokenData = $tokenData;
        $this->id = $id;
        $this->curpFirmante = $curpFirmante;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.firma-administrativo');
    }
}
