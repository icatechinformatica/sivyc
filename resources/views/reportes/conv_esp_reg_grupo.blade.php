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
    </style>
</head>
<body>


    <header>
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
            <img class="derecha" src="{{ public_path('img/logoSecretarisObrasPublicas.png') }}">
    </header>
    <div class="contenedor">
        <h5 align=center>CONVENIO ESPECIFICO</h5>
        {{-- crear un div para encerrar todo lo que lleva al inicio --}}
        <div>
            <div align=right style="font-size:12px;"><b>NO. {{$data1->cespecifico}}</b></div>
        </div>
             <br>

        <div class="table-responsive-sm">
            <div align="justify" style="font-size:12px;">
                CONVENIO ESPECÍFICO DE PRESTACIÓN DE SERVICIOS EN MATERIA DE CAPACITACIÓN, DE
                EL(LOS) CURSO(S) DENOMINADO(S), “{{$data1->curso}}”, QUE CELEBRAN POR UNA PARTE EL
                INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, A QUIEN EN
                LO SUCESIVO SE LE DENOMINARÁ “EL ICATECH”, REPRESENTADO EN ESTE ACTO POR EL (LA)
                {{$data2->dunidad}}, EN SU CARÁCTER DE {{$data2->pdunidad}} {{$data1->unidad}} Y POR LA OTRA PARTE EL (LA) {{$data1->depen}}.,
                A QUIEN EN LO SUCESIVO SE LE
                DENOMINARÁ “LA SOP”, REPRESENTADO EN ESTE ACTO POR EL (LA) LIC. ANGEL CARLOS TORRES
                CULEBRO; EN SU CARÁCTER DE SECRETARIO DE OBRAS PÚBLICAS., MISMOS QUE CUANDO
                ACTÚEN DE MANERA CONJUNTA SERÁN DENOMINADOS COMO “LAS PARTES” SUJETÁNDOSE AL
                TENOR DE LOS ANTECEDENTES, DECLARACIONES Y CLÁUSULAS SIGUIENTES:
            </div>
            <br><br>
            <div align=center style="font-size:12px;"><b>ANTECEDENTES</b></div>
            <br><br>
            <div align="justify" style="font-size:12px;">
                ÚNICO. - CON FECHA {{$data1->dia}} DE {{strtoupper($data1->mes)}} DEL {{$data1->anio}} “EL ICATECH” Y “LA SOP”, SUSCRIBIERON
                CONVENIO DE COLABORACIÓN EN EL QUE SE ESTABLECIERON LAS BASES TENDIENTES AL MEJOR
                APROVECHAMIENTO DE SUS RECURSOS, CON EL FIN DE ALCANZAR SUS OBJETIVOS Y LOGRAR ASI
                UN MAYOR IMPACTO A FAVOR DE LA SOCIEDAD CHIAPANECA, A TRAVÉS DE LA FORMALIZACIÓN DE
                CONVENIOS ESPECÍFICOS EN LOS QUE SE ESTABLECIERAN LAS PARTICULARIDADES DE LAS
                ACTIVIDADES A REALIZAR Y LOS TIEMPOS ESTABLECIDOS PARA ELLO.
            </div>

            <br><br>
            <div align=center style="font-size:12px;"><b>DECLARACIONES</b></div>
            <br><br>

            <div align="left" style="font-size:12px;">
                <b>I.   DECLARA "EL ICATECH" QUE:</b>
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
                I.2     CON FUNDAMENTO EN LO DISPUESTO POR EL ARTÍCULO 13 FRACCIÓN IV DEL REGLAMENTO
                INTERIOR DEL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE
                CHIAPAS, PUBLICADO EN EL PERIÓDICO OFICIAL NÚMERO 404, DE FECHA 31 DE OCTUBRE DEL
                2018 EL (LA) {{$data2->dgeneral}} EN SU CARÁCTER DE DIRECTOR(A)
                GENERAL, TIENE DENTRO DE SUS ATRIBUCIONES DELEGABLES LA DE CELEBRAR Y SUSCRIBIR
                CONVENIOS, ACUERDOS, CONTRATOS Y DEMÁS ACTOS DE CARÁCTER ADMINISTRATIVO,
                RELACIONADOS CON LOS ASUNTOS DE COMPETENCIA DE “EL ICATECH”.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                I.3     EN TÉRMINOS DE LO CITADO EN LA DECLARACIÓN ANTERIOR Y CON FUNDAMENTO EN LO
                DISPUESTO POR LOS ARTÍCULOS 16 FRACCIÓN XXIV Y 29 FRACCIONES I Y II DEL REGLAMENTO
                INTERIOR DEL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE
                CHIAPAS, A EL (LA) {{$data2->dunidad}}, SE ENCUENTRA
                FACULTADO(A) PARA REPRESENTAR EN ESTE ACTO A “EL ICATECH”, EN SU CARÁCTER DE
                DIRECTOR(A) DE LA UNIDAD DE CAPACITACIÓN {{$data1->unidad}}, TAL Y COMO LO ACREDITA CON EL
                NOMBRAMIENTO, EXPEDIDO A SU FAVOR POR EL (LA) {{$data2->dgeneral}}, EN SU CARÁCTER DE DIRECTOR(A) GENERAL, POR LO TANTO, CUENTA CON PLENA
                FACULTAD LEGAL PARA SUSCRIBIR EL PRESENTE CONVENIO ESPECÍFICO DE COLABORACIÓN.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
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
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                I.5     PARA EFECTOS DEL PRESENTE CONVENIO, SEÑALA COMO SU DOMICILIO LEGAL, EL UBICADO
                {{$data2->direccion}}.
            </div>
            <br>
            <div align="left" style="font-size:12px;">
                <b>II.   DECLARA "LA SOP" QUE:</b>
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                II.1    EL (LA) {{$data3->nombre_titular}}, EN SU CARÁCTER DE SECRETARIO DE OBRAS
                PÚBLICAS DEL PODER EJECUTIVO DEL ESTADO DE CHIAPAS, TIENE PLENA CAPACIDAD
                JURÍDICA Y VOLUNTAD PARA CELEBRAR Y SUSCRIBIR EL PRESENTE CONVENIO ESPECÍFICO DE
                PRESTACIÓN DE SERVICIOS EN MATERIA DE CAPACITACIÓN, OBLIGÁNDOSE EN TODOS SUS
                TÉRMINOS; DE CONFORMIDAD CON LO DISPUESTO EN LOS ARTÍCULOS 18, SEGUNDO PÁRRAFO,
                DE LA LEY ORGÁNICA DE LA ADMINISTRACIÓN PÚBLICA DEL ESTADO DE CHIAPAS Y 14,
                FRACCIÓN VII DEL REGLAMENTO INTERIOR VIGENTE DE ESTA DEPENDENCIA DEL EJECUTIVO
                ESTATAL.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                II.2    SEÑALA COMO SU DOMICILIO PARA EFECTOS DEL PRESENTE CONVENIO, {{$data3->direccion}}
            </div>
            <br><br>

            <div align="left" style="font-size:12px;">
                <b>III.   DECLARAN "LAS PARTES" QUE:</b>
            </div>
            <div align="justify" style="font-size:12px;">
                III.3   RECONOCEN LA PERSONALIDAD CON QUE SE OSTENTAN EN LA CELEBRACIÓN DEL PRESENTE
                INSTRUMENTO, POR LO QUE MANIFIESTAN SU INTERÉS EN CELEBRARLO PARA CONTRIBUIR A
                LA REALIZACIÓN DE SUS OBJETIVOS COMUNES MEDIANTE EL INTERCAMBIO DE APOYO
                ACADÉMICO, LOGÍSTICO Y OPERATIVO, SUJETÁNDOSE PARA TAL EFECTO A LAS SIGUIENTES:
            </div>
            <br>
            <div align="left" style="font-size:12px;">
                <b>III.   DECLARAN "LAS PARTES" QUE:</b>
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                III.1   RECONOCEN LA PERSONALIDAD CON QUE SE OSTENTAN EN LA CELEBRACIÓN DEL PRESENTE
                INSTRUMENTO, POR LO QUE MANIFIESTAN SU INTERÉS EN CELEBRARLO PARA CONTRIBUIR A
                LA REALIZACIÓN DE SUS OBJETIVOS COMUNES MEDIANTE EL INTERCAMBIO DE APOYO
                ACADÉMICO, LOGÍSTICO Y OPERATIVO, SUJETÁNDOSE PARA TAL EFECTO A LAS SIGUIENTES:
            </div>
            <br>
            <div align="center" style="font-size:12px;">
                <b>CLÁUSULAS</b>
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>PRIMERA. - DEL OBJETO.</b> EL PRESENTE CONVENIO TIENE POR OBJETO ESTABLECER LAS BASES
                CONFORME A LAS CUALES, <b>“LAS PARTES”</b> DESARROLLARÁN ACTIVIDADES EN MATERIA DE
                CAPACITACIÓN PARA EL TRABAJO.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>SEGUNDA. -</b> PARA DAR CUMPLIMIENTO AL OBJETO DEL PRESENTE CONVENIO, <b>“LAS PARTES”</b>
                CONVIENEN QUE <b>“EL ICATECH”</b> OFRECERÁ A <b>“LA SOP”</b> LOS SERVICIOS DE CAPACITACIÓN
                CONSISTENTES EN LOS CURSOS DE: <b>“{{$data1->curso}}”</b>, A TRAVÉS DEL DEPARTAMENTO DE
                VINCULACIÓN DE LA UNIDAD DE CAPACITACIÓN {{$data1->unidad}}.
            </div>
            <br>
            <div align="left" style="font-size:12px;">
                <b>TERCERA. - “LAS PARTES”</b> ACUERDAN QUE LAS CAPACITACIONES SE IMPARTIRÁN:
            </div>
            <br>
            {{-- tabla --}}
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
                    <td style="font-size:12px;">PRIMEROS AUXILIOS</td>
                    <td style="font-size:12px;">G054T32022/ 05 DE SEPTIEMBRE 2022</td>
                    <td style="font-size:12px;">$0.00</td>
                    <td style="font-size:12px;">20</td>
                    <td style="font-size:12px;">09:00 A 16:00 HRS</td>
                    <td style="font-size:12px;">20/02/2023 AL 22/02/2023</td>
                </tr>
                </tbody>
            </table>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>EN LA MODALIDAD DE CURSO {{$data1->tcapacitacion}},</b> EMITIÉNDOSE AL CONCLUIR LA MISMA, UNA
                CONSTANCIA DE ACREDITACIÓN POR PARTE DE <b>“EL ICATECH”</b>.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>CUARTA.</b> - <b>“LA SOP”</b> ACEPTA LOS PLANES Y PROGRAMAS QUE <b>“EL ICATECH”</b> DESARROLLÓ PARA
                DAR CUMPLIMIENTO A LO ESTABLECIDO EN EL OBJETO DE ESTE CONVENIO; CUMPLIR CON LAS
                NORMAS ESTABLECIDAS POR ÉSTE EN MATERIA DE SEGURIDAD E HIGIENE DENTRO DE LA UNIDAD
                DE CAPACITACIÓN, ASÍ COMO INTEGRAR GRUPOS MÍNIMOS DE 15 PERSONAS PARA RECIBIR
                CAPACITACIÓN DE CALIDAD.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>QUINTA. - “EL ICATECH”</b> SERÁ RESPONSABLE DEL REGISTRO DE ALUMNOS, ASÍ COMO DE LA
                EMISIÓN DEL RECONOCIMIENTO CORRESPONDIENTE, SIEMPRE Y CUANDO ESTOS CUMPLAN LAS
                HORAS DEL CURSO Y EFECTÚEN EL PAGO DE LA CUOTA DE RECUPERACIÓN REFERIDA EN LA
                CLÁUSULA TERCERA.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>SEXTA-</b> PARA QUE LOS ALUMNOS PROPUESTOS POR <b>“LA SOP”</b> ESTÉN EN CONDICIONES DE RECIBIR
                EL RECONOCIMIENTO OFICIAL CONSISTENTE EN LAS CONSTANCIAS POR PARTE DE <b>“EL
                ICATECH”</b>, DEBERÁN ACUDIR PUNTUAL Y REGULARMENTE A LOS CURSOS DE CAPACITACIÓN, PARA
                TAL EFECTO, <b>“EL ICATECH”</b> LLEVARÁ UN REGISTRO DE ASISTENCIA DE LOS MISMOS; QUE SERÁ
                REALIZADO POR EL INSTRUCTOR QUE IMPARTIRÁ EL CURSO.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>SÉPTIMA.- “LAS PARTES”</b> CONVIENEN QUE EL PERSONAL DESIGNADO, CONTRATADO O
                COMISIONADO PARA LA REALIZACIÓN DE LOS OBJETIVOS DEL PRESENTE CONVENIO, ESTARÁ BAJO
                LA SUPERVISIÓN DIRECTA DE LA PARTE QUE LO DESIGNE, CONTRATE O COMISIONE Y POR LO
                TANTO, EN NINGÚN MOMENTO SE CONSIDERARÁ A LA OTRA PARTE COMO EMPLEADOR SUSTITUTO,
                POR LO QUE LA MISMA NO TENDRÁ RELACIÓN ALGUNA DE CARÁCTER LABORAL CON DICHO
                PERSONAL Y CONSECUENTEMENTE QUEDA LIBERADA DE CUALQUIER RESPONSABILIDAD QUE
                PUDIERA PRESENTARSE EN MATERIA DE TRABAJO Y SEGURIDAD SOCIAL.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>OCTAVA. -</b> EL PRESENTE ACTO JURÍDICO DEJARÁ DE SURTIR EFECTOS LEGALES CUANDO ALGUNA
                DE <b>“LAS PARTES”</b> INCURRA EN INCUMPLIMIENTO DE CUALQUIERA DE LAS OBLIGACIONES QUE EN
                ESTE INSTRUMENTO JURÍDICO CONTRAEN; LO QUE DETERMINEN <b>“LAS PARTES”</b> POR MUTUO
                CONSENTIMIENTO O CUANDO ALGUNA DE ELLAS COMUNIQUE A LA OTRA POR ESCRITO SU DESEO
                DE DARLO POR CONCLUIDO, LO QUE DEBERÁ COMUNICARSE POR ESCRITO Y SURTIRÁ SUS
                EFECTOS LEGALES TRES DÍAS DESPUÉS DE RECIBIDA LA NOTIFICACIÓN, SIN PERJUICIO DEL
                CUMPLIMIENTO DE LAS ACCIONES QUE SE ESTÉN OPERANDO.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>NOVENA. -</b> NINGUNA DE <b>“LAS PARTES”</b> SERÁ RESPONSABLE DE CUALQUIER RETRASO O
                INCUMPLIMIENTO EN LA REALIZACIÓN DEL PRESENTE CONVENIO, SEA RESULTADO DIRECTA O
                INDIRECTAMENTE DE ALGÚN CASO FORTUITO O DE FUERZA MAYOR.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                <b>DÉCIMA. - “LAS PARTES”</b> MANIFIESTAN QUE EL PRESENTE ACTO JURÍDICO ES PRODUCTO DE LA
                BUENA FE, EN RAZÓN DE LO CUAL, LOS CONFLICTOS QUE LLEGARÁN A PRESENTARSE EN CUANTO
                A SU INTERPRETACIÓN, FORMALIZACIÓN Y CUMPLIMIENTO, SERÁN RESUELTOS DE COMÚN
                ACUERDO POR <b>“LAS PARTES”</b>.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                NO OBSTANTE, LO ANTERIOR, EN CASO DE NO LLEGAR A ALGÚN ACUERDO, <b>“LAS PARTES”</b> SE
                SOMETEN EXPRESAMENTE A LA JURISDICCIÓN DE LOS TRIBUNALES COMPETENTES DE <b>LA CIUDAD
                DE {{$data1->muni}}</b>, CHIAPAS; RENUNCIANDO A AQUELLA QUE PUDIERA
                CORRESPONDERLES EN RAZÓN DE SUS DOMICILIOS PRESENTES O FUTUROS.
            </div>
            <br>
            <div align="justify" style="font-size:12px;">
                ENTERADAS <b>“LAS PARTES”</b> DEL CONTENIDO Y ALCANCE LEGAL DEL PRESENTE CONVENIO, LO
                FIRMAN DE CONFORMIDAD Y POR DUPLICADO ANTE LA PRESENCIA DE LOS TESTIGOS QUE AL FINAL
                Y AL CALCE LO SUSCRIBEN, EN LA CIUDAD DE {{$data1->muni}}, CHIAPAS, EL DÍA {{$data1->dia}} DEL
                MES DE {{$data1->mes}} DEL AÑO {{$data1->anio}}.
            </div>
            <br><br><br>
            {{-- tabla para agregar las firmas --}}
            <table align="center">
                <thead>
                    <tr>
                        <th colspan="4" style="border: hidden; font-size:12px;" align="center">
                            POR "LA SOP"
                                <br>
                                <br>
                                <br>_______________________________________
                                <br> {{$data3->nombre_titular}}
                                <br>
                                <br> SECRETARIO DE OBRAS PUBLICAS
                        </th>
                        <th colspan="4" style="border: hidden; font-size:12px;" align="center">
                            POR "EL ICATECH"
                                <br>
                                <br>
                                <br>________________________________________
                                <br> {{$data2->dunidad}}
                                <br>
                                <br>{{$data2->pdunidad}} {{$data1->unidad}}
                        </th>
                    </tr>
                </thead>
                <tbody >

                </tbody>
            </table>
            <br><br><br>
            <div align="center" style="font-size:12px;">
                <b>TESTIGOS</b>
            </div>
            <br><br><br>
            {{-- tabla para agregar las firmas --}}
            <table align="center">
                <thead>
                    <tr>
                        <th colspan="4" style="border: hidden; font-size:12px;" align="center">

                                <br>
                                <br>_______________________________________
                                <br> {{$data2->vinculacion}}
                                <br>
                                <br> {{$data2->pvinculacion}} DE <br> LA UNIDAD DE CAPACTITACIÓN {{$data1->unidad}}
                        </th>
                        <th colspan="4" style="border: hidden; font-size:12px;" align="center">

                                <br>
                                <br>________________________________________
                                <br> {{$data2->academico}}
                                <br>
                                <br> {{$data2->pacademico}} DE LA <br> LA UNIDAD DE CAPACTITACIÓN {{$data1->unidad}}
                        </th>
                    </tr>
                </thead>
                <tbody >

                </tbody>
            </table>

            <br><br>
            <div align="justify" style="font-size:12px;">
                LAS FIRMAS QUE ANTECEDEN, FORMAN PARTE DEL CONVENIO ESPECÍFICO DE PRESTACIÓN DE
                SERVICIOS EN MATERIA DE CAPACITACIÓN, DE LOS CURSOS QUE CELEBRAN POR UNA PARTE EL
                INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS Y POR LA
                OTRA PARTE EL(LA) {{$data1->depen}}.
            </div>


        </div>
    </div>
</body>
</html>
