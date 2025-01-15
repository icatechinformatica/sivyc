<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        body{font-family: sans-serif; padding: 100px 50px 100px 30px;  }
        @page {margin: 2px; size: letter; border: 1px solid red;}
        /* header { position: fixed; left: 0px; top: 0px; right: 0px;text-align: center;width:100%;line-height: 30px;} */
        header { position: fixed; left: 0px; top: 70px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        img.izquierda {float: left;width: 100%;height: 60px;}
        img.izquierdabot {
            float: inline-end;
            width: 712px;
            height: 100px;
        }
        img.derechabot {position:fixed;right: 50px;width: 350px;height: 60px;}
        img.derecha {float: right;width: 50%;height: 60px;}
        footer {position:fixed;left:0px;bottom:0px;width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        .direccion
        {
            text-align: right;
            position: absolute;
            top: -90px; /*820*/
            right: 40px;
            font-size: 10px;
            color: black;
            line-height: 1;
        }

        .direccion_old
        {
            text-align: left;
            position: absolute;
            top: -5%;
            left: 5%;
            font-size: 8.5px;
            color: rgb(255, 255, 255);
            line-height: 1;
        }

        #fondo1 {
            background-image: url('img/membretado/membretado.jpg');
            background-size: 103% 100%;
            background-position: center;
            /* width: 100%;
            margin: auto;
            height: 100%; */
        }

        #fondo_old {
            background-image: url('img/membretado/membretado_old.jpg');
            background-size: 98%,100%;
            background-position: center;
        }

        .content {

            border: 1px solid blue;
        }
    </style>
    @yield("content_script_css")
</head>
{{-- cambio prueba --}}
{{-- @section('content') --}}
<body @if(!isset($fecha) || $fecha < '08-12-2024') id='fondo1' @else id='fondo_old' @endif >
    <header  >
        @if(isset($leyenda))
            <small>{{$leyenda}}</small>
        @elseif(isset($distintivo))
            <small>{{$distintivo}}</small>
        @endif
    </header>
    <footer>
        @if(isset($reporte_fotografico))
            @if(!is_null($uuid))
                <div style="margin-left: 30px; margin-right: 30px; position: absolute; top: -155px; left: 15px; font-size:10px; text-align:justify">
                    <span style="">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha: {{$fecha_sello}} Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas.</span>
                </div>
            @endif
        @endif
        <p @if(!isset($fecha) || $fecha < '07-12-2024') class='direccion' @else class='direccion_old' @endif>
            @if(!is_array($direccion)) @php $direccion = explode("*",$direccion) @endphp @endif
            @foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}} @endforeach
        </p>
    </footer>
    <div class="content">
        @yield("content")
    </div>
    @yield("script_content_js")
</body>
</html>
{{-- @endsection --}}
