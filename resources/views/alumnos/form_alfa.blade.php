<div class="col-12 mb-2 mt-4">
    <h5 class="fw-bold border-bottom pb-1 mb-3"><i class="bi bi-person mr-2"></i>Alumno Alfa</h5>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        {!! html()->label('Fecha de registro')->for('fecha_registro') !!}
        {!! html()->date('fecha_registro')->class('form-control')->id('fecha_registro') !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Entidad de nacimiento')->for('entidad_nacimiento_alfa') !!}
        {!! html()->select('entidad_nacimiento_alfa', [
        'aguascalientes'=>'Aguascalientes','baja_california'=>'Baja California','baja_california_sur'=>'Baja
        California Sur',
        'campeche'=>'Campeche','coahuila'=>'Coahuila','colima'=>'Colima','chiapas'=>'Chiapas','chihuahua'=>'Chihuahua',
        'cdmx'=>'Ciudad de
        México','durango'=>'Durango','guanajuato'=>'Guanajuato','guerrero'=>'Guerrero','hidalgo'=>'Hidalgo',
        'jalisco'=>'Jalisco','mexico'=>'México','michoacan'=>'Michoacán','morelos'=>'Morelos','nayarit'=>'Nayarit',
        'nuevo_leon'=>'Nuevo
        León','oaxaca'=>'Oaxaca','puebla'=>'Puebla','queretaro'=>'Querétaro','quintana_roo'=>'Quintana Roo',
        'san_luis_potosi'=>'San Luis
        Potosí','sinaloa'=>'Sinaloa','sonora'=>'Sonora','tabasco'=>'Tabasco','tamaulipas'=>'Tamaulipas',
        'tlaxcala'=>'Tlaxcala','veracruz'=>'Veracruz','yucatan'=>'Yucatán','zacatecas'=>'Zacatecas','extranjero'=>'Extranjero'])
        ->class('form-control')->id('entidad_nacimiento_alfa') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('habla_espanol')->id('habla_espanol') !!}
        {!! html()->label('¿Habla español?')->for('habla_espanol')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('habla_lengua_indigena')->id('habla_lengua_indigena') !!}
        {!! html()->label('¿Habla algún dialecto o lengua indígena?')->for('habla_lengua_indigena')->class('ml-2
        mb-0') !!}
        {!! html()->text('cual_lengua_indigena')->class('form-control
        ml-2')->placeholder('¿Cuál?')->style('max-width: 120px;') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('otro_idioma')->id('otro_idioma') !!}
        {!! html()->label('¿Otro idioma adicional al español?')->for('otro_idioma')->class('ml-2 mb-0') !!}
        {!! html()->text('cual_otro_idioma')->class('form-control
        ml-2')->placeholder('¿Cuál?')->style('max-width: 120px;') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('se_considera_indigena')->id('se_considera_indigena') !!}
        {!! html()->label('¿De acuerdo a su cultura, usted se considera
        indígena?')->for('se_considera_indigena')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('afromexicano')->id('afromexicano') !!}
        {!! html()->label('¿Usted se considera afromexicano(a) o
        afrodescendiente?')->for('afromexicano')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Vialidad')->for('vialidad') !!}
        {!! html()->select('vialidad',
        ['calle'=>'Calle','avenida'=>'Avenida','boulevard'=>'Boulevard','carretera'=>'Carretera','privada'=>'Privada','otro'=>'Otro'])->class('form-control')->id('vialidad')
        !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Nombre vialidad')->for('nombre_vialidad') !!}
        {!! html()->text('nombre_vialidad')->class('form-control')->id('nombre_vialidad') !!}
    </div>
    <div class="col-md-2 mb-3">
        {!! html()->label('Número exterior')->for('numero_exterior') !!}
        {!! html()->text('numero_exterior')->class('form-control')->id('numero_exterior') !!}
    </div>
    <div class="col-md-2 mb-3">
        {!! html()->label('Número interior')->for('numero_interior') !!}
        {!! html()->text('numero_interior')->class('form-control')->id('numero_interior') !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Asentamiento humano')->for('asentamiento_humano') !!}
        {!! html()->select('asentamiento_humano',
        ['colonia'=>'Colonia','barrio'=>'Barrio','fraccionamiento'=>'Fraccionamiento','ejido'=>'Ejido','otro'=>'Otro'])->class('form-control')->id('asentamiento_humano')
        !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Nombre asentamiento humano')->for('nombre_asentamiento_humano') !!}
        {!! html()->text('nombre_asentamiento_humano')->class('form-control')->id('nombre_asentamiento_humano')
        !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Entre qué vialidad')->for('entre_vialidad') !!}
        {!! html()->select('entre_vialidad',
        ['calle'=>'Calle','avenida'=>'Avenida','boulevard'=>'Boulevard','carretera'=>'Carretera','privada'=>'Privada','otro'=>'Otro'])->class('form-control')->id('entre_vialidad')
        !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Nombre vialidad entre')->for('nombre_vialidad_entre') !!}
        {!! html()->text('nombre_vialidad_entre')->class('form-control')->id('nombre_vialidad_entre') !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Y qué vialidad')->for('entre_2_vialidad') !!}
        {!! html()->select('entre_2_vialidad',
        ['calle'=>'Calle','avenida'=>'Avenida','boulevard'=>'Boulevard','carretera'=>'Carretera','privada'=>'Privada','otro'=>'Otro'])->class('form-control')->id('entre_2_vialidad')
        !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Nombre vialidad (entre 2)')->for('nombre_vialidad_entre_2') !!}
        {!! html()->text('nombre_vialidad_entre_2')->class('form-control')->id('nombre_vialidad_entre_2') !!}
    </div>
    <div class="col-md-2 mb-3">
        {!! html()->label('Código Postal')->for('codigo_postal_alfa') !!}
        {!! html()->text('codigo_postal_alfa')->class('form-control')->id('codigo_postal_alfa') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('equipo_computo')->id('equipo_computo') !!}
        {!! html()->label('¿Tiene equipo de cómputo?')->for('equipo_computo')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('acceso_internet')->id('acceso_internet') !!}
        {!! html()->label('¿Tiene acceso a internet?')->for('acceso_internet')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Correo electrónico IAEA')->for('correo_iaea') !!}
        {!! html()->email('correo_iaea')->class('form-control')->id('correo_iaea') !!}
    </div>
    <div class="col-12 mb-2 mt-4">
        <h6 class="fw-bold">En su vida diaria ¿Usted tiene problemas para?:</h6>
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('problema_caminar')->id('problema_caminar') !!}
        {!! html()->label('Caminar, subir o bajar escaleras')->for('problema_caminar')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('problema_banarse')->id('problema_banarse') !!}
        {!! html()->label('Bañarse, vestirse o comer')->for('problema_banarse')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('problema_condicion_mental')->id('problema_condicion_mental') !!}
        {!! html()->label('¿Tiene algún problema o condición mental? (Autismo, Síndrome de Down, Esquizofrenia,
        etc.)')->for('problema_condicion_mental')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('problema_oir')->id('problema_oir') !!}
        {!! html()->label('Oír, aún usando aparatos auditivos')->for('problema_oir')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('problema_hablar')->id('problema_hablar') !!}
        {!! html()->label('Hablar o comunicarse (por ejemplo, entender o ser entendidos por
        otros)')->for('problema_hablar')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('problema_ver')->id('problema_ver') !!}
        {!! html()->label('Ver aun usando lentes')->for('problema_ver')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('problema_recordar')->id('problema_recordar') !!}
        {!! html()->label('Recordar o concentrarse')->for('problema_recordar')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-12 mb-2 mt-4">
        <h6 class="fw-bold">¿Tiene trabajo activo?</h6>
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('jubilado')->id('jubilado') !!}
        {!! html()->label('Jubilado(a) o pensionado(a)')->for('jubilado')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('desempleado')->id('desempleado') !!}
        {!! html()->label('Desempleado(a)')->for('desempleado')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('estudiante')->id('estudiante') !!}
        {!! html()->label('Estudiante')->for('estudiante')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->label('Otro')->for('otro_trabajo') !!}
        {!! html()->text('otro_trabajo')->class('form-control')->id('otro_trabajo') !!}
    </div>
    <div class="col-12 mb-2 mt-4">
        <h6 class="fw-bold">Tipos de ocupación</h6>
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('ocupacion_agropecuario')->id('ocupacion_agropecuario') !!}
        {!! html()->label('Trabajador/a agropecuario')->for('ocupacion_agropecuario')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('ocupacion_ayudante')->id('ocupacion_ayudante') !!}
        {!! html()->label('Ayudante o similar')->for('ocupacion_ayudante')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('ocupacion_hogar')->id('ocupacion_hogar') !!}
        {!! html()->label('Trabajador/a del hogar')->for('ocupacion_hogar')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('ocupacion_deportista')->id('ocupacion_deportista') !!}
        {!! html()->label('Deportista')->for('ocupacion_deportista')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('ocupacion_inspector')->id('ocupacion_inspector') !!}
        {!! html()->label('Inspector/a supervisor/a')->for('ocupacion_inspector')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('ocupacion_gobierno')->id('ocupacion_gobierno') !!}
        {!! html()->label('Empleado/a de gobierno')->for('ocupacion_gobierno')->class('ml-2 mb-0') !!}
    </div>
</div>

<!-- Antecedentes escolares -->
<div class="col-md-12 mb-2 mt-4">
    <h6 class="fw-bold">Antecedentes escolares</h6>
</div>
<div class="row align-items-end">
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('sin_estudios')->id('sin_estudios') !!}
        {!! html()->label('Sin estudios')->for('sin_estudios')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('primaria_antecedente')->id('primaria_antecedente') !!}
        {!! html()->label('Primaria')->for('primaria_antecedente')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3">
        {!! html()->text('grado_primaria')->class('form-control')->placeholder('Grado') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('secundaria_antecedente')->id('secundaria_antecedente') !!}
        {!! html()->label('Secundaria')->for('secundaria_antecedente')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3">
        {!! html()->text('grado_secundaria')->class('form-control')->placeholder('Grado') !!}
    </div>
</div>

<!-- Nivel al que ingresa -->
<div class="col-12 mb-2 mt-4">
    <h6 class="fw-bold">Nivel al que ingresa</h6>
</div>
<div class="row align-items-end">
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('alfabetizacion')->id('alfabetizacion') !!}
        {!! html()->label('Alfabetización')->for('alfabetizacion')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('primaria_nivel')->id('primaria_nivel') !!}
        {!! html()->label('Primaria')->for('primaria_nivel')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('primaria_10_14')->id('primaria_10_14') !!}
        {!! html()->label('Primaria 10-14')->for('primaria_10_14')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('secundaria_nivel')->id('secundaria_nivel') !!}
        {!! html()->label('Secundaria')->for('secundaria_nivel')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('ejercicio_diagnostico')->id('ejercicio_diagnostico') !!}
        {!! html()->label('Ejercicio diagnóstico (Alfabetización)')->for('ejercicio_diagnostico')->class('ml-2
        mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('examen_diagnostico')->id('examen_diagnostico') !!}
        {!! html()->label('Examen diagnóstico')->for('examen_diagnostico')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('reconocimiento_saberes')->id('reconocimiento_saberes') !!}
        {!! html()->label('Reconocimiento de saberes')->for('reconocimiento_saberes')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('atencion_educativa')->id('atencion_educativa') !!}
        {!! html()->label('Atención educativa')->for('atencion_educativa')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('hispanohablante')->id('hispanohablante') !!}
        {!! html()->label('Hispanohablante')->for('hispanohablante')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('hablante_lengua_indigena')->id('hablante_lengua_indigena') !!}
        {!! html()->label('Hablante de lengua indígena')->for('hablante_lengua_indigena')->class('ml-2 mb-0')
        !!}
        {!! html()->text('entia_lengua')->class('form-control
        ml-2')->placeholder('Entia/Lengua')->style('max-width: 120px;') !!}
    </div>
</div>


<!-- ¿Qué le motiva a estudiar? -->
<div class="col-12 mb-2 mt-4">
    <h6 class="fw-bold">¿Qué le motiva a estudiar?</h6>
</div>
<div class="row align-items-end">
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('motivo_certificado')->id('motivo_certificado') !!}
        {!! html()->label('Obtener el certificado de
        Primaria/Secundaria')->for('motivo_certificado')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('motivo_continuar_media')->id('motivo_continuar_media') !!}
        {!! html()->label('Continuar la Educación Media Superior')->for('motivo_continuar_media')->class('ml-2
        mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('motivo_empleo')->id('motivo_empleo') !!}
        {!! html()->label('Obtener un empleo')->for('motivo_empleo')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('motivo_mejorar_condiciones')->id('motivo_mejorar_condiciones') !!}
        {!! html()->label('Mejorar mis condiciones laborales')->for('motivo_mejorar_condiciones')->class('ml-2
        mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('motivo_ayudar_hijos')->id('motivo_ayudar_hijos') !!}
        {!! html()->label('Ayudar a mis hijos/nietos con las tareas')->for('motivo_ayudar_hijos')->class('ml-2
        mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('motivo_superacion')->id('motivo_superacion') !!}
        {!! html()->label('Superacion personal')->for('motivo_superacion')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->text('motivo_otro')->class('form-control')->placeholder('Otro') !!}
    </div>
</div>

<!-- Número de hijos -->
<div class="col-12 mb-2 mt-4">
    <h6 class="fw-bold">Numero de Hijos</h6>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        {!! html()->text('numero_hijos')->class('form-control')->placeholder('') !!}
    </div>
</div>

<!-- ¿Cómo se enteró de nuestros servicios? -->
<div class="col-12 mb-2 mt-4">
    <h6 class="fw-bold">¿Cómo se enteró de nuestros servicios?</h6>
</div>
<div class="row align-items-end">
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('difusion_inea')->id('difusion_inea') !!}
        {!! html()->label('Difusión de INEA')->for('difusion_inea')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('invitacion_personal')->id('invitacion_personal') !!}
        {!! html()->label('Invitación personal')->for('invitacion_personal')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3">
        {!! html()->text('servicio_otro')->class('form-control')->placeholder('Otro') !!}
    </div>
</div>

<!-- Modelo y Subproyecto -->
<div class="col-12 mb-2 mt-4">
    <h6 class="fw-bold">Modelo y Subproyecto</h6>
</div>
<div class="row align-items-end">
    <div class="col-md-4 mb-3">
        {!! html()->label('Modelo')->for('modelo') !!}
        {!! html()->select('modelo', [''=>'- SELECCIONAR -','modelo1'=>'Modelo 1','modelo2'=>'Modelo
        2','modelo3'=>'Modelo 3'])->class('form-control')->id('modelo') !!}
    </div>
    <div class="col-md-4 mb-3">
        {!! html()->label('Etapa EB.')->for('etapa_eb') !!}
        {!! html()->text('etapa_eb')->class('form-control')->id('etapa_eb') !!}
    </div>
</div>
<div class="row align-items-end">
    <div class="col-md-4 mb-3">
        {!! html()->label('Subproyecto')->for('subproyecto') !!}
        {!! html()->text('subproyecto')->class('form-control')->id('subproyecto')->value('CHIAPAS PUEDE
        INSTITUTOS') !!}
    </div>
    <div class="col-md-4 mb-3">
        {!! html()->label('Dependencia')->for('dependencia') !!}
        {!!
        html()->text('dependencia')->class('form-control')->id('dependencia')->value('ICATECH')->attribute('readonly',
        true) !!}
    </div>
</div>

<!-- Documentación de la persona beneficiaria -->
<div class="col-12 mb-2 mt-4">
    <h6 class="fw-bold">Documentación de la persona beneficiaria</h6>
</div>
<div class="row align-items-end">
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('documento_fotografia')->id('documento_fotografia') !!}
        {!! html()->label('Fotografía')->for('documento_fotografia')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-4 mb-3 d-flex align-items-center">
        {!! html()->checkbox('documento_legal_extranjero')->id('documento_legal_extranjero') !!}
        {!! html()->label('Documento legal equivalente
        (extranjeros)')->for('documento_legal_extranjero')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('documento_ficha_cereso')->id('documento_ficha_cereso') !!}
        {!! html()->label('Ficha signalética (CERESO)')->for('documento_ficha_cereso')->class('ml-2 mb-0') !!}
    </div>
</div>

<!-- Documentos Probatorios / Constancias de capacitación -->
<div class="col-12 mb-2 mt-4">
    <h6 class="fw-bold">Documentos Probatorios / Constancias de capacitación.</h6>
</div>
<div class="row align-items-end">
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('certificado_primaria')->id('certificado_primaria') !!}
        {!! html()->label('Certificado de primaria')->for('certificado_primaria')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('boletas_primaria')->id('boletas_primaria') !!}
        {!! html()->label('Boletas de primaria')->for('boletas_primaria')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3">
        {!! html()->text('grado_boleta_primaria')->class('form-control')->placeholder('Grado') !!}
    </div>
    <div class="col-md-2 mb-3 d-flex align-items-center">
        {!! html()->checkbox('boletas_secundaria')->id('boletas_secundaria') !!}
        {!! html()->label('Boletas de secundaria')->for('boletas_secundaria')->class('ml-2 mb-0') !!}
    </div>
    <div class="col-md-2 mb-3">
        {!! html()->text('grado_boleta_secundaria')->class('form-control')->placeholder('Grado') !!}
    </div>
</div>
<div class="row align-items-end">
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->checkbox('informe_calificaciones_inea')->id('informe_calificaciones_inea') !!}
        {!! html()->label('Informe de calificaciones INEA')->for('informe_calificaciones_inea')->class('ml-2
        mb-0') !!}
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-center">
        {!! html()->label('Constancias de Capacitación:')->for('constancias_capacitacion') !!}
        {!! html()->text('constancias_capacitacion_numero')->class('form-control
        ml-2')->placeholder('Numero')->style('max-width: 100px;') !!}
        {!! html()->text('constancias_capacitacion_horas')->class('form-control
        ml-2')->placeholder('Horas')->style('max-width: 100px;') !!}
    </div>
</div>

<!-- Información de la Unidad Operativa -->
<div class="col-12 mb-2 mt-4">
    <h6 class="fw-bold">Información de la Unidad Operativa</h6>
</div>
<div class="row align-items-end">
    <div class="col-md-6 mb-3">
        {!! html()->label('Unidad Operativa')->for('unidad_operativa') !!}
        {!! html()->text('unidad_operativa')->class('form-control')->placeholder('Ingresa la Unidad Operativa')
        !!}
    </div>
    <div class="col-md-6 mb-3">
        {!! html()->label('Círculo de estudio')->for('circulo_estudio') !!}
        {!! html()->text('circulo_estudio')->class('form-control')->placeholder('Ingresa el Círculo de Estudio')
        !!}
    </div>
</div>
<div class="row align-items-end">
    <div class="col-md-4 mb-3">
        {!! html()->label('Fecha de llenado del registro')->for('fecha_llenado_registro') !!}
        {!! html()->text('fecha_llenado_registro')->class('form-control') !!}
    </div>
    <div class="col-md-4 mb-3">
        {!! html()->label('Nombre completo de la persona beneficiaria del
        INEA')->for('nombre_beneficiario_inea') !!}
        {!! html()->text('nombre_beneficiario_inea')->class('form-control') !!}
    </div>
    <div class="col-md-4 mb-3">
        {!! html()->label('Nombre completo de la persona que capturó')->for('nombre_capturo') !!}
        {!! html()->text('nombre_capturo')->class('form-control') !!}
    </div>
</div>