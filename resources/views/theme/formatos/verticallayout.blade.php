<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 1.3em;
            /* margin: 10px; */
            /* margin-bottom: 80px; */
        }

        @page {
            /* margin: 110px 20px 53px; */
            margin: 120px 30px 40px 30px;
        }

        header {
            position: fixed;
            left: 0px;
            top: -110px;
            right: 0px;
            color: black;
            text-align: center;
            /* line-height: 15px; */
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

        /* .direccion {position: absolute;  top: 0.1cm; width:380px; margin-left:0px; height:auto; font-family: sans-serif; font-size: 9px; color:#FFF; } */
        .direccion {
            top: 0.85cm;
            text-align: left;
            position: absolute;
            bottom: 60px;
            left: 6px;
            font-size: 8px;
            color: #FFF;
            font-weight: bold;
            line-height: 1;
        }

        .contenido_centrado {
            position: absolute;
            left: 50%;

            /* padding-top: -10rem; */
            margin-left: -120px;
            height: 50px;
            width: 40%;
        }
        .contenido_centrado span {
            font-size: 10px;
            margin-top: 0.8rem;
            letter-spacing: 0.8px; /* Adjust as needed */
            /* display: block; */
        }
    </style>
    @yield('content_script_css')
</head>
{{-- cambio prueba --}}
{{-- @section('content') --}}

<body>
    <header>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/formatos/bannervertical.jpeg'))) }}"
            width="100%">
        <div class="contenido_centrado">
            <p style="text-align:center; padding: 0; font-size:10px; font-weight: bold; font-style: italic;">INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS.
               <br> UNIDAD EJECUTIVA
              <br> DEPARTAMENTO DE INFORMÁTICA</p>
        </div>
        <p
            style="text-align: center; font-weight: bold; font-style: italic; margin-top: 15px; padding:0px; font-size: 10px;">
            {{ $distintivo }}</p>
        </div>
    </header>
    <footer>
        <div style="position: relative;">
            <img style=" position: absolute;"
                src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/formatos/footer_horizontal.jpeg'))) }}"
                width="100%">
            @php $direccion = explode("*", $direccion);  @endphp
            <p class='direccion'><b>
                    @foreach ($direccion as $point => $ari)
                        @if ($point != 0)
                            <br>
                        @endif {{ $ari }}
                    @endforeach
                </b></p>
        </div>
    </footer>
    @yield('content')

    @yield('script_content_js')
</body>

</html>
{{-- @endsection --}}
