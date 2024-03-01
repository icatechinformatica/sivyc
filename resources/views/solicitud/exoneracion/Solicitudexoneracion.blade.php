@extends('theme.formatos.hlayout')
@section('title', 'Solicitud de Exoneración | SIVyC Icatech')
@section('css')
    <style>    
        @page { margin-bottom: 125px; }    
        body {
            margin-top: 150px;
            
        }
        
        .tb {width: 100%; border-collapse: collapse; text-align: center; font-size: 8px;}
        .tb tr, .tb td, .tb th{ border: black 1px solid; padding: 1px;}
        .tb thead{background: #EAECEE; width: 100%; }
        .page-break {
            page-break-after: always;
        }

        .fa-arrow-right:before {
            font-family: DejaVu Sans;
            content: "\2192";
            color:black;
            font-size:10px;
        }
        .container{ margin-top: -50px; font-family: sans-serif; font-size: 12px;}        
    </style>    
@endsection
@section('header') 
    <table style="text-align: right; border-collapse: collapse; font-family: sans-serif; font-size: 12px;" align="right">
        <tr><td>Unidad de Capacitación {{$reg_unidad->ubicacion}}.</td></tr>
        <tr><td style="font-weight:normal;">@if ($marca) {{ "Revisión No. "}} @else {{ "Memorándum No. "}} @endif{{$mexoneracion}}.</td></tr>
        <tr><td style="font-weight:normal;">{{$reg_unidad->municipio}}, Chiapas; {{$fecha}}.</td></tr>
    </table>    
@endsection
@section('body')   
    <main>
        <div class="container">
            <table style="border-collapse: collapse;">
                <tr>
                    <td><b>{{$reg_unidad->dgeneral}}.</b></td>
                </tr>
                <tr>
                    <td><b>Director(a) General del ICATECH.</b></td>
                </tr>
                <tr>
                    <td><b>Presente.</b></td>
                </tr>
            </table>
            
            <div style="text-align: justify; padding-top:7px; margin-bottom:7px;">Conforme a las atribuciones que me confiere el artículo 42 fracción II, IV y V del Reglamento Interior de nuestro Instituto, me 
                permito solicitarle la @if ($cursos[0]->tipo_exoneracion=='EXO') {{"Exoneración"}} @else {{"Reducción de Cuota de Recuperación"}} @endif
                , derivado de la solicitud del @if ($depen=='CAPACITACION ABIERTA') {{"(los) grupo(s) de CAPACITACION ABIERTA"}} @else {{$depen}} @endif
                que será(n) atendido(s) con el(los) siguiente(s) curso(s) de capacitación, conforme a lo siguiente:
            </div>
            
            <table class="tb">
                <thead>
                    <tr>
                        <th rowspan="2">CURSO /<br/>CERTIFICACIÓN</th>
                        <th rowspan="2">UNIDAD DE CAPACITACIÓN Y/O ACCIÓN MÓVIL</th> 
                        <th rowspan="2">NOMBRE DEL CURSO</th>
                        <th rowspan="2">MOD</th>
                        <th rowspan="2">COSTO</th>
                        <th rowspan="2">HORAS</th>
                        <th rowspan="2">FECHA INICIO</th>       
                        <th rowspan="2">FECHA TERMINO</th>
                        <th rowspan="2">CUPO</th>
                        <th colspan="2">SEXO</th>
                        <th colspan="2">FOLIAJE EXONERACION</th>                        
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
                        <td>{{$item->mod}}</td>
                        <td>{{$item->costo}}</td>
                        <td>{{$item->dura}}</td>
                        <td>{{$item->inicio}}</td>
                        <td>{{$item->termino}}</td>
                        <td>{{$total}}</td>
                        <td>{{$item->mujer}}</td>
                        <td>{{$item->hombre}}</td>
                        <td>{{$item->fini}}</td>
                        <td>{{$item->ffin}}</td>                        
                        <td >@if ($item->tipo_exoneracion == 'EXO') {{"X"}}  @endif</td>
                        <td >@if ($item->tipo_exoneracion == 'EPAR') {{"X"}} @endif</td>
                        <td>@if ($item->no_convenio) {{$item->no_convenio}} @else {{$item->noficio}} <br> {{$item->foficio}} @endif</td>
                        <td>{{$item->razon_exoneracion}}</td>
                        <td>{{$item->observaciones}}</td>
                    </tr>                     
                    @endforeach 
                </tbody>                   
            </table>            
            <p style="page-break-inside: avoid;  width: 100%; border: black 1px solid; font-size: 8px; padding: 2px; margin-top:7px; ">
                GLOSARIO:<br>
                TIPO DE EXONERACIÓN:&nbsp;&nbsp;<b>EXO</b> <span class="fa-arrow-right"></span> EXONERACIÓN&nbsp;&nbsp;&nbsp;<b>REDU</b> <span class="fa-arrow-right"></span> REDUCCIÓN DE PAGO <br>
                RAZÓN DE LA EXONERACIÓN:&nbsp;&nbsp;<b>AM</b> <span class="fa-arrow-right"></span> ADULTOS MAYORES&nbsp;&nbsp;<b>BR</b> <span class="fa-arrow-right"></span> BAJOS RECURSOS&nbsp;&nbsp;<b>D</b> <span class="fa-arrow-right"></span> DISCAPACITADOS&nbsp;&nbsp;<b>MS</b> <span class="fa-arrow-right"></span> MADRES SOLTERAS&nbsp;&nbsp;<b>PPL</b> <span class="fa-arrow-right"></span> PERSONAS PRIVADAS DE LA LIBERTAD&nbsp;&nbsp;<b>GRS</b> <span class="fa-arrow-right"></span> GRUPOS DE REINSERCIÓN SOCIAL&nbsp;&nbsp;<b>O</b> <span class="fa-arrow-right"></span> OTRO
            </p>            
            <div style="page-break-inside: avoid; text-align: justify;">
                Lo anterior, con la finalidad de atender grupos en situación de vulnerabilidad que por sus características presentan desventaja por sexo, estado civil,
                 nivel educativo, origen étnico, situación o condición física y/o mental y requieren de un esfuerzo adicional para incorporarse al desarrollo 
                y a la convivencia, como  lo señala el artículo 32 de la Ley General de Educación y artículo 23, de los 
                lineamientos para los procesos de vinculación y capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.<br/>
                Considerando que los costos de los cursos que imparte esta Institución, varían de los $100.00 (Cien pesos 00/100 M.N.) a los $1,200.00 (Mil Doscientos 00/100 M.N.); 
                según su tipo Oficios, Profesionalización, Especialización y Salud, así como su clasificación Básico, Medio o Avanzado y duración total de horas de capacitación. <br>
                Se anexa(n) lista de alumnos y solicitud de la parte interesada. 
                <br/>
                Atentamente.
            </div>
            <table style="page-break-inside: avoid; width: 100%; text-align: center; border-collapse: collapse; font-size: 8px; margin-top:-10px;" >
                <tr>
                    <td style="padding: 0px;">
                        <div></div>
                        <div style="border: black 1px solid;">
                            <br><b>Elabora</b><br><br><br><br><br>
                            ________________________________________________ <br>
                            <b>{{$reg_unidad->vinculacion}}</b><br>
                            Titular del Departamento de Vinculación de la Unidad de Capacitación {{$reg_unidad->ubicacion}} <br>&nbsp;
                        </div>
                        <div></div>
                    </td>
                    <td>
                        <div style="width: 30px;"> </div>
                    </td>
                    <td style="padding: 0px;">
                        <div></div>
                        <div style="border: black 1px solid;">
                            <br><b>Valida</b><br><br><br><br><br>
                            ________________________________________________ <br>
                            <b>{{$reg_unidad->dunidad}}</b> <br>
                            Titular de la Dirección de la Unidad de Capacitación {{$reg_unidad->ubicacion}} <br>&nbsp;
                        </div>
                        <div></div>                        
                    </td>
                    <td>
                        <div style="width: 30px;"> </div>
                    </td>
                    <td style="padding: 0px;">
                        <div style="border: black 1px solid;"><br>
                            <b>Con fundamento en el artículo 13</b><br>
                            <b>Fracciones II y XXV del Reglamento Interior del Instituto de</b><br>
                            <b>Capacitación y Vinculación Tecnológica del Estado de Chiapas</b><br>
                            <b>Autoriza</b><br><br>
                            ________________________________________________ <br>
                            <b>{{$reg_unidad->dgeneral}}.</b> <br>
                            Director(a) General del ICATECH <br>&nbsp;
                        </div>
                        <div style="border: black 1px solid;"> <br>
                            <b>Vo.Bo.</b><br><br>
                            ________________________________________________ <br>
                            <b>{{$reg_unidad->dacademico}}</b> <br>
                            Director(a) de Técnica Académica <br>&nbsp;
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
                        <th colspan="3">{{$item['horario']}} hrs.</th>
                    </tr>
                    <tr>
                        <th>FECHA DE INICIO</th>
                        <th>{{$item['inicio']}}</th>
                        <th colspan="2">FECHA DE TERMINO</th>
                        <th colspan="3">{{$item['termino']}}</th>
                    </tr>
                    <tr>
                        @if ($item['tcapacitacion']=='PRESENCIAL')
                            <th>TIPO DE CAPACITACIÓN</th>
                            <th>PRESENCIAL</th>
                            <th colspan="2">LUGAR DE CAPACITACIÓN</th>
                            <th colspan="3" width="auto" >{{$item['lugar']}} </th>
                        @else
                        <th>TIPO DE CAPACITACIÓN</th>
                        <th colspan="6">A DISTANCIA</th>
                        @endif
                    </tr>
                    <tr>
                        <th>DIAS</th>
                        <th colspan="6">{{$item['dias']}}</th>
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
                        <td>{{$value->sexo}}</td>
                        <td>{{$value->edad}}</td>
                        <td>{{$value->costo}}</td>
                    </tr>
                    
                    @endforeach
                </tbody>
            </table>
            @endforeach
        </div>
    </main>

@endsection
@section('js')
<script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 540, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
@endsection