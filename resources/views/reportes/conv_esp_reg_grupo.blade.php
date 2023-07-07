<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CONVENIO ESPECIFICO</title>

    <style>
        body{font-family: sans-serif}
        @page {margin: 20px 50px 120px 50px;size: letter;}
        header { position: fixed; left: 0px; top: 30px; right: 0px;text-align: center;width:100%;line-height: 30px;}
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
        footer { position:fixed;left:0px;bottom:-100px;height:0px;width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        .contenedor {
        position:RELATIVE;
        top:120px;
        width:100%;
        margin:auto;

        /* Propiedad que ha sido agreda*/

        }
        .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 60px;
                left: 25px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }
            .color_dina{
                color: #000;
            }
            .negrita{
                font-weight: bold;
            }
            /* ESTILO DE LA IMAGEN QUE SE MUESTRA EN EL ENCABEZADO */
            /* .encabezado {
                position: fixed;
                top: 10px;
                right: 0px;
                padding: 10px;
                float: right;
            } */

            .encabezado {
                position: relative;
                padding: 10px;
            }

            .logo {
                position: absolute;
                top: -10px;
                right: 100px;
                z-index: 9999;
            }

            .encabezado img {
                width: 100px;
                height: 100px;
            }

            /* Estilos en el logo de cerss */
            .encabezado_cerss {
                position: relative;
                padding: 10px;
            }

            .logo_cerss {
                position: absolute;
                top: 0px;
                right: 118px;
                z-index: 9999;
            }

            .encabezado_cerss img {
                width: 31%;
                height: 60px;
            }
    </style>
</head>
<body>
    @php
        // $id_cerrs = $data1->id_cerss;
        $id_cerss = null;
        // $id_cerss = 10;
    @endphp

    <header>
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
            @if ($id_cerss != null)
                {{-- Logo cuando es cerss --}}
                <div class="encabezado_cerss">
                    <div class="logo_cerss">
                        <img src="{{public_path('img/secretaria_proteccion_c.jpg')}}" alt="Logo">
                    </div>
                </div>
            @else
                {{-- Condición para mostrar los logos de los convenios normales --}}
                @if ($data3->logo_instituto)
                    <div class="encabezado">
                        <div class="logo">
                            @if ($diferencia == 'local')
                                <img src="{{public_path($data3->logo_instituto)}}" alt="Logo">
                            @endif
                            @if ($diferencia == 'web')
                                <img src="{{$data3->logo_instituto}}" alt="Logo">
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

    </header>
    <div class="contenedor">
        {{-- Si cerss esta activo entonces escondemos --}}
        @if (!$id_cerss)
            <h5 align=center>CONVENIO ESPECIFICO</h5>
        @endif
        {{-- crear un div para encerrar todo lo que lleva al inicio --}}
        <div>
            @if ($id_cerss)
            <div align=right style="font-size:12px;"><span class="color_dina negrita">G152T42019VIL226</span></div>
            @else
                <div align=right style="font-size:12px;"><span class="color_dina negrita">NO. {{$data1->cespecifico}}</span></div>
            @endif
        </div>
        <br>
        <div class="table-responsive-sm">
            @if ($id_cerss)
                {{-- Cerss --}}
                <div align="justify" style="font-size:12px;">
                    CONVENIO ESPECÍFICO DE COLABORACIÓN INTERINSTITUCIONAL DE PRESTACIÓN DE
                    SERVICIOS EN MATERIA DE CAPACITACIÓN, DEL CURSO DENOMINADO “MANUALIDADES A
                    BASE DE PAPEL, PLÁSTICO Y NYLON RECICLADO” QUE CELEBRAN POR UNA PARTE EL
                    INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, A
                    QUIEN EN LO SUCESIVO SE LE DENOMINARÁ “ICATECH”, REPRESENTADO POR LA MTRA.
                    FABIOLA LIZBETH ASTUDILLO REYES TITULAR DE LA DIRECCIÓN GENERAL, QUIEN EN ESTE
                    ACTO LA REPRESENTA LA LCDA. FLOR DEL ROSARIO CRUZ MAGDALENO, EN SU CARÁCTER
                    DE TITULAR DE LA UNIDAD DE CAPACITACIÓN VILLAFLORES Y POR LA OTRA PARTE, LA
                    SECRETARÍA DE SEGURIDAD Y PROTECCIÓN CIUDADANA, REPRESENTADO POR LA
                    COMISARIA GENERAL LIC. GABRIELA DEL SOCORRO ZEPEDA SOTO, QUIEN EN ESTE ACTO
                    LO REPRESENTA EL COMISARIO JEFE LIC. JOSÉ MIGUEL ALARCÓN GARCÍA, TITULAR DE LA
                    SUBSECRETARÍA DE EJECUCIÓN DE SANCIONES PENALES Y MEDIDAS DE SEGURIDAD, A
                    QUIEN EN LO SUCESIVO SE LE DENOMINARA “SECRETARÌA”, MISMOS QUE CUANDO ACTÚEN
                    DE MANERA CONJUNTA SERÁN DENOMINADOS COMO LAS “PARTES” SUJETÁNDOSE AL
                    TENOR DE LOS ANTECEDENTES, DECLARACIONES Y CLÁUSULAS SIGUIENTES:
                </div>
            @else
                {{-- Normal --}}
                <div align="justify" style="font-size:12px;">
                    CONVENIO ESPECÍFICO DE PRESTACIÓN DE SERVICIOS EN MATERIA DE CAPACITACIÓN, DE
                    EL(LOS) CURSO(S) DENOMINADO(S), <span class="color_dina negrita">“{{$data1->curso}}”</span>, QUE CELEBRAN POR UNA PARTE EL
                    INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, A QUIEN EN
                    LO SUCESIVO SE LE DENOMINARÁ <span class="negrita">“EL ICATECH”</span>, REPRESENTADO EN ESTE ACTO POR EL (LA)
                    <span class="color_dina negrita">{{$data2->dunidad}}</span>, EN SU CARÁCTER DE <span class="color_dina">{{$data2->pdunidad}} {{$data1->unidad}}</span> Y POR LA OTRA PARTE EL (LA) <span class="color_dina negrita">{{$data1->depen}}</span>,
                    A QUIEN EN LO SUCESIVO SE LE
                    DENOMINARÁ <span class="color_dina negrita">“{{$data3->siglas_inst}}”</span>, REPRESENTADO EN ESTE ACTO POR EL (LA) <span class="color_dina negrita">{{$data3->nombre_titular}}</span>;
                    EN SU CARÁCTER DE <span class="color_dina">{{$data3->cargo_fun}}</span>., MISMOS QUE CUANDO
                    ACTÚEN DE MANERA CONJUNTA SERÁN DENOMINADOS COMO <span class="negrita">“LAS PARTES”</span> SUJETÁNDOSE AL
                    TENOR DE LOS ANTECEDENTES, DECLARACIONES Y CLÁUSULAS SIGUIENTES:
                </div>
            @endif

            <br><br>
            <div align=center style="font-size:14px;"><b>A N T E C E D E N T E S</b></div>
            <br><br>

            <div align="justify" style="font-size:12px;">
                <span class="negrita">ÚNICO. -</span> CON FECHA <span class="color_dina">{{$data1->dia}} DE {{strtoupper($data1->mes)}} DEL {{$data1->anio}}</span> <span class="negrita">“EL ICATECH”</span> Y <span class="color_dina negrita">“{{$data3->siglas_inst}}”</span>, SUSCRIBIERON
                CONVENIO DE COLABORACIÓN EN EL QUE SE ESTABLECIERON LAS BASES TENDIENTES AL MEJOR
                APROVECHAMIENTO DE SUS RECURSOS, CON EL FIN DE ALCANZAR SUS OBJETIVOS Y LOGRAR ASI
                UN MAYOR IMPACTO A FAVOR DE LA SOCIEDAD CHIAPANECA, A TRAVÉS DE LA FORMALIZACIÓN DE
                CONVENIOS ESPECÍFICOS EN LOS QUE SE ESTABLECIERAN LAS PARTICULARIDADES DE LAS
                ACTIVIDADES A REALIZAR Y LOS TIEMPOS ESTABLECIDOS PARA ELLO.
            </div>

            <br><br>
            <div align=center style="font-size:14px;"><b>D E C L A R A C I O N E S</b></div>
            <br><br>

            <div align="left" style="font-size:12px;">
                <span class="negrita">I.   DECLARA "EL ICATECH" QUE:</span>
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                I.1     ES UN ORGANISMO DESCENTRALIZADO DE LA ADMINISTRACIÓN PÚBLICA
                ESTATAL CON PERSONALIDAD JURÍDICA Y PATRIMONIO PROPIO, CONFORME A LO DISPUESTO
                EN EL ARTÍCULO 1 DEL DECRETO NÚMERO 182, PUBLICADO EN EL PERIÓDICO OFICIAL
                NÚMERO 032, DE FECHA 26 DE JULIO DEL AÑO 2000 Y DEL DECRETO NÚMERO 183, POR EL QUE
                SE REFORMAN, DEROGAN Y ADICIONAN DIVERSAS DISPOSICIONES DEL DECRETO POR EL QUE
                SE CREA EL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE
                CHIAPAS, PUBLICADO EN EL PERIÓDICO OFICIAL NÚMERO 094, DE FECHA 21 DE MAYO DEL
                AÑO 2008.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    I.2 LA LCDA. FLOR DEL ROSARIO CRUZ MAGDALENO, TIENE PERSONALIDAD JURÍDICA PARA
                    REPRESENTAR EN ESTE ACTO A “ICATECH”, EN SU CARÁCTER DE TITULAR DE LA UNIDAD DE
                    CAPACITACIÓN VILLAFLORES, COMO LO ACREDITA CON EL NOMBRAMIENTO EXPEDIDO A SU
                    FAVOR POR LA MTRA. FABIOLA LIZBETH ASTUDILLO REYES, EN SU CARÁCTER DE TITULAR
                    DE LA DIRECCION GENERAL, Y CUENTA CON PLENA FACULTAD LEGAL PARA SUSCRIBIR EL
                    PRESENTE CONVENIO ESPECÍFICO DE COLABORACIÓN CONFORME A LO DISPUESTO POR EL
                    ARTICULO 42 FRACCIÓN I Y II DEL REGLAMENTO INTERIOR DEL INSTITUTO DE
                    CAPACITACIÓÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS.
                @else
                    I.2     CON FUNDAMENTO EN LO DISPUESTO POR EL ARTÍCULO 13 FRACCIÓN IV DEL REGLAMENTO
                    INTERIOR DEL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE
                    CHIAPAS, PUBLICADO EN EL PERIÓDICO OFICIAL NÚMERO 404, DE FECHA 31 DE OCTUBRE DEL
                    2018 EL (LA) <span class="color_dina">{{$data2->dgeneral}}</span> EN SU CARÁCTER DE DIRECTOR(A)
                    GENERAL, TIENE DENTRO DE SUS ATRIBUCIONES DELEGABLES LA DE CELEBRAR Y SUSCRIBIR
                    CONVENIOS, ACUERDOS, CONTRATOS Y DEMÁS ACTOS DE CARÁCTER ADMINISTRATIVO,
                    RELACIONADOS CON LOS ASUNTOS DE COMPETENCIA DE <span>“EL ICATECH”</span>.
                @endif

            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    I.3 TIENE POR OBJETO IMPARTIR E IMPULSAR LA CAPACITACIÓN PARA EL TRABAJO EN LA
                    ENTIDAD, PROCURANDO LA MEJOR CALIDAD Y VINCULACIÓN DE ESTE SERVICIO CON EL
                    APARATO PRODUCTIVO Y LAS NECESIDADES DE DESARROLLO REGIONAL, ESTATAL Y
                    NACIONAL; PROMOVER LA IMPARTICIÓN DE CURSOS DE CAPACITACIÓN A OBREROS EN
                    MANO DE OBRA CALIFICADA, QUE CORRESPONDEN A LAS NECESIDADES DE LOS MERCADOS
                    LABORALES DEL ESTADO; APOYAR LAS ACCIONES DE CAPACITACIÓN PARA EL TRABAJO DE
                    LOS SECTORES PRODUCTIVOS DEL ESTADO, ASÍ COMO LA CAPACITACIÓN TANTO PARA EL
                    TRABAJO DE PERSONAS SIN EMPLEO O DISCAPACITADAS, COMO NO EGRESADOS DE
                    PRIMARIAS, SECUNDARIAS O PREPARATORIAS Y AUMENTAR CON LOS PROGRAMAS DE
                    CAPACITACIÓN EL NIVEL DE PRODUCTIVIDAD DE LOS TRABAJADORES.
                @else
                    I.3     EN TÉRMINOS DE LO CITADO EN LA DECLARACIÓN ANTERIOR Y CON FUNDAMENTO EN LO
                    DISPUESTO POR LOS ARTÍCULOS 16 FRACCIÓN XXIV Y 29 FRACCIONES I Y II DEL REGLAMENTO
                    INTERIOR DEL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE
                    CHIAPAS, A EL (LA) <span class="color_dina negrita">{{$data2->dunidad}}</span>, SE ENCUENTRA
                    FACULTADO(A) PARA REPRESENTAR EN ESTE ACTO A <span class="negrita">“EL ICATECH”</span>, EN SU CARÁCTER DE
                    DIRECTOR(A) DE LA UNIDAD DE CAPACITACIÓN <span class="color_dina">{{$data1->unidad}}</span>, TAL Y COMO LO ACREDITA CON EL
                    NOMBRAMIENTO, EXPEDIDO A SU FAVOR POR EL (LA) <span class="color_dina">{{$data2->dgeneral}}</span>, EN SU CARÁCTER DE DIRECTOR(A) GENERAL, POR LO TANTO, CUENTA CON PLENA
                    FACULTAD LEGAL PARA SUSCRIBIR EL PRESENTE CONVENIO ESPECÍFICO DE COLABORACIÓN.
                @endif
            </div>
            <br>
            <br>
            <br>
            <div align="justify" style="font-size:12px;">
                @if (!$id_cerss)
                    I.4     TIENE POR OBJETO IMPARTIR E IMPULSAR LA CAPACITACIÓN PARA EL TRABAJO EN LA ENTIDAD,
                    PROCURANDO LA MEJOR CALIDAD Y VINCULACIÓN DE ESTE SERVICIO CON EL APARATO
                    PRODUCTIVO Y LAS NECESIDADES DE DESARROLLO REGIONAL, ESTATAL Y NACIONAL;
                    PROMOVER LA IMPARTICIÓN DE CURSOS DE CAPACITACIÓN A OBREROS EN MANO DE OBRA
                    CALIFICADA, QUE CORRESPONDEN A LAS NECESIDADES DE LOS MERCADOS LABORALES DEL
                    ESTADO; APOYAR LAS ACCIONES DE CAPACITACIÓN PARA EL TRABAJO DE LOS SECTORES
                    PRODUCTIVOS DEL ESTADO, ASÍ COMO LA CAPACITACIÓN TANTO PARA EL TRABAJO DE
                    PERSONAS SIN EMPLEO O DISCAPACITADAS, COMO NO EGRESADOS DE PRIMARIAS,
                    SECUNDARIAS O PREPARATORIAS Y AUMENTAR CON LOS PROGRAMAS DE CAPACITACIÓN EL
                    NIVEL DE PRODUCTIVIDAD DE LOS TRABAJADORES.
                @endif
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                I.5     PARA EFECTOS DEL PRESENTE CONVENIO, SEÑALA COMO SU DOMICILIO LEGAL, EL UBICADO
                <span class="color_dina">{{$data2->direccion}}</span>.
            </div>
            <br>
            <div align="left" style="font-size:12px;">
                @if ($id_cerss)
                    <b>II.   DECLARA <span class="color_dina negrita">"SECRETARIA"</span> QUE:</b>
                @else
                    <b>II.   DECLARA <span class="color_dina negrita">"{{$data3->siglas_inst}}"</span> QUE:</b>
                @endif
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    II.1 DE CONFORMIDAD CON LO ESTABLECIDO EN LOS ARTÍCULOS 1, 2 FRACCIÓN I, 21, 28
                    FRACCIÓN XV Y 43 FRACCIÓN XVI, DE LA LEY ORGÁNICA DE LA ADMINISTRACIÓN PÚBLICA
                    DEL ESTADO DE CHIAPAS, ARTÍCULOS 1, 2, 4 FRACCIÓN V, IX Y XI Y 5 DE LA LEY QUE
                    ESTABLECE LAS BASES DE OPERACIÓN DE LA SECRETARÍA DE SEGURIDAD Y PROTECCIÓN
                    CIUDADANA DEL ESTADO DE CHIAPAS, ARTÍCULOS 15 Y 16 FRACCIÓN I Y III, DEL
                    REGLAMENTO DE LA LEY QUE ESTABLECE LAS BASES DE OPERACIÓN DE LA SECRETARÍA DE
                    SEGURIDAD Y PROTECCIÓN CIUDADANA DEL ESTADO DE CHIAPAS, ES UNA DEPENDENCIA
                    CENTRALIZADA DEL EJECUTIVO ESTATAL, CON LAS ATRIBUCIONES QUE LE CONFIEREN
                    DICHOS PROGRESIVOS.
                @else
                    II.1    EL (LA) <span class="color_dina negrita">{{$data3->nombre_titular}}</span>, EN SU CARÁCTER DE <span class="color_dina">{{$data3->cargo_fun}}</span> DEL <span class="color_dina">{{$data3->poder_pertenece}}</span>, TIENE PLENA CAPACIDAD
                    JURÍDICA Y VOLUNTAD PARA CELEBRAR Y SUSCRIBIR EL PRESENTE CONVENIO ESPECÍFICO DE
                    PRESTACIÓN DE SERVICIOS EN MATERIA DE CAPACITACIÓN, OBLIGÁNDOSE EN TODOS SUS
                    TÉRMINOS; DE CONFORMIDAD CON LO DISPUESTO EN LOS ARTÍCULOS 18, SEGUNDO PÁRRAFO,
                    DE LA LEY ORGÁNICA DE LA ADMINISTRACIÓN PÚBLICA DEL ESTADO DE CHIAPAS Y 14,
                    FRACCIÓN VII DEL REGLAMENTO INTERIOR VIGENTE DE ESTA DEPENDENCIA DEL EJECUTIVO
                    ESTATAL.
                @endif

            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    II.2 EL COMISARIO JEFE LIC. JOSÉ MIGUEL ALARCÓN GARCÍA, TITULAR DE SUBSECRETARÍA
                    DE EJECUCIÓN DE SANCIONES PENALES Y MEDIDAS DE SEGURIDAD, CUENTA CON
                    FACULTADES LEGALES PARA SUSCRIBIR EL PRESENTE CONVENIO ESPECIFICO,
                    ACREDITANDO SU PERSONALIDAD CON EL NOMBRAMIENTO DE FECHA 01 DE ENERO DEL
                    2019, EXPEDIDO POR LA COMISARIA GENERAL LIC. GABRIELA DEL SOCORRO ZEPEDA SOTO,
                    TITULAR DE LA SECRETARÍA DE SEGURIDAD Y PROTECCIÓN CIUDADANA, EN TÉRMINOS DEL
                    ARTÍCULO 36 FRACCIÓN VI Y 38 FRACCIÓN XXIII, DEL REGLAMENTO DE LA LEY QUE
                    ESTABLECE LAS BASES DE OPERACIÓN DE LA SECRETARÍA DE SEGURIDAD Y PROTECCIÓN
                    CIUDADANA DEL ESTADO DE CHIAPAS, TIENE FACULTAD DE SUSCRIBIR EL PRESENTE
                    INSTRUMENTO LEGAL.
                @else
                    II.2    SEÑALA COMO SU DOMICILIO PARA EFECTOS DEL PRESENTE CONVENIO, <span class="color_dina">{{$data3->direccion}}</span>
                @endif
            </div>
            <br><br>
            {{-- SI ES CERSS SE CREA MAS CONTENIDO PARA EL CONVENIO --}}
            @if ($id_cerss)
                <div align="justify" style="font-size:12px;">
                    II.3. EL TITULAR DE LA SUBSECRETARÍA DE EJECUCIÓN DE SANCIONES PENALES Y MEDIDAS
                    DE SEGURIDAD, EN TÉRMINOS DEL ARTÍCULO 18, DE LA CONSTITUCIÓN POLÍTICA DE LOS
                    ESTADOS UNIDOS MEXICANOS, CONSECUTIVOS 1, FRACCIÓN III, 7 PÁRRAFO QUINTO, 14, 15
                    FRACCIÓN II, 72, 87, 88 Y 95, DE LA LEY NACIONAL DE EJECUCIÓN PENAL, NUMERAL 38,
                    FRACCIÓN XIII, DEL REGLAMENTO DE LA LEY QUE ESTABLECE LAS BASES DE OPERACIÓN DE
                    LA SECRETARÍA DE SEGURIDAD Y PROTECCIÓN CIUDADANA DEL ESTADO DE CHIAPAS, TIENE
                    COMO PRINCIPAL OBJETIVO LA REINSERCIÓN SOCIAL DE LAS PERSONAS PRIVADAS DE LA
                    LIBERTAD, EN ESTABLECIMIENTOS PENITENCIARIOS DE LA ENTIDAD, CON BASE DEL
                    RESPETO DE LOS DERECHOS HUMANOS, DEL TRABAJO, LA CAPACITACIÓN PARA EL MISMO,
                    LA EDUCACIÓN, LA SALUD Y EL DEPORTE Y PROCURAR QUE NO VUELVAN A DELINQUIR,
                </div>
                <br>
                <div align="justify" style="font-size:12px;">
                    II.4. SU DOMICILIO CONVENCIONAL PARA LOS EFECTOS LEGALES DEL PRESENTE ACTO
                    JURÍDICO, SE ENCUENTRA UBICADO 1 ORIENTE. SUR NO. 2237, BARRIO SAN FRANCISCO,
                    TUXTLA GUTIÉRREZ, CHIAPAS. (VERIFICAR QUE LA DIRECCIÓN SEA CORRECTA).
                </div>
                <br>
            @endif

            <div align="left" style="font-size:12px;">
                <b>III.   DECLARAN "LAS PARTES" QUE:</b>
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    III.1 ÙNICA.- RECONOCEN MUTUAMENTE LA CAPACIDAD JURÍDICA QUE SE OSTENTAN EN LA
                    CELEBRACIÓN DEL PRESENTE INSTRUMENTO, POR LO QUE MANIFIESTAN SU INTERÉS EN
                    CELEBRARLO PARA CONTRIBUIR A LA REALIZACIÓN DE SUS OBJETIVOS COMUNES MEDIANTE
                    EL INTERCAMBIO DE APOYO ACADÉMICO, LOGÍSTICO Y OPERATIVO, SUJETÁNDOSE PARA TAL
                    EFECTO A LAS SIGUIENTES:
                @else
                    III.1   RECONOCEN LA PERSONALIDAD CON QUE SE OSTENTAN EN LA CELEBRACIÓN DEL PRESENTE
                    INSTRUMENTO, POR LO QUE MANIFIESTAN SU INTERÉS EN CELEBRARLO PARA CONTRIBUIR A
                    LA REALIZACIÓN DE SUS OBJETIVOS COMUNES MEDIANTE EL INTERCAMBIO DE APOYO
                    ACADÉMICO, LOGÍSTICO Y OPERATIVO, SUJETÁNDOSE PARA TAL EFECTO A LAS SIGUIENTES:
                @endif
            </div>
            <br>
            <br>
            <div align="center" style="font-size:14px;">
                <b>C L Á U S U L A S</b>
            </div>
            <br>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    PRIMERA.- DEL OBJETO. EL PRESENTE CONVENIO TIENE POR OBJETO ESTABLECER LAS
                    BASES CONFORME A LAS CUALES, LAS “PARTES” DESARROLLARÁN ACTIVIDADES EN
                    MATERIA DE CAPACITACIÓN PARA EL TRABAJO, A FAVOR DE LAS PERSONAS PRIVADAS DE LA
                    LIBERTAD, EN RECLUSIÒN EN EL CENTRO ESTATAL DE REINSERCIÓN SOCIAL DE
                    SENTENCIADOS NO. 08, UBICADO EN EL MPIO. DE VILLA FLORES, CHIAPAS, CON DOMICILIO EN
                    LA CARRETERA FRANCISCO VILLA, ENTRONQUE AL RECREO, COL. NAMBIYUGUA, C.P. 30470.
                @else
                    <b>PRIMERA. - DEL OBJETO.</b> EL PRESENTE CONVENIO TIENE POR OBJETO ESTABLECER LAS BASES
                    CONFORME A LAS CUALES, <b>“LAS PARTES”</b> DESARROLLARÁN ACTIVIDADES EN MATERIA DE
                    CAPACITACIÓN PARA EL TRABAJO.
                @endif
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>SEGUNDA.- </b> PARA DAR CUMPLIMIENTO AL OBJETO DEL PRESENTE CONVENIO, <b>“LAS PARTES”</b>
                CONVIENEN QUE <b>“EL ICATECH”</b> OFRECERÁ A <span class="color_dina negrita">“{{$data3->siglas_inst}}”</span> LOS SERVICIOS DE CAPACITACIÓN
                CONSISTENTES EN EL CURSO DE: <span class="color_dina negrita">“{{$data1->curso}}”</span>, A TRAVÉS DEL DEPARTAMENTO DE
                VINCULACIÓN DE LA UNIDAD DE CAPACITACIÓN <span class="color_dina">{{$data1->unidad}}</span>.
            </div>
            <br>
            <div align="left" style="font-size:12px;">
                @if ($id_cerss)
                    TERCERA.– LAS “PARTES” ACUERDAN QUE LA CAPACITACIÓN SE IMPARTIRÁ DEL DÍA 10 DE
                    JULIO AL 21 DE JULIO DE 2023 (DE LUNES A VIERNES, A EXCEPCIÓN DE LOS DIAS MARTES 11
                    Y 18 DE JULIO) DE 10:00 A 12:30 HRS. CUBRIENDO UN TOTAL DE 20 HORAS DE CURSO;
                    EMITIÉNDOSE AL CONCLUIR LA MISMA UNA CONSTANCIA DE ACREDITACIÓN POR PARTE DEL
                    “ICATECH”, CUYA CUOTA DE RECUPERACIÓN SERÁ EXONERADO EL 100% DE LA CUOTA
                    TOTAL DEL CURSO.
                @else
                    <b>TERCERA.- “LAS PARTES”</b> ACUERDAN QUE LA CAPACITACIÓN SE IMPARTIRÁ:
                @endif
            </div>
            <br>
            {{-- tabla --}}
            @if (!$id_cerss)
                <table class="tablas" border="1">
                    <thead>
                        <tr>
                            <th style="font-size:12px;"><b>NOMBRE DEL CURSO</b></th>
                            <th style="font-size:12px;"><b>NÚMERO Y FECHA DE CONVENIO</b></th>
                            <th style="font-size:12px;"><b>COSTO</b></th>
                            <th style="font-size:12px;"><b>HORAS</b></th>
                            <th style="font-size:12px;"><b>HORARIO</b></th>
                            <th style="font-size:12px;"><b>FECHA INICIO/TERMINO DEL CURSO</b></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="font-size:12px;">{{strtoupper($data1->curso)}}</td>
                        <td style="font-size:12px;">{{$data1->cespecifico}} A {{$data1->dia}} DE {{strtoupper($data1->mes)}} DEL {{$data1->anio}}</td>
                        <td style="font-size:12px;">${{$data1->costo}}</td>
                        <td style="font-size:12px;">{{$data1->dura}}</td>
                        <td style="font-size:12px;">{{strtoupper($data1->hini)}} A {{strtoupper($data1->hfin)}} HRS {{$data1->observaciones != 'NINGUNO' ? '/ '.$data1->observaciones : ''}}</td>
                        <td style="font-size:12px;">DEL {{$data1->diaini}} {{$data1->mesini != $data1->mesfin ? 'DE '.strtoupper($data1->mesini) : ''}}
                            {{$data1->anioini != $data1->aniofin ? 'DEL '.$data1->anioIni : ''}} AL {{$data1->diafin}} DE {{strtoupper($data1->mesfin)}} DEL AÑO {{$data1->aniofin}}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <div align="justify" style="font-size:12px;">
                    <b>EN LA MODALIDAD DE CURSO <span class="color_dina">{{$data1->tcapacitacion}}</span>,</b> EMITIÉNDOSE AL CONCLUIR LA MISMA, UNA
                    CONSTANCIA DE ACREDITACIÓN POR PARTE DE <b>“EL ICATECH”</b>.
                </div>
                <br>
                <br>
            @endif
            <br>
            <div align="justify" style="font-size:12px;">
                <b>CUARTA.</b> - <b><span class="color_dina">“{{$data3->siglas_inst}}”</span></b> ACEPTA LOS PLANES Y PROGRAMAS QUE <b>“EL ICATECH”</b> DESARROLLÓ PARA
                DAR CUMPLIMIENTO A LO ESTABLECIDO EN EL OBJETO DE ESTE CONVENIO; CUMPLIR CON LAS
                NORMAS ESTABLECIDAS POR ÉSTE EN MATERIA DE SEGURIDAD E HIGIENE DENTRO DE LA UNIDAD
                DE CAPACITACIÓN, ASÍ COMO INTEGRAR GRUPOS MÍNIMOS DE 17 PERSONAS PARA RECIBIR
                CAPACITACIÓN DE CALIDAD.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    QUINTA.– “ICATECH” DESIGNA AL C. SANCHEZ PEREZ ELBA COMO INSTRUCTOR EXTERNO
                    PARA IMPARTIR EL CURSO MATERIA DE ESTE CONVENIO, QUIEN CUENTA CON NÚMERO DE
                    VALIDACIÓN MEMORÁNDUM NO. ICATECH/600/0814/2023 Y EXHIBE DOCUMENTACIÓN
                    CORRESPONDIENTE QUE LO ACRÉDITA Y FACULTA PARA ELLO.
                @else
                    <b>QUINTA. - “EL ICATECH”</b> SERÁ RESPONSABLE DEL REGISTRO DE ALUMNOS, ASÍ COMO DE LA
                    EMISIÓN DEL RECONOCIMIENTO CORRESPONDIENTE, SIEMPRE Y CUANDO ESTOS CUMPLAN LAS
                    HORAS DEL CURSO Y EFECTÚEN EL PAGO DE LA CUOTA DE RECUPERACIÓN REFERIDA EN LA
                    CLÁUSULA TERCERA.
                @endif
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    SEXTA.– LAS “PARTES” CONVIENEN QUE “ICATECH” ABSORBERÁ LOS HONORARIOS DEL
                    INSTRUCTOR Y GASTOS DE ADMINISTRACIÓN CORRESPONDIENTES.
                @else
                    <b>SEXTA-</b> PARA QUE LOS ALUMNOS PROPUESTOS POR <b><span class="color_dina">“{{$data3->siglas_inst}}”</span></b> ESTÉN EN CONDICIONES DE RECIBIR
                    EL RECONOCIMIENTO OFICIAL CONSISTENTE EN LAS CONSTANCIAS POR PARTE DE <b>“EL
                    ICATECH”</b>, DEBERÁN ACUDIR PUNTUAL Y REGULARMENTE A LOS CURSOS DE CAPACITACIÓN, PARA
                    TAL EFECTO, <b>“EL ICATECH”</b> LLEVARÁ UN REGISTRO DE ASISTENCIA DE LOS MISMOS; QUE SERÁ
                    REALIZADO POR EL INSTRUCTOR QUE IMPARTIRÁ EL CURSO.
                @endif
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    SÉPTIMA.– “ICATECH” SERÁ RESPONSABLE DEL REGISTRO DE ALUMNOS, ASÍ COMO DE LA
                    EMISIÓN DEL RECONOCIMIENTO CORRESPONDIENTE, SIEMPRE Y CUANDO ESTOS
                    COMPLEMENTEN LAS 20 HORAS DEL CURSO
                @else
                    <b>SÉPTIMA.- “LAS PARTES”</b> CONVIENEN QUE EL PERSONAL DESIGNADO, CONTRATADO O
                    COMISIONADO PARA LA REALIZACIÓN DE LOS OBJETIVOS DEL PRESENTE CONVENIO, ESTARÁ BAJO
                    LA SUPERVISIÓN DIRECTA DE LA PARTE QUE LO DESIGNE, CONTRATE O COMISIONE Y POR LO
                    TANTO, EN NINGÚN MOMENTO SE CONSIDERARÁ A LA OTRA PARTE COMO EMPLEADOR SUSTITUTO,
                    POR LO QUE LA MISMA NO TENDRÁ RELACIÓN ALGUNA DE CARÁCTER LABORAL CON DICHO
                    PERSONAL Y CONSECUENTEMENTE QUEDA LIBERADA DE CUALQUIER RESPONSABILIDAD QUE
                    PUDIERA PRESENTARSE EN MATERIA DE TRABAJO Y SEGURIDAD SOCIAL.
                @endif
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    OCTAVA.- PARA QUE LOS ALUMNOS PROPUESTOS POR LA “SECRETARIA” SE ENCUENTREN
                    EN CONDICIONES DE RECIBIR EL RECONOCIMIENTO OFICIAL CONSISTENTE EN LAS
                    CONSTANCIAS POR PARTE DEL “ICATECH”, DEBERÁN ACUDIR PUNTUAL Y REGULARMENTE A
                    LOS CURSOS DE CAPACITACIÓN, PARA TAL EFECTO, “ICATECH” LLEVARÁ UN REGISTRO DE
                    ASISTENCIA DE LOS MISMOS.
                @else
                    <b>OCTAVA. -</b> EL PRESENTE ACTO JURÍDICO DEJARÁ DE SURTIR EFECTOS LEGALES CUANDO ALGUNA
                    DE <b>“LAS PARTES”</b> INCURRA EN INCUMPLIMIENTO DE CUALQUIERA DE LAS OBLIGACIONES QUE EN
                    ESTE INSTRUMENTO JURÍDICO CONTRAEN; LO QUE DETERMINEN <b>“LAS PARTES”</b> POR MUTUO
                    CONSENTIMIENTO O CUANDO ALGUNA DE ELLAS COMUNIQUE A LA OTRA POR ESCRITO SU DESEO
                    DE DARLO POR CONCLUIDO, LO QUE DEBERÁ COMUNICARSE POR ESCRITO Y SURTIRÁ SUS
                    EFECTOS LEGALES TRES DÍAS DESPUÉS DE RECIBIDA LA NOTIFICACIÓN, SIN PERJUICIO DEL
                    CUMPLIMIENTO DE LAS ACCIONES QUE SE ESTÉN OPERANDO.
                @endif
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    NOVENA.- LAS “PARTES” CONVIENEN QUE EL PERSONAL DESIGNADO, CONTRATADO O
                    COMISIONADO PARA LA REALIZACIÓN DE LOS OBJETIVOS DEL PRESENTE CONVENIO, ESTARÁ
                    BAJO LA SUPERVISIÓN DIRECTA DE LA PARTE QUE LO DESIGNE, CONTRATE Y COMISIONE
                    POR LO TANTO, EN NINGÚN MOMENTO SE CONSIDERARÁ A LA OTRA PARTE COMO
                    EMPLEADOR SUSTITUTO, POR LO QUE LA MISMA NO TENDRÁ RELACIÓN ALGUNA DE
                    CARÁCTER LABORAL CON DICHO PERSONAL Y CONSECUENTEMENTE QUEDA LIBERADA DE
                    CUALQUIER RESPONSABILIDAD QUE PUDIERA PRESENTARSE EN MATERIA DE TRABAJO Y
                    SEGURIDAD SOCIAL.
                @else
                    <b>NOVENA. -</b> NINGUNA DE <b>“LAS PARTES”</b> SERÁ RESPONSABLE DE CUALQUIER RETRASO O
                    INCUMPLIMIENTO EN LA REALIZACIÓN DEL PRESENTE CONVENIO, SEA RESULTADO DIRECTA O
                    INDIRECTAMENTE DE ALGÚN CASO FORTUITO O DE FUERZA MAYOR.
                @endif
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    DÉCIMA.- EL PRESENTE ACTO JURÍDICO DEJARÁ DE SURTIR EFECTOS LEGALES CUANDO
                    ALGUNA DE LAS “PARTES” INCURRA EN INCUMPLIMIENTO DE CUALQUIERA DE LAS
                    OBLIGACIONES QUE EN ESTE INSTRUMENTO JURÍDICO CONTRAEN; LO QUE DETERMINEN LAS
                    “PARTES” POR MUTUO CONSENTIMIENTO O CUANDO ALGUNA DE ELLAS COMUNIQUE A LA
                    OTRA POR ESCRITO SU DESEO DE DARLO POR CONCLUIDO, LO QUE DEBERÁ COMUNICARSE
                    POR ESCRITO Y SURTIRÁ SUS EFECTOS LEGALES TRES DÍAS DESPUÉS DE RECIBIDA LA
                    NOTIFICACIÓN, SIN PERJUICIO DEL CUMPLIMIENTO DE LAS ACCIONES QUE SE ESTÉN
                    OPERANDO.
                @else
                    <b>DÉCIMA. - “LAS PARTES”</b> MANIFIESTAN QUE EL PRESENTE ACTO JURÍDICO ES PRODUCTO DE LA
                    BUENA FE, EN RAZÓN DE LO CUAL, LOS CONFLICTOS QUE LLEGARÁN A PRESENTARSE EN CUANTO
                    A SU INTERPRETACIÓN, FORMALIZACIÓN Y CUMPLIMIENTO, SERÁN RESUELTOS DE COMÚN
                    ACUERDO POR <b>“LAS PARTES”</b>.
                @endif
            </div>
            <br>
            @if ($id_cerss)
                <div align="justify" style="font-size:12px;">
                    DÉCIMA PRIMERA.- NINGUNA DE LAS “PARTES” SERÁ RESPONSABLE DE CUALQUIER
                    RETRASO O INCUMPLIMIENTO EN LA REALIZACIÓN DEL PRESENTE CONVENIO, SEA
                    RESULTADO DIRECTA O INDIRECTAMENTE DE ALGÚN CASO FORTUITO O DE FUERZA MAYOR.
                </div>
                <div align="justify" style="font-size:12px;">
                    DÉCIMA SEGUNDA.– LAS “PARTES” MANIFIESTAN QUE EL PRESENTE ACTO JURÍDICO ES
                    PRODUCTO DE LA BUENA FE, EN RAZÓN DE LO CUAL, LOS CONFLICTOS QUE LLEGARÁN A
                    PRESENTARSE EN CUANTO A SU INTERPRETACIÓN, FORMALIZACIÓN Y CUMPLIMIENTO,
                    SERÁN RESUELTOS DE COMÚN ACUERDO POR LAS “PARTES”.
                </div>
                <br>
            @endif
            <div align="justify" style="font-size:12px;">
                NO OBSTANTE, LO ANTERIOR, EN CASO DE NO LLEGAR A ALGÚN ACUERDO, <b>“LAS PARTES”</b> SE
                SOMETEN EXPRESAMENTE A LA JURISDICCIÓN DE LOS TRIBUNALES COMPETENTES DE <b>LA CIUDAD
                DE <span class="color_dina">{{$data1->muni}}</span></b>, CHIAPAS; RENUNCIANDO A AQUELLA QUE PUDIERA
                CORRESPONDERLES EN RAZÓN DE SUS DOMICILIOS PRESENTES <span style="margin-right: 4px;">O</span> FUTUROS.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                @if ($id_cerss)
                    ENTERADAS LAS “PARTES” DEL CONTENIDO Y ALCANCE LEGAL DEL PRESENTE CONVENIO
                    ESPECIFICO, LO FIRMAN DE CONFORMIDAD Y POR DUPLICADO ANTE LA PRESENCIA DE LOS
                    TESTIGOS QUE AL FINAL LO SUSCRIBEN, EN LA CIUDAD DE VILLA FLORES, CHIAPAS, EL DÍA 10
                    DE JULIO DEL AÑO DOS MIL VEINTITRÉS.
                @else
                    ENTERADAS <b>“LAS PARTES”</b> DEL CONTENIDO Y ALCANCE LEGAL DEL PRESENTE CONVENIO, LO
                    FIRMAN DE CONFORMIDAD Y POR DUPLICADO ANTE LA PRESENCIA DE LOS TESTIGOS QUE AL FINAL
                    Y AL CALCE LO SUSCRIBEN, EN LA CIUDAD DE <span class="color_dina">{{$data1->muni}}</span>, CHIAPAS, EL DÍA <span class="color_dina">{{$data1->dia}} DEL
                    MES DE {{strtoupper($data1->mes)}} DEL AÑO {{$data1->anio}}</span>.
                @endif
            </div>
            <br><br><br>
            {{-- tabla para agregar las firmas --}}
            @if ($id_cerss)
                <table align="center">
                    <thead>
                        <tr>
                            <th colspan="4" style="border: hidden; font-size:12px;" align="center">
                                POR <span class="color_dina negrita">"{{$data3->siglas_inst}}"</span>
                                    <br>
                                    <br>
                                    <br>_______________________________________
                                    <br><span class="color_dina negrita">
                                        LIC. JOSÉ MIGUEL ALARCÓN GARCÍA.
                                        COMISARIO JEFE
                                        TITULAR DE LA SUBSECRETARIA DE
                                        EJECUCIÓN PENAL DE SANCIONES
                                        PENALES Y MEDIDAS DE SEGURIDAD.</span>
                                    <br>
                                    {{-- <br><span class="color_dina" style="font-weight: normal;">{{$data1->depen}} {{$data3->cargo_fun}}</span> --}}
                            </th>
                            <th colspan="4" style="border: hidden; font-size:12px;" align="center">
                                POR "EL ICATECH"
                                    <br>
                                    <br>
                                    <br>________________________________________
                                    <br><span class="color_dina negrita">{{$data2->dunidad}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$data2->pdunidad}} <br> {{$data1->unidad}}</span>
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
                            <th colspan="4" style="border: hidden; font-size:12px;" align="center">
                                POR <span class="color_dina negrita">"{{$data3->siglas_inst}}"</span>
                                    <br>
                                    <br>
                                    <br>_______________________________________
                                    <br><span class="color_dina negrita">{{$data3->nombre_titular}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$data1->depen}} {{$data3->cargo_fun}}</span>
                            </th>
                            <th colspan="4" style="border: hidden; font-size:12px;" align="center">
                                POR "EL ICATECH"
                                    <br>
                                    <br>
                                    <br>________________________________________
                                    <br><span class="color_dina negrita">{{$data2->dunidad}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$data2->pdunidad}} <br> {{$data1->unidad}}</span>
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
            <br><br><br>
            {{-- tabla para agregar las firmas --}}
            @if ($id_cerss)
                <table align="center">
                    <thead>
                        <tr>
                            <th colspan="4" style="border: hidden; font-size:12px;" align="center">

                                    <br>
                                    <br>_______________________________________
                                    <br><span class="color_dina negrita">
                                        LIC. JUSTINA RAFAELA ROMERO ROSALES
                                        INSPECTOR JEFE
                                        ENCARGADA DE LA DIRECCION DEL
                                        CENTRO ESTATAL DE REINSERCIÓN SOCIAL
                                        DE SENTENCIADOS NO. 8.
                                        </span>
                                    <br>
                                    {{-- <br><span class="color_dina" style="font-weight: normal;">{{$data2->pvinculacion}}</span><span class="color_dina" style="font-weight: normal;">DE LA UNIDAD DE CAPACTITACIÓN {{$data1->unidad}}</span> --}}
                            </th>
                            <th colspan="4" style="border: hidden; font-size:12px;" align="center">

                                    <br>
                                    <br>________________________________________
                                    <br><span class="color_dina negrita">{{$data2->academico}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$data2->pacademico}}</span> <span class="color_dina" style="font-weight: normal;">DE LA UNIDAD DE CAPACTITACIÓN {{$data1->unidad}}</span>
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
                            <th colspan="4" style="border: hidden; font-size:12px;" align="center">

                                    <br>
                                    <br>_______________________________________
                                    <br><span class="color_dina negrita">{{$data2->vinculacion}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$data2->pvinculacion}}</span><span class="color_dina" style="font-weight: normal;">DE LA UNIDAD DE CAPACTITACIÓN {{$data1->unidad}}</span>
                            </th>
                            <th colspan="4" style="border: hidden; font-size:12px;" align="center">

                                    <br>
                                    <br>________________________________________
                                    <br><span class="color_dina negrita">{{$data2->academico}}</span>
                                    <br>
                                    <br><span class="color_dina" style="font-weight: normal;">{{$data2->pacademico}}</span> <span class="color_dina" style="font-weight: normal;">DE LA UNIDAD DE CAPACTITACIÓN {{$data1->unidad}}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody >

                    </tbody>
                </table>
            @endif


            <br><br>
            <div align="justify" style="font-size:10px;">
                @if ($id_cerss)
                    LAS FIRMAS QUE ANTECEDEN, FORMAN PARTE DEL CONVENIO ESPECÍFICO DE PRESTACIÓN DE SERVICIOS EN MATERIA DE CAPACITACIÓN, DEL
                    CURSO “MANUALIDADES A BASE DE PAPEL, PLÁSTICO Y NYLON RECICLADO” QUE CELEBRAN POR UNA PARTE EL INSTITUTO DE CAPACITACIÓN Y
                    VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS Y POR LA OTRA PARTE LA SECRETARÍA DE SEGURIDAD Y PROTECCIÓN CIUDADANA,
                    ATRAVES DE LA SUBSECRETARÍA DE EJECUCIÓN DE SANCIONES PENALES Y MEDIDAS DE SEGURIDAD, EN BENEFICIO DEL CENTRO ESTATAL DE
                    REINSERCION SOCIAL DE SENTENCIADOS NO. 08 EL FLAMBOYAN, EL DÍA 10 DE JULIO DEL AÑO DOS MIL VEINTITRÉS , EN LA CIUDAD DE VILLA
                    FLORES, CHIAPAS.
                @else
                    LAS FIRMAS QUE ANTECEDEN, FORMAN PARTE DEL CONVENIO ESPECÍFICO DE PRESTACIÓN DE
                    SERVICIOS EN MATERIA DE CAPACITACIÓN, DE LOS CURSOS QUE CELEBRAN POR UNA PARTE EL
                    INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS Y POR LA
                    OTRA PARTE EL(LA) <span class="color_dina">{{$data1->depen}}</span>.
                @endif
            </div>


        </div>
    </div>
</body>
</html>
