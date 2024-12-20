<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        body{font-family: sans-serif}
        @page {margin: 20px 30px 20px 30px; size: letter;}
        /* header { position: fixed; left: 0px; top: 0px; right: 0px;text-align: center;width:100%;line-height: 30px;} */
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
            top: -100px; /*820*/
            right: 20px;
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
            background-size: 111%,120%;
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
    </style>
    @yield("content_script_css")
</head>
{{-- cambio prueba --}}
{{-- @section('content') --}}
<body @if(!isset($fecha) || $fecha < '08-12-2024') id='fondo1' @else id='fondo_old' @endif >
    {{-- <header>
        <h6><small><small>{{$leyenda}}</small></small></h6><p class='direccion'>
    </header> --}}
    <footer>
        <p @if(!isset($fecha) || $fecha < '07-12-2024') class='direccion' @else class='direccion_old' @endif>
            @if(!is_array($direccion)) @php $direccion = explode("*",$direccion) @endphp @endif
            @foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}} @endforeach
        </p>
    </footer>
    @yield("content")

    @yield("script_content_js")
</body>
</html>
{{-- @endsection --}}
