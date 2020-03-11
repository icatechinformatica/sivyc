<!DOCTYPE HTML>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style type="text/css">
    #aviso-movil-horizontal { display: none; }
    @media only screen and (orientation:portrait) {
        #wrapper { display:none; }
        #aviso-movil-horizontal { display:block; }
    }
    @media only screen and (orientation:landscape) {
        #aviso-movil-horizontal { display:none; }
    }
</style>
</head>
<body>

    <div id="aviso-movil-horizontal">
        Por favor, coloca tu móvil en horizontal.
    </div>
    <div id="wrapper">
        <div align=center> <b>UNIDAD DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE CHIAPAS
            <br>DIRECCIÓN DE PLANEACIÓN
            <br>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO
            <br>FORMATO DE SOLICITUD DE SUFICIENCIA PRESUPUESTAL
            <br>UNIDAD DE CAPACITACIÓN {{$data2->unidad_capacitacion}} anexo de Memorándum No.{{$data2->no_memo}}</b> </div>

            <br><br>
            <div class="table-responsive">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" class="text-right">No. DE SUFICIENCIA</th>
                        <th scope="col" class="text-center">FECHA</th>
                        <th scope="col" class="text-center">INSTRUCTOR</th>
                        <th scope="col" class="text-right">UNIDAD&/ACCIÓN MÓVIL DE CAPACITACIÓN</th>
                        <th scope="col" class="text-center">CURSO</th>
                        <th scope="col" class="text-right">CLAVE DEL GRUPO</th>
                        <th scope="col" class="text-center">ZONA ECÓNOMICA</th>
                        <th scope="col" class="text-right">HSM (horas)</th>
                        <th scope="col" class="text-center">IMPORTE POR HORA</th>
                        <th scope="col" class="text-right">IVA 16%</th>
                        <th scope="col" class="text-center">PARTIDA/CONCEPTO</th>
                        <th scope="col" class="text-right">IMPORTE</th>
                        <th scope="col" class="text-right">FUENTE DE FINANCIAMIENTO FEDERAL</th>
                        <th scope="col" class="text-center">OBSERVACIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key=>$item)
                        <tr>
                            <th scope="col" class="text-center">{{$item->numero_presupuesto}}</th>
                            <th scope="col" class="text-center">{{$item->fecha}}</th>
                            <th scope="col" class="text-center">{{$item->nombre}} {{$item->apellidoPaterno}} {{$item->apellidoMaterno}}</th>
                            <th scope="col" class="text-center">{{$item->unidad}}</th>
                            <th scope="col" class="text-center">{{$item->curso_nombre}}</th>
                            <th scope="col" class="text-center">{{$item->clave}}</th>
                            <th scope="col" class="text-center">{{$item->ze}}</th>
                            <th scope="col" class="text-center">{{$item->horas}}</th>
                            <th scope="col" class="text-center">{{$item->importe_hora}}</th>
                            <th scope="col" class="text-center">{{$item->iva}}</th>
                            <th scope="col" class="text-center">12101 Honorarios</th>
                            <th scope="col" class="text-center">{{$item->importe_total}}</th>
                            <th scope="col" class="text-center">X</th>
                            <th scope="col" class="text-center"></th>

                        </tr>
                    @endforeach
                </tbody>
                <tfoot>

                </tfoot>
            </table>
            <br>
            <br>
            <div align=center> <b>SOLICITA
                <br>
                <br>
                <br><small>{{$data2->nombre_remitente}}</small>
                <br>________________________________________
                <br><small>{{$data2->puesto_remitente}} {{$data2->unidad_capacitacion}}</small></b>
            </div>
        </div>
    </div>
</body>
</html>
