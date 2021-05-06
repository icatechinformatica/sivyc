<!DOCTYPE HTML>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SOLICITUD MODIFICACIÓN DE CURSO</title>
    <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: sans-serif;
        }

        @page {
            margin: 110px 40px 110px;
        }

        header {
            position: fixed;
            left: 0px;
            top: -100px;
            right: 0px;
            height: 60px;
            background-color: white;
            color: black;
            text-align: center;
            line-height: 60px;
        }

        header h1 {
            margin: 10px 0;
        }

        header h2 {
            margin: 0 0 10px 0;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: -90px;
            right: 0px;
            height: 60px;
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
            width: 300px;
            height: 60px;
        }

        img.izquierdabot {
            float: inline-end;
            width: 450px;
            height: 60px;
        }

        img.derechabot {
            position: absolute;
            left: 700px;
            width: 350px;
            height: 60px;

        }

        img.derecha {
            float: right;
            width: 200px;
            height: 60px;
        }

        div.content {
            margin-top: 60%;
            margin-bottom: 70%;
            margin-right: -25%;
            margin-left: 0%;
        }

    </style>
</head>

<body>
    <header>
        <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
        <img class="derecha" src="{{ public_path('img/chiapas.png') }}">
        <br>
        <div id="wrapper">
            <div align=center>
                <b>
                    <h6>UNIDAD DE CAPACITACIÓN {{ $solicitud[0]->unidad }}
                        <br>DEPARTAMENTO ACADÉMICO
                        <br>SOLICITUD DE REPROGRAMACIÓN O CANCELACIÓN DE CURSO
                        <br>ARC-02
                    </h6>
                </b>
            </div>
        </div>
        <h6>"2021, Año de la Independencia"</h6>
        <br>
    </header>
    <footer>
        <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
        <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
    </footer>
    

    <br><br><br>
    <table class="mt-3" width="100%">
        <tbody>
            <tr>
                <td width="50%"><small>PARA: <strong>{{$para[0]->nombre}} {{$para[0]->apellidoPaterno}} {{$para[0]->apellidoMaterno}}</strong></small></td>
                <td class="text-right" width="50%"> <small>UNIDAD DE CAPACITACIÓN: <strong>{{ $solicitud[0]->unidad }}</strong></small></td>
            </tr>
            <tr>
                <td width="50%"><small>DE: <strong>example</strong></small></td>
                <td class="text-right" width="50%"><small>
                    MEMORÁNDUM NO. ICATECH: <strong>{{$solicitud[0]->num_solicitud}}</strong></small>
                </td>
            </tr>
            <tr>
                <td width="50%"><small>CC: <strong>ARCHIVO MINUTARIO</strong></small></td>
                <td class="text-right" width="50%"><small>FECHA: <strong>{{$solicitud[0]->fecha_solicitud}}</strong></small></td>
            </tr>
        </tbody>
    </table>

    <div class="form-row">
        <table width="700" class="table table-bordered table-striped" id="table-one">
            <thead>
                <tr>
                    <th scope="col"><small style="font-size: 9px;">NOMBRE DEL CURSO</small></th>
                    <th scope="col"><small style="font-size: 9px;">MOD</small></th>
                    <th scope="col"><small style="font-size: 9px;">DURA</small></th>
                    <th scope="col"><small style="font-size: 9px;">CLAVE</small></th>
                    <th scope="col"><small style="font-size: 9px;">No. MEMORANDUM AUT.</small></th>
                    <th scope="col"><small style="font-size: 9px;">INSTRUCTOR</small></th>
                    <th scope="col"><small style="font-size: 9px;">INICIO</small></th>
                    <th scope="col"><small style="font-size: 9px;">TERMINO</small></th>
                    <th scope="col"><small style="font-size: 9px;">ESPACIO FÍSICO</small></th>
                    <th scope="col"><small style="font-size: 9px;">SOLICITUD</small></th>
                    <th scope="col"><small style="font-size: 9px;">OBSERVACIONES</small></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitud as $item)
                    <tr>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->curso }}</small></td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->mod }}</small></td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->dura }}</small></td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->clave }}</small></td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->munidad}}</td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{$item->nombre}}</small></td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->inicio }}</small></td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->termino }}</small></td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->efisico }}</td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->opcion_solicitud }}</td>
                        <td scope="col" class="text-center"><small style="font-size: 9px;">{{ $item->obs_solicitud }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    </div>

    <table width="100%">
        <tbody>
            <tr>
                <td class="text-center" width="33%">SOLICITA/ELABORÓ</td>
                <td class="text-center" width="33%">Vo. Bo.</td>
                <td class="text-center" width="33%"></td>
            </tr>
            <tr>
                <td>
                    <small>.</small>
                </td>
            </tr>
            <tr>
                <td class="text-center" width="33%">________________________________________</td>
                <td class="text-center" width="33%">________________________________________</td>
                <td class="text-center" width="33%"></td>
            </tr>
            <tr>
                <td class="text-center" width="33%"><small>JEFE DE ACADÉMICO</small></td>
                <td class="text-center" width="33%"><small>DIRECTOR(A) DE LA UNIDAD DE CAPACITACIÓN</small></td>
                <td class="text-center" width="33%"><small>SELLO DE LA UNIDAD DE CAPACITACIÓN</small></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
