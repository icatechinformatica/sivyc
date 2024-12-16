@extends('theme.formatos.vlayout2025')
@section('title', 'MEMORANDUM FORMATO ENTREGA SATISFACTORIA | SIVyC Icatech')
@section('content_script_css')
    <style type="text/css">
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;}
        .contenedor {
        position:RELATIVE;
        top:120px;
        width:100%;
        margin:auto;
        }
        .margin_top_ccp {
            margin-top: 7em;
        }
    </style>
@endsection
@section('content')
    {{-- SECCIÓN DE CONTENIDO --}}
    <div class="contenedor">
        <div align=right style="font-size:11px;"><b>DIRECCIÓN DE PLANEACIÓN. </b></div>
        <div align=right style="font-size:11px;"><b>MEMORÁNDUM NO. {{ $num_memo_planeacion }}</b></div>
        <div align=right style="font-size:11px;"><b>TUXTLA GUTIÉRREZ, CHIAPAS; {{ $fecha_ahora_espaniol }}</b></div>
        <br><br>
        <div align=left style="font-size:12px;"><b>{{ $funcionarios['dacademico']['titulo'] }} {{ $funcionarios['dacademico']['nombre'] }}.</b></div>
        <div align=left style="font-size:11px;"><b>{{ $funcionarios['dacademico']['puesto'] }}.</b></div>
        <div align="left" style="font-size: 11px;"><b>Presente</b></div>
        <br><br><br><br>
        <div align="justify" style="font-size:16px;">
           <p>
            De acuerdo a la información estadistica reportada mediante el Formato T, recibido por
            medio del Sistema Integral de Vinculación y Capacitación (SIVyC), con cierre al mes de <b>{{$mesReport}}</b>,
            me permito informarle que una vez revisada la información relativa a los números
            absolutos de las variables cualitativas y cuantitativas; esta Dirección a través del
            Departamento de Programación y Presupuesto da por concluido el cierre estadístico del mes
            de {{$mesReport}} del {{$anio}}.
           </p>
        </div>
        <br>

        <br>
        <div align="justify" style="font-size:16px;">Sin más por el momento y agradeciéndole su valioso apoyo, le envío un cordial saludo.</div>
        <br>

        <br>
        <br>
        <div style="font-size:11px;"> <b>A T E N T A M E N T E</b> </div>

        <div class="margin_top_ccp">
            <div style="font-size:11px;"> <b>{{ $funcionarios['dplaneacion']['titulo'] }} {{ $funcionarios['dplaneacion']['nombre'] }}</b> </div>
            <div style="font-size:11px;"> <b> {{ $funcionarios['dplaneacion']['puesto'] }} </b> </div>
            <br><br><br>
            <div style="font-size:10px;"> <b>C.C.P {{ $funcionarios['dgeneral']['titulo'] }} {{ $funcionarios['dgeneral']['nombre'] }}  , {{ $funcionarios['dgeneral']['puesto'] }} . EDIFICIO.</b> </div>
            <div style="font-size:10px"><b>C.C. {{ $funcionarios['progpres']['titulo'] }} {{ $funcionarios['progpres']['nombre'] }} - {{ $funcionarios['progpres']['puesto']}} . EDIFICIO.</b></div>
            <div style="font-size:9px;"> <b>ARCHIVO.</b> </div>
            <div style="font-size:9px;"> <b>VALIDÓ:  {{ $funcionarios['progpres']['titulo'] }} {{ $funcionarios['progpres']['nombre'] }} - {{ $funcionarios['progpres']['puesto'] }} .</b> </div>
            <div style="font-size:9px;"> <b>ELABORÓ: LIC. {{ $funcionarios['elabora']['nombre'] }} - {{ $funcionarios['elabora']['puesto'] }}.</b></div>
            <br><br>
        </div>

    </div>
@endsection
@section('script_content_js')
@endsection
