<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AgregarCurpAlumno extends Component
{
    public $curp;
    public $grupoId;
    public $bandera;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($curp, $grupoId, $bandera)
    {
        $this->curp = $curp;
        $this->grupoId = $grupoId;
        $this->bandera = $bandera;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.agregar-curp-alumno');
    }
}
