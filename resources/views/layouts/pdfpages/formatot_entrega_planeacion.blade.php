<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ENTREGA FORMATO T PLANEACIÓN</title>

    <style>
        body{font-family: sans-serif}
        @page {margin: 20px 50px 120px 50px;size: letter;}
        header { position: fixed; left: 0px; top: 0px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        img.izquierda {float: left;width: 100%;height: 60px;}
        img.izquierdabot {
                float: inline-end;
                width: 712px;
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
        .margin_top_ccp {
            margin-top: 7em;
        }
        .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 820px;
                left: 20px;
                font-size: 7.5px;
                color: white;
                line-height: 1;
            }
    </style>
</head>
<body>
    {{-- SECCIÓN DE LA CABECERA --}}
    <header>
        <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
        <h6><small><small>{{$leyenda}}</small></small></h6><p class='direccion'>
</header>
    {{-- SECCIÓN DE LA CABECERA FIN --}}
    {{-- SECCIÓN DE PIE DE PÁGINA --}}
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
    {{-- SECCIÓN DE PIE DE PÁGINA FIN --}}
    {{-- SECCIÓN DE CONTENIDO --}}
    <div class="contenedor">
        <div align=right style="font-size:11px;"><b>DIRECCIÓN TÉCNICA ACADÉMICA. </b></div>
        <div align=right style="font-size:11px;"><b>MEMORANDUM NO. {{ $num_memo_planeacion }}</b></div>
        <div align=right style="font-size:11px;"><b>TUXTLA GUTIÉRREZ, CHIAPAS; {{ $fecha_ahora_espaniol }}</b></div>
        <br><br>
        <div align=left style="font-size:12px;"><b>{{ $directorPlaneacion->nombre." ".$directorPlaneacion->apellidoPaterno." ".$directorPlaneacion->apellidoMaterno }}.</b></div>
        <div align=left style="font-size:11px;"><b>{{ $directorPlaneacion->puesto }}.</b></div>
        <div align="left" style="font-size: 11px;"><b>Presente</b></div>
        <br><br><br><br>
        <div align="justify" style="font-size:16px;">
           <p>
            Por este medio y en seguimiento a la integración del Formato T de las Unidades de Capacitación
            de este Instituto correspondiente al mes de <b>{{$mesUnity}}</b> de la anualidad en curso; me es grato infórmarle
            que fue revisado y liberado el reporte estadístico denominado Formato T con la cantidad de <strong>{{$totalCursos}}</strong> cursos.
           </p>
        </div>
        <br>

        <br>
        <div align="justify" style="font-size:16px;">Sin más por el momento agradezco de su atención y le envío un cordial saludo.</div>
        {{-- <br> --}}

        <br>
        <br><br>
        <div style="font-size:11px;"> <b>A T E N T A M E N T E</b> </div>

        <div class="margin_top_ccp">
            <div style="font-size:11px;"> <b> {{ $reg_unidad->dacademico }} </b> </div>
            <div style="font-size:11px;"> <b> {{ $reg_unidad->pdacademico }} </b> </div>
            <br><br><br>
            <div style="font-size:9px;"> <b>C.c.p  {{ $reg_unidad->dgeneral }}  , {{ $reg_unidad->pdgeneral }} . Para su conocimiento.</b> </div>
            <div style="font-size:9px"><b>C.c. {{ $directorio->nombre." ".$directorio->apellidoPaterno." ".$directorio->apellidoMaterno }} - {{ $directorio->puesto }} . Para su conocimiento.</b></div>
            <div style="font-size:9px;"> <b>Archivo.</b> </div>
            <div style="font-size:8px;"> <b>Validó: {{ $reg_unidad->dacademico }} - {{ $reg_unidad->pdacademico }}</b></div>
            <div style="font-size:8px;"> <b>Elaboró: {{ $jefeDepto->nombre." ".$jefeDepto->apellidoPaterno." ".$jefeDepto->apellidoMaterno }} - {{ $jefeDepto->puesto }} . </b></div>
            {{-- <div style="font-size:8px;"> <b>Elaboró:  {{ $reg_unidad->academico }} .  {{ $reg_unidad->pacademico }} .</b></div> --}}
            <br><br>
        </div>

    </div>
    {{-- SECCIÓN DE CONTENIDO FIN --}}

</body>
</html>
