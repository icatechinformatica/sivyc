@extends('theme.sivyc.layout')

@section('title', 'Cronograma de Pagos')

@section('content_script_css')
    <style>
        .colorTop {
            background-color: #541533;
        }

    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <form id="formFechas" action="{{route('cronogramaPagos.inicio')}}" method="get">
            @csrf

            <div class="row mt-2">
                <!-- fecha inicial -->
                <div class="col">
                    <div class="form-groupp">
                        <label for="fecha_inicio" class="control-label">Fecha de Inicio</label>
                        <input type='text' id="fecha_inicio" autocomplete="off" readonly="readonly" name="fecha_inicio"
                            class="form-control datepicker" value="{{$fechaInicio}}" required>
                    </div>
                </div>

                <!-- Fecha conclusion -->
                <div class="col">
                    <div class="form-group">
                        <label for="fecha_termino" class="control-label">Fecha de Termino</label>
                        <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly" name="fecha_termino"
                            class="form-control datepicker" value="{{$fechaTermino}}" required>
                    </div>
                </div>

                <div class="col d-flex align-items-center">
                    <button type="submit" id="btnBuscarCurso" class="btn btn-primary">FILTRAR</button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col"><strong>PP</strong> - Pagos Programados</div>
            <div class="col"><strong>CB</strong> - Pagos Cargados al Banco</div>
            <div class="col"><strong>EP</strong> - Pagos Efectivamente Pagados</div>
            <div class="col"><strong>0</strong> - No Agregado</div>
        </div>

        <div class="d-none"> {{$cp = 0, $cb = 0, $ce = 0, $jp = 0, $jb = 0, $je = 0,
                $cap = 0, $cab = 0, $cae = 0, $rp = 0, $rb = 0, $re = 0,
                $tp = 0, $tb = 0, $te = 0, $sp = 0, $sb = 0, $se = 0,
                $tup = 0, $tub = 0, $tue = 0, $top = 0, $tob = 0, $toe = 0,
                $op = 0, $ob = 0, $oe = 0, $vp = 0, $vb = 0, $ve = 0,
                $yp = 0, $yb = 0, $ye = 0}}</div>
        <div class="d-none">{{$comitan = 0, $comitanBanco = 0, $comitanEfectivos = 0,  $jiquipilas = 0, $jiquipilasBanco = 0, $jiquipilasEfectivos = 0,
                $catazaja = 0, $catazajaBanco = 0, $catazajaEfectivos = 0, $reforma = 0, $reformaBanco = 0, $reformaEfectivos = 0,
                $tapachula = 0, $tapachulaBanco = 0, $tapachulaEfectivos = 0, $sanCristobal = 0, $sanCristobalBanco = 0, $sanCristobalEfectivos = 0,
                $tuxtla = 0, $tuxtlaBanco = 0, $tuxtlaEfectivos = 0,$tonala = 0, $tonalaBanco = 0, $tonalaEfectivos = 0,
                $ocosingo = 0, $ocosingoBanco = 0, $ocosingoEfectivos = 0, $villaflores = 0, $villafloresBanco = 0, $villafloresEfectivos = 0,
                $yajalon = 0, $yajalonBanco = 0, $yajalonEfectivos = 0}}</div>
        <div class="d-none">{{$totalPP = 0, $totalCB = 0, $totalEP = 0, $totTotalPP = 0, $totTotalCB = 0, $totTotalEP = 0}}</div>


        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th><small>Unidades de Capacitación</small></th>
                    @foreach ($unidades as $unidad)
                        <th class="text-center" colspan="3"><small>{{$unidad->nombre}} <br> PP - CB - EP</small></th>
                    @endforeach
                    <th class="text-center" colspan="3"><small><strong>Totales</strong> <br> PP - CB - EP</small></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grouped as $group)
                    {{-- {{$group}} <br><br> --}}
                    <tr>
                        <td><small>{{$group[0]->start}}</small></td>

                        {{-- comitan --}}
                        <div class="d-none">{{$cp = 0}}</div>
                        <div class="d-none">{{$cb = 0}}</div>
                        <div class="d-none">{{$ce = 0}}</div>
                        <div class="d-none">{{$totalPP = 0}}</div>
                        <div class="d-none">{{$totalCB = 0}}</div>
                        <div class="d-none">{{$totalEP = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 1)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$comitan += $item->title}} {{$cp = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$comitanBanco += $item->title}} {{$cb = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$comitanEfectivos += $item->title}} {{$ce = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            {{-- <div class="d-none">{{$cp = null}}</div>
                            <div class="d-none">{{$cb = null}}</div>
                            <div class="d-none">{{$ce = null}}</div>
                            @foreach ($group as $item)
                                @if ($item->id_unidad == 1)
                                    @if ($item->backgroundColor == null)
                                        <div class="d-none">{{$comitan += $item->title}} {{$cp = $item->title}}</div>
                                    @elseif ($item->backgroundColor == '#fceb30')
                                        <div class="d-none">{{$comitanBanco += $item->title}} {{$cb = $item->title}}</div>
                                    @elseif ($item->backgroundColor == '#00ff04')
                                        <div class="d-none">{{$comitanEfectivos += $item->title}} {{$ce = $item->title}}</div>
                                    @endif
                                @endif
                            @endforeach --}}
                            <small>{{ $cp }}</small>
                        </td>
                        <td>
                            <small>{{ $cb }}</small>
                        </td>
                        <td>
                            <small> {{ $ce }}</small>
                        </td>

                        {{-- jiquipilas --}}
                        <div class="d-none">{{$jp = 0}}</div>
                        <div class="d-none">{{$jb = 0}}</div>
                        <div class="d-none">{{$je = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 2)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$jiquipilas += $item->title}} {{$jp = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$jiquipilasBanco += $item->title}} {{$jb = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$jiquipilasEfectivos += $item->title}} {{$je = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            {{-- <small>{{$jp != null ? $jp : '0'}} - {{$jb != null ? $jb : '0'}} - {{$je != null ? $je : '0'}}</small> --}}
                            <small>{{ $jp }}</small>
                        </td>
                        <td>
                            <small>{{ $jb }}</small>
                        </td>
                        <td>
                            <small>{{ $je }}</small>
                        </td>
                        {{-- catazaja --}}
                        <div class="d-none">{{$cap = 0}}</div>
                        <div class="d-none">{{$cab = 0}}</div>
                        <div class="d-none">{{$cae = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 3)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$catazaja += $item->title}} {{$cap = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$catazajaBanco += $item->title}} {{$cab = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$catazajaEfectivos += $item->title}} {{$cae = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{ $cap }}</small>
                        </td>
                        <td>
                            <small>{{ $cab }}</small>
                        </td>
                        <td>
                            <small>{{ $cae }}</small>
                        </td>
                        {{-- reforma --}}
                        <div class="d-none">{{$rp = 0}}</div>
                        <div class="d-none">{{$rb = 0}}</div>
                        <div class="d-none">{{$re = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 4)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$reforma += $item->title}} {{$rp = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$reformaBanco += $item->title}} {{$rb = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$reformaEfectivos += $item->title}} {{$re = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{ $rp }}</small>
                        </td>
                        <td>
                            <small>{{ $rb }}</small>
                        </td>
                        <td>
                            <small>{{ $re }}</small>
                        </td>
                        {{-- tapachula --}}
                        <div class="d-none">{{$tp = 0}}</div>
                        <div class="d-none">{{$tb = 0}}</div>
                        <div class="d-none">{{$te = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 5)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$tapachula += $item->title}} {{$tp = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$tapachulaBanco += $item->title}} {{$tb = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$tapachulaEfectivos += $item->title}} {{$te = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{ $tp }}</small>
                        </td>
                        <td>
                            <small>{{ $tb }}</small>
                        </td>
                        <td>
                            <small>{{ $te }}</small>
                        </td>
                        {{-- san cristobal --}}
                        <div class="d-none">{{$sp = 0}}</div>
                        <div class="d-none">{{$sb = 0}}</div>
                        <div class="d-none">{{$se = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 6)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$sanCristobal += $item->title}} {{$sp = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$sanCristobalBanco += $item->title}} {{$sb = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$sanCristobalEfectivos += $item->title}} {{$se = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{ $sp }}</small>
                        </td>
                        <td>
                            <small>{{ $sb }}</small>
                        </td>
                        <td>
                            <small>{{ $se }}</small>
                        </td>
                        {{-- tuxtla --}}
                        <div class="d-none">{{$tup = 0}}</div>
                        <div class="d-none">{{$tub = 0}}</div>
                        <div class="d-none">{{$tue = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 7)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$tuxtla += $item->title}} {{$tup = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$tuxtlaBanco += $item->title}} {{$tub = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$tuxtlaEfectivos += $item->title}} {{$tue = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{ $tup }}</small>
                        </td>
                        <td>
                            <small>{{ $tub }}</small>
                        </td>
                        <td>
                            <small>{{ $tue }}</small>
                        </td>
                        {{-- tonala --}}
                        <div class="d-none">{{$top = 0}}</div>
                        <div class="d-none">{{$tob = 0}}</div>
                        <div class="d-none">{{$toe = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 8)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$tonala += $item->title}} {{$top = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$tonalaBanco += $item->title}} {{$tob = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$tonalaEfectivos += $item->title}} {{$toe = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{ $top }}</small>
                        </td>
                        <td>
                            <small>{{ $tob }}</small>
                        </td>
                        <td>
                            <small>{{ $toe }}</small>
                        </td>
                        {{-- ocosingo --}}
                        <div class="d-none">{{$op = 0}}</div>
                        <div class="d-none">{{$ob = 0}}</div>
                        <div class="d-none">{{$oe = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 9)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$ocosingo += $item->title}} {{$op = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$ocosingoBanco += $item->title}} {{$ob = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$ocosingoEfectivos += $item->title}} {{$oe = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{$op }}</small>
                        </td>
                        <td>
                            <small>{{$ob }}</small>
                        </td>
                        <td>
                            <small>{{$oe }}</small>
                        </td>
                        {{-- villaflores --}}
                        <div class="d-none">{{$vp = 0}}</div>
                        <div class="d-none">{{$vb = 0}}</div>
                        <div class="d-none">{{$ve = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 10)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$villaflores += $item->title}} {{$vp = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$villafloresBanco += $item->title}} {{$vb = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$villafloresEfectivos += $item->title}} {{$ve = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{$vp }}</small>
                        </td>
                        <td>
                            <small>{{$vb }}</small>
                        </td>
                        <td>
                            <small>{{$ve }}</small>
                        </td>
                        {{-- yajalon --}}
                        <div class="d-none">{{$yp = 0}}</div>
                        <div class="d-none">{{$yb = 0}}</div>
                        <div class="d-none">{{$ye = 0}}</div>
                        @foreach ($group as $item)
                            @if ($item->id_unidad == 11)
                                @if ($item->backgroundColor == null)
                                    <div class="d-none">{{$yajalon += $item->title}} {{$yp = $item->title}} {{$totalPP += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#fceb30')
                                    <div class="d-none">{{$yajalonBanco += $item->title}} {{$yb = $item->title}} {{$totalCB += $item->title}}</div>
                                @elseif ($item->backgroundColor == '#00ff04')
                                    <div class="d-none">{{$yajalonEfectivos += $item->title}} {{$ye = $item->title}} {{$totalEP += $item->title}}</div>
                                @endif
                            @endif
                        @endforeach
                        <td>
                            <small>{{$yp }}</small>
                        </td>
                        <td>
                            <small>{{$yb }}</small>
                        </td>
                        <td>
                            <small>{{$ye }}</small>
                        </td>
                        <td><small><strong>{{$totalPP}}</strong></small></td>
                        <td><small><strong>{{$totalCB}}</strong></small></td>
                        <td><small><strong>{{$totalEP}}</strong></small></td>
                        <div class="d-none">{{$totTotalPP += $totalPP}}</div>
                        <div class="d-none">{{$totTotalCB += $totalCB}}</div>
                        <div class="d-none">{{$totTotalEP += $totalEP}}</div>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Totales</strong></td>
                    <td><small><strong>{{$comitan}}</strong></small></td>
                    <td><small><strong>{{$comitanBanco}}</strong></small></td>
                    <td><small><strong>{{$comitanEfectivos}}</strong></small></td>
                    <td><small><strong>{{$jiquipilas}}</strong></small></td>
                    <td><small><strong>{{$jiquipilasBanco}}</strong></small></td>
                    <td><small><strong>{{$jiquipilasEfectivos}}</strong></small></td>
                    {{-- <td><small><strong>{{$catazaja}} - {{$catazajaBanco}} - {{$catazajaEfectivos}}</strong></small></td> --}}
                    <td><small><strong>{{$catazaja}}</strong></small></td>
                    <td><small><strong>{{$catazajaBanco}}</strong></small></td>
                    <td><small><strong>{{$catazajaEfectivos}}</strong></small></td>
                    <td><small><strong>{{$reforma}}</strong></small></td>
                    <td><small><strong>{{$reformaBanco}}</strong></small></td>
                    <td><small><strong>{{$reformaEfectivos}}</strong></small></td>
                    <td><small><strong>{{$tapachula}}</strong></small></td>
                    <td><small><strong>{{$tapachulaBanco}}</strong></small></td>
                    <td><small><strong>{{$tapachulaEfectivos}}</strong></small></td>
                    <td><small><strong>{{$sanCristobal}}</strong></small></td>
                    <td><small><strong>{{$sanCristobalBanco}}</strong></small></td>
                    <td><small><strong>{{$sanCristobalEfectivos}}</strong></small></td>
                    <td><small><strong>{{$tuxtla}}</strong></small></td>
                    <td><small><strong>{{$tuxtlaBanco}}</strong></small></td>
                    <td><small><strong>{{$tuxtlaEfectivos}}</strong></small></td>
                    <td><small><strong>{{$tonala}}</strong></small></td>
                    <td><small><strong>{{$tonalaBanco}}</strong></small></td>
                    <td><small><strong>{{$tonalaEfectivos}}</strong></small></td>
                    <td><small><strong>{{$ocosingo}}</strong></small></td>
                    <td><small><strong>{{$ocosingoBanco}}</strong></small></td>
                    <td><small><strong>{{$ocosingoEfectivos}}</strong></small></td>
                    <td><small><strong>{{$villaflores}}</strong></small></td>
                    <td><small><strong>{{$villafloresBanco}}</strong></small></td>
                    <td><small><strong>{{$villafloresEfectivos}}</strong></small></td>
                    <td><small><strong>{{$yajalon}}</strong></small></td>
                    <td><small><strong>{{$yajalonBanco}}</strong></small></td>
                    <td><small><strong>{{$yajalonEfectivos}}</strong></small></td>
                    <td><small><strong class="text-success">{{$totTotalPP}}</strong></small></td>
                    <td><small><strong class="text-success">{{$totTotalCB}}</strong></small></td>
                    <td><small><strong class="text-success">{{$totTotalEP}}</strong></small></td>
                </tr>
            </tbody>
        </table>

        <div class="row d-flex justify-content-end">
            <button id="btnReporteCronograma" type="button"
                class="btn btn-info mb-4">Generar Reporte</button>
            <form target="_blank" id="formReporteCronograma" action="{{ route('cronogramaPagos.reporte') }}" method="get">@csrf</form>
        </div>

        <button id="btnModal" type="button" class="d-none" data-toggle="modal" data-target="#modalMessages"></button>

        <!-- Modal -->
        <div class="modal fade" id="modalMessages" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background: #541533">
                        <h5 class="modal-title text-white" id="titulo">cj</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong id="mensaje"></strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // formato fechas
        var dateFormat = "yy-mm-dd",
            from = $("#fecha_inicio").datepicker({
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: 'yy-mm-dd'
            }).on("change", function() {
                to.datepicker("option", "minDate", getDate(this));
            }),
            to = $("#fecha_termino").datepicker({
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: 'yy-mm-dd'
            })
            .on("change", function() {
                // from.datepicker("option", "maxDate", getDate(this));
            });

        function getDate(element) {
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }
            return date;
        }

        $('#formFechas').validate({
            rules: {
                fecha_inicio: {
                    required: true
                },
                fecha_termino: {
                    required: true
                }
            },
            messages: {
                fecha_inicio: {
                    required: 'Campo requerido'
                },
                fecha_termino: {
                    required: 'Campo requerido'
                }
            }
        });

        $('#btnReporteCronograma').click(function (){
            if ($('#fecha_inicio').val() == '' || $('#fecha_termino').val() == '') {
                $('#titulo').html('Generar Reporte');
                $('#mensaje').html('Debe realizar una busqueda antes de generar un reporte');
                $('#btnModal').click();
            } else {
                $('#formReporteCronograma').submit();
            }
        });
    </script>
@endsection
