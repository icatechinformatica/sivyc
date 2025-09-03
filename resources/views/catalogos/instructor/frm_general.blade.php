<!-- Creado por Orlando Chávez orlando@sidmac.com -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Instructor | Sivyc Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}

        label.onpoint{
            cursor:pointer;
        }
        #center {
            vertical-align:middle;
            text-align:center;
            padding:0px;
        }

        .switch-container {
            display: flex;
            align-items: center;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .switch-text {
            margin-left: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .acordeon-borde {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        input:checked + .slider {
            background-color: #28a745; /* Color verde */
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .checkbox {
            margin-top: 5px;
        }


    </style>

    <div class="card-header">
            <h1>Registro de Instructor</h1>
    </div>
    @if(session('mensaje'))
        <div class="alert alert-danger">
            {{ session('mensaje') }}
        </div>
    @endif

    <div class="card card-body">
        <form action="" enctype="multipart/form-data" method="post" id="reginstructor">
            @csrf

            {{-- Sección ALFA --}}
            <div>
                @include('catalogos.instructor.frm_datosalfa')
            </div>

            <hr style="border-color:dimgray">

            {{-- Sección Datos Personales --}}
            <div>
                @include('catalogos.instructor.frm_datospersonales')
            </div>

            <hr style="border-color:dimgray">

            {{-- Sección de requisitos --}}
            <div>
                @include('catalogos.instructor.frm_cargaarchivos')
            </div>
        </form>

        <hr style="border-color:dimgray">

        {{-- Sección de experiencia docente --}}
        {{-- Seccion de experiencia laboral --}}
        {{-- Sección de certificados --}}
        {{-- Seccion de especialidades a impartir --}}
        <div>
            @include('catalogos.instructor.frm_exp_perfil_entrevista')
        </div>

        <br><hr style="border-color:dimgray">

        {{-- Generar curriculum --}}
        <div>
            <h3 class="font-weight-bold">Curriculum Vitae: ICATECH</h3>
            <div>
                <a class="btn btn-md btn-primary" href="" target="_blank">Generar PDF de curriculum</a>
                <button class="btn btn-md btn-primary">Subir<i class="fa fa-cloud ml-2"></i></button>

                <a href="" target="_blank" class="btn btn-md btn-success">VER PDF <i  class="far fa-file-pdf text-white"></i></a>
            </div>
        </div>

        <hr style="border-color:dimgray">

        {{-- Sección de movimientos --}}
        <div class="d-flex justify-content-end">
            <a class="btn btn-primary" href="{{URL::previous()}}">REGRESAR</a>
            <button type="submit" class="btn btn-danger">GUARDAR CAMBIOS</button>
        </div>
    </div>

    {{-- MODALES --}}

    {{-- MODAL DE PERFIL PROFESIONAL --}}
    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="addperprofModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #c90166;">
                    <h5 class="modal-title text-center font-weight-bold text-white">AGREGAR PERFIL PROFESIONAL</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="inputgrado_prof" class="form-label">Nivel Educativo</label>
                            <select class="form-control" name="grado_prof" id="grado_prof">
                                <option value="sin especificar">SIN ESPECIFICAR</option>
                                <option value="PRIMARIA">Primaria</option>
                                <option value="SECUNDARIA">Secundaria</option>
                                <option value="BACHILLERATO">Bachillerato</option>
                                <option value="CARRERA TÉCNICA">Carrera Técnica</option>
                                <option value="LICENCIATURA">Licenciatura</option>
                                <option value="MAESTRIA">Maestría</option>
                                <option value="DOCTORADO">Doctorado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="inputarea_carrera">Area de la carrera</label>
                            <input name="area_carrera" id="area_carrera" type="text" class="form-control" aria-required="true">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="inputgrado_prof">Nombre de la Carrera</label>
                            <input name="carrera" id="carrera" type="text" class="form-control" aria-required="true">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="inputinstitucion_pais">Pais de la Institución Educativa</label>
                            <input name="institucion_pais" id="institucion_pais" type="text" class="form-control" aria-required="true">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputinstitucion_entidad">Entidad de la Institución Educativa</label>
                            <input name="institucion_entidad" id="institucion_entidad" type="text" class="form-control" aria-required="true">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputinstitucion_ciudad">Ciudad de la Institución Educativa</label>
                            <input name="institucion_ciudad" id="institucion_ciudad" type="text" class="form-control" aria-required="true">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputestatus">Documento Obtenido</label>
                            <select class="form-control" name="estatus" id="estatus">
                                <option value="sin especificar">SIN ESPECIFICAR</option>
                                <option value="CONSTANCIA">Constancia</option>
                                <option value="CERTIFICADO">Certificado</option>
                                <option value="TITULO">Titulo</option>
                                <option value="CEDULA">Cedula</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputinstitucion_nombre">Nombre de la Institución Educativa</label>
                            <input name="institucion_nombre" id="institucion_nombre" type="text" class="form-control" aria-required="true">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputfecha_documento">Fecha de Expedicion del Documento</label>
                            <input name="fecha_documento" id="fecha_documento" type="date" class="form-control" aria-required="true">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputperiodo">Periodo Escolar Cursado</label>
                            <input name="periodo" id="periodo" type="text" class="form-control" aria-required="true">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputfolio_documento">Folio del Documento</label>
                            <input name="folio_documento" id="folio_documento" type="text" class="form-control" aria-required="true">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="inputicursos_recibidos">Cursos Recibidos</label>
                            <select name="cursos_recibidos" id="cursos_recibidos" class="form-control">
                                <option value="sin especificar">SIN ESPECIFICAR</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputicapacitador_icatech">Capacitador ICATECH</label>
                            <select name="capacitador_icatech" id="capacitador_icatech" class="form-control">
                                <option value="sin especificar">SIN ESPECIFICAR</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputcursos_recibidos">Cursos Recibidos ICATECH</label>
                            <select name="recibidos_icatech" id="recibidos_icatech" class="form-control">
                                <option value="sin especificar">SIN ESPECIFICAR</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputcursos_impartidos">Cursos Impartidos</label>
                            <select name="cursos_impartidos" id="cursos_impartidos" class="form-control">
                                <option value="sin especificar">SIN ESPECIFICAR</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                    </div>
                    {{-- <div class="form-row">
                        <div class="form-group col-md-11" style="text-align: right;width:100%">
                        </div>
                    </div> --}}
                    <div class="d-flex justify-content-end">
                        <button onclick="" class="btn mr-sm-4 mt-3" >Agregar</button>
                    </div>
                    <br>
                    <input type="hidden" name="idInstructor" id="idInstructor">
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script_content_js')
    <script src="{{ asset("js/catalogos/instructores/instructoresValidate.js") }}"></script>
    <script src="{{ asset("js/catalogos/instructores/funciones.js") }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function local() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("municipio").value;
            var datos = {valor: valor};
            console.log('hola');

            var url ='/instructores/busqueda/nomesp';

            var url = '/instructores/busqueda/localidad';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#localidad").empty();
                var selectL = document.getElementById('localidad'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].clave;
                    newOption.text=respuesta[i].localidad;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local2() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("entidad").value;
            var datos = {valor: valor};
            // console.log('hola');
            var url = '/instructores/busqueda/municipio';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#municipio").empty();
                var selectL = document.getElementById('municipio'),
                option,
                i = 0,
                il = respuesta.length;
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].id;
                    newOption.text=respuesta[i].muni;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local_nacimiento() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("municipio_nacimiento").value;
            var datos = {valor: valor};
            console.log('hola');

            var url ='/instructores/busqueda/nomesp';

            var url = '/instructores/busqueda/localidad';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#localidad_nacimiento").empty();
                var selectL = document.getElementById('localidad_nacimiento'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].clave;
                    newOption.text=respuesta[i].localidad;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local2_nacimiento() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("entidad_nacimiento").value;
            var datos = {valor: valor};
            // console.log('hola');
            var url = '/instructores/busqueda/municipio';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#municipio_nacimiento").empty();
                var selectL = document.getElementById('municipio_nacimiento'),
                option,
                i = 0,
                il = respuesta.length;
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].id;
                    newOption.text=respuesta[i].muni;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function saveperprof() {
            var datos = {
                            grado_prof: document.getElementById("grado_prof").value,
                            area_carrera: document.getElementById("area_carrera").value,
                            carrera: document.getElementById("carrera").value,
                            estatus: document.getElementById("estatus").value,
                            institucion_pais: document.getElementById("institucion_pais").value,
                            institucion_entidad: document.getElementById("institucion_entidad").value,
                            institucion_ciudad: document.getElementById("institucion_ciudad").value,
                            institucion_nombre: document.getElementById("institucion_nombre").value,
                            fecha_documento: document.getElementById("fecha_documento").value,
                            periodo: document.getElementById("periodo").value,
                            folio_documento: document.getElementById("folio_documento").value,
                            cursos_recibidos: document.getElementById("cursos_recibidos").value,
                            capacitador_icatech: document.getElementById("capacitador_icatech").value,
                            recibidos_icatech: document.getElementById("recibidos_icatech").value,
                            cursos_impartidos: document.getElementById("cursos_impartidos").value,
                            // exp_lab: document.getElementById("exp_lab").value,
                            // exp_doc: document.getElementById("exp_doc").value,
                            idInstructor: document.getElementById("idInstructor").value,
                            row: document.getElementById("tableperfiles").rows.length
                        };
            if(datos.grado_prof != '' && datos.area_carrera != '' && datos.carrera != '' && datos.estatus != 'SIN ESPECIFICAR' &&
             datos.institucion_pais != '' && datos.institucion_entidad != '' && datos.institucion_ciudad != '' &&
             datos.institucion_nombre != '' && datos.fecha_documento != '' && datos.periodo != '' && datos.folio_documento != '' &&
             datos.cursos_recibidos != 'SIN ESPECIFICAR' && datos.cursos_impartidos != 'SIN ESPECIFICAR' &&
             datos.capacitador_icatech != 'SIN ESPECIFICAR' && datos.recibidos_icatech != 'SIN ESPECIFICAR'
            //  &&  datos.exp_lab != '' && datos.exp_doc != ''
            )
            {
                var url = '/perfilinstructor/guardar';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                request.done(( respuesta) =>
                {
                    $('#addperprofModal').modal('hide');

                    if(respuesta['exist'] == 'FALSO')
                    {
                        $('#warning1').prop("class", "d-none d-print-none")
                        var table = document.getElementById('tableperfiles')
                        var header = table.createTHead();
                        var row1 = header.insertRow(0);
                        var cell1 = row1.insertCell(0);
                        var cell2 = row1.insertCell(1);
                        var cell3 = row1.insertCell(2);
                        var cell4 = row1.insertCell(3);
                        var cell5 = row1.insertCell(4);
                        var cell6 = row1.insertCell(5);
                        cell1.innerHTML = 'Grado Profesional';
                        cell2.innerHTML = 'Area de la Carrera';
                        cell3.innerHTML = 'Nivel de Estudio';
                        cell4.innerHTML = 'Nombre de la Institución';
                        cell5.innerHTML = 'Status';
                        cell6.innerHTML = 'Accion';
                        var body = table.createTBody();
                        var row2 = body.insertRow(0);
                        var cell7 = row2.insertCell(0);
                        var cell8 = row2.insertCell(1);
                        var cell9 = row2.insertCell(2);
                        var cell10 = row2.insertCell(3);
                        var cell11 = row2.insertCell(4);
                        var cell12 = row2.insertCell(5);
                        cell7.innerHTML = datos.grado_prof;
                        cell8.innerHTML = datos.area_carrera;
                        cell9.innerHTML = datos.estatus;
                        cell10.innerHTML = datos.institucion_nombre;
                        cell11.innerHTML = respuesta['status'];
                        cell12.innerHTML = respuesta['button'] + ' ' + respuesta['button2'];
                        $('#buttonaddperfprof').prop("class", "btn mr-sm-4 mt-3");
                        $('#divperfil').prop("class", "");
                        $('#divnonperfil').prop("class", "d-none d-print-none");
                    }
                    else
                    {
                        var row = document.getElementById("tableperfiles").rows.length
                        var table = document.getElementById('tableperfiles')
                        var row = table.insertRow(row);
                        var cell1 = row.insertCell(0);
                        var cell2 = row.insertCell(1);
                        var cell3 = row.insertCell(2);
                        var cell4 = row.insertCell(3);
                        var cell5 = row.insertCell(4);
                        var cell6 = row.insertCell(5);
                        cell1.innerHTML = datos.grado_prof;
                        cell2.innerHTML = datos.area_carrera;
                        cell3.innerHTML = datos.estatus;
                        cell4.innerHTML = datos.institucion_nombre;
                        cell5.innerHTML = respuesta['status'];
                        cell6.innerHTML = respuesta['button'] + ' ' + respuesta['button2'];
                    }

                    document.getElementById("grado_prof").value = '';
                    document.getElementById("area_carrera").value = '';
                    document.getElementById("estatus").value = '';
                    document.getElementById("institucion_pais").value = '';
                    document.getElementById("institucion_entidad").value = '';
                    document.getElementById("institucion_ciudad").value = '';
                    document.getElementById("institucion_nombre").value = '';
                    document.getElementById("fecha_documento").value = '';
                    document.getElementById("folio_documento").value = '';
                    document.getElementById("cursos_recibidos").value = '';
                    document.getElementById("capacitador_icatech").value = '';
                    document.getElementById("recibidos_icatech").value = '';
                    document.getElementById("cursos_impartidos").value = '';
                    document.getElementById("exp_lab").value = '';
                    document.getElementById("exp_doc").value = '';
                    document.getElementById("idInstructor").value = '';
                    $('#addperprofwarning').prop("class", "d-none d-print-none")
                    const span = document.getElementById('newnrevisionspan');
                    $('#newnrevisionwarning').prop("class", "alert alert-success")
                    span.textContent = respuesta['nrevisiontext'];
                });

            }
            else
            {
                const span = document.getElementById('addperfprofspan');
                $('#addperprofwarning').prop("class", "alert alert-danger")
                span.textContent = 'Error: Uno o mas campos estan vacios';
                $('#addperprofModal').animate({scrollTop: 0},400);
                // console.log('nada');
            }
        }

        function savemodperprof() {
            var datos = {
                            grado_prof: document.getElementById("grado_prof2").value,
                            area_carrera: document.getElementById("area_carrera2").value,
                            carrera: document.getElementById("carrera2").value,
                            estatus: document.getElementById("estatus2").value,
                            institucion_pais: document.getElementById("institucion_pais2").value,
                            institucion_entidad: document.getElementById("institucion_entidad2").value,
                            institucion_ciudad: document.getElementById("institucion_ciudad2").value,
                            institucion_nombre: document.getElementById("institucion_nombre2").value,
                            fecha_documento: document.getElementById("fecha_documento2").value,
                            periodo: document.getElementById("periodo2").value,
                            folio_documento: document.getElementById("folio_documento2").value,
                            cursos_recibidos: document.getElementById("cursos_recibidos2").value,
                            capacitador_icatech: document.getElementById("capacitador_icatech2").value,
                            recibidos_icatech: document.getElementById("recibidos_icatech2").value,
                            cursos_impartidos: document.getElementById("cursos_impartidos2").value,
                            // exp_lab: document.getElementById("exp_lab2").value,
                            // exp_doc: document.getElementById("exp_doc2").value,
                            idInstructor: document.getElementById("idInstructor2").value,
                            idperfprof: document.getElementById("idperfprof2").value,
                            pos: document.getElementById("row").value
                        };
            if(datos.grado_prof != '' && datos.area_carrera != '' && datos.carrera != '' && datos.estatus != 'SIN ESPECIFICAR' &&
             datos.institucion_pais2 != '' && datos.institucion_entidad != '' && datos.institucion_ciudad != '' &&
             datos.institucion_nombre != '' && datos.fecha_documento != '' && datos.periodo != ''&& datos.folio_documento != '' &&
             datos.capacitador_icatech != 'SIN ESPECIFICAR' && datos.recibidos_icatech != 'SIN ESPECIFICAR' &&
             datos.exp_lab != '' && datos.exp_doc != '')
            {
                var url = '/instructor/mod/perfilinstructor/guardar';
                var request2 = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request2.done(( respuesta) =>
                {
                    position = document.getElementById("row").value;
                    // console.log(position);
                    $('#modperprofModal').modal('hide');
                        var table = document.getElementById('tableperfiles')
                        var row = table.rows[position];
                        var cell1 = row.cells[0];
                        var cell2 = row.cells[1];
                        var cell3 = row.cells[2];
                        var cell4 = row.cells[3];
                        var cell5 = row.cells[4];
                        var cell6 = row.cells[5];
                        cell1.innerHTML = datos.grado_prof;
                        cell2.innerHTML = datos.area_carrera;
                        cell3.innerHTML = datos.estatus;
                        cell4.innerHTML = datos.institucion_nombre;
                        cell5.innerHTML = respuesta['status'];
                        cell6.innerHTML = respuesta['button'] + ' ' + respuesta['button2'] + ' ' + respuesta['button3'];

                    $('#modperprofwarning').prop("class", "d-none d-print-none")
                });
            }
            else
            {
                const span = document.getElementById('modperfprofspan');
                $('#modperprofwarning').prop("class", "alert alert-danger")
                span.textContent = 'Error: Uno o mas campos estan vacios';
                $('#modperprofModal').animate({scrollTop: 0},400);
                // console.log('nada');
            }
        }

        function delperprof() {
            var datos = {
                            id: document.getElementById("iddelperfprof").value
                        };

            var url = '/instructor/mod/perfilinstructor/eliminar';
            var request2 = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request2.done(( respuesta) =>
            {
                if(respuesta['error'] != 'error')
                {
                    position = document.getElementById("row").value;
                    // console.log(respuesta);
                    $('#delperprofModal').modal('hide');
                    $('#delperprofwarning').prop("class", "d-none d-print-none")
                        var table = document.getElementById('tableperfiles')
                        var locdel = document.getElementById('locdel').value
                        table.deleteRow(locdel);
                }
                else
                {
                    const span = document.getElementById('delperfprofspan');
                    $('#delperprofwarning').prop("class", "alert alert-danger")
                    span.textContent = 'Error: Una o mas especialidades dependen de este perfil profesional';
                    $('#delperprofModal').animate({scrollTop: 0},400);
                }
            });
        }

        function delespecvalid() {
            var datos = {
                            id: document.getElementById("idespecvalid").value,
                            idins: document.getElementById("idinsespecvalid").value
                        };

            var url = '/instructor/mod/especialidadimpartir/eliminar';
            var request2 = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request2.done(( respuesta) =>
            {
                position = document.getElementById("row").value;
                $('#delespecvalidModal').modal('hide');
                $('#delespecvalidwarning').prop("class", "d-none d-print-none")
                var table = document.getElementById('espec-table')
                var loc2del = document.getElementById('loc2del').value
                table.deleteRow(loc2del);
            });
        }

        function saveexpdoc() {
            var datos = {
                            asignatura: document.getElementById("asignatura").value,
                            institucion: document.getElementById("institucion").value,
                            funcion: document.getElementById("funcion").value,
                            periodo: document.getElementById("periodoadddoc").value,
                            idins: document.getElementById("idInstructorexpdoc").value
                        };
            if(datos.asignatura != '' && datos.institucion != '' && datos.funcion != '' &&
             datos.periodo != '' && datos.idins != '')
            {
                var url = '/expdoc/guardar';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                request.done(( respuesta) =>
                {
                    console.log(respuesta);
                    $('#addexpdocModal').modal('hide');
                    var row = document.getElementById("tableexpdoc").rows.length
                    var table = document.getElementById('tableexpdoc')
                    var row = table.insertRow(respuesta['pos']);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    var cell5 = row.insertCell(4);
                    cell1.innerHTML = datos.asignatura;
                    cell2.innerHTML = datos.institucion;
                    cell3.innerHTML = datos.funcion;
                    cell4.innerHTML = datos.periodo;
                    cell5.innerHTML = respuesta['button'];

                    document.getElementById("asignatura").value = '';
                    document.getElementById("institucion").value = '';
                    document.getElementById("funcion").value = '';
                    document.getElementById("periodo").value = '';
                    document.getElementById("idInstructorexpdoc").value = '';
                    $('#addexpdocwarning').prop("class", "d-none d-print-none")
                    const span = document.getElementById('newnrevisionspan');
                });

            }
            else
            {
                const span = document.getElementById('addexpdocspan');
                $('#addexpdocwarning').prop("class", "alert alert-danger")
                span.textContent = 'Error: Uno o mas campos estan vacios';
                $('#addexpdocModal').animate({scrollTop: 0},400);
                console.log('nada');
            }
        }

        function delexpdoc() {
            var datos = {
                            idins: document.getElementById("idinsdelexpdoc").value,
                            asignatura: document.getElementById("asdel").value,
                            institucion: document.getElementById("indel").value,
                            funcion: document.getElementById("fudel").value,
                            periodo: document.getElementById("pedel").value
                        };

            var url = '/instructor/expdoc/eliminar';
            var request2 = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request2.done(( respuesta) =>
            {
                var table = document.getElementById('tableexpdoc')
                countdel = table.rows.length

                for(i = 1; i < countdel; i++)
                {
                    table.deleteRow(1);
                }
                i = 1;
                respuesta.forEach( function(valor, indice, array){
                    row = table.insertRow(i)
                    cell1 = row.insertCell(0)
                    cell2 = row.insertCell(1)
                    cell3 = row.insertCell(2)
                    cell4 = row.insertCell(3)
                    cell5 = row.insertCell(4)
                    cell1.innerHTML = valor.asignatura
                    cell2.innerHTML = valor.institucion
                    cell3.innerHTML = valor.funcion
                    cell4.innerHTML = valor.periodo
                    cell5.innerHTML = valor.button});
                $('#delexpdocModal').modal('hide');
                console.log(respuesta)
            });
        }

        function saveexplab() {
            var datos = {
                            puesto: document.getElementById("puestolab").value,
                            periodo: document.getElementById("periodolab").value,
                            institucion: document.getElementById("institucionlab").value,
                            idins: document.getElementById("idInstructorexplab").value
                        };
            if(datos.puesto != '' && datos.periodo != '' &&  datos.institucion != '' && datos.idins != '')
            {
                var url = '/explab/guardar';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                request.done(( respuesta) =>
                {
                    $('#addexplabModal').modal('hide');
                    var row = document.getElementById("tableexplab").rows.length
                    var table = document.getElementById('tableexplab')
                    var row = table.insertRow(respuesta['pos']);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    cell1.innerHTML = datos.puesto;
                    cell2.innerHTML = datos.periodo;
                    cell3.innerHTML = datos.institucion;
                    cell4.innerHTML = respuesta['button'];

                    document.getElementById("puestolab").value = '';
                    document.getElementById("periodolab").value = '';
                    document.getElementById("institucionlab").value = '';
                    document.getElementById("idInstructorexpdoc").value = '';
                    $('#addexplabwarning').prop("class", "d-none d-print-none")
                    const span = document.getElementById('newnrevisionspan');
                });

            }
            else
            {
                const span = document.getElementById('addexplabspan');
                $('#addexplabwarning').prop("class", "alert alert-danger")
                span.textContent = 'Error: Uno o mas campos estan vacios';
                $('#addexplabModal').animate({scrollTop: 0},400);
            }
        }

        function delexplab() {
            var datos = {
                            idins: document.getElementById("idinsdelexplab").value,
                            puesto: document.getElementById("pulabdel").value,
                            periodo: document.getElementById("pelabdel").value,
                            institucion: document.getElementById("inlabdel").value
                        };

            var url = '/instructor/explab/eliminar';
            var request2 = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request2.done(( respuesta) =>
            {
                var table = document.getElementById('tableexplab')
                countdel = table.rows.length

                for(i = 1; i < countdel; i++)
                {
                    table.deleteRow(1);
                }
                i = 1;
                respuesta.forEach( function(valor, indice, array){
                    row = table.insertRow(i)
                    cell1 = row.insertCell(0)
                    cell2 = row.insertCell(1)
                    cell3 = row.insertCell(2)
                    cell4 = row.insertCell(3)
                    cell1.innerHTML = valor.puesto
                    cell2.innerHTML = valor.periodo
                    cell3.innerHTML = valor.institucion
                    cell4.innerHTML = valor.button});
                $('#delexplabModal').modal('hide');
                console.log(respuesta)
            });
        }

        chkpre = document.getElementById("chkpre").value;
        if(chkpre == 'FALSE')
        {
            let arine = document.getElementById("arch_ine");
            let ardom = document.getElementById("arch_domicilio");
            let arcur = document.getElementById("arch_curp");
            let arban = document.getElementById("arch_banco");
            let arfot = document.getElementById("arch_foto");
            let arid = document.getElementById("arch_id");
            let arrfc = document.getElementById("arch_rfc");
            let arest = document.getElementById("arch_estudio");
            let aralt = document.getElementById("arch_curriculum_personal");
            let imageName0 = document.getElementById("imageName0");
            let imageName = document.getElementById("imageName");
            let imageName2 = document.getElementById("imageName2");
            let imageName3 = document.getElementById("imageName3");
            let imageName4 = document.getElementById("imageName4");
            let imageName5 = document.getElementById("imageName5");
            let imageName6 = document.getElementById("imageName6");
            let imageName7 = document.getElementById("imageName7");
            let imageName8 = document.getElementById("imageName8");

            arine.addEventListener("change", ()=>{
                let inputImage0 = document.querySelector("#arch_ine").files[0];
                imageName0.innerText = inputImage0.name;
            })
            ardom.addEventListener("change", ()=>{
                let inputImage = document.querySelector("#arch_domicilio").files[0];
                imageName.innerText = inputImage.name;
            })
            arcur.addEventListener("change", ()=>{
                let inputImage2 = document.querySelector("#arch_curp").files[0];
                imageName2.innerText = inputImage2.name;
            })
            arban.addEventListener("change", ()=>{
                let inputImage3 = document.querySelector("#arch_banco").files[0];
                imageName3.innerText = inputImage3.name;
            })
            arfot.addEventListener("change", ()=>{
                let inputImage4 = document.querySelector("#arch_foto").files[0];
                imageName4.innerText = inputImage4.name;
            })
            arid.addEventListener("change", ()=>{
                let inputImage5 = document.querySelector("#arch_id").files[0];
                imageName5.innerText = inputImage5.name;
            })
            arrfc.addEventListener("change", ()=>{
                let inputImage6 = document.querySelector("#arch_rfc").files[0];
                imageName6.innerText = inputImage6.name;
            })
            arest.addEventListener("change", ()=>{
                let inputImage7 = document.querySelector("#arch_estudio").files[0];
                imageName7.innerText = inputImage7.name;
            })
            aralt.addEventListener("change", ()=>{
                let inputImage8 = document.querySelector("#arch_curriculum_personal").files[0];
                imageName8.innerText = inputImage8.name;
            })
        }

        function validacionpdfview()
        {
            window.open(document.getElementById('validacionpdf').value, "_blank");
            // console.log(document.getElementById('validacionpdf').value)
        }

        $('#addperprofModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            document.getElementById('idInstructor').value = id;
        });

        $('#modperprofModal').on('show.bs.modal', function(event){
            var button = $(event.relatedTarget);
            var id = button.data('id');
            if (!Array.isArray(id)) {
                if (!Array.isArray(id)) {
                    try {
                        id = JSON.parse(id);
                    } catch (e) {
                        id = id.split(",").map(item => item.trim().replace(/^"|"$/g, ''));
                    }
                }
            }
            $('#modperprofwarning').prop("class", "d-none d-print-none")
            document.getElementById('grado_prof2').value = id['0'];
            document.getElementById('area_carrera2').value = id['1'];
            document.getElementById('carrera2').value = id['2'];
            document.getElementById('estatus2').value = id['3'];
            document.getElementById('institucion_pais2').value = id['4'];
            document.getElementById('institucion_entidad2').value = id['5'];
            document.getElementById('institucion_ciudad2').value = id['6'];
            document.getElementById('institucion_nombre2').value = id['7'];
            document.getElementById('fecha_documento2').value = id['8'];
            document.getElementById('periodo2').value = id['9'];
            document.getElementById('folio_documento2').value = id['10'];
            document.getElementById('cursos_recibidos2').value = id['11'];
            document.getElementById('capacitador_icatech2').value = id['12'];
            document.getElementById('recibidos_icatech2').value = id['13'];
            document.getElementById('cursos_impartidos2').value = id['14'];
            // document.getElementById('exp_lab2').value = id['13'];
            // document.getElementById('exp_doc2').value = id['14'];
            document.getElementById('idperfprof2').value = id['15'];
            document.getElementById('idInstructor2').value = id['16'];
            document.getElementById('row').value = id['17'];
        });

        $('#delperprofModal').on('show.bs.modal', function(event){
            $('#delperprofwarning').prop("class", "d-none d-print-none")
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            document.getElementById('iddelperfprof').value = id['0'];
            document.getElementById('locdel').value = id['1'];
        });

        $('#delespecvalidModal').on('show.bs.modal', function(event){
            $('#delespecvalidwarning').prop("class", "d-none d-print-none")
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            document.getElementById('idespecvalid').value = id['0'];
            document.getElementById('loc2del').value = id['1'];
            document.getElementById('idinsespecvalid').value = id['2'];
        });

        $('#sendtodtaModal').on('show.bs.modal', function(event){
            $('#sentodtawarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idtodta').value = id;
        });

        $('#verperfprofModal').on('show.bs.modal', function(event){
            var button = $(event.relatedTarget);
            var idb = button.data('id');
            if (idb['16'] != 'VALIDADO')
            {
                // console.log(idb)
                var div = document.getElementById('listaperfprof')
                div.innerHTML = '<li>Grado Profesional: <b>' + idb['1'] + '</b></li><br>' +
                    '<li>Estatus: <b>' + idb['16'] + '</b></li><br>' +
                    '<li>Pais: <b>' + idb['4'] + '</b></li><br>' +
                    '<li>Entidad: <b>' + idb['5'] + '</b></li><br>' +
                    '<li>Ciudad: <b>' + idb['6'] + '</b></li><br>' +
                    '<li>Institución: <b>' + idb['7'] + '</b></li><br>' +
                    '<li>Fecha de Expedicion: <b>' + idb['8'] + '</b></li><br>' +
                    '<li>Folio de Documento: <b>' + idb['10'] + '</b></li><br>' +
                    '<li>Cursos Recibidos: <b>' + idb['11'] + '</b></li><br>' +
                    '<li>Capacitador ICATECH: <b>' + idb['12'] + '</b></li><br>' +
                    '<li>Cursos Recibidos ICATECH: <b>' + idb['13'] + '</b></li><br>' +
                    '<li>Cursos Impartidos: <b>' + idb['14'] + '</b></li><br>';
            }
            else
            {
                var datos = {
                                id: idb
                            };

                var url = '/instructor/detalles/perfilinstructor';
                var request2 = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request2.done(( respuesta) =>
                {
                    // console.log(respuesta);
                    var div = document.getElementById('listaperfprof')
                    div.innerHTML = '<li>Grado Profesional: <b>' + respuesta['grado_profesional'] + '</b></li><br>' +
                        '<li>Estatus: <b>' + respuesta['estatus'] + '</b></li><br>' +
                        '<li>Pais: <b>' + respuesta['pais_institucion'] + '</b></li><br>' +
                        '<li>Entidad: <b>' + respuesta['entidad_institucion'] + '</b></li><br>' +
                        '<li>Ciudad: <b>' + respuesta['ciudad_institucion'] + '</b></li><br>' +
                        '<li>Institución: <b>' + respuesta['nombre_institucion'] + '</b></li><br>' +
                        '<li>Fecha de Expedicion: <b>' + respuesta['fecha_expedicion_documento'] + '</b></li><br>' +
                        '<li>Folio de Documento: <b>' + respuesta['folio_documento'] + '</b></li><br>' +
                        '<li>Cursos Recibidos: <b>' + respuesta['cursos_recibidos'] + '</b></li><br>' +
                        '<li>Capacitador ICATECH: <b>' + respuesta['capacitador_icatech'] + '</b></li><br>' +
                        '<li>Cursos Recibidos ICATECH: <b>' + respuesta['recibidos_icatech'] + '</b></li><br>' +
                        '<li>Cursos Impartidos: <b>' + respuesta['cursos_impartidos'] + '</b></li><br>' +
                        '<li>Experiencia Laboral: <b>' + respuesta['experiencia_laboral'] + '</b></li><br>' +
                        '<li>Experiencia Docente: <b>' + respuesta['experiencia_docente'] + '</b></li><br>';
                });
            }
        });

        $('#verespevaliModal').on('show.bs.modal', function(event){
            var button = $(event.relatedTarget);
            var idb = button.data('id');
            console.log(idb)
            if(idb['7'] != 'VALIDADO')
            {
                console.log('a')

                var div = document.getElementById('listaespevali')
                div.innerHTML = '<li>Especialidad: <b>' + idb['0'] + '</b></li><br>' +
                    '<li>Peril Profesional: <b>' + idb['1'] + '</b></li><br>' +
                    '<li>Unidad Solicita: <b>' + idb['2'] + '</b></li><br>' +
                    '<li>Criterio Pago: <b>' + idb['3'] + '</b></li><br>' +
                    '<li>Memorandum de Solicitud: <b>' + idb['4'] + '</b></li><br>' +
                    '<li>Fecha de Solicitud: <b>' + idb['5'] + '</b></li><br>' +
                    '<li>Observaciones: <b>' + idb['6'] + '</b></li><br>' +
                    '<li>Cursos:</li><ul>' + idb['9'] + '</ul>';
            }
            else
            {
                var datos = {
                                id: idb
                            };

                var url = '/instructor/detalles/especialidadvalidada';
                var request2 = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request2.done(( respuesta) =>
                {
                    console.log(respuesta);
                    var div = document.getElementById('listaespevali')
                    div.innerHTML = '<li>Especialidad: <b>' + respuesta['especialidad'] + '</b></li><br>' +
                        '<li>Peril Profesional: <b>' + respuesta['perfilprof'] + '</b></li><br>' +
                        '<li>Unidad Solicita: <b>' + respuesta['unidad_solicita'] + '</b></li><br>' +
                        '<li>Criterio Pago: <b>' + respuesta['cp'] + '</b></li><br>' +
                        '<li>Memorandum de Solicitud: <b>' + respuesta['memorandum_solicitud'] + '</b></li><br>' +
                        '<li>Fecha de Solicitud: <b>' + respuesta['fecha_solicitud'] + '</b></li><br>' +
                        '<li>Observaciones: <b>' + respuesta['observacion'] + '</b></li><br>' +
                        '<li>Cursos:</li><ul>' + respuesta['cursos'] + '</ul>';
                });
            }
        });

        $('#prevalidarModal').on('show.bs.modal', function(event){
            $('#prevalidarwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinspreval').value = id;
        });

        $('#returntounitModal').on('show.bs.modal', function(event){
            $('#returntounitwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinsreturn').value = id;
        });

        $('#validacionesModal').on('show.bs.modal', function(event){
            $('#validacionwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            console.log(id)
            id = id.reverse();

            var selectL = document.getElementById('validacionpdf'),
            option,
            i = 0,
            il = id.length;

            // Convertir selectL en un objeto jQuery
            selectLJQ = $(selectL);
            // Limpiar las opciones del select
            selectLJQ.empty();
            // console.log(il);
            // console.log( id[0])
            for (; i < il; i += 1)
            {
                newOption = document.createElement('option');
                if(id[i].arch_val != null)
                {
                    newOption.value = id[i].arch_val;
                    newOption.text=id[i].memo_val;
                }
                else
                {
                    newOption.value = id[i].arch_baja;
                    newOption.text=id[i].memo_baja;
                }
                // selectL.appendChild(option);
                selectL.add(newOption);
            }
        });

        $('#entrevistaModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idInstructorentrevista').value = id;
        });

        $('#modentrevistaModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);

            var datos = {
                            id: id
                        };

            var url = '/instructores/detalles/getentrevista';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });
            request.done(( respuesta) =>
            {
                document.getElementById('MQ1').value = respuesta['1'];
                document.getElementById('MQ2').value = respuesta['2'];
                document.getElementById('MQ3').value = respuesta['3'];
                document.getElementById('MQ4').value = respuesta['4'];
                document.getElementById('MQ5').value = respuesta['5'];
                document.getElementById('MQ6').value = respuesta['6'];
                document.getElementById('MQ7').value = respuesta['7'];
                document.getElementById('MQ8').value = respuesta['8'];
                document.getElementById('MQ9').value = respuesta['9'];
                document.getElementById('MQ10').value = respuesta['10'];
                document.getElementById('MQ11').value = respuesta['11'];
                document.getElementById('MQ12').value = respuesta['12'];
                document.getElementById('MQ13').value = respuesta['13'];
                document.getElementById('MQ14').value = respuesta['14'];
                // var div = document.getElementById('listaperfprof')

            });
            document.getElementById('idInstructorentrevistamod').value = id;
        });

        $('#updentrevistaModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idInstructorentrevistaupd').value = id;
        });

        $('#updcurriculumModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idInstructorcurriculumupd').value = id;
        });

        $('#addexpdocModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            console.log(id);
            document.getElementById('idInstructorexpdoc').value = id;
        });

        $('#delexpdocModal').on('show.bs.modal', function(event){
            $('#delexpdocwarning').prop("class", "d-none d-print-none")
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var table = document.getElementById('tableexpdoc')
            var row = table.rows[id['0']];
            var asignatura = row.cells['0'].innerHTML
            var institucion = row.cells['1'].innerHTML
            var funcion = row.cells['2'].innerHTML
            var periodo = row.cells['3'].innerHTML

            document.getElementById('idinsdelexpdoc').value = id['1'];
            document.getElementById('locdelexpdoc').value = id['0'];
            document.getElementById('asdel').value = asignatura;
            document.getElementById('indel').value = institucion;
            document.getElementById('fudel').value = funcion;
            document.getElementById('pedel').value = periodo;
        });

        $('#addexplabModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idInstructorexplab').value = id;
        });

        $('#delexplabModal').on('show.bs.modal', function(event){
            $('#delexplabwarning').prop("class", "d-none d-print-none")
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var table = document.getElementById('tableexplab')
            var row = table.rows[id['0']];
            var puesto = row.cells['0'].innerHTML
            var periodo = row.cells['1'].innerHTML
            var institucion = row.cells['2'].innerHTML

            document.getElementById('idinsdelexplab').value = id['1'];
            document.getElementById('locdelexplab').value = id['0'];
            document.getElementById('pulabdel').value = puesto;
            document.getElementById('pelabdel').value = periodo;
            document.getElementById('inlabdel').value = institucion;
        });

        $('#bajainstructorModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idbajains').value = id;
        });

        $('#reacinstructorModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idreacins').value = id;
        });

        $('#bajaespeModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idbajaespe').value = id;
        });

        $('#enviarMovimiento').on('click', function() {
            // Get the selected value from the dropdown
            var movimiento = $('#movimiento').val();
            $.ajax({
                url: '/retorno/movimiento/instructor', // Replace with the actual endpoint
                type: 'POST',
                data: {
                    movimiento: movimiento,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Handle the response from the controller if needed
                    console.log(response);
                },
                error: function(error) {
                    // Handle the error if the request fails
                    console.error('AJAX Error:', error.responseText);
                }
            });
        });

        document.getElementById('reginstructor').addEventListener('submit', function (e) {
            var fileInputs = [
                'arch_domicilio',
                'arch_curp',
                'arch_banco',
                'arch_foto',
                'arch_id',
                'arch_rfc',
                'arch_estudio',
                'arch_curriculum_personal'
            ];

            for (var i = 0; i < fileInputs.length; i++) {
                var inputId = fileInputs[i];
                var fileInput = document.getElementById(inputId);
                if (fileInput.files.length === 0) {
                    switch(inputId) {
                        case 'arch_domicilio':
                            href = document.getElementById('arch_domicilio_pdf').getAttribute('href');
                            inputIdName = 'Comprobande de Domicilio';
                        break;
                        case 'arch_curp':
                            href = document.getElementById('arch_curp_pdf').getAttribute('href');
                            inputIdName = 'CURP';
                        break;
                        case 'arch_banco':
                            href = document.getElementById('arch_banco_pdf').getAttribute('href');
                            inputIdName = 'Comprobante Bancario';
                        break;
                        case 'arch_foto':
                            href = document.getElementById('arch_foto_jpg').getAttribute('href');
                            inputIdName = 'Fotografía';
                        break;
                        case 'arch_id':
                            href = document.getElementById('arch_id_pdf').getAttribute('href');
                            inputIdName = 'Acta de Nacimiento';
                        break;
                        case 'arch_rfc':
                            href = document.getElementById('arch_rfc_pdf').getAttribute('href');
                            inputIdName = 'RFC';
                        break;
                        case 'arch_estudio':
                            href = document.getElementById('arch_estudio_pdf').getAttribute('href');
                            inputIdName = 'Comprobante de Estudios';
                        break;
                        case 'arch_curriculum_personal':
                            href = document.getElementById('arch_curriculum_personal_pdf').getAttribute('href');
                            inputIdName = 'Curriculum';
                        break;
                    }
                    if(href == null) {
                        e.preventDefault(); // Prevent form submission
                        alert('El campo de ' + inputIdName + ' esta vacio. Favor de subir el documento.');
                        return;
                    }
                }
            }
        });
    </script>
    <script>
        document.getElementById('toggleAcordeon').addEventListener('change', function() {
          var acordeon = document.getElementById('acordeonInstructor');
          if (this.checked) {
            acordeon.style.display = 'block';  // Muestra el acordeón
            $('#collapse1').collapse('show'); // Expande el acordeón automáticamente
            document.getElementById('form-domicilio').setAttribute('hidden', true);
          } else {
            acordeon.style.display = 'none';  // Oculta el acordeón
            $('#collapse1').collapse('hide'); // Colapsa el acordeón
            document.getElementById('form-domicilio').removeAttribute('hidden');
          }
        });

        function puestoOnOff(checkbox, selectId) {
            var select = document.getElementById(selectId);  // Obtener el select por su ID

            // if()

            if (checkbox.checked) {
                select.removeAttribute('hidden');  // Muestra el select cuando el checkbox está marcado
            } else {
                select.setAttribute('hidden', true);  // Oculta el select cuando el checkbox no está marcado
            }
        }

        function toggleOcupacion() {
            const ocupacion = document.getElementById('ocupacion').value;
            const labelOcupa = document.getElementById('label_ocupa');
            const sinOcupa = document.getElementById('sin_ocupa');
            const conOcupa = document.getElementById('con_ocupa');
            const ingresoMensual = document.getElementById('ingreso_mensual_div');
            const ingresoLabel = document.getElementById('ingreso_label');

            if (ocupacion === 'si') {
                labelOcupa.removeAttribute('hidden');
                conOcupa.removeAttribute('hidden');
                ingresoMensual.removeAttribute('hidden');
                ingresoLabel.removeAttribute('hidden');
                sinOcupa.setAttribute('hidden', true);
            } else if (ocupacion === 'no') {
                labelOcupa.removeAttribute('hidden');
                sinOcupa.removeAttribute('hidden');
                conOcupa.setAttribute('hidden', true);
                ingresoMensual.setAttribute('hidden', true);
                ingresoLabel.setAttribute('hidden', true);
            } else {
                labelOcupa.setAttribute('hidden', true);
                conOcupa.setAttribute('hidden', true);
                sinOcupa.setAttribute('hidden', true);
                ingresoMensual.setAttribute('hidden', true);
                ingresoLabel.setAttribute('hidden', true);
            }
        }
    </script>
    <script>
        document.getElementById('toggleAcordeon').addEventListener('change', function() {
          var acordeon = document.getElementById('acordeonInstructor');
          if (this.checked) {
            acordeon.style.display = 'block';  // Muestra el acordeón
            $('#collapse1').collapse('show'); // Expande el acordeón automáticamente
            // document.getElementById('form-domicilio').setAttribute('hidden', true);
          } else {
            acordeon.style.display = 'none';  // Oculta el acordeón
            $('#collapse1').collapse('hide'); // Colapsa el acordeón
            // document.getElementById('form-domicilio').removeAttribute('hidden');
          }
        });

        function puestoOnOff(checkbox, selectId) {
            var select = document.getElementById(selectId);  // Obtener el select por su ID

            // if()

            if (checkbox.checked) {
                select.removeAttribute('hidden');  // Muestra el select cuando el checkbox está marcado
            } else {
                select.setAttribute('hidden', true);  // Oculta el select cuando el checkbox no está marcado
            }
        }

        function toggleOcupacion() {
            const ocupacion = document.getElementById('ocupacion').value;
            const labelOcupa = document.getElementById('label_ocupa');
            const sinOcupa = document.getElementById('sin_ocupa');
            const conOcupa = document.getElementById('con_ocupa');
            const ingresoMensual = document.getElementById('ingreso_mensual_div');
            const ingresoLabel = document.getElementById('ingreso_label');

            if (ocupacion === 'si') {
                labelOcupa.removeAttribute('hidden');
                conOcupa.removeAttribute('hidden');
                ingresoMensual.removeAttribute('hidden');
                ingresoLabel.removeAttribute('hidden');
                sinOcupa.setAttribute('hidden', true);
            } else if (ocupacion === 'no') {
                labelOcupa.removeAttribute('hidden');
                sinOcupa.removeAttribute('hidden');
                conOcupa.setAttribute('hidden', true);
                ingresoMensual.setAttribute('hidden', true);
                ingresoLabel.setAttribute('hidden', true);
            } else {
                labelOcupa.setAttribute('hidden', true);
                conOcupa.setAttribute('hidden', true);
                sinOcupa.setAttribute('hidden', true);
                ingresoMensual.setAttribute('hidden', true);
                ingresoLabel.setAttribute('hidden', true);
            }
        }
    </script>
@endsection

