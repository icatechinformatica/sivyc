@extends('theme.formatos.hlayout'.$layout_año)
@section('title', 'Lista de Alumnos | SIVyC Icatech')
@section('content_script_css')
<style>
    .container { font-size: 9px;}
    /* @page { margin-bottom: 120px; } */
    #titulo {position: fixed; top: 45px; left: 0px; width: 100%; text-align: center;}
    #titulo h2{padding:0px; margin:10px 0px 5px 0px; font-size: 14px;}
    #titulo h3{padding:0px; margin:0px; font-size: 12px;}

    .tb {width: 100%; border-collapse: collapse; text-align: center; }
    .tb tr, .tb td, .tb th{ border: black 1px solid; padding: 1px;}
    .tb thead{background: #EAECEE;}
    header{ top: 20px; font-size: 11px; font-weight: bold; line-height: 1;}
    body { padding-top: 16%; }

</style>
@endsection
@section('content')
    <div id="titulo">
        <h2>Lista de Alumnos</h2>
        <h3>Grupo: {{ $folio_grupo}}</h3>
    </div>
    <div class="container">
            @php
                $consc = 1;
                $mod = $alumnos[0]->mod;
                $nombre_curso = $alumnos[0]->nombre_curso;
                $dura = $alumnos[0]->horas;
                $horario = $alumnos[0]->horario;
                $inicio = $alumnos[0]->inicio;
                $termino = $alumnos[0]->termino;
                $tipo = $alumnos[0]->tipo_curso;
                $depen = $alumnos[0]->depe;
            @endphp
            <table class="tb">
                <thead>
                    <tr>
                        <th colspan="9" style="font-size:10.5px;">DATOS GENERALES DEL CURSO DE CAPACITACIÓN O CERTIFICACIÓN</th>
                    </tr>
                    <tr>
                        <th colspan="2">NOMBRE DEL CURSO</th>
                        <th colspan="5">{{$nombre_curso}}</th>
                        <th colspan="1">MODALIDAD</th>
                        <th>{{$mod}}</th>
                    </tr>
                    <tr>
                        <th colspan="2">DURACIÓN EN HORAS</th>
                        <th>{{$dura}}</th>
                        <th colspan="4">HORARIO</th>
                        <th colspan="2">{{$horario}} hrs.</th>
                    </tr>
                    <tr>
                        <th colspan="2">FECHA DE INICIO</th>
                        <th>{{$inicio}}</th>
                        <th colspan="4">FECHA DE TERMINO</th>
                        <th colspan="2">{{$termino}}</th>
                    </tr>
                    <tr>
                        <th colspan="2">TIPO DE CURSO</th>
                        <th>{{$tipo}}</th>
                        <th colspan="4">INSTITUCIÓN O DEPENDENCIA</th>
                        <th colspan="2">{{$depen}}</th>
                    </tr>
                    <tr>
                        <th colspan="9" style="font-size:10.5px;">LISTA DE ALUMNOS</th>
                    </tr>
                    <tr>
                        <th>N°</th>
                        <th>APELLIDO PATERNO</th>
                        <th>APELLIDO MATERNO</th>
                        <th>NOMBRE(S)</th>
                        <th>CURP</th>
                        <th>SEXO</th>
                        <th>EDAD</th>
                        <th>CORREO</th>
                        <th>CUOTA DE RECUPERACIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $item)
                        <tr>
                            <td>{{$consc++}}</td>
                            <td>{{$item->apellido_paterno}}</td>
                            <td>{{$item->apellido_materno}}</td>
                            <td>{{$item->nombre}}</td>
                            <td>{{$item->curp}}</td>
                            <td>@if ($item->sexo=='H'){{"H"}} @else {{"M"}} @endif</td>
                            <td>{{$item->edad}}</td>
                            <td>{{$item->correo}}</td>
                            <td>{{$item->costo}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    </div>

@endsection
@section('script_content_js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 500, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script>
@endsection
