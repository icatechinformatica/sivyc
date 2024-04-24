<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table style="border: 1px solid black">
    <thead>
        <tr style="border: 1px solid black;">
            <th><b>CONS.</b></th>
            <th><b>CLAVE</b></th>
            <th><b>HORAS CURSO</b></th>
            <th><b>CURSO</b></th>
            <th><b>NOMBRE</b></th>
            <th><b>APELLIDO PATERNO</b></th>
            <th><b>APELLIDO MATERNO</b></th>
            <th><b>RFC</b></th>
            <th><b>CURP</b></th>
            <th><b>BANCO</b></th>
            <th><b>NO. CUENTA</b></th>
            <th><b>CLABE</b></th>
            <th><b>SUFICIENCIA PRESUPUESTAL</b></th>
            <th><b>FECHA CONTRATO</b></th>
            <th><b>MONTO CONTRATO</b></th>
            <th><b>INICIO CURSO</b></th>
            <th><b>FINAL CURSO</b></th>
            <th><b>URL FACTURA XML</b></th>
        </tr>
    </thead>
    <tbody>
        @php $total = null;@endphp
        @foreach ($data as $key => $cadwell)
            <tr>
                @php
                    $total = $cadwell->importe_total - $cadwell->iva;
                    $datosBanco = json_decode($cadwell->soportes_instructor);
                @endphp

                <td>{{$key+1}}</td>
                <td>{{$cadwell->clave}}</td>
                <td>{{$cadwell->dura}}</td>
                <td>{{$cadwell->curso}}</td>
                <td>{{$cadwell->nombre}}</td>
                <td>{{$cadwell->apellidoPaterno}}</td>
                <td>{{$cadwell->apellidoMaterno}}</td>
                <td>{{$cadwell->rfc}}</td>
                <td>{{$cadwell->curp}}</td>
                @if(!is_null($datosBanco))
                    <td>{{$datosBanco->banco}}</td>
                    <td>{{$datosBanco->no_cuenta}}</td>
                    <td>{{$datosBanco->interbancaria}}</td>
                @endif
                <td>{{$cadwell->folio_validacion}}</td>
                <td>{{$cadwell->inicio}}</td>
                <td>{{$total}}</td>
                <td>{{$cadwell->inicio}}</td>
                <td>{{$cadwell->termino}}</td>
                @if(is_null($cadwell->arch_factura_xml))
                    <td>N/A</td>
                @else
                    <td><a href="{{$cadwell->arch_factura_xml}}" download="prueba.xml">XML {{$cadwell->folio_validacion}}</a></td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
