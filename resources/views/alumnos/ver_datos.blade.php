@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{ asset('css/alumnos/consulta.css') }}" />
<link rel="stylesheet" href="{{ asset('css/stepbar.css') }}" />
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-12">
        <span>{{ $esNuevoRegistro ? 'Registrar nuevo alumno' : 'Editar alumno' }}</span>
    </div>
</div>

<div class="card card-body" id="formulario-alumno">
    {!! html()->form('POST', route('alumnos.store'))->id('form-alumno')->open() !!}
    <div class="row">
        <!-- Step Progress y contenido principal -->
        <div class="col-md-3 d-none d-md-block">
            <nav id="step-progress" class="nav-sticky">
                <ul class="list-group list-group-flush step-progress-nav">
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="datos-personales">
                        <span class="step-circle mr-2" data-status="terminado">1</span>
                        <span class="fw-bold text-black">Datos personales</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="domicilio">
                        <span class="step-circle mr-2" data-status="restante">2</span>
                        <span class="fw-bold">Domicilio</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="contacto">
                        <span class="step-circle mr-2" data-status="restante">3</span>
                        <span class="fw-bold">Contacto</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="grupos-vulnerables">
                        <span class="step-circle mr-2" data-status="restante">4</span>
                        <span class="fw-bold">Grupos vulnerables</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="capacitacion">
                        <span class="step-circle mr-2" data-status="restante">5</span>
                        <span class="fw-bold">Capacitación</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="documentacion">
                        <span class="step-circle mr-2" data-status="restante">6</span>
                        <span class="fw-bold">Documentación</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="empleado">
                        <span class="step-circle mr-2" data-status="restante">7</span>
                        <span class="fw-bold">Empleado</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="cerss">
                        <span class="step-circle mr-2" data-status="restante">8</span>
                        <span class="fw-bold">CERSS</span>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="col-md-9">
            {{--  * Sección: Datos personales  --}}
            <div class="col-12 mb-4 step-section" id="datos-personales">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-person-vcard mr-2"></i>Datos personales</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            {!! html()->label('CURP')->for('curp') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="curp-addon"><i class="bi bi-credit-card-2-front"></i></span>
                                </div>
                                {!! html()->text('curp')->class('form-control')->id('curp')->value($curp)->isReadonly(true) !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Nombre')->for('nombre_s') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="nombre-addon"><i class="bi bi-person"></i></span>
                                </div>
                                {!! html()->text('nombre_s')->class('form-control')->id('nombre_s')->attribute('aria-label', 'Nombre') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Primer Apellido')->for('primer_apellido') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="primer-apellido-addon"><i class="bi bi-person-lines-fill"></i></span>
                                </div>
                                {!! html()->text('primer_apellido')->class('form-control')->id('primer_apellido')->attribute('aria-label', 'Primer Apellido')->attribute('aria-describedby', 'primer-apellido-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Segundo Apellido')->for('segundo_apellido') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="segundo-apellido-addon"><i class="bi bi-person-lines-fill"></i></span>
                                </div>
                                {!! html()->text('segundo_apellido')->class('form-control')->id('segundo_apellido')->attribute('aria-label', 'Segundo Apellido')->attribute('aria-describedby', 'segundo-apellido-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Entidad de Nacimiento')->for('entidad_de_nacimiento') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="entidad-addon"><i class="bi bi-geo-alt"></i></span>
                                </div>
                                {!! html()->text('entidad_de_nacimiento')->class('form-control')->id('entidad_de_nacimiento')->attribute('aria-label', 'Entidad de Nacimiento')->attribute('aria-describedby', 'entidad-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Fecha de Nacimiento')->for('fecha_de_nacimiento') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="fecha-addon"><i class="bi bi-calendar-date"></i></span>
                                </div>
                                {!! html()->text('fecha_de_nacimiento')->class('form-control')->id('fecha_de_nacimiento')->attribute('aria-label', 'Fecha de Nacimiento')->attribute('aria-describedby', 'fecha-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Sexo')->for('sexo') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="sexo-addon"><i class="bi bi-gender-ambiguous"></i></span>
                                </div>
                                {!! html()->text('sexo')->class('form-control')->id('sexo')->attribute('aria-label', 'Sexo')->attribute('aria-describedby', 'sexo-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            {!! html()->label('Nacionalidad')->for('nacionalidad') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="nacionalidad-addon"><i class="bi bi-flag"></i></span>
                                </div>
                                {!! html()->text('nacionalidad')->class('form-control')->id('nacionalidad')->attribute('aria-label', 'Nacionalidad')->attribute('aria-describedby', 'nacionalidad-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Estado Civil')->for('estado_civil') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="estado-civil-addon"><i class="bi bi-people"></i></span>
                                </div>
                                {!! html()->select('estado_civil',
                                [null => 'SELECCIONA ESTADO CIVL', 'soltero'=>'Soltero','casado'=>'Casado','otro'=>'Otro'])->class('form-control')->id('estado_civil')->attribute('aria-label', 'Estado Civil')->attribute('aria-describedby', 'estado-civil-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Siguiente')->class('btn btn-primary float-end guardar-seccion')->id('validar-datos-personales')->type('button')->attribute('data-seccion', 'datos-personales') }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- * Sección: Domicilio  --}}
            <div class="col-12 mb-4 step-section" id="domicilio">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-house-door mr-2"></i>Domicilio</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            {!! html()->label('País')->for('pais') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="pais-addon"><i class="bi bi-globe2"></i></span>
                                </div>
                                {!! html()->text('pais')->class('form-control')->id('pais')->attribute('aria-label', 'País')->attribute('aria-describedby', 'pais-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Estado')->for('estado') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="estado-addon"><i class="bi bi-map"></i></span>
                                </div>
                                {!! html()->text('estado')->class('form-control')->id('estado')->attribute('aria-label', 'Estado')->attribute('aria-describedby', 'estado-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Municipio')->for('municipio') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="municipio-addon"><i class="bi bi-building"></i></span>
                                </div>
                                {!! html()->text('municipio')->class('form-control')->id('municipio')->attribute('aria-label', 'Municipio')->attribute('aria-describedby', 'municipio-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Localidad')->for('localidad') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="localidad-addon"><i class="bi bi-geo"></i></span>
                                </div>
                                {!! html()->text('localidad')->class('form-control')->id('localidad')->attribute('aria-label', 'Localidad')->attribute('aria-describedby', 'localidad-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Código Postal')->for('codigo_postal') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="cp-addon"><i class="bi bi-mailbox"></i></span>
                                </div>
                                {!! html()->text('codigo_postal')->class('form-control')->id('codigo_postal')->attribute('aria-label', 'Código Postal')->attribute('aria-describedby', 'cp-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Domicilio')->for('domicilio') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="domicilio-addon"><i class="bi bi-geo-alt"></i></span>
                                </div>
                                {!! html()->text('domicilio')->class('form-control')->id('domicilio')->attribute('aria-label', 'Domicilio')->attribute('aria-describedby', 'domicilio-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Colonia')->for('colonia') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="colonia-addon"><i class="bi bi-signpost"></i></span>
                                </div>
                                {!! html()->text('colonia')->class('form-control')->id('colonia')->attribute('aria-label', 'Colonia')->attribute('aria-describedby', 'colonia-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Siguiente')->class('btn btn-primary float-end')->id('btn-siguiente-datos-personales')->attribute('data-next-step', 'domicilio')->type('button') }}
    {{ html()->button('Siguiente')->class('btn btn-primary float-end guardar-seccion')->id('btn-siguiente-domicilio')->attribute('data-next-step', 'domicilio')->type('button')->attribute('data-seccion', 'domicilio') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- * Sección: Contacto  --}}
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
                                {!! html()->text('telefono_casa')->class('form-control')->id('telefono_casa')->attribute('aria-label', 'Teléfono Casa')->attribute('aria-describedby', 'tel-casa-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Teléfono Celular')->for('telefono_celular') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="tel-cel-addon"><i class="bi bi-phone"></i></span>
                                </div>
                                {!! html()->text('telefono_celular')->class('form-control')->id('telefono_celular')->attribute('aria-label', 'Teléfono Celular')->attribute('aria-describedby', 'tel-cel-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Correo Electrónico')->for('correo_electronico') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="correo-addon"><i class="bi bi-envelope"></i></span>
                                </div>
                                {!! html()->email('correo_electronico')->class('form-control')->id('correo_electronico')->attribute('aria-label', 'Correo Electrónico')->attribute('aria-describedby', 'correo-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Facebook')->for('facebook') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="facebook-addon"><i class="bi bi-facebook"></i></span>
                                </div>
                                {!! html()->text('facebook')->class('form-control')->id('facebook')->attribute('aria-label', 'Facebook')->attribute('aria-describedby', 'facebook-addon') !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3 d-flex align-items-center"
                                style="background: #fffbe6; border: 2px solid #ffe58f; border-radius: 8px; padding: 12px;">
                                {!! html()->label('¿Usted autoriza dar su número de celular para alguna oportunidad en la Bolsa de Trabajo?')
                                                ->for('autoriza_bolsa_trabajo')->class('fw-bold me-3 mb-0')->style('color: #ad8b00; font-size: 1.1em;') !!}
                                <div class="form-check ml-2">
                                    {!! html()->checkbox('autoriza_bolsa_trabajo', false, 1)->class('form-check-input')->id('autoriza_bolsa_trabajo') !!}
                                    {!! html()->label('Sí')->for('autoriza_bolsa_trabajo')->class('form-check-label') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Siguiente')->class('btn btn-primary float-end')->id('btn-siguiente-datos-personales')->attribute('data-next-step', 'domicilio')->type('button') }}
    {{ html()->button('Siguiente')->class('btn btn-primary float-end guardar-seccion')->id('btn-siguiente-contacto')->attribute('data-next-step', 'contacto')->type('button')->attribute('data-seccion', 'contacto') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- * Sección: Grupos vulnerables  --}}
            <div class="col-12 mb-4 step-section" id="grupos-vulnerables">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-exclamation-triangle mr-2"></i>Grupos vulnerables</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check my-4 border-bottom pb-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'sin_grupo_vulnerable')->class('form-check-input')->id('grupo_vulnerable_sin_grupo') !!}
                                {!! html()->label('NO PERTENEZCO A UN GRUPO VULNERABLE')->for('grupo_vulnerable_sin_grupo')->class('form-check-label ml-2') !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'lgbttti')->class('form-check-input')->id('grupo_vulnerable_lgbttti') !!}
                                {!! html()->label('LGBTTTI+')->for('grupo_vulnerable_lgbttti')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'persona_afroamericana')->class('form-check-input')->id('grupo_vulnerable_persona_afroamericana') !!}
                                {!! html()->label('PERSONA AFROAMERICANA')->for('grupo_vulnerable_persona_afroamericana')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'persona_indigena')->class('form-check-input')->id('grupo_vulnerable_persona_indigena') !!}
                                {!! html()->label('PERSONA INDÍGENA')->for('grupo_vulnerable_persona_indigena')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'persona_adulta_mayor')->class('form-check-input')->id('grupo_vulnerable_persona_adulta_mayor') !!}
                                {!! html()->label('PERSONA ADULTA MAYOR')->for('grupo_vulnerable_persona_adulta_mayor')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'trabajadora_sexual')->class('form-check-input')->id('grupo_vulnerable_trabajadora_sexual') !!}
                                {!! html()->label('TRABAJADORA SEXUAL')->for('grupo_vulnerable_trabajadora_sexual')->class('form-check-label ml-2') !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'discapacidad_para_oir')->class('form-check-input')->id('grupo_vulnerable_discapacidad_para_oir') !!}
                                {!! html()->label('DISCAPACIDAD PARA OÍR')->for('grupo_vulnerable_discapacidad_para_oir')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'discapacidad_intelectual')->class('form-check-input')->id('grupo_vulnerable_discapacidad_intelectual') !!}
                                {!! html()->label('DISCAPACIDAD INTELECTUAL')->for('grupo_vulnerable_discapacidad_intelectual')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'madre_jefa_familia')->class('form-check-input')->id('grupo_vulnerable_madre_jefa_familia') !!}
                                {!! html()->label('MADRE JEFA DE FAMILIA')->for('grupo_vulnerable_madre_jefa_familia')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'discapacidad_para_hablar')->class('form-check-input')->id('grupo_vulnerable_discapacidad_para_hablar') !!}
                                {!! html()->label('DISCAPACIDAD PARA HABLAR')->for('grupo_vulnerable_discapacidad_para_hablar')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'mujeres_embarazadas')->class('form-check-input')->id('grupo_vulnerable_mujeres_embarazadas') !!}
                                {!! html()->label('MUJERES EMBARAZADAS')->for('grupo_vulnerable_mujeres_embarazadas')->class('form-check-label ml-2') !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'persona_migrante')->class('form-check-input')->id('grupo_vulnerable_persona_migrante') !!}
                                {!! html()->label('PERSONA MIGRANTE')->for('grupo_vulnerable_persona_migrante')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'persona_privada_libertad')->class('form-check-input')->id('grupo_vulnerable_persona_privada_libertad') !!}
                                {!! html()->label('PERSONA PRIVADA DE LA LIBERTAD')->for('grupo_vulnerable_persona_privada_libertad')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'discapacidad_visual')->class('form-check-input')->id('grupo_vulnerable_discapacidad_visual') !!}
                                {!! html()->label('DISCAPACIDAD VISUAL')->for('grupo_vulnerable_discapacidad_visual')->class('form-check-label ml-2') !!}
                            </div>
                            <div class="form-check my-4">
                                {!! html()->checkbox('grupos_vulnerables[]', false, 'discapacidad_motriz')->class('form-check-input')->id('grupo_vulnerable_discapacidad_motriz') !!}
                                {!! html()->label('DISCAPACIDAD MOTRIZ')->for('grupo_vulnerable_discapacidad_motriz')->class('form-check-label ml-2') !!}
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Siguiente')->class('btn btn-primary float-end')->id('btn-siguiente-datos-personales')->attribute('data-next-step', 'domicilio')->type('button') }}
    {{ html()->button('Siguiente')->class('btn btn-primary float-end guardar-seccion')->id('btn-siguiente-grupos-vulnerables')->attribute('data-next-step', 'grupos-vulnerables')->type('button')->attribute('data-seccion', 'grupos-vulnerables') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- * Sección: Capacitación  --}}
            <div class="col-12 mb-4 step-section" id="capacitacion">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-info-circle mr-2"></i>DE LA CAPACITACIÓN
                    </h5>
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            {!! html()->label('ULTIMO GRADO DE ESTUDIOS')->for('ultimo_grado_estudios') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="grado-estudios-addon"><i class="bi bi-book"></i></span>
                                </div>
                                {!! html()->select('ultimo_grado_estudios', ['ninguno'=>'Ninguno','primaria'=>'Primaria','secundaria'=>'Secundaria','preparatoria'=>'Preparatoria','licenciatura'=>'Licenciatura','posgrado'=>'Posgrado'])
                                            ->class('form-control')->id('ultimo_grado_estudios')
                                            ->attribute('aria-label', 'Ultimo Grado de Estudios')
                                            ->attribute('aria-describedby', 'grado-estudios-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-9 mb-3">
                            {!! html()->label('MEDIO POR EL QUE SE ENTERO DEL SISTEMA')->for('medio_enterado_sistema') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="medio-enterado-addon"><i class="bi bi-megaphone"></i></span>
                                </div>
                                {!! html()->select('medio_enterado_sistema', ['internet'=>'INTERNET','prensa' =>'PRENSA','radio'=>'RADIO','television'=>'TELEVISIÓN','papel' => 'FOLLETOS, CARTELES, VOLANTES.', 'otros'=>'OTROS'])
                                            ->class('form-control')->id('medio_enterado_sistema')
                                            ->attribute('aria-label', 'Medio por el que se enteró del sistema')
                                            ->attribute('aria-describedby', 'medio-enterado-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-9 mb-3">
                            {!! html()->label('MOTIVOS DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN')->for('motivo_eleccion_capacitacion') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="motivo-eleccion-addon"><i class="bi bi-list-check"></i></span>
                                </div>
                                {!! html()->select('motivo_eleccion_capacitacion', ['emplearse_autoemplearse_1' => 'PARA EMPLEARSE O AUTOEMPLEARSE','emplearse_autoemplearse_2' => 'PARA EMPLEARSE O AUTOEMPLEARSE','ahorrar_gastos' => 'PARA AHORRAR GASTOS AL INGRESO FAMILIAR','espera_incorporarse' => 'POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓNEDUCATIVA','mejorar_trabajo' => 'PARA MEJORAR SU SITUACIÓN EN EL TRABAJO','tiempo_libre' => 'POR DISPOSICIÓN DE TIEMPO LIBRE','otro' => 'OTRO' ])
                                            ->class('form-control')->id('motivo_eleccion_capacitacion')
                                            ->attribute('aria-label', 'Motivos de elección del sistema de capacitación')
                                            ->attribute('aria-describedby', 'motivo-eleccion-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-9 mb-3">
                            {!! html()->label('MEDIO DE CONFIRMACIÓN')->for('medio_confirmacion') !!}
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="medio-confirmacion-addon"><i class="bi bi-chat-dots"></i></span>
                                </div>
                                {!! html()->select('medio_confirmacion', ['Whatsapp' => 'WHATSAPP','Mensaje de Texto' => 'MENSAJE DE TEXTO','Correo Electrónico' => 'CORREO ELECTRÓNICO','Facebook' => 'FACEBOOK','Instagram' => 'INSTAGRAM','x' => 'X (Antes Twitter)','Telegram' => 'TELEGRAM',])
                                            ->class('form-control')->id('medio_confirmacion')
                                            ->attribute('aria-label', 'Medio de confirmación')
                                            ->attribute('aria-describedby', 'medio-confirmacion-addon') !!}
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            {{ html()->button('Siguiente')->class('btn btn-primary float-end')->id('btn-siguiente-datos-personales')->attribute('data-next-step', 'domicilio')->type('button') }}
    {{ html()->button('Siguiente')->class('btn btn-primary float-end guardar-seccion')->id('btn-siguiente-capacitacion')->attribute('data-next-step', 'capacitacion')->type('button')->attribute('data-seccion', 'capacitacion') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- * Sección: Documentación Requerida  --}}
            <div class="col-12 mb-4 step-section" id="documentacion">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold pb-1 mb-3"><i class="bi bi-file-earmark-text"></i> Documentación Requerida</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="">
                                <tr>
                                    <th>Documento</th>
                                    <th>Fecha de expedición</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-4">
                                        <div class="form-check">
                                            {!! html()->checkbox('documento_curp')->id('documento_curp') !!}
                                            {!! html()->label('CURP')->for('documento_curp')->class('form-check-label ml-2') !!}
                                        </div>
                                    </td>
                                    <td class="p-4"> {!! html()->date('fecha_documento_curp')->class('form-control')->id('fecha_documento_curp') !!} </td>
                                </tr>
                                <tr>
                                    <td class="p-4">
                                        <div class="form-check">
                                            {!! html()->checkbox('documento_ultimo_grado')->id('documento_ultimo_grado') !!}
                                            {!! html()->label('Ultimo Grado de estudios')->for('documento_ultimo_grado')->class('form-check-label ml-2') !!}
                                        </div>
                                    </td>
                                    <td class="p-4"> {!! html()->date('fecha_documento_ultimo_grado')->class('form-control')->id('fecha_documento_ultimo_grado') !!} </td>
                                </tr>
                                <tr>
                                    <td class="p-4">
                                        <div class="form-check">
                                            {!! html()->checkbox('documento_ficha_cerss')->id('documento_ficha_cerss') !!}
                                            {!! html()->label('Ficha CERSS')->for('documento_ficha_cerss')->class('form-check-label ml-2') !!}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        {!! html()->date('fecha_documento_ficha_cerss')->class('form-control')->id('fecha_documento_ficha_cerss') !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <label for="archivo_requisito" class="form-label">Adjuntar un solo PDF los requisitos en el orden especificado:</label>
                        <input type="file" name="archivo_requisito" id="archivo_requisito" class="form-control" accept=".pdf">
                        <small class="text-muted">Solo se permite subir un archivo PDF.</small>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        {{ html()->button('Siguiente')->class('btn btn-primary float-end')->id('btn-siguiente-datos-personales')->attribute('data-next-step', 'domicilio')->type('button') }}
    {{ html()->button('Siguiente')->class('btn btn-primary float-end guardar-seccion')->id('btn-siguiente-documentacion')->attribute('data-next-step', 'documentacion')->type('button')->attribute('data-seccion', 'documentacion') }}
                    </div>
                </div>
            </div>

            {{-- * Sección: Alumno Empleado  --}}
            <div class="col-12 mb-4 step-section" id="empleado">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-file-earmark-text"></i> ¿Está empleado el aspirante?</h5>
                    <div class="form-check mb-3">
                        {!! html()->checkbox('empleado_aspirante')->id('empleado_aspirante') !!}
                        {!! html()->label('Si el aspirante es empleado, marcar esta casilla.')->for('empleado_aspirante')->class('form-check-label ml-2') !!}
                    </div>
                    <div class="row mt-3 d-none" id="datos-empleo">
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Empresa donde trabaja')->for('empresa_trabaja') !!}
                            {!! html()->text('empresa_trabaja')->class('form-control')->id('empresa_trabaja')->placeholder('Nombre de la empresa') !!}
                        </div>
                        <div class="col-md-4 mb-3">
                            {!! html()->label('Puesto')->for('puesto_trabajo') !!}
                            {!! html()->text('puesto_trabajo')->class('form-control')->id('puesto_trabajo')->placeholder('Puesto que desempeña') !!}
                        </div>
                        <div class="col-md-2 mb-3">
                            {!! html()->label('Antigüedad')->for('antiguedad_trabajo') !!}
                            {!! html()->text('antiguedad_trabajo')->class('form-control')->id('antiguedad_trabajo')->placeholder('Años/Meses') !!}
                        </div>
                        <div class="col-md-6 mb-3">
                            {!! html()->label('Dirección de la empresa')->for('direccion_trabajo') !!}
                            {!! html()->text('direccion_trabajo')->class('form-control')->id('direccion_trabajo')->placeholder('Dirección completa') !!}
                        </div>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        {{ html()->button('Siguiente')->class('btn btn-primary float-end')->id('btn-siguiente-datos-personales')->attribute('data-next-step', 'domicilio')->type('button') }}
                        {{ html()->button('Siguiente')->class('btn btn-primary float-end guardar-seccion')->id('btn-siguiente-empleado')->attribute('data-next-step', 'empleado')->type('button')->attribute('data-seccion', 'empleado') }}
                    </div>
                </div>
            </div>

            {{-- * Sección: Alumno CERSS --}}
            <div class="col-12 mb-4 step-section" id="cerss">
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-shield-lock"></i> ¿El aspirante pertenece a algún CERSS?</h5>
                    <div class="form-check mb-3">
                        {!! html()->checkbox('aspirante_cerss')->id('aspirante_cerss') !!}
                        {!! html()->label('Si el aspirante pertenece a algún CERSS, marcar esta casilla.')->for('aspirante_cerss')->class('form-check-label ml-2') !!}
                    </div>
                    <div class="row mt-3" id="datos-cerss">
                        <div class="col-md-12 mb-3">
                            {!! html()->label('NUMERO DE EXPEDIENTE')->for('numero_expediente') !!}
                            {!! html()->text('numero_expediente')->class('form-control')->id('numero_expediente')->placeholder('Número de expediente') !!}
                        </div>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        {{ html()->button('Terminar')->class('btn btn-primary float-end')->id('btn-terminar-registro')->type('button') }}
                        {{ html()->button('Terminar')->class('btn btn-primary float-end guardar-seccion')->id('btn-terminar-registro')->type('button')->attribute('data-seccion', 'cerss') }}
                    </div>
                </div>
            </div>
        </div>
    </div>  <!-- fin row principal -->

    {{-- ! Se cierra el div ROW --}}
</div>

{!! html()->form()->close() !!}
</div>

<!-- Spinner de carga pantalla completa -->
<div id="spinner-curp" class="{{$esNuevoRegistro ? '' : 'd-none'}}"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.7); z-index: 2000; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="sr-only">Cargando...</span>
    </div>
    <div class="mt-4 text-white h4 text-center" style="text-shadow: 0 2px 8px #000;">Obteniendo datos de la CURP</div>
</div>
@endsection

@push('script_sign')
<script src="{{ asset('js/alumnos/consulta.js') }}"></script>
<script src="{{ asset('js/alumnos/registro_validaciones.js') }}"></script>
<script src="{{ asset('js/alumnos/registro.js') }}"></script>
<script>
    // Variables globales para la obtención de la CURP JS
    window.registroBladeVars = {
        routeCurp: '{{ route("alumnos.obtener.datos.curp", ":encodecurp") }}',
        csrfToken: '{{ csrf_token() }}'
    };

    inicializarNavegacionSecciones();

    // Evento para botones de guardar sección
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.guardar-seccion').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const seccion = btn.getAttribute('data-seccion');
                console.log('Guardando seccion ' + seccion);
            });
        });
    });
</script>
@endpush