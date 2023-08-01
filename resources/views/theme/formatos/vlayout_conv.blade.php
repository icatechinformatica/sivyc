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
        margin-top: -10px;
        margin-bottom: 80px;
    }
    @page {
        margin: 150px 60px 50px 95px;
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
    .direccion {
            top: 0.85cm;
            text-align: left;
            position: absolute;
            bottom: 60px;
            left: 6px;
            font-size: 8px;
            color:#FFF;
            font-weight: bold;
            line-height: 1;
            width: 240px;
            margin-top: 5px;
            margin-left: 2px;
    }
    </style>
    @yield("css")
</head>

<body>
    <header>
        @yield('header')
    </header>
    <footer>
        <div style="position: relative;";>
            <img style=" position: absolute;" src='img/formatos/footer_horizontal.jpeg' width="100%">
            {{-- <p class="direccion">@yield('footer')</p> --}}
            @php $direccion = explode("*", $direccion);  @endphp
            {{-- <p class='direccion' style="width: 80%; margin: 0 auto;"><b>@foreach($direccion as $point => $ari)@if($point != 0) <br> @endif {{$ari}}@endforeach</b></p> --}}
            <div align="justify" class="direccion">
                @foreach($direccion as $point => $ari){{$ari.' '}}@endforeach
            </div>
        </div>
    </footer>

    @yield("body")
    @yield("js")
</body>
</html>
