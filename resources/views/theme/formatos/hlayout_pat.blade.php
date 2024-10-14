<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        @page { margin: 15px 30px 90px 30px; }
        body { margin-top: 120px; font-family: sans-serif; font-size: 9px; width:100%; padding:0px;}
        header { position: fixed; top: 0cm; text-align: center; font-weight: bold;}
        footer { position: fixed; top: 17.7cm; width:100%;}
        .direccion {position: absolute;  top: 1.3cm; width:400px; margin-left:25px; height:auto; font-family: sans-serif; font-size: 8px; color:#FFF; }
        .mielemento { position: absolute; z-index: 1; top: 0px; left: 370px; }
    </style>
    @if(isset($marca))
        @if ($marca)
            <style>
                header:after {
                content: "BORRADOR";
                font-size: 19em;
                color: rgba(40, 40, 43, 0.35);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 30%;
                right: 0;
                bottom: 0;
                left: 10%;
                transform: rotate(-25deg);
                }
            </style>
            @endif
    @endif

    @yield("css")
</head>
<body>
    <header>
        <img src='img/formatos/bannerhorizontal.jpeg' width="100%">
        <img class="mielemento" src='img/logoDGCFT.png' width="25%">
        {{-- <p style="margin-top:-55px; padding:0px; font-size: 11px; "><span  style="font-style: italic; display:block; padding-bottom:5px;">@if(isset($distintivo)){{ $distintivo }}@endif</span>@if(isset($titulo)){{ $titulo }}@endif</p> --}}
        @yield("header")
    </header>
    <section>
        @yield("body")
    </section>
    <footer>
        @yield("footer")
    </footer>
    {{-- <footer>
        @yield("footer")
        <div style="position: relative;";>
            <img style=" position: absolute;" src='img/formatos/footer_horizontal.jpeg' width="100%">
            @php $direccion = explode("*", $direccion);  @endphp
            <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
            <p class='direccion'><b>Calle 15 de Mayo B.Gpe</b></p>
        </div>
    </footer> --}}

    @yield("js")
</body>
</html>
