@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{ asset('css/global.css') }}" />
<link rel="stylesheet" href="{{ asset('css/alumnos/consulta.css') }}" />
<link rel="stylesheet" href="{{ asset('css/stepbar.css') }}" />
<style>
    .notyf-warning {
        background-color: #fedc2e !important;
        color: #fff !important;
        border: 1px solid #ffe58f !important;
        font-size: 1.2em !important;
        font-weight: bold;
        text-shadow: 0 1px 2px #ad8b00;
        padding: 18px 28px;
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
<input type="hidden" id="esNuevoRegistro" value="{{ $esNuevoRegistro ? 'true' : 'false' }}" />
@php
$documentos = $esNuevoRegistro ? [] : json_decode($datos->archivos_documentos ?? '[]', true);
$datosCerss = $esNuevoRegistro ? [] : json_decode($datos->cerss ?? '[]', true);

// Obtener grupos vulnerables seleccionados del alumno
$gruposVulnerablesSeleccionados = [];
if (!$esNuevoRegistro && $datos) {
    $gruposVulnerablesSeleccionados = $datos->gruposVulnerables->pluck('id_grupo_vulnerable')->toArray();
}
@endphp

<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-12">
        <span>{{ $esNuevoRegistro ? 'Registrar nuevo alumno' : 'Editar alumno' }}</span>
    </div>
</div>

<div class="card card-body" id="formulario-alumno">
    {{-- Formulario de Datos Personales --}}
    <div class="row">
        <!-- Step Progress y contenido principal -->
        <div class="col-md-3 d-none d-md-block">
            <nav id="step-progress" class="nav-sticky">
                <ul class="list-group list-group-flush step-progress-nav">
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="datos-personales">
                        <span class="step-circle mr-2">1</span>
                        <span class="fw-bold text-black text-uppercase">DATOS PERSONALES</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="domicilio_section">
                        <span class="step-circle mr-2">2</span>
                        <span class="fw-bold text-uppercase">DOMICILIO</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="contacto">
                        <span class="step-circle mr-2">3</span>
                        <span class="fw-bold text-uppercase">CONTACTO</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="grupos-vulnerables">
                        <span class="step-circle mr-2">4</span>
                        <span class="fw-bold text-uppercase">GRUPOS VULNERABLES</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="capacitacion">
                        <span class="step-circle mr-2">5</span>
                        <span class="fw-bold text-uppercase">CAPACITACIÓN</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="laboral">
                        <span class="step-circle mr-2">6</span>
                        <span class="fw-bold text-uppercase">LABORAL</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="cerss">
                        <span class="step-circle mr-2">7</span>
                        <span class="fw-bold text-uppercase">CERSS</span>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="col-md-9">
            {{-- * Sección: Datos personales --}}
            {!! html()->form()->id('form-datos-personales')->open() !!}
            {{ html()->hidden('id_usuario_captura', auth()->user()->id) }}
            <div class="col-12 mb-4 step-section" id="datos-personales">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-person-vcard mr-2"></i>Datos personales
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            {!! html()->label('CURP')->for('curp') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="curp-addon"><i class="bi bi-credit-card-2-front"></i></span>
                                </div>
                                {{ html()->text('curp')->class('form-control')->id('curp')->isReadonly(true)->value($esNuevoRegistro ? $curp : $datos->curp) }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Adjuntar Documento CURP')->for('documento_curp') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="documento-curp-addon"><i class="bi bi-file-earmark-text"></i></span>
                                </div>
                                {!! html()->file('documento_curp')->class('form-control')->id('documento_curp')->attribute('aria-label', 'Adjuntar Documento CURP')->attribute('aria-describedby', 'documento-curp-addon')->attribute('accept', '.pdf,application/pdf') !!}
                            </div>

                            {{-- * Input para mostrar el documento CURP existente --}}
                            @if (!empty($documentos['curp']))
                            <small class="form-text text-muted mt-1">
                                <a href="{{ asset('storage/' . $documentos['curp']['ruta']) }}" target="_blank"
                                    class="text-primary text-decoration-underline">Ver documento CURP</a>
                            </small>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Fecha de Expedición CURP')->for('fecha_documento_curp') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="fecha-curp-addon"><i class="bi bi-calendar"></i></span>
                                </div>
                                {!! html()->date('fecha_documento_curp')->class('form-control')->id('fecha_documento_curp')->attribute('aria-label', 'Fecha de Expedición CURP')->attribute('aria-describedby', 'fecha-curp-addon')
                                        ->value(!$esNuevoRegistro && !empty($documentos['curp']['fecha_expedicion']) ? date('Y-m-d', strtotime($documentos['curp']['fecha_expedicion'])) : '' ) !!}
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Nombre')->for('nombre_s') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="nombre-addon"><i class="bi bi-person"></i></span>
                                </div>
                                {!! html()->text('nombre_s')->class('form-control')->id('nombre_s')->attribute('aria-label', 'Nombre')->value(!$esNuevoRegistro ? $datos->nombre : '') !!}
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Primer Apellido')->for('primer_apellido') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="primer-apellido-addon"><i
                                            class="bi bi-person-lines-fill"></i></span>
                                </div>
                                {!!
                                html()->text('primer_apellido')->class('form-control')->id('primer_apellido')->attribute('aria-label',
                                'Primer Apellido')->attribute('aria-describedby',
                                'primer-apellido-addon')->value(!$esNuevoRegistro ? $datos->apellido_paterno : '') !!}
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Segundo Apellido')->for('segundo_apellido') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="segundo-apellido-addon"><i class="bi bi-person-lines-fill"></i></span>
                                </div>
                                {!! html()->text('segundo_apellido')->class('form-control')->id('segundo_apellido')->attribute('aria-label', 'Segundo Apellido')->attribute('aria-describedby', 'segundo-apellido-addon')
                                        ->value(!$esNuevoRegistro ? $datos->apellido_materno : '') !!}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Entidad de Nacimiento')->for('entidad_de_nacimiento_select') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="entidad-addon"><i class="bi bi-geo-alt"></i></span>
                                </div>
                                {!! html()->select('entidad_de_nacimiento_select', [null => 'SELECCIONE LA ENTIDAD'] + $entidades->pluck('nombre', 'id')->toArray())->class('form-control')->id('entidad_de_nacimiento_select')->attribute('aria-label', 'Entidad de Nacimiento')->attribute('aria-describedby', 'entidad-addon')
                                        ->value(!$esNuevoRegistro ? $datos->entidad_de_nacimiento : '') !!}
                                {!! html()->text('entidad_de_nacimiento')->class('form-control')->id('entidad_de_nacimiento')->attribute('aria-label', 'Entidad de Nacimiento')->attribute('aria-describedby', 'entidad-addon') !!}

                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Fecha de Nacimiento')->for('fecha_de_nacimiento') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="fecha-addon"><i class="bi bi-calendar-date"></i></span>
                                </div>
                                {!! html()->text('fecha_de_nacimiento')->class('form-control')->id('fecha_de_nacimiento')->attribute('aria-label', 'Fecha de Nacimiento')->attribute('aria-describedby', 'fecha-addon')
                                        ->value(!$esNuevoRegistro ? $datos->fecha_nacimiento : '') !!}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Sexo')->for('sexo') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="sexo-addon"><i class="bi bi-gender-ambiguous"></i></span>
                                </div>

                                {!! html()->select('sexo_select', [null => 'SELECCIONE EL SEXO'] + $sexos->pluck('sexo', 'id')->toArray())->class('form-control')->id('sexo_select')->attribute('aria-label', 'Sexo')->attribute('aria-describedby', 'sexo-addon')
                                        ->value(!$esNuevoRegistro ? $datos->sexo->id : '') !!}
                                {!! html()->text('sexo_input')->id('sexo_input')->class('form-control')->value(!$esNuevoRegistro ? $datos->sexo->sexo : '') !!}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Nacionalidad')->for('nacionalidad_select') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="nacionalidad-addon"><i class="bi bi-flag"></i></span>
                                </div>
                                {!! html()->select( 'nacionalidad_select', [null => 'SELECCIONE LA NACIONALIDAD'] + $nacionalidades->pluck('nacionalidad', 'id_nacionalidad')->toArray())->class('form-control')->id('nacionalidad_select')->attribute('aria-label','Nacionalidad')->attribute('aria-describedby', 'nacionalidad-addon')
                                        ->value(!$esNuevoRegistro ? $datos->nacionalidad->id_nacionalidad : '') !!}
                                {!! html()->text('nacionalidad_input')->id('nacionalidad_input')->class('form-control')
                                        ->value(!$esNuevoRegistro ? $datos->nacionalidad->nacionalidad : '') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Estado Civil')->for('estado_civil') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="estado-civil-addon"><i class="bi bi-people"></i></span>
                                </div>
                                {!! html()->select('estado_civil_select', ['' => 'Seleccionar'] + $estadosCiviles->pluck('nombre', 'id')->toArray())->class('form-control')->id('estado_civil_select')->attribute('aria-label', 'Estado Civil')->attribute('aria-describedby', 'estado-civil-addon')->attribute('required', true)->value(!$esNuevoRegistro ? $datos->estadoCivil->id : '') !!}
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Guardar datos personales')->class('btn btn-primary float-end guardar-seccion rounded')->id('validar-datos-personales')->type('button') }}
                        </div>
                    </div>
                </div>
            </div>
            {!! html()->form()->close() !!}

            {{-- * Formulario de Domicilio --}}
            {!! html()->form()->id('form-domicilio')->open() !!}
            <div class="col-12 mb-4 step-section" id="domicilio_section">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-house-door mr-2"></i>Domicilio</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            {!! html()->label('País')->for('pais') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="pais-addon"><i class="bi bi-globe2"></i></span>
                                </div>
                                {!! html()->select('pais_select', [null => 'SELECCIONE EL PAÍS'] + $paises->pluck('nombre', 'id')->toArray())->class('form-control')->id('pais_select')->value(!$esNuevoRegistro && $datos->pais ? $datos->pais->id : '') !!}
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Estado')->for('estado') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="estado-addon"><i class="bi bi-map"></i></span>
                                </div>
                                {!! html()->select('estado_select', [null => 'SELECCIONE EL ESTADO'] + $estados->pluck('nombre', 'id')->toArray())->class('form-control')->id('estado_select')->attribute('aria-label', 'Estado')->attribute('aria-describedby', 'estado-addon')->attribute('data-selected-estado', (!$esNuevoRegistro && $datos->estado ? $datos->estado->id : ''))
                                        ->value(!$esNuevoRegistro && $datos->estado ? $datos->estado->id : '') !!}
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Municipio')->for('municipio') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="municipio-addon"><i class="bi bi-building"></i></span>
                                </div>
                                {!! html()->select('municipio_select', [null => 'SELECCIONE EL MUNICIPIO'] + $municipios->pluck('muni', 'id')->toArray())->class('form-control')->id('municipio_select')->attribute('aria-label','Municipio')->attribute('aria-describedby', 'municipio-addon')->attribute('data-selected-municipio', (!$esNuevoRegistro && $datos->municipio ? $datos->municipio->id : ''))
                                    ->value(!$esNuevoRegistro && $datos->municipio ? $datos->municipio->id : '') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Localidad')->for('localidad') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="localidad-addon"><i class="bi bi-geo"></i></span>
                                </div>
                                {!! html()->text('localidad')->class('form-control')->id('localidad')->attribute('aria-label', 'Localidad')->attribute('aria-describedby', 'localidad-addon')
                                        ->value($esNuevoRegistro ? '' : $datos->clave_localidad) !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Código Postal')->for('codigo_postal') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="cp-addon"><i class="bi bi-mailbox"></i></span>
                                </div>
                                {!! html()->text('codigo_postal')->class('form-control')->id('codigo_postal')->attribute('aria-label', 'Código Postal')->attribute('aria-describedby', 'cp-addon')
                                        ->value($esNuevoRegistro ? '' : $datos->cp) !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Domicilio')->for('domicilio') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="domicilio-addon"><i class="bi bi-geo-alt"></i></span>
                                </div>
                                {!! html()->text('domicilio')->class('form-control')->id('domicilio')->attribute('aria-label', 'Domicilio')->attribute('aria-describedby', 'domicilio-addon')
                                        ->value($esNuevoRegistro ? '' : $datos->domicilio) !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Colonia')->for('colonia') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="colonia-addon"><i class="bi bi-signpost"></i></span>
                                </div>
                                {!! html()->text('colonia')->class('form-control')->id('colonia')->attribute('aria-label', 'Colonia')->attribute('aria-describedby', 'colonia-addon')
                                        ->value($esNuevoRegistro ? '' : $datos->colonia) !!}
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Guardar domicilio')->class('btn btn-primary float-end')->id('validar-domicilio')->type('button') }}
                        </div>
                    </div>
                </div>
            </div>
            {!! html()->form()->close() !!}

            {{-- * Formulario de Contacto --}}
            {!! html()->form()->id('form-contacto')->open() !!}
            <div class="col-12 mb-4 step-section" id="contacto">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-telephone mr-2"></i>Contacto</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Teléfono Casa')->for('telefono_casa') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="tel-casa-addon"><i class="bi bi-telephone"></i></span>
                                </div>
                                {!! html()->number('telefono_casa')->class('form-control')->id('telefono_casa')->attribute('aria-label', 'Teléfono Casa')->attribute('aria-describedby', 'tel-casa-addon')->attribute('maxlength', '10')
                                    ->value($esNuevoRegistro ? '' : $datos->telefono_casa) !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Teléfono Celular')->for('telefono_celular') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="tel-cel-addon"><i class="bi bi-phone"></i></span>
                                </div>
                                {!! html()->number('telefono_celular')->class('form-control')->id('telefono_celular')->attribute('aria-label', 'Teléfono Celular')->attribute('aria-describedby', 'tel-cel-addon')->attribute('maxlength', '10')
                                    ->value($esNuevoRegistro ? '' : $datos->telefono_celular) !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Correo Electrónico')->for('correo_electronico') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="correo-addon"><i class="bi bi-envelope"></i></span>
                                </div>
                                {!! html()->email('correo_electronico')->class('form-control')->id('correo_electronico')->attribute('aria-label', 'Correo Electrónico')->attribute('aria-describedby', 'correo-addon')
                                        ->value($esNuevoRegistro ? '' : $datos->correo) !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Facebook')->for('facebook') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="facebook-addon"><i class="bi bi-facebook"></i></span>
                                </div>
                                {!! html()->text('facebook')->class('form-control')->id('facebook')->attribute('aria-label', 'Facebook')->attribute('aria-describedby', 'facebook-addon')
                                        ->value($esNuevoRegistro ? '' : $datos->facebook) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3 d-flex align-items-center"
                                style="background: #fffbe6; border: 2px solid #ffe58f; border-radius: 8px; padding: 12px;">
                                {!! html()->label('¿AUTORIZA SU NÚMERO PARA ALGUNA OPORTUNIDAD EN LA BOLSA DE TRABAJO?')->for('autoriza_bolsa_trabajo')->class('fw-bold me-3
                                mb-0')->style('color: #ad8b00; font-size: 1.1em;') !!}
                                <div class="form-check ml-2">
                                    {!! html()->checkbox('autoriza_bolsa_trabajo', false, 1)->class('form-check-input')->id('autoriza_bolsa_trabajo')
                                        ->checked($esNuevoRegistro ? false : $datos->check_bolsa) !!}
                                    {!! html()->label('Sí')->for('autoriza_bolsa_trabajo')->class('form-check-label') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Guardar contacto')->class('btn btn-primary float-end')->id('validar-contacto')->type('button') }}
                        </div>
                    </div>
                </div>
            </div>
            {!! html()->form()->close() !!}

            {{-- * Formulario de Grupos Vulnerables --}}
            {!! html()->form()->id('form-grupos-vulnerables')->open() !!}
            <div class="col-12 mb-4 step-section" id="grupos-vulnerables">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-exclamation-triangle mr-2"></i>Grupos vulnerables</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check my-4 border-bottom pb-4">
                                {!! html()->checkbox('pertenece_a_grupo_vulnerable')->class('form-check-input')->id('pertenece_a_grupo_vulnerable')
                                        ->checked($esNuevoRegistro ? false : !$datos->vulnerable && empty($gruposVulnerablesSeleccionados)) !!}
                                {!! html()->label('NO PERTENEZCO A UN GRUPO VULNERABLE')->for('pertenece_a_grupo_vulnerable')->class('form-check-label ml-2') !!}
                            </div>
                        </div>
                        {{-- ? Generar checkboxes dinámicamente en 3 columnas --}}
                        @php
                        $gruposChunks = $gruposVulnerables->chunk(ceil($gruposVulnerables->count() / 3));
                        @endphp

                        @foreach ($gruposChunks as $chunk)
                        <div class="col-md-4">
                            @foreach ($chunk as $grupo)
                            <div class="form-check my-4">
                                {!!
                                html()->checkbox('grupos_vulnerables[]')->value($grupo->id_grupo_vulnerable)->class('form-check-input')->id('grupo_vulnerable_'. $grupo->id_grupo_vulnerable)
                                    ->checked(in_array($grupo->id_grupo_vulnerable, $gruposVulnerablesSeleccionados)) !!}
                                {!! html()->label(strtoupper($grupo->grupo_vulnerable))->for('grupo_vulnerable_' . $grupo->id_grupo_vulnerable)->class('form-check-label ml-2') !!}
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Guardar grupos vulnerables')->class('btn btn-primary float-end')->id('validar-grupos-vulnerables')->type('button') }}
                        </div>
                    </div>
                </div>
            </div>
            {!! html()->form()->close() !!}

            {{-- * Formulario de Capacitación --}}
            {!! html()->form()->id('form-capacitacion')->open() !!}
            <div class="col-12 mb-4 step-section" id="capacitacion">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-info-circle mr-2"></i>DE LA CAPACITACIÓN </h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            {!! html()->label('ULTIMO GRADO DE ESTUDIOS')->for('ultimo_grado_estudios') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="grado-estudios-addon"><i class="bi bi-mortarboard"></i></span>
                                </div>
                                {!! html()->select('ultimo_grado_estudios', ['' => 'SELECCIONE EL GRADO'] + $gradoEstudios->pluck('grado_estudio', 'id_grado_estudio')->toArray() )->class('form-control')->id('ultimo_grado_estudios')->attribute('aria-label', 'Ultimo Grado de Estudios')->attribute('aria-describedby', 'grado-estudios-addon')
                                        ->value(!$esNuevoRegistro && $datos->gradoEstudio ? $datos->gradoEstudio->id_grado_estudio : '') !!}
                            </div>
                        </div>
                        <div class="col-md-5 mb-3">
                            {!! html()->label('DOCUMENTO DEL ULTIMO GRADO DE ESTUDIOS')->for('documento_ultimo_grado') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="documento-grado-addon"><i class="bi bi-award"></i></span>
                                </div>
                                {!! html()->file('documento_ultimo_grado')->class('form-control')->id('documento_ultimo_grado')->attribute('aria-label', 'Documento del Ultimo Grado de Estudios')->attribute('aria-describedby', 'documento-grado-addon')->attribute('accept', '.pdf,application/pdf') !!}
                            </div>
                            @if (!empty($documentos['ultimo_grado_estudio']))
                            <small class="form-text text-muted mt-1">
                                <a href="{{ url('storage/' . $documentos['ultimo_grado_estudio']['ruta']) }}" target="_blank" class="text-primary text-decoration-underline">Ver documento del Ultimo Grado de Estudios</a>
                            </small>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('FECHA DEL DOCUMENTO')->for('fecha_documento_ultimo_grado') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="fecha-documento-addon"><i class="bi bi-calendar"></i></span>
                                </div>
                                {!! html()->date('fecha_documento_ultimo_grado')->class('form-control')->id('fecha_documento_ultimo_grado')->attribute('aria-label', 'Fecha del Documento')->attribute('aria-describedby', 'fecha-documento-addon')
                                        ->value(!$esNuevoRegistro && !empty($documentos['ultimo_grado_estudio']['fecha_expedicion']) ? date('Y-m-d', strtotime($documentos['ultimo_grado_estudio']['fecha_expedicion'])) : '') !!}
                            </div>
                        </div>
                        <div class="col-md-9 mb-3">
                            {!! html()->label('MEDIO POR EL QUE SE ENTERO DEL SISTEMA')->for('medio_enterado_sistema') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="medio-enterado-addon"><i class="bi bi-megaphone"></i></span>
                                </div>
                                {!! html()->select('medio_enterado_sistema', ['' => 'SELECCIONA UN MEDIO', 1 => 'INTERNET', 2 => 'PRENSA', 3 => 'RADIO', 4 => 'TELEVISIÓN', 5 => 'FOLLETOS, CARTELES, VOLANTES.', 6 => 'OTROS'])->class('form-control')->id('medio_enterado_sistema')->attribute('aria-label', 'Medio por el que se enteró del sistema')->attribute('aria-describedby', 'medio-enterado-addon')
                                        ->value(!$esNuevoRegistro && !empty($datos->medio_entero) ? $datos->medio_entero : '') !!}
                            </div>
                        </div>
                        <div class="col-md-9 mb-3">
                            {!! html()->label('MOTIVOS DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN')->for('motivo_eleccion_capacitacion') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="motivo-eleccion-addon"><i class="bi bi-list-check"></i></span>
                                </div>
                                {!! html()->select('motivo_eleccion_capacitacion', ['' => 'SELECCIONA UN MOTIVO', 1 => 'PARA EMPLEARSE O AUTOEMPLEARSE', 3 => 'PARA AHORRAR GASTOS AL INGRESO FAMILIAR', 4 => 'POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓNEDUCATIVA', 5 => 'PARA MEJORAR SU SITUACIÓN EN EL TRABAJO', 6 => 'POR DISPOSICIÓN DE TIEMPO LIBRE', 7 => 'OTRO'])->class('form-control')->id('motivo_eleccion_capacitacion')->attribute('aria-label', 'Motivos de elección del sistema de capacitación')->attribute('aria-describedby', 'motivo-eleccion-addon')
                                    ->value(!$esNuevoRegistro && !empty($datos->sistema_capacitacion_especificar) ? $datos->sistema_capacitacion_especificar : '') !!}
                            </div>
                        </div>
                        <div class="col-md-9 mb-3">
                            {!! html()->label('MEDIO DE CONFIRMACIÓN')->for('medio_confirmacion') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="medio-confirmacion-addon"><i class="bi bi-chat-dots"></i></span>
                                </div>
                                {!! html()->select('medio_confirmacion', ['' => 'SELECCIONA UN MEDIO', 1 => 'WHATSAPP', 2 => 'MENSAJE DE TEXTO', 3 => 'CORREO ELECTRÓNICO', 4 => 'FACEBOOK', 5 => 'INSTAGRAM', 6 => 'X (Antes Twitter)', 7 => 'TELEGRAM'])->class('form-control')->id('medio_confirmacion')->attribute('aria-label', 'Medio de confirmación')->attribute('aria-describedby', 'medio-confirmacion-addon')->value(!$esNuevoRegistro && !empty($datos->medio_confirmacion) ? $datos->medio_confirmacion : '') !!}
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Guardar capacitación')->class('btn btn-primary float-end')->id('validar-capacitacion')->type('button') }}
                        </div>
                    </div>
                </div>
            </div>
            {!! html()->form()->close() !!}

            {{-- * Formulario de Alumno Laboral --}}
            {!! html()->form()->id('form-laboral')->open() !!}
            <div class="col-12 mb-4 step-section" id="laboral">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-file-earmark-text"></i> ¿Está empleado el aspirante?</h5>
                    <div class="form-check mb-3">
                        {!! html()->checkbox('empleado_aspirante')->id('empleado_aspirante')->checked(!$esNuevoRegistro ? $datos->empleado : false)->class('form-check-input') !!}
                        {!! html()->label('Si el aspirante es empleado, marcar esta casilla.')->for('empleado_aspirante')->class('form-check-label ml-2') !!}
                    </div>
                    <div class="row mt-3 d-none" id="datos-laboral">
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Empresa donde trabaja')->for('empresa_trabaja') !!}
                            {!! html()->text('empresa_trabaja')->class('form-control')->id('empresa_trabaja')->placeholder('Nombre de la empresa')
                                    ->value($esNuevoRegistro ? '' : $datos->empresa_trabaja) !!}
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Puesto')->for('puesto_trabajo') !!}
                            {!! html()->text('puesto_trabajo')->class('form-control')->id('puesto_trabajo')->placeholder('Puesto que desempeña')
                                ->value($esNuevoRegistro ? '' : $datos->puesto_empresa) !!}
                        </div>
                        <div class="col-md-2 mb-3">
                            {!! html()->label('Antigüedad')->for('antiguedad_trabajo') !!}
                            {!! html()->text('antiguedad_trabajo')->class('form-control')->id('antiguedad_trabajo')->placeholder('Años/Meses')
                                    ->value($esNuevoRegistro ? '' : $datos->antiguedad) !!}
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Dirección de la empresa')->for('direccion_trabajo') !!}
                            {!! html()->text('direccion_trabajo')->class('form-control')->id('direccion_trabajo')->placeholder('Dirección completa')
                                    ->value($esNuevoRegistro ? '' : $datos->direccion_empresa) !!}
                        </div>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        {{ html()->button('Guardar Laboral')->class('btn btn-primary float-end')->id('validar-empleo')->type('button') }}
                    </div>
                </div>
            </div>
            {!! html()->form()->close() !!}

            {{-- * Formulario de CERSS --}}
            {!! html()->form()->id('form-cerss')->open() !!}
            <div class="col-12 mb-4 step-section" id="cerss">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-shield-lock"></i> ¿El aspirante pertenece a algún CERSS?</h5>
                    <div class="form-check mb-3">
                        {!! html()->checkbox('aspirante_cerss')->id('aspirante_cerss')->checked(!$esNuevoRegistro && !empty($datosCerss['aspirante_cerss']) && $datosCerss['aspirante_cerss'] == '1') !!}
                        {!! html()->label('Si el aspirante pertenece a algún CERSS, marcar esta casilla.')->for('aspirante_cerss')->class('form-check-label ml-2') !!}
                    </div>
                    <div class="row mt-3" id="datos-cerss">
                        <div class="col-md-6 mb-3">
                            {!! html()->label('NUMERO DE EXPEDIENTE')->for('numero_expediente') !!}
                            {!! html()->text('numero_expediente')->class('form-control')->id('numero_expediente')->placeholder('Número de expediente')
                                ->value(!$esNuevoRegistro && !empty($datosCerss['numero_expediente']) ? $datosCerss['numero_expediente'] : '') !!}
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('FICHA CERSS')->for('documento_ficha_cerss') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="documento-grado-addon"><i class="bi bi-file-earmark-text"></i></span>
                                </div>
                                {!! html()->file('documento_ficha_cerss')->class('form-control')->id('documento_ficha_cerss')->class('py-0')->attribute('aria-label', 'Documento de la Ficha CERSS')->attribute('aria-describedby', 'documento-grado-addon') !!}
                            </div>
                            @if (!$esNuevoRegistro && !empty($datosCerss['ficha_cerss']))
                            <small class="form-text text-muted mt-1">
                                <a href="{{ asset('storage/' . $datosCerss['ficha_cerss']) }}" target="_blank" class="text-primary text-decoration-underline">Ver documento CERSS</a>
                            </small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        {{ html()->button('Guardar CERSS y Finalizar Registro')->class('btn btn-primary float-end')->id('validar-cerss')->type('button') }}
                    </div>

                        @if ($secciones && $secciones->id == 5)
                            @if (isset($grupoId))
                                {{-- vamos a cambiar la forma en como se envia los datos al grupo --}}
                                <div class="col-md-12 d-flex justify-content-end mt-5">
                                    <a href="#" class="btn btn-warning float-start"
                                        id="enviarGrupo"
                                        onclick="event.preventDefault(); enviarGrupoCurp('{{ $grupoId }}', '{{ $datos->curp }}');">
                                            Cargar en Grupo
                                    </a>
                                </div>
                            @else
                                <div class="col-md-12 d-flex justify-content-end mt-5">
                                    <a href="{{ route('alumnos.consulta.alumno') }}" class="btn btn-secondary float-start" id="regresar-inicio">Regresar al inicio</a>
                                </div>
                            @endif

                        @endif
                    </div>
                </div>
                {!! html()->form()->close() !!}
            </div>
        </div> <!-- fin row principal -->

    {{-- ! Se cierra el div ROW --}}
</div>

<!-- Spinner de carga pantalla completa -->
<div id="spinner-curp" class="{{ $esNuevoRegistro ? '' : 'd-none' }}"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.7); z-index: 2000; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="sr-only">Cargando...</span>
    </div>
    <div class="mt-4 text-white h4 text-center" style="text-shadow: 0 2px 8px #000;">Obteniendo datos de la CURP</div>
</div>
@endsection

@push('script_sign')
<script>
    // Variables globales para la obtención de la CURP JS
        window.registroBladeVars = {
            esNuevoRegistro: {{ $esNuevoRegistro ? 'true' : 'false' }},
            routeCurp: '{{ route('alumnos.obtener.datos.curp', ':encodecurp') }}',
            routeEstadosPorPais: '{{ route('alumnos.estados.pais') }}',
            routeMunicipiosPorEstado: '{{ route('alumnos.municipios.estado') }}',
            csrfToken: '{{ csrf_token() }}',
            tieneDocumentoCURP: {{ !empty($documentos['curp']) ? 'true' : 'false' }},
            tieneDocumentoUltimoGrado: {{ !empty($documentos['ultimo_grado_estudio']) ? 'true' : 'false' }},
            tieneDocumentoFichaCerss: {{ !empty($documentos['ficha_cerss']) ? 'true' : 'false' }},
            ultimaSeccionGuardada: {!! $secciones ? json_encode($secciones->pivot['secciones'], JSON_UNESCAPED_UNICODE) : 'null' !!}
        };
</script>
<script src="{{ asset('js/alumnos/consulta.js') }}"></script>
<script src="{{ asset('js/alumnos/registro_validaciones.js') }}"></script>
<script src="{{ asset('js/alumnos/registro.js') }}"></script>
<script type="text/javascript">
    function enviarGrupoCurp(grupoId, curp)
    {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('grupos.asignar.alumnos') }}';

        // CSRF Token
        let csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        form.innerHTML += `
            <input type="hidden" name="grupo_id" value="${grupoId}">
            <input type="hidden" name="curp" value="${curp}">
        `;
        document.body.appendChild(form);
        form.submit();
    }

</script>
@endpush
