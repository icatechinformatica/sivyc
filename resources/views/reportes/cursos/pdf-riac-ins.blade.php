<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
<html>
<head>
     <style>
        body{font-family: sans-serif;}
        @page { margin: 200px 25px 180px 25px; }
        header { position: fixed; left: 0px; top: -190px; right: 0px; height: 190px; text-align: center; }
        header h6{ height:0; line-height: 14px; padding: 8px; margin: 0;}
        header #tipo{ margin-top: 10px; text-align: center; font-size: 10px;}
        header #curso{ margin-top: 8px; font-size: 8px; border: 1px solid gray; padding: 8px; line-height: 18px;}
        footer { position: fixed; left: 0px; right: 0px; height: 600px; top: 0px;}
        footer .page:after { content: counter(page, sans-serif);}

        .cuadro{ border: 1px solid black; width: 50px; padding: 10px;}
        #curso {text-align: left; }
        #curso b{margin-left: 10px; margin-right: 50px;}
        .tabla { border-collapse: collapse; width: 100%;}
        .tabla tr td, .tabla tr th{ font-size: 8px; border: gray 1px solid; text-align: center; padding: 3px;}
        .tab{ margin-left: 10px; margin-right: 50px;}
        .tab1{ margin-left: 10px; margin-right:28px; }
        .tab2{ margin-left: 3px; margin-right: 0px;}

     </style>
</head>
<body>
     <header>
            <img src="img/reportes/sep.png" alt='sep' width="12%" style='position:fixed; left:0; margin: -170px 0 0 20px;' />
            <h6>SUBSECRETAR&Iacute;A DE EDUCACI&Oacute;N E INVESTIGACI&Oacute;N TECNOL&Oacute;GICAS</h6>
            <h6>DIRECCI&Oacute;N GENERAL DE CENTROS DE FORMACI&Oacute;N PARA EL TRABAJO</h6>
            <h6>REGISTRO DE INSCRIPCI&Oacute;N, ACREDITACI&Oacute;N Y CERTIFICACI&Oacute;N</h6>
            <h6>(RIACD-02)</h6>
            <div id="tipo">
                EXT: <span class="cuadro">&nbsp;&nbsp;@if($curso->mod=="EXT"){{"X"}}@else{{" "}}@endif&nbsp;&nbsp;</span>&nbsp;
                CAE: <span class="cuadro">&nbsp;&nbsp;@if($curso->mod=="CAE"){{"X"}}@else{{" "}}@endif&nbsp;&nbsp;</span>&nbsp;
                REG: <span class="cuadro">&nbsp;&nbsp;@if($curso->mod=="REG"){{"X"}}@else{{" "}}@endif&nbsp;&nbsp;&nbsp;</span>&nbsp;
                EMP: <span class="cuadro">&nbsp;&nbsp;@if($curso->mod=="EMP"){{"X"}}@else{{" "}}@endif&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div id="curso">
                INSTITUTO DESCENTRALIZADO: <b>INSTITUTO DE CAPACITACI&Oacute;N Y VINCULACI&Oacute;N TECNOL&Oacute;GICA DEL ESTADO DE CHIAPAS</b>
                UNIDAD DE CAPACITACI&Oacute;N: <span class="tab">{{$curso->plantel}} {{ $curso->unidad }}</span>
                CLAVE CCT: <span class="tab">{{ $curso->cct }}</span>
                <br />
                AREA: <span class="tab1">{{ $curso->area }}</span>
                ESPECIALIDAD: <span class="tab1">{{ $curso->espe }}</span>
                CURSO: <span class="tab1">{{ $curso->curso }}</span>
                CLAVE: &nbsp;&nbsp;{{ $curso->clave }}
                <br />
                CICLO ESCOLAR: <span class="tab2">{{ $curso->ciclo }}</span>
                PERIODO: <span class="tab2">@if(isset($periodo[$curso->mes_termino])){{ $periodo[$curso->mes_termino] }}@endif</span>
                FECHA INICIO: <span class="tab2"> {{ $curso->fechaini }}</span>
                FECHA TERMINO: <span class="tab2"> {{ $curso->fechafin }}</span>
                DURACI&Oacute;N EN HORAS: <span class="tab2">{{ $curso->dura }}</span>
                GRUPO: <span class="tab2">{{ $curso->grupo }}</span>
                HORARIO:<span class="tab2">{{ $curso->hini }} A {{ $curso->hfin }}</span>
                CURP: &nbsp;&nbsp;{{ $curso->curp}}
                CONVENIO REALIZADO CON: <span class="tab1">{{ $curso->depen}}</span>
            </div>
     </header>
     <footer>
        <table class="tabla" width="100%" >
            <tbody>
                <tr>
                    <th height="440px" colspan="2" style="border-bottom: white;">&nbsp;</th>
                    <th height="440px" colspan="4" style="border-bottom: white;">&nbsp;</th>
                </tr>
                <tr>
                    <th colspan="2" style="border-bottom: white;">INSCRIPCI&Oacute;N</th>
                    <th colspan="4" style="border-bottom: white;">ACREDITACI&Oacute;N / CERTIFICACI&Oacute;N</th>
                </tr>
                <tr>
                    <td width="300px" style=" border-right: white;">
                        <br /><br /><br /><br /><br /><br />
                        C. {{ $curso->dunidad }}
                        <hr width="250px" />
                        NOMBRE Y FIRMA DEL {{ $curso->pdunidad }}
                        <br /><br /><br />
                    </td>
                    <td width="92px"><br /><br /><br /><br /><br /><br />SELLO </td>
                    <td style=" border-right: white;">
                        <br /><br /><br /><br /><br /><br />
                        C. {{ $curso->dunidad }}
                        <hr width="220px" />
                        NOMBRE Y FIRMA DEL {{ $curso->pdunidad }}
                        <br /><br /><br />
                    </td>
                     <td width="50px" style=" border-right: white;"><br /><br /><br /><br /><br /><br />SELLO </td>
                     <td style=" border-right: white;">
                        <br /><br /><br /><br /><br /><br />
                        C. {{ $curso->dgeneral }}
                        <hr width="220px" />
                        NOMBRE Y FIRMA DEL {{ $curso->pdgeneral }}
                        <br /><br /><br />
                    </td>
                     <td width="55px"><br /><br /><br /><br /><br /><br />SELLO </td>
                </tr>
            </tbody>
       </table>

     </footer>
     <div id="content">
        <table class="tabla">
            <thead>
                <tr>
                    <th width="15px" rowspan="3">N<br/>U<br/>M</th>
                    <th width="70px" rowspan="3">N&Uacute;MERO DE <br/>CONTROL</th>
                    <th width="300px">NOMBRE DEL ALUMNO</th>
                    <th colspan="6"><b>INSCRIPCI&Oacute;N</b></th>
                    <th colspan="3"><b>ACREDITACI&Oacute;N</b></th>
                    <th colspan="2"><b>CERTIFICACI&Oacute;N</b></th>
                </tr>
                <tr>
                    <th rowspan="2">PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</th>
                    <th colspan="2">TIPO DE ALUMNO</th>
                    <th rowspan="2">TIPO DE DISCAP.</th>
                    <th rowspan="2">SEXO</th>
                    <th rowspan="2">EDAD</th>
                    <th rowspan="2">ESCOLA<br/>RIDAD</th>
                    <th rowspan="2">ACRED<br/>ITADO</th>
                    <th rowspan="2">POR ACRED.</th>
                    <th rowspan="2">DESER<br/>CI&Oacute;N</th>
                    <th rowspan="2">FOLIO DEL DIPLOMA</th>
                    <th rowspan="2" width="80px">FOLIO DE LA CONSTANCIA</th>
                </tr>
                <tr>
                    <th>INS. IND</th>
                    <th>BECADOS</th>
                </tr>
            </thead>
            <tbody>
            @foreach($alumnos as $a)
                <tr>
                    <td>{{ $consec++ }}</td>
                    <td>{{ $a->matricula }}</td>
                    <td>{{ $a->alumno }}</td>
                    <td>@if($a->abrinscri!="ET" AND $a->abrinscri!="EP"){{ "X" }}@endif</td>
                    <td>@if($a->abrinscri=="ET" OR $a->abrinscri=="EP"){{ "X" }}@endif</td>
                    @if ($a->id_gvulnerable &&(in_array(18, json_decode($a->id_gvulnerable))||in_array(19, json_decode($a->id_gvulnerable))||
                    in_array(20, json_decode($a->id_gvulnerable))||in_array(21, json_decode($a->id_gvulnerable))||in_array(22, json_decode($a->id_gvulnerable))))
                      @switch($a->id_gvulnerable)
                          @case(in_array(18, json_decode($a->id_gvulnerable)))
                              <td>{{"1"}}</td>
                              @break
                          @case(in_array(19, json_decode($a->id_gvulnerable)))
                              <td>{{"2"}}</td>
                              @break
                          @case(in_array(20, json_decode($a->id_gvulnerable)))
                                <td>{{"3"}}</td>
                              @break
                          @case(in_array(21, json_decode($a->id_gvulnerable)))
                                <td>{{"4"}}</td>
                              @break
                          @case(in_array(22, json_decode($a->id_gvulnerable)))
                                <td>{{"5"}}</td>
                              @break
                          @default
                          <td style="color: red;"><b>{{ "DATO REQUERIDO"}}</b></td>
                      @endswitch
                    @else
                        <td>{{"6"}}</td>
                    @endif
                   {{-- @if(isset($discapacidad[$a->discapacidad]))<td>{{ $discapacidad[$a->discapacidad] }}</td>
                    @else <td style="color: red;"><b>{{ "DATO REQUERIDO"}}</b></td> @endif--}}
                   @if($a->sexo)<td>{{ $a->sexo }}</td>
                    @else <td style="color: red;"><b>{{ "DATO REQUERIDO"}}</b></td> @endif
                    @if($a->edad )<td>{{ $a->edad  }}</td>
                    @else <td style="color: red;"><b>{{ "DATO REQUERIDO"}}</b></td> @endif
                    @if(isset($escolaridad[$a->escolaridad]))<td> {{ $escolaridad[$a->escolaridad] }}</td>
                    @else <td style="color: red;"><b>{{ "DATO REQUERIDO"}}</b></td> @endif
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>

     </div>
</body>
</html>
