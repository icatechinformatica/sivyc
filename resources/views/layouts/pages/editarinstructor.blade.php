<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Edición de Instructor | Sivyc Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
<form action="{{ url('/instructor/guardar-mod') }}" method="post" id="registerinstructor" enctype="multipart/form-data">
    @csrf
    <div class="card-header">
        <h1>Editar Instructor<h1>
    </div>
    <div class="card card-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputobservacion" class="control-label"><b>Observaciones de Rechazo</b></label>
                <textarea cols="4" rows="4" type="text" class="form-control" readonly aria-required="true" id="observacion" name="observacion">{{$datains->rechazo}}</textarea>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Vista de Documentos</h2>
        <div class="form-row">
            @if ($datains->archivo_ine != NULL)
                <a class="btn btn-success" href={{$datains->archivo_ine}} target="_blank">Comprobante INE</a><br>
            @else
                <a class="btn" style="background-color: grey;" disabled>Comprobante INE</a><br>
            @endif
            @if ($datains->archivo_domicilio != NULL)
                <a class="btn btn-success" href={{$datains->archivo_domicilio}} target="_blank">Comprobante de Domicilio</a><br>
            @else
            <a class="btn" style="background-color: grey;" disabled>Comprobante de Domicilio</a><br>
            @endif
            @if ($datains->archivo_curp != NULL)
                <a class="btn btn-success" href={{$datains->archivo_curp}} target="_blank">CURP</a><br>
            @else
                <a class="btn" style="background-color: grey;" disabled>CURP</a><br>
            @endif
            @if ($datains->archivo_alta != NULL)
                <a class="btn btn-success" href={{$datains->archivo_alta}} target="_blank">Alta de Instructor</a><br>
            @else
                <a class="btn" style="background-color: grey;" disabled>Alta de Instructor</a><br>
            @endif
        </div>
        <br>
        <div class="form-row">
            @if ($datains->archivo_bancario != NULL)
                <a class="btn btn-success" href={{$datains->archivo_bancario}} target="_blank">Datos Bancarios</a><br>
            @else
                <a class="btn" style="background-color: grey;" disabled>Datos Bancarios</a><br>
            @endif
            @if ($datains->archivo_rfc != NULL)
                <a class="btn btn-success" href={{$datains->archivo_rfc}} target="_blank">RFC/Constancia Fiscal</a><br>
            @else
                <a class="btn" style="background-color: grey;" disabled>RFC/Constancia Fiscal</a><br>
            @endif
            @if ($datains->archivo_fotografia != NULL)
                <a class="btn btn-success" href={{$datains->archivo_fotografia}} target="_blank">Fotografía</a><br>
            @else
                <a class="btn" style="background-color: grey;" disabled>Fotografía</a><br>
            @endif
            @if ($datains->archivo_estudios != NULL)
                <a class="btn btn-success" href={{$datains->archivo_estudios}} target="_blank">Estudios</a><br>
            @else
                <a class="btn" style="background-color: grey;" disabled>Estudios</a><br>
            @endif
            @if ($datains->archivo_otraid != NULL)
                <a class="btn btn-success" href={{$datains->archivo_otraid}} target="_blank">Otra Identificación</a><br>
            @else
                <a class="btn" style="background-color: grey;" disabled>Otra Identificación</a><br>
            @endif
        </div>
        <hr style="border-color:dimgray">
        <div>
            <label><h2>Cambiar Documentos</h2></label>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="inputarch_ine">Archivo INE</label>
                <input type="file" accept="application/pdf" class="form-control" id="arch_ine" name="arch_ine" placeholder="Archivo PDF">
            </div>
            <div class="form-group col-md-3">
                <label for="inputarch_domicilio">Archivo Comprobante de Domicilio</label>
                <input type="file" accept="application/pdf" class="form-control" id="arch_domicilio" name="arch_domicilio" placeholder="Archivo PDF">
            </div>
            <div class="form-group col-md-3">
                <label for="inputarch_curp">Archivo CURP</label>
                <input type="file" accept="application/pdf" class="form-control" id="arch_curp" name="arch_curp" placeholder="Archivo PDF">
            </div>
            <div class="form-group col-md-3">
                <label for="inputarch_alta">Archivo Alta de Instructor</label>
                <input type="file" accept="application/pdf" class="form-control" id="arch_alta" name="arch_alta" placeholder="Archivo PDF">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="inputarch_banco">Archivo Datos Bancarios</label>
                <input type="file" accept="application/pdf" class="form-control" id="arch_banco" name="arch_banco" placeholder="Archivo PDF">
            </div>
            <div class="form-group col-md-3">
                <label for="inputarch_rfc">RFC/Constancia Fiscal</label>
                <input type="file" accept="application/pdf" class="form-control" id="arch_rfc" name="arch_rfc" placeholder="Archivo PDF">
            </div>
            <div class="form-group col-md-3">
                <label for="inputarch_foto">Archivo Fotografia</label>
                <input type="file" accept="image/jpeg" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF">
            </div>
            <div class="form-group col-md-3">
                <label for="inputarch_estudio">Archivo Grado de Estudios</label>
                <input type="file" accept="application/pdf" class="form-control" id="arch_estudio" name="arch_estudio" placeholder="Archivo PDF">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="inputarch_id">Archivo Otra Identificación</label>
                <input type="file" accept="application/pdf" class="form-control" id="arch_id" name="arch_id" placeholder="Archivo PDF">
            </div>
        </div>
        <hr style="border-color:dimgray">
        <div>
            <label><h2>Datos Personales</h2></label>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputnombre">Nombre</label>
                <input name='nombre' id='nombre' value="{{ $datains->nombre }}" type="text"  class="form-control" aria-required="true">
            </div>
            <div class="form-group col-md-4">
                <label for="inputapellido_paterno">Apellido Paterno</label>
                <input name='apellido_paterno' id='apellido_paterno' value="{{ $datains->apellidoPaterno }}" type="text" class="form-control" aria-required="true" >
            </div>
            <div class="form-group col-md-4">
                <label for="inputapellido_materno">Apellido Materno</label>
                <input name='apellido_materno' id='apellido_materno' value="{{ $datains->apellidoMaterno}}" type="text" class="form-control" aria-required="true" >
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputcurp">CURP</label>
                <input name='curp' id='curp' value="{{$datains->curp}}" type="text"  class="form-control"  aria-required="true">
            </div>
            <div class="form-group col-md-4">
                <label for="inputrfc">RFC/Constancia Fiscal</label>
                <input name='rfc' id='rfc' value="{{$datains->rfc}}" type="text"  class="form-control"  aria-required="true">
            </div>
            <div class="form-group col-md-4">
                <label for="inputfolio_ine">Folio INE</label>
                <input name='folio_ine' id='folio_ine' value="{{$datains->folio_ine }}" type="text"  class="form-control"  aria-required="true">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputsexo">Sexo</label>
                <input name='sexo' id='sexo' value="{{$datains->sexo}}" type="text"  class="form-control"  aria-required="true">
            </div>
            <div class="form-gorup col-md-4">
                <label for="inputestado_civil">Estado Civil</label>
                <select class="form-control" name="estado_civil" id="estado_civil">
                    <option value="">SELECCIONE</option>
                    @foreach ($ec as $item)
                        <option value="{{$item->nombre}}" @if($item->nombre == $datains->estado_civil) selected @endif>{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                <input name='fecha_nacimientoins' id='fecha_nacimientoins' value="{{$datains->fecha_nacimiento}}" type="date"  class="form-control" aria-required="true">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputentidad">Entidad</label>
                <select class="form-control" name="entidad" id="entidad" onchange="local2()">
                    <option value="">SELECCIONE</option>
                    @foreach ($estados as $cadwell)
                        <option value="{{$cadwell->id}}" @if($cadwell->nombre == $datains->entidad) selected @endif>{{$cadwell->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="inputmunicipio">Municipio</label>
                <select class="form-control" name="municipio" id="municipio" onchange="local()">
                    <option value="">Sin Especificar</option>
                    @foreach($municipios as $data)
                        <option value="{{$data->id}}" @if($data->muni == $datains->municipio) selected @endif>{{$data->muni}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="inputmunicipio">Localidad</label>
                <select class="form-control" name="localidad" id="localidad">
                    <option value="sin especificar">Sin Especificar</option>
                    @foreach($localidades as $localid)
                        <option value="{{$localid->clave}}" @if($localid->clave == $datains->clave_loc) selected @endif>{{$localid->localidad}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-7">
                <label for="inputdomicilio">Direccion de Domicilio</label>
                <input name="domicilio" id="domicilio" value="{{$datains->domicilio }}" type="text"  class="form-control" aria-required="true">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputtelefono">Numero de Telefono Personal</label>
                <input name="telefono" id="telefono" value="{{$datains->telefono }}" type="tel"  class="form-control" aria-required="true" required>
            </div>
            <div class="form-group col-md-6">
                <label for="inputcorreo">Correo Electronico</label>
                <input name="correo" id="correo" value="{{$datains->correo }}" type="email"  class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputbanco">Nombre del Banco</label>
                <input name="banco" id="banco" value="{{$datains->banco }}" type="text"  class="form-control" aria-required="true">
            </div>
            <div class="form-group col-md-4">
                <label for="inputclabe">Clabe Interbancaria</label>
                <input name="clabe" id="clabe" value="{{$datains->interbancaria }}" type="text"  class="form-control" aria-required="true">
            </div>
            <div class="form-group col-md-4">
                <label for="inputnumero_cuenta">Numero de Cuenta</label>
                <input name="numero_cuenta" value="{{$datains->no_cuenta }}" id="numero_cuenta" type="text"  class="form-control" aria-required="true">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="extracurricular"><h3>Registro de Capacitador Externo STPS</h3></label>
                <textarea name="stps" id="stps" cols="6" rows="4" class="form-control">{{$datains->stps}}</textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="extracurricular"><h3>Estandar CONOCER</h3></label>
                <textarea name="conocer" id="conocer" cols="6" rows="4" class="form-control">{{$datains->conocer}}</textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="extracurricular"><h3>Datos Extracurriculares</h3></label>
                <textarea name="extracurricular" id="extracurricular" cols="6" rows="10" class="form-control">{{$datains->extracurricular}}</textarea>
            </div>
        </div>
        <input name="id" id="id" type="text" hidden value="{{$datains->id}}">
        <br>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" >Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('script_content_js')
    <script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
    <script>
        function local() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("municipio").value;
            var datos = {valor: valor};
            console.log('hola');
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
    </script>
@endsection

