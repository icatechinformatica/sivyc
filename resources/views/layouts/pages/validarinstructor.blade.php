<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Validación de Instructor | Sivyc Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
<div class="card-header">
    <h1>Validación de Instructor<h1>
</div>
<div class="card card-body">
    <h2>Vista de Documentos</h2>
    <div class="form-row">
        @if ($getinstructor->archivo_ine != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_ine}} target="_blank">Comprobante INE</a><br>
        @else
            <a class="btn" style="background-color: grey;" disabled>Comprobante INE</a><br>
        @endif
        @if ($getinstructor->archivo_domicilio != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_domicilio}} target="_blank">Comprobante de Domicilio</a><br>
        @else
        <a class="btn" style="background-color: grey;" disabled>Comprobante de Domicilio</a><br>
        @endif
        @if ($getinstructor->archivo_curp != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_curp}} target="_blank">CURP</a><br>
        @else
            <a class="btn" style="background-color: grey;" disabled>CURP</a><br>
        @endif
        @if ($getinstructor->archivo_alta != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_alta}} target="_blank">Alta de Instructor</a><br>
        @else
            <a class="btn" style="background-color: grey;" disabled>Alta de Instructor</a><br>
        @endif
    </div>
    <br>
    <div class="form-row">
        @if ($getinstructor->archivo_bancario != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_bancario}} target="_blank">Datos Bancarios</a><br>
        @else
            <a class="btn" style="background-color: grey;" disabled>Datos Bancarios</a><br>
        @endif
        @if ($getinstructor->archivo_rfc != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_rfc}} target="_blank">RFC/Constancia Fiscal</a><br>
        @else
            <a class="btn" style="background-color: grey;" disabled>RFC/Constancia Fiscal</a><br>
        @endif
        @if ($getinstructor->archivo_fotografia != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_fotografia}} target="_blank">Fotografía</a><br>
        @else
            <a class="btn" style="background-color: grey;" disabled>Fotografía</a><br>
        @endif
        @if ($getinstructor->archivo_estudios != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_estudios}} target="_blank">Estudios</a><br>
        @else
            <a class="btn" style="background-color: grey;" disabled>Estudios</a><br>
        @endif
        @if ($getinstructor->archivo_otraid != NULL)
            <a class="btn btn-success" href={{$getinstructor->archivo_otraid}} target="_blank">Otra Identificación</a><br>
        @else
            <a class="btn" style="background-color: grey;" disabled>Otra Identificación</a><br>
        @endif
    </div>
    <hr style="border-color:dimgray">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <button type="button" class="btn btn-danger btn-lg"
                    data-toggle="modal" data-placement="top"
                    data-target="#RechazarModal"
                    data-id='{{$getinstructor->id}}'>
                    <i class="fa fa-remove"></i> &nbsp Rechazar
                </button>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-success btn-lg"
                    data-toggle="modal" data-placement="top"
                    data-target="#ValidarModal"
                    data-id='{{$getinstructor->id}}'>
                    <i class="fa fa-check"></i> &nbsp Validar
                </button>
            </div>
        </div>
    </div>
    <div>
        <label><h2>Datos Personales</h2></label>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="inputnombre">Nombre</label>
            <input name='nombre' id='nombre' value="{{ $getinstructor->nombre }}" type="text" disabled class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-4">
            <label for="inputapellido_paterno">Apellido Paterno</label>
            <input name='apellido_paterno' id='apellido_paterno' value="{{ $getinstructor->apellidoPaterno }}" type="text" class="form-control" aria-required="true" disabled>
        </div>
        <div class="form-group col-md-4">
            <label for="inputapellido_materno">Apellido Materno</label>
            <input name='apellido_materno' id='apellido_materno' value="{{ $getinstructor->apellidoMaterno}}" type="text" class="form-control" aria-required="true" disabled>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="inputcurp">CURP</label>
            <input name='curp' id='curp' value="{{$getinstructor->curp}}" type="text" disabled class="form-control" disabled aria-required="true">
        </div>
        <div class="form-group col-md-4">
            <label for="inputrfc">RFC/Constancia Fiscal</label>
            <input name='rfc' id='rfc' value="{{$getinstructor->rfc}}" type="text" disabled class="form-control" disabled aria-required="true">
        </div>
        <div class="form-group col-md-4">
            <label for="inputfolio_ine">Folio INE</label>
            <input name='folio_ine' id='folio_ine' value="{{$getinstructor->folio_ine }}" type="text" disabled class="form-control" disabled aria-required="true">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="inputsexo">Sexo</label>
            <input name='sexo' id='sexo' value="{{$getinstructor->sexo}}" type="text" disabled class="form-control" disabled aria-required="true">
        </div>
        <div class="form-gorup col-md-4">
            <label for="inputestado_civil">Estado Civil</label>
            <input name='estado_civil' id='estado_civil' value="{{$getinstructor->estado_civil }}" type="text" disabled class="form-control" disabled aria-required="true">
        </div>
        <div class="form-group col-md-4">
            <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
            <input name='fecha_nacimientoins' id='fecha_nacimientoins' value="{{$getinstructor->fecha_nacimiento}}" type="date" disabled class="form-control" aria-required="true">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="inputentidad">Entidad</label>
            <input name='entidad' id='entidad' value="{{$getinstructor->entidad}}" type="text" disabled class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-4">
            <label for="inputmunicipio">Municipio</label>
            <input name='municipio' id='municipio' value="{{$getinstructor->municipio}}" type="text" disabled class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-4">
            <label for="inputmunicipio">Localidad</label>
            <input name='localidad' id='localidad' value="{{$getinstructor->localidad}}" type="text" disabled class="form-control" aria-required="true">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-7">
            <label for="inputdomicilio">Direccion de Domicilio</label>
            <input name="domicilio" id="domicilio" value="{{$getinstructor->domicilio }}" type="text" disabled class="form-control" aria-required="true">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="inputtelefono">Numero de Telefono Personal</label>
            <input name="telefono" id="telefono" value="{{$getinstructor->telefono }}" type="tel" disabled class="form-control" aria-required="true" required>
        </div>
        <div class="form-group col-md-6">
            <label for="inputcorreo">Correo Electronico</label>
            <input name="correo" id="correo" value="{{$getinstructor->correo }}" type="email" disabled class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true" required>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="inputbanco">Nombre del Banco</label>
            <input name="banco" id="banco" value="{{$getinstructor->banco }}" type="text" disabled class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-4">
            <label for="inputclabe">Clabe Interbancaria</label>
            <input name="clabe" id="clabe" value="{{$getinstructor->interbancaria }}" type="text" disabled class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-4">
            <label for="inputnumero_cuenta">Numero de Cuenta</label>
            <input name="numero_cuenta" value="{{$getinstructor->no_cuenta }}" id="numero_cuenta" type="text" disabled class="form-control" aria-required="true">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="extracurricular"><h3>Registro de Capacitador Externo STPS</h3></label>
            <textarea name="stps" id="stps" cols="6" rows="4" class="form-control">{{$getinstructor->stps}}</textarea>
        </div>
        <div class="form-group col-md-6">
            <label for="extracurricular"><h3>Estandar CONOCER</h3></label>
            <textarea name="conocer" id="conocer" cols="6" rows="4" class="form-control">{{$getinstructor->conocer}}</textarea>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="extracurricular"><h3>Datos Extracurriculares</h3></label>
            <textarea name="extracurricular" id="extracurricular" cols="6" rows="10" class="form-control">{{$getinstructor->extracurricular}}</textarea>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
            </div>
        </div>
    </div>
</div>
<!-- Modal RECHAZAR-->
<div class="modal fade" id="RechazarModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Rechazo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center">
                <div style="text-align:center" class="form-group">
                    <div class="modal-body">
                        ¿ Estás seguro de rechazar al instructor ?
                    </div>
                    <form method="POST" action="{{ route('instructor-rechazo') }}">
                    @csrf
                        <textarea name="observacion" id="observacion" cols="45" rows="5"></textarea>
                        <br>
                        <input type="text" name="idinsrec" id="idinsrec" hidden>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Rechazar</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal VALIDAR-->
<div class="modal fade" id="ValidarModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Validación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center">
                <div style="text-align:center" class="form-group">
                    <div class="modal-body">
                        ¿ Estás seguro de validar el instructor ?
                    </div>
                    <form method="POST" action="{{ route('instructor-validado') }}">
                        @csrf
                        <input type="text" name="idins" id="idins" hidden>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Validar</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- END -->
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
<script>
    function local() {
        // var x = document.getElementById("municipio").value;
        // console.log(x);

        var valor = document.getElementById("municipio").value;
        var datos = {valor: valor};
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
            // console.log(il);
            // console.log( respuesta[1].id)
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

    $(function()
    {
        $('#ValidarModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        $('#idins').val(id);
        });

        $('#RechazarModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        $('#idinsrec').val(id);
        });
    });
</script>
@endsection
