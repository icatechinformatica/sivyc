    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
        thead {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #ffffff;           
        }    
        .table-responsive {
            height:500px;
            overflow:scroll;
        }
    </style>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
    <thead class="thead-light">
        <tr>
            <th rowspan='2' class="text-center border border-dark">
                UNIDAD/ACC.MÓVIL/ZONA
            </th>
            <th colspan='6' class="text-center border border-dark">
                CURSOS
            </th>
            <th colspan='3' class="text-center border border-dark">
                HORAS
            </th>
            <th colspan='4' class="text-center border border-dark">
                COSTOS
            </th>
            <th colspan='5' class="text-center border border-dark">
                REPORTADO EN FORMATO T
            </th>
        </tr>
        <tr>
            <th class="text-center border border-dark">POA</th>
            <th class="text-center border border-dark">AUTORIZADOS</th>
            <th class="text-center border border-dark">DIFER.</td>
            <th class="text-center border border-dark">SUFIC.AUT.</th>
            <th class="text-center border border-dark">RECEP.FINAN</th>
            <th class="text-center border border-dark">VALID.FINAN</th>
            

            <th class="text-center border border-dark">PROG.ANUAL</th>
            <th class="text-center border border-dark">AUTORIZADAS</th>
            <th class="text-center border border-dark">DIFER.</th>            
            <th class="text-center border border-dark">APERTURADOS</th>
            <th class="text-center border border-dark">SUFIC.AUT.</th>
            <th class="text-center border border-dark">DIFER.</th>
            <th class="text-center border border-dark">PAGADO</th>
            <th class="text-center border border-dark">CURSOS</th>
            <th class="text-center border border-dark">INSCRITOS</th>
            <th class="text-center border border-dark">EGRESADOS</th>
            <th class="text-center border border-dark">DESERCIÓN</th>
            <th class="text-center border border-dark">HORAS</th>
        </tr>
    </thead>
        @isset($data)
        <tbody>
            @php
                $totales = $totalz2= $totalz3= ['0'=>0,'1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>0];
            @endphp
            @foreach ($data as $i)
                @if($i->orden==1)
                    <tr class="bg-dark text-light">
                    @php
                        $unidad = "UNIDAD ".$i->unidad;
                        $color = "text-warning";
                        $totales[0] += $i->cursos_programados;
                        $totales[1] += $i->cursos_autorizados;
                        $totales[2] += $i->cursos_programados-$i->cursos_autorizados;
                        $totales[3] += $i->suficiencia_autorizada;
                        $totales[4] += $i->recep_financ;
                        $totales[5] += $i->valid_financ;
                        $totales[6] += $i->horas_programadas;
                        $totales[7] += $i->horas_impartidas;
                        $totales[8] += $i->horas_programadas-$i->horas_impartidas;
                        
                        $totales[9] += $i->costo_aperturado;
                        $totales[10] += $i->costo_supre;
                        $totales[11] += $i->costo_aperturado-$i->costo_supre;
                        $totales[12] += $i->pagado;

                        $totales[13] += $i->cursos_reportados;
                        $totales[14] += $i->inscritos;
                        $totales[15] += $i->egresados;
                        $totales[16] += $i->desercion;
                        $totales[17] += $i->horas_reportadas;
                    @endphp
                @elseif($i->orden==3)
                    @php
                        $unidad = "UNIDAD ".$i->unidad;
                        $color = "text-warning";
                        $totalz3[0] += $i->cursos_programados;
                        $totalz3[1] += $i->cursos_autorizados;
                        $totalz3[2] += $i->cursos_programados-$i->cursos_autorizados;
                        $totalz3[3] += $i->suficiencia_autorizada;
                        $totalz3[4] += $i->recep_financ;
                        $totalz3[5] += $i->valid_financ;
                        $totalz3[6] += $i->horas_programadas;
                        $totalz3[7] += $i->horas_impartidas;
                        $totalz3[8] += $i->horas_programadas-$i->horas_impartidas;
                        
                        $totalz3[9] += $i->costo_aperturado;
                        $totalz3[10] += $i->costo_supre;
                        $totalz3[11] += $i->costo_aperturado-$i->costo_supre;
                        $totalz3[12] += $i->pagado;

                        $totalz3[13] += $i->cursos_reportados;
                        $totalz3[14] += $i->inscritos;
                        $totalz3[15] += $i->egresados;
                        $totalz3[16] += $i->desercion;
                        $totalz3[17] += $i->horas_reportadas;
                    @endphp
                    <tr class="bg-light">
                        @php
                            $unidad =  "ZONA ".$i->ze ;
                            $color = "text-danger";
                        @endphp
                @elseif($i->orden==20)
                    @php
                        $unidad = "UNIDAD ".$i->unidad;
                        $color = "text-warning";
                        $totalz2[0] += $i->cursos_programados;
                        $totalz2[1] += $i->cursos_autorizados;
                        $totalz2[2] += $i->cursos_programados-$i->cursos_autorizados;
                        $totalz2[3] += $i->suficiencia_autorizada;
                        $totalz2[4] += $i->recep_financ;
                        $totalz2[5] += $i->valid_financ;
                        $totalz2[6] += $i->horas_programadas;
                        $totalz2[7] += $i->horas_impartidas;
                        $totalz2[8] += $i->horas_programadas-$i->horas_impartidas;
                        
                        $totalz2[9] += $i->costo_aperturado;
                        $totalz2[10] += $i->costo_supre;
                        $totalz2[11] += $i->costo_aperturado-$i->costo_supre;
                        $totalz2[12] += $i->pagado;

                        $totalz2[13] += $i->cursos_reportados;
                        $totalz2[14] += $i->inscritos;
                        $totalz2[15] += $i->egresados;
                        $totalz2[16] += $i->desercion;
                        $totalz2[17] += $i->horas_reportadas;
                    @endphp
                    <tr class="bg-light">
                        @php
                            $unidad =  "ZONA ".$i->ze ;
                            $color = "text-danger";
                        @endphp
                @else
                    <tr>
                        @php
                            $unidad =  $i->unidad ;
                            $color = "text-danger";
                        @endphp
                @endif
                    <td>{{ $unidad }}</td>
                    <td class="text-center"> 
                        {{ number_format($i->cursos_programados, 0, '', ',') }}
                    </td>
                    <td class="text-center">{{ number_format($i->cursos_autorizados, 0, '', ',') }}</td>
                    @if($i->cursos_autorizados>$i->cursos_programados)
                        <td class="text-center {{$color}}">{{ number_format($i->cursos_programados-$i->cursos_autorizados, 0, '', ',') }}</td>
                    @else
                        <td class="text-center">{{ number_format($i->cursos_programados-$i->cursos_autorizados, 0, '', ',') }}</td>
                    @endif
                    <td class="text-center">{{ number_format($i->suficiencia_autorizada, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->recep_financ, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->valid_financ, 0, '', ',') }}</td>

                    <td class="text-center">                        
                        {{ number_format($i->horas_programadas, 0, '', ',') }}                        
                    </td>
                    <td class="text-center">{{ number_format($i->horas_impartidas, 0, '', ',') }}</td>
                    @if($i->horas_programadas-$i->horas_impartidas<0)
                        <td class="text-center {{$color}}">{{ number_format($i->horas_programadas-$i->horas_impartidas, 0, '', ',') }}</td>
                    @else
                        <td class="text-center">{{ number_format($i->horas_programadas-$i->horas_impartidas, 0, '', ',') }}</td>
                    @endif

                    <td class="text-center">{{ number_format($i->costo_aperturado, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->costo_supre, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->costo_aperturado-$i->costo_supre, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->pagado, 0, '', ',') }}</td>
                    
                    <td class="text-center">{{ number_format($i->cursos_reportados, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->inscritos, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->egresados, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->desercion, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->horas_reportadas, 0, '', ',') }}</td>
                </tr>
            @endforeach
            <tr class="bg-light">
                <td><b>TOTAL ZONA III</b></td>
                @for($n=0;$n<=17;$n++)
                    <td align="center" @if($totalz3[$n]<0) class = 'text-danger' @endif><b>{{ number_format($totalz3[$n], 0, '', ',') }}</b></td>
                @endfor
            </tr>
            <tr class="bg-light">
                <td><b>TOTAL ZONA II</b></td>
                @for($n=0;$n<=17;$n++)
                    <td align="center" @if($totalz2[$n]<0) class = 'text-danger' @endif><b>{{ number_format($totalz2[$n], 0, '', ',') }}</b></td>
                @endfor
            </tr>
            <tr>
                <td><b>TOTAL GENERAL</b></td>
                @for($n=0;$n<=17;$n++)
                    <td align="center" @if($totales[$n]<0) class = 'text-danger' @endif><b>{{ number_format($totales[$n], 0, '', ',') }}</b></td>
                @endfor
            </tr>
        </tbody>
        @endisset
    </table>
</div>