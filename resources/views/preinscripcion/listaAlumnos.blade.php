<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LISTA DE ALUMNOS</title>
    <style>
        @page {
            margin: 40px 30px 10px 30px;
        }
        body {
            /*margin: 3cm 2cm 2cm;*/
            margin-top: 120px;
            font-family: sans-serif; font-size: 12px;
        }
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 4cm;
            text-align: center;
            /*line-height: 5px;*/
        }
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
            text-align: center;
            line-height: 35px;
        }
        img.izquierda {float: left;width: 200px;height: 60px;}
        img.derecha {float: right;width: 200px;height: 60px;}
        .tb {width: 100%; border-collapse: collapse; text-align: center; font-size: 8px;}
        .tb tr, .tb td, .tb th{ border: black 1px solid; padding: 1px;}
        .tb thead{background: #EAECEE;}
    </style>
</head>
<body>
    <header>
        <img class="izquierda" src='img/logohorizontalica1.png'>
        <img class="derecha" src='img/chiapas.png'>
        <div style="clear: both;">
            <font size="1" align="center"><b>{{$distintivo}}</b></font>
        </div>
        {{-- <table style="text-align: right; border-collapse: collapse;" align="right">
            <tr>
                <td><b>Unidad de Capacitación .</b></td> 
            </tr>
            <tr>
                <td>, Chis., .</td>
            </tr>
        </table> --}}
    </header>
    <main>
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
                        <th colspan="7">DATOS GENERALES DEL CURSO DE CAPACITACIÓN O CERTIFICACIÓN EXTRAORDINARIA</th>
                    </tr>
                    <tr>
                        <th>NOMBRE DEL CURSO</th>
                        <th colspan="3">{{$nombre_curso}}</th>
                        <th colspan="2">MODALIDAD</th>
                        <th>{{$mod}}</th>
                    </tr>
                    <tr>
                        <th>DURACIÓN EN HORAS</th>
                        <th>{{$dura}}</th>
                        <th colspan="2">HORARIO</th>
                        <th colspan="3">{{$horario}} hrs.</th>
                    </tr>
                    <tr>
                        <th>FECHA DE INICIO</th>
                        <th>{{$inicio}}</th>
                        <th colspan="2">FECHA DE TERMINO</th>
                        <th colspan="3">{{$termino}}</th>
                    </tr>
                    <tr>
                        <th>TIPO DE CURSO</th>
                        <th>{{$tipo}}</th>
                        <th colspan="2">INSTITUCIÓN O DEPENDENCIA</th>
                        <th colspan="3">{{$depen}}</th>
                    </tr>
                    <tr>
                        <th colspan="7">LISTA DE ALUMNOS</th>
                    </tr>
                    <tr>
                        <th>N°</th>
                        <th>APELLIDO PATERNO</th>
                        <th>APELLIDO MATERNO</th>
                        <th>NOMBRE(S)</th>
                        <th>SEXO</th>
                        <th>EDAD</th>
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
                            <td>{{$item->costo}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
    <footer>
        {{--  <p><strong></strong></p>  --}}
    </footer>
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(50, 570, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script>
</body>
</html>