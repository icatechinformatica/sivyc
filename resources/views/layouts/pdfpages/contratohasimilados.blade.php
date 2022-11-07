<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body{
            font-family: sans-serif;
        }
        @page {
            margin: 30px 60px, 60px, 60px;
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
            bottom: 0px;
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
        <div class= "container g-pt-30" style="font-size: 12px;">
            <div id="content">
                <div align=right> <b>Contrato No.{{$data_contrato->numero_contrato}}.</b> </div>
                <br><div align="justify"><b>CONTRATO DE PRESTACIÓN DE SERVICIOS PROFESIONALES POR HONORARIOS ASIMILADOS A SALARIOS EN SU MODALIDAD DE HORAS CURSO, QUE CELEBRAN POR UNA PARTE, EL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, REPRESENTADO POR {{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}, EN SU CARÁCTER DE {{$director->puesto}} DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}} Y POR LA OTRA LA O EL C. {{$nomins}}, EN SU CARÁCTER DE INSTRUCTOR EXTERNO; A QUIENES EN LO SUCESIVO SE LES DENOMINARÁ “ICATECH” Y “PRESTADOR DE SERVICIOS” RESPECTIVAMENTE; MISMO QUE SE FORMALIZA AL TENOR DE LAS DECLARACIONES Y CLÁUSULAS SIGUIENTES:</b></div>
                <br><div align="center"> DECLARACIONES</div>
                <div align="justify">
                    <dl>
                        <dt>I.  <b>“ICATECH”</b> declara que:<br>
                        <br><dd>I.1 Es un Organismo Descentralizado de la Administración Pública Estatal, con personalidad jurídica y patrimonio propios, conforme a lo dispuesto en el artículo 1 del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                        <br><dd>I.2 La Mtra. Fabiola Lizbeth Astudillo Reyes, en su cáracter de Titular de la Dirección General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, cuenta con personalidad juridica que acredita con nombramiento expedido a su favor por el Dr. Rutilio Escandón Cadenas, Gobernador del Estado de Chiapas, de fecha 16 de enero de 2019, por lo que se encuentra plenamente facultada en términos de lo dispuesto en los artículos 28 fracción I de la Ley de Entidades Paraestatales del Estado de Chiapas; 15 fracción I del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, así como el 13 fracción IV del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, mismas que no le han sido limitadas o revocadas por lo que, delega su representación a los Titulares de las Unidades de Capacitación conforme a lo dispuesto por el artículo @if($fecha_fir >= $fecha_act) 42 fracción I @else 29 fracción I @endif del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                        <br><dd>I.3 {{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}, {{$director->puesto}} DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}} tiene personalidad jurídica para representar en este acto a “ICATECH”, como lo acredita con el nombramiento expedido por la Titular de la Dirección General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, y cuenta con plena facultad legal para suscribir el presente Instrumento conforme a lo dispuesto por los artículos @if($fecha_fir >= $fecha_act) 42 fracción I @else 29 fracción I @endif del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas y 12 fracción V, de los Lineamientos para los Procesos de Vinculación y Capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                        <br><dd>I.4 Tiene por objetivo impartir e impulsar la capacitación para la formación en el trabajo, propiciando la mejor calidad y vinculación de este servicio con el aparato productivo y las necesidades de desarrollo regional, estatal y nacional; actuar como organismo promotor en materia de capacitación para el trabajo, conforme a lo establecido por la Secretaría de Educación Pública; promover la capacitación que permita adquirir, reforzar o potencializar los conocimientos, habilidades y destrezas necesarias para elevar el nivel de vida, competencia laboral y productividad en el Estado; promover el surgimiento de nuevos perfiles académicos, que correspondan a las necesidades del mercado laboral.</dd>
                        <br><dd>I.5 De acuerdo a las necesidades de <b>“ICATECH”</b>, se requiere contar con los servicios de una persona física con conocimientos en {{$especialidad->nombre}}, por lo que se ha determinado llevar a cabo la Contratación por <b>HONORARIOS ASIMILADOS A SALARIOS</b> en la modalidad de horas curso como <b>"PRESTADOR DE SERVICIOS"</b>.</dd>
                        <br><dd>I.6 Para los efectos del presente contrato se cuenta con la clave de grupo {{$data->clave}} y validación del instructor emitido por la Dirección Técnica Académica de <b>“ICATECH”</b> conforme a lo dispuesto por el artículo 4 fracción III de los Lineamientos para los Procesos de Vinculación y Capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, emitido por la Dirección Técnica Académica de <b>“ICATECH”</b>.</dd>
                        <br><dd>I.7 Para los efectos del presente contrato se cuenta con la suficiencia presupuestal, conforme al presupuesto de egresos autorizado, emitido por la Dirección de Planeación de <b>“ICATECH”</b>.</dd>
                        <br><dd>I.8 Para los efectos del presente Contrato señala como su domicilio legal, el ubicado en la 14 poniente norte, número 239, Colonia Moctezuma, C. P. 29030, en la Ciudad de Tuxtla Gutiérrez, Chiapas.</dd>
                    </dl>
                    <dl><dt>II. <b>"PRESTADOR DE SERVICIOS"</b> declara que:</dt>
                        <br><dd>II.1 Es una persona física, de nacionalidad Mexicana, que acredita mediante @if($data->instructor_tipo_identificacion == 'INE')Credencial Para Votar @else{{$data->instructor_tipo_identificacion}}@endif con número de folio {{$data->instructor_folio_identificacion}}, con plena capacidad jurídica y facultades que le otorga la ley, para contratar y obligarse, así como también con los estudios, conocimientos y la experiencia necesaria en la materia de {{$especialidad->nombre}} y conoce plenamente las necesidades de los servicios objeto del presente contrato, así como que ha considerado todos los factores que intervienen para desarrollar eficazmente las actividades que desempeñará.</dd>
                        <br><dd>II.2 Cuenta con el Registro Federal de Contribuyentes número {{$data->rfc}}, expedido por el Servicio de Administración Tributaria de la Secretaría de Hacienda y Crédito Público, conforme a lo dispuesto por los artículos 27 del Código Fiscal de la Federación y 110 fracción I de la Ley de Impuesto sobre la Renta.</dd>
                        <br><dd>II.3 Es conforme de que <b>“ICATECH”</b>, le retenga los impuestos a que haya lugar por concepto de la Prestación de Servicios Profesionales por <b>HONORARIOS ASIMILADOS A SALARIOS</b>.</dd>
                        <br><dd>II.4 Bajo protesta de decir verdad, no se encuentra inhabilitado por autoridad competente alguna, así como a la suscripción del presente documento no ha sido parte en juicios del orden civil, mercantil, penal, administrativo o laboral en contra de <b>“ICATECH”</b> o de alguna otra institución pública o privada; y que no se encuentra en algún otro supuesto o situación que pudiera generar conflicto de intereses para prestar los servicios profesionales objeto del presente contrato.</dd>
                        <br><dd>II.5 Para los efectos del presente contrato, señala como su domicilio legal el ubicado en {{$data->domicilio}}.</dd>
                    </dl>
                </div>
                <div align="justify">Con base en las declaraciones antes expuestas, declaran las partes que es su voluntad celebrar el presente contrato, sujetándose a las siguientes:</div>
                <br>
                <div align="center"><strong> CLÁUSULAS </strong></div>
                <br><div align="justify">
                    <dd><b>PRIMERA.- OBJETO DEL CONTRATO</b>. El presente instrumento tiene por objeto establecer al <b>“PRESTADOR DE SERVICIOS”</b> los términos y condiciones que se obliga con <b>“ICATECH”</b>, a brindar sus servicios profesionales bajo el régimen de <b>HONORARIOS ASIMILADOS A SALARIOS,</b> para otorgar el curso establecido en el ARC01 y/o ARC02.</dd>
                    <br><dd><b>SEGUNDA.- MONTO DE LOS HONORARIOS</b>. El monto total de los servicios que <b>“ICATECH”</b>, pagará al <b>“PRESTADOR DE SERVICIOS”</b> será por la cantidad de <b>${{$cantidad}} ({{$data_contrato->cantidad_letras1}} {{$monto['1']}}/100 M.N.)</b>, por curso impartido, mismos que se acumularán y serán pagaderos mensualmente, menos las retenciones que el ICATECH le retendrá como pago provisional por concepto del Impuesto sobre la Renta, de conformidad con lo establecido en el Artículo 96 de la Ley del Impuesto Sobre la Renta, enterando a las Autoridades Hacendarias las retenciones correspondientes.</dd>
                    <br><dd>El monto resultante señalado en el <b>párrafo primero</b> de esta cláusula se otorgará al <b>“PRESTADOR DE SERVICIOS”</b> conforme a la disponibilidad financiera de <b>“ICATECH”</b>; que se realizará en una sola exhibición, por medio de cheque y/o transferencia electrónica interbancaria a la cuenta que señala, y se agrega al presente contrato, por lo que se efectuará la acumulación de todos los cursos impartidos durante el mes y se realizará la determinación del ISR correspondiente. Asi mismo se elaborará un recibo, el cual se le hará llegar al correo que indique, (la cual se agrega al presente instrumento) mismo que deberá cubrir los requisitos fiscales estipulados por la Secretaría de Hacienda y Crédito Público; por lo que <b>“PRESTADOR DE SERVICIOS”</b> no podrá exigir retribución alguna por ningún otro concepto.</dd>
                    <br><dd><b>TERCERA.- DE LA OBLIGACIÓN DEL “PRESTADOR DE SERVICIOS”</b>. Se obliga a desempeñar las obligaciones que contrae en este acto y con todo el sentido ético y profesional que requiere <b>“ICATECH”</b>, de acuerdo con las políticas y reglamentos del mismo para:</dd>
                    <Ol type = "I">
                        <li> Diseñar, preparar y dictar los cursos a su cargo con toda la diligencia y esmero que exige la calidad de <b>“ICATECH”</b>.</li>
                        <br><li> Asistir con toda puntualidad a sus cursos y aprovechar íntegramente el tiempo necesario para el mejor desarrollo de los mismos.</li>
                        <br><li> Generar un reporte final del curso impartido o de cualquier incidente o problema que surgió en el desarrollo del mismo.</li>
                        <br><li> Cumplir con los procedimientos del control escolar de alumnos que implemente <b>“ICATECH”</b>.</li>
                        <br><li> Respetar las normas de conducta que establece <b>“ICATECH”</b>.</li>
                        <br><li> Implementar el Lenguaje Incluyente en la impartición de los cursos.</li>
                    </Ol>
                </div>
                <div align="justify">
                    <dd><b>CUARTA.- SECRETO PROFESIONAL DEL “PRESTADOR DE SERVICIOS”.</b> En el presente contrato se obliga al <b>“PRESTADOR DE SERVICIOS”</b>, a no divulgar por medio de publicaciones, informes, videos, fotografías, medios electrónicos, conferencias o en cualquier otra forma, los datos y resultados obtenidos de los trabajos de este contrato, sin la autorización expresa de <b>“ICATECH”</b>, pues dichos datos y resultados son considerados confidenciales. Esta obligación subsistirá, aún después de haber terminado la vigencia de este contrato.</dd>
                    <br><dd><b>QUINTA.- VIGENCIA</b>. La vigencia del presente contrato será conforme a la duración del curso objeto del presente Instrumento, detallados en la <b>CLÁUSULA PRIMERA</b>; el cual será forzoso al <b>“PRESTADOR DE SERVICIOS”</b> y voluntario para <b>“ICATECH”</b> mismo que podrá darlo por terminado anticipadamente en cualquier tiempo, siempre y cuando existan motivos o razones de interés general, incumpla cualquiera de las obligaciones adquiridas con la formalización del presente instrumento o incurra en alguna de las causales previstas en la Cláusula Octava, mediante notificación por escrito a <b>“PRESTADOR DE SERVICIOS”</b>; en todo caso <b>“ICATECH”</b> deberá cubrir el monto únicamente en cuanto a los servicios prestados.</dd>
                    <br><dd>Concluido el término del presente contrato no podrá haber prórroga automática por el simple transcurso del tiempo y terminará sin necesidad de darse aviso entre las partes. Si terminada la vigencia de este contrato, <b>“ICATECH”</b> tuviere necesidad de seguir utilizando los servicios del <b>“PRESTADOR DE SERVICIOS”</b>, se requerirá la celebración de un nuevo contrato, sin que éste pueda ser computado con el anterior.</dd>
                    <br><dd><b>SEXTA.- SEGUIMIENTO. “ICATECH”</b> a través de los representantes que al efecto designe, tendrá en todo tiempo el derecho de supervisar el estricto cumplimiento de este contrato, por lo que podrá revisar e inspeccionar las actividades que desempeñe <b>“PRESTADOR DE SERVICIOS”</b>.</dd>
                    <br><dd><b>SÉPTIMA.- PROPIEDAD DE RESULTADOS Y DERECHOS DE AUTOR</b>. Los documentos, estudios y demás materiales que se generen en la ejecución o como consecuencia de este contrato, serán propiedad de <b>“ICATECH”</b>, obligando al <b>“PRESTADOR DE SERVICIOS”</b> a entregarlos al término del presente instrumento.</dd>
                    <br><dd>Se obliga al <b>“PRESTADOR DE SERVICIOS”</b> a responder ilimitadamente de los daños o perjuicios que pudiera causar a <b>“ICATECH”</b> o a terceros, si con motivo de la prestación de los servicios contratados viola derechos de autor, de patentes y/o marcas u otro derecho reservado, por lo que manifiesta en este acto bajo protesta de decir verdad, no encontrarse en ninguno de los supuestos de infracción a la Ley Federal de Derechos de Autor ni a la Ley de Propiedad Industrial.</dd>
                    <br><dd>En caso de que sobreviniera alguna reclamación o controversia legal en contra de <b>“ICATECH”</b> por cualquiera de las causas antes mencionadas, la única obligación de éste será dar aviso al <b>“PRESTADOR DE SERVICIOS”</b> en el domicilio previsto en este instrumento para que ponga a salvo a <b>“ICATECH”</b> de cualquier controversia.</dd>
                    <br><dd><b>OCTAVA.- RESCISIÓN. “ICATECH”</b> podrá rescindir el presente contrato sin responsabilidad alguna, sin necesidad de declaración judicial, bastando para ello una notificación por escrito cuando concurran causas de interés general, cuando el <b>“PRESTADOR DE SERVICIOS”</b> incumpla algunas de las obligaciones del presente contrato y demás disposiciones contenidas en las leyes que le sean aplicables y cuando a juicio de <b>“ICATECH”</b> incurra en las siguientes causales:</dd>
                </div>
                <ol type = "I">
                    <li>Negligencia o impericia. </li>
                    <li>Falta de probidad u honradez.</li>
                    <li>Por prestar los servicios de forma ineficiente e inoportuna.</li>
                    <li>Por no apegarse a lo estipulado en el presente contrato.</li>
                    <li>Por no observar la discreción debida respecto a la información a la que tenga acceso como consecuencia de la información de los servicios encomendados.</li>
                    <li>Por suspender injustificadamente la prestación de los servicios o por negarse a corregir lo rechazado por <b>“ICATECH”</b>.</li>
                    <li>Por negarse a informar a <b>“ICATECH”</b> sobre los resultados de la prestación del servicio encomendado. </li>
                    <li>Por impedir el desempeño normal de las labores durante la prestación de los servicios. </li>
                    <li>Si se comprueba que la protesta a que se refiere la Declaración II.2 de <b>“PRESTADOR DE SERVICIOS”</b> se realizó con falsedad.</li>
                    <li>Por no otorgar los cursos en el tiempo establecido (horas del curso). </li>
                </ol>
                <div align="justify"><dd>Asimismo; en caso de tener evidencias de que el curso no fue impartido, se procederá a dar por rescindido el contrato, y se interpondrá la acción legal que corresponda.</dd>
                    <br><dd>Podrá dar por rescindido al <b>“PRESTADOR DE SERVICIOS”</b> el <b>“ICATECH”</b> de forma anticipada el presente contrato, previo aviso que realice por escrito con un mínimo de 10 días hábiles.</dd>
                    <br><dd><b>“ICATECH”</b> se reservará el derecho de aceptar la terminación anticipada del contrato, sin que ello implique la renuncia a deducir las acciones legales que en su caso procedan.</dd>
                    <br><dd><b>NOVENA.- CESIÓN. “PRESTADOR DE SERVICIOS”</b> no podrá en ningún caso ceder total o parcialmente a terceros llámese persona física o persona moral, los derechos y obligaciones derivadas del presente contrato.</dd>
                    <br><dd><b>DÉCIMA.- RELACIONES PROFESIONALES. “ICATECH”</b> no adquiere ni reconoce obligación alguna de carácter laboral a favor del <b>“PRESTADOR DE SERVICIOS”</b>, en virtud de no ser aplicables a la relación contractual que consta en este instrumento, los artículos 1º y 8º de la Ley Federal del Trabajo y 123 apartado “A” y “B” de la Constitución Política de los Estados Unidos Mexicanos, por lo que no será considerado al <b>“PRESTADOR DE SERVICIOS”</b> como trabajador de <b>“ICATECH”</b> para los efectos legales y en particular para obtener las prestaciones establecidas en su artículo 5 A, fracciones V, VI y VII de la Ley del Instituto Mexicano del Seguro Social.</dd>
                    <br><dd>En el presente instrumento se obliga al <b>“PRESTADOR DE SERVICIOS”</b> a ser el único responsable del cumplimiento con las normas laborales, fiscales, o cualquier otro acto contractual de diversa índole, incluso las de seguridad social e INFONAVIT que pudieran derivarse de la prestación de los servicios aquí contratados, consecuentemente libera de toda responsabilidad a <b>“ICATECH”</b> de las obligaciones que pudieran presentarse por estos conceptos.</dd>
                    <br><dd><b>DÉCIMA PRIMERA.- RECONOCIMIENTO CONTRACTUAL</b>. El presente contrato se rige por lo dispuesto en el Título Décimo del Contrato de Prestación de Servicios, Capítulo I, del Código Civil del Estado de Chiapas, por lo que no existe relación de dependencia ni de subordinación entre <b>“ICATECH”</b> y <b>“PRESTADOR DE SERVICIOS”</b>, ni podrán tenerse como tales los necesarios nexos de coordinación entre uno y otro.</dd>
                    <br><dd>El presente contrato constituye el acuerdo de voluntades entre las partes, en relación con el objeto del mismo y deja sin efecto cualquier otra negociación o comunicación entre éstas, ya sea oral o escrita con anterioridad a la fecha de su firma.</dd>
                    <br><dd><b>DÉCIMA SEGUNDA</b>.- Manifiestan ambas partes bajo protesta de decir verdad que en el presente contrato no ha mediado dolo, error, mala fe, engaño, violencia, intimidación, ni cualquiera otra causa que pudiera invalidar el contenido y fuerza legal del mismo.</dd>
                    <br><dd><b>DÉCIMA TERCERA.- DOMICILIOS</b>. Para los efectos del presente instrumento las partes señalan como sus domicilios legales los estipulados en el Apartado de Declaraciones del presente instrumento legal.</dd>
                    <br><dd>Mientras las partes no notifiquen por escrito el cambio de su domicilio, los emplazamientos y demás diligencias judiciales y extrajudiciales, se practicarán en el domicilio señalado en esta cláusula.</dd>
                    <br><dd><b>DÉCIMA CUARTA.- RESPONSABILIDAD DEL “PRESTADOR DE SERVICIOS”</b>. Será el responsable de la ejecución de los trabajos y deberá sujetarse en la realización de éstos, a todos aquellos reglamentos administrativos y manuales que las autoridades competentes hayan emitido, así como a las disposiciones establecidas por <b>“ICATECH”</b>.</dd>
                    <br><dd><b>DÉCIMA QUINTA</b>.- Las partes convienen que los datos personales insertos en el presente instrumento legal son protegidos por la Ley de Protección de Datos Personales en Posesión de Sujetos Obligados del Estado de Chiapas y la Ley de Transparencia y Acceso a la Información Publica del Estado de Chiapas, así como los Lineamientos Generales de la Custodia y Protección de Datos Personales e Información Reservada y Confidencial en Posesión de los Sujetos Obligados del Estado de Chiapas y demás normatividad aplicable.</dd>
                    <br><dd><b>DÉCIMA SEXTA.- JURISDICCIÓN</b>. Para la interpretación y cumplimiento del presente contrato, así como para todo aquello que no esté expresamente estipulado en el mismo, las partes se someterán a la jurisdicción y competencia de los tribunales del fuero común de la ciudad de Tuxtla Gutiérrez, Chiapas, renunciando al fuero que pudiera corresponderles por razón de su domicilio presente o futuro.</dd>
                    <br><dd>Leído que fue el presente contrato a las partes que en él intervienen y una vez enterados de su contenido y alcance legales, son conformes con los términos del mismo y para constancia lo firman y ratifican ante la presencia de los testigos que al final suscriben; en el municipio de {{$data_contrato->municipio}}, Chiapas; el día {{$D}} de {{$M}} del año {{$Y}}, en dos ejemplares en original.</dd>
                </div>
                <table>
                    <tr>
                        <td colspan="2"><p align="center"><b>"ICATECH"</b></p></td>
                        <td colspan="2"><p align="center"><b>"PRESTADOR DE SERVICIOS"</b></p></td>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center"><br><br></td></div>
                        <td colspan="2"><div align="center"><br><br></td></div>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center"><b>{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}</b></td></div>
                        <td colspan="2"><div align="center"><b>C. {{$nomins}}</b></td></div>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center"><b>{{$director->puesto}} DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}}</b></td></div>
                        <td colspan="2"><div align="center"></td></div>
                    </tr>
                </table>
                <p align="center"><b>"TESTIGOS"</b></p>
                <br><br>
                <table>
                    <tr>
                        <td colspan="2"><p align="center"></p></td>
                        <td colspan="2"><p align="center"></p></td>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center"><b>{{$testigo1->nombre}} {{$testigo1->apellidoPaterno}} {{$testigo1->apellidoMaterno}}</b></td></div>
                        <td colspan="2"><div align="center"><b>{{$testigo2->nombre}} {{$testigo2->apellidoPaterno}} {{$testigo2->apellidoMaterno}}</b></td></div>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center"><b>{{$testigo1->puesto}}</b></td></div>
                        <td colspan="2"><div align="center"><b>{{$testigo2->puesto}}</b></td></div>
                    </tr>
                </table>
                <div align=center>
                    <br>
                    <br/>
                    <br>________________________________________
                    <br><small><b>{{$testigo3->nombre}} {{$testigo3->apellidoPaterno}} {{$testigo3->apellidoMaterno}}</b></small>
                    <br><small><b>{{$testigo3->puesto}}</b> </small></b>
                </div>
                <br>
                <div align=justify>
                    <small  style="font-size: 10px;">Las Firmas que anteceden corresponden al Contrato de prestación de servicios profesionales por honorarios asimilados a salarios en su modalidad de horas curso No. {{$data_contrato->numero_contrato}}, que celebran por una parte el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, representado por el (la) C. {{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}, {{$director->puesto}} DE CAPACITACIÓN {{$data_contrato->unidad_capacitacion}}, y el (la) C. {{$nomins}}, en el Municipio de {{$data_contrato->municipio}}, a {{$D}} de {{$M}} del año {{$Y}}.</small>
                </div>
            </div>
        </div>
    </body>
</html>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
