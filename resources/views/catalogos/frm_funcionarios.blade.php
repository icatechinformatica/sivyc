<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Funcionarios | SIVyC Icatech')
    <!--seccion-->

@section('content_script_css')
    <style>
        * {
            box-sizing: border-box;
        }

        /* Estilo del loader */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
            z-index: 9999; /* Asegura que esté por encima de otros elementos */
            display: none; /* Ocultar inicialmente */
        }

        #loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 6px solid #fff;
            border-top: 6px solid #621132;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .card-header{
            font-variant: small-caps;
            background-color: #621132;
            color: white;
            margin: 1.7% 1.7% 1% 1.7%;
            padding: 1.3% 39px 1.3% 39px;
            font-style: normal;
            font-size: 22px;
        }

        .card-body{
            margin: 1%;
            margin-left: 1.7%;
            margin-right: 1.7%;
            /* padding: 55px; */
            -webkit-box-shadow: 0 8px 6px -6px #999;
            -moz-box-shadow: 0 8px 6px -6px #999;
            box-shadow: 0 8px 6px -6px #999;
        }
        .card-body.card-msg{
            background-color: yellow;
            margin: .5% 1.7% .5% 1.7%;
            padding: .5% 5px .5% 25px;
        }

        body { background-color: #E6E6E6; }

        .btn, .btn:focus{ color: white; background: #12322b; font-size: 14px; border-color: #12322b; margin: 0 5px 0 5px; padding: 10px 13px 10px 13px; }
        .btn:hover { color: white; background:#2a4c44; border-color: #12322b; }

        /*Deshabilitamos la parte de forzar mayusculas*/
        input[type=text],
        select,
        textarea {
            text-transform: none !important;
        }

        .negritas_tabla{
            font-size: 14px;
            font-weight: bold;
        }

        .letras_tabla{
            font-size:11px;
        }


    </style>


@endsection

@section('content')
    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    <div class="card-header">
        Funcionarios
    </div>
    <div class="card card-body" style="min-height:450px;">
        @if ($message = Session::get('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ $message }}</strong>
            </div>
        @endif

        {{-- prueba de documentos electronicos --}}
        <div class="mb-4">
            <form action="{{route('generar.html.contrato')}}" method="post">
            @csrf
                <button class="btn btn-primary">CONTRATO</button>
            </form>
        </div>

        {{-- tabla --}}
        @if (count($data_func)> 0)
            <div class="col-4">
                <div class="">
                    {!! Form::open(['route' => 'catalogos.funcionarios.inicio', 'method' => 'POST', 'class'=>'d-flex d-row mb-2']) !!}
                        @csrf
                        {!! Form::text('busqueda', $busqueda, ['class' => 'form-control', 'placeholder' => 'Funcionario / Curp / Cargo', 'required' => false]) !!}
                        {!! Form::submit('Buscar', ['class' => 'btn-sm btn-info ml-2']) !!}
                        <a href="{{ route('catalogos.funcionarios.inicio') }}" class="btn-sm btn-secondary ml-2 pt-2">Limpiar</a>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="col-12 text-center">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th class="negritas_tabla">Id</th>
                        <th class="negritas_tabla">Funcionario</th>
                        <th class="negritas_tabla">Cargo</th>
                        <th class="negritas_tabla">Titular</th>
                        <th class="negritas_tabla">Telefono</th>
                        <th class="negritas_tabla">Correo</th>
                        <th class="negritas_tabla">Curp</th>
                        <th class="negritas_tabla">Status</th>
                        <th class="negritas_tabla">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_func as $item)
                            <tr>
                                <th scope="row">{{$item->id}}</th>
                                {{-- <td class="letras_tabla">{{$item->nombre}}</td> --}}
                                {{-- Nuevo modulo --}}
                                <td>
                                    <a href="#" onclick="consultarInfo('6Y-250041', 'info_curso', 2025)" class="text-primary">
                                    {{$item->nombre}}
                                    </a>
                                </td>
                                {{-- Nuevo modulo --}}
                                <td class="letras_tabla">
                                    {{-- {{$item->cargo}} --}}
                                    <a href="#" onclick="consultarInfo('6Y-250041', 'info_instructor', 2025)" class="text-primary">
                                        {{$item->cargo}}
                                    </a>
                                </td>
                                <td class="letras_tabla">{{$item->titular ? 'SI' : 'NO'}}</td>
                                <td class="letras_tabla">{{$item->telefono ?? 'SIN TELEFONO'}}</td>
                                <td class="letras_tabla">{{$item->correo ?? 'SIN CORREO'}}</td>
                                <td class="letras_tabla">{{$item->curp ?? 'SIN CURP' }}</td>
                                <td class="letras_tabla">{{$item->activo == 'true' ? 'ACTIVO' : 'INACTIVO'}}</td>
                                <td><button class="btn-sm btn-warning" onclick="modificar_fun({{$item->id}})">Modificar</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Paginación --}}
            <div class="row py-4">
                <div class="col d-flex justify-content-center">
                    {{$data_func->appends(request()->query())->links()}}
                </div>
            </div>
        @endif

        {{-- Formulario de alta --}}
        {!! Form::open(['route' => 'frm.guardar', 'method' => 'POST']) !!}
            <div class="col-12 shadow p-3 mb-5 rounded">
                <p class="font-weight-bold py-0" style="font-size:18px;">Registrar Funcionario</p>
                <hr>
                <div class="d-flex row">
                    {!! Form::hidden('id_registro', '', ['id' => 'id_registro']) !!}
                    {{-- Campo select --}}
                    <div class="col-3 mb-2">
                        {!! Form::label('org', 'Selecciona un organismo:', ['class' => 'form-label']) !!}
                        {!! Form::select('org', [
                            '' => '-- Selecciona --',
                        ] + $list_org, null, ['class' => 'form-control', 'required' => true]) !!}
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('nombre', 'Nombre del funcionario:', ['class' => 'form-label']) !!}
                        {!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'JAVIER LOPEZ MORENO', 'required' => true]) !!}
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('cargo', 'Cargo:', ['class' => 'form-label']) !!}
                        {!! Form::text('cargo', null, ['class' => 'form-control', 'placeholder' => 'JEFE DE DEPARTAMENTO...', 'required' => true]) !!}
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('adscripcion', 'Adscripción:', ['class' => 'form-label']) !!}
                        {!! Form::text('adscripcion', null, ['class' => 'form-control', 'placeholder' => 'UNIDAD EJECUTIVA', 'required' => false]) !!}
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('direc', 'Dirección:', ['class' => 'form-label']) !!}
                        {!! Form::text('direc', null, ['class' => 'form-control', 'placeholder' => 'Calle 15 de Mayo...', 'required' => false]) !!}
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('telefono', 'Teléfono:', ['class' => 'form-label']) !!}
                        {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => '9612344235', 'required' => true]) !!}
                    </div>

                    <!-- Campo de Correo Electrónico -->
                    <div class="col-3 mb-2">
                        {!! Form::label('email', 'Correo Electrónico:', ['class' => 'form-label']) !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'ejemplo@correo.com', 'required' => true]) !!}
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('email2', 'Correo Institucional:', ['class' => 'form-label']) !!}
                        {!! Form::email('email2', null, ['class' => 'form-control', 'placeholder' => 'ejemplo@correo.com', 'required' => false]) !!}
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('curp', 'Curp:', ['class' => 'form-label']) !!}
                        {!! Form::text('curp', null, ['class' => 'form-control', 'placeholder' => 'MOAL97090....', 'required' => true]) !!}
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('titulo', 'Titulo:', ['class' => 'form-label']) !!}
                        {!! Form::text('titulo', null, ['class' => 'form-control', 'placeholder' => 'ING.', 'required' => true]) !!}
                    </div>


                    <div class="col-3 mb-2 mt-2 text-center">
                        {!! Form::label('titular', '¿Es titular?', ['class' => 'form-label']) !!}
                        <div class="d-flex justify-content-center">
                            <div>
                                {!! Form::radio('titular', 'titular_si', false, ['id' => 'titular_si', 'class'=>'text-center', 'required' => true]) !!}
                                {!! Form::label('titular_si', 'Si') !!}
                            </div>
                            <div class="ml-4">
                                {!! Form::radio('titular', 'titular_no', false, ['id' => 'titular_no', 'required' => true]) !!}
                                {!! Form::label('titular_no', 'No') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-3 mb-2 mt-2 text-center">
                        {!! Form::label('status', 'Status', ['class' => 'form-label']) !!}
                        <div class="d-flex justify-content-center">
                            <div>
                                {!! Form::radio('status', 'activo', false, ['id' => 'activo', 'required' => true]) !!}
                                {!! Form::label('activo', 'Activo') !!}
                            </div>
                            <div class="ml-4">
                                {!! Form::radio('status', 'inactivo', false, ['id' => 'inactivo', 'required' => true]) !!}
                                {!! Form::label('inactivo', 'Inactivo') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-3 mb-2">
                        {!! Form::label('sel_cargo', '¿Pertenece a una UC? Seleccioné el cargo:', ['class' => 'form-label']) !!}
                        {!! Form::select('sel_cargo', [
                            '' => '-- Selecciona --',
                        ] + $list_cargos, null, ['class' => 'form-control', 'required' => false]) !!}
                    </div>

                    <!-- Botón de Envío -->
                    <div class="col-3 mt-3 text-left mb-2">
                        {!! Form::submit('Guardar Cambios', ['class' => 'btn']) !!}
                    </div>

                </div>
            </div>
        {!! Form::close() !!}

    </div>

    <!-- Modal Dinamico Nuevo modulo -->
<div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesLabel"></h5>
                <button type="button" class="close cerrarModal" onclick="cerrarModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-body-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cerrarModal" onclick="cerrarModal()">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

    {{-- Aqui termina el modal --}}
@section('script_content_js')

    <script language="javascript">

        $(document).ready(function(){
            /*Deshabilitamos la parte de convertir a mayusculas*/
            $("input[type=text], textarea, select").off("keyup");
        });

        function modificar_fun(fun_id) {
            let data = {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "id_registro": fun_id,
                }
                $.ajax({
                    type:"post",
                    url: "{{ route('frm.obtener.datos') }}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        if(response.status == 200){
                            console.log("si ingreso");

                            //Agregar los valores del responde a los campos de texto.
                        }
                    }
                });

        }
        //Nuevo modulo
        //Funcion para obtener datos del grupo
        function consultarInfo(valor, tipo_valor, ejercicio) {
            if (valor != '' && tipo_valor != '') {
                let data = {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "valor": valor,
                    "tipo_valor": tipo_valor,
                    "ejercicio": ejercicio
                }
                $.ajax({
                    type:"post",
                    url: "{{ route('obtener.datos.modal') }}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        if(response.status == 200){
                            if (response.tipoValor == 'info_curso') {
                                const cursoData = response.consulta || {};
                                detallesCurso(
                                    cursoData.efisico || '',
                                    cursoData.hini || '',
                                    cursoData.hfin || '',
                                    cursoData.muni || ''
                                );

                            }else if(response.tipoValor == 'info_instructor'){
                                // Procesar las especialidades
                                const instructorData = response.consulta || {};
                                let arrayEspecialidades = [];
                                try {
                                    arrayEspecialidades = instructorData.especialidades ? instructorData.especialidades.split('|') : [];
                                } catch (e) {
                                    arrayEspecialidades = [];
                                    console.error("Error al procesar especialidades:", e);
                                }

                                detallesInstructor(
                                    instructorData.nombre_completo || 'No disponible',
                                    instructorData.telefono || 'No disponible',
                                    instructorData.fecha_ingreso || 'No disponible',
                                    response.unidad_solicita || 'No disponible',
                                    instructorData.nivelAcad || 'No disponible',
                                    instructorData.carrera || 'No disponible',
                                    arrayEspecialidades,
                                    response.tcursosIns || 'No disponible',
                                    response.monto_pago || 'No disponible'
                                );
                            }
                        }else{
                            alert("Error en la busqueda de información");
                        }
                    }
                });
            }

        }

        //Nuevo modulo
        function detallesCurso(lugar, horaIni, horaFin, municipio) {
            let contenido = `
                <p><strong>Lugar:</strong> ${lugar}</p>
                <p><strong>Hora de Inicio:</strong> ${horaIni} - <strong>Hora Final:</strong> ${horaFin}</p>
                <p><strong>Municipio:</strong> ${municipio}</p>
            `;
            abrirModal("Detalles del curso", contenido);
        }


        //Nuevo modulo
        function detallesInstructor(nombre_completo, telefono, fecha_ingreso, unidad_solicita, nivelAcad, carrera, especialidades, tcursosIns, monto_pago) {
            let listaEspecialidades = especialidades.map(esp => `<li>${esp}</li>`).join('');
            let contenido = `
                <p><strong>Telefono:</strong> ${telefono}</p>
                <p><strong>Unidad al que pertenece:</strong> ${unidad_solicita}</p>
                <p><strong>Nivel Academico:</strong> ${nivelAcad}</p>
                <p><strong>Carrera:</strong> ${carrera}</p>
                <p><strong>Total de cursos impartidos:</strong> ${tcursosIns}</p>
                <p><strong>Pago del Instructor:</strong> $ ${monto_pago} pesos</p>
                <div>
                    <strong>Especialidades validadas:</strong>
                    <ul>${listaEspecialidades}</ul>
                </div>
            `;
            abrirModal("Detalles del Instructor", contenido);
        }

        //Nuevo modulo
        function abrirModal(titulo, contenido) {
            document.getElementById("modalDetallesLabel").innerText = titulo;
            document.getElementById("modal-body-content").innerHTML = contenido;
            $("#modalDetalles").modal("show");
        }

        function cerrarModal() {
            document.activeElement.blur();
            $("#modalDetalles").modal("hide");
        }

    </script>
@endsection

