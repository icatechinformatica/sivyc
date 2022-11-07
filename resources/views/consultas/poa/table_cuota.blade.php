@isset($data) 
    @php
        $costo = 0;
        $cuota[] = 0;
        $m = 0;
    @endphp    
    @foreach ($data as $i)
        @php
            if($costo == $i->costo OR $costo>='350.00'){
                
            }else{                 
                $costo = $i->costo;
                $cuota[] = $i->costo;
                $m++;
            }             
        @endphp 
    @endforeach   
    @php
        $data = array_reduce((array)$data, 'array_merge', array());   
    @endphp 
@endisset
<div>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="menu[0]" data-toggle="tab" href="#contenido[0]" role="tab" aria-controls="contenido[0]" aria-selected="true">CUOTA 0</a>            
    </li>
    @for($n=1;$n<count($cuota);$n++)
        <li class="nav-item">
            <a class="nav-link" id="menu[{$n}}]" data-toggle="tab" href="#contenido[{{$n}}]" role="tab" aria-controls="contenido[{$n}}]" aria-selected="false">{{$cuota[$n]}}</a>
        </li>        
    @endfor
</ul>       
<div class="tab-content">
    @for($n=0; $n<count($cuota);$n++) 
        @php  
            $valor = $cuota[$n];
            $dat = array_filter($data, function($v) use($valor) {
                if($valor>=350)
                    return ($v->costo >= $valor);
                else
                    return ($v->costo == $valor);
            }, ARRAY_FILTER_USE_BOTH);            
            
            $total_cursos = $total_inscritos = $total_egresados = 0;
        @endphp
        <div class="tab-pane fade  @if($n==0){{'show active'}}@endif" id="contenido[{{$n}}]" role="tabpanel" aria-labelledby="menu[{{$n}}]">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">UNIDAD/ACC.MÓVIL/ZONA</th>
                        <th class="text-center">CUOTA</th>
                        <th class="text-center">CURSOS</th>
                        <th  class="text-center">INSCRITOS</th>
                        <th class="text-center">EGRESADOS</th>
                    </tr>       
                </thead>                    
                    <tbody>                        
                        @foreach ($dat as $valor)
                            <tr>
                                <td>{{ $valor->unidad }}</td>                                
                                <td class="text-right">{{ number_format($valor->costo, 0, '', ',')}}</td>
                                <td class="text-center">{{ number_format($valor->cursos_reportados, 0, '', ',')}}</td>
                                <td class="text-center">{{ number_format($valor->inscritos, 0, '', ',')}}</td>
                                <td class="text-center">{{ number_format($valor->inscritos-$valor->desercion, 0, '', ',') }}</td>                    
                            </tr>  
                            @php
                                $total_cursos += $valor->cursos_reportados;
                                $total_inscritos += $valor->inscritos;
                                $total_egresados += ($valor->inscritos-$valor->desercion);
                            @endphp                             
                        @endforeach          
                        <tr>
                            <td><b>TOTALES</b></td>
                            <td class="text-right"></td>
                            <td class="text-center"><b>{{ number_format($total_cursos, 0, '', ',')}}</b></td>
                            <td class="text-center"><b>{{ number_format($total_inscritos, 0, '', ',')}}</b></td>
                            <td class="text-center"><b>{{ number_format($total_egresados, 0, '', ',')}}</b></td>                 
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endfor
    </div>
 </div>    