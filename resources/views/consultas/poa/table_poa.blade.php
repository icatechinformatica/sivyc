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
            <th rowspan='2' class="text-center">
                UNIDAD/ACC.MÓVIL/ZONA
            </th>
            <th colspan='4' class="text-center">
                CURSOS
            </th>
            <th colspan='3' class="text-center">
                HORAS
            </th>
            <th colspan='5' class="text-center">
                REPORTADO EN FORMATO T
            </th>
        </tr>
        <tr>
            <th>PROG.ANUAL</th>
            <th>AUTORIZADOS</th>
            <th>DIFER.</td>
            <th>SUFIC.AUT.</th>
            <th>PROG.ANUAL</th>
            <th>AUTORIZADAS</th>
            <th>DIFER.</th>
            <th>CURSOS</th>
            <th>INSCRITOS</th>
            <th>EGRESADOS</th>
            <th>DESERCIÓN</th>
            <th>HORAS</th>
        </tr>
    </thead>
        @isset($data)
        <tbody>
            @php
                $totales = ['0'=>0,'1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0];
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
                        $totales[4] += $i->horas_programadas;
                        $totales[5] += $i->horas_impartidas;
                        $totales[6] += $i->horas_programadas-$i->horas_impartidas;
                        $totales[7] += $i->cursos_reportados;
                        $totales[8] += $i->inscritos;
                        $totales[9] += $i->egresados;
                        $totales[10] += $i->desercion;
                        $totales[11] += $i->horas_reportadas;
                    @endphp
                @elseif($i->orden==2)
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
                    <td class="text-center">
                        @if($i->ze == $i->poa_ze)
                        {{ number_format($i->horas_programadas, 0, '', ',') }}
                        @else
                            {{ '0' }}
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($i->horas_impartidas, 0, '', ',') }}</td>
                    @if($i->horas_programadas-$i->horas_impartidas<0)
                        <td class="text-center {{$color}}">{{ number_format($i->horas_programadas-$i->horas_impartidas, 0, '', ',') }}</td>
                    @else
                        <td class="text-center">{{ number_format($i->horas_programadas-$i->horas_impartidas, 0, '', ',') }}</td>
                    @endif
                    <td class="text-center">{{ number_format($i->cursos_reportados, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->inscritos, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->egresados, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->desercion, 0, '', ',') }}</td>
                    <td class="text-center">{{ number_format($i->horas_reportadas, 0, '', ',') }}</td>
                </tr>
            @endforeach
            <tr>
                <td><b>TOTALES</b></td>
                @for($n=0;$n<=11;$n++)
                    <td align="center"><b>{{ number_format($totales[$n], 0, '', ',') }}</b></td>
                @endfor
            </tr>
        </tbody>
        @endisset
    </table>
</div>
