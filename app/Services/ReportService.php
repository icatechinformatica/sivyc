<?php

namespace App\Services;
use PDF;
use App\Models\Reportes\Rf001Model;
use App\Models\Unidad;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;

class ReportService
{
    public function __construct()
    {
        // setear los datos
    }

    public function getReport($distintivo, $organismo, $id, $nombreElaboro, $puestoElaboro)
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $rf001Detalle = new Rf001Model();
        $rf001 = $rf001Detalle::findOrFail($id);
        $data = \DB::table('tbl_unidades')->where('unidad', $rf001->unidad)->first();
        $unidad = strtoupper($data->ubicacion);
        $municipio = mb_strtoupper($data->municipio, 'UTF-8');
        #OBTENEMOS LA FECHA ACTUAL
        $fechaActual = getdate();
        $anio = $fechaActual['year']; $mes = $fechaActual['mon']; $dia = $fechaActual['mday'];
        $dia = ($dia < 10) ? '0'.$dia : $dia;

        $fecha_comp = $dia.' de '.$meses[$mes-1].' del '.$anio;
        $dirigido = \DB::table('tbl_funcionarios')->where('id', 12)->first();
        $conocimiento = \DB::table('tbl_funcionarios')
            ->leftjoin('tbl_organismos', 'tbl_organismos.id', '=', 'tbl_funcionarios.id_org')
            ->where('tbl_organismos.id', 13)
            ->select('tbl_organismos.nombre', 'tbl_funcionarios.nombre as nombre_funcionario', 'tbl_funcionarios.cargo', 'tbl_funcionarios.titulo')
            ->first();
        $direccion = $data->direccion;

        $delegado = \DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('f.activo', 'true')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $rf001->unidad)
            ->First();
        // return $data;

        return PDF::loadView('reportes.rf001.reporterf001', compact('distintivo', 'organismo', 'data', 'unidad', 'rf001', 'municipio', 'fecha_comp', 'dirigido', 'direccion', 'conocimiento', 'nombreElaboro', 'puestoElaboro', 'delegado'));
    }

    public function xmlFormat($id, $organismo, $unidad, $usuario)
    {
        $rf001 = (new Rf001Model())->findOrFail($id); // obtener RF001 por id

        dd($rf001);
        // creación del cuerpo
        $body = $this->createBody($id, $rf001);

        $ubicacion = Unidad::where('id', $unidad)->value('ubicacion');

        $dataFirmantes = \DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
            ->Join('tbl_unidades AS u', 'u.id', 'org.id_unidad')
            ->Where('org.id_parent',1)
            ->Where('fun.activo', 'true')
            ->Where('u.unidad', $ubicacion)
            ->First();

        $body = $this->createBody($id, $dataFirmantes);
        $nameFileOriginal = 'contrato '.$rf001->memorandum.'.pdf';
        $numFirmantes = '1';

        //Creacion de array para pasarlo a XML
        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => $usuario->name,
                    'cargo_emisor' => $usuario->puesto,
                    'dependencia_emisor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas'
                    // 'curp_emisor' => $dataEmisor->curp
                ],
            ],
            'archivo' => [
                '_attributes' => [
                    'nombre_archivo' => $nameFileOriginal
                    // 'md5_archivo' => $md5
                    // 'checksum_archivo' => utf8_encode($text)
                ],
                // 'cuerpo' => ['Por medio de la presente me permito solicitar el archivo '.$nameFile]
                'cuerpo' => [strip_tags($body)]
            ],
            'firmantes' => [
                '_attributes' => [
                    'num_firmantes' => $numFirmantes
                ],
                'firmante' => [
                    $dataFirmantes
                ]
            ],
        ];
        //Creacion de estampa de hora exacta de creacion
        $date = Carbon::now();
        $month = $date->month < 10 ? '0'.$date->month : $date->month;
        $day = $date->day < 10 ? '0'.$date->day : $date->day;
        $hour = $date->hour < 10 ? '0'.$date->hour : $date->hour;
        $minute = $date->minute < 10 ? '0'.$date->minute : $date->minute;
        $second = $date->second < 10 ? '0'.$date->second : $date->second;
        $dateFormat = $date->year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;

        return $body;
    }

    protected function createBody($id, $firmante)
    {
        $bodyHtml = null;

        $bodyHtml = '<div align=right> <b>Contrato No.' . 312423423 . '.</b> </div>
        <br><div align="justify"><b>CONTRATO DE PRESTACIÓN DE SERVICIOS PROFESIONALES MODALIDAD DE '.'ESTO ES UNA PRUEBA'.', QUE CELEBRAN POR UNA PARTE, EL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, REPRESENTADO POR '. 'DFSDFSFDSDFS'. ', EN SU CARÁCTER DE '. 'SADSADDSDSADSA'.' '.'ADSDSADSASAD'.' Y POR LA OTRA LA O EL C. '.'SDAADHKJH'. ', EN SU CARÁCTER DE INSTRUCTOR EXTERNO; A QUIENES EN LO SUCESIVO SE LES DENOMINARÁ “ICATECH” Y “PRESTADOR DE SERVICIOS” RESPECTIVAMENTE; MISMO QUE SE FORMALIZA AL TENOR DE LAS DECLARACIONES Y CLÁUSULAS SIGUIENTES:</b></div>
        <br><div align="center"> DECLARACIONES</div>
        <div align="justify">
            <dl>
                <dt>I.  <b>“ICATECH”</b> declara que:<br>
                <br><dd>I.1 Es un Organismo Descentralizado de la Administración Pública Estatal, con personalidad jurídica y patrimonio propio, conforme a lo dispuesto en el artículo 1 del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                <br><dd>I.2 La Mtra. Fabiola Lizbeth Astudillo Reyes, en su carácter de Titular de la Dirección General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, cuenta con personalidad jurídica que acredita con nombramiento expedido a su favor por el Dr. Rutilio Escandón Cadenas, Gobernador del Estado de Chiapas, de fecha 16 de enero de 2019, por lo que se encuentra plenamente facultada en términos de lo dispuesto en los artículos 28 fracción I de la Ley de Entidades Paraestatales del Estado de Chiapas; 15 fracción I del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, así como el 13 fracción IV del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, mismas que no le han sido limitadas o revocadas por lo que, delega su representación a los Titulares de las Unidades de Capacitación conforme a lo dispuesto por el artículo 42 fracción I del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                <br><dd>I.3 '. 'TUXTLA'.', '.'SDADSADSA'.' '.'DSADSADSADSA'.', tiene personalidad jurídica para representar en este acto a “ICATECH”, como lo acredita con el nombramiento expedido por la Titular de la Dirección General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, y cuenta con plena facultad legal para suscribir el presente Instrumento conforme a lo dispuesto por los artículos 42 fracción I del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas y 12 fracción V, de los Lineamientos para los Procesos de Vinculación y Capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                <br><dd>I.4 Tiene por objetivo impartir e impulsar la capacitación para la formación en el trabajo, propiciando la mejor calidad y vinculación de este servicio con el aparato productivo y las necesidades de desarrollo Regional, Estatal y Nacional; actuar como Organismo promotor en materia de capacitación para el trabajo, conforme a lo establecido por la Secretaría de Educación Pública; promover la capacitación que permita adquirir, reforzar o potencializar los conocimientos, habilidades y destrezas necesarias para elevar el nivel de vida, competencia laboral y productividad en el Estado; promover el surgimiento de nuevos perfiles académicos, que correspondan a las necesidades del mercado laboral.</dd>
                <br><dd>I.5 De acuerdo a las necesidades de <b>“ICATECH”</b>, se requiere contar con los servicios de una persona física con conocimientos en '. 'DDFSSDSDF' .', por lo que se ha determinado llevar a cabo la Contratación bajo el régimen de <b>SUELDOS Y SALARIOS E INGRESOS ASIMILADOS A SALARIOS,</b> en la modalidad de '.'CERTIFICACIÓN AUTORIZADA'.' como <b>"PRESTADOR DE SERVICIOS"</b>.</dd>
                <br><dd>I.6 Para los efectos del presente contrato se cuenta con la clave de grupo No: '. 'WQEEWEQW'. ', así como la validación del instructor emitido por la Dirección Técnica Académica de <b>“ICATECH”</b> conforme a lo dispuesto por el artículo 4 fracción III de los Lineamientos para los Procesos de Vinculación y Capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, emitido por la Dirección Técnica Académica de <b>“ICATECH”</b>.</dd>
                <br><dd>I.7 Para los efectos del presente Contrato se cuenta con la suficiencia presupuestal, conforme al presupuesto de egresos autorizado, emitido por la Dirección de Planeación de <b>“ICATECH”</b>.</dd>
                <br><dd>I.8 Para los efectos del presente Contrato señala como su domicilio legal, el ubicado en la 14 poniente norte, número 239, Colonia Moctezuma, C. P. 29030, en la Ciudad de Tuxtla Gutiérrez, Chiapas.</dd>
            </dl>
            <dl><dt>II. <b>"PRESTADOR DE SERVICIOS"</b> declara que:</dt>
                <br><dd>II.1 Es una persona física, de nacionalidad mexicana, que acredita mediante'. 'INE' .' con número de folio '.'DSFJDLKJ'. ', con plena capacidad jurídica y facultades que le otorga la ley, para contratar y obligarse, así como también con los estudios, conocimientos y la experiencia necesaria en la materia de '. 'CXVCVXXVC'. ' y conoce plenamente las necesidades de los servicios objeto del presente contrato, así como que ha considerado todos los factores que intervienen para desarrollar eficazmente las actividades que desempeñará.</dd>
                <br><dd>II.2 Se encuentra al corriente en el pago de sus impuestos y cuenta con el Registro Federal de Contribuyentes número '. 'JDSALKAJLKSDJA'. ', expedido por el Servicio de Administración Tributaria de la Secretaría de Hacienda y Crédito Público, conforme a lo dispuesto por los artículos 27 del Código Fiscal de la Federación y 110 fracción I de la Ley de Impuesto sobre la Renta.</dd>
                <br><dd>II.3 Es conforme de que <b>“ICATECH”</b>, le retenga los impuestos a que haya lugar por concepto de la Prestación de Servicios Profesionales por <b>SUELDOS Y SALARIOS E INGRESOS ASIMILADOS A SALARIOS</b>.</dd>
                <br><dd>II.4 Bajo protesta de decir verdad, no se encuentra inhabilitado por autoridad competente alguna, así como a la suscripción del presente documento no ha sido parte en juicios del orden civil, mercantil, penal, administrativo o laboral en contra de <b>“ICATECH”</b> o de alguna otra institución pública o privada; y que no se encuentra en algún otro supuesto o situación que pudiera generar conflicto de intereses para prestar los servicios profesionales objeto del presente contrato.</dd>
                <br><dd>II.5 Para los efectos del presente contrato, señala como su domicilio legal el ubicado en C: '.'SADDSALJLKDL'. '.</dd>
            </dl>
        </div>
        <div align="justify">Con base en las declaraciones antes expuestas, declaran las partes que es su voluntad celebrar el presente contrato, sujetándose a las siguientes:</div>
        <br>
        <div align="center"><strong> CLÁUSULAS </strong></div>
        <br><div align="justify">
            <dd><b>PRIMERA.- OBJETO DEL CONTRATO</b>. El presente instrumento tiene por objeto establecer al <b>“PRESTADOR DE SERVICIOS”</b> los términos y condiciones que se obliga con <b>“ICATECH”</b>, a brindar sus servicios profesionales bajo el régimen de <b>SUELDOS Y SALARIOS E INGRESOS ASIMILADOS A SALARIOS,</b> para otorgar '. 'el curso establecido' . '  en el ARC01 y/o ARC02.</dd>
            <br><dd><b>SEGUNDA.- MONTO</b>. El monto que <b>“ICATECH”</b>, pagará al <b>“PRESTADOR DE SERVICIOS”</b> será por la cantidad de <b>$'. '1000.00'. '/100 M.N. </b>; por '.'CURSO'.', menos las retenciones que el <b>“ICATECH”</b> le realizará como pago provisional por concepto de Impuesto sobre la Renta, de conformidad con lo establecido al artículo 96 de la Ley del Impuesto sobre la Renta, enterando a las Autoridades Hacendarias las retenciones correspondientes.</dd>
            <br><dd>El monto resultante señalado en el <b>párrafo primero</b> de esta cláusula se otorgará al <b>“PRESTADOR DE SERVICIOS”</b> conforme a la disponibilidad financiera de <b>“ICATECH”</b>; que se realizará en una sola exhibición, por medio de cheque y/o transferencia interbancaria a la cuenta que señala, y se agrega al presente Contrato. Así mismo, se expedirá el comprobante fiscal digital por internet (CFDI), el cual se hará llegar al correo que indique (el cual se agrega al presente Instrumento), mismo que deberá cubrir los requisitos fiscales estipulados por la Secretaría de Hacienda y Crédito Público; por lo que el <b>“PRESTADOR DE SERVICIOS”</b> no podrá exigir retribución alguna por ningún otro concepto.</dd>
            <br><dd><b>TERCERA.- DE LA OBLIGACIÓN DEL “PRESTADOR DE SERVICIOS”</b>. Se obliga a desempeñar las obligaciones que contrae en este acto conforme a los procedimientos de control escolar y con todo el sentido ético y profesional que requiere <b>“ICATECH”</b>, de acuerdo con las políticas y reglamentos del mismo para:</dd>
            <Ol type = "I">
                <li> Diseñar, preparar e impartir '. 'la certificación extraordinaria' .' a su cargo con toda la diligencia y esmero que exige la calidad de <b>“ICATECH”</b>.</li>
                <br><li> Vigilar que su '.'CURSO'.' se aproveche íntegramente el tiempo necesario para el mejor desarrollo del mismo.</li>
                <br><li> Realizar la comprobación final '.'CURSO'.'.</li>
                <br><li> Respetar las normas de conducta.</li>
                <br><li> Implementar el Lenguaje Incluyente en la impartición '.'CURSO'.'.</li>
            </Ol>
        </div>
        <div align="justify">
            <dd><b>CUARTA.- SECRETO PROFESIONAL DEL “PRESTADOR DE SERVICIOS”.</b> En el presente contrato se obliga al <b>“PRESTADOR DE SERVICIOS”</b>, a no divulgar por medio de publicaciones, informes, videos, fotografías, medios electrónicos, conferencias o en cualquier otra forma, los datos y resultados obtenidos de los trabajos de este Contrato, sin la autorización expresa de <b>“ICATECH”</b>, pues dichos datos y resultados son considerados confidenciales. Esta obligación subsistirá, aún después de haber terminado la vigencia de este Contrato.</dd>
            <br><dd><b>QUINTA.- VIGENCIA</b>. La vigencia del presente Contrato será conforme a la duración '.'CURSO'.' objeto del presente Instrumento, detallados en la <b>CLÁUSULA PRIMERA</b>; el cual será forzoso al <b>“PRESTADOR DE SERVICIOS”</b> y voluntario para <b>“ICATECH”</b> mismo que podrá darlo por terminado anticipadamente en cualquier tiempo, siempre y cuando existan motivos o razones de interés general, incumpla cualquiera de las obligaciones adquiridas con la formalización del presente instrumento o incurra en alguna de las causales previstas en la Cláusula Octava, mediante notificación por escrito al <b>“PRESTADOR DE SERVICIOS”</b>; en todo caso <b>“ICATECH”</b> deberá cubrir el monto únicamente en cuanto a los servicios prestados.</dd>
            <br><dd>Concluido el término del presente Contrato no podrá haber prórroga automática por el simple transcurso del tiempo y terminará sin necesidad de darse aviso entre las partes. Si terminada la vigencia de este contrato, <b>“ICATECH”</b> tuviere necesidad de seguir utilizando los servicios del <b>“PRESTADOR DE SERVICIOS”</b>, se requerirá la celebración de un nuevo Contrato, sin que éste pueda ser computado con el anterior.</dd>
            <br><dd><b>SEXTA.- SEGUIMIENTO. “ICATECH”</b> a través de los representantes que al efecto designe, tendrá en todo tiempo el derecho de dar seguimiento del estricto cumplimiento de este contrato, en relación a las actividades del <b>“PRESTADOR DE SERVICIOS”</b>.</dd>
            <br><dd><b>SÉPTIMA.- PROPIEDAD DE RESULTADOS Y DERECHOS DE AUTOR</b>. Los documentos, estudios y demás materiales que se generen en la ejecución o como consecuencia de este contrato, serán propiedad de <b>“ICATECH”</b>, obligando al <b>“PRESTADOR DE SERVICIOS”</b> a entregarlos al término del presente instrumento.</dd>
            <br><dd>Se obliga al <b>“PRESTADOR DE SERVICIOS”</b> a responder ilimitadamente de los daños o perjuicios que pudiera causar a <b>“ICATECH”</b> o a terceros, si con motivo de la prestación de los servicios contratados viola derechos de autor, de patentes y/o marcas u otro derecho reservado, por lo que manifiesta en este acto bajo protesta de decir verdad, no encontrarse en ninguno de los supuestos de infracción a la Ley Federal de Derechos de Autor ni a la Ley de Propiedad Industrial.</dd>
            <br><dd>En caso de que sobreviniera alguna reclamación o controversia legal en contra de <b>“ICATECH”</b> por cualquiera de las causas antes mencionadas, la única obligación de éste será dar aviso al <b>“PRESTADOR DE SERVICIOS”</b> en el domicilio previsto en este instrumento para que ponga a salvo a <b>“ICATECH”</b> de cualquier controversia.</dd>
            <br><dd><b>OCTAVA.- RESCISIÓN. “ICATECH”</b> podrá rescindir el presente contrato sin responsabilidad alguna, sin necesidad de declaración judicial, bastando para ello una notificación por escrito cuando concurran causas de interés general, cuando el <b>“PRESTADOR DE SERVICIOS”</b> incumpla algunas de las obligaciones del presente Contrato y demás disposiciones contenidas en las leyes que le sean aplicables y cuando a juicio de <b>“ICATECH”</b> incurra en las siguientes causales:</dd>
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
            <li>Si se comprueba que la protesta a que se refiere la Declaración II.4 de <b>“PRESTADOR DE SERVICIOS”</b> se realizó con falsedad.</li>
            <li>Por no otorgar '.'CURSO'.' en el tiempo establecido. </li>
        </ol>
        <div align="justify"><dd>Asimismo; en caso de tener evidencias de que '.'la certificación extraordinaria'.', se procederá a dar por rescindido el contrato, y se interpondrá la acción legal que corresponda.</dd>
            <br><dd>El <b>“ICATECH”</b> podrá dar por terminado las obligaciones contractuales contraídas con el <b>“PRESTADOR DE SERVICIOS”</b> de forma anticipada, previo aviso que realice por escrito con un mínimo de 10 días hábiles.</dd>
            <br><dd><b>“ICATECH”</b> se reservará el derecho de aceptar la terminación anticipada del contrato, sin que ello implique la renuncia a deducir las acciones legales que en su caso procedan.</dd>
            <br><dd><b>NOVENA.- CESIÓN. “PRESTADOR DE SERVICIOS”</b> no podrá en ningún caso ceder total o parcialmente a terceros llámese persona física o persona moral, los derechos y obligaciones derivadas del presente contrato.</dd>
            <br><dd><b>DÉCIMA.- RELACIONES PROFESIONALES. “ICATECH”</b> no adquiere ni reconoce obligación alguna de carácter laboral a favor del <b>“PRESTADOR DE SERVICIOS”</b>, en virtud de no ser aplicables a la relación contractual que consta en este instrumento, los artículos 1º y 8º de la Ley Federal del Trabajo y 123 apartado “A” y “B” de la Constitución Política de los Estados Unidos Mexicanos, por lo que no será considerado al <b>“PRESTADOR DE SERVICIOS”</b> como trabajador de <b>“ICATECH”</b> para los efectos legales y en particular para obtener las prestaciones establecidas en su artículo 5 A, fracciones V, VI y VII de la Ley del Instituto Mexicano del Seguro Social.</dd>
            <br><dd>En el presente instrumento se obliga al <b>“PRESTADOR DE SERVICIOS”</b> a ser el único responsable del cumplimiento con las normas laborales, fiscales, o cualquier otro acto contractual de diversa índole, incluso las de seguridad social e INFONAVIT que pudieran derivarse de la prestación de los servicios aquí contratados, consecuentemente libera de toda responsabilidad a <b>“ICATECH”</b> de las obligaciones que pudieran presentarse por estos conceptos.</dd>
            <br><dd><b>DÉCIMA PRIMERA.- RECONOCIMIENTO CONTRACTUAL</b>. El presente contrato se rige por lo dispuesto en el Título Décimo del Contrato de Prestación de Servicios, Capítulo I, del Código Civil del Estado de Chiapas y del Código Civil Federal en su precepto legal 1803, fracción I, por lo que no existe relación de dependencia ni de subordinación entre <b>“ICATECH”</b> y <b>“PRESTADOR DE SERVICIOS”</b>, ni podrán tenerse como tales los necesarios nexos de coordinación entre uno y otro.</dd>
            <br><dd>El presente contrato constituye el acuerdo de voluntades entre las partes, en relación con el objeto del mismo y deja sin efecto cualquier otra negociación o comunicación entre éstas, ya sea oral o escrita con anterioridad a la fecha de su firma.</dd>
            <br><dd><b>DÉCIMA SEGUNDA</b>.- Manifiestan ambas partes bajo protesta de decir verdad que en el presente contrato no ha mediado dolo, error, mala fe, engaño, violencia, intimidación, ni cualquiera otra causa que pudiera invalidar el contenido y fuerza.</dd>
            <br><dd><b>DÉCIMA TERCERA.- DOMICILIOS</b>. Para los efectos del presente instrumento las partes señalan como sus domicilios legales los estipulados en el Apartado de Declaraciones del presente instrumento legal.</dd>
            <br><dd>Mientras las partes no notifiquen por escrito el cambio de su domicilio, los emplazamientos y demás diligencias judiciales y extrajudiciales, se practicarán en el domicilio señalado en esta cláusula.</dd>
            <br><dd><b>DÉCIMA CUARTA.- RESPONSABILIDAD DEL “PRESTADOR DE SERVICIOS”</b>. Será el responsable de la ejecución de los trabajos y deberá sujetarse en la realización de éstos, a todos aquellos reglamentos administrativos y manuales que las autoridades competentes hayan emitido, así como a las disposiciones establecidas por <b>“ICATECH”</b>.</dd>
            <br><dd><b>DÉCIMA QUINTA</b>.- Las partes convienen que los datos personales insertos en el presente instrumento legal son protegidos por la Ley de Protección de Datos Personales en Posesión de Sujetos Obligados del Estado de Chiapas y la Ley de Transparencia y Acceso a la Información Pública del Estado de Chiapas, así como los Lineamientos Generales de la Custodia y Protección de Datos Personales e Información Reservada y Confidencial en Posesión de los Sujetos Obligados del Estado de Chiapas y demás normatividad aplicable.</dd>
            <br><dd><b>DÉCIMA SEXTA.- JURISDICCIÓN</b>. Para la interpretación y cumplimiento del presente Contrato, así como para todo aquello que no esté expresamente estipulado en el mismo, las partes se someterán a la jurisdicción y competencia de los tribunales del fuero común de la ciudad de Tuxtla Gutiérrez, Chiapas, renunciando al fuero que pudiera corresponderles por razón de su domicilio presente o futuro.</dd>
            <br><dd>Leído que fue el presente contrato a las partes que en él intervienen y una vez enterados de su contenido y alcance legal, son conformes con los términos del mismo y lo suscriben el día de su inicio y ratifican para constancia en el Municipio de '. 'TUXTLA GUTIÉRREZ'. ', Chiapas.</dd>
        </div>';

        return $bodyHtml;

    }

    private function incapacidad($incapacidad, $incapacitado)
    {
        $fechaActual = now();
        if(!is_null($incapacidad->fecha_inicio)) {
            $fechaInicio = \Carbon::parse($incapacidad->fecha_inicio);
            $fechaTermino = \Carbon::parse($incapacidad->fecha_termino)->endOfDay();
            if ($fechaActual->between($fechaInicio, $fechaTermino)) {
                // La fecha de hoy está dentro del rango
                $firmanteIncapacidad = \DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
                    ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                    ->Where('fun.id', $incapacidad->id_firmante)
                    ->First();

                return($firmanteIncapacidad);
            } else {
                // La fecha de hoy NO está dentro del rango
                if($fechaTermino->isPast()) {
                    $newIncapacidadHistory = 'Ini:'.$incapacidad->fecha_inicio.'/Fin:'.$incapacidad->fecha_termino.'/IdFun:'.$incapacidad->id_firmante;
                    array_push($incapacidad->historial, $newIncapacidadHistory);
                    $incapacidad->fecha_inicio = $incapacidad->fecha_termino = $incapacidad->id_firmante = null;
                    $incapacidad = json_encode($incapacidad);

                    \DB::Table('tbl_funcionarios')->Where('nombre',$incapacitado)
                        ->Update([
                            'incapacidad' => $incapacidad
                    ]);
                }

                return false;
            }
        }
        return false;
    }
}
