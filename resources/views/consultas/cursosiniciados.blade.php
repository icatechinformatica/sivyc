<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />

    <div class="card-header">
        Consulta de Cursos Iniciados

    </div>
    <div class="card card-body" style=" min-height:450px;">
        {{-- <form action="{{ route('xls-cursosiniciados') }}" method="post" id="registercontrato" enctype="multipart/form-data"> --}}
            {{-- @csrf --}}
            {!! Form::open(['route' => 'consulta-cursosval', 'method' => 'GET', 'class' => 'form-inline', 'id' => 'frm']) !!}
                <div class="pull-left">
                    <select name="unidad" class="form-control mr-sm-2" id="unidad">
                        <option >SELECCIONE UNIDAD</option>
                        @foreach ($unidades as $cadwell)
                        <option value="{{$cadwell->ubicacion}}" @if($cadwell->ubicacion == $unidad) selected @endif>{{$cadwell->ubicacion}}</option>
                        @endforeach
                    </select>
                    <select name="initer" class="form-control mr-sm-2" id="initer">
                        <option>CURSOS</option>
                        <option value="inicio" @if($initer == 'inicio') selected @endif>INICIADOS</option>
                        <option value="termino" @if($initer == 'termino') selected @endif>TERMINADOS</option>
                    </select>
                    {{-- {!! Form::date('inicio', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'FECHA INICIO', 'aria-label' => 'BUSCAR', 'value' => {inicio}}]) !!} --}}
                    {{-- {!! Form::date('termino', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'FECHA TERMINO', 'aria-label' => 'BUSCAR', 'value' => {termino}}]) !!} --}}
                    <input type="date" id="inicio" name="inicio" class="form-control mr-sm-2" placeholder="Fecha Inicio" value="{{$inicio}}">
                    <input type="date" id="termino" name="termino" class="form-control mr-sm-2" placeholder="Fecha Termino" value="{{$termino}}">
                    <button class="btn mr-sm-4 mt-3 form-control" type="submit">BUSCAR</button>
                    {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn mr-sm-4 mt-3']) }}
                </div>
            {!! Form::close() !!}
        {{-- </form> --}}
        <br>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                           <th scope="col" class="text-center" width="1%" rowspan="2">#</th>
                            <th scope="col" class="text-center" width="8%" rowspan="2">UNIDAD/ACCION MOVIL</th>
                            {{-- <th scope="col" class="text-center" width="8%">ACCION MOVIL</th> --}}
                            <th scope="col" class="text-center" width="8%" rowspan="2">ESCPECIALIDAD</th>
                            <th scope="col" class="text-center" width="12%" rowspan="2">CURSO</th>
                            <th scope="col" class="text-center" width="8%" rowspan="2">CLAVE</th>
                            <th scope="col" class="text-center" width="2%" rowspan="2">MODALIDAD</th>
                            <th scope="col" class="text-center" width="2%" rowspan="2">DURACIÓN</th>
                            <th scope="col" class="text-center" width="5%" rowspan="2">INICIO</th>
                            <th scope="col" class="text-center" width="5%" rowspan="2">TERMINO</th>
                            <th scope="col" class="text-center" width="10%" rowspan="2">HORARIO</th>
                            <th scope="col" class="text-center" width="8%" rowspan="2">DÍAS</th>
                            <th scope="col" class="text-center" width="2%" rowspan="2">HORAS</th>
                            <th scope="col" class="text-center" width="2%" rowspan="2">CUPO</th>
                            <th scope="col" class="text-center" width="10%" rowspan="2">INSTRUCTOR</th>
                            <th scope="col" class="text-center" width="2%" rowspan="2">CRITERIO DE PAGO</th>
                            <th scope="col" class="text-center" width="2%" rowspan="2">FEMENINO</th>
                            <th scope="col" class="text-center" width="2%" rowspan="2">MASCULINO</th>
                            {{-- <th scope="col" class="text-center" width="4%" rowspan="2">CUOTA</th> --}}
                            {{-- <th scope="col" class="text-center" width="4%" rowspan="2">ESQUEMA</th> --}}
                            <th scope="col" class="text-center" width="4%" colspan="4">TIPO PAGO</th>
                            <th scope="col" class="text-center" width="25%" rowspan="2">OBSERVACIONES</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">MUNICIPIO</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">DEPEND. BENEFICIADA</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">MEMO DE SOLICITUD ARC-01</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">MEMO DE AUTORIZACION DTA</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">MEMO DE SOLICITUD DE REPROGRAMACIÓN.</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">MEMO DE AUTORIZACION DE REPROGRAMACIÓN DTA</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">ESPACIO FÍSICO</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">HONORARIOS</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">REPORTADO</th>
                            <th scope="col" class="text-center" width="6%" rowspan="2">TIPO DE CAPACITACIÓN</th>
                        </tr>
                        <tr>
                            <th scope="col" class="text-center" width="1%" rowspan="2">CUOTA</th>
                            <th scope="col" class="text-center" width="1%" rowspan="2">CERTIFICACIÓN</th>
                            <th scope="col" class="text-center" width="1%" rowspan="2">EXONERACIÓN</th>
                            <th scope="col" class="text-center" width="1%" rowspan="2">EXONERACIÓN PARCIAL</th>
                        </tr>
                    </thead>
                    @if(isset($data))
                    <?php $i=1;   ?>
                    <tbody>
                        @foreach($data as $d)
                        @php $cupo = $d->hombre + $d->mujer; @endphp
                            <tr>
                                <td>{{ $i++ }}</td>
                                {{-- <td>{d->ubicacion }}</td> --}}
                                <td>{{ $d->unidad }}</td>
                                <td>{{ $d->espe }}</td>
                                <td>{{ $d->curso }}</td>
                                <td>{{ $d->clave }}</td>
                                <td class="text-center" >{{ $d->mod }}</td>
                                <td class="text-center" >{{ $d->dura }}</td>
                                <td class="text-center" >{{ $d->inicio }}</td>
                                <td class="text-center" >{{ $d->termino }}</td>
                                <td class="text-center" >{{ $d->hini }} A {{ $d->hfin }}</td>
                                <td class="text-center" >{{ $d->dia }}</td>
                                <td class="text-center" >{{ $d->horas }}</td>
                                <td class="text-center" >{{ $cupo }}</td>
                                <td class="text-center" >{{ $d->nombre }}</td>
                                <td class="text-center" >{{ $d->cp }}</td>
                                <td class="text-center" >{{ $d->mujer }}</td>
                                <td class="text-center" >{{ $d->hombre }}</td>
                                {{-- <td class="text-center" >{d->costo }}</td> --}}
                                {{-- <td class="text-center" >{d->tipo_curso }}</td> --}}
                                <td class="text-center" >@if($d->tipo == 'PINS' && $d->tipo_curso == 'CURSO') X @endif</td>
                                <td class="text-center" >@if($d->tipo_curso == 'CERTIFICACION') X @endif</td>
                                <td class="text-center" >@if($d->tipo == 'EXO' && $d->tipo_curso == 'CURSO') X @endif</td>
                                <td class="text-center" >@if($d->tipo == 'EPAR' && $d->tipo_curso == 'CURSO') X @endif</td>
                                <td class="text-center" >{{ $d->nota }}</td>
                                <td class="text-center" >{{ $d->muni }}</td>
                                <td class="text-center" >{{ $d->depen }}</td>
                                <td class="text-center" >{{ $d->munidad }}</td>
                                <td class="text-center" >{{ $d->mvalida }}</td>
                                <td class="text-center" >{{ $d->nmunidad }}</td>
                                <td class="text-center" >{{ $d->nmacademico }}</td>
                                <td class="text-center" >{{ $d->efisico }}</td>
                                <td class="text-center" >{{ $d->modinstructor }}</td>
                                <td class="text-center" >{{ $d->status }}</td>
                                <td class="text-center" >{{ $d->tcapacitacion }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                    @else
                    <div class="alert alert-warning">
                        <strong>Info!</strong> No hay Registros
                    </div>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
        <script language="javascript">
            // console.log('a');
            $(document).ready(function(){
                $("#botonXLS" ).click(function(){ console.log('a'); $('#frm').attr('action', "{{route('xls-cursosiniciados')}}"); $("#frm").attr("target", '_blank');$('#frm').submit();});
            });
            $(function() {
                $( ".datepicker" ).datepicker({
                    dateFormat: "yy-mm-dd"
                });
            });
        </script>
    @endsection
