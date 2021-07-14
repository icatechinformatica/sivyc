<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style type="text/css">
    @page{margin: 60px 30px 20px; font-size: 11px}
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
           @if ($alumnos->chk_fotografia == TRUE)
           <img class="derecha img-thumbnail mb-3" src="{{ public_path($pathimg) }}">
           @else
           <img class="derecha img-thumbnail mb-3" src="{{ public_path('img/blade_icons/nophoto.png') }}">
           @endif
        </div>
        <div style="text-align:center;" class="demo"><b>SUBSECRETARIA DE EDUCACIÓN MEDIA SUPERIOR <br>DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO <br>SOLICITUD DE INSCRIPCIÓN <br> ( SID - 01 )</b></div>
    </div>
    <br>
    <div>
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
            
                <tr><td colspan="7" class="variable"><b>DATOS PERSONALES</b></td></tr>
            
            
                <tr>
                    <td colspan="2" style="padding-top: 5px;"><b> NOMBRE DEL ASPIRANTE: </b></td>
                    <td colspan="2" style="padding-top: 5px;">{{ $alumnos->apellido_paterno }} <br><b> PRIMER APELLIDO</b></td>
                    <td colspan="2" style="padding-top: 5px;">{{ $alumnos->apellido_materno }} <br><B> SEGUNDO APELLIDO</B></td>
                    <td style="padding-top: 5px;">{{ $alumnos->nombrealumno }} <br><B> NOMBRE(S)</B></td>
                </tr>
                <tr>
                    <td><b>SEXO: @php if($alumnos->sexo=="FEMENINO"){echo "M(X) H( )";} else {echo"M( ) H(X)";} @endphp</b></td>
                    <td><b>CURP:</b></td>
                    <td>{{$alumnos->curp_alumno }}</td>
                    <td><b>EDAD:</b> </td>
                    <td>{{$edad}} AÑOS</td>
                    <td><b>TELEFONO:</b></td>
                    <td>{{$alumnos->telefono}}</td>
                </tr>
                <tr>
                    <td><b>DOMICILIO: </b></td>
                    <td colspan="2">{{ $alumnos->domicilio }}</td>
                    <td colspan="2"><b>COLONIA O LOCALIDAD: </b></td>
                    <td colspan="2">{{ $alumnos->colonia }}</td>
                </tr>
                <tr>
                    <td><b>C.P.: </b></td>
                    <td>{{ $alumnos->cp }}</td>
                    <td colspan="2"><b>MUNICIPIO: </b> </td>
                    <td>{{ $alumnos->municipio }}</td>
                    <td><b>ESTADO: </b></td>
                    <td>{{ $alumnos->estado }}</td>
                </tr>
                <tr>
                    <td colspan="7"><b>ESTADO CIVIL:</b> @php if($alumnos->estado_civil=="SOLTERO"){echo "SOLTERO(X) CASADO( ) VIUDO( ) DIVORCIADO( ) UNION LIBRE( )";}
                        if($alumnos->estado_civil=="CASADO"){echo "SOLTERO( ) CASADO(X) VIUDO( ) DIVORCIADO( ) UNION LIBRE( )";}
                        if($alumnos->estado_civil=="VIUDO"){echo "SOLTERO( ) CASADO( ) VIUDO(X) DIVORCIADO( ) UNION LIBRE( )";}
                        if($alumnos->estado_civil=="DIVORCIADO"){echo "SOLTERO( ) CASADO( ) VIUDO( ) DIVORCIADO(X) UNION LIBRE( )";}
                        if($alumnos->estado_civil=="UNION LIBRE"){echo "SOLTERO( ) CASADO( ) VIUDO( ) DIVORCIADO( ) UNION LIBRE(X)";}
                       else{echo "SOLTERO( ) CASADO( ) VIUDO( ) DIVORCIADO( ) UNION LIBRE( )";} @endphp</td>
                </tr>
                <tr>
                    <td colspan="7"><b>DISCAPACIDAD QUE PRESENTA: </b></td>
                </tr>
                <tr>
                    <td colspan="2">@php if($alumnos->discapacidad=="VISUAL"){echo "VISUAL(X)";}else{echo "VISUAL( )";} @endphp</td>
                    <td colspan="2"> @php if($alumnos->discapacidad=="AUDITIVA"){echo "AUDITIVA(X)";}else{echo "AUDITIVA( )";} @endphp</td>
                    <td colspan="2">@php if($alumnos->discapacidad=="DE COMUNICACION"){echo "DE COMUNICACION(X)";}else{echo "DE COMUNICACION( )";} @endphp</td>
                    <td></td>
                </tr>
                <tr>
                    <TD colspan="2">@php if($alumnos->discapacidad=="MOTRIZ"){echo "MOTRIZ(X)";}else{echo "MOTRIZ( )";} @endphp</TD>
                    <TD colspan="2">@php if($alumnos->discapacidad=="INTELECTUAL"){echo "INTELECTUAL(X)";}else{echo "INTELECTUAL( )";} @endphp</TD>
                    <TD colspan="2"></TD>
                    <TD></TD>
                </tr>
            
        </table>
    </div>
    <br>
    <div style="border: 1px solid black;">
        <table class="table">
                <tr>
                  <td colspan="4" class="variable"><b>DATOS GENERALES</b></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 5px;">ESPECIALIDAD A LA QUE DESEAN INSCRIBIRSE:</td>
                    <td colspan="2" style="padding-top: 5px;">{{ $alumnos->especialidad }}</td>
                </tr>
                <tr>
                    <td>CURSO:</td>
                    <td>{{ $alumnos->nombre_curso }}</td>
                    <td>TIPO:</td>
                    <td>{{$alumnos->tipo_curso}}</td>
                </tr>
                <tr>
                    <td>HORARIO: </td>
                    <td>{{ $alumnos->horario }} </td>
                    <td>GRUPO: </td>
                    <td>{{ $alumnos->grupo }}</td>
                </tr>
                <tr>
                    <td>ÚLTIMO GRADO DE ESTUDIOS:</td>
                    <td colspan="3">{{$alumnos->ultimo_grado_estudios}}</td>
                </tr>
                <tr>
                    <td colspan="4"><b>DOCUMENTACIÓN ENTREGADA: </b><br>
                                @if($alumnos->chk_acta_nacimiento == TRUE || $alumnos->chk_curp == TRUE)(X) @else() ( ) @endif COPIA DE ACTA DE NACIMIENTO (NO MAYOR A 2 AÑOS) O CURP (VIGENCIA UN AÑO)
                            <br>@if($alumnos->chk_comprobante_ultimo_grado == TRUE)(X) @else() ( ) @endif COPIA COMPROBANTE DEL ULTIMO GRADO DE ESTUDIOS EN CASO DE CONTAR CON EL
                            <br>@if($alumnos->chk_fotografia == TRUE)(X) @else() ( ) @endif FOTOGRAFÍA DIGITAL O IMPRESA</se> 
                </tr>
                <tr>
                    <td colspan="4"><b>EXTRANJEROS ANEXAR</b></td>
                </tr>
                <tr>
                    <td colspan="4">@if($alumnos->chk_comprobante_calidad_migratoria == TRUE)(X) @else() ( ) @endif COMPROBANTE DE CALIDAD MIGRATORIA CON LA QUE SE ENCUENTRA EN EL TERRITORIO NACIONAL</td>
                </tr>
                <tr>
                    <td><b>EMPRESA DONDE TRABAJA: </b></td>
                    <td>{{ $alumnos->empresa_trabaja }}</td>
                    <td><b>PUESTO: </b></td>
                    <td>{{ $alumnos->puesto_empresa }}</td>
                </tr>
                <tr>
                    <td><b>ANTIGUEDAD: </b></td>
                    <td>{{ $alumnos->antiguedad }}</td>
                    <td><b>DIRECCIÓN: </b></td>
                    <td>{{ $alumnos->direccion_empresa }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="font_size: 8px;border: 1px solid black;border-collapse: collapse;">NOTA: LA DOCUMENTACIÓN DEBERA ENTREGARSE EN ORIGINAL Y COPIA PARA SU COTEJO.</td>
                </tr>
        </table>
    </div>
    <div style="border: 1px solid black;">
        <table>
            <tr>
                <td colspan="3" class="variable"><b>DATOS PARA LA UNIDAD DE CAPACITACIÓN</b></td>
            </tr>
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
        <div>
            <table >
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><B>COMPROBANTE DEL ASPIRANTE</B></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>FECHA:</b> {{$date}}</td>
                       <td><b>NÚMERO DE SOLICITUD:</b> {{$alumnos->no_control.$alumnos->id}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <div style="border: 1px solid black;">
            <table>
                <tr>
                    <td style="padding-top: 5px;"><b>ASPIRANTE:</b></td>
                    <td colspan="5" style="padding-top: 5px;">{{ $alumnos->apellido_paterno }} {{ $alumnos->apellido_materno }} {{ $alumnos->nombrealumno }}</td>
                </tr>
                <tr>
                    <td><b>CURSO:</b></td>
                    <td>{{ $alumnos->nombre_curso }}</td>
                    <td><b>HORARIO:</b></td>
                    <td>{{$alumnos->horario }}</td>
                    <td><b>GRUPO:</b></td>
                    <td>{{ $alumnos->grupo }}</td>
                </tr>
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
                    <td></b></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>