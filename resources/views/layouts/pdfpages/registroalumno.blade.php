<html>
    <!--pdf registro para alumnos-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">

  <style>
    body{
      font-family: sans-serif;
      font-size: 1.2em;
      margin: 10px 10px 10px 10px;
    }
    @page {
        margin: 20px 20px;
    }
    small {
        font-size: .6em
    }
    sa {
        text-decoration-line: overline;
    }
    se {
        text-decoration-line: underline;
    }
    table {
        margin-top: .3em;
        margin-bottom: 0.2em;
        border-collapse: collapse
        padding: 0; margin: 0;
        border: 0.8px solid black; //Cualquier otro tipo de borde como bottom que es el inferior o ninguno
    }
    table, td {
        margin-top: .0em;
        margin-bottom: 0em;
        border-collapse: collapse;
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
        font-size: .4em
    }
    td{
        padding: 0em 0px;
    }
    div.centrado {
        text-align: center;
    }
    small.texto-centrado {
        font-size: .7em
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
                SOLICITUD DE INSCRIPCIÓN ( SID - 01 ).</b>
                </div>
            </small>
        </p><br>
        <table class="table tds">
            <colgroup>
                <col style="width: 33%"/>
                <col style="width: 33%"/>
                <col style="width: 33%"/>
            </colgroup>
            <tbody>
                <tr style="height: 25px; margin: 0px; paddin: 0px;">
                    <td style="border: hidden;">
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
        @if ($alumnos->chk_fotografia == TRUE)
            <img class="derecha img-thumbnail mb-3" src="{{ public_path($pathimg) }}">
        @else
            <img class="derecha img-thumbnail mb-3" src="{{ public_path('img/blade_icons/nophoto.png') }}">
        @endif
        <table class="table td">
            <colgroup>
				<col style="width: 30%"/>
				<col style="width: 70%"/>
                <col style="width: 30%"/>
                <col style="width: 25%"/>
			</colgroup>
            <thead>
              <tr style='padding-bottom: -1em'>
                <td scope="col" colspan="4" style='padding-bottom: -1em'>
                    <div align="center">
                        <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DATOS DE LA UNIDAD DE CAPACITACIÓN</b>
                    </div>
                </td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="3" style='border-bottom:none'>
                    <small>
                        <b>INSTITUTO:</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <se>
                            <b>INSTITUTO DE CAPACITACIÓN Y VINCULACION TECNÓLOGICA DEL ESTADO DE CHIAPAS "ICATECH"</b>
                        </se>
                    </small>
                </td>
              </tr>
              <tr>
                <td scope="row" colspan="2" style='border-right:none;border-top:none'>
                    <small>
                        <b> UNIDAD DE CAPACITACIÓN: &nbsp;&nbsp; {{ $alumnos->unidad }}</b>
                    </small>

                </td>
                <td scope="row" colspan="2" style='border-left:none;border-top:none'>
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
                            <b>DATOS PERSONALES</b>
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
                        <td style='border-right:none; border-top:none; border-bottom:none;'>
                            <small>
                                <b>ESTADO CIVIL: &nbsp;&nbsp;</b>
                                <se>{{ $alumnos->estado_civil }}</se>
                            </small>
                        </td>
                        <td colspan="3" style='border-right:none; border-top:none; border-left:none;'>
                            <small>
                                <b>DISCAPACIDAD QUE PRESENTA: &nbsp;&nbsp;</b>
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
                            <se><br>
                                @if($alumnos->chk_acta_nacimiento == TRUE || $alumnos->chk_curp == TRUE)(X) @else() ( ) @endif COPIA DE ACTA DE NACIMIENTO (NO MAYOR A 2 AÑOS) O CURP (VIGENCIA UN AÑO)
                            <br>@if($alumnos->chk_comprobante_ultimo_grado == TRUE)(X) @else() ( ) @endif COPIA COMPROBANTE DEL ULTIMO GRADO DE ESTUDIOS EN CASO DE CONTAR CON EL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@if($alumnos->chk_fotografia == TRUE)(X) @else() ( ) @endif FOTOGRAFÍA DIGITAL O IMPRESA</se>
                            <br><b>EXTRANJEROS ANEXAR: &nbsp;&nbsp;</b>
                            <se><p>@if($alumnos->chk_comprobante_calidad_migratoria == TRUE)(X) @else() ( ) @endif COMPROBANTE DE CALIDAD MIGRATORIA CON LA QUE SE ENCUENTRA EN EL TERRITORIO NACIONAL</se>
                        </small>
                    </td>
                </tr>
                <!--<tr>
                    <td colspan="2" style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>EXTRANJEROS ANEXAR: &nbsp;&nbsp;</b>
                            <se><p>@if($alumnos->chk_comprobante_calidad_migratoria == TRUE)(X) @else() ( ) @endif COMPROBANTE DE CALIDAD MIGRATORIA CON LA QUE SE ENCUENTRA EN EL TERRITORIO NACIONAL</se>
                        </small>
                    </td>
                </tr>-->
                <tr>
                    <td style='border-right:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>EMPRESA DONDE TRABAJA: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->empresa_trabaja }}</se>
                        </small>
                    </td>
                    <td style='border-right:none; border-left:none; border-top:none; border-bottom:none;'>
                        <small>
                            <b>PUESTO: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->puesto_empresa }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td style='border-right:none; border-top:none;'>
                        <small>
                            <b>ANTIGUEDAD: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->antiguedad }}</se>
                        </small>
                    </td>
                    <td style='border-right:none; border-left:none; border-top:none;'>
                        <small>
                            <b>DIRECCIÓN: &nbsp;&nbsp;</b>
                            <se>{{ $alumnos->direccion_empresa }}</se>
                        </small>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none; border-top:none;'>
                        <small class="sml">
                            <b>NOTA: LA DOCUMENTACIÓN DEBERA ENTREGARSE EN ORIGINAL Y COPIA PARA SU COTEJO.</b>
                        </small>
                    </td>
                </tr>
            </tbody>
        </table>
        <p><p><p> <p><p><p>
        <table class="table td" cellspacing="0" cellpadding="0">
            <colgroup>
				<col style="width: 50%"/>
                <col style="width: 50%"/>
            </colgroup>
            <tbody>
                <tr>
                    <td scope="col" colspan="2">
                        <div align="center">
                            <b>DATOS PARA LA UNIDAD DE CAPACITACIÓN</b>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style='border-right:none; border-top:none;  border-bottom:none;'>
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

        <table class="table dashed" cellspacing="0" cellpadding="0">
            <colgroup>
				<col style="width: 25%"/>
                <col style="width: 25%"/>
                <col style="width: 25%"/>
                <col style="width: 25%"/>
            </colgroup>
            <thead>
                <!--<tr>
                    <td colspan="4" style='border-right:none; border-top:none; border-bottom:none;'>
                        <div class="left-algn">
                            <small>
                                <b>COMPROBANTE PARA EL INSTITUTO</b>
                            </small>
                        </div>
                    </td>
                </tr>-->
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" scope="row" class="tres" style='border-right:none; border-top:none; border-bottom:none;'>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <small>
                                <b>COMPROBANTE PARA EL INSTITUTO</b>
                            </small>
                        
                        <br>
                        <small>
                            <b>FECHA:</b>
                            <se>{{$date}}</se>
                        </small>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <small>
                            <b>NÚMERO DE SOLICITUD:</b>
                            <se>{{$alumnos->no_control}}{{$alumnos->id}}</se>
                        </small>
                    </td>
                   <!-- <td colspan="2" scope="row" class="tres" style='border-right:none; border-left:none; border-top:none; border-bottom:none;'>
                        <br><small>
                            <b>NÚMERO DE SOLICITUD:</b>
                            <se>{{$alumnos->no_control}}{{$alumnos->id}}</se>
                        </small>
                    </td>-->
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

