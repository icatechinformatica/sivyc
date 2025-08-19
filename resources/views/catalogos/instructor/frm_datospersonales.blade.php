{{-- Datos personales del instructor --}}
<div>
    <h3 class="font-weight-bold">Datos Personales</h3>
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="inputnombre">Nombre</label>
            <input name='nombre' id='nombre' type="text" class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-3">
            <label for="inputapellido_paterno">Apellido Paterno</label>
            <input name='apellido_paterno' id='apellido_paterno' type="text" class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-3">
            <label for="inputapellido_materno">Apellido Materno</label>
            <input name='apellido_materno' id='apellido_materno' type="text" class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-3">
            <label for="inputcurp">CURP</label>
            <input name='curp' id='curp' type="text" class="form-control" aria-required="true">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="inputrfc">RFC/Constancia Fiscal</label>
            <input name='rfc' id='rfc' type="text" class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-3">
            <label for="inputhonorarios">Regimen</label>
            <select class="form-control" name="honorario" id="honorario">
                <option value="">Sin Especificar</option>
                @foreach ($lista_regimen as $regimen)
                    <option value="{{$regimen->concepto}}">{{$regimen->concepto}}</option>
                @endforeach
                {{-- <option value="HONORARIOS">Honorarios</option> --}}
                {{-- <option value="ASIMILADOS A SALARIOS">Asimilados a Salarios</option> --}}
                {{-- <option value="HONORARIOS Y ASIMILADOS A SALARIOS">Honorarios y Asimilado a Salarios</option> --}}
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="inputhonorarios">Tipo de Instructor</label>
            <select class="form-control" name="tipo_instructor" id="tipo_instructor">
                <option value="">Sin Especificar</option>
                <option value="INTERNO">INTERNO</option>
                <option value="EXTERNO">EXTERNO</option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="inputtipo_identificacion">Tipo de Identificación</label>
            <select class="form-control" name="tipo_identificacion" id="tipo_identificacion">
                <option value="">SIN ESPECIFICAR</option>
                <option value="INE">INE</option>
                <option value="PASAPORTE">PASAPORTE</option>
                <option value="LICENCIA DE CONDUCIR">LICENCIA DE CONDUCIR</option>
                <option value="CARTILLA MILITAR">CARTILLA MILITAR</option>
                <option value="CEDULA PROFESIONAL">CEDULA PROFESIONAL</option>
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="inputfolio_ine">Folio de Identificación</label>
            <input name='folio_ine' id='folio_ine' type="text" class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-3">
            <label for="inputexpiracion_identificacion">Expiración de Identificación</label>
            <input name='expiracion_identificacion' id='expiracion_identificacion' type="date" class="form-control" aria-required="true" required>
        </div>
        <div class="form-group col-md-3">
           <label for="inputsexo">Sexo</label>
            <select class="form-control" name="sexo" id="sexo">
                <option value="">SELECCIONE</option>
                <option value='MASCULINO'>Masculino</option>
                <option value='FEMENINO'>Femenino</option>
            </select>
        </div>
        <div class="form-gorup col-md-3">
            <label for="inputestado_civil">Estado Civil</label>
            <select class="form-control" name="estado_civil" id="estado_civil">
                <option value="">SELECCIONE</option>
                @foreach ($lista_civil as $item)
                    <option value="{{$item->nombre}}">{{$item->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
            <input name='fecha_nacimientoins' id='fecha_nacimientoins' type="date" class="form-control" aria-required="true">
        </div>
        {{-- pendiente --}}
        <div class="form-group col-md-3">
            <label for="inputtelefono">Teléfono</label>
            <input name="telefono" id="telefono" type="tel" class="form-control" aria-required="true" required>
        </div>
        <div class="form-group col-md-3">
            <label for="inputtelefono">Teléfono de Casa</label>
            <input name="telefono_casa" id="telefono_casa" type="tel" class="form-control" aria-required="true" required>
        </div>
        <div class="form-group col-md-3">
            <label for="inputcorreo">Correo Electrónico</label>
            <input name="correo" id="correo" type="email" class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="inputentidad">Entidad de Residencia</label>
            <select class="form-control" name="entidad" id="entidad" onchange="local2()">
                <option value="">SELECCIONE</option>
                @foreach ($estados as $cadwell)
                    <option value="{{$cadwell->id}}">{{$cadwell->nombre}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="inputmunicipio">Municipio de Residencia</label>
            <select class="form-control" name="municipio" id="municipio" onchange="local()">
                <option value="">Sin Especificar</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="inputbanco">Dirección de Domicilio</label>
            <input name="domicilio" id="domicilio" type="text" class="form-control" aria-required="true">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="inputbanco">Código Postal</label>
            <input name="codigo_postal" id="codigo_postal" type="text" class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-3">
            <label for="inputbanco">Nombre del Banco</label>
            <select class="form-control" name="banco" id="banco">
                <option value="">SELECCIONE EL BANCO</option>
                @foreach ($bancos as $juicy)
                    <option value="{{$juicy->nombre}}">{{$juicy->nombre}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="inputclabe">Clabe Interbancaria</label>
            <input name="clabe" id="clabe" type="text" class="form-control" aria-required="true">
        </div>
        <div class="form-group col-md-3">
            <label for="inputnumero_cuenta">Número de Cuenta</label>
            <input name="numero_cuenta" id="numero_cuenta" type="text" class="form-control" aria-required="true">
        </div>
    </div>
</div>
