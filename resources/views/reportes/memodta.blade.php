<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FORMATO T</title>

    <style>
        body{font-family: sans-serif}
        @page {margin: 20px 50px 110px 50px;size: letter;}
        header { position: fixed; left: 0px; top: 0px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        img.izquierda {float: left;width: 100%;height: 60px;}
        img.izquierdabot {
                float: inline-end;
                width: 713px;
                height: 100px;
            }
        img.derechabot {position:fixed;right: 50px;width: 350px;height: 60px;}
        img.derecha {float: right;width: 50%;height: 60px;}
        .tablas{border-collapse: collapse;width: 100%;}
        /* agregamos a 3 el padding para que no salte a la otra pagina y la deje en blanco */
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        /* .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;} */
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;}
        footer {position:fixed;left:0px;bottom:0px;width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        .contenedor {
        position:RELATIVE;
        top:120px;
        width:100%;
        margin:auto;

        /* Propiedad que ha sido agreda*/

        }
        .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 830px; /*773*/
                left: 10px; /*20*/
                font-size: 7.5px;
                color: white;
                line-height: 1;
            }
    </style>

     {{-- condicion cuando el array sea de 14 elementos cambia el pading de la fila de la tabla--}}
    @if (count($reg_cursos) ==14)
        <style>
            .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 3px;}
        </style>
    @endif
</head>
<body>
    <header>
            <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
            <h6><small><small>{{$leyenda}}</small></small></h6><p class='direccion'>
    </header>
    <footer>
        <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
        <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}} @endforeach</b></p>
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
    </footer>
    <div class="contenedor" style="margin-bottom: 100px;">
        {{-- crear un div para encerrar todo lo que lleva al inicio --}}
        <div>
            <div align=right style="font-size:13px;">UNIDAD DE CAPACITACION {{ $reg_unidad->unidad }}</div>
            <div align=right style="font-size:13px;">MEMORANDUM NO. {{ $numero_memo }}</div>
            <div align=right style="font-size:13px;">{{ $reg_unidad->unidad }}, CHIAPAS; {{ $fecha_nueva }}</div>
            <br>
            <div align=left style="font-size:13px;">C. {{ $destinatario->nombre }}</div>
            <div align=left style="font-size:13px;">{{ $destinatario->cargo }}</div><br>

            <div align=left style="font-size:13px;">Asunto: Reporte de cursos finalizados de la  Unidad de Capacitación {{ $reg_unidad->unidad }}.</div><br>
            <div align="justify" style="font-size:13px;">
                Derivado del proceso académico de entrega de información, adjunto al presente con firmas autógrafas y los sellos originales correspondientes:
            </div>
            <br>
            <table class="tablag">
                <thead>
                    <tr>
                        <td style="font-size:13px;"><b>* RIAC-02 de inscripción</b></td>
                        <td style="font-size:13px;"><b>* LAD-04</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:13px;"><b>* RIAC-02 de certificación</b></td>
                        <td style="font-size:13px;"><b>* RESD-05</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:13px;"><b>* RIAC-02 de acreditación</b></td>
                    </tr>
                </thead>
            </table>
            <br>
            <div align="justify" style="font-size:13px;">Del ciclo escolar {{ $reg_cursos[0]->ciclo }} en la Unidad de Capacitación {{ $reg_unidad->unidad }}, se reportan {{ $total }} curso(s)/certificacione(s) para este mes:</div>

        </div>
             <br>
            {{-- se llenan de datos esta tabla --}}
            <table class="tablas">
                <thead>
                    <tr>
                        <th>UNIDAD DE CAPACITACIÓN O CENTRO DE TRABAJO ACCIÓN MÓVIL</th>
                        <th>CURSO</th>
                        <th>MOD</th>
                        <th>INICIO </th>
                        <th>TERMINO</th>
                        <th>CUPO</th>
                        <th>INSTRUCTOR EXTERNO</th>
                        <th>CLAVE</th>
                        <th>OBSERVACIONES</th>
                    </tr>
                </thead>
                <tbody>

                   @foreach ($reg_cursos as $a)
                    <tr>
                        <th>{{ $a->unidad }}</th>
                        <th>{{ $a->curso }}</th>
                        <th>{{ $a->mod }}</th>
                        <th>{{ $a->inicio }}</th>
                        <th>{{ $a->termino }}</th>
                        <th>{{ $a->cupo }}</th>
                        <th>{{ $a->nombre }}</th>
                        <th>{{ $a->clave }}</th>
                        <th>{{ $a->tnota }}</th>
                    </tr>
                   @endforeach
                </tbody>
            </table>
            {{-- Al final del documento --}}
            <br>
            {{-- creo un div para contener todo el texto que lleva al final --}}
            <div>
                <div style="font-size:13px;">Sin más por el momento, le envío un cordial saludo.</div>
                <br>
                <div style="font-size:13px;">ATENTAMENTE</div>
                <br><br><br>
                <div style="font-size:13px;">C. {{ $remitente->nombre }}</div>
                <div style="font-size:13px;">{{ $remitente->cargo }}</div>
                <br><br>
                <div style="font-size:10px;">C.c.p. {{$ccp->nombre}}. - {{$ccp->cargo}}. - Para su conocimiento.</div>
                <div style="font-size:10px;">Archivo.</b> </div>
                <div style="font-size:10px;">Validó: {{ $remitente->nombre }}. - {{ $remitente->cargo }}.</div>
                <div style="font-size:10px;">Elaboró: {{ $elabora->nombre }}. - {{ $elabora->cargo }}.</div>
            </div>

    </div>
</body>
</html>





