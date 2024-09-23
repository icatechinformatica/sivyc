<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MEMORANDUM FORMATO ENTREGA SATISFACTORIA</title>
    <style type="text/css">
        body{font-family: sans-serif}
        /* margenes top 20px right 50px bottom 120px left 50px */
        @page {margin: 20px 50px 120px 50px;size: letter;}
        header { position: fixed; left: 0px; top: 0px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        /* header { position: fixed;
                left: 0px;
                top: -90px;
                padding-left: 45px;
                height: 70px;
                width: 85%;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            } */
        img.izquierda {float: left;width: 100%;height: 60px;}
        img.izquierdabot {
            float: inline-end;
            width: 712px;
            height: 100px;
        }
        img.derechabot {position:fixed;right: 50px;width: 350px;height: 60px;}
        img.derecha {float: right;width: 200px;height: 60px;}
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .direccion
        {
            text-align: left;
            position: absolute;
            bottom: 830px; /*830*/
            left: 20px;
            font-size: 7.5px;
            color: white;
            line-height: 1;
        }
        .tablag tr td { font-size: 8px; padding: 0px;}
        footer {position:fixed;left:0px;bottom:0px;width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        .contenedor {
        position:RELATIVE;
        top:120px;
        width:100%;
        margin:auto;

        /* Propiedad que ha sido agreda*/

        }
        .margin_top_ccp {
            margin-top: 7em;
        }
    </style>
</head>
<body>
    <header>
        <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
        <br><h6>{{$leyenda}}</h6>
    </header>
    <footer>
        <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
        <p class='direccion'><b>
            @php $direccion = explode("*",$funcionarios['dacademico']['direccion']) @endphp
            @foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}} @endforeach
            <br>
            {{-- Teléfono: {{$funcionarios['dacademico']['telefono']}}  --}}
            Correo: {{$funcionarios['progpres']['correo']}}
        </b></p>
    </footer>
    {{-- SECCIÓN DE PIE DE PÁGINA FIN --}}
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
    {{-- SECCIÓN DE CONTENIDO FIN --}}

</body>
</html>
