<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SOLICITUD EXONERACIÓN {{$mexoneracion}}</title>
    <style>
        @page {
            margin: 40px 30px 10px 30px;
        }
        body {
            /*margin: 3cm 2cm 2cm;*/
            margin-top: 120px;
            font-family: sans-serif; font-size: 10px;
        }
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 4cm;
            text-align: center;
            /*line-height: 5px;*/
        }
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
            text-align: center;
            line-height: 35px;
        }
        img.izquierda {float: left;width: 200px;height: 60px;}
        img.derecha {float: right;width: 200px;height: 60px;}
        .tb {width: 100%; border-collapse: collapse; text-align: center;}
        .tb tr, .tb td, .tb th{ border: black 1px solid; padding: 1px;}
        .tb thead{background: #EAECEE;}
        .page-break {
            page-break-after: always;
        }
        #watermark {
            position: fixed;

            /** 
                Establece una posición en la página para tu imagen
                Esto debería centrarlo verticalmente
            **/
            bottom:   .1cm;
            left:     .1cm;

            /** Cambiar las dimensiones de la imagen **/
            width:    26cm;
            height:   20cm;

            /** Tu marca de agua debe estar detrás de cada contenido **/
            z-index:  -1000;
        }
        .fa-arrow-right:before {
            font-family: DejaVu Sans;
            content: "\2192";
            color:black;
            font-size:10px;
        }
    </style>
</head>
<body>
    <header>
        <img class="izquierda" src='img/logohorizontalica1.png'>
        <img class="derecha" src='img/chiapas.png'>
        <div style="clear: both;">
            <font size="1" align="center"><b>{{$distintivo}}</b></font>
        </div>
        <table style="text-align: right; border-collapse: collapse;" align="right">
            <tr>
                <td><b>Unidad de Capacitación {{$reg_unidad->ubicacion}}.</b></td> 
            </tr>
            <tr>
                <td>@if ($marca) {{ "Revisión No. "}} @else {{ "Memorándum No. "}} @endif{{$mexoneracion}}.</td>
            </tr>
            <tr>
                <td>{{$reg_unidad->municipio}}, Chis., {{$date}}.</td>
            </tr>
        </table>
    </header>
    @if ($marca)
        <div id="watermark">
            <img src="img/borrador.jfif" height="100%" width="100%" />
        </div>  
    @endif
    <main>
        <div class="container">
            <table style="border-collapse: collapse;">
                <tr>
                    <td><b>{{$reg_unidad->dgeneral}}.</b></td>
                </tr>
                <tr>
                    <td><b>Representante Legal del ICATECH.</b></td>
                </tr>
                <tr>
                    <td><b>Presente.</b></td>
                </tr>
            </table>
            <br>
            <div>Conforme a las atribuciones que me confiere el artículo 29 fracción II y X del Reglamento Interior de nuestro Instituto, me 
                permito solicitarle la @if ($cursos[0]->tipo_exoneracion=='EXO') {{"Exoneración"}} @else {{"Reducción de Cuota de Recuperación"}} @endif
                sobre la cuota de recuperación, derivado de la solicitud de {{$depen}},
                que será(n) atendido(s) con el(los) siguiente(s) curso(s) de capacitación, conforme a lo siguiente:
            </div>
            <br>
            <table class="tb">
                <thead>
                    <tr>
                        <th rowspan="2">SERVICIO</th>
                        <th rowspan="2">UNIDAD DE CAPACITACIÓN Y/O ACCIÓN MÓVIL</th> 
                        <th rowspan="2">NOMBRE DEL CURSO</th>
                        <th rowspan="2">COSTO</th>
                        <th rowspan="2">HORAS</th>
                        <th rowspan="2">FECHA INICIO</th>       
                        <th rowspan="2">FECHA TERMINO</th>
                        <th rowspan="2">CUPO</th>
                        <th colspan="2">SEXO</th>
                        <th colspan="2">FOLIAJE EXONERACION</th>
                        <th rowspan="2">INSTRUCTOR</th> 
                        <th rowspan="2">EXO.</th>
                        <th rowspan="2">REDU.</th>
                        <th colspan="3">DEPENDENCIA O GRUPO BENEFICIADO</th>
                    </tr>
                    <tr>
                        <th>F</th>
                        <th>M</th>
                        <th>DEL</th>
                        <th>AL</th>
                        <th>No. CONVENIO / No. OFICIO DE SOLICITUD</th>
                        <th>RAZÓN DE LA EXONERACIÓN</th>
                        <th>OBSERVACIONES.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cursos as $item)
                    @php
                        $total = $item->hombre + $item->mujer;
                    @endphp
                    <tr>
                        <td>{{$item->tipo_curso}}</td>
                        <td>{{$item->unidad}}</td>
                        <td>{{$item->curso}}</td>
                        <td>{{$item->costo}}</td>
                        <td>{{$item->dura}}</td>
                        <td>{{$item->inicio}}</td>
                        <td>{{$item->termino}}</td>
                        <td>{{$total}}</td>
                        <td>{{$item->mujer}}</td>
                        <td>{{$item->hombre}}</td>
                        <td>{{$item->fini}}</td>
                        <td>{{$item->ffin}}</td>
                        <td>{{$item->instructor}}</td>
                        <td >@if ($item->tipo_exoneracion == 'EXO') {{"X"}}  @endif</td>
                        <td >@if ($item->tipo_exoneracion == 'EPAR') {{"X"}} @endif</td>
                        <td>@if ($item->no_convenio) {{$item->no_convenio}} @else {{$item->noficio}} <br> {{$item->foficio}} @endif</td>
                        <td>{{$item->razon_exoneracion}}</td>
                        <td>{{$item->observaciones}}</td>
                    </tr>
                    @endforeach 
                </tbody>                   
            </table>
            <br>
            <table style="width: 100%; border-collapse: collapse; border: black 1px solid;">
                <tr>
                    <td>GLOSARIO: </td>
                    <td>EXO <span class="fa-arrow-right"></span> EXONERACIÓN</td>
                    <td colspan="2">REDU <span class="fa-arrow-right"></span> REDUCCIÓN DE PAGO</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5">Razón de la exoneración.</td>
                </tr>
                <tr>
                    <td>MS <span class="fa-arrow-right"></span> MADRES SOLTERAS</td>
                    <td>AM <span class="fa-arrow-right"></span> ADULTOS MAYORES</td>
                    <td>BR <span class="fa-arrow-right"></span> BAJOS RECURSOS</td>
                    <td>D <span class="fa-arrow-right"></span> DISCAPACITADOS</td>
                    <td>PPL <span class="fa-arrow-right"></span> PERSONAS PRIVADAS DE LA LIBERTAD</td>
                </tr>
                <tr>
                    <td>GRS <span class="fa-arrow-right"></span> GRUPOS DE REINSERCIÓN SOCIAL</td>
                    <td colspan="4">O <span class="fa-arrow-right"></span> OTRO</td>
                </tr>
            </table>
            <br>
            <div>Lo anterior, con la finalidad de atender grupos en situación de vulnerabilidad que por sus características presentan desventaja por sexo, estado civil,
                 nivel educativo, origen étnico, situación o condición física y/o mental y requieren de un esfuerzo adicional para incorporarse al desarrollo 
                y a la convivencia, como  lo señala el artículo 32 de la Ley General de Educación y artículo 3, fracción IV, del Decreto de Creación del Instituto 
                de Capacitación y Vinculación Tecnológica del Estado de Chiapas. <br>
                Considerando que los costos de los cursos que imparte esta Institución, varían de los $100.00 (Cien pesos 00/100 M.N.) a los $1,200.00 (Mil Doscientos 00/100 M.N.); 
                según su tipo Oficios, Profesionalización, Especialidad y Salud, así como su clasificación Básico, Medio o Avanzado y duración total de horas de capacitación. <br>
                Se anexa(n) lista de alumnos y solicitud de la parte interesada. <br>
                Atentamente.
            </div>
            <table style="width: 100%; text-align: center; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0px;">
                        <div></div>
                        <div style="border: black 1px solid;">
                            <b>Elabora</b><br><br>
                            ________________________________________________ <br>
                            <b>{{$reg_unidad->vinculacion}}</b><br>
                            Titular del Departamento de Vinculación de la Unidad de Capacitación {{$reg_unidad->ubicacion}}
                        </div>
                        <div></div>
                    </td>
                    <td>
                        <div style="width: 30px;"> </div>
                    </td>
                    <td style="padding: 0px;">
                        <div></div>
                        <div style="border: black 1px solid;">
                            <b>Valida</b><br><br>
                            ________________________________________________ <br>
                            <b>{{$reg_unidad->dunidad}}</b> <br>
                            Titular de la Dirección de la Unidad de Capacitación <br> {{$reg_unidad->ubicacion}}
                        </div>
                        <div></div>                        
                    </td>
                    <td>
                        <div style="width: 30px;"> </div>
                    </td>
                    <td style="padding: 0px;">
                        <div style="border: black 1px solid;">
                            <b>Con fundamento en el artículo 13</b><br>
                            <b>Fracciones XIII y XXI, del Reglamento Interno</b><br>
                            <b>Autoriza</b><br><br>
                            ________________________________________________ <br>
                            <b>{{$reg_unidad->dgeneral}}.</b> <br>
                            Representante Legal del ICATECH
                        </div>
                        <div style="border: black 1px solid;">
                            <b>Vo.Bo.</b><br><br>
                            ________________________________________________ <br>
                            <b>{{$reg_unidad->dacademico}}</b> <br>
                            Director(a) de Técnica Académica
                        </div>
                    </td>
                </tr>
            </table>
            <div class="page-break"></div>
            <div style="width: 100%; text-align: center;">
                <font size="1" align="center"><b>ANEXO(S)</b></font>
            </div><br>
            @foreach ($data as $item)
            @php
                $cons = 1;
            @endphp
            <table class="tb">
                <thead>
                    <tr>
                        <th colspan="7">DATOS GENERALES DEL CURSO DE CAPACITACIÓN O CERTIFICACIÓN EXTRAORDINARIA</th>
                    </tr>
                    <tr>
                        <th>NOMBRE DEL CURSO</th>
                        <th colspan="3">{{$item['curso']}}</th>
                        <th colspan="2">MODALIDAD</th>
                        <th>{{$item['mod']}}</th>
                    </tr>
                    <tr>
                        <th>DURACIÓN EN HORAS</th>
                        <th>{{$item['dura']}}</th>
                        <th colspan="2">HORARIO</th>
                        <th colspan="3">{{$item['horario']}}</th>
                    </tr>
                    <tr>
                        <th>FECHA DE INICIO</th>
                        <th>{{$item['inicio']}}</th>
                        <th colspan="2">FECHA DE TERMINO</th>
                        <th colspan="3">{{$item['termino']}}</th>
                    </tr>
                    <tr>
                        <th>LUGAR DE CAPACITACIÓN</th>
                        <th colspan="6">{{$item['lugar']}}</th>
                    </tr>
                    <tr>
                        <th>DIAS</th>
                        <th colspan="6">{{$item['dias']}}</th>
                    </tr>
                    <tr>
                        <th>NOMBRE DEL INSTRUCTOR</th>
                        <th colspan="6">{{$item['instructor']}}</th>
                    </tr>
                    <tr>
                        <th colspan="7">LISTA DE ALUMNOS</th>
                    </tr>
                    <tr>
                        <th>N°</th>
                        <th>APELLIDO PATERNO</th>
                        <th>APELLIDO MATERNO</th>
                        <th>NOMBRE(S)</th>
                        <th>SEXO</th>
                        <th>EDAD</th>
                        <th>CUOTA DE RECUPERACIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['alumnos'] as $value)
                    <tr>
                        <td>{{$cons++}}</td>
                        <td>{{$value->apellido_paterno}}</td>
                        <td>{{$value->apellido_materno}}</td>
                        <td>{{$value->nombre}}</td>
                        <td>@if ($value->sexo=='MASCULINO') {{"M"}} @else {{"F"}} @endif</td>
                        <td>{{$value->edad}}</td>
                        <td>{{$value->costo}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endforeach
        </div>
    </main>
    <footer>
        {{--  <p><strong></strong></p>  --}}
    </footer>
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(50, 570, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script>
</body>
</html>