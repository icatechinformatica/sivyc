{{-- Realizado por Jose Luis Moreno Arcos --}}
@extends('theme.formatos.vlayout_conv')
@section('title', 'Convenio especifico | SIVyC Icatech')
@section('css')
    <style>
        img.izquierda {float: left;width: 31%;height: 60px;}
        img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 100%;
            }
        img.derechabot {position:fixed;right: 50px;width: 350px;height: 60px;}
        img.derecha {float: right;width: 28%;height: 60px;}
        .tablas{border-collapse: collapse;width: 100%;}
        /* agregamos a 3 el padding para que no salte a la otra pagina y la deje en blanco */
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        /* .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;} */
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;}

            .color_dina{
                color: #000;
            }
            .negrita{
                font-weight: bold;
            }

            .encabezado {
                position: relative; /* Cambia de fixed a relative */
                padding: 10px;
                margin-right: 10px;
            }

            .logo {
                position: absolute;
                top: -0px;
                right: 100px;
                z-index: 9999; /* Asegura que la imagen esté en el frente */
            }

            .encabezado img {
                width: auto;
                height: 60px;
            }

            /* Estilos en el logo de cerss */
            .encabezado_cerss {
                position: relative;
                padding: 10px;
                margin-right: -5px;
            }

            .logo_cerss {
                position: absolute;
                top: 4px;
                right: 118px;
                z-index: 9999;
            }

            .encabezado_cerss img {
                width: 31%;
                height: 55px;
            }
            .estilo_p{
                line-height: 1.5;
            }
    </style>
@endsection

@php
    #validaciones
    $id_cerss = null;
    $municipio = $dura = $unidad = $letradia = $hini = $hfin = $tcapacitacion = $instructor
    = $cursoind = $cespecifico = $depen = $costo = $inicio = $termino = $cernombre =
    $cerdirecc = $instructor_mespecialidad = $totalp = $diace = $mesce = $anioce =
    $diaini = $diafin = $mesini = $mesfin =  $anioini = $aniofin = $nombre_titular = $diagen =
    $mesgen = $aniogen = $cgeneral = 'DATO REQUERIDO';
    #tbl_cursos
    if ($data1) {
        if ($data1->muni) $municipio = $data1->muni;
        if ($data1->dura) $dura = $data1->dura;
        if ($data1->unidad) $unidad = $data1->unidad;
        if ($data1->hini) $hini = $data1->hini;
        if ($data1->hfin) $hfin = $data1->hfin;
        if ($data1->tcapacitacion) $tcapacitacion = $data1->tcapacitacion;
        if ($data1->nombre) $instructor = $data1->nombre;
        if ($data1->curso) $cursoind = $data1->curso;
        if ($data1->cespecifico) $cespecifico = $data1->cespecifico;
        if ($data1->depen) $depen = $data1->depen;
        if ($data1->depen_representante) $nombre_titular = $data1->depen_representante;
        if ($data1->costo) $costo = $data1->costo;
        if ($data1->fcespe) $diace = $data1->dia; $mesce = $data1->mes; $anioce = $data1->anio;
        if ($data1->inicio) $diaini = $data1->diaini; $mesini = $data1->mesini; $anioini = $data1->anioini;
        if ($data1->termino) $diafin = $data1->diafin; $mesfin = $data1->mesfin; $aniofin = $data1->aniofin;
        if ($data1->fcgen != null) $diagen = $data1->diagen; $mesgen = $data1->mesgen; $aniogen = $data1->aniogen;
        if ($data1->cgeneral != null) $cgeneral = $data1->cgeneral;
        if ($data1->letradia) $letradia = $data1->letradia;


        if ($data1->id_cerss) {
            $id_cerss = $data1->id_cerss;
            if ($data1->cernombre) $cernombre = $data1->cernombre;
            if ($data1->cerdirecc) $cerdirecc = $data1->cerdirecc;
            if ($data1->letradia) $letradia = $data1->letradia;
            if ($data1->instructor_mespecialidad) $instructor_mespecialidad = $data1->instructor_mespecialidad;
        }
        if ($data1->totalp) $totalp = $data1->totalp;
    }
    #tbl_unidades
    $dunidad = $pdunidad = $dgeneral = $direccion = $academico = $pacademico =
    $vinculacion = $pvinculacion = 'DATO REQUERIDO';

    if ($data2) {
        if ($data2->dunidad) $dunidad = $data2->dunidad;
        if ($data2->pdunidad) $pdunidad = $data2->pdunidad;
        if ($data2->dgeneral) $dgeneral = $data2->dgeneral;
        if ($data2->direccion) $direccion = $data2->direccion;
        if ($data2->academico) $academico = $data2->academico;
        if ($data2->pacademico) $pacademico = $data2->pacademico;
        if ($data2->vinculacion) $vinculacion = $data2->vinculacion;
        if ($data2->pvinculacion) $pvinculacion = $data2->pvinculacion;
    }

    #organismos_publicos
    $direccion_org = $logo_instituto = $siglas_inst = $cargo_fun =
    $poder_pertenece = 'DATO REQUERIDO';
    if ($data3) {
        // if($data3->nombre_titular) $nombre_titular = $data3->nombre_titular;
        if($data3->direccion) $direccion_org = $data3->direccion;
        if($data3->logo_instituto) $logo_instituto = $data3->logo_instituto;
        if($data3->siglas_inst) $siglas_inst = $data3->siglas_inst;
        if($data3->cargo_fun) $cargo_fun = $data3->cargo_fun;
        if($data3->poder_pertenece) $poder_pertenece = $data3->poder_pertenece;
    }

@endphp

@section('header')
        <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
        @if ($id_cerss != null)
            {{-- Logo cuando es cerss --}}
            <div class="encabezado_cerss">
                <div class="logo_cerss">
                    <img src="{{public_path('img/organismos/secretaria_proteccion_c.jpg')}}" alt="Logo">
                </div>
            </div>
        @else
            {{-- Condición para mostrar los logos de los convenios normales --}}
            @if ($logo_instituto)
                <div class="encabezado">
                    <div class="logo">
                        @if ($diferencia == 'local')
                            <img src="{{public_path($logo_instituto)}}" alt="Logo">
                        @endif
                        @if ($diferencia == 'web')
                            <img src="{{$logo_instituto}}" alt="Logo">
                        @endif
                    </div>
                </div>
            @else
                <div class="encabezado">
                    <div class="logo">
                        <img src="{{public_path('img/organismos/no_image.jpg')}}" alt="Logo">
                    </div>
                </div>
            @endif
        @endif
@endsection


@section('body')
    <div class="contenedor">
        <h6 align=center style="margin-top: 5px;">CONVENIO ESPECIFICO</h6>
        <div>
            <div align=right style="font-size:12px;"><span class="color_dina negrita">{{$cespecifico}}</span></div>
        </div>
        <br>

        <div class="">
            {{-- nueva version --}}
            <div align="justify" style="font-size:12px;" class="estilo_p">
                CONVENIO ESPECÍFICO DE COLABORACIÓN INTERISTITUCIONAL PARA LA PRESTACIÓN DE SERVICIOS EN MATERIA DE
                CAPACITACIÓN, QUE CELEBRAN POR UNA PARTE EL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL
                ESTADO DE CHIAPAS, A QUIEN EN LO SUCESIVO SE DENOMINARÁ <span class="negrita">“EL ICATECH”</span>,
                REPRESENTADO EN ESTE ACTO POR EL (LA) C. <span class="color_dina negrita">{{$dunidad}}</span>, EN SU
                CARÁCTER DE <span class="color_dina">{{$pdunidad}} {{$unidad}}</span> Y POR LA OTRA PARTE EL (LA)
                <span class="color_dina negrita">{{$depen}}</span>, A QUIEN EN LO SUCESIVO SE LE DENOMINARÁ
                <span class="color_dina negrita">“EL/LA {{$siglas_inst}}</span>”, REPRESENTADO EN ESTE ACTO POR EL (LA) C.
                <span class="color_dina negrita">{{$part_firm_user != null && count($part_firm_user) == 3 ? $part_firm_user[0] : $nombre_titular}}</span>;
                EN SU CARÁCTER DE <span class="color_dina">{{$part_firm_user != null && count($part_firm_user) == 3 ? $part_firm_user[2] : $cargo_fun}}</span>,
                MISMOS QUE CUANDO ACTÚEN DE MANERA CONJUNTA SERÁN DENOMINADOS COMO <span class="negrita">LAS “PARTES”</span>
                SUJETÁNDOSE AL TENOR DE LOS ANTECEDENTES, DECLARACIONES Y CLÁUSULAS SIGUIENTES:
            </div>

            <br><br>
            <div align=center style="font-size:14px;"><b>A N T E C E D E N T E S</b></div>
            <br>

            <div align="justify" style="font-size:12px;" class="estilo_p">
                <span class="negrita">ÚNICO. -</span> CON FECHA <span class="color_dina negrita">{{sprintf("%02d", $diagen)}}
                DE {{strtoupper($mesgen)}} DE {{$aniogen}}</span> <span class="negrita">“EL ICATECH”</span> Y
                <span class="color_dina negrita">“{{$siglas_inst}}”</span>, SUSCRIBIERON CONVENIO GENERAL DE COLABORACIÓN
                INTERINSTITUCIONAL CON NÚMERO <span class="color_dina negrita">{{$cgeneral}}</span> EN EL QUE SE ESTABLECIERON LAS BASES TENDIENTES AL MEJOR APROVECHAMIENTO
                DE SUS RECURSOS, CON EL FIN DE ALCANZAR SUS OBJETIVOS Y LOGRAR ASI UN MAYOR IMPACTO A FAVOR DE LA SOCIEDAD
                CHIAPANECA, A TRAVÉS DE LA FORMALIZACIÓN DE CONVENIOS ESPECÍFICOS EN LOS QUE SE ESTABLECERÁN LAS
                PARTICULARIDADES DE LAS ACTIVIDADES A REALIZAR Y LOS TIEMPOS DETERMINADOS PARA ELLO.
            </div>

            <br><br>
            <div align=center style="font-size:14px;"><b>D E C L A R A C I O N E S</b></div>
            <br>

            <div align="left" style="font-size:12px;">
                <span class="negrita">I.   DECLARA "EL ICATECH" QUE:</span>
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>I.1</b>     ES UN ORGANISMO DESCENTRALIZADO DE LA ADMINISTRACIÓN PÚBLICA
                ESTATAL CON PERSONALIDAD JURÍDICA Y PATRIMONIO PROPIO, CONFORME A LO DISPUESTO
                EN EL ARTÍCULO 1 DEL DECRETO NÚMERO 182, PUBLICADO EN EL PERIÓDICO OFICIAL
                NÚMERO 032, DE FECHA 26 DE JULIO DEL AÑO 2000 Y DEL DECRETO NÚMERO 183, POR EL QUE
                SE REFORMAN, DEROGAN Y ADICIONAN DIVERSAS DISPOSICIONES DEL DECRETO POR EL QUE
                SE CREA EL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE
                CHIAPAS, PUBLICADO EN EL PERIÓDICO OFICIAL NÚMERO 094, DE FECHA 21 DE MAYO DEL
                AÑO 2008.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>I.2</b> LA (EL) <span class="color_dina negrita">{{$dunidad}}</span>, TIENE PERSONALIDAD JURÍDICA PARA
                REPRESENTAR EN ESTE ACTO AL <span class="color_dina negrita">“ICATECH”</span>, EN SU CARÁCTER DE
                <span class="color_dina negrita">{{$pdunidad}} {{$unidad}}</span>, COMO LO ACREDITA CON EL NOMBRAMIENTO EXPEDIDO
                POR LA <span class="color_dina negrita">{{$dgeneral}},</span> EN SU CARÁCTER DE <span class="color_dina negrita">TITULAR
                DE LA DIRECCION GENERAL</span>, Y CUENTA CON PLENA FACULTAD LEGAL PARA SUSCRIBIR EL
                PRESENTE CONVENIO ESPECÍFICO DE COLABORACIÓN CONFORME A LO DISPUESTO POR EL
                ARTICULO 42 FRACCIÓN I Y II DEL REGLAMENTO INTERIOR DEL INSTITUTO DE
                CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>I.3</b> TIENE POR OBJETO IMPARTIR E IMPULSAR LA CAPACITACIÓN PARA EL TRABAJO EN LA
                ENTIDAD, PROCURANDO LA MEJOR CALIDAD Y VINCULACIÓN DE ESTE SERVICIO CON EL
                APARATO PRODUCTIVO Y LAS NECESIDADES DE DESARROLLO REGIONAL, ESTATAL Y
                NACIONAL; PROMOVER LA IMPARTICIÓN DE CURSOS DE CAPACITACIÓN A OBREROS EN
                MANO DE OBRA CALIFICADA, QUE CORRESPONDEN A LAS NECESIDADES DE LOS MERCADOS
                LABORALES DEL ESTADO; APOYAR LAS ACCIONES DE CAPACITACIÓN PARA EL TRABAJO DE
                LOS SECTORES PRODUCTIVOS DEL ESTADO, ASÍ COMO LA CAPACITACIÓN TANTO PARA EL
                TRABAJO DE PERSONAS SIN EMPLEO O DISCAPACITADAS, COMO NO EGRESADOS DE
                PRIMARIAS, SECUNDARIAS O PREPARATORIAS Y AUMENTAR CON LOS PROGRAMAS DE
                CAPACITACIÓN EL NIVEL DE PRODUCTIVIDAD DE LOS TRABAJADORES.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>I.4</b>     PARA EFECTOS DEL PRESENTE CONVENIO, SEÑALA COMO SU DOMICILIO LEGAL, EL UBICADO
                @php $direccionc = explode("*", $direccion);  @endphp
                <span class="color_dina">@foreach($direccionc as $point => $ari){{$ari}}@endforeach</span>.
            </div>
            <br>
            <div align="left" style="font-size:12px;">
                <b>II.   DECLARA <span class="color_dina negrita">"{{$siglas_inst}}"</span> QUE:</b>
            </div>
            <br>
            {{-- pendiente de contenido nuevo homologar --}}
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>II.1</b> EL (LA) C.<span class="color_dina negrita">{{$part_firm_user != null && count($part_firm_user) == 3 ? $part_firm_user[0] : $nombre_titular}}</span>,
                EN SU CARÁCTER DE <span class="color_dina">{{$part_firm_user != null && count($part_firm_user) == 3 ? $part_firm_user[2] : $cargo_fun}}</span>,
                TIENE PLENA CAPACIDAD JURÍDICA Y VOLUNTAD PARA CELEBRAR Y SUSCRIBIR EL PRESENTE CONVENIO ESPECÍFICO DE PRESTACIÓN DE SERVICIOS EN MATERIA DE CAPACITACIÓN, OBLIGÁNDOSE EN TODOS SUS TÉRMINOS.
                <br><br>
                <b>II.2</b> ACREDITA SU PERSONALIDAD JURIDICA CON ….. (8)
                <br><br>
                <b>II.3</b> PARA EFECTOS DEL PRESENTE CONVENIO, SEÑALA COMO SU DOMICILIO LEGAL, EL UBICADO EN {{$direccion_org}}
            </div>
            <br>
            <div align="left" style="font-size:12px;">
                <b>III.   DECLARAN "LAS PARTES" QUE:</b>
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>III.1 ÙNICA.-</b> RECONOCEN MUTUAMENTE LA CAPACIDAD JURÍDICA QUE SE OSTENTAN EN LA
                CELEBRACIÓN DEL PRESENTE INSTRUMENTO, POR LO QUE MANIFIESTAN SU INTERÉS EN
                CELEBRARLO PARA CONTRIBUIR A LA REALIZACIÓN DE SUS OBJETIVOS COMUNES MEDIANTE
                EL INTERCAMBIO DE APOYO ACADÉMICO, LOGÍSTICO Y OPERATIVO, SUJETÁNDOSE PARA TAL
                EFECTO A LAS SIGUIENTES:
            </div>
            <br>
            <div align="center" style="font-size:14px;">
                <b>C L Á U S U L A S</b>
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>PRIMERA. - DEL OBJETO.</b> EL PRESENTE CONVENIO TIENE POR OBJETO ESTABLECER LAS BASES
                CONFORME A LAS CUALES, <b>LAS “PARTES”</b> DESARROLLARÁN ACTIVIDADES EN MATERIA DE
                CAPACITACIÓN PARA EL TRABAJO.
            </div>
            <br>
            <div align="justify" style="font-size:12px; margin-bottom:10px;" class="estilo_p">
                <b>SEGUNDA. - DEL CUMPLIMIENTO.</b> PARA DAR CUMPLIMIENTO AL OBJETO DEL PRESENTE CONVENIO ESPECIFICO,
                <b>LAS “PARTES”</b> CONVIENEN QUE <b>“ICATECH”</b> OFRECERÁ A <span class="color_dina negrita">“{{$siglas_inst}}”</span>
                LOS SERVICIOS DE CAPACITACIÓN CONSISTENTES EN EL (LOS) CURSO(S) RELACIONADO(S) EN LA SIGUIENTE TABLA
                A TRAVÉS DEL DEPARTAMENTO DE VINCULACIÓN DE LA UNIDAD DE CAPACITACIÓN {{$unidad}}.
                {{-- <span class="color_dina negrita">“
                    @foreach ($allcourses as $index => $curso)
                        {{$index+1 < count($allcourses) ? $curso->curso.', ' : $curso->curso.'"'}}
                    @endforeach
                </span> --}}
                {{-- AQUI SE COLOCA LA TABLA --}}
                {{-- tabla --}}

            <br>
            </div>
            @if (!$id_cerss)
                <table class="tablas" border="1">
                    <thead>
                        {{-- <tr>
                            <th style="font-size:12px;"><b>NOMBRE DEL CURSO</b></th>
                            <th style="font-size:12px;"><b>NÚMERO</b></th>
                            <th style="font-size:12px;"><b>COSTO</b></th>
                            <th style="font-size:12px;"><b>HORAS</b></th>
                            <th style="font-size:12px;"><b>HORARIO</b></th>
                            <th style="font-size:12px;"><b>FECHA INICIO/TERMINO DEL CURSO</b></th>
                        </tr> --}}
                        <tr>
                            <th style="font-size:10px;"><b>ESPECIALIDAD</b></th>
                            <th style="font-size:10px;"><b>NOMBRE DEL CURSO</b></th>
                            <th style="font-size:10px;"><b>CLAVE DE GRUPO</b></th>
                            <th style="font-size:10px;"><b>MODALIDAD</b></th>
                            <th style="font-size:10px;"><b>COSTO DE RECUPERACIÓN</b></th>
                            <th style="font-size:10px;"><b>DURACIÓN</b></th>
                            <th style="font-size:10px;"><b>NUMERO DE ALUMNOS</b></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($allcourses as $key => $curso)
                        {{-- <tr>
                            <td style="font-size:12px;" align="left">{{strtoupper($curso->curso)}}</td>
                            <td style="font-size:12px;">{{$array_folios[$key]}}</td>
                            <td style="font-size:12px;">${{$curso->costo}}</td>
                            <td style="font-size:12px;">{{$curso->dura}}</td>
                            <td style="font-size:12px;">{{date("H:i", strtotime($curso->hini))}} A {{date("H:i", strtotime($curso->hfin))}} HORAS</td>
                            <td style="font-size:12px;">{{sprintf("%02d", $curso->diainic).'/'.sprintf("%02d", $curso->mesinic).'/'.$curso->anioinic}} AL {{sprintf("%02d", $curso->diafinc).'/'.sprintf("%02d", $curso->mesfinc).'/'.$curso->aniofinc}}</td> --}}
                            {{-- <td style="font-size:12px;">{{$array_folios[$key]}} A {{$curso->diaconvenio}} DE {{strtoupper($curso->mesconvenio)}} DEL {{$curso->anioconvenio}}</td> --}}
                            {{-- <td style="font-size:12px;">{{strtoupper($curso->hini)}} A {{strtoupper($curso->hfin)}} HRS {{$curso->observaciones != 'NINGUNO' ? '/ '.$curso->observaciones : ''}}</td> --}}
                            {{-- <td style="font-size:12px;">DEL {{$curso->diainic}} {{$curso->mesinic != $curso->mesfinc ? 'DE '.strtoupper($curso->mesinic) : ''}}
                                {{$curso->anioinic != $curso->aniofinc ? 'DEL '.$curso->anioinic : ''}} AL {{$curso->diafinc}} DE {{strtoupper($curso->mesfinc)}} DEL AÑO {{$curso->aniofinc}}
                            </td> --}}
                        {{-- </tr> --}}

                        <tr>
                            <td style="font-size:10px;">{{$curso->espe}}</td>
                            <td style="font-size:10px;">{{$curso->curso}}</td>
                            <td style="font-size:10px;">{{$curso->clave}}</td>
                            <td style="font-size:10px;">{{$curso->tcapacitacion}}</td>
                            <td style="font-size:10px;">${{($curso->costo)/($curso->hombre + $curso->mujer)}}</td>
                            <td style="font-size:10px;">{{$curso->dura.' HRS'}}</td>
                            <td style="font-size:10px;">{{$curso->hombre + $curso->mujer}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
            <br>
            <div align="left" style="font-size:12px;" class="estilo_p">
                <div align="justify">
                    <b>TERCERA.– VIGENCIA LAS “PARTES”</b> ACUERDAN QUE LA CAPACITACIÓNES SE IMPARTIRÁN EN EL
                    PERIODO COMPRENDIDO DE <span class="color_dina negrita">{{sprintf("%02d", $diaini)}}{{$mesini != $mesfin ? 'DE '.strtoupper($mesini) : ''}}
                    {{$anioini != $aniofin ? 'DEL '.$anioini : ''}} AL {{sprintf("%02d", $diafin)}} DE {{strtoupper($mesfin)}} DEL AÑO {{$aniofin}}
                    ({{$letradia}}) DE {{date("H:i", strtotime($hini))}} A {{date("H:i", strtotime($hfin))}} HRS </span>. CUBRIENDO UN TOTAL DE <span class="color_dina negrita">{{$dura.' HORAS'}} </span>
                    EMITIÉNDOSE AL CONCLUIR LA MISMA UNA CONSTANCIA DE CAPACITACIÓN CON VALIDEZ OFICIAL POR PARTE DE LA SECRETARIA
                    DE EDUCACIÓN PÚBLICA <b>(SEP)</b> EXPEDIDA POR EL <b>“ICATECH”</b>.
                </div>
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>CUARTA.</b> - <b><span class="color_dina negrita">“{{$siglas_inst}}”</span></b> ACEPTA LOS PLANES Y PROGRAMAS
                QUE <b>“ICATECH”</b> DESARROLLÓ PARA DAR CUMPLIMIENTO A LO ESTABLECIDO EN EL OBJETO DE ESTE CONVENIO; CUMPLIR CON LAS
                NORMAS ESTABLECIDAS POR ÉSTE EN MATERIA DE SEGURIDAD E HIGIENE DENTRO DE LA UNIDAD
                DE CAPACITACIÓN, ASÍ COMO INTEGRAR GRUPOS MÍNIMOS DE <span class="color_dina">15 PERSONAS</span> PARA RECIBIR
                CAPACITACIÓN DE CALIDAD.
                {{-- {{$totalp}} --}}
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>QUINTA. - “EL ICATECH”</b> SERÁ RESPONSABLE DEL REGISTRO DE ALUMNOS, ASÍ COMO DE LA
                EMISIÓN DE LA CONSTANCIA CORRESPONDIENTE, SIEMPRE Y CUANDO ESTOS EFECTÚEN EL PAGO DE LA CUOTA DE RECUPERACIÓN
                REFERIDA EN LA CLÁUSULA SEGUNDA, CUMPLAN CON LAS HORAS DEL CURSO Y ACREDITEN LAS EVALUACIONES CORRESPONDIENTES.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>SEXTA-</b> PARA QUE LOS ALUMNOS PROPUESTOS POR <b><span class="color_dina">“{{$siglas_inst}}”</span></b>
                ESTÉN EN CONDICIONES DE RECIBIR EL RECONOCIMIENTO OFICIAL CONSISTENTE EN LAS CONSTANCIAS POR PARTE DE <b>“EL
                ICATECH”</b>, DEBERÁN ACUDIR PUNTUAL Y REGULARMENTE A LOS CURSOS DE CAPACITACIÓN, PARA
                TAL EFECTO, <b>“ICATECH”</b> LLEVARÁ UN REGISTRO DE ASISTENCIA DE LOS MISMOS; QUE SERÁ
                REALIZADO POR EL INSTRUCTOR QUE IMPARTIRÁ EL CURSO.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>SÉPTIMA.- DE LA RELACIÓN LABORAL LAS “PARTES”</b> CONVIENEN QUE EL PERSONAL DESIGNADO, CONTRATADO O
                COMISIONADO PARA LA REALIZACIÓN DE LOS OBJETIVOS DEL PRESENTE CONVENIO, ESTARÁ BAJO
                LA SUPERVISIÓN DIRECTA DE LA PARTE QUE LO DESIGNE, CONTRATE O COMISIONE Y POR LO
                TANTO, EN NINGÚN MOMENTO SE CONSIDERARÁ A LA OTRA PARTE COMO EMPLEADOR SUSTITUTO,
                POR LO QUE LA MISMA NO TENDRÁ RELACIÓN ALGUNA DE CARÁCTER LABORAL CON DICHO
                PERSONAL Y CONSECUENTEMENTE QUEDA LIBERADA DE CUALQUIER RESPONSABILIDAD QUE
                PUDIERA PRESENTARSE EN MATERIA DE TRABAJO Y SEGURIDAD SOCIAL.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>OCTAVA.- FALTA DE CUMPLIMIENTO.</b> EL PRESENTE ACTO JURÍDICO DEJARÁ DE SURTIR EFECTOS LEGALES CUANDO ALGUNA
                DE <b>LAS “PARTES”</b> INCURRA EN INCUMPLIMIENTO DE CUALQUIERA DE LAS OBLIGACIONES QUE EN
                ESTE INSTRUMENTO JURÍDICO CONTRAEN; LO QUE DETERMINEN <b>“LAS PARTES”</b> POR MUTUO
                CONSENTIMIENTO O CUANDO ALGUNA DE ELLAS COMUNIQUE A LA OTRA POR ESCRITO SU DESEO
                DE DARLO POR CONCLUIDO, LO QUE DEBERÁ COMUNICARSE POR ESCRITO Y SURTIRÁ SUS
                EFECTOS LEGALES TRES DÍAS DESPUÉS DE RECIBIDA LA NOTIFICACIÓN, SIN PERJUICIO DEL
                CUMPLIMIENTO DE LAS ACCIONES QUE SE ESTÉN OPERANDO.

            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>NOVENA.- COORDINACIÓN Y EJECUCIÓN.</b> PARA EL ADECUADO DESARROLLO Y SEGUIMIENTO DE LAS ACTIVIDADES A
                QUE SE REFIERE EL PRESENTE INSTRUMENTO LAS <b>“PARTES”</b> DESIGNAN COMO RESPONSABLES: POR PARTE DE ICATECH,
                SE DESIGNA A {{-- PENDIENTE --}} POR PARTE DE <b><span class="color_dina">“{{$siglas_inst}}”</span></b>
                SE DESIGNA A {{-- PENDIENTE --}}
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                <b>DÉCIMA.- CONFIDENCIALIDAD Y DATOS PERSONALES.</b> TODA LA INFORMACIÓN PERSONAL QUE SE TRANSMITA O GENERE CON
                MOTIVO DE LA REALIZACIÓN DEL PRESENTE CONVENIO ESPECIFICO, SERÁ MANEJADA EN LOS TÉRMINOS Y LINEAMIENTOS
                DESCRITOS EN LA LEY DE PROTECCIÓN DE DATOS PERSONALES EN POSESIÓN DE SUJETOS OBLIGADOS DEL ESTADO DE CHIAPAS Y
                LA LEY DE TRANSPARENCIA Y ACCESO A LA INFORMACIÓN PÚBLICA DEL ESTADO DE CHIAPAS.
                <br>
                POR LO QUE, POR ESTE MEDIO SE DA POR MANIFIESTO EL CONSENTIMIENTO PARA QUE EL MANEJO DE LOS DATOS SEAN CONFORME
                LO DICTAN LAS LEYES EN MATERIA; ASIMISMO, SE INFORMA QUE EN TODO MOMENTO EL TITULAR O SU REPRESENTANTE PODRÁN
                SOLICITAR A “ICATECH” EL ACCESO, RECTIFICACIÓN, CANCELACIÓN U OPOSICIÓN AL TRATAMIENTO DE LOS DATOS PERSONALES
                QUE LE CONCIERNE, DE CONFORMIDAD CON LO ESTABLECIDO EN EL CAPÍTULO I DEL TÍTULO TERCERO DE LA LEY DE PROTECCIÓN
                DE DATOS PERSONALES EN POSESIÓN DE SUJETOS OBLIGADOS DEL ESTADO DE CHIAPAS.
                <br>
                LOS RESPECTIVOS AVISOS DE PRIVACIDAD PUEDEN SER CONSULTADOS EN LA SIGUIENTE
                LIGA: HTTPS://WWW.ICATECH.GOB.MX/AVISOS-DE-PRIVACIDAD.
            </div>
            <br>
                <div align="justify" style="font-size:12px;" class="estilo_p">
                    <b>DÉCIMA PRIMERA.-</b> NINGUNA DE <b>LAS “PARTES”</b> SERÁ RESPONSABLE DE CUALQUIER
                    RETRASO O INCUMPLIMIENTO EN LA REALIZACIÓN DEL PRESENTE CONVENIO, CUANDO SEA
                    RESULTADO DIRECTO O INDIRECTAMENTE DE ALGÚN CASO FORTUITO O DE FUERZA MAYOR.
                </div>
                <br>
                <div align="justify" style="font-size:12px;" class="estilo_p">
                    <b>DÉCIMA SEGUNDA.– LAS “PARTES”</b> MANIFIESTAN QUE EL PRESENTE ACTO JURÍDICO ES
                    PRODUCTO DE LA BUENA FE, EN RAZÓN DE LO CUAL, LOS CONFLICTOS QUE LLEGARÁN A
                    PRESENTARSE EN CUANTO A SU INTERPRETACIÓN, FORMALIZACIÓN Y CUMPLIMIENTO,
                    SERÁN RESUELTOS DE COMÚN ACUERDO POR LAS <b>“PARTES”</b>.
                </div>
                <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                NO OBSTANTE, LO ANTERIOR, EN CASO DE NO LLEGAR A ALGÚN ACUERDO, <b>LAS “PARTES”</b> SE
                SOMETEN EXPRESAMENTE A LA JURISDICCIÓN DE LOS TRIBUNALES COMPETENTES DE <b>LA CIUDAD
                DE TUXTLA GUTIÉRREZ, CHIAPAS;</b> RENUNCIANDO A AQUELLA QUE PUDIERA
                CORRESPONDERLES EN RAZÓN DE SUS DOMICILIOS PRESENTES <span style="margin-right: 4px;">O</span> FUTUROS.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                ENTERADAS <b>LAS “PARTES”</b> DEL CONTENIDO Y ALCANCE LEGAL DEL PRESENTE CONVENIO ESPECIFICO, LO
                FIRMAN DE CONFORMIDAD Y POR DUPLICADO ANTE LA PRESENCIA DE LOS TESTIGOS QUE AL FINAL
                Y AL CALCE LO SUSCRIBEN, EN LA CIUDAD DE <span class="color_dina">{{$municipio}}</span>, CHIAPAS,
                EL DÍA <span class="color_dina">{{sprintf("%02d", $diace)}} DEL
                MES DE {{strtoupper($mesce)}} DEL AÑO {{$anioce}}</span>.
            </div>
            <br><br><br>
            {{-- tabla para agregar las firmas --}}
            @if ($id_cerss)
                <table align="center">
                    <thead>
                        <tr>
                            <th colspan="4" style="border: hidden; font-size:12px; vertical-align: top;" align="center">
                                POR <span class="color_dina negrita">"{{$siglas_inst}}"</span>
                                <br><br><br>
                                    <span>_______________________________________</span>
                                    <br><span class="color_dina negrita">{{$part_firm_cer1 != null ? $part_firm_cer1[0] : $nombre_titular}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">
                                        @if ($part_firm_cer1 != null && count($part_firm_cer1) == 3)
                                            {{$part_firm_cer1[1]}} <br> {{$part_firm_cer1[2]}}
                                        @else
                                            {{$cargo_fun}} <br> {{$depen}}
                                        @endif

                                    </span>

                            </th>
                            <th colspan="4" style="border: hidden; font-size:12px; vertical-align: top;" align="center">
                                POR "EL ICATECH"
                                <br><br><br>
                                    <span>________________________________________</span>
                                    <br><span class="color_dina negrita">{{$dunidad}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$pdunidad}} <br> {{$unidad}}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody >
                    </tbody>
                </table>
            @else
                <table align="center">
                    <thead>
                        <tr>
                            <th colspan="4" style="border: hidden; font-size:12px; vertical-align: top;" align="center">
                                POR <span class="color_dina negrita">"{{$siglas_inst}}"</span>
                                    <br>
                                    <br>
                                    <br>_______________________________________
                                    <br><span class="color_dina negrita">{{$part_firm_user != null ? $part_firm_user[0] :  $nombre_titular}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">
                                        {{-- {{$part_firm_user != null ? $part_firm_user[1] : $data1->depen.`<br>`.$data3->cargo_fun}} --}}
                                        @if ($part_firm_user != null && count($part_firm_user) == 3)
                                            {{$part_firm_user[1]}} <br> {{$part_firm_user[2]}}
                                        @else
                                            {{$depen}} <br> {{$cargo_fun}}
                                        @endif

                                    </span>
                            </th>
                            <th colspan="4" style="border: hidden; font-size:12px; vertical-align: top;" align="center">
                                POR "EL ICATECH"
                                    <br>
                                    <br>
                                    <br>________________________________________
                                    <br><span class="color_dina negrita">{{$dunidad}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$pdunidad}} <br> {{$unidad}}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody >
                    </tbody>
                </table>
            @endif
            <br><br><br>
            <div align="center" style="font-size:12px;">
                <b>TESTIGOS</b>
            </div>
            {{-- <br><br><br> --}}
            <br><br>
            {{-- tabla para agregar las firmas --}}
            @if($id_cerss)
                <table align="center">
                    <thead>
                        <tr>
                            <th colspan="4" style="border: hidden; font-size:12px; vertical-align: top;" align="center">

                                    <br>
                                    <br>_______________________________________
                                    <br><span class="color_dina negrita">{{$part_firm_cer2 != null ? $part_firm_cer2[0] : ''}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">
                                        @if ($part_firm_cer2 != null && count($part_firm_cer2) == 3)
                                            {{$part_firm_cer2[1]}} <br> {{$part_firm_cer2[2]}}
                                        @else
                                            CAMPO REQUERIDO
                                        @endif
                                        {{-- {{$part_firm_cer2 != null ? $part_firm_cer2[1] : ''}}</span> --}}
                            </th>
                            <th colspan="4" style="border: hidden; font-size:12px; vertical-align: top;" align="center">

                                    <br>
                                    <br>________________________________________
                                    <br><span class="color_dina negrita">{{$vinculacion}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$pvinculacion}}</span> <span class="color_dina" style="font-weight: normal;">DE LA UNIDAD DE CAPACTITACIÓN {{$unidad}}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody >

                    </tbody>
                </table>
            @else
                <table align="center">
                    <thead>
                        <tr>
                            <th colspan="4" style="border: hidden; font-size:12px; vertical-align: top;" align="center">

                                    <br>
                                    <br>_______________________________________
                                    <br><span class="color_dina negrita">{{$vinculacion}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$pvinculacion}}</span><span class="color_dina" style="font-weight: normal;"> DE LA UNIDAD DE CAPACTITACIÓN {{$unidad}}</span>
                            </th>
                            <th colspan="4" style="border: hidden; font-size:12px; vertical-align: top;" align="center">

                                    <br>
                                    <br>________________________________________
                                    <br><span class="color_dina negrita">{{$academico}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$pacademico}}</span> <span class="color_dina" style="font-weight: normal;">DE LA UNIDAD DE CAPACTITACIÓN {{$unidad}}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody >

                    </tbody>
                </table>
            @endif

            <br><br>
            <div align="justify" style="font-size:10px;" class="estilo_p">
                    LAS FIRMAS QUE ANTECEDEN, FORMAN PARTE DEL CONVENIO ESPECÍFICO DE PRESTACIÓN DE
                    SERVICIOS EN MATERIA DE CAPACITACIÓN, DE LOS CURSOS QUE CELEBRAN POR UNA PARTE EL
                    INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS Y POR LA
                    OTRA PARTE EL (LA) <span class="color_dina negrita">{{$depen}}</span>.
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(73, 760, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script>
@endsection

