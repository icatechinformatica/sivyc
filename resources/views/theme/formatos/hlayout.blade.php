<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        @page {size: Letter landscape; margin: 0px}
        body{
            font-family: sans-serif;
            margin: 0px;
            padding: 35px            
        }       
        header { position: fixed;    
            margin: 0px;
            padding: 0px;
            width:100%;         
            color: black;
            text-align: center;            
            font-size: 11px;
            font-weight: bold;
            top: 35px;
        }
        
        footer { 
            bottom: 0;
            width: 100%;
            height: 50px;            
            line-height: 50px;
        }
        .direccion {
            text-align: right;
            position: absolute;
            bottom: 30px;
            right: 30px;
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
            position: fixed;
            padding: 0px;
            margin: 0px;
            top: 10px;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('img/membretado/membretado horizontal.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
        }

        #fondo_old {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('img/membretado/membretado horizontal_old.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
        }
    </style>
    @if(isset($marca))
        @if ($marca)
            <style>
                header:after {
                content: "BORRADOR";                
                color: rgba(40, 40, 43, 0.35);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 30%;
                right: 0;
                bottom: 0;
                left: 20%;
                transform: rotate(-25deg);
                font-family: sans-serif;
                font-weight: bold; 
                font-size: 12em;
                }
            </style>
        @endif
    @endif
    @yield("content_script_css")
</head>
@php
    $distintivo = !empty($distintivo) ? str_replace("*", "<br/>", $distintivo) : '';
    $fondoNuevo = ($fechaLayout ?? $fecha ?? '9999-12-31') > '08-12-2024';
    
    $direccion = !empty($direccion) ? str_replace("*", "<br/>", $direccion) : '';
    $clase_direccion = (!isset($fecha) || $fecha > '07-12-2024') ? 'direccion' : 'direccion_old';

@endphp
<body>    
    <div id="{{ $fondoNuevo ? 'fondo1' : 'fondo_old' }}"></div>
    <div class="content">
        <header>{!! $distintivo !!}</header>
        <footer>
            <p class="{{ $clase_direccion }}">
                {!! $direccion !!}
            </p>
        </footer>
        @yield("content")
    </div>
    @yield("script_content_js")
</body>
</html>