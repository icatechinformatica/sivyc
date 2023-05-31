<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="{{ public_path('\BootstrapCustomized\bootstrap-onlytables.min.css') }}">
        <style>
            body{
                font-family: sans-serif;
            }
            @page {
                margin: 110px 80px 110px;
            }
            header { position: fixed;
                left: 0px;
                top: -100px;
                right: 0px;
                height: 60px;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            }
            header h1{
                margin: 10px 0;
            }
            header h2{
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
            div.content
            {
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
            <br><h6>"2021, Año de la Independencia"</h6>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
            <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
        </footer>
        <div id="wrapper">
            <div align=center><b><h6>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE CHIAPAS
                <br>REPORTE DE RELACION ALUMNOS-VINCULADOR
            </div>
            <div class="form-row">
                <table width="700" class="table table-striped" id="table-one">
                    <thead>
                        <tr>
                            <td scope="col"><small style="font-size: 10px;">Sec. de Solic.</small></td>
                            <td scope="col"><small style="font-size: 10px;">UNIDAD</small></td>
                            <td scope="col"><small style="font-size: 10px;">ALUMNO</small></td>
                            <td scope="col"><small style="font-size: 10px;">MATRICULA</small></td>
                            <td width="10px"><small style="font-size: 10px;">CURP</small></td>
                            <td scope="col"><small style="font-size: 10px;">SEXO</small></td>
                            <td scope="col"><small style="font-size: 10px;">ESPECIALIDAD</small></td>
                            <td scope="col"><small style="font-size: 10px;">CURSO</small></td>
                            <td scope="col"><small style="font-size: 10px;">CLAVE DE CURSO</small></td>
                            <td scope="col"><small style="font-size: 10px;">MODALIDAD</small></td>
                            <td scope="col"><small style="font-size: 10px;">TIPO DE CURSO</small></td>
                            <td scope="col"><small style="font-size: 10px;">INSCRITO SICE</small></td>
                            <td scope="col"><small style="font-size: 10px;">INSCRITO SIVYC<small></td>
                            <td scope="col"><small style="font-size: 10px;">REALIZO SICE<small></td>
                            <td scope="col"><small style="font-size: 10px;">REALIZO SIVYC<small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key=>$item)
                            <tr>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$key}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->unidad}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->alumno}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->matricula}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$curp[$key]}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$sexo[$key]}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->espe}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->curso}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->clave}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->mod}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->tcapacitacion}}</td>
                                @if ($item->realizo != NULL)
                                    <td scope="col" class="text-center"><small style="font-size: 10px;">X</td>
                                @else
                                    <td scope="col" class="text-center"><small style="font-size: 10px;"></td>
                                @endif
                                {{-- @php dd($realizo); @endphp --}}
                                @if (array_key_exists($key, $realizo) && $realizo[$key] != NULL)
                                    <td scope="col" class="text-center"><small style="font-size: 10px;">X</td>
                                @else
                                    <td scope="col" class="text-center"><small style="font-size: 10px;"></td>
                                @endif
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->realizo}}</small></td>
                                @if (array_key_exists($key, $realizo) && $realizo[$key] != NULL)
                                    <td scope="col" class="text-center"><small style="font-size: 10px;">{{$realizo[$key]}}</small></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </body>
</html>
