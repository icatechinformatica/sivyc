<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        body{font-family: sans-serif; padding: 160px 50px 145px 80px;  }
        @page {margin: 0px; size: letter; padding-left: 300px;}
        /* header { position: fixed; left: 0px; top: 0px; right: 0px;text-align: center;width:100%;line-height: 30px;} */
        header { position: fixed; left: 20px; top: 120px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        img.izquierda {float: left;width: 100%;height: 70px;}
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
            text-align: left;
            position: absolute;
            top: -120px; /*820*/
            right: 360px;
            font-size: 10px;
            color: black;
            line-height: 1;
        }

        #fondo1 {
            background-image: url('img/membretado/membretado_expeunico.jpg');
            background-size: 103% 100%;
            background-position: center;
            /* width: 100%;
            margin: auto;
            height: 100%; */
        }

        .content {

            /* border: 1px solid blue; */
        }
    </style>
    @yield("content_script_css")
</head>
{{-- cambio prueba --}}
{{-- @section('content') --}}
<body id='fondo1' >
    <header>
        @if(isset($leyenda) && !empty($leyenda))
            @if(!is_array($leyenda)) @php $leyenda = explode("*",$leyenda) @endphp @endif
             <p><small style="line-height: 1;">@foreach($leyenda as $keys => $part)@if($keys != 0)<br> @endif {{$part}} @endforeach</small></p>
            {{-- <small>{{$leyenda}}</small> --}}
        @elseif(isset($distintivo))
            @if(!is_array($distintivo)) @php $distintivo = explode("*",$distintivo) @endphp @endif
            <small>@foreach($distintivo as $keys => $part)@if($keys != 0)<br> @endif {{$part}} @endforeach</small>
            {{-- <small>{{$distintivo}}</small> --}}
        @endif
    </header>
    <footer>
        <p class='direccion'>
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
