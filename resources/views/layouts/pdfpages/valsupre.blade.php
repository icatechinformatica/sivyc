<?php
if ($data[0]->tipo_curso=='CERTIFICACION'){
    $tipo='CERTIFICACIÓN_EXTRAORDINARIA';
}
else{
    $tipo='CURSO';
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style type="text/css">
       *{
        box-sizing: border-box;
        }

        @page {
            margin: 110px 40px 80px;
            }
            header { position: fixed;
                left: 0px;
                top: -80px;
                right: 0px;
                height: 60px;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            }
            body{
                font-family: sans-serif;
                font-size: 1.3em;
                margin: 10px;
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
                bottom: -40px;
                right: 0px;
                height: 60px;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
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
                width: 350px;
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

        #wrappertop {
        margin-top: 0%;
        background-position: 5px 10px;
        background-repeat: no-repeat;
        background-size: 32px;
        width: 100%;
        line-height: 60%;
        font-size: 16px;
        padding: 0px;
        border: 1px solid transparent;
        margin-bottom: 0px;
        }
        #wrapperbot {
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

    <script defer>
        function alumn(h, m)
        {
            console.log(h);
            var total = h + m;
            if(total < 9)
            {
                document.write("<td style='text-align: center'><small>Federal</small></td>");
            }
            else
            {
                document.write("<td style='text-align: center'><small>Estatal</small></td>");
            }
        }
    </script>
</head>
    <body>
        <header>
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
            <img class="derecha" src="{{ public_path('img/chiapas.png') }}">
        </header>
        <div>
            <div id="wrappertop">
                <div align=center><font size=0><b>{{$distintivo}}</b></font><br/>
                    <FONT SIZE=0><b>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS<br/>
                    <FONT SIZE=0>DIRECCION DE PLANEACION</FONT><br/>
                    <FONT SIZE=0>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO</FONT><br/>
                    <FONT SIZE=0>FORMATO DE VALIDACIÓN DE SUFICIENCIA PRESUPUESTAL</FONT><br/>
                    <FONT SIZE=0>EN ATENCIÓN AL MEMORÁNDUM {{$data2->no_memo}}</FONT></p>
                </div>
                <div class="c"><FONT SIZE=0>Folio de Validación: {{$data2->folio_validacion}}<br/>
                Fecha: {{$Dv}} de {{$Mv}} del {{$Yv}}</FONT>
                </div>
                <div class="b"> <FONT SIZE=0>UNIDAD DE CAPACITACIÓN {{$data2->unidad_capacitacion}}</font><br/>
                    <FONT SIZE=0><b>{{$getremitente->nombre}} {{$getremitente->apellidoPaterno}} {{$getremitente->apellidoMaterno}}</b></FONT><br/>
                    <FONT SIZE=0><b>{{$getremitente->puesto}}</b></FONT><br/>
                    <FONT SIZE=0><b>PRESENTE</b></FONT><br/></div>
                    <div class="d"> <FONT SIZE=0>En atención a su solicitud con memorándum No.{{$data2->no_memo}} de fecha {{$D}} de {{$M}} del {{$Y}}; me permito comunicarle lo siguiente:<br/></font>
                        <font size=0>La Secretaria de Hacienda aprobó el presupuesto del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, en lo general para el Ejercicio Fiscal 2021, en ese sentido, con Fundamento en el Art. 13 del decreto de presupuesto
                        de egresos del Estado de Chiapas para el Ejercicio Fiscal 2021 y en apego al tabulador de pagos del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas por servicios de @if($tipo=='CURSO') Capacitación @else Certificación Extraordinaria @endif, al Padrón de Instructores del ICATECH
                        y a la clave de autorización de apertura de cursos y certificación, y demás disposiciones normativas aplicables vigentes; le informo que una vez revisada su solicitud y la información descrita en el formato de Validación de Suficiencia Presupuestal, se otorga la Validación
                        Presupuestal, con el fin de que conforme a lo indicado en la normatividad aplicable vigente se continúe y se cumpla con los procedimientos administrativos que correspondan, observando además el contrato de prestación de servicios profesionales por honorarios en su
                        modalidad de @if($tipo=='CURSO') Horas-Curso @else Certificación Extraordinaria @endif que celebran el ICATECH con el prestador de servicio.<br/></font>
                        <br><font size=0>Por lo anterior, me permito remitir a usted el original de la solicitud, así como su respectivo respaldo documental, debidamente validado presupuestalmente.<br/></font>
                        <font size=0>La presente validación presupuestal no implica ninguna autorización de pago de recursos, si no que únicamente se refiere a la verificación de la disponibilidad presupuestal, No omito manifestarle que, en estricto apego a la normatividad vigente establecida,
                        el área administrativa solicitante, es responsable de la correcta aplicación de los recursos públicos validados, en tal sentido el ejercicio y comprobación del gasto, deberá sujetarse a las disposiciones legales aplicables para tal efecto.<br/></font>
                    </div>
                <br>
            </div>
            <div class="form-row">
                <table width="700"  class="table table-striped" id="table-one">
                    <thead>
                        <tr class="active">
                            <td scope="col"><small style="font-size: 8px;">No. DE SUFICIENCIA</small></td>
                            <td scope="col" ><small style="font-size: 8px;">FECHA</small></td>
                            <td scope="col" ><small style="font-size: 8px;">INSTRUCTOR</small></td>
                            <td width="10px"><small style="font-size: 8px;">UNIDAD/ A.M. DE CAP.</small></td>
                            <td scope="col" ><small style="font-size: 8px;">SERVICIO</small></td>
                            <td scope="col" ><small style="font-size: 8px;">NOMBRE</small></td>
                            <td scope="col"><small style="font-size: 8px;">CLAVE DEL GRUPO</small></td>
                            <td scope="col" ><small style="font-size: 8px;">ZONA ECÓNOMICA</small></td>
                            <td scope="col"><small style="font-size: 8px;">HSM (horas)</small></td>
                            <td scope="col" ><small style="font-size: 8px;">IMPORTE POR HORA</small></td>
                            <td scope="col"><small style="font-size: 8px;">IVA 16%</small></td>
                            <td scope="col" ><small style="font-size: 8px;">PARTIDA/ CONCEPTO</small></td>
                            <td scope="col"><small style="font-size: 8px;">IMPORTE</small></td>
                            <td scope="col"><small style="font-size: 8px;">Fuente de Financiamiento</small></td>
                            <td scope="col" ><small style="font-size: 8px;">OBSERVACION</small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key=>$item)
                            <tr>
                                <td><small style="font-size: 8px;">{{$item->folio_validacion}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->fecha}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->nombre}} {{$item->apellidoPaterno}} {{$item->apellidoMaterno}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->unidad}}</small></td>
                                @if ($item->tipo_curso=='CERTIFICACION')
                                    <td><small style="font-size: 8px;">CERTIFICACIÓN EXTRAORDINARIA</small></td>
                                @else
                                    <td><small style="font-size: 8px;">CURSO</small></td>
                                @endif
                                <td><small style="font-size: 8px;">{{$item->curso_nombre}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->clave}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->ze}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->dura}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->importe_hora}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->iva}}</small></td>
                                <input id='hombre{{$key}}' name="hombre" hidden value="{{$item->hombre}}">
                                <input id='mujer{{$key}}' name="mujer" hidden value="{{$item->mujer}}">
                                <td><small style="font-size: 8px;">12101 Honorarios</small></td>
                                <td><small style="font-size: 8px;">{{$item->importe_total}}</small></td>
                                <!--<script>alumn(hombre{key}}.value, mujer{key}}.value);</script>-->
                                <td style="text-align: center; font-size: 10px;"><small>{{$recursos[$key]}}</small></td>
                                <td><small style="font-size: 8px;">{{$item->comentario}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div id="wrapperbot">
            <div align=center>
                <small>LUIS ALFONSO CRUZ VELASCO</small>
                <br>________________________________________<br/>
                <br><small>JEFE DE DEPARTAMENTO DE PROGRAMACION Y PRESUPUESTO</small></b>
            </div>
            <div><br><br><br>
                <FONT SIZE=0><b>C.c.p.</b>FABIOLA LIZBETH ASTUDILLO REYES.-DIRECTORA GENERAL.-Para su conocimiento</FONT><br/>
                <FONT SIZE=0><b>C.c.p.</b>SALVADOR BETANZOS SOLIS.-DIRECTOR DE PLANEACION.-mismo fin</FONT><br/>
                <FONT SIZE=0><b>C.c.p.</b>JORGE LUIS BARRAGAN LOPEZ.-JEFE DE DEPARTAMENTO DE RECURSOS FINANCIEROS.-mismo fin</FONT><br/>
                <FONT SIZE=0><b>C.c.p.</b>{{$getccp4->nombre}} {{$getccp4->apellidoPaterno}} {{$getccp4->apellidoMaterno}}.-{{$getccp4->puesto}}.-mismo fin</FONT><br>
                <FONT SIZE=0><b>C.c.p.</b>Archivo/ Minutario</FONT>
            </div>
        </div>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
            <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
        </footer>
    </body>
</html>
