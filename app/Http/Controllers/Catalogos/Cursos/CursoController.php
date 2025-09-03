<?php

namespace App\Http\Controllers\Catalogos\Cursos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\especialidad;
use App\Models\tbl_unidades;
use App\Models\criterio_pago;
use Illuminate\Support\Facades\DB;
use App\Models\Area;

class CursoController extends Controller
{
    function __construct() {
        $this->categorias = ['OFICIOS','PROFESIONALIZACIÓN','ESPECIALIZACIÓN','SALUD','CURSO ALFA'];
        $this->perfil = [
            'PRIMARIA INCONCLUSA',
            'PRIMARIA TERMINADA',
            'SECUNDARIA INCONCLUSA',
            'SECUNDARIA TERMINADA',
            'NIVEL MEDIO SUPERIOR INCONCLUSO',
            'NIVEL MEDIO SUPERIOR TERMINADO',
            'NIVEL SUPERIOR INCONCLUSO',
            'NIVEL SUPERIOR TERMINADO',
            'POSTGRADO'
        ];
    }
    public function index()
    {

        return view('catalogos.cursos.lista_cursos');
    }

    public function vista_crear_curso()
    {
        $especialidad = new especialidad();
        $especialidades = $especialidad->all();
        $unidades = new tbl_unidades();
        $unidadesMoviles = $unidades->SELECT('ubicacion')->orderBy('ubicacion', 'asc')->GROUPBY('ubicacion')->GET();
        $criterioPago = new criterio_pago;
        $cp = $criterioPago->Where('id','!=','0')->Where('activo', TRUE)->GET();
        $area = new Area();
        $areas = $area->all();
        $gruposvulnerables = DB::table('grupos_vulnerables')->SELECT('id','grupo')->ORDERBY('grupo','ASC')->GET();
        $dependencias = DB::table('organismos_publicos')->SELECT('id','organismo')->ORDERBY('organismo','ASC')->GET();
        $categorias = $this->categorias;
        $perfil = $this->perfil;

        return view('catalogos.cursos.agregar_cursos', compact('especialidades', 'areas', 'unidadesMoviles', 'cp', 'gruposvulnerables','dependencias','categorias','perfil'));
    }
}
