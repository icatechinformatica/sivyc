<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FORMATO T</title>

    <style>
        body{font-family: sans-serif}
        @page {margin: 20px 50px 120px 50px;size: letter;}
        header { position: fixed; left: 0px; top: 0px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        img.izquierda {float: left;width: 100%;height: 60px;}
        img.izquierdabot {
                float: inline-end;
                width: 712px;
                height: 100px;
            }
        img.derechabot {position:fixed;right: 50px;width: 350px;height: 60px;}
        img.derecha {float: right;width: 50%;height: 60px;}
        .tablas{border-collapse: collapse;width: 100%;}
        /* agregamos a 3 el padding para que no salte a la otra pagina y la deje en blanco */
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        /* .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;} */
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
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
        .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 830px; /*820*/
                left: 20px;
                font-size: 7.5px;
                color: white;
                line-height: 1;
            }
    </style>

     {{-- condicion cuando el array sea de 14 elementos cambia el pading de la fila de la tabla--}}
    {{-- @if (count($reg_cursos) ==14)
        <style>
            .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 3px;}
        </style>
    @endif --}}
</head>
<body>
    <header>
            <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
            <h6>{{$leyenda}}</h6><p class='direccion'>
    </header>
    <footer>
        <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">

        {{-- <p class='direccion' style="bottom:25px;"><b>
            Av. Circunvalación Pichucalco núm. 212-B. Colonia Moctezuma.<br>
            C.P. 29030; Tuxtla Gutiérrez, Chiapas.<br><br>
            Teléfono (961)6121621, Email: dtecnicaacademica@gmail.com
        </b></p> --}}
        <p class='direccion'><b>
            @php $direccion = explode("*",$funcionarios['dacademico']['direccion']) @endphp
            @foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}} @endforeach
            <br>Teléfono: {{$funcionarios['dacademico']['telefono']}} Correo: {{$funcionarios['dacademico']['correo']}}
        </b></p>
    </footer>
    <div class="contenedor" style="margin-bottom: 100px;">
        {{-- crear un div para encerrar todo lo que lleva al inicio --}}
        <div>
            <div align=right style="font-size:13px;"><b>Dirección Técnica Académica</b></div>
            <div align=right style="font-size:13px;"><b>MEMORANDUM NO. {{$numero_memo}}</b></div>
            <div align=right style="font-size:13px;"><b>Tuxtla Gutiérrez, Chiapas; {{$D}} de {{$M}} del {{$Y}}</b></div>
            <br><br>
            <div align=left style="font-size:13px;"><b> {{$funcionarios['dunidad']['titulo']}} {{$funcionarios['dunidad']['nombre']}}</b></div>
            <div align=left style="font-size:13px;"><b>{{$funcionarios['dunidad']['puesto']}}.</b></div>
            <div align=left style="font-size:13px;"><b>Presente</b></div>
            <br><br>
            <div align="justify" style="font-size:14px;">
                En seguimiento a la integracion del formato T del mes de <b>{{$MT}}</b> del presente año de su Unidad de Capacitación, recibido el
                pasado <b>{{$info_cursos['fecha_envio']}}</b> por medio del Sistema Integral de Vinculación y Capacitación (SIVYC), le informo que fueron recibidos
                un total de @foreach($count_cursos as $key => $data)@if(array_key_last($count_cursos) == $key) y @endif <b>{{$data}}</b> cursos de la @if($unidad->ubicacion == $key) Unidad @else Acción Móvil @endif <b>{{$key}}</b>;@endforeach
                de lo anterior, hago de su conocimiento que, una vez revisada la información, se reportaron a la Dirección de Planeación de este Instituto un total de <b>{{$info_cursos['total_cursos']}}</b> cursos.
                @if(!is_null($historial_meses))No omito manifestar que no cuentan con cursos pendientes por reportar en formato T del mes <b>{{$historial_meses}}.@endif</b>
            </div>
            <br><br>
            <div align="justify" style="font-size:16px;">Sin más por el momento, agradezco su atención y le envio un cordial saludo.</div>
            <br><br><br><br>
            <div align="justify" style="font-size:16px;"><b>A T E N T A M E N T E</b></div>
            <br><br><br>
            <div align="justify" style="font-size:14px;"><b>{{$funcionarios['dacademico']['titulo']}} {{$funcionarios['dacademico']['nombre']}}.</b></div>
            <div align="justify" style="font-size:14px;"><b>{{$funcionarios['dacademico']['puesto']}}.</b></div>
            <br><br><br>
            <div align="justify" style="font-size:10px;">C.c.p. {{$funcionarios['dacademico_unidad']['titulo']}} {{$funcionarios['dacademico_unidad']['nombre']}} - {{$funcionarios['dacademico_unidad']['puesto']}}, Ciudad.</div>
            <div align="justify" style="font-size:10px;">Archivo.</div>
            <div align="justify" style="font-size:10px;">Validó: {{$funcionarios['certificacion']['titulo']}} {{$funcionarios['certificacion']['nombre']}} - {{$funcionarios['certificacion']['puesto']}}, Edificio.</div>
            <div align="justify" style="font-size:10px;">Elaboró: {{$funcionarios['elabora']['nombre']}} - {{$funcionarios['elabora']['puesto']}}.</div>
        </div>
    </div>
</body>
</html>





