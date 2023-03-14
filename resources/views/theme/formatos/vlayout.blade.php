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
        font-size: 1.3em;
        margin: 10px;
    }
    @page {
        margin: 110px 20px 60px;
    }
    header {
        position: fixed;
        left: 0px;
        top: -110px;
        right: 0px;
        color: black;
        text-align: center;
        line-height: 30px;
        height: 100px;
    }
    footer {
        position: fixed;
        left: 0px;
        bottom: -30px;
        right: 0px;
        height: 100px;
        text-align: center;
        line-height: 60px;
    }
    .direccion {position: absolute;  top: 1.3cm; width:380px; margin-left:28px; height:auto; font-family: sans-serif; font-size: 9px; color:#FFF; }
    </style>
    @yield("content_script_css")
</head>
{{-- cambio prueba --}}
@section('content')
    <header>
        <img src='img/formatos/bannervertical.jpeg' width="100%">
        <p style="text-align: center; font-weight: bold; font-style: italic; margin-top:-10px; padding:0px; font-size: 11px;">{{$distintivo}}</p>
    </header>
    <footer>
        <div style="position: relative;";>
            <img style=" position: absolute;" src='img/formatos/footer_horizontal.jpeg' width="100%">
            @php $direccion = explode("*", $direccion);  @endphp
            <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
        </div>
    </footer>
    @yield("content")

    @yield("script_content_js")
</body>
</html>
@endsection
