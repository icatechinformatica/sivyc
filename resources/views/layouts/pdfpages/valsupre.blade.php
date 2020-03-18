<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style type="text/css">
* {
        box-sizing: border-box;
        }

        #wrappertop {
        margin-top: 0%
        background-image: url('img/search.png');
        background-position: 5px 10px;
        background-repeat: no-repeat;
        background-size: 32px;
        width: 100%;
        line-height: 60%;
        font-size: 16px;
        padding: 12px 20px 12px 40px;
        border: 1px solid transparent;
        margin-bottom: 0px;
        }
        #wrapperbot {
        background-image: url('img/search.png');
        background-position: 5px 10px;
        background-repeat: no-repeat;
        background-size: 32px;
        width: 100%;
        line-height: 70%;
        font-size: 16px;
        padding: 12px 20px 12px 40px;
        border: 1px solid transparent;
        margin-bottom: 0px;
        }

    div.a {
        text-align: center;
      }

      div.b {
        text-align: left;
      }

      div.c {
        text-align: right;
      }

      div.d {
        text-align: justify;
      }
    </style>

</head>
    <body>
        <div id="wrappertop">
            <div align=center> <FONT SIZE=0><b>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS<br/>
                <FONT SIZE=0>DIRECCION DE PLANEACION</FONT><br/>
                <FONT SIZE=0>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO</FONT><br/>
                <FONT SIZE=0>FORMATO DE VALIDACIÓN DE SUFICIENCIA PRESUPUESTAL</FONT><br/>
                <FONT SIZE=0>EN ATENCIÓN AL MEMORANDÚM {{$data2->no_memo}}</FONT></p>
            </div>
            <div class="c"><FONT SIZE=0>Folio de Validación: {{$data2->folio_validacion}}<br/>
            Fecha: {{$Dv}} de {{$Mv}} del {{$Yv}}</FONT>
            </div>
            <div class="b"> <FONT SIZE=0>UNIDAD DE CAPACITACIÓN {{$data2->unidad_capacitacion}}</font><br/>
                <FONT SIZE=0><b>{{$data2->nombre_remitente}}</b></FONT><br/>
                <FONT SIZE=0><b>{{$data2->puesto_remitente}}</b></FONT><br/>
                <FONT SIZE=0><b>PRESENTE</b></FONT><br/></div>
                <div class="d"> <FONT SIZE=0>En atención a su solicitud con memorandúm No.{{$data2->no_memo}} de fecha {{$D}} de {{$M}} del {{$Y}}; me permito comunicarle lo siguiente:<br/></font>
                    <font size=0>La Secretaria de HAcienda aprobó el presupuesto del Instituto de Capacitación y Vinculación Técnologica del Estado de Chiapas, en lo general para el Ejercicio Fiscal 2020, en ese sentido, con Fundamento en el Art. 17 Fracción V, VI y 26
                    del decreto de presupuesto de egresos del estado de Chiapas para el Ejercicio Fiscal 2020 y en pagos del Instituto de Capacitación y Vinculación Técnologica del Estado de Chiapas por servicios de capacitación, al Padrón de Instructores del ICATECH
                    y a la clave de autorización de apertura de cursos y demás disposiciones normativas aplicables vigentes; le informo que una vez revisada su solicitud y la información descrita en el formato de Validación de Suficiencia Presupuestal, se otorga la Validación
                    Presupuestal, con el fin de que conforme a lo indicado en la normatividad aplicable vigente se continue y se cumpla con los procedimientos administrativos que correspondan, observando además el contrato de prestación de servicios profesionales por honorarios en su
                    modalidad de Horas-Curso que celebran el ICATECH con el prestador de servicio.<br/></font>
                    <font size=0>Por lo anterior, me permito remitir a usted el original de la solicitud, asi como su respectivo respaldo documenta, debidamente validado presupuestalmente.<br/></font>
                    <font size=0>La presente validación presupuestal no implica ninguna autorización de pago de recursos, si no que únicamente se refiere a la verificación de la disponibilidad presupuestal, No omito manifestarle que, en estricto apego a la normatividad vigente establecida,
                    el área administrativa solicitante, es responsable de la correcta aplicación de los recursos públicos validados, en tal sentido el ejercicio y comprobación del gasto, deberá sujetarse a las disposiciones legales aplicables para tal efecto.<br/></font>
                </div>
            <br><br>
        </div>
            <div class="form-row">
                <table width="700" class="table table-bordered" id="table-one">
                    <thead>
                        <tr class="active">
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
                            <td scope="col"><small>Fuente de financiamiento federal</small></td>
                            <td scope="col" ><small>OBSERVACION</small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key=>$item)
                            <tr>
                                <td><small>{{$item->numero_presupuesto}}</small></td>
                                <td><small>{{$item->fecha}}</small></td>
                                <td><small>{{$item->nombre}} {{$item->apellidoPaterno}} {{$item->apellidoMaterno}}</small></td>
                                <td><small>{{$item->unidad}}</small></td>
                                <td><small>{{$item->curso_nombre}}</small></td>
                                <td><small>{{$item->clave}}</small></td>
                                <td><small>{{$item->ze}}</small></td>
                                <td><small>{{$item->horas}}</small></td>
                                <td><small>{{$item->importe_hora}}</small></td>
                                <td><small>{{$item->iva}}</small></td>
                                <td><small>12101 Honorarios</small></td>
                                <td><small>{{$item->importe_total}}</small></td>
                                <td><small>  X  </small></td>
                                <td><small></small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        <div id="wrapperbot">
            <div align=center>
                <small>{{$data2->nombre_firmante}}</small>
                <br>________________________________________<br/>
                <br><small>{{$data2->puesto_firmante}}</small></b>
            </div>
            <br><br><br><p><FONT SIZE=1><b>C.c.p.</b>{{$data2->val_ccp1}}.-{{$data2->val_ccpp1}}.-Para su conocimiento<br/>
                <FONT SIZE=1><b>C.c.p.</b>{{$data2->val_ccp2}}.-{{$data2->val_ccpp2}}.-mismo fin</FONT><br/>
                <FONT SIZE=1><b>C.c.p.</b>{{$data2->val_ccp3}}.-{{$data2->val_ccpp3}}.-mismo fin</FONT><br/>
                <FONT SIZE=1><b>C.c.p.</b>{{$data2->val_ccp4}}.-{{$data2->val_ccpp4}}.-mismo fin</FONT><br/>
                <FONT SIZE=1><b>C.c.p.</b>Archivo/ Minutario</FONT><br/>
                </p>
        </div>
    </body>
</html>
