<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FirmaComponente extends Component
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
    public function __construct($indice, $cadenaOriginal, $baseXml, $tokenData)
    {
        //
        $this->indice = $indice;
        $this->cadenaOriginal = $cadenaOriginal;
        $this->baseXml = $baseXml;
        $this->tokenData = $tokenData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.firma-componente');
    }
}
