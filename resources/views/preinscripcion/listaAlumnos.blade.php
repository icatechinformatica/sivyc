@extends('theme.formatos.hlayout')
@section('title', 'Lista de Alumnos | SIVyC Icatech')
@section('css')
<style>
    body { margin-top: 105px;}
    @page { margin-bottom: 120px; }
    #titulo {position: fixed; top: 45px; width: 100%;}
    #titulo h2{padding:0px; margin:10px 0px 5px 0px; font-size: 14px;}
    #titulo h3{padding:0px; margin:0px; font-size: 12px;}

    .tb {width: 100%; border-collapse: collapse; text-align: center; }
    .tb tr, .tb td, .tb th{ border: black 1px solid; padding: 1px;}
    .tb thead{background: #EAECEE;}

    </style>
@endsection
@section('header')
    <div id="titulo">
        <h2>Lista de Alumnos</h2>
        <h3>Grupo: {{ $folio_grupo}}</h3>
    </div>
@endsection
@section('body')
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
                        <th colspan="8">DATOS GENERALES DEL CURSO DE CAPACITACIÓN O CERTIFICACIÓN EXTRAORDINARIA</th>
                    </tr>
                    <tr>
                        <th>NOMBRE DEL CURSO</th>
                        <th colspan="3">{{$nombre_curso}}</th>
                        <th colspan="3">MODALIDAD</th>
                        <th>{{$mod}}</th>
                    </tr>
                    <tr>
                        <th>DURACIÓN EN HORAS</th>
                        <th>{{$dura}}</th>
                        <th colspan="3">HORARIO</th>
                        <th colspan="3">{{$horario}} hrs.</th>
                    </tr>
                    <tr>
                        <th>FECHA DE INICIO</th>
                        <th>{{$inicio}}</th>
                        <th colspan="3">FECHA DE TERMINO</th>
                        <th colspan="3">{{$termino}}</th>
                    </tr>
                    <tr>
                        <th>TIPO DE CURSO</th>
                        <th>{{$tipo}}</th>
                        <th colspan="3">INSTITUCIÓN O DEPENDENCIA</th>
                        <th colspan="3">{{$depen}}</th>
                    </tr>
                    <tr>
                        <th colspan="8">LISTA DE ALUMNOS</th>
                    </tr>
                    <tr>
                        <th>N°</th>
                        <th>APELLIDO PATERNO</th>
                        <th>APELLIDO MATERNO</th>
                        <th>NOMBRE(S)</th>
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
                            <td>@if ($item->sexo=='MASCULINO') {{"M"}} @else {{"F"}} @endif</td>
                            <td>{{$item->edad}}</td>
                            <td>{{$item->correo}}</td>
                            <td>{{$item->costo}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    </div>

@endsection
@section('js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 538, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script>
@endsection
