@extends('theme.sivyc.layout')
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body{
            font-family: sans-serif;
        }
        @page {
            margin: 60px 60px;
        }
        header { position: fixed;
            left: 0px;
            top: -155px;
            right: 0px;
            height: 50px;
            background-color: #ddd;
            text-align: center;
        }
        header h1{
            margin: 1px 0;
        }
        header h2{
            margin: 0 0 1px 0;
        }
        footer {
            position: fixed;
            left: 0px;
            bottom: -30px;
            right: 0px;
            height: 10px;
            text-align: center;
        }
        footer .page:after {
            content: counter(page);
        }
        footer table {
            width: 100%;
        }
        footer p {
            text-align: right;
        }
        footer .izq {
            text-align: left;
        }
        table, td {
                  border:0px solid black;
                }
        table {
            border-collapse:collapse;
            width:100%;
        }
        td {
            padding:0px;
        }
        .page-number:before {
            content: "Pagina " counter(page);
        }
    </style>
</head>
    <body>
        <footer>
            <div class="page-number"></div>
        </footer>
        <br><br><br><br>
        <div class= "container g-pt-30" style="font-size: 12px;">
            <div id="content">
                <div align=right> <b>Contrato No.<a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$data_contrato->numero_contrato}}.</a></b> </div>
                <br><div align="justify"><b>CONTRATO DE PRESTACIÓN DE SERVICIOS PROFESIONALES POR HONORARIOS EN SU MODALIDAD DE HORAS CURSO, QUE CELEBRAN POR UNA PARTE, EL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, REPRESENTADO POR EL (LA) C. <a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}</a>, EN SU CARÁCTER DE <a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$director->puesto}} DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}}</a> Y POR LA OTRA (EL) (LA) C.<a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal"> {{$nomins}}</a>, EN SU CARÁCTER DE INSTRUCTOR EXTERNO; A QUIENES EN LO SUCESIVO SE LES DENOMINARÁ “EL ICATECH” Y “EL PRESTADOR DE SERVICIOS” RESPECTIVAMENTE; MISMO QUE SE FORMALIZA AL TENOR DE LAS DECLARACIONES Y CLÁUSULAS SIGUIENTES:</b></div>
                <br><div align="center"> DECLARACIONES</div>
                <div align="justify">
                    <dl>
                        <dt>I.Declara <b>“EL ICATECH”</b> que:<br>
                        <br><dd>I.1 Es un Organismo Descentralizado de la Administración Pública Estatal, con personalidad jurídica y patrimonio propios, conforme a lo dispuesto en el artículo 1 del Decreto número 182, publicado en el Periódico Oficial número 032, de fecha 26 de Julio del año 2000 y del Decreto número 183 por el que se reforman, derogan y adicionan diversas disposiciones del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, publicado en el Periódico Oficial número 094, de fecha 21 de mayo del año 2008.</dd>
                        <br><dd>I.2 La Mtra. Fabiola Lizbeth Astudillo Reyes, es Directora General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, personalidad que se acredita con nombramiento expedido a su favor por el Dr. Rutilio Escandón Cadenas, Gobernador del Estado de Chiapas, de fecha 16 de enero de 2019, por lo que se encuentra plenamente facultada en términos de lo dispuesto en los artículos 28 fracción I de la Ley de Entidades Paraestatales del Estado de Chiapas; 15 fracción I del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, así como el 13 fracción IV del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, mismas que no le han sido limitadas o revocadas en forma alguna.</dd>
                        <br><dd>I.3 Con fundamento en el artículo 13 fracción IV del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, la Mtra. Fabiola Lizbeth Astudillo Reyes, en su carácter de Directora General, tiene como atribución delegable la de celebrar y suscribir convenios, acuerdos, contratos y demás actos de carácter administrativo de conformidad con las actividades de <b>“EL ICATECH”</b>. En ese sentido, tuvo a bien designar mediante Circular No. <b>ICATECH/100/001/20</b> de fecha 28 de enero de 2020, y conforme a lo dispuesto por el artículo 29 fracción I, III y XI del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, al (a la) C. <a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}, {{$director->puesto}} DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}}</a>, para suscribir el presente acuerdo de voluntades.</dd>
                        <br><dd>I.4 Tiene como objetivo impartir e impulsar la capacitación para el trabajo en la entidad, procurando la mejor calidad y vinculación de este servicio con el aparato productivo y las necesidades de desarrollo regional, estatal y nacional; promover la impartición de cursos de capacitación a obreros en mano de obra calificada, que corresponden a las necesidades de los mercados laborales del estado; apoyar las acciones de capacitación para el trabajo de los sectores productivos del estado, así como la capacitación tanto para el trabajo de personas sin empleo o discapacitadas, como no egresados de primarias, secundarias o preparatorias y aumentar con los programas de capacitación el nivel de productividad de los trabajadores.</dd>
                        <br><dd>I.5 De acuerdo a las necesidades de <b>“EL ICATECH”</b>, se requiere contar con los servicios de una persona física con conocimientos en <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$especialidad->nombre}}</a>, por lo que se ha determinado llevar a cabo la Contratación por Honorarios en la modalidad de horas curso de <b>"EL PRESTADOR DE SERVICIOS"</b>.</dd>
                        <br><dd>I.6 Para los efectos del presente contrato se cuenta con el Dictamen de autorización de habilidades como instructor según memorándum que se agrega al presente instrumento como <b>Anexo I</b>, emitido por la Dirección Técnica Académica de <b>“EL ICATECH”</b>.</dd>
                        <br><dd>I.7 Para los efectos del presente contrato se cuenta con la clave de grupo que se agrega al presente instrumento como <b>Anexo II</b>, emitido por la Dirección Técnica Académica de <b>“EL ICATECH”</b>.</dd>
                        <br><dd>I.8 Para los efectos del presente contrato se cuenta con la suficiencia presupuestal, conforme al presupuesto de egresos autorizado, que se agrega al presente instrumento como <b>Anexo III</b>, emitido por la Dirección de Planeación de <b>“EL ICATECH”</b>.</dd>
                        <br><dd>I.9 Para los efectos del presente Contrato señala como su domicilio legal, el ubicado en la 14 poniente norte, número 239, Colonia Moctezuma, C. P. 29030, en la Ciudad de Tuxtla Gutiérrez, Chiapas.</dd>
                    </dl>
                    <br><dl><dt>II.Declara <b>"EL PRESTADOR DE SERVICIOS"</b> que:</dt>
                        <br><dd>II.1 Es una persona física, de nacionalidad Mexicana, lo cual acredita mediante credencial de elector número de folio <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$data->folio_ine}}</a>, por lo que cuenta con los conocimientos necesarios para impartir el curso.</dd>
                        <br><dd>II.2 Se encuentra al corriente en el pago de sus impuestos y cuenta con el Registro Federal de Contribuyentes número <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$data->rfc}}</a>, otorgado por el Servicio de Administración Tributaria de la Secretaría de Hacienda y Crédito Público.</dd>
                        <br><dd>II.3 Cuenta con la Clave Única del Registro de Población (CURP) y su número es <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$data->curp}}</a>.</dd>
                        <br><dd>II.4 Goza de plena capacidad jurídica y facultades que le otorga la ley, para contratar y obligarse, así como también con los estudios, conocimientos y la experiencia necesaria en la materia de <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$especialidad->nombre}}</a> y conoce plenamente las necesidades de los servicios objeto del presente contrato, así como que ha considerado todos los factores que intervienen para desarrollar eficazmente las actividades que desempeñará.</dd>
                        <br><dd>II.5 Es conforme de que <b>“EL ICATECH”</b>, le retenga los impuestos a que haya lugar por concepto de la Prestación de Servicios Profesionales por Honorarios.</dd>
                        <br><dd>II.6 Bajo protesta de decir verdad, no se encuentra inhabilitado por autoridad competente alguna, así como a la suscripción del presente documento no ha sido parte en juicios del orden civil, mercantil o laboral en contra de <b>“EL ICATECH”</b> o de alguna otra institución pública o privada; y que no se encuentra en algún otro supuesto o situación que pudiera generar conflicto de intereses para prestar los servicios profesionales objeto del presente contrato.</dd>
                        <br><dd>II.7 Para los efectos del presente contrato, señala como su domicilio legal el ubicado en <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$data->domicilio}}</a>.</dd>
                    </dl>
                </div>
                <div align="justify">Con base en las declaraciones antes expuestas, declaran las partes que es su voluntad celebrar el presente contrato, sujetándose a las siguientes:</div>
                <br>
                <div align="center"><strong> CLAUSULAS </strong></div>
                <br><div align="justify">
                    <dd><b>PRIMERA.- OBJETO DEL CONTRATO</b>. En los términos y condiciones del presente contrato <b>“EL PRESTADOR DE SERVICIOS”</b> se obliga a prestar sus servicios profesionales por <b>Honorarios</b> a <b>“EL ICATECH”</b>, consistente en otorgar el curso que se detalla en el <b>Anexo II</b>, formando parte integrante del presente contrato como si a la letra se insertase, donde se establece bajo que especialidad, periodo de impartición, horario, días, horas que se cubrirán y en el lugar que será impartido el curso.</dd>
                    <br><dd><b>SEGUNDA.- MONTO DE LOS HONORARIOS</b>. El monto total de los honorarios que <b>“EL ICATECH”</b>, pagará a <b>“EL PRESTADOR DE SERVICIOS”</b> será por la cantidad de <b><a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">${{$cantidad}} ({{$data_contrato->cantidad_letras1}} {{$monto['1']}}/100 M.N.)</a></b>, más el 16% (dieciséis por ciento), del Impuesto al Valor Agregado, menos las retenciones que de conformidad con la Ley del Impuesto Sobre la Renta, y demás disposiciones fiscales que procedan para el caso que nos ocupa.</dd>
                    <br><dd><b>“EL PRESTADOR DE SERVICIOS”</b> recibirá el monto resultante señalado en el párrafo primero de esta cláusula, conforme a la disponibilidad financiera de <b>“EL ICATECH”</b>; él pago se realizará en una sola exhibición, por medio de transferencia electrónica interbancaria a la cuenta que señala y se agrega al presente contrato como <b>Anexo IV</b>, formando parte integrante del presente instrumento como si a la letra se insertase, contra la entrega del recibo de honorarios y/o factura correspondiente, (la cual se agrega al presente instrumento como <b>Anexo V</b>), mismo que deberá cubrir los requisitos fiscales estipulados por la Secretaría de Hacienda y Crédito Público; por lo que <b>“EL PRESTADOR DE SERVICIOS”</b> no podrá exigir retribución alguna por ningún otro concepto.</dd>
                    <br><dd><b>TERCERA.- DE LA OBLIGACIÓN DE “EL PRESTADOR DE SERVICIOS”</b>. <b>“EL PRESTADOR DE SERVICIOS”</b> se obliga a desempeñar las obligaciones que contrae en este acto y con todo el sentido ético y profesional que requiere <b>“EL ICATECH”</b> y de acuerdo con las políticas y reglamentos del mismo. <b>“EL PRESTADOR DE SERVICIOS”</b> declara conocer y se obliga a:</dd>
                    <Ol type = "I">
                        <li> Diseñar, preparar y dictar los cursos a su cargo con toda la diligencia y esmero que exige la calidad de <b>“EL ICATECH”</b>.</li>
                        <br><li> Asistir con toda puntualidad a sus cursos y aprovechar íntegramente el tiempo necesario para el mejor desarrollo de los mismos.</li>
                        <br><li> Generar un reporte final del curso impartido o de cualquier incidente o problema que surgió en el desarrollo del mismo.</li>
                        <br><li> Cumplir con los procedimientos del control escolar de alumnos que implemente <b>“EL ICATECH”</b>.</li>
                        <br><li> Respetar las normas de conducta que establece <b>“EL ICATECH”</b>.</li>
                    </Ol>
                </div>
                <div align="justify">
                    <dd><b>CUARTA.- SECRETO PROFESIONAL. “EL PRESTADOR DE SERVICIOS”</b> conviene no divulgar por medio de publicaciones, informes, conferencias o en cualquier otra forma, los datos y resultados obtenidos de los trabajos de este contrato, sin la autorización expresa de <b>“EL ICATECH”</b>, pues dichos datos y resultados son considerados confidenciales. Esta obligación subsistirá, aún después de haber terminado la vigencia de este contrato.</dd>
                    <br><dd><b>QUINTA.- VIGENCIA</b>. La vigencia del presente contrato será conforme a la duración del Curso objeto del presente Instrumento, detallados en la <b>CLÁUSULA PRIMERA</b>; el cual será forzoso para <b>“EL PRESTADOR DE SERVICIOS”</b> y voluntario para <b>“EL ICATECH”</b> mismo que podrá darlo por terminado anticipadamente en cualquier tiempo, siempre y cuando existan motivos o razones de interés general, incumpla cualquiera de las obligaciones adquiridas con la formalización del presente instrumento o incurra en alguna de las causales previstas en la Cláusula Octava, mediante notificación por escrito a <b>“EL PRESTADOR DE SERVICIOS”</b>; en todo caso <b>“EL ICATECH”</b> deberá cubrir los honorarios únicamente en cuanto a los días de servicio prestados.</dd>
                    <br><dd>Concluido el término del presente contrato no podrá haber prórroga automática por el simple transcurso del tiempo y terminará sin necesidad de darse aviso entre las partes. Si terminada la vigencia de este contrato, <b>“EL ICATECH”</b> tuviere necesidad de seguir utilizando los servicios de <b>“EL PRESTADOR DE SERVICIOS”</b>, se requerirá la celebración de un nuevo contrato, sin que éste pueda ser computado con el anterior.</dd>
                    <br><dd><b>SEXTA.- SEGUIMIENTO. “EL ICATECH”</b> a través de los representantes que al efecto designe, tendrá en todo tiempo el derecho de supervisar el estricto cumplimiento de este contrato, por lo que podrá revisar e inspeccionar las actividades que desempeñe <b>“EL PRESTADOR DE SERVICIOS”</b>.</dd>
                    <br><dd><b>SÉPTIMA.- PROPIEDAD DE RESULTADOS Y DERECHOS DE AUTOR</b>. Los documentos, estudios y demás materiales que se generen en la ejecución o como consecuencia de este contrato, pasarán a ser propiedad de <b>“EL ICATECH”</b>, quedando obligado <b>“EL PRESTADOR DE SERVICIOS”</b> a entregarlos al término del presente instrumento.</dd>
                    <br><dd><b>“EL PRESTADOR DE SERVICIOS”</b> se obliga con <b>“EL ICATECH”</b> a responder ilimitadamente de los daños o perjuicios que pudiera causar a <b>“EL ICATECH”</b> o a terceros, si con motivo de la prestación de los servicios contratados viola derechos de autor, de patentes y/o marcas u otro derecho reservado, por lo anterior <b>“EL PRESTADOR DE SERVICIOS”</b> manifiesta en este acto bajo protesta de decir verdad, no encontrarse en ninguno de los supuestos de infracción a la Ley Federal de Derechos de Autor ni a la Ley de Propiedad Industrial.</dd>
                    <br><dd>En caso de que sobreviniera alguna reclamación en contra de <b>“EL ICATECH”</b> por cualquiera de las causas antes mencionadas, la única obligación de éste será dar aviso a <b>“EL PRESTADOR DE SERVICIOS”</b> en el domicilio previsto en este instrumento para que ponga a salvo a <b>“EL ICATECH”</b> de cualquier controversia.</dd>
                    <br><dd><b>OCTAVA.- RESCISIÓN. “EL ICATECH”</b> podrá rescindir el presente contrato sin responsabilidad alguna, sin necesidad de declaración judicial, bastando para ello una notificación por escrito cuando concurran causas de interés general, cuando <b>“EL PRESTADOR DE SERVICIOS”</b> incumpla algunas de las obligaciones del presente contrato y demás disposiciones contenidas en las leyes que le sean aplicables y cuando a juicio de <b>“EL ICATECH”</b> incurra en las siguientes causales:</dd>
                </div>
                <ol type = "I">
                    <li>Negligencia o impericia. </li>
                    <li>Falta de probidad u honradez.</li>
                    <li>Por prestar los servicios de forma ineficiente e inoportuna.</li>
                    <li>Por no apegarse a lo estipulado en el presente contrato.</li>
                    <li>Por no observar la discreción debida respecto a la información a la que tenga acceso como consecuencia de la información de los servicios encomendados.</li>
                    <li>Por suspender injustificadamente la prestación de los servicios o por negarse a corregir lo rechazado por <b>“EL ICATECH”</b>.</li>
                    <li>Por negarse a informar a <b>“EL ICATECH”</b> sobre los resultados de la prestación del servicio encomendado. </li>
                    <li>Por impedir el desempeño normal de las labores durante la prestación de los servicios. </li>
                    <li>Si se comprueba que la protesta a que se refiere la Declaración II.2 de <b>“EL PRESTADOR DE SERVICIOS”</b> se realizó con falsedad.</li>
                    <li>Por no otorgar los cursos en el tiempo establecido (horas del curso). </li>
                </ol>
                <div align="justify"><dd>Asimismo, en caso de tener evidencias de que el curso no fue impartido, se procederá a dar por rescindido el contrato, y se actuará conforme a lo dispuesto por la Ley de Responsabilidad Administrativa para el Estado de Chiapas.</dd>
                    <br><dd><b>“EL PRESTADOR DE SERVICIOS”</b> podrá dar por rescindido de forma anticipada el presente contrato, previo aviso que realice por escrito con un mínimo de 10 días hábiles a <b>“EL ICATECH”</b>.</dd>
                    <br><dd><b>“EL ICATECH”</b> se reservará el derecho de aceptar la terminación anticipada del contrato, sin que ello implique la renuncia a deducir las acciones legales que en su caso procedan.</dd>
                    <br><dd><b>NOVENA.- CESIÓN. “EL PRESTADOR DE SERVICIOS”</b> no podrá en ningún caso ceder total o parcialmente a terceros llámese persona física o persona moral, los derechos y obligaciones derivadas del presente contrato.</dd>
                    <br><dd><b>DÉCIMA.- RELACIONES PROFESIONALES. “EL ICATECH”</b> no adquiere ni reconoce obligación alguna de carácter laboral a favor de <b>“EL PRESTADOR DE SERVICIOS”</b>, en virtud de no ser aplicables a la relación contractual que consta en este instrumento, los artículos 1º y 8º de la Ley Federal del Trabajo y 123 apartado “A” de la Constitución Política de los Estados Unidos Mexicanos, por lo que <b>“EL PRESTADOR DE SERVICIOS”</b>, no será considerado como trabajador para los efectos legales y en particular para obtener las prestaciones establecidas en su artículo 5 A, fracciones V, VI y VII de la Ley del Instituto Mexicano del Seguro Social.</dd>
                    <br><dd><b>“EL PRESTADOR DE SERVICIOS”</b> acepta ser el único responsable del cumplimiento de las obligaciones derivadas de las relaciones laborales, fiscales, contractuales o de cualquier otra índole, incluso las de seguridad social e INFONAVIT que pudieran derivarse de la prestación de los servicios aquí contratados, consecuentemente <b>“EL PRESTADOR DE SERVICIOS”</b> libera de toda responsabilidad a <b>“EL ICATECH”</b> de las obligaciones que pudieran presentarse por éstos conceptos.</dd>
                    <br><dd><b>“EL PRESTADOR DE SERVICIOS”</b> se obliga a prestar sus servicios en el domicilio previamente convenido por las partes.</dd>
                    <br><dd><b>DÉCIMA PRIMERA.- “EL PRESTADOR DE SERVICIOS”</b> reconoce que la relación existente con <b>“EL ICATECH”</b> es de naturaleza civil, por tanto, en ningún momento podrá ser considerado como trabajador de <b>“EL ICATECH”</b> con motivo del cumplimiento de las obligaciones derivadas del presente instrumento, por lo cual libera a éste de cualquier responsabilidad laboral.</dd>
                    <br><dd><b>DÉCIMA SEGUNDA.- RECONOCIMIENTO CONTRACTUAL</b>. El presente contrato se rige por lo dispuesto en el Título Décimo del Contrato de Prestación de Servicios, Capitulo I, del Código Civil del Estado de Chiapas, por lo que no existe relación de dependencia ni de subordinación entre <b>“EL ICATECH”</b> y <b>“EL PRESTADOR DE SERVICIOS”</b>, ni podrán tenerse como tales los necesarios nexos de coordinación entre uno y otro.</dd>
                    <br><dd>El presente contrato constituye el acuerdo de voluntades entre las partes, en relación con el objeto del mismo y deja sin efecto cualquier otra negociación o comunicación entre éstas, ya sea oral o escrita con anterioridad a la fecha de su firma.</dd>
                    <br><dd><b>DÉCIMA TERCERA</b>.- Manifiestan ambas partes bajo protesta de decir verdad que en el presente contrato no ha mediado dolo, error, mala fe, engaño, violencia, intimidación, ni cualquiera otra causa que pudiera invalidar el contenido y fuerza legal del mismo.</dd>
                    <br><dd><b>DÉCIMA CUARTA.- DOMICILIOS</b>. Para los efectos del presente instrumento las partes señalan como sus domicilios legales los siguientes:</dd>
                    <br><dd><b>“EL ICATECH”</b>: ubicado en la 14 poniente norte número 239, de la Colonia Moctezuma, con Código Postal 29030, en la Ciudad de Tuxtla Gutiérrez, Chiapas.</dd>
                    <br><dd><b>“EL PRESTADOR DE SERVICIOS”</b>: <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$data->domicilio}}</a>.</dd>
                    <br><dd>Mientras las partes no notifiquen por escrito el cambio de su domicilio, los emplazamientos y demás diligencias judiciales y extrajudiciales, se practicarán en el domicilio señalado en esta cláusula.</dd>
                    <br><dd><b>DÉCIMA QUINTA.- RESPONSABILIDAD DE “EL PRESTADOR DE SERVICIOS”</b>. <b>“EL PRESTADOR DE SERVICIOS”</b> será el responsable de la ejecución de los trabajos y deberá sujetarse en la realización de éstos, a todos aquellos reglamentos administrativos y manuales que las autoridades competentes hayan emitido, así como a las disposiciones establecidas al efecto por <b>“EL ICATECH”</b>.</dd>
                    <br><dd><b>DÉCIMA SEXTA.- JURISDICCIÓN</b>. Para la interpretación y cumplimiento del presente contrato, así como para todo aquello que no esté expresamente estipulado en el mismo, las partes se someterán a la jurisdicción y competencia de los tribunales del fuero común de la ciudad de Tuxtla Gutiérrez, Chiapas, renunciando al fuero que pudiera corresponderles por razón de su domicilio presente o futuro.</dd>
                    <br><dd>Leído que fue el presente contrato a las partes que en él intervienen y una vez enterados de su contenido y alcance legales, son conformes con los términos del mismo y para constancia lo firman y ratifican ante la presencia de los testigos que al final suscriben; en el municipio de <a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$data_contrato->municipio}}</a>, Chiapas; el día <a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$D}} de {{$M}} del año {{$Y}}</a>, en dos tantos originales.</dd>
                </div>
                <br><br>
                <table>
                    <tr>
                        <td colspan="2"><p align="center">"EL ICATECH"</p></td>
                        <td colspan="2"><p align="center">"EL (LA) PRESTADOR DE SERVICIOS"</p></td>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center"><br><br></td></div>
                        <td colspan="2"><div align="center"><br><br></td></div>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center"><a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}</a></td></div>
                        <td colspan="2"><div align="center">C. <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$nomins}}</a></td></div>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center">{{$director->puesto}} DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}}</td></div>
                        <td colspan="2"><div align="center"></td></div>
                    </tr>
                </table>
                <br><br>
                <p align="center">"TESTIGOS"</p>
                <br><br>
                <table>
                    <tr>
                        <td colspan="2"><p align="center"></p></td>
                        <td colspan="2"><p align="center"></p></td>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center"><a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$testigo1->nombre}} {{$testigo1->apellidoPaterno}} {{$testigo1->apellidoMaterno}}</a></td></div>
                        <td colspan="2"><div align="center"><a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$testigo2->nombre}} {{$testigo2->apellidoPaterno}} {{$testigo2->apellidoMaterno}}</a></td></div>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center">{{$testigo1->puesto}}</td></div>
                        <td colspan="2"><div align="center">{{$testigo2->puesto}}</td></div>
                    </tr>
                </table>
                <div align=center>
                    <br>
                    <br/>
                    <br>________________________________________
                    <br><small><a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$testigo3->nombre}} {{$testigo3->apellidoPaterno}} {{$testigo3->apellidoMaterno}}</a></small>
                    <br><small>{{$testigo3->puesto}} DE LA UNIDAD {{$data_contrato->unidad_capacitacion}} </small></b>
                </div>
                <br><br>
                <div align=justify>
                    <small  style="font-size: 12px;">Las Firmas que anteceden corresponden al Contrato de prestación de servicios profesionales por honorarios en su modalidad de horas curso No. <a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$data_contrato->numero_contrato}}</a>, que celebran por una parte el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, representado por el (la) C. <a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}, {{$director->puesto}} (a) DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}}</a>, y el (la) C. <a data-target="#instructorModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#instructorModal">{{$nomins}}</a>, en el Municipio de <a data-target="#contratoModal" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#contratoModal">{{$data_contrato->municipio}}, a {{$D}} de {{$M}} del año {{$Y}}</a>.</small>
                </div>
            </div>
        </div>
    </body>
    <div class="modal fade right" id="instructorModal" role="dialog">
        <div class="modal-dialog modal-full-height modal-right">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Informacion Del Instructor</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div>
                        <li>Nombre del Instructor: <b>{{$nomins}}</b></li><br>
                        <li>Especialidad Validada: <b>{{$especialidad->nombre}}</b></li><br>
                        <li>Folio de INE: <b>{{$data->folio_ine}}</b></li><br>
                        <li>RFC: <b>{{$data->rfc}}</b></li><br>
                        <li>CURP: <b>{{$data->curp}}</b></li><br>
                        <li>Domicilio: <b>{{$data->domicilio}}</b></li><br>
                    </div>
            </div>
        </div>
    </div>
    <div class="modal fade right" id="contratoModal" role="dialog">
        <div class="modal-dialog modal-full-height modal-right">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Informacion Del Contrato</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div>
                        <li>Numero de Contrato: <b>{{$data_contrato->numero_contrato}}</b></li><br>
                        <li>Unidad de Capacitación: <b>{{$data_contrato->unidad_capacitacion}}</b></li><br>
                        <li>Director(a) de la U.C.: <b>{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}</b></li><br>
                        <li>Pago al Instructor: <b>${{$cantidad}}</b></li><br>
                        <li>Pago al Instructor (Letra): <b>({{$data_contrato->cantidad_letras1}} {{$monto['1']}}/100 M.N.)</b></li><br>
                        <li>Municipio de la Firma: <b>{{$data_contrato->municipio}}</b></li><br>
                        <li>Fecha de la Firma: <b>{{$D}} de {{$M}} del año {{$Y}}</b></li><br>
                        <li>Nombre del Primer Testigo: <b>{{$testigo1->nombre}} {{$testigo1->apellidoPaterno}} {{$testigo1->apellidoMaterno}}</b></li><br>
                        <li>Puesto del Primer Testigo: <b>{{$testigo1->puesto}}</b></li><br>
                        <li>nombre del Segundo Testigo: <b>{{$testigo2->nombre}} {{$testigo2->apellidoPaterno}} {{$testigo2->apellidoMaterno}}</b></li><br>
                        <li>Puesto del Segundo Testigo: <b>{{$testigo2->puesto}}</b></li><br>
                        <li>nombre del Tercer Testigo: <b>{{$testigo3->nombre}} {{$testigo3->apellidoPaterno}} {{$testigo3->apellidoMaterno}}</b></li><br>
                        <li>Puesto del Tercer Testigo: <b>{{$testigo3->puesto}}</b></li><br>
                    </div>
            </div>
        </div>
    </div>
</html>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
