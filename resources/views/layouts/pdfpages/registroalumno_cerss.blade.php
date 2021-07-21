<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style type="text/css">
    @page{margin: 60px 30px 20px; font-size: 9px}
    @font-face {
        font-family: "Baby sweet";           
        src: url("/fonts/gotham-light.ttf") format("truetype");
        font-weight: normal;
        font-style: normal;

    }  
    body{
        font-family: sans-serif;
    }
    table {
        width: 100%;
        border-collapse: collapse; 
    }
    td{
        padding: 0px;
        padding-left: 5px;
        padding-bottom: 3px;
    }
    .p{
        text-decoration: overline;
    }
    .variable{
        text-align: center;
        border: 1px solid black;
    }
    img.izquierda{float:left}
    img.derecha {
        float: right;
        width: 2.5cm;
        height: 2.5cm;
      }
</style>
<body>
    <div>
        <div><img class="izquierda" src="{{ public_path('img/sep1.png') }}"></div>
        <div>
            @if ($alumnos->chk_fotografia == false || empty($alumnos->chk_fotografia))
            <img class="derecha img-thumbnail mb-3" src="{{ public_path('img/blade_icons/nophoto.png') }}">
            @else
            <img class="derecha img-thumbnail mb-3" src="{{ public_path($pathimg) }}">
            @endif
        </div>
        <div class="demo" style="text-align: center;">
            <b>SUBSECRETARIA DE EDUCACIÓN MEDIA SUPERIOR <br>
            DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO <br>
            SOLICITUD DE INSCRIPCIÓN EN EL CERESO <br> ( SID - CERSS 01 )</b>
        </div>
    </div>
    <br>
    <div >
        <table style="text-align: center; width:80%;">
            <thead></thead>
            <tbody>
                <tr>
                    <td style="text-decoration: underline;">{{$date}}</td>
                    <td>{{$alumnos->no_control.$alumnos->id}}</td>
                </tr>
                <tr>
                    <td>FECHA</td>
                    <td class="p">NÚMERO DE SOLICITUD</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div style="border: 1px solid black;">
        <table>
            <tr>
                <td colspan="4" class="variable"><b>DATOS DE LA UNIDAD DE CAPACITACIÓN</b></td>
            </tr>
            <tr>
              <td colspan="4" style="padding-top: 5px;"><b>INSTITUTO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INSTITUTO DE CAPACITACIÓN Y VINCULACION TECNÓLOGICA DEL ESTADO DE CHIAPAS</b></td>
            </tr>
            <tr>
              <td><b> UNIDAD DE CAPACITACIÓN: {{ $alumnos->unidad }}</b></td>
              <td></td>
              <td><b> CLAVE CCT:  {{$alumnos->unidades }}</b></td>
              <td></td>
            </tr>
        </table>
    </div>
    <br>
    <div style="border: 1px solid black;">
        <table>
            <tr><td class="variable" colspan="4"><b>DATOS PERSONALES Y CERSS</b></td></tr>
            <tr>
                <td colspan="2" style="padding-top: 5px;"><b>NÚMERO DE EXPEDIENTE: &nbsp;&nbsp;</b>{{ $alumnos->numero_expediente }}</td>
                <td colspan="2" style="padding-top: 5px;"><b>NOMBRE DEL CERSS: &nbsp;&nbsp;</b>{{ $alumnos->nombre_cerss }}</td>
            </tr>
            <tr>
                <td colspan="2"><b>DIRECCIÓN CERSS: &nbsp;&nbsp;</b>{{ $alumnos->direccion_cerss }}</td>
                <td colspan="2"><b>TITULAR CERSS: &nbsp;&nbsp;</b>{{ $alumnos->titular_cerss }}</td>
            </tr>
            <tr>
                <td colspan="4"><b>NACIONALIDAD: &nbsp;&nbsp;</b>{{ $alumnos->nacionalidad }}</td>
            </tr>
            <tr>
                <td><b>NOMBRE DEL ASPIRANTE:</b></td>
                <td>{{ $alumnos->apellido_paterno }} <br><b>PRIMER APELLIDO</b></td>
                <td>{{ $alumnos->apellido_materno }} <br><b>SEGUNDO APELLIDO</b></td>
                <td>{{ $alumnos->nombrealumno }} <br><b>NOMBRE(S)</b></td>
            </tr>
            <tr>
                <td><b>SEXO: @php if($alumnos->sexo=="FEMENINO"){echo "M(X) H( )";} else {echo"M( ) H(X)";} @endphp</b></td>
                <td><b>CURP: &nbsp;&nbsp;</b>{{ $alumnos->curp_alumno }}</td>
                <td><b>EDAD: &nbsp;&nbsp;</b>{{ $edad }} AÑOS</td>
                <td><b>TELEFONO: &nbsp;&nbsp;</b>{{ $alumnos->telefono }}</td>
            </tr>
            <tr>
                <td colspan="2"><b>DOMICILIO: &nbsp;&nbsp;</b>{{ $alumnos->domicilio }}</td>
                <td colspan="2"><b>COLONIA O LOCALIDAD: &nbsp;&nbsp;</b>{{ $alumnos->colonia }}</td>
            </tr>
            <tr>
                <td><b>C.P.: &nbsp;&nbsp;</b>{{ $alumnos->cp }}</td>
                <td colspan="2"><b>MUNICIPIO: &nbsp;&nbsp;</b>{{ $alumnos->municipio }}</td>
                <td><b>ESTADO: &nbsp;&nbsp;</b>{{ $alumnos->estado }}</td>
            </tr>
            <tr>
                <td colspan="4"><b>ESTADO CIVIL:</b> @php if($alumnos->estado_civil=="SOLTERO"){echo "SOLTERO(X) CASADO( ) VIUDO( ) DIVORCIADO( ) UNION LIBRE( )";}
                    if($alumnos->estado_civil=="CASADO"){echo "SOLTERO( ) CASADO(X) VIUDO( ) DIVORCIADO( ) UNION LIBRE( )";}
                    if($alumnos->estado_civil=="VIUDO"){echo "SOLTERO( ) CASADO( ) VIUDO(X) DIVORCIADO( ) UNION LIBRE( )";}
                    if($alumnos->estado_civil=="DIVORCIADO"){echo "SOLTERO( ) CASADO( ) VIUDO( ) DIVORCIADO(X) UNION LIBRE( )";}
                    if($alumnos->estado_civil=="UNION LIBRE"){echo "SOLTERO( ) CASADO( ) VIUDO( ) DIVORCIADO( ) UNION LIBRE(X)";}
                   else{echo "SOLTERO( ) CASADO( ) VIUDO( ) DIVORCIADO( ) UNION LIBRE( )";} @endphp</td>
            </tr>
            <tr>
                <td colspan="4"><b>DISCAPACIDAD QUE PRESENTA: </b></td>
            </tr>
            <tr>
                <td>@php if($alumnos->discapacidad=="VISUAL"){echo "VISUAL(X)";}else{echo "VISUAL( )";} @endphp</td>
                <td> @php if($alumnos->discapacidad=="AUDITIVA"){echo "AUDITIVA(X)";}else{echo "AUDITIVA( )";} @endphp</td>
                <td>@php if($alumnos->discapacidad=="DE COMUNICACION"){echo "DE COMUNICACION(X)";}else{echo "DE COMUNICACION( )";} @endphp</td>
                <td></td>
            </tr>
            <tr>
                <TD>@php if($alumnos->discapacidad=="MOTRIZ"){echo "MOTRIZ(X)";}else{echo "MOTRIZ( )";} @endphp</TD>
                <TD>@php if($alumnos->discapacidad=="INTELECTUAL"){echo "INTELECTUAL(X)";}else{echo "INTELECTUAL( )";} @endphp</TD>
                <TD></TD>
                <TD></TD>
            </tr>
        </table>
    </div>
    <br>
    <div style="border: 1px solid black;">
        <table class="table">
            <tr><td colspan="3" class="variable"><b>DATOS GENERALES</b></td></tr>
            <tr>
                <td colspan="3">ESPECIALIDAD A LA QUE DESEAN INSCRIBIRSE: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $alumnos->especialidad }}</td>
            </tr>
            <tr>
                <td colspan="2">CURSO: &nbsp;&nbsp; {{ $alumnos->nombre_curso }}</td>
                <td>TIPO: &nbsp;&nbsp; {{$alumnos->tipo_curso}}</td>
            </tr>
            <tr>
                <td>HORARIO: &nbsp;&nbsp; {{ $alumnos->horario }}</td>
                <td colspan="2">GRUPO: &nbsp;&nbsp; {{ $alumnos->grupo }}</td>
            </tr>
            <tr>
                <td colspan="3"><b>DOCUMENTACIÓN ENTREGADA: </b><br>
                            @if($alumnos->chk_acta_nacimiento == TRUE || $alumnos->chk_curp == TRUE)(X) @else() ( ) @endif COPIA DE ACTA DE NACIMIENTO (NO MAYOR A 2 AÑOS) O CURP (VIGENCIA UN AÑO)
                        <br>@if($alumnos->chk_comprobante_ultimo_grado == TRUE)(X) @else() ( ) @endif COPIA COMPROBANTE DEL ULTIMO GRADO DE ESTUDIOS EN CASO DE CONTAR CON EL
                        <br>@if($alumnos->chk_fotografia == TRUE)(X) @else() ( ) @endif FOTOGRAFÍA DIGITAL O IMPRESA
                        <br>
                                @if ($alumnos->chk_ficha_cerss == true)
                                    (X)
                                @else
                                    ()
                                @endif
                                FICHA CERSS
            </tr>
            <tr><td class="variable" colspan="3"><b>DATOS PARA LA UNIDAD DE CAPACITACIÓN</b></td></tr>
            <tr>
                <td colspan="3" style="padding-top: 5px;"><b>MEDIO POR EL QUE SE ENTERÓ DEL SISTEMA:</b></td>
            </tr>
            <tr>
                <td><b>@if($alumnos->medio_entero=="PRENSA") (X) @else() ( ) @endif PRENSA </b></td>
                <td><b>@if($alumnos->medio_entero=="TELEVISION") (X) @else() ( )  @endif TELEVISION</b></td>
                <td><b>@if($alumnos->medio_entero=="FOLLETOS,CARTELES,VOLANTE") (X)  @else() ( ) @endif FOLLETOS,CARTELES,VOLANTES</b></td>
            </tr>
            <tr>
                <td><b>@if($alumnos->medio_entero=="RADIO") (X)  @else() ( ) @endif RADIO</b></td>
                <td><b>@if($alumnos->medio_entero=="INTERNET") (X)  @else() ( ) @endif INTERNET</b></td>
                <td></td>
            </tr>
            <tr>
                <td><b>@if($alumnos->medio_entero!="PRENSA"&&$alumnos->medio_entero!="TELEVISION"&&$alumnos->medio_entero!="FOLLETOS,CARTELES,VOLANTE"&&$alumnos->medio_entero!="RADIO"&&$alumnos->medio_entero!="INTERNET") (X)  @else() ( ) @endif OTROS</b></td>
                <td><b>ESPECIFIQUE:</b></td>
                <td>@if($alumnos->medio_entero!="PRENSA"&&$alumnos->medio_entero!="TELEVISION"&&$alumnos->medio_entero!="FOLLETOS,CARTELES,VOLANTE"&&$alumnos->medio_entero!="RADIO"&&$alumnos->medio_entero!="INTERNET") {{$alumnos->medio_entero}} @else() @endif</td>
            </tr>
            <tr>
                <td colspan="3"><b>MOTIVOS DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN:</b></td>
            </tr>
            <tr>
                <td><b> @if($alumnos->sistema_capacitacion_especificar=="EMPLEARSE O AUTOEMPLEARSE") (X) @else() ( ) @endif PARA EMPLEARSE O AUTOEMPLEARSE </b></td>
                <td></td>
                <td><b> @if($alumnos->sistema_capacitacion_especificar=="MEJORAR SU SITUACION EN EL TRABAJO") (X) @else() ( ) @endif PARA MEJORAR SU SITUACION EN EL TRABAJO</b></td>
            </tr>
            <tr>
                <td colspan="3"><b>@if($alumnos->sistema_capacitacion_especificar=="AHORRAR GASTOS AL INGRESO FAMILIAR") (X) @else() ( ) @endif PARA AHORRAR GASTOS AL INGRESO FAMILIAR</b></td>
            </tr>
            <tr>
                <td colspan="3"><b>@if($alumnos->sistema_capacitacion_especificar=="ESTAR EN ESPERA  DE INCORPORARSE EN OTRA INSTITUCION EDUCATIVA") (X) @else() ( ) @endif POR ESTAR EN ESPERA DE INCORPORARSE EN OTRA INSTITUCIÓN EDUCATIVA</b></td>
            </tr>
            <tr>
                <td colspan="3"><b>@if($alumnos->sistema_capacitacion_especificar=="DISPOSICION DE TIEMPO LIBRE") (X) @else() ( ) @endif POR DISPOSICIÓN DE TIEMPO LIBRE</b></td>
            </tr>
            <tr>
                <td><b>@if($alumnos->sistema_capacitacion_especificar!="EMPLEARSE O AUTOEMPLEARSE"&&$alumnos->sistema_capacitacion_especificar!="MEJORAR SU SITUACION EN EL TRABAJO"&&$alumnos->sistema_capacitacion_especificar!="AHORRAR GASTOS AL INGRESO FAMILIAR"&&$alumnos->sistema_capacitacion_especificar!="ESTAR EN ESPERA  DE INCORPORARSE EN OTRA INSTITUCION EDUCATIVA"&&$alumnos->sistema_capacitacion_especificar!="DISPOSICION DE TIEMPO LIBRE") (X) @else() ( ) @endif OTROS</b></td>
                <td><b>ESPECIFIQUE:</b></td>
                <td>@if($alumnos->sistema_capacitacion_especificar!="EMPLEARSE O AUTOEMPLEARSE"&&$alumnos->sistema_capacitacion_especificar!="MEJORAR SU SITUACION EN EL TRABAJO"&&$alumnos->sistema_capacitacion_especificar!="AHORRAR GASTOS AL INGRESO FAMILIAR"&&$alumnos->sistema_capacitacion_especificar!="ESTAR EN ESPERA  DE INCORPORARSE EN OTRA INSTITUCION EDUCATIVA"&&$alumnos->sistema_capacitacion_especificar!="DISPOSICION DE TIEMPO LIBRE") {{$alumnos->sistema_capacitacion_especificar}} @else() @endif</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;">EL ASPIRANTE SE COMPROMETE A CUMPLIR CON LAS NORMAS Y DISPOSICIONES DICTADAS POR LAS AUTORIDADES DE LA UNIDAD</td>
            </tr>
        </table>
        <br><br><br>
        <table class="table1">
            <tr>
                <td>{{ $alumnos->apellido_paterno }} {{ $alumnos->apellido_materno }} {{ $alumnos->nombrealumno }}</td>
                <td></td>
                <td align="right" style="text-align: center">{{ $alumnos->realizo }}</td>
            </tr>
            <tr>
                <td class="p"><b> NOMBRE Y FIRMA DEL ASPIRANTE</b></td>
                <td></td>
                <td class="p" align="right" style="text-align: center"><b> NOMBRE Y FIRMA DE LA PERSONA QUE INSCRIBE </b></td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <div style="font_size: 8px;"><b>AVISO DE PRIVACIDAD:</b>LOS DATOS PERSONALES CONTENIDOS EN ESTA SID-01 DE INSCRICIÓN, SERÁN PROTEGIDOS CONFORME A LO DISPUESTO POR LA LEY GENERAL DE PROTECCIÓN DE DATOS PERSONALES EN POSESIÓN DE SUJETOS OBLIGADOS, Y DEMÁS NORMATIVIDAD QUE RESULTE APLICABLE</div>
    </div>
    <br>
    <div style="border: 1px solid black;"></div>
    <br>
    <div style="border-style: dotted;border-width: 1px;"></div>
    <br>
    <div>
        <table>
            <tr>
                <td colspan="4" style="text-align: right"><b>COMPROBANTE PARA EL INSTITUTO</b> </div></td>
            </tr>
            <tr>
                <td colspan="2"><b>FECHA:</b> {{$date}}</td>
                <td colspan="2"><b>NÚMERO DE SOLICITUD:</b> {{$alumnos->no_control}}{{$alumnos->id}}</td>
            </tr>
        </table>
    </div>
    <br>
    <div style="border: 1px solid black;">
        <table>
            <tbody>
                <tr>
                    <td colspan="4"><b>NOMBRE DEL ASPIRANTE:</b> {{ $alumnos->apellido_paterno }} {{ $alumnos->apellido_materno }} {{ $alumnos->nombrealumno }}</td>
                </tr>
                <tr>
                    <td><b>CURSO:</b> {{ $alumnos->nombre_curso }}</td>
                    <td><b>HORARIO:</b> {{ $alumnos->horario }}</td>
                    <td><b>GRUPO:</b> {{ $alumnos->grupo }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br><br>
            <table>
                <tr>
                    <td><b>{{ $alumnos->realizo }}</b></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="p"><b> NOMBRE Y FIRMA DE LA PERSONA QUE RECIBE</b></td>
                    <td><b>SELLO</b></td>
                    <td class="p"><b>FIRMA DEL ASPIRANTE</b></td>
                </tr>
            </table>
    </div>
</body>
</html>
