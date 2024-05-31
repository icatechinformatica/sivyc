<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.formatos.hlayout')
@section('title', 'PAT | SIVyC Icatech')
@section('css')
@php
    $hestilo = "background-color:#DEDEDE;  text-align: center; font-size: 7.5px;";
@endphp
    <style>
         @page { margin-bottom: 20px; }         
        #titulo { position: fixed; top: 15px; right: 0; text-align: center;  left: 0px;}
        #titulo h2 { padding:0px; margin:0px; font-size: 11px; font-weight:normal;  text-align: left; height:16px;}
        #titulo h3  { padding:0px; margin:0px; font-size: 11px; font-weight:normal;  margin-left: 15px;}        
        #fontotext { color: white; font-size: 11px; font-weight:bold; font-style: italic; margin-left: 15px;}        
        
        .contenedor { width: 100%;}
        table { border-collapse: collapse; width:1200px;}
        th { padding: 1px; border: 1px solid #fff; }
        td { border: 1px solid #ccc; text-align: center; }

        #tabla_firmantes {width: 100%; margin-top: 20px; padding:0px;}
        #tabla_firmantes td{ border: 1px solid #ccc; }
        
    </style>
@endsection

@section('header')
    <div id="titulo">
        <h3>SUBSECRETARÍA DE EDUCACIÓN MEDIA SUPERIOR</h3>
        <h3>DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO</h3>
        <div id="fontotext"><b style="background: black;">"CALENDARIZADO ANUAL PROGRAMÁTICO DEL PROGRAMA ANUAL DE TRABAJO @if($ejercicio) {{$ejercicio}} @endif "</b></div>
        <h3>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS</h3>   
        <br/> 
        <h2>DIRECCIÓN: <b>{{isset($padre) ? $padre : ''}} </b></h2>
        <h2>DEPARTAMENTO: <b>{{isset($hijo) ? $hijo : ''}} </b></h2>
    </div>
@endsection
@section('body')     
    @if(count($data)>0) 
        <div class="contenedor">
            <table>
                <thead>    
                    <tr>
                        <th rowspan="2" style="{{$hestilo}}">NÚMERO DE FUNCIÓN</th>
                        <th rowspan="2" style="{{$hestilo}} width: 145px; font-size: 8.5px; ">FUNCIONES</th>
                        <th rowspan="2" style="{{$hestilo}} width: 160px; font-size: 8.5px; ">ACTIVIDADES</th>
                        <th class="col-1" rowspan="2" style="{{$hestilo}} font-size: 8.5px;">UNIDAD<br/>DE<br/>MEDIDA</th> 
                        <th rowspan="2" style="{{$hestilo}} font-size: 8.5px;"> TIPO DE U.M.</th>
                        <th colspan="2" style="{{$hestilo}} font-size: 8.5px;">META ANUAL</th>
                            @php 
                                $m = 1;
                            @endphp
                            @foreach($data[1] as $idorg => $org )
                                <th colspan="2" style="{{$hestilo}} height: 40px;">{{$m++}}<br/> {{ $org }}</th>
                            @endforeach
                                            
                    </tr>
                    <tr>                                
                        <th style="{{$hestilo}}">PROG</th>
                        <th style="{{$hestilo}}">ALC</th>
                        @foreach($data[1] as $org )
                            <th style="{{$hestilo}}">PROG</th>
                            <th style="{{$hestilo}}">ALC</th>
                        @endforeach
                    </tr>                        
                </thead>
                @php
                    $cont = $i = 1;
                    $id_parent = null;
                @endphp
                <tbody>
                    @foreach($data[0] as $item)
                        <tr>
                        @if($id_parent <> $item->idparent)
                            @if($item->rowspan < 5)
                                <td rowspan="{{$item->rowspan}}"> <b>{{ $i++ }}</b></td>
                                <td rowspan="{{$item->rowspan}}"><b>{{ $item->funcion }}</b></td>
                            @else
                                <td rowspan="4" ><b>{{ $i++ }}</b></td>
                                <td rowspan="4">
                                    <b>{{ $item->funcion }}</b>
                                </td>
                            @endif
                            @php                            
                                $id_parent = $item->idparent;
                                $cont = 1;
                            @endphp
                        @elseif($cont >= 5)
                            <td style="border-top: 2px solid #fff;">&nbsp;</td>
                            <td style="border-top: 2px solid #fff;">&nbsp;</td>                        
                        @endif
                            <td style="text-align: left; padding:4px;">{{$cont}}.- {{ $item->procedimiento}}</td>
                            <td>{{ $item->unidadm}}</td>
                            <td>{{ $item->tipo_unidadm}}</td>
                            <td>{{ $item->programada}}</td>
                            <td>{{ $item->alcanzada}}</td>
                            @foreach($data[1] as $idorg => $org )
                                @php
                                    $prog = "prog_".$idorg;
                                    $alc = "alc_".$idorg;                                                
                                @endphp
                                <td>{{ $item->$prog}}</td>
                                <td>{{ $item->$alc}}</td>                                            
                            @endforeach
                        </tr>     
                        @php
                            $cont ++;                                
                        @endphp 
                    @endforeach    
                </tbody>
                <tfoot>
                    <tr>
                    <td colspan="{{ (count($data[1])*2)+7}}" style="border: 1px solid #fff;">
                        <table id="tabla_firmantes">
                        <tr>
                            <td><b>ELABORÓ</b></td>
                            <td rowspan="3" style="width:5%; border-top: 1px solid #fff; border-bottom: 1px solid #fff;">&nbsp;</td>
                            <td rowspan="2" style="width:20%; border-left: 2px solid #fff; width:20%; border-right: 1px solid #fff; border-top: 1px solid #fff;">&nbsp;</td>
                            <td rowspan="3" style="width:5%; border-top: 1px solid #fff; border-bottom: 1px solid #fff;">&nbsp;</td>
                            <td><b>Vo. Bo.</b></td>
                        </tr> 
                        <tr>
                            <td style="height:30px;"> @if($firmante1) {{ $firmante1->titulo }} {{ $firmante1->nombre }}@endif</td>                                                      
                            <td>@if($firmante2) {{ $firmante2->titulo }} {{ $firmante2->nombre }}@endif</td>
                        </tr>
                        <tr>
                            <td>@if($firmante1) {{ $firmante1->cargo }}@endif</td>                            
                            <td style="height:20px;"><b>FECHA: {{ date('m/d/Y')}} </b></td>                            
                            <td>@if($firmante2) {{ $firmante2->cargo }}@endif</td>
                        </tr>            
                    </table>            
                </td>        
                </tr>
            </tfoot>
           </table>
        </div>
    @endif
@endsection

@section('js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(710, 570, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }

    </script>
@endsection