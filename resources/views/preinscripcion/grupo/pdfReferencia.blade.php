<html>
<head>
     <style>
        body{font-family: sans-serif;}
        @page { margin:25px; }        
        table { padding:15px;}
        table tr td{ padding: 5px; }
        hr { margin:15px;}
    </style>
</head>
<body>
    <header></header>
    <footer></footer>
    <main>
    @if(isset($data))        
        @foreach($data as $item)
            <table>
                <thead></thead>
                <tbody>
                    <tr><td colspan="2"><b>{{$instituto}}</b></td></tr>
                    <tr><td><b>Referencia de pago:</b></td><td>{{ $item->referencia }}</td></tr>
                    <tr><td><b>{{$nombre}}:</b></td><td>{{$item->alumno}}</td></tr>
                    <tr><td><b>Monto:</b></td><td>{{number_format($item->costo, 2, '.', '')}}</td></tr>
                    <tr><td><b>Fecha limite:</b></td><td>{{date('d/m/Y', strtotime($item->inicio))}}</td></tr>
                    <tr><td><b>Concepto:</b></td><td>CUOTA DE CURSO DE CAPACITACIÓN</td></tr>
                    <tr><td><b>Banco autorizado:</b></td><td>BBVA</td></tr>
                    <tr><td><b>Instrucciones:</b></td><td>XXXXX</td></tr>                
                </tbody>                
            </table>
            <hr style="border: none; border-top: 1px dashed #000;">
        @endforeach
     @else
            {{ "No se encontraron datos que mostrar, por favor intente de nuevo." }}
     @endif
</body>
</html>


