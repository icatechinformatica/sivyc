
<!--Creado por Romelia Pérez Nangüelú--rpnanguelu@gmail.com -->
@extends('theme.global.layout')

@section('title', 'METAS ANUALES | Sivyc Icatech')
@section('css_content')
    <link rel="stylesheet" href="{{ asset('css/tablero/metas.css') }}" />
@endsection
@section('content')

    @if ($message = Session::get('success'))
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link alert-light active" id="nav-cursos-tab" data-toggle="tab" href="#nav-cursos"
                role="tab" aria-controls="nav-cursos" aria-selected="true">CURSOS</a>
            <a class="nav-item nav-link alert-light" id="nav-horas-tab" data-toggle="tab" href="#nav-horas" role="tab"
                aria-controls="nav-horas" aria-selected="false">HORAS</a>
            <a class="nav-item nav-link alert-light" id="nav-beneficiarios-tab" data-toggle="tab" href="#nav-beneficiarios"
                role="tab" aria-controls="nav-beneficiarios" aria-selected="false">BENEFICIARIOS</a>
            <a class="nav-item nav-link alert-light" id="nav-inversion-tab" data-toggle="tab" href="#nav-inversion"
                role="tab" aria-controls="nav-inversion" aria-selected="false">INVERSI&Oacute;N</a>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-cursos" role="tabpanel" aria-labelledby="nav-cursos-tab">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <h2 class="titulo">Metas Anuales de Cursos Aperturados</h2>
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <form class="form-inline" action="{{ route('tablero.metas.index') }}" method="post" id="frm" name="frm">
                                        @csrf

                                        {{-- {!! Form::open(['route' => 'tablero.metas.index', 'method'=> 'POST', 'role'=> 'search', 'class' => 'form-inline', 'id'=>'frm', 'name'=>'frm', 'enctype'=>'multipart/form-data' ]) !!}
                                            {{ Form::label('anio', 'AÑO DE EJERCICIO: ') }}
                                            {{ Form::select('ejercicio', $lst_ejercicio,$ejercicio ,array('id'=>'ejercicio','class' => 'form-control  mr-sm-2')) }}
                                            {{ Form::button('FILTRAR', array('class' => 'btn btn-outline-info my-1 my-sm-0', 'type' => 'button', 'id' => 'filtrar' )) }}
                                        {!! Form::close() !!} --}}

                                        <div class="input-group">
                                            <label for="ejercicio" class="px-2">AÑO DE EJERCICIO:</label>
                                            <select style="width: 250px" class="custom-select" id="ejercicio" name="ejercicio">
                                                <option {{ $ejercicio === '2020' ? 'selected' : '' }} >2020</option>
                                                <option {{ $ejercicio === '2021' ? 'selected' : '' }}>2021</option>
                                                <option {{ $ejercicio === '2022' ? 'selected' : '' }}>2022</option>
                                                <option {{ $ejercicio === '2023' ? 'selected' : '' }}>2023</option>
                                                <option {{ $ejercicio === '2024' ? 'selected' : '' }}>2024</option>
                                                <option {{ $ejercicio === '2025' ? 'selected' : '' }}>2025</option>
                                                <option {{ $ejercicio === '2026' ? 'selected' : '' }}>2026</option>
                                                <option {{ $ejercicio === '2027' ? 'selected' : '' }}>2027</option>
                                                <option {{ $ejercicio === '2028' ? 'selected' : '' }}>2028</option>
                                                <option {{ $ejercicio === '2029' ? 'selected' : '' }}>2029</option>
                                                <option {{ $ejercicio === '2030' ? 'selected' : '' }}>2030</option>
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-info my-1 my-sm-0" type="submit" id="filtrar">Filtrar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" style="min-height:420px" id="tabla">
                            <table class="table ">
                                <thead style="background-color: #ccc9c9;">
                                    <tr>
                                        <th scope="col" rowspan="2" class="text-dark"
                                            style="background-color: #ccc9c9; vertical-align: middle">#</th>
                                        <th scope="col" rowspan="2" class="text-left text-dark"
                                            style="background-color: #ccc9c9;  vertical-align: middle">UNIDAD DE
                                            CAPACITACI&Oacute;N</th>
                                        <th scope="col" colspan="2" class="text-center">ENE</th>
                                        <th scope="col" colspan="2" class="text-center">FEB</th>
                                        <th scope="col" colspan="2" class="text-center">MAR</th>
                                        <th scope="col" colspan="2" class="text-center">ABR</th>
                                        <th scope="col" colspan="2" class="text-center">MAY</th>
                                        <th scope="col" colspan="2" class="text-center">JUN</th>
                                        <th scope="col" colspan="2" class="text-center">JUL</th>
                                        <th scope="col" colspan="2" class="text-center">AGO</th>
                                        <th scope="col" colspan="2" class="text-center">SEPT</th>
                                        <th scope="col" colspan="2" class="text-center">OCT</th>
                                        <th scope="col" colspan="2" class="text-center">NOV</th>
                                        <th scope="col" colspan="2" class="text-center">DIC</th>
                                        <th scope="col" rowspan="2" class="text-dark" style=" vertical-align: middle">
                                            TOTAL<br />CURSOS </th>
                                        <th scope="col" rowspan="2" class="text-dark" style=" vertical-align: middle">
                                            GRAF</th>
                                    </tr>
                                    <tr>
                                        @foreach ($lst_field as $m)
                                            <th class="text-dark" style="background-color: gray;">P</th>
                                            <th class="text-dark" style="background-color: #ccc9c9;">A</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php
                                    $i = 1;
                                    $n = 0;
                                    $total_cursos = $total_beneficiarios = $total_horas = 0;
                                    ?>
                                    @foreach ($data as $items)
                                        <?php
                                        $total_cursos += $items['total_cursos'];
                                        ?>
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td class="text-left">{{ $items['unidad'] }}</td>
                                            @foreach ($lst_field as $k => $m)
                                                <td class=" bg-light">{{ number_format($items[$m], 0, '', ',') }}</td>
                                                <td>{{ number_format($items[$m . '_r'], 0, '', ',') }} </td>
                                            @endforeach
                                            <td> {{ number_format($items['total_cursos'], 0, '', ',') }}</td>
                                            <td>
                                                <a href="#"
                                                    onclick="generarGRAF({{ $n++ }},'cursos','{{ $items['unidad'] }}.- CURSOS APERTURADOS' )"
                                                    class="fa fa-cube text-blue" data-toggle="modal"
                                                    data-target="#textGRAF">
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="text-dark">
                                        <td></td>
                                        <td class="text-left">ICATECH</td>
                                        @foreach ($lst_field as $k => $m)
                                            <td class=" bg-light"> {{ number_format($dataT[$m], 0, '', ',') }} </td>
                                            <td>{{ $dataT[$m . '_r'] }}</td>
                                        @endforeach
                                        <td> {{ number_format($total_cursos, 0, '', ',') }}</td>
                                        <td>
                                            <a href="#" onclick="generarGRAF(11, 'cursos', 'ICATECH.- CURSOS APERTURADOS')"
                                                class="fa fa-cube text-blue" data-toggle='modal'
                                                data-target='#textGRAF'></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br /><br />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="nav-horas" role="tabpanel" aria-labelledby="nav-horas-tab">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <br />
                            <h2 class="titulo">Metas Anuales en Horas</h2>
                        </div>
                        <div class="table-responsive" style="min-height:420px" id="tabla">
                            <table class="table ">
                                <thead style="background-color: #ccc9c9;">
                                    <tr>
                                        <th scope="col" rowspan="2" class="text-dark"
                                            style="background-color: #ccc9c9; vertical-align: middle">#</th>
                                        <th scope="col" rowspan="2" class="text-left text-dark"
                                            style="background-color: #ccc9c9;  vertical-align: middle">UNIDAD DE
                                            CAPACITACI&Oacute;N</th>
                                        <th scope="col" colspan="2" class="text-center">ENE</th>
                                        <th scope="col" colspan="2" class="text-center">FEB</th>
                                        <th scope="col" colspan="2" class="text-center">MAR</th>
                                        <th scope="col" colspan="2" class="text-center">ABR</th>
                                        <th scope="col" colspan="2" class="text-center">MAY</th>
                                        <th scope="col" colspan="2" class="text-center">JUN</th>
                                        <th scope="col" colspan="2" class="text-center">JUL</th>
                                        <th scope="col" colspan="2" class="text-center">AGO</th>
                                        <th scope="col" colspan="2" class="text-center">SEPT</th>
                                        <th scope="col" colspan="2" class="text-center">OCT</th>
                                        <th scope="col" colspan="2" class="text-center">NOV</th>
                                        <th scope="col" colspan="2" class="text-center">DIC</th>
                                        <th scope="col" rowspan="2" class="text-dark" style=" vertical-align: middle">
                                            TOTAL<br />HORAS</th>
                                        <th scope="col" rowspan="2" class="text-dark" style=" vertical-align: middle">
                                            GRAF</th>
                                    </tr>
                                    <tr>
                                        @foreach ($lst_field as $m)
                                            <th class="text-dark" style="background-color: gray;">P</th>
                                            <th class="text-dark" style="background-color: #ccc9c9;">A</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php
                                    $i = 1;
                                    $n = 0;
                                    $total_cursos = $total_beneficiarios = $total_horas = 0;
                                    ?>
                                    @foreach ($data as $items)
                                        <?php
                                        $total_horas += $items['total_horas'];
                                        
                                        ?>
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td class="text-left">{{ $items['unidad'] }}</td>
                                            @foreach ($lst_field as $k => $m)
                                                <td class=" bg-light">{{ number_format($items['hr_' . $m], 0, '', ',') }}</td>
                                                <td>{{ number_format($items['hr_' . $m . '_r'], 0, '', ',') }} </td>
                                            @endforeach
                                            <td>{{ number_format($items['total_horas'], 0, '', ',') }}</td>
                                            <td>
                                                <a href="#"
                                                    onclick='generarGRAF({{ $n++ }}, "hr" ,"{{ $items['unidad'] }}.- CURSOS APERTURADOS EN HORAS")'
                                                    class="fa fa-cube text-blue" data-toggle="modal"
                                                    data-target="#textGRAF">
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                    <tr class="text-dark">
                                        <td></td>
                                        <td class="text-left">ICATECH</td>
                                        @foreach ($lst_field as $k => $m)
                                            <td class=" bg-light"> {{ number_format($dataT['hr_' . $m], 0, '', ',') }}</td>
                                            <td>{{ number_format($dataT['hr_' . $m . '_r'], 0, '', ',') }}</td>
                                        @endforeach
                                        <td>{{ number_format($total_horas, 0, '', ',') }}</td>
                                        <td>
                                            <a href="#"
                                                onclick='generarGRAF(11,"hr","ICATECH.- CURSOS APERTURADOS EN HORAS")'
                                                class="fa fa-cube text-blue" data-toggle='modal'
                                                data-target='#textGRAF'></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br /><br />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="nav-beneficiarios" role="tabpanel" aria-labelledby="nav-beneficiarios-tab">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <br />
                            <h2 class="titulo">Metas Anuales de Beneficiarios</h2>
                        </div>
                        <div class="table-responsive" style="min-height:420px" id="tabla">
                            <table class="table ">
                                <thead style="background-color: #ccc9c9;">
                                    <tr>
                                        <th scope="col" rowspan="2" class="text-dark"
                                            style="background-color: #ccc9c9; vertical-align: middle">#</th>
                                        <th scope="col" rowspan="2" class="text-left text-dark"
                                            style="background-color: #ccc9c9;  vertical-align: middle">UNIDAD DE
                                            CAPACITACI&Oacute;N</th>
                                        <th scope="col" colspan="2" class="text-center">ENE</th>
                                        <th scope="col" colspan="2" class="text-center">FEB</th>
                                        <th scope="col" colspan="2" class="text-center">MAR</th>
                                        <th scope="col" colspan="2" class="text-center">ABR</th>
                                        <th scope="col" colspan="2" class="text-center">MAY</th>
                                        <th scope="col" colspan="2" class="text-center">JUN</th>
                                        <th scope="col" colspan="2" class="text-center">JUL</th>
                                        <th scope="col" colspan="2" class="text-center">AGO</th>
                                        <th scope="col" colspan="2" class="text-center">SEPT</th>
                                        <th scope="col" colspan="2" class="text-center">OCT</th>
                                        <th scope="col" colspan="2" class="text-center">NOV</th>
                                        <th scope="col" colspan="2" class="text-center">DIC</th>
                                        <th scope="col" rowspan="2" class="text-dark" style=" vertical-align: middle">
                                            TOTAL<br />BENEF </th>
                                        <th scope="col" rowspan="2" class="text-dark" style=" vertical-align: middle">
                                            GRAF</th>
                                    </tr>
                                    <tr>
                                        @foreach ($lst_field as $m)
                                            <th class="text-dark" style="background-color: gray;">P</th>
                                            <th class="text-dark" style="background-color: #ccc9c9;">A</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php
                                    $i = 1;
                                    $n = 0;
                                    $total_cursos = $total_beneficiarios = $total_horas = 0;
                                    ?>
                                    @foreach ($data as $items)
                                        <?php
                                        $total_beneficiarios += $items['total_beneficiarios'];
                                        ?>
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td class="text-left">{{ $items['unidad'] }}</td>
                                            @foreach ($lst_field as $k => $m)
                                                <td class=" bg-light">{{ number_format($items['benef_' . $m], 0, '', ',') }}</td>
                                                <td>{{ number_format($items['benef_' . $m . '_r'], 0, '', ',') }} </td>
                                            @endforeach
                                            <td>{{ number_format($items['total_beneficiarios'], 0, '', ',') }}</td>
                                            <td>
                                                <a href="#"
                                                    onclick='generarGRAF({{ $n++ }},"benef","{{ $items['unidad'] }}.- BENEFICIARIOS DE CURSOS APERTURADOS")'
                                                    class="fa fa-cube text-blue" data-toggle="modal"
                                                    data-target="#textGRAF">
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="text-dark">
                                        <td></td>
                                        <td class="text-left">ICATECH</td>
                                        @foreach ($lst_field as $k => $m)
                                            <td class=" bg-light">{{ number_format($dataT['benef_' . $m], 0, '', ',') }} </td>
                                            <td>{{ number_format($dataT['benef_' . $m . '_r'], 0, '', ',') }}</td>
                                        @endforeach
                                        <td>{{ number_format($total_beneficiarios, 0, '', ',') }}</td>
                                        <td>
                                            <a href="#"
                                                onclick='generarGRAF(11,"benef","ICATECH.- BENEFICIARIOS DE CURSOS APERTURADOS")'
                                                class="fa fa-cube text-blue" data-toggle='modal'
                                                data-target='#textGRAF'></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br /><br />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="nav-inversion" role="tabpanel" aria-labelledby="nav-inversion-tab">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <br />
                            <h2 class="titulo">Inversi&oacute;n en Cursos Aperturados</h2>
                        </div>
                        <div class="table-responsive" style="min-height:420px" id="tabla">
                            <table class="table ">
                                <thead class="text-dark" style="background-color: gray;">
                                    <tr>
                                        <th scope="col" class="bg-light">#</th>
                                        <th scope="col" class="text-left bg-light">UNIDAD DE CAPACITACI&Oacute;N</th>
                                        <th scope="col" class="text-center">ENE</th>
                                        <th scope="col" class="text-center bg-light">FEB</th>
                                        <th scope="col" class="text-center">MAR</th>
                                        <th scope="col" class="text-center bg-light">ABR</th>
                                        <th scope="col" class="text-center">MAY</th>
                                        <th scope="col" class="text-center bg-light">JUN</th>
                                        <th scope="col" class="text-center">JUL</th>
                                        <th scope="col" class="text-center bg-light">AGO</th>
                                        <th scope="col" class="text-center">SEPT</th>
                                        <th scope="col" class="text-center bg-light">OCT</th>
                                        <th scope="col" class="text-center">NOV</th>
                                        <th scope="col" class="text-center bg-light">DIC</th>
                                        <th scope="col">TOTAL</th>
                                        <th scope="col" class="bg-light">GRAF</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php
                                    $i = 1;
                                    $s = $t_inversion = 0;
                                    ?>
                                    @foreach ($data as $items)
                                        <?php
                                        $total_inversion = 0;
                                        ?>
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td class="text-left">{{ $items['unidad'] }}</td>
                                            @foreach ($lst_field as $k => $m)
                                                <?php
                                                if ($s % 2 == 0) {
                                                    $color = 'class="bg-light"';
                                                } else {
                                                    $color = '';
                                                }
                                                ?>
                                                <td <?php echo $color; ?>>
                                                    {{ number_format($items['inversion_' . $m], 0, '', ',') }} </td>
                                                <?php
                                                $total_inversion += $items['inversion_' . $m];
                                                $s++;
                                                ?>
                                            @endforeach
                                            <td class="bg-light">{{ number_format($total_inversion, 0, '', ',') }}
                                            </td>
                                            <td>
                                                <a href="#"
                                                    onclick='generaGRAF_LINE({{ $n++ }},"{{ $items['unidad'] }}.- INVERSIÓN DE CURSOS PAGADOS")'
                                                    class="fa fa-cube text-blue" data-toggle="modal"
                                                    data-target="#textGRAF">
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="text-dark">
                                        <td></td>
                                        <td class="text-left">ICATECH</td>
                                        <?php $s = 0; ?>
                                        @foreach ($lst_field as $k => $m)
                                            <?php
                                            if ($s % 2 == 0) {
                                                $color = 'class="bg-light"';
                                            } else {
                                                $color = '';
                                            }
                                            ?>
                                            <td <?php echo $color; ?>>
                                                {{ number_format($dataT['inversion_' . $m], 0, '', ',') }}</td>
                                            <?php
                                            $t_inversion += $dataT['inversion_' . $m];
                                            $s++;
                                            ?>
                                        @endforeach
                                        <td class="bg-light">{{ number_format($t_inversion, 0, '', ',') }}</td>
                                        <td>
                                            <a href="#"
                                                onclick='generaGRAF_LINE(11,"ICATECH.- INVERSIÓN DE CURSOS PAGADOS")'
                                                class="fa fa-cube text-blue" data-toggle='modal'
                                                data-target='#textGRAF'></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br /><br />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div id="textGRAF" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog"
            aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <canvas id="myChart" width="600" height="300"></canvas>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">CERRAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts_content')
    <script src="{{ asset('js/tablero/Chart.min.js') }}"></script>

    <script type="text/javascript">
        function generarGRAF(opt, tipo, title) {
            var ctx = document.getElementById('myChart').getContext('2d');

            $("#title").text(title);
            if (opt == 11) {
                var d = <?php echo json_encode($dataT); ?>;
            } else {
                var dat = <?php echo json_encode($data); ?>;
                var d = dat[opt];
            }
            if (tipo == "benef") {
                var dataP = [d['benef_ene'], d['benef_feb'], d['benef_mar'], d['benef_abr'], d['benef_may'], d['benef_jun'],
                    d['benef_jul'], d['benef_ago'], d['benef_sep'], d['benef_oct'], d['benef_nov'], d['benef_dic']
                ];
                var dataR = [d['benef_ene_r'], d['benef_feb_r'], d['benef_mar_r'], d['benef_abr_r'], d['benef_may_r'], d[
                    'benef_jun_r'], d['benef_jul_r'], d['benef_ago_r'], d['benef_sep_r'], d['benef_oct_r'], d[
                    'benef_nov_r'], d['benef_dic_r']];
            } else if (tipo == "hr") {
                var dataP = [d['hr_ene'], d['hr_feb'], d['hr_mar'], d['hr_abr'], d['hr_may'], d['hr_jun'], d['hr_jul'], d[
                    'hr_ago'], d['hr_sep'], d['hr_oct'], d['hr_nov'], d['hr_dic']];
                var dataR = [d['hr_ene_r'], d['hr_feb_r'], d['hr_mar_r'], d['hr_abr_r'], d['hr_may_r'], d['hr_jun_r'], d[
                    'hr_jul_r'], d['hr_ago_r'], d['hr_sep_r'], d['hr_oct_r'], d['hr_nov_r'], d['hr_dic_r']];
            } else {
                var dataP = [d['ene'], d['feb'], d['mar'], d['abr'], d['may'], d['jun'], d['jul'], d['ago'], d['sep'], d[
                    'oct'], d['nov'], d['dic']];
                var dataR = [d['ene_r'], d['feb_r'], d['mar_r'], d['abr_r'], d['may_r'], d['jun_r'], d['jul_r'], d['ago_r'],
                    d['sep_r'], d['oct_r'], d['nov_r'], d['dic_r']
                ];
            }

            var data = {
                labels: ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'],
                datasets: [{
                    label: '# PROGRAMADOS',
                    data: dataP,
                    backgroundColor: "rgba(0, 100, 0, 0.7)",
                    borderColor: "rgba(0, 100, 0, 1)",
                    borderWidth: 1,
                    hoverBackgroundColor: "rgba(0, 100, 0, 0.7)"
                }, {
                    label: '# APERTURADOS',
                    data: dataR,
                    backgroundColor: "rgba(218, 165, 32, 0.7)",
                    borderColor: "rgba(218, 165, 32, 1)",
                    borderWidth: 1,
                    hoverBackgroundColor: "rgba(218, 165, 32, 0.7)"
                }]
            }
            if (Math.max(...data.datasets[0].data) > Math.max(...data.datasets[1].data)) {
                var BarMax = Math.max(...data.datasets[0].data);
            } else {
                var BarMax = Math.max(...data.datasets[1].data);
            }

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    "hover": {
                        "animationDuration": 0
                    },
                    "animation": {
                        "duration": 1,
                        "onComplete": function() {
                            var chartInstance = this.chart,
                                ctx = chartInstance.ctx;

                            ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart
                                .defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';

                            this.data.datasets.forEach(function(dataset, i) {
                                var meta = chartInstance.controller.getDatasetMeta(i);
                                meta.data.forEach(function(bar, index) {
                                    var data = dataset.data[index];
                                    ctx.fillText(data, bar._model.x, bar._model.y);
                                });
                            });

                        }
                    },
                    legend: {
                        "display": true
                    },
                    tooltips: {
                        "enabled": false
                    },
                    scales: {
                        yAxes: [{
                            display: true,
                            gridLines: {
                                display: true
                            },
                            ticks: {
                                max: Math.max(BarMax + Math.round(BarMax * .12)),
                                display: true,
                                beginAtZero: false,
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: true
                            },
                            ticks: {
                                beginAtZero: false
                            }
                        }]
                    }
                }
            });
        }

        function generaGRAF_LINE(opt, title) {
            var speedCanvas = document.getElementById("myChart");
            $("#title").text(title);
            if (opt == 11) {
                var d = <?php echo json_encode($dataT); ?>;
            } else {
                var dat = <?php echo json_encode($data); ?>;
                var d = dat[opt];
            }
            var dataR = [d['inversion_ene'], d['inversion_feb'], d['inversion_mar'], d['inversion_abr'], d['inversion_may'],
                d['inversion_jun'], d['inversion_jul'], d['inversion_ago'], d['inversion_sep'], d['inversion_oct'], d[
                    'inversion_nov'], d['inversion_dic']
            ];

            Chart.defaults.global.defaultFontFamily = "Arial";
            Chart.defaults.global.defaultFontSize = 12;

            var speedData = {
                labels: ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'],
                datasets: [{
                    label: "Inversi\u00F3n",
                    data: dataR,
                    backgroundColor: "#bbedc9",
                }]
            };

            var chartOptions = {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 80,
                        fontColor: 'black'
                    }
                }
            };

            var lineChart = new Chart(speedCanvas, {
                type: 'line',
                data: speedData,
                options: chartOptions
            });
        }
    </script>
@endsection
