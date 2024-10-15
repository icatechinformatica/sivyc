<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            /* font-family: sans-serif;
            font-size: 16px; */
            /* margin: 10px; */
            /* margin-bottom: 80px; */
        }

        @page {
            /* margin: 110px 20px 53px; */
            margin: 120px 30px 50px 30px;
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
            bottom: -35px;
            right: 0px;
            height: 100px;
            text-align: center;
            line-height: 60px;
        }

        /* .direccion {position: absolute;  top: 0.1cm; width:380px; margin-left:0px; height:auto; font-family: sans-serif; font-size: 9px; color:#FFF; } */
        .direccion {
            text-align: left;
            position: absolute;
            bottom: 9px;
            left: 10px;
            font-size: 8px;
            color: rgb(255, 255, 255);
            line-height: 1;
            top: 0.75cm;
        }

        img.izquierdabot {
            float: inline-end;
            width: 100%;
            height: 80px;
        }
    </style>
    @if (isset($marca))
        @if ($marca)
            <style>
                header:after {
                    content: "BORRADOR";
                    font-size: 8em;
                    color: rgba(40, 40, 43, 0.35);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: fixed;
                    top: 90%;
                    right: 0;
                    bottom: 0;
                    left: 150%;
                    transform: rotate(-50deg);
                }
            </style>
        @endif
    @endif

    @yield('content_script_css')
</head>
{{-- cambio prueba --}}
{{-- @section('content') --}}

<body style="margin-top:18px; margin-bottom:10px;">
    <header>
        <img src='img/formatos/bannervertical.jpeg' width="100%">
        <p style="text-align: center; font-weight: bold; font-style: italic; padding:0px; font-size: 11px;">
            {{ $distintivo }}</p>
        </div>
    </header>
    <footer>
        <div style="position: relative;">
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
            <p class='direccion'>
                <b>
                    @php $direccion = explode("*",$direccion) @endphp
                    @foreach ($direccion as $point => $ari)
                        @if ($point != 0)
                            <br>
                        @endif {{ $ari }}
                    @endforeach
                    <br>
                    {{-- @if (!is_null($funcionarios['dunidad']['telefono']))Teléfono: {{$funcionarios['dunidad']['telefono']}} @endif  --}}
                </b>
            </p>
        </div>
    </footer>
    @yield('content')

    @yield('script_content_js')
</body>

</html>
{{-- @endsection --}}
