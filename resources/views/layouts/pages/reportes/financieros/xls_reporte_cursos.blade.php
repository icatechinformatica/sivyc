<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table style="border: 1px solid black">
    <thead>
        <tr style="border: 1px solid black;">
            <th><b>CONS.</b></th>
            <th><b>CLAVE</b></th>
            <th><b>HORAS CURSO</b></th>
            <th><b>INSTRUCTOR</b></th>
            <th><b>SUFICIENCIA PRESUPUESTAL</b></th>
            <th><b>URL FACTURA XML</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $cadwell)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$cadwell->clave}}</td>
                <td>{{$cadwell->dura}}</td>
                <td>{{$cadwell->nombre}}</td>
                <td>{{$cadwell->folio_validacion}}</td>
                <td><a href="{{$cadwell->arch_factura_xml}}" download="prueba.xml">XML {{$cadwell->folio_validacion}}</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
