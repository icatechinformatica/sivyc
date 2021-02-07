<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
    <style>
        @page {
            margin: 20px 20px 10px 20px;
        }

        body {
            font-family: sans-serif;
            font-size: 1.3em;
            margin: 10px;
        }

        small {
            font-size: .7em
        }
        sa {
            text-decoration-line: overline;
        }
        se {
            text-decoration-line: underline;
        }
        table {
            margin-top: .6em;
            margin-bottom: 0.5em;
            padding: 0; margin: 0;
            border: 0.8px solid black; //Cualquier otro tipo de borde como bottom que es el inferior o ninguno
        }
        table, td {
            border-style: none;
            padding: 0;
            border: 1px solid black; //Cualquier otro tipo de borde como bottom que es el inferior o ninguno
        }
        .dashed {
            border: 1px dashed black;
        }

        .tds{
            border: hidden;
        }

        td.tres { width: calc(100%/2); }
        td.cuatro { width: calc(100%/4); }
        small.sml {
            font-size: .5em
        }
        td{
            padding: 2em 3px;
        }
        div.centrado {
            text-align: center;
        }
        small.texto-centrado {
            font-size: .8em
        }
        .linea {
            border-top: 1px solid black;
            height: 2px;
            max-width: 200px;
            padding: 0;
            margin: 5px auto 0 auto;
          }

          .centrados{
              text-align: center;
          }
          .left-algn{
            text-align: right;
          }
          img.izquierda {
            float: left;
          }

          img.derecha {
            float: right;
            width: 100px;
            height: 100px;
          }
        .saltopagina{
            page-break-after:always;
        }
    </style>
</head>
<body>
    <div class="container g-pt-90">
        <p>
            <img class="izquierda" src="{{ public_path('img/sep1.png') }}">
            <!--aqui va img-->
            <small>
                <div class="centrados">
                <b>SUBSECRETARIA DE EDUCACIÓN MEDIA SUPERIOR
                DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO
                SOLICITUD DE INSCRIPCIÓN EN EL CERESO ( SID - CERSS 01 )</b>
                </div>
            </small>
        </p>
        <table class="table tds">
            <colgroup>
                <col style="width: 33%"/>
                <col style="width: 33%"/>
                <col style="width: 33%"/>
            </colgroup>
            <tbody>
                <tr>
                    <td style="border: hidden">
                        <small>
                            <div class="centrados">
                                {{$date}}
                                <div class="linea"></div>
                                <br>FECHA
                            </div>
                        </small>
                    </td>
                    <td style="border: hidden">
                        <small>
                            <div class="centrados">
                                {{ $alumnos->no_control }}
                                <div class="linea"></div>
                                N°. DE CONTROL
                            </div>
                        </small>
                    </td>
                    <td style="border: hidden">
                        <small>
                            <div class="centrados">
                                {{$alumnos->no_control}}{{$alumnos->id}}
                                <div class="linea"></div>
                                NÚMERO DE SOLICITUD
                            </div>
                        </small>
                    </td>
                </tr>
            </tbody>
        </table>
        @if ($alumnos->chk_fotografia == false || empty($alumnos->chk_fotografia))
            <img class="derecha img-thumbnail mb-3" src="{{ public_path('img/blade_icons/nophoto.png') }}">
        @else
            <img class="derecha img-thumbnail mb-3" src="{{ public_path($pathimg) }}">
        @endif
        <table class="table td">
            <colgroup>
				<col style="width: 30%"/>
				<col style="width: 70%"/>
			</colgroup>
            <thead>
              <tr>
                <td scope="col" colspan="2">
                    <div align="center">
                        <b>DATOS DE LA UNIDAD DE CAPACITACIÓN</b>
                    </div>
                </td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="2" style='border-bottom:none'>
                    <small>
                        <b>INSTITUTO:</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <se>
                            <b>INSTITUTO DE CAPACITACIÓN Y VINCULACION TECNÓLOGICA DEL ESTADO DE CHIAPAS "ICATECH"</b>
                        </se>
                    </small>
                </td>
              </tr>
              <tr>
                <td scope="row" class="tres" style='border-right:none;border-top:none'>
                    <small>
                        <b> UNIDAD DE CAPACITACIÓN: &nbsp;&nbsp; {{ $alumnos->unidad }}</b>
                    </small>

                </td>
                <td scope="row" class="tres" style='border-left:none;border-top:none'>
                   <small>
                       <b> CLAVE CCT:  {{$alumnos->unidades }}</b>
                   </small>
                </td>
              </tr>
            </tbody>
        </table>
        <table class="table td">
            <colgroup>
				<col style="width: 25%"/>
                <col style="width: 25%"/>
                <col style="width: 25%"/>
                <col style="width: 25%"/>
			</colgroup>
            <thead>
                <tr>
                  <td scope="col" colspan="4">
                      <div align="center">
                          <b>DATOS PERSONALES Y CERSS</b>
                      </div>
                  </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row" style='border-left:none; border-right:none; border-bottom:none;'>
                        <small>
                            <b> PRIMER APELLIDO: &nbsp;&nbsp; </b>
                            <se>{{ $alumnos->apellido_paterno }}</se>
                        </small>
                    </td>
                    <td scope="row" style='border-right:none;border-left:none; border-bottom:none;'>
                        <small>
                            <b> SEGUNDO APELLIDO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->apellido_materno }}</se>
                        </small>
                    </td>
                    <td scope="row" colspan="2" style='border-left:none; border-bottom:none;'>
                        <small>
                            <b> NOMBRE(S): &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->nombrealumno }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>SEXO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->sexo }}</se>
                        </small>
                    </td>
                    <td style='border-left:none; border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>CURP: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->curp_alumno }}</se>
                        </small>
                    </td>
                    <td style='border-left:none; border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>EDAD: &nbsp;&nbsp;</b>
                            <se>{{ $edad }} AÑOS </se>
                        </small>
                    </td>
                    <td style='border-left:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>TELEFONO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->telefono }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="tres" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>DOMICILIO: &nbsp;&nbsp;</b>
                            <se>
                                {{ $alumnos->domicilio }}
                            </se>
                        </small>
                    </td>
                    <td colspan="2" class="tres" style='border-left:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>COLONIA O LOCALIDAD: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->colonia }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>C.P.: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->cp }}</se>
                        </small>
                    </td>
                    <td style='border-left:none; border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>MUNICIPIO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->municipio }}</se>
                        </small>
                    </td>
                    <td colspan="2" style='border-left:none; border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>ESTADO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->estado }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>ESTADO CIVIL: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->estado_civil }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>NÚMERO DE EXPEDIENTE: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->numero_expediente }}</se>
                        </small>
                    </td>
                    <td colspan="2" style='border-right:none; border-left:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>NOMBRE DEL CERSS: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->nombre_cerss }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>DIRECCIÓN CERSS: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->direccion_cerss }}</se>
                        </small>
                    </td>
                    <td colspan="2" style='border-right:none; border-left:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>TITULAR CERSS: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->titular_cerss }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none; border-top:none;'>
                        <small>
                            <b>NACIONALIDAD: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->nacionalidad }}</se>
                        </small>
                    </td>
                    <td colspan="2" style='border-left:none; border-top:none;'>
                        <small>
                            <b>DISCAPACIDAD: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->discapacidad }}</se>
                        </small>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table td" cellspacing="0" cellpadding="0">
            <colgroup>
				<col style="width: 50%"/>
                <col style="width: 50%"/>
			</colgroup>
            <thead>
                <tr>
                  <td scope="col" colspan="2">
                      <div align="center">
                          <b>DATOS GENERALES</b>
                      </div>
                  </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" scope="row" style='border-left:none; border-right:none; border-bottom:none;'>
                        <small>
                            <b> ESPECIALIDAD A LA QUE DESEAN INSCRIBIRSE: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
                            <se>{{ $alumnos->especialidad }}</se>
                         </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" scope="row" class="tres" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>CURSO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->nombre_curso }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td scope="row" class="tres" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>HORARIO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->horario }}</se>
                        </small>
                    </td>
                    <td scope="row" class="tres" style='border-left:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>GRUPO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->grupo }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>DOCUMENTACIÓN ENTREGADA: &nbsp;&nbsp;</b>
                            <se>
                                <br>
                                <se>
                                    @if ($alumnos->chk_ficha_cerss == true)
                                        (X)
                                    @else
                                        ()
                                    @endif
                                    FICHA CERSS
                                </se>
                            </se>

                        </small>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><p><p>
        <table class="table td" cellspacing="0" cellpadding="0">
            <colgroup>
				<col style="width: 50%"/>
                <col style="width: 50%"/>
            </colgroup>
            <thead>
                <tr>
                    <td scope="col" colspan="2">
                        <div align="center">
                            <b>DATOS PARA LA UNIDAD DE CAPACITACIÓN</b>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" style='border-left:none; border-right:none; border-bottom:none;'>
                        <small>
                            <b>MEDIO POR EL QUE SE ENTERÓ DEL SISTEMA: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->medio_entero }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>MOTIVOS DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->sistema_capacitacion_especificar }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none; border-top:none; border-bottom:none;'>
                        <div class="centrado">
                            <small>
                                EL ASPIRANTE SE COMPROMETE A CUMPLIR CON LAS NORMAS Y DISPOSICIONES DICTADAS POR LAS AUTORIDADES DE LA UNIDAD.
                            </small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td scope="row" class="tres" style='border-right:none;border-top:none; border-bottom:none;'>
                        <small>
                            <div class="centrados">
                                <b> {{ $alumnos->apellido_paterno }} {{ $alumnos->apellido_materno }} {{ $alumnos->nombrealumno }}</b>
                                <div class="linea"></div>
                            </div>
                        </small>
                    </td>
                    <td scope="row" class="tres" style='border-left:none;border-top:none; border-bottom:none;'>
                       <small>
                            <div class="centrados">
                                <b> {{ $alumnos->realizo }} </b>
                                <div class="linea"></div>
                            </div>
                       </small>
                    </td>
                </tr>
                <tr>
                    <td scope="row" class="tres" style='border-right:none;border-top:none'>
                        <small>
                            <div class="centrados">
                                <b> NOMBRE Y FIRMA DEL ASPIRANTE</b>
                            </div>
                        </small>
                    </td>
                    <td scope="row" class="tres" style='border-left:none;border-top:none'>
                       <small>
                            <div class="centrados">
                                <b> NOMBRE Y FIRMA DE LA PERSONA QUE INSCRIBE </b>
                            </div>
                       </small>
                    </td>
                </tr>
            </tbody>
        </table>

        <div clas='saltopagina'><p><p><p><p><p><p>

        <table class="table dashed" cellspacing="0" cellpadding="0">
            <colgroup>
				<col style="width: 25%"/>
                <col style="width: 25%"/>
                <col style="width: 25%"/>
                <col style="width: 25%"/>
            </colgroup>
            <thead>
                <tr>
                    <td colspan="4" style='border-right:none; border-top:none; border-bottom:none;'>
                        <div class="left-algn">
                            <small>
                                <b>COMPROBANTE PARA EL INSTITUTO</b>
                            </small>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" scope="row" class="tres" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>FECHA:</b>
                            <se>{{$date}}</se>
                        </small>
                    </td>
                    <td colspan="2" scope="row" class="tres" style='border-right:none; border-left:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>NÚMERO DE SOLICITUD:</b>
                            <se>{{$alumnos->no_control}}{{$alumnos->id}}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style='border-right:none; border-left:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>NOMBRE DEL ASPIRANTE:</b>
                            <se>{{ $alumnos->apellido_paterno }} {{ $alumnos->apellido_materno }} {{ $alumnos->nombrealumno }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td class="cuatro" style='border-right:none;border-top:none; border-bottom:none;'>
                        <small>
                            <b>CURSO:</b>
                            <se>{{ $alumnos->nombre_curso }}</se>
                        </small>
                    </td>
                    <td class="cuatro" style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <b>HORARIO:</b>
                            <se>{{ $alumnos->horario }}</se>
                        </small>
                    </td>
                    <td class="cuatro" style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <b>GRUPO:</b>
                            <se>{{ $alumnos->grupo }}</se>
                        </small>
                    </td>
                    <td class="cuatro" style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <b>COSTO:$</b>
                            <se>{{ $alumnos->costo }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <div class="centrados">
                                <b> {{ $alumnos->realizo }} </b>
                                <div class="linea"></div>
                            </div>
                        </small>
                    </td>
                    <td style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <div class="centrados">
                                &nbsp;&nbsp;
                                <div class="linea"></div>
                            </div>
                        </small>
                    </td>
                    <td style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <div class="centrados">
                                &nbsp;&nbsp;
                                <div class="linea"></div>
                            </div>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <div class="centrados">
                                <b> NOMBRE Y FIRMA DE LA PERSONA QUE RECIBE </b>
                            </div>
                        </small>
                    </td>
                    <td style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <div class="centrados">
                                <b>SELLO</b>
                            </div>
                        </small>
                    </td>
                    <td style='border-right:none;border-top:none; border-left:none; border-bottom:none;'>
                        <small>
                            <div class="centrados">
                                <b>FIRMA DEL ASPIRANTE</b>
                            </div>
                        </small>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

 </body>
</html>
