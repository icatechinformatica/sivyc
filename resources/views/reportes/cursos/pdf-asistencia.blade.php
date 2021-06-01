<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
<html>
<head>
     <style>
        body{font-family: sans-serif;}
        @page { margin: 100px 25px 170px 25px; }
        header { position: fixed; left: 0px; top: -80px; right: 0px; text-align: center;}        
        header h6{ height:0; line-height: 14px; padding: 8px; margin: 0;}
        table #curso{ font-size: 8px; padding: 10px; line-height: 18px; text-align: justify;}
        main {padding: 0; margin: 0; margin-top: 0px; }
     
        .tabla{  border-collapse: collapse; width: 100%; }        
        .tabla tr td, .tabla tr th{ font-size: 8px; border: gray 1px solid; text-align: center; padding: 3px;}
        .tab{ margin-left: 10px; margin-right: 50px;}
        .tab1{ margin-left: 15px; margin-right: 50px; }
        .tab2{ margin-left: 5px; margin-right: 20px;}   
        
        footer { position:fixed;   left:0px;   bottom:-170px;   height:150px;   width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        
        .tablaf { border-collapse: collapse; width: 100%;}     
        .tablaf tr td { font-size: 9px; text-align: center; padding: 3px;}
        
        .tab{ margin-left: 20px; margin-right: 50px;}
        .tab1{ margin-left: 10px; margin-right: 20px; }
        .tab2{ margin-left: 10px; margin-right: 60px;} 
     </style>
</head>
<body>

     <header>
            <img src="img/reportes/sep.png" alt='sep' width="12%" style='position:fixed; left:0; margin: -70px 0 0 20px;' />
            <h6>SUBSECRETAR&Iacute;A DE EDUCACI&Oacute;N E INVESTIGACI&Oacute;N TECNOL&Oacute;GICAS</h6>
            <h6>DIRECCI&Oacute;N GENERAL DE CENTROS DE FORMACI&Oacute;N PARA EL TRABAJO</h6>
            <h6>LISTA DE ASISTENCIA</h6>
            <h6>(LAD-04)</h6> 
           
     </header>

     <footer>
            <table class="tablaf"  width="100%">       
                <tbody>                
                    <tr>
                        <td width="10%">&nbsp; </td>
                        <td width="25%" >                       
                            <br /><br /><br /><br /><br /><br />                                                
                            {{ $curso->nombre }}                                               
                            <hr width="280px" />
                            NOMBRE Y FIRMA DEL INSTRUCTOR
                            <br /><br /><br />                                                                        
                        </td>
                        <td width="25%">&nbsp;</td>
                        <td width="15%"> 
                            <br /><br /><br /><br /><br /><br />           
                            <hr width="120px" />
                            SELLO 
                            <br /><br /><br />   
                        </td>
                        <td width="15%">&nbsp;</td>                                                            
                    </tr>
                </tbody>
           </table>                          
     </footer> 
     <main>   
     @if(isset($Ym))         
         @for($n=0;$n<count($Ym);$n++) 
            <?php  
              $m = explode("-",$Ym[$n]);  
              $consec=1;              
            ?>
            
            <table class="tabla">
                <thead>
                    <tr>
                        <td colspan="36">
                             <div id="curso">                
                                UNIDAD DE CAPACITACI&Oacute;N: <span class="tab">{{$curso->plantel}} {{ $curso->unidad }}</span>
                                CLAVE CCT: <span class="tab">{{ $curso->cct }}</span>
                                CICLO ESCOLAR: <span class="tab">{{ $curso->ciclo }}</span>
                                GRUPO: <span class="tab">{{ $curso->grupo }}</span>              
                                MES: <span class="tab">@if(isset($mes[$m[1]])){{ $mes[$m[1]] }}@endif</span>
                                A&Ntilde;O: &nbsp;&nbsp;{{ $m[0] }}
                                <br />
                                AREA: <span class="tab1">{{ $curso->area }}</span>                
                                ESPECIALIDAD: <span class="tab1">{{ $curso->espe }}</span>                                
                                CURSO: <span class="tab1"> {{ $curso->curso }}</span>
                                CLAVE: &nbsp;&nbsp; {{ $curso->clave }}                
                                <br />
                                FECHA INICIO: <span class="tab1"> {{ $curso->fechaini }}</span>
                                FECHA TERMINO: <span class="tab1"> {{ $curso->fechafin }}</span>
                                HORARIO: <span class="tab2"> {{ $curso->dia}} DE {{ $curso->hini }} A {{ $curso->hfin }}</span>                
                                CURP: &nbsp;&nbsp;{{ $curso->curp}}                    
                            </div>
                            
                        </td>
                    </tr>
                    <tr>
                        <th colspan="36" style="border-left: white; border-right: white;"></th>                
                    </tr>
                    <tr>
                        <th width="15px" rowspan="2">N<br/>U<br/>M</th>
                        <th width="100px" rowspan="2">N&Uacute;MERO DE <br/>CONTROL</th>
                        <th width="280px">NOMBRE DEL ALUMNO</th>
                        @for($i=1;$i<=31;$i++)
                            <th width="10px" rowspan="2"><b>{{ $i }}</b></th>
                        @endfor
                        <th colspan="2"><b>TOTAL</b></th>                                        
                    </tr>               
                    <tr>                    
                        <th>PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</th> 
                        <th> A </th>
                        <th> I </th>                  
                    </tr>                
                </thead>
                <tbody>   
                @foreach($alumnos as $a)         
                    <tr>
                        <td>{{ $consec++ }}</td>
                        <td>{{ $a->matricula }}</td>
                        <td>{{ $a->alumno }}</td>
                        @for($i=1;$i<=31;$i++)
                            <td></td>                    
                        @endfor
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr> 
                    @endforeach
                </tbody> 
                <tfoot>            
                </tfoot>          
            </table> 
            @if($n<count($Ym)-1)<p style="page-break-before: always;"></p>@endif
        @endfor
     @else 
            {{ "El Curso no tiene registrado la fecha de inicio y de termino" }}
     @endif
</body>
</html>
