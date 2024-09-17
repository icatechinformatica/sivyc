<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ isset($title) ? $title : 'Plantilla de Reporte de Bitácora' }}</title>
    <style>
        header {
            position: fixed;
            left: 0px;
            right: 0px;
            color: black;
            text-align: center;
            line-height: 16px;
            height: 100px;
            top: -88px;
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

        body {
            font-family: sans-serif;
            /* margin-bottom: 70px; */
        }

        @page {
            margin: 110px 30px 27px 30px;
        }

        table {
            width: 100%;
        }

        #encabezado {
            margin-right: 40px;
            width: 100%;
        }

        #encabezado .fila #col_1 {
            width: 15%
        }

        #encabezado .fila #col_2 {
            text-align: center;
            width: 55%
        }

        #encabezado .fila #col_3 {
            width: 15%
        }

        #encabezado .fila td img {
            width: 50%
        }

        #encabezado .fila #col_2 #span1 {
            font-size: 13px;
        }

        #encabezado .fila #col_2 #span2 {
            font-size: 10px;
        }
    </style>
    @yield('contenido_css')
</head>

<body>
    <header>
        <table id="encabezado">
            <tr class="fila">
                <td id="col_1">
                    {{-- <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/reportes/logohorizontalica1.png'))) }}"
                        style="width: 150px; height: 50px;" /> --}}
                </td>
                <td id="col_2">
                    {{-- centrado contenido --}}
                    <span id="span1">INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE
                        CHIAPAS</span>
                    <br>
                    <span id="span2">UNIDAD DE APOYO ADMINISTRATIVO</span>
                    <br>
                    <span id="span2">ÁREA DE RECURSOS MATERIALES Y SERVICIOS</span>
                </td>
                <td id="col_3">
                    {{-- <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/reportes/chiapas.png'))) }}"
                        style="width: 120px; height: 45px;"> --}}
                </td>
            </tr>
        </table>

    </header>
    <footer>
        <div style="position: relative;";>
            {{-- <img style=" position: absolute;"
                src='data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/reportes/footervertical.jpeg'))) }}'
                width="100%"> --}}
        </div>
    </footer>
    @yield('contenido')
    @yield('contentJS')
</body>

</html>
