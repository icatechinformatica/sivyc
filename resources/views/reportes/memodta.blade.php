@extends('theme.formatos.vlayout'.$layout_año)
@section('title', 'Formato T | SIVyC Icatech')
@section('content_script_css')
    <style>
        .tablas{border-collapse: collapse;width: 100%;}
        /* agregamos a 3 el padding para que no salte a la otra pagina y la deje en blanco */
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        /* .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;} */
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;}
        .contenedor {
        position:RELATIVE;
        top:80px;
        width:100%;
        margin:auto;
        /* Propiedad que ha sido agreda*/
        }
    </style>

     {{-- condicion cuando el array sea de 14 elementos cambia el pading de la fila de la tabla--}}
    @if (count($reg_cursos) ==14)
        <style>
            .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 3px;}
        </style>
    @endif
@endsection
@section('content')
    <div class="contenedor" style="margin-bottom: 100px;">
        {{-- crear un div para encerrar todo lo que lleva al inicio --}}
        <div>
            <div align=right style="font-size:13px;">UNIDAD DE CAPACITACION {{ $reg_unidad->unidad }}</div>
            <div align=right style="font-size:13px;">MEMORANDUM NO. {{ $numero_memo }}</div>
            <div align=right style="font-size:13px;">{{ $reg_unidad->unidad }}, CHIAPAS; {{ $fecha_nueva }}</div>
            <br>
            <div align=left style="font-size:13px;">C. {{ $funcionarios['dacademico']['nombre'] }}</div>
            <div align=left style="font-size:13px;">{{ $funcionarios['dacademico']['puesto'] }}</div><br>

            <div align=left style="font-size:13px;">Asunto: Reporte de cursos finalizados de la  Unidad de Capacitación {{ $reg_unidad->unidad }}.</div><br>
            <div align="justify" style="font-size:13px;">
                Derivado del proceso académico de entrega de información, adjunto al presente con firmas autógrafas y los sellos originales correspondientes:
            </div>
            <br>
            <table class="tablag">
                <thead>
                    <tr>
                        <td style="font-size:13px;"><b>* RIAC-02 de inscripción</b></td>
                        <td style="font-size:13px;"><b>* LAD-04</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:13px;"><b>* RIAC-02 de certificación</b></td>
                        <td style="font-size:13px;"><b>* RESD-05</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:13px;"><b>* RIAC-02 de acreditación</b></td>
                    </tr>
                </thead>
            </table>
            <br>
            <div align="justify" style="font-size:13px;">Del ciclo escolar {{ $reg_cursos[0]->ciclo }} en la Unidad de Capacitación {{ $reg_unidad->unidad }}, se reportan {{ $total }} curso(s)/certificacione(s) para este mes:</div>

        </div>
             <br>
            {{-- se llenan de datos esta tabla --}}
            <table class="tablas">
                <thead>
                    <tr>
                        <th>UNIDAD DE CAPACITACIÓN O CENTRO DE TRABAJO ACCIÓN MÓVIL</th>
                        <th>CURSO</th>
                        <th>MOD</th>
                        <th>INICIO </th>
                        <th>TERMINO</th>
                        <th>CUPO</th>
                        <th>INSTRUCTOR EXTERNO</th>
                        <th>CLAVE</th>
                        <th>OBSERVACIONES</th>
                    </tr>
                </thead>
                <tbody>

                   @foreach ($reg_cursos as $a)
                    <tr>
                        <th>{{ $a->unidad }}</th>
                        <th>{{ $a->curso }}</th>
                        <th>{{ $a->mod }}</th>
                        <th>{{ $a->inicio }}</th>
                        <th>{{ $a->termino }}</th>
                        <th>{{ $a->cupo }}</th>
                        <th>{{ $a->nombre }}</th>
                        <th>{{ $a->clave }}</th>
                        <th>{{ $a->tnota }}</th>
                    </tr>
                   @endforeach
                </tbody>
            </table>
            {{-- Al final del documento --}}
            <br>
            {{-- creo un div para contener todo el texto que lleva al final --}}
            <div>
                <div style="font-size:13px;">Sin más por el momento, le envío un cordial saludo.</div>
                <br>
                <div style="font-size:13px;">ATENTAMENTE</div>
                <br><br><br>
                <div style="font-size:13px;">C. {{ $funcionarios['dunidad']['nombre'] }}</div>
                <div style="font-size:13px;">{{ $funcionarios['dunidad']['puesto'] }}</div>
                <br><br>
                <div style="font-size:10px;">C.c.p. {{$funcionarios['certificacion']['nombre']}}. - {{$funcionarios['certificacion']['puesto']}}. - Para su conocimiento.</div>
                <div style="font-size:10px;">Archivo.</b> </div>
                <div style="font-size:10px;">Validó: {{ $funcionarios['dunidad']['nombre'] }}. - {{ $funcionarios['dunidad']['puesto'] }}.</div>
                <div style="font-size:10px;">Elaboró: {{ $funcionarios['elabora']['nombre']}}. - {{ $funcionarios['elabora']['puesto'] }}.</div>
            </div>

    </div>
@endsection
@section('script_content_js')
@endsection
