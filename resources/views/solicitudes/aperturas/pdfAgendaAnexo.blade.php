@extends('theme.formatos.vlayout2025')
@section('title', 'AGENDA - ANEXO | SIVyC Icatech')
@section('content_script_css')
    <style>        
        body{font-family: sans-serif; padding: 100px 50px 145px 80px;  }
        .tabla { border-collapse: collapse; width: 100%; }
        .tabla tr th {padding:0px;margin:0px; page-break-inside: avoid; padding: 2px; background-color: lightgray;}
        .tabla th, .tabla td{page-break-inside: avoid;font-size: 12px; border: gray 1px solid; text-align: center;font-weight:bold; padding: 2px;}        
        .centrado { text-align: center; }
    </style>
@endsection
@section('content')    
        <h5 class="centrado">AGENDA - ANEXO(S)</h5>
    @foreach($data as $item)        
        <h5>Folio: {{$item->folio_grupo}}</h5>
        <h5>Curso: {{$item->curso}}</h5>
        @php
            $agendaArray = json_decode($item->agenda, true);
        @endphp
        
            
            <table class="tabla">
                <thead>
                    <tr>
                        <th>FECHAS</th>
                        <th>HORARIOS</th>
                        <th>HORAS</th>
                    </tr>             
                </thead>

                <tbody>
                    @foreach ($agendaArray as $i) 
                        <tr>
                            <td> {{ $i['fecha'] }} </td>
                            <td> {{ $i['horario'] }} </td>
                            <td> {{ $i['horas'] }}</td>
                        </tr>             
                    @endforeach                    
                    <tr>                            
                        
                        <td colspan="2" style="text-align: right; padding-right:20px;" >TOTAL HORAS: </td>
                        <td> {{ $item->total_horas*1 }}</td>
                    </tr>  
                </tbody>
            </table>     
        
    @endforeach
@endsection
@section('content_script_js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $pdf->text(40, 538, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
             ');
        }
    </script>
@endsection
