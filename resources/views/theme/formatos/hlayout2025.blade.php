<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        body{
            font-family: sans-serif;
            padding: 100px 50px 20px 50px;
            /* border: 1px solid red; */
        }
        @page {
            margin: 2px;
        }
        header { position: fixed;
            left: 35px;
            top: 70px;
            padding-left: 45px;
            height: 70px;
            width: 85%;
            /* background-color: white; */
            color: black;
            text-align: center;
            line-height: 60px;
        }
        header h1{
            margin: 10px 0;
        }
        header h2{
            margin: 0 0 10px 0;
        }
        footer {
            position: fixed;
            left: 0px;
            bottom: -90px;
            right: 0px;
            height: 100px;
            width: 85%;
            padding-left: 45px;
            background-color: white;
            color: black;
            text-align: center;
        }
        footer .page:after {
            content: counter(page);
        }
        footer table {
            width: 100%;
        }
        footer p {
            text-align: right;
        }
        footer .izq {
            text-align: left;
            }
        img.izquierda {
            float: left;
            width: 100%;
            height: 100%;
        }

        img.izquierdabot {
            float: inline-end;
            width: 100%;
            height: 100%;
        }

        .direccion {
            text-align: right;
            position: absolute;
            top: -110%;
            right: 0%;
            font-size: 10px;
            color: rgb(0, 0, 0);
            line-height: 1;
        }

        .direccion_old {
            text-align: left;
            position: absolute;
            top: -65%;
            left: 12%;
            font-size: 8.5px;
            color: rgb(255, 255, 255);
            line-height: 1;
        }

        #fondo1 {
            background-image: url('img/membretado/membretado horizontal.jpg');
            background-size: 100% 100%;
            background-position: center;
            /* width: 100%;
            margin: auto;
            height: 100%; */
        }

        #fondo_old {
            margin: 20px 30px 20px 30px;
            background-image: url('img/membretado/membretado horizontal_old.jpg');
            background-size: 98%,100%;
            background-position: center;
        }

        .content {
            margin-bottom: 120px;
            /* border: 1px solid red; */
        }
    </style>
    @if(isset($marca))
        @if ($marca)
            <style>
                header:after {
                content: "BORRADOR";
                font-size: 11em;
                color: rgba(40, 40, 43, 0.35);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 35%;
                right: 0;
                bottom: 0;
                left: 20%;
                transform: rotate(-25deg);
                }
            </style>
        @endif
    @endif
    @yield("content_script_css")
</head>
{{-- cambio prueba --}}
{{-- @section('content') --}}
<body @if((isset($fechaLayout) && $fechaLayout > '08-12-2024') ||!isset($fecha) || $fecha > '08-12-2024') id='fondo1' @else id='fondo_old' @endif >
    <header>
        @if(isset($leyenda))
        @if(!is_array($leyenda)) @php $leyenda = explode("*",$leyenda) @endphp @endif
         <p><small style="line-height: 1;">@foreach($leyenda as $keys => $part)@if($keys != 0)<br> @endif {{$part}} @endforeach</small></p>
        {{-- <small>{{$leyenda}}</small> --}}
    @elseif(isset($distintivo))
        @if(!is_array($distintivo)) @php $distintivo = explode("*",$distintivo) @endphp @endif
        <small style="line-height: 1;">@foreach($distintivo as $keys => $part)@if($keys != 0)<br> @endif {{$part}} @endforeach</small>
        {{-- <small>{{$distintivo}}</small> --}}
    @endif
    </header>
<footer>
    <p @if(!isset($fecha) || $fecha > '07-12-2024') class='direccion' @else class='direccion_old' @endif>
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
