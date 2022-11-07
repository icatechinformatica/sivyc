<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FORMATO T</title>
    <style>
        body{font-family: sans-serif}
        @page {margin: 20px 50px 120px 50px;size: letter;}
        header { position: fixed; left: 0px; top: 30px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        img.izquierda {float: left;width: 200px;height: 60px;}
        img.izquierdabot {position:fixed;left: 50px;width: 350px;height: 60px;}
        img.derechabot {position:fixed;right: 50px;width: 350px;height: 60px;}
        img.derecha {float: right;width: 200px;height: 60px;}
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;}
        footer { position:fixed;left:0px;bottom:0px;height:0px;width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        .contenedor {
        position:RELATIVE;
        top:120px;
        width:100%;
        margin:auto;

        /* Propiedad que ha sido agreda*/

        }
    }
    </style>
</head>
<body>
    <header>
            <img class="izquierda" src="{{ public_path('img/logohorizontalica1.jpg') }}">
            <img class="derecha" src='img/chiapas.png'>
            <br>
            <h6><small><small>{{$leyenda}}</small></small></h6>
    </header>
    <footer>
        <script type="text/php">
            if (isset($pdf))
            {
                $x = 275;
                $y = 725;
                $text = "Hoja {PAGE_NUM} de {PAGE_COUNT}";
                $font = "Arial";
                $size = 11;
                $color = array(0,0,0);
                $word_space = 0.0;  //  default
                $char_space = 0.0;  //  default
                $angle = 0.0;   //  default
                $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            }
        </script>
        <table class="tablad" bgcolor="black">
                <tr>
                    <td colspan="4" style="color:white;"><b>Av. Circunvalación Pichucalco N 212-B Colonia Moctezuma</b></td>
                    {{-- <td colspan="4" style="color:white;"><b>14 Poniente Norte No. 239 Colonia Moctezuma</b></td> --}}
                </tr>
                <tr>
                    <td colspan="4" style="color:white;"><b>Tuxtla Gutiérrez, Chiapas; Telefono (961)6121621 Ext.601 C.P.29030</b></td>
                    {{-- <td colspan="4" style="color:white;"><b>Tuxtla Gutiérrez, C.P.29030 Telefono+52(961)61-2-16-21</b></td> --}}
                </tr>
                <tr>
                    <td colspan="4" style="color:white;"><b>Email: dtecnicaacademica@gmail.com</b></td>
                </tr>
        </table>

        <img class="derecha" src='img/icatech-imagen.png'>
    </footer>
    <div class= "contenedor">
        <div align=right style="font-size:11px;"><b>DIRECCION TECNICA ACADEMICA</b></div>
        <div align=right style="font-size:11px;"><b>MEMORANDUM NO. {{ $nume_memo }}</b></div>
        <div align=right style="font-size:11px;"><b>TUXTLA GUTIERREZ, CHIAPAS; {{ $fecha_nueva }}</b></div>
        <br>
        <div align=left style="font-size:12px;"><b>{{ $reg_unidad->dunidad }}, {{ $reg_unidad->pdunidad }}</b></div>
        <div align=left style="font-size:11px;"><b>PRESENTE.</b></div>
        <br>
            @php
                $num=1;
            @endphp
            <div class="table-responsive-sm">
                <div align="justify" style="font-size:11px;">
                    En seguimiento a la integración del Formato T del mes de {{$mesReportado2}} del presente
                    año de su Unidad de Capacitación, recibido el pasado {{$diaArray[0]}} de {{$mesReportado2}} al correo electronico {{$correo_institucional}}, le informo que fueron recibidos los formatos RIACD-02 INSCRIPCION,
                    RIAC-02 ACREDITACION, RIAC-02 CERTIFICACION, LAD-04 LISTA DE ASISTENCIA, RESD-05 CALIFICACIONES
                    digitalizados con firmas y sellos de un total de {{ $sum_total }} cursos enviados a la Unidad {{ $reg_unidad->unidad }}. De lo anterior,
                    hago de su conocimiento que, una vez revisada la informacion le comento, se reportaron a la Dirección
                    de Planeación de este Instituto un total de {{ $totalReportados }} cursos y {{ $total }} no se reportaron de acuerdo a las siguientes observaciones
                </div>
                <br>
                <table class="tablas">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>MES</th>
                            <th>UNIDAD/ACCION MOVIL</th>
                            <th>ESPECIALIDAD</th>
                            <th>CURSO</th>
                            <th>CLAVE</th>
                            <th>OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reg_cursos as $index => $a)
                        <tr>
                            <th>{{ $num }}</th>
                            <th>{{ $a->mes }}</th>
                            <th>{{ $a->unidad }}</th>
                            <th>{{ $a->espe }}</th>
                            <th>{{ $a->curso }}</th>
                            <th>{{ $a->clave }}</th>
                            <th>{{ json_decode($a->comentario_enlaces_retorno, JSON_UNESCAPED_SLASHES) }}</th>
                        </tr>
                            @php
                                $num=$num+1;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
                <br>
                <div align="justify" style="font-size:11px;">
                    No omito manifestar sobre la información antes mencionada, que se encuentran pendientes los documentos
                    originales de las entregas realizadas por su Unidad de Capacitación para el reporte estadístico una vez
                    reintegrados al trabajo presencial.
                </div>
                <br>
                <div style="font-size:11px;">Sin más por el momento, agradezco su atención y le envío un cordial saludo.</div>
                <br>
                <div style="font-size:11px;"> <b>A T E N T A M E N T E</b> </div>
                <br><br><br><br>
                <div style="font-size:11px;"> <b>{{ $reg_unidad->dacademico }}</b> </div>
                <div style="font-size:11px;"> <b>{{ $reg_unidad->pdacademico }}</b> </div>
                <br>
                <div style="font-size:9px;"> <b>C.C.P MTRA. FABIOLA LIZBETH ASTUDILLO REYES, DIRECTORA GENERAL DEL ICATECH. PARA SU CONOCIMIENTO. - CIUDAD</b> </div>
                <div style="font-size:9px;"> <b>{{ $reg_unidad->academico }}. {{ $reg_unidad->pacademico }}.</b> </div>
                <div style="font-size:9px;"> <b>ARCHIVO / MINUTARIO.</b> </div>
                <div style="font-size:7px;"> <b>VALIDÓ: ING. MARÍA TERESA JIMÉNEZ FONSECA. JEFA DEL DEPTO. DE CERTIFICACIÓN Y CONTROL</b> </div>
                <div style="font-size:7px;"> <b>ELABORÓ: {{ $elabora }}.</b> </div>
            </div><br>
    </div>
</body>
</html>
