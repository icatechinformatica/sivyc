<!DOCTYPE HTML>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div id="wrapper">
        <div align=center> <b>UNIDAD DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE CHIAPAS
            <br>DIRECCIÓN DE PLANEACIÓN
            <br>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO
            <br>FORMATO DE SOLICITUD DE SUFICIENCIA PRESUPUESTAL
            <br>UNIDAD DE CAPACITACIÓN {{$data2->unidad_capacitacion}} anexo de Memorándum No.{{$data2->no_memo}}</b> </div>
        </div>
            <br><br>
        <div class="form-row">
            <table width="700" class="table table-bordered" id="table-one">
                <thead>
                    <tr>
                        <td scope="col"><small>No. DE SUFICIENCIA</small></td>
                        <td scope="col" ><small>FECHA</small></td>
                        <td scope="col" ><small>INSTRUCTOR</small></td>
                        <td width="10px"><small>UNIDAD/ A.M. DE CAP.</small></td>
                        <td scope="col" ><small>CURSO</small></td>
                        <td scope="col"><small>CLAVE DEL GRUPO</small></td>
                        <td scope="col" ><small>ZONA ECÓNOMICA</small></td>
                        <td scope="col"><small>HSM (horas)</small></td>
                        <td scope="col" ><small>IMPORTE POR HORA</small></td>
                        <td scope="col"><small>IVA 16%</small></td>
                        <td scope="col" ><small>PARTIDA/ CONCEPTO</small></td>
                        <td scope="col"><small>IMPORTE</small></td>
                        <td scope="col" ><small>OBSERVACION</small></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key=>$item)
                        <tr>
                            <td scope="col" class="text-center"><small>{{$item->folio_validacion}}</small></td>
                            <td scope="col" class="text-center"><small>{{$item->fecha}}</small></td>
                            <td scope="col" class="text-center"><small>{{$item->nombre}} {{$item->apellidoPaterno}} {{$item->apellidoMaterno}}</small></td>
                            <td scope="col" class="text-center"><small>{{$item->unidad}}</small></td>
                            <td scope="col" class="text-center"><small>{{$item->curso_nombre}}</td>
                            <td scope="col" class="text-center"><small>{{$item->clave}}</small></td>
                            <td scope="col" class="text-center"><small>{{$item->ze}}</small></td>
                            <td scope="col" class="text-center"><small>{{$item->dura}}</small></td>
                            <td scope="col" class="text-center"><small>{{$item->importe_hora}}</td>
                            <td scope="col" class="text-center"><small>{{$item->iva}}</td>
                            <td scope="col" class="text-center"><small>12101 Honorarios</td>
                            <td scope="col" class="text-center"><small>{{$item->importe_total}}</td>
                            <td scope="col" class="text-center"><small>{{$item->comentario}}</small></td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <br>
            <div align=center> <b>SOLICITA
                <br>
                <br>
                <br><small>{{$getremitente->nombre}} {{$getremitente->apellidoPaterno}} {{$getremitente->apellidoMaterno}}</small>
                <br>________________________________________
                <br><small>{{$getremitente->puesto}} DE {{$data2->unidad_capacitacion}}</small></b>
            </div>
        </div>
    </div>
</body>
</html>
