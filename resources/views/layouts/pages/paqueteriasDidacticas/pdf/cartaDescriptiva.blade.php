<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CARTA DESCRIPTIVA</title>
    <style>      
        body{font-family: sans-serif}
        @page {margin: 40px 30px 10px 30px;}
            header { position: fixed; left: 0px;  right: 0px; text-align: center;}
            header h1{height:0; line-height: 14px; padding: 9px; margin: 0;}
            header h2{margin-top: 20px; font-size: 8px; border: 1px solid gray; padding: 12px; line-height: 18px; text-align: justify;}
            footer {position:fixed;   left:0px;   bottom:0px;   height:150px;   width:100%;}
            footer .page:after { content: counter(page, sans-serif);}
            img.izquierda {float: right;width: 200px;height: 60px;}
            img.izquierdabot {position: absolute;left: 50px;width: 350px;height: 60px;}
            img.derechabot {position: absolute;right: 50px;width: 350px;height: 60px;}
            img.derecha {float: right;width: 200px;height: 60px;}
            img.centro {float: right; width: 200px; height: 60px; }
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr{font-style: Gotham; font-size: 10px; border: gray 1px solid;  padding: 1px 1px;}
        .tablas th{font-style: Gotham; font-size: 10px; border: gray 1px solid; text-align: center; padding: 1px 1px;}
        .tablas td{font-style: Gotham; font-size: 10px; border: gray 1px solid; text-align: left; padding: 1px 1px;}
        .tablaf { border-collapse: collapse; width: 100%; position: absolute; bottom:100px;}     
        .tablaf tr td { font-size: 8px; text-align: center; padding: 0px 0px; border: gray 1px solid;}

        
        .tablar tr td{  font-size: 12px; text-align: center; padding: 0px 0px; border: gray 1px solid;}


        .firma  { font-size: 8px; text-align: center; padding-top: 100px ; }
        .tablad { border-collapse: collapse;}     
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;}     
        .tablag tr td{ font-size: 8px; padding: 0px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        .marco {border-style: solid; padding-right: 80px; padding-left: 80px;}
        .page_break { page-break-before: always; }

        .encabezado{position: relative; top: 0; left: 0;}
        .img-fondo{position: relative; left: 0;}
        .logos{position: absolute; top: 30; left: 0;}
    </style>
</head>

 <header class="encabezado">
    <img class="img-fondo" src="img/headerpaqdid.jpeg" align="center">
    <div class="logos"">
        <div align=center>
            <img class="centro" src='img/icatech-imagen.png'>
        </div>
        <div align="right">
            <img class="derecha" src='img/instituto_oficial.png'>
        </div>
    </div>
</header>
<!-- <footer>
    <img src="img/footerpaqdid.jpg" style="background-image" align="center">
</footer>  -->
<!-- 
<div id="form_wrapper" style="background-image:url('img/yellow.png');">
    <img src="C:\Users\George\Documents\HTML\My Local Soccer\pics\icons\gradient box.gif" 
        style="position:absolute; top: 155px; left: 480px; width:auto; height:auto;">
    <input type="text" class="tb7" value="Username" style="margin-left: 563px" 
        maxlength="20" onfocus="if(this.value == 'Username') {this.value='';}" />         
    </br>
    <input type="text" class="tb8"  value="Password" style="margin-left: 563px;"   
        maxlength="20" onfocus="if(this.value =='Password') {this.value='';}"/>
    <input type="image" height="25px"   width="67px" style="vertical-align:top;"
        src="C:\Users\George\Documents\HTML\My Local Soccer\pics\icons\sign in.gif" />
</div> -->
<body>
    <div class= "container g-pt-30">
        <div id="content">
            
            
            
            <br>
            <br>
            <div >
                <div align=center><br> 
                   <h2>CARTA DESCRIPTIVA</h2>
                </div>
            </div>
            <br>
            
            <div class="table table-responsive">
                <table class="tablas">
                    <thead>
                        <tr>
                            <th colspan="10">DATOS GENERALES</th>
                        </tr>

                    </thead>
                    <tbody>
                        
                        <tr>
                            <td colspan="2" rowspan="2">Entidad Federativa: {{ $cartaDescriptiva->entidadfederativa }}</td>
                            <td colspan="2" rowspan="2">Ciclo Escolar: {{ $cartaDescriptiva->cicloescolar }}</td>
                            <td colspan="2" rowspan="2">Programa Estrategico (en caso aplicable): </td>
                            <td colspan="4">Modalidad</td>
                        </tr>
                        <tr>
                            <td>EXT</td>
                            <td>@if($cartaDescriptiva->modalidad == 'EXT') X @endif</td>
                            <td>CAE</td>
                            <td>@if($cartaDescriptiva->modalidad == 'CAE') X @endif</td>
                        </tr>
                        <tr>
                            <td colspan="5">Tipo:  &nbsp;&nbsp;&nbsp;&nbsp;  Presencial(@if($cartaDescriptiva->tipo== 'PRESENCIAL') X @endif)  &nbsp;&nbsp;&nbsp;&nbsp; A Distancia( @if($cartaDescriptiva->tipo == 'A DISTANCIA') X @endif)</td>
                            <td colspan="5">Especialidad: {{ $cartaDescriptiva->especialidad }}</td>
                        </tr>
                        <tr>
                            <td colspan="7">Nombre del curso: {{ $cartaDescriptiva->nombrecurso }} </td>
                            <td colspan="3">Duracion en horas: {{ $cartaDescriptiva->duracion }} Hrs</td>
                        </tr>
                        <tr>
                            <td colspan="5">Campo de Formación Profesional: {{ $cartaDescriptiva->formacionlaboral }}</td>
                            <td colspan="5">Especialidad: {{ $cartaDescriptiva->especialidad }} </td>
                        </tr>
                    
                        <tr>
                    
                            <td colspan="10">Aprendizaje esperado: <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->aprendizajeesperado));?>  </td>
                        </tr>
                    
                        <tr>
                            <td colspan="10">Objetivos especificos por tema: <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->objetivoespecifico));?>  </td>
                        </tr>
                        <tr>
                            <td colspan="10">Transversabilidad con otros cursos: {{ $cartaDescriptiva->transversabilidad }} </td>
                        </tr>
                        <tr>
                            <td colspan="10">Publico o personal al que va dirigido: {{ $cartaDescriptiva->publico }} </td>
                        </tr>
                        <tr>
                            <td colspan="10">Proceso de evaluacion: <br>
                            @foreach ($cartaDescriptiva->ponderacion as $ponderacion)
                            {{ $ponderacion->criterio }} &nbsp;&nbsp;.................... {{ $ponderacion->ponderacion }}% <br>
                            @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10">observaciones: <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->observaciones)); ?>  </td>
                        </tr>
                        <tr>
                            <td colspan="2">Contenido Tematico</td>
                            <td colspan="2">Estrategia Didactica</td>
                            <td colspan="2">Proceso de Evaluacion </td>
                            <td colspan="4">Duracion (EN HORAS)</td>
                        </tr>

                        @foreach ($cartaDescriptiva->contenidoTematico as $contenido)
                        <tr>
                            <td colspan="2">{{ $contenido->contenido }} </td>
                            <td colspan="2">{{ $contenido->estrategia }}</td>
                            <td colspan="2">{{ $contenido->proceso }} </td>
                            <td colspan="4">{{ $contenido->duracion }} </td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
                <br>
                <br>
                <table class="tablas">
                    <thead>
                        <tr>
                            <th colspan="10">RECURSOS DIDACTICOS</th>
                        </tr>

                    </thead>
                    <tbody>
                        
                        <tr>
                            <td colspan="3" rowspan="2">Elementos de Apoyo: </td>
                            <td colspan="4" rowspan="2">Auxiliares de la enseñanza: </td>
                            <td colspan="3" rowspan="2">Referencias: </td>
                        </tr>
                        @foreach($cartaDescriptiva->recursosDidacticos as $recursos)
                        <tr>
                            <td colspan="3">{{ $recursos->elementoapoyo }}</td>
                            <td colspan="4">{{ $recursos->auxenseñanza }}</td>
                            <td colspan="3">{{ $recursos->referencias }} </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
               
            </div> 
        </div>
    </div>


    <!--================================= end of page =================================-->
    <div class="page_break"></div>
    <!--================================= end of page =================================-->

    <div class= "container g-pt-30">
        <div id="content">
            
            <img class="derecha" src='img/chiapas.png'>
           
            <div id="wrappertop">
                <div align=center><br> 
                   <img class="izquierda" src='img/logohorizontalica1.png'>
                </div><br><br><br>
            </div>
            
            <br>
            <br>
            <div >
                <div align=center><br> 
                   <h2 style="color:#FCB6B5">EVALUACIÓN DE APRENDIZAJE AL ALUMNO</h2>
                </div>
                <label style="font-style: italic; text-align: left; ">(Estos datos deben ser llenados por el departamento académico de la Unidad)</label>
            </div>
            <br>
            
            <div class="table table-responsive">
                <table class="tablas">
                    <tbody>
                        <tr>
                            <td colspan="7">Unidad de Capacitación: </td>
                        </tr>
                        <tr>
                            <td colspan="7">Nombre del Curso de Capacitación:</td>
                        </tr>
                        <tr>
                            <td colspan="7">Especialidad:</td>
                        </tr>
                        <tr>
                            <td colspan="7">Nombre del Instructor: </td>
                        </tr>
                        <tr>
                            <td colspan="5">Nombre completo del Alumno: </td>
                            <td colspan="2">No de Control: </td>
                        </tr>
                        <tr>
                            <td colspan="7">Lugar y Fecha de aplicación:</td>
                        </tr>
                    </tbody>
                </table>
            </div> 
            <label for="">Instrucciones</label><br>

            @foreach($evalAlumno as $pregunta)
            {{ $loop->index }} .- {{$pregunta->descripcion}} <br>
                @foreach($pregunta->opciones as $opcion)
                    &nbsp;&nbsp;.- {{$opcion }} <br>
                @endforeach
            <br>
            @endforeach

            <div class="table table-responsive">
                
               <table class="tablaf">
                    <tbody>
                        <tr>
                            <td colspan="3">ALUMNO</td>
                            <td colspan="3">APLICO</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="firma">NOMBRE Y FIRMA DEL ALUMNO</td>
                            <td colspan="3" class="firma">NOMBRE Y FIRMA DEL INSTRUCTOR</td>
                        </tr>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--================================= end of page =================================-->
    <div class="page_break"></div>
    <!--================================= end of page =================================-->


    <div class="marco">
        <div >
            <div style="color:#FCB6B5" align=center><br> 
                <h2>CLAVE DE RESPUESTAS </h2><br>
                <h2>“EVALUACIÓN DE APRENDIZAJE AL ALUMNO”</h2>
            </div>

            <div class="table table-responsive">
                <table class="tablar">
                    <tbody>
                        <tr>
                            <td>Nombre del instructor</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="firma">Fecha de Aplicación</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="firma">Curso</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="firma">Especialidad</td>
                            <td></td>
                        </tr>
                        <tr></tr>
                    </tbody>
                </table>
            </div>

            <br><br><br>
            <div class="table table-responsive">
                <table class="tablas">
                    <thead>
                        <tr style="background-color:#F6F6F6">
                            <th>Numero de Reactivo</th>
                            <th>Contenido Tematico</th>
                            <th>Respuesta Correcta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evalAlumno as $pregunta)
                        <tr style="text-align:center;">
                            <td>{{$loop->index + 1}}</td>
                            <td>{{$pregunta->descripcion}}</td>
                            <td>{{$pregunta->respuesta}}</td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        </div>


        
    </div>

    
    <!--================================= end of page =================================-->
    <div class="page_break"></div>
    <!--================================= end of page =================================-->

   <div class="marco">
        <div style="color:#FCB6B5" align=center><br> 
            <h2>GUIA DE OBSERVACION DEL INSTRUCTOR</h2><br>
        </div>       
        <label for="" style="text-align: justify;">Instrucciones: Observe si la ejecución de las actividades enunciadas las realiza 
        el capacitando que se está evaluando y marca con una X el cumplimiento en la columna correspondiente, así mismo es importante 
        anotar las observaciones pertinentes.</label>

        <div class="table table-responsive">
            <table class="tablas">
                <tbody>
                    <tr>
                        <td rowspan="2" style="text-align:center;">CD</td>
                        <td rowspan="2" style="text-align:center;">REACTIVOS</td>
                        <td colspan="2" style="text-align:center;">CUMPLIMINENTO</td>
                        <td rowspan="2" style="text-align:center;">OBSERVACIONES</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">SI</td>
                        <td style="text-align:center;">NO</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Comprende los beneficios</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Comprende los objetivos</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Identifica bien una estructura de clase.</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Realiza bien la actividad planificada</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Utilizas las tic's para su aprendizaje</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table table-responsive">
            <table class="tablaf">
                <thead>
                    <tr>
                        <td>APLICÓ</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="firma">NOMBRE Y FIRMA DEL INSTRUCTOR</td>
                    </tr>
                </tbody>

            </table>
        </div>
        
   </div>
</body>
</html>