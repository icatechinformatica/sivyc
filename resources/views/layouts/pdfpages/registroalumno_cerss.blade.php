<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SID-CERSS 01</title>
    <style>
        *{box-sizing: border-box;}
        @page{
            margin: 60px 30px 20px; 
            font-size: 9px;
            font-family: sans-serif;
        }
        .encabezado{ text-align: center; width: 100%;}
        img.izquierda{ float:left}
        img.derecha { float: right; width: 2.5cm; height: 3.0cm;}
        .p{ text-decoration: overline;}
        table { width: 100%; border-collapse: collapse;}
        /* td{ padding: 0px; padding-left: 5px; padding-bottom: 3px;} */
        .variable{ text-align: center; border: 1px solid black;}
        td{ padding-top: 2px; padding-left: 2px;}
    </style>
</head>
<body>
    <div class="encabezado">
        <img class="izquierda" src="{{ public_path('img/sep1.png') }}">
        @if ($alumnos->chk_fotografia == TRUE)
           <img class="derecha img-thumbnail mb-3" src="{{ public_path($pathimg) }}">
        @else
           <img class="derecha img-thumbnail mb-3" src="{{ public_path('img/blade_icons/nophoto.png') }}">
        @endif
        <p><strong>SUBSECRETARIA DE EDUCACIÓN MEDIA SUPERIOR <br>DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO <br>SOLICITUD DE INSCRIPCIÓN EN EL CERESO<br> ( SID - CERSS 01 )</strong></p>
    </div>
    <br>
    <table style="width: 80%; text-align: center;">
        <tr>
            <td style="text-decoration: underline;">{{$date}}</td>
            <td>{{str_pad($alumnos->id, 8, "0", STR_PAD_LEFT)}}</td>
        </tr>
        <tr>
            <td>FECHA</td>
            <td class="p">NÚMERO DE SOLICITUD</td>
        </tr>
    </table>
    <br>
    <table style="border: 1px solid black;">
        <tr>
            <td colspan="4" class="variable"><strong>DATOS DE LA UNIDAD DE CAPACITACIÓN</strong></td>
        </tr>
        <tr>
          <td colspan="4"><strong>INSTITUTO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INSTITUTO DE CAPACITACIÓN Y VINCULACION TECNÓLOGICA DEL ESTADO DE CHIAPAS</strong></td>
        </tr>
        <tr>
          <td style="padding-bottom: 2px;"><strong> UNIDAD DE CAPACITACIÓN: {{ $alumnos->unidad }}</strong></td>
          <td style="padding-bottom: 2px;"></td>
          <td style="padding-bottom: 2px;"><strong> CLAVE CCT:  {{$alumnos->unidades }}</strong></td>
          <td style="padding-bottom: 2px;"></td>
        </tr>
    </table>
    <br>
    <table style="border: 1px solid black;">
        <tr><td colspan="7" class="variable"><strong>DATOS PERSONALES Y CERSS</strong></td></tr>
        <tr>
            <td colspan="3"><strong>NÚMERO DE EXPEDIENTE: &nbsp;&nbsp;</strong>{{$alumnos->numero_expediente}}</td>
            <td colspan="4"><strong>NOMBRE DEL CERSS: &nbsp;&nbsp;</strong>{{$alumnos->nombre_cerss}}</td>
        </tr>
        <tr>
            <td colspan="4"><strong>DIRECCIÓN CERSS: &nbsp;&nbsp;</strong>{{$alumnos->direccion_cerss}}</td>
            <td colspan="3"><strong>TITULAR CERSS: &nbsp;&nbsp;</strong>{{$alumnos->titular_cerss}}</td>
        </tr>
        <tr>
            <td colspan="7"><strong>NACIONALIDAD: &nbsp;&nbsp;</strong>{{$alumnos->nacionalidad}}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>NOMBRE DEL ASPIRANTE:</strong></td>
            <td colspan="2">{{ strtoupper($alumnos->apellido_paterno) }}<br><strong>PRIMER APELLIDO</strong></td>
            <td colspan="2">{{ strtoupper($alumnos->apellido_materno) }}<br><strong> SEGUNDO APELLIDO</strong></td>
            <td>{{ strtoupper($alumnos->nombrealumno) }}<br><strong>NOMBRE(S)</strong></td>
        </tr>
        <tr>
            <td><strong>SEXO: @php if($alumnos->sexo=="FEMENINO"){echo "M(X) H( )";} else {echo"M( ) H(X)";} @endphp</strong></td>
            <td><strong>CURP:</strong></td>
            <td>{{strtoupper($alumnos->curp_alumno) }}</td>
            <td><strong>EDAD:</strong> </td>
            <td>{{$edad}} AÑOS</td>
            <td><strong>TELEFONO:</strong></td>
            <td>@php if($alumnos->telefono_casa){echo ($alumnos->telefono_casa); }else{if($alumnos->telefono_personal){echo($alumnos->telefono_personal);}else{echo($alumnos->telefono);} } @endphp</td>
        </tr>
        <tr>
            <td><strong>DOMICILIO: </strong></td>
            <td colspan="2">{{ strtoupper($alumnos->domicilio) }}</td>
            <td colspan="2"><strong>COLONIA O LOCALIDAD: </strong></td>
            <td colspan="2">{{ strtoupper($alumnos->colonia) }}</td>
        </tr>
        <tr>
            <td><strong>C.P.: </strong></td>
            <td>{{ $alumnos->cp }}</td>
            <td><strong>MUNICIPIO: </strong> </td>
            <td colspan="2">{{strtoupper($alumnos->municipio)  }}</td>
            <td><strong>ESTADO: </strong></td>
            <td>{{ strtoupper($alumnos->estado) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>ESTADO CIVIL:</strong></td>
            @php 
                if($alumnos->estado_civil=="SOLTERO (A)"||$alumnos->estado_civil=="SOLTERO"){echo "<td>SOLTERO(X)</td> <td>CASADO( )</td> <td>VIUDO( )</td> <td>DIVORCIADO( )</td> <td>UNION LIBRE( )</td>";}
                else if($alumnos->estado_civil=="CASADO (A)"||$alumnos->estado_civil=="CASADO"){echo "<td>SOLTERO( )</td> <td>CASADO(X)</td> <td>VIUDO( )</td> <td>DIVORCIADO( )</td> <td>UNION LIBRE( )</td>";}
                else if($alumnos->estado_civil=="VIUDO (A)"||$alumnos->estado_civil=="VIUDO"){echo "<td>SOLTERO( )</td> <td>CASADO( )</td> <td>VIUDO(X)</td> <td>DIVORCIADO( )</td> <td>UNION LIBRE( )</td>";}
                else if($alumnos->estado_civil=="DIVORCIADO (A)"||$alumnos->estado_civil=="DIVORCIADO"){echo "<td>SOLTERO( )</td> <td>CASADO( )</td> <td>VIUDO( )</td> <td>DIVORCIADO(X)</td> <td>UNION LIBRE( )</td>";}
                else if($alumnos->estado_civil=="UNION LIBRE"||$alumnos->estado_civil=='UNIÓN LIBRE'){echo "<td>SOLTERO( )</td> <td>CASADO( )</td> <td>VIUDO( )</td> <td>DIVORCIADO( )</td> <td>UNION LIBRE(X)</td>";}
                else {echo "<td>SOLTERO( )</td> <td>CASADO( )</td> <td>VIUDO( )</td> <td>DIVORCIADO( )</td> <td>UNION LIBRE( )</td>";}
            @endphp
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom: 2px;"><strong>DISCAPACIDAD QUE PRESENTA: </strong></td>
            @php
                if($alumnos->id_gvulnerable && in_array(18, json_decode($alumnos->id_gvulnerable))){
                    echo " <td>VISUAL(X)</td> <td>AUDITIVA( )</td> <td>DE COMUNICACIÓN( )</td> <td>MOTRIZ( ) </td> <td>INTELECTUAL( )</td>";
                } elseif ($alumnos->id_gvulnerable && in_array(19, json_decode($alumnos->id_gvulnerable))) {
                    echo "<td>VISUAL( )</td> <td>AUDITIVA(X)</td> <td>DE COMUNICACIÓN( )</td> <td>MOTRIZ( ) </td> <td>INTELECTUAL( )</td>";
                } elseif ($alumnos->id_gvulnerable && in_array(20, json_decode($alumnos->id_gvulnerable))) {
                    echo "<td>VISUAL( )</td> <td>AUDITIVA( )</td> <td>DE COMUNICACIÓN(X)</td> <td>MOTRIZ( ) </td> <td>INTELECTUAL( )</td>";
                } elseif ($alumnos->id_gvulnerable && in_array(21, json_decode($alumnos->id_gvulnerable))) {
                    echo "<td>VISUAL( )</td> <td>AUDITIVA( )</td> <td>DE COMUNICACIÓN( )</td> <td>MOTRIZ(X) </td> <td>INTELECTUAL( )</td>";
                } elseif ($alumnos->id_gvulnerable && in_array(22, json_decode($alumnos->id_gvulnerable))) {
                    echo "<td>VISUAL( )</td> <td>AUDITIVA( )</td> <td>DE COMUNICACIÓN( )</td> <td>MOTRIZ( ) </td> <td>INTELECTUAL(X)</td>";
                } else {
                    echo "<td>VISUAL( )</td> <td>AUDITIVA( )</td> <td>DE COMUNICACIÓN( )</td> <td>MOTRIZ( ) </td> <td>INTELECTUAL( )</td>";
                }
            @endphp
        </tr>
    </table>
    <br>
    <table style="border: 1px solid black">
        <tr>
          <td colspan="4" class="variable"><strong>DATOS GENERALES</strong></td>
        </tr>
        <tr>
            <td colspan="4"><strong>ESPECIALIDAD A LA QUE DESEAN INSCRIBIRSE:</strong>  {{ strtoupper($alumnos->especialidad) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>CURSO:</strong>  {{ strtoupper($alumnos->nombre_curso) }}</td>
            <td colspan="2"><strong>TIPO:</strong>  {{strtoupper($alumnos->tipocurso)}}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>HORARIO:</strong>  {{ $alumnos->horario }}</td>
            <td colspan="2"><strong>GRUPO:</strong>  {{ $alumnos->grupo }}</td>
        </tr>
        <tr>
            <td colspan="4"><strong>ÚLTIMO GRADO DE ESTUDIOS:</strong>  {{strtoupper($alumnos->ultimo_grado_estudios)}}</td>
        </tr>
        <tr>
            <td colspan="4"><strong>DOCUMENTACIÓN ENTREGADA: </strong><br>
                        @if($alumnos->chk_acta_nacimiento == TRUE || $alumnos->chk_curp == TRUE)(X) @else() ( ) @endif COPIA DE ACTA DE NACIMIENTO (NO MAYOR A 2 AÑOS) O CURP (VIGENCIA UN AÑO)
                    <br>@if($alumnos->chk_comprobante_ultimo_grado == TRUE)(X) @else() ( ) @endif COPIA COMPROBANTE DEL ULTIMO GRADO DE ESTUDIOS EN CASO DE CONTAR CON EL
                    <br>@if($alumnos->chk_fotografia == TRUE)(X) @else() ( ) @endif FOTOGRAFÍA DIGITAL O IMPRESA
                    <br>@if($alumnos->chk_ficha_cerss == true) (X) @else() ( ) @endif FICHA CERSS</td>
        </tr>
        <tr>
            <td colspan="4"><strong>EXTRANJEROS ANEXAR</strong></td>
        </tr>
        <tr>
            <td colspan="4">@if($alumnos->chk_comprobante_calidad_migratoria == TRUE)(X) @else() ( ) @endif COMPROBANTE DE CALIDAD MIGRATORIA CON LA QUE SE ENCUENTRA EN EL TERRITORIO NACIONAL</td>
        </tr>
        <tr>
            <td colspan="2"><strong>EMPRESA DONDE TRABAJA: </strong> @php if($alumnos->empleado==true){echo(strtoupper($alumnos->empresa_trabaja));}else{echo"";}@endphp</td>
            <td colspan="2"><strong>PUESTO: </strong> {{strtoupper($alumnos->puesto_empresa) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>ANTIGUEDAD: </strong> {{ $alumnos->antiguedad }}</td>
            <td colspan="2"><strong>DIRECCIÓN: </strong> {{ strtoupper($alumnos->direccion_empresa) }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font_size: 8px;border: 1px solid black;border-collapse: collapse;">NOTA: LA DOCUMENTACIÓN DEBERA ENTREGARSE EN ORIGINAL Y COPIA PARA SU COTEJO.</td>
        </tr>
    </table>
    <table style="border: 1px solid black;">
        <tr>
            <td colspan="3" class="variable"><b>DATOS PARA LA UNIDAD DE CAPACITACIÓN</b></td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 5px;"><b>MEDIO POR EL QUE SE ENTERÓ DEL SISTEMA:</b></td>
        </tr>
        <tr>
            <td><b>@if($alumnos->medio_entero=="PRENSA") (X) @else() ( ) @endif PRENSA </b></td>
            <td><b>@if($alumnos->medio_entero=="TELEVISIÓN") (X) @else() ( )  @endif TELEVISIÓN</b></td>
            <td><b>@if($alumnos->medio_entero=="FOLLETOS,CARTELES,VOLANTE") (X)  @else() ( ) @endif FOLLETOS,CARTELES,VOLANTES</b></td>
        </tr>
        <tr>
            <td><b>@if($alumnos->medio_entero=="RADIO") (X)  @else() ( ) @endif RADIO</b></td>
            <td><b>@if($alumnos->medio_entero=="INTERNET") (X)  @else() ( ) @endif INTERNET</b></td>
            <td></td>
        </tr>
        <tr>
            <td><b>@if($alumnos->medio_entero!="PRENSA"&&$alumnos->medio_entero!="TELEVISIÓN"&&$alumnos->medio_entero!="FOLLETOS,CARTELES,VOLANTE"&&$alumnos->medio_entero!="RADIO"&&$alumnos->medio_entero!="INTERNET") (X)  @else() ( ) @endif OTROS</b></td>
            <td><b>ESPECIFIQUE:</b></td>
            <td>@if($alumnos->medio_entero!="PRENSA"&&$alumnos->medio_entero!="TELEVISIÓN"&&$alumnos->medio_entero!="FOLLETOS,CARTELES,VOLANTE"&&$alumnos->medio_entero!="RADIO"&&$alumnos->medio_entero!="INTERNET") {{$alumnos->medio_entero}} @else() @endif</td>
        </tr>
        <tr>
            <td colspan="3"><b>MOTIVOS DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN:</b></td>
        </tr>
        <tr>
            <td><b> @if($alumnos->sistema_capacitacion_especificar=="PARA EMPLEARSE O AUTOEMPLEARSE") (X) @else() ( ) @endif PARA EMPLEARSE O AUTOEMPLEARSE </b></td>
            <td></td>
            <td><b> @if($alumnos->sistema_capacitacion_especificar=="PARA MEJORAR SU SITUACIÓN EN EL TRABAJO") (X) @else() ( ) @endif PARA MEJORAR SU SITUACIÓN EN EL TRABAJO</b></td>
        </tr>
        <tr>
            <td colspan="3"><b>@if($alumnos->sistema_capacitacion_especificar=="PARA AHORRAR GASTOS AL INGRESO FAMILIAR") (X) @else() ( ) @endif PARA AHORRAR GASTOS AL INGRESO FAMILIAR</b></td>
        </tr>
        <tr>
            <td colspan="3"><b>@if($alumnos->sistema_capacitacion_especificar=="POR ESTAR EN ESPERA  DE INCORPORARSE EN OTRA INSTITUCIÓN EDUCATIVA") (X) @else() ( ) @endif POR ESTAR EN ESPERA DE INCORPORARSE EN OTRA INSTITUCIÓN EDUCATIVA</b></td>
        </tr>
        <tr>
            <td colspan="3"><b>@if($alumnos->sistema_capacitacion_especificar=="POR DISPOSICIÓN DE TIEMPO LIBRE") (X) @else() ( ) @endif POR DISPOSICIÓN DE TIEMPO LIBRE</b></td>
        </tr>
        <tr>
            <td><b>@if($alumnos->sistema_capacitacion_especificar!="PARA EMPLEARSE O AUTOEMPLEARSE"&&$alumnos->sistema_capacitacion_especificar!="PARA MEJORAR SU SITUACIÓN EN EL TRABAJO"&&$alumnos->sistema_capacitacion_especificar!="PARA AHORRAR GASTOS AL INGRESO FAMILIAR"&&$alumnos->sistema_capacitacion_especificar!="POR ESTAR EN ESPERA  DE INCORPORARSE EN OTRA INSTITUCIÓN EDUCATIVA"&&$alumnos->sistema_capacitacion_especificar!="POR DISPOSICIÓN DE TIEMPO LIBRE") (X) @else() ( ) @endif OTROS</b></td>
            <td><b>ESPECIFIQUE:</b></td>
            <td>@if($alumnos->sistema_capacitacion_especificar!="PARA EMPLEARSE O AUTOEMPLEARSE"&&$alumnos->sistema_capacitacion_especificar!="PARA MEJORAR SU SITUACIÓN EN EL TRABAJO"&&$alumnos->sistema_capacitacion_especificar!="PARA AHORRAR GASTOS AL INGRESO FAMILIAR"&&$alumnos->sistema_capacitacion_especificar!="POR ESTAR EN ESPERA  DE INCORPORARSE EN OTRA INSTITUCIÓN EDUCATIVA"&&$alumnos->sistema_capacitacion_especificar!="POR DISPOSICIÓN DE TIEMPO LIBRE") {{$alumnos->sistema_capacitacion_especificar}} @else() @endif</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">EL ASPIRANTE SE COMPROMETE A CUMPLIR CON LAS NORMAS Y DISPOSICIONES DICTADAS POR LAS AUTORIDADES DE LA UNIDAD</td>
        </tr>
        <tr>
            <td style="text-align: center;text-transform: uppercase;">
                <br><br>
                {{$alumnos->apellido_paterno}} {{$alumnos->apellido_materno}} {{$alumnos->nombrealumno}} <br>
                <strong style="text-decoration: overline;">NOMBRE Y FIRMA DEL ASPIRANTE</strong>
            </td>
            <td></td>
            <td style="text-align: center;text-transform: uppercase;">
                <br><br>
                {{$alumnos->realizo}} <br>
                <strong style="text-decoration: overline;">NOMBRE Y FIRMA DE LA PERSONA QUE INSCRIBE</strong>
            </td>
        </tr>
    </table>
    <br>
    <div style="font_size: 8px; width: 100%;"><strong>AVISO DE PRIVACIDAD:</strong>LOS DATOS PERSONALES CONTENIDOS EN ESTA SID-01 DE INSCRICIÓN, SERÁN PROTEGIDOS CONFORME A LO DISPUESTO POR LA LEY GENERAL DE PROTECCIÓN DE DATOS PERSONALES EN POSESIÓN DE SUJETOS OBLIGADOS, Y DEMÁS NORMATIVIDAD QUE RESULTE APLICABLE</div>
    <br>
    <div style="border: 1px solid black; width: 100%;"></div>
    <br>
    <div style="border-style: dotted;border-width: 1px; width: 100%;"></div>
    <br>
    <table>
        <tr>
            <td>
                <br>
                <strong>FECHA:</strong> {{$date}}
            </td>
            <td style="text-align: right;">
                <strong>COMPROBANTE DEL ASPIRANTE</strong><br>
                <strong>NÚMERO DE SOLICITUD:</strong> {{$alumnos->no_control.$alumnos->id}}
            </td>
        </tr>
    </table>
    <br>
    <table style="border: 1px solid black;">
        <tr>
            <td><strong>ASPIRANTE:</strong></td>
            <td colspan="5" style="text-transform: uppercase;">{{$alumnos->apellido_paterno}} {{$alumnos->apellido_materno}} {{$alumnos->nombrealumno}}</td>
        </tr>
        <tr>
            <td><strong>CURSO:</strong></td>
            <td colspan="3" style="text-transform: uppercase;">{{ strtoupper($alumnos->nombre_curso) }}</td>
            <td><strong>HORARIO:</strong> {{$alumnos->horario }}</td>
            <td style="text-transform: uppercase;"><strong>GRUPO:</strong> {{ $alumnos->grupo }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">
                <br><br>
                <strong style="text-transform: uppercase;">{{$alumnos->realizo}}</strong><br>
                <strong style="text-decoration: overline;">NOMBRE Y FIRMA DE LA PERSONA QUE RECIBE</strong>
            </td>
            <td colspan="2"></td>
            <td style="text-align: center;">
                <br><br>
                <strong>SELLO</strong>
            </td>
            <td></td>
        </tr>
    </table>
</body>
</html>