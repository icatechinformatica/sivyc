<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        body{font-family: sans-serif}
        @page {margin: 20px 30px 20px 30px;size: letter;}
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

        #fondo1 {
            background-image: url('img/membretado/membretado.jpg');
            background-size: 111%,120%;
            background-position: center;
            /* width: 100%;
            margin: auto;
            height: 100%; */
        }
    </style>
    @yield("content_script_css")
</head>
{{-- cambio prueba --}}
{{-- @section('content') --}}
<body id='fondo1'>
    {{-- <header>
        <h6><small><small>{{$leyenda}}</small></small></h6><p class='direccion'>
    </header> --}}
    <footer>
        <p class='direccion'>
            @foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}} @endforeach
        </p>
    </footer>
    @yield("content")

    @yield("script_content_js")
</body>
</html>
{{-- @endsection --}}
