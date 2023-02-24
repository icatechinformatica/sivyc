<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Evaluacion De Aprendizaje al Alumno</title>
    <style>      
        body{margin-top: 50px; margin-bottom: 100px; }
        @page {margin: 40px 70px 10px 70px;    }
            
        header { position: fixed; left: 0px;  right: 0px; text-align: center;}
        header h1{height:0; line-height: 14px; padding: 9px; margin: 0;}
        header h2{margin-top: 20px; font-size: 8px; border: 1px solid gray; padding: 12px; line-height: 18px; text-align: justify;}
        footer {position:fixed;   left:0px;   bottom:-50px;   height:150px;   width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        img.izquierda {float: right;width: 200px;height: 60px;}
        img.izquierdabot {position: absolute;left: 50px;width: 350px;height: 60px;}
        img.derechabot {position: absolute;right: 50px;width: 350px;height: 60px;}
        img.derecha { float: right; width: 200px; height: 60px; }
        img.centro { float: right;width: 200px;height: 60px;}
        table { page-break-before: avoid !important;}
        .tablas {border-collapse: collapse; width: 100%; height: auto; margin-top: 20px; }
        .tablas tr{ font-size: 12px; border: black 1px solid;  padding-bottom: 10px;}
        .tablas th{ font-size: 12px; border: black 1px solid; text-align: center; padding-bottom: 10px;}
        .tablas td{ font-size: 12px; border: black 1px solid; text-align: left; padding-bottom: 10px;}
        .tablaf { border-collapse: collapse; width: 100%; position: absolute; bottom:210px; font-size:20px; text-align: center; }     
        .tablaf tr { border: black 2px solid; text-align: center; padding: 1px 1px; }
        .tablaf th { border: black 2px solid; text-align: center; padding: 1px 1px; }
        .tablaf td { border: black 2px solid; text-align: center; padding: 1px 1px; }

        
        .tablar tr td{  border-collapse: collapse; font-size: 12px; text-align: center; padding: 0px 0px; border: black 1px solid;}
        

        .tablad { border-collapse: collapse;}     
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;}     
        .tablag tr td{ font-size: 8px; padding: 0px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        .marco {border-style: solid; padding-right: 80px; padding-left: 80px; }
        .page_break { page-break-before: always; }

        .encabezado{position: fixed; top: -50; }
        .img-fondo{position: relative; left: -20;}
        .logos{position: absolute; top: 30; left: 0;}

        .titulos img{float: left;width: 100px;height: 100px; margin-left: 100px; padding-right: 50px}
        .titulos h2{ position: relative;  top: 18px;  left: 10px;}
        .titulos2 img{float: left;width: 80px;height: 80px; margin-left: 100px; padding-right: 10px}
        .segoeUI { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
        .titulos h3{ position: relative;  top: 18px;  left: 10px;}

        .contenido{font-weight: bold; font-size: 12px; }

        .preguntas{padding-left: 10px;  text-align: justify;}

        
        
    </style>
</head>

 <header class="encabezado">
    <img class="img-fondo" src="img/paqueterias/headerpaqdid.jpeg" align="center">
    <div class="logos"">
        <div align=center>
            <img class="centro" src='img/icatech-imagen.png'>
        </div>
        <div align="right">
            <img class="derecha" src='img/instituto_oficial.png'>
        </div>
    </div>
</header>

<footer>
    <img class="img-fondo" src="img/paqueterias/footerpaqdid.jpg" style="height: 100px !important; width: 100%;" align="center">
</footer> 

<body>
    <div class= "container g-pt-30">
        <div id="content">
            
          
            <div >
                
                <div class="titulos2" ><br> 
                    <img class="" " src='img/paqueterias/logo_eval_alumno.png'>
                    <h3 class="segoeUI" style="color:#692E41; font-size:22px;">EVALUACIÓN DE APRENDIZAJE AL ALUMNO</h3>
                </div>
                <br>
                
            </div>
            <br>
            
            <div class="table table-responsive"style="padding-left: 20px; padding-right: 20px;">
                <label class="segoeUI" style="font-style: italic; text-align: left; font-size: 12px;">(Estos datos deben ser llenados por el departamento académico de la Unidad)</label>
                <table class="tablas" >
                    <tbody>
                        <tr>
                            <td colspan="7"><label class="contenido segoeUI" style="font-size: 14px;">Unidad de Capacitación: {{$curso->unidad_amovil}} </label></td>
                        </tr>
                        <tr>
                            <td colspan="7"><label class="contenido segoeUI" style="font-size: 14px;">Nombre del Curso de Capacitación: {{$curso->nombre_curso}}</label></td>
                        </tr>
                        <tr>
                            <td colspan="7"><label class="contenido segoeUI" style="font-size: 14px;">Especialidad: {{$cartaDescriptiva->especialidad}} </label></td>
                        </tr>
                        <tr>
                            <td colspan="7"><label class="contenido segoeUI" style="font-size: 14px;">Nombre del Instructor: </label></td>
                        </tr>
                        <tr>
                            <td colspan="5"><label class="contenido segoeUI" style="font-size: 14px;">Nombre completo del Alumno: </label></td>
                            <td colspan="2"><label class="contenido segoeUI" style="font-size: 14px;">No de Control: </label></td>
                        </tr>
                        <tr>
                            <td colspan="7"><label class="contenido segoeUI" style="font-size: 14px;">Lugar y Fecha de aplicación:</label></td>
                        </tr>
                        
                    </tbody>
                </table>
                <br><br>
                <label >Instrucciones: {{$evalAlumno->instrucciones ?? ''}} </label><br>
            </div> 



            <div class="preguntas">
                <label for="segoeUI" style="font-weight: bold; font: size 12px;">(Preguntas)</label><br>
                @if($evalAlumno != null)
                @foreach($evalAlumno as $pregunta)
                
                @if(isset($pregunta->descripcion))
                
               <label class="segoeUI" style="font-weight: bold;"> {{ $loop->index + 1}} .- {{$pregunta->descripcion}}</label> <br><br>
                @if($pregunta->tipo == 'multiple')
                    @foreach($pregunta->opciones as $indice => $opcion)
                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $abecedario[$indice] }} .- {{$opcion }} <br>
                    @endforeach
                @else
                    <br><br><br>
                @endif
                <br>

                @endif
                @endforeach
                @endif
            </div>

                
            <table class="tablaf">
                <tbody style="width: 100%;">
                    <tr>
                        <td style="width:50%; font-size: 15px;" >ALUMNO</td>
                        <td style="width:50%; font-size: 15px;" >APLICO</td>
                    </tr>
                    <tr>
                        <td style="width:50%; padding-top:80; font-size: 15px;" >NOMBRE Y FIRMA DEL ALUMNO</td>
                        <td style="width:50%; padding-top:80; font-size: 15px;" >NOMBRE Y FIRMA DEL INSTRUCTOR</td>
                    </tr>
                    <tr><td></td><td></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!--================================= end of page =================================-->
    <div class="page_break"></div>
    <!--================================= end of page =================================-->

    <div class="marco">
        <div >
            <div style="color:#692E41" align=center><br> 
                <h2>CLAVE DE RESPUESTAS </h2><br>
                <h2>“EVALUACIÓN DE APRENDIZAJE AL ALUMNO”</h2>
            </div>


                <table class="tablas">
                    <tbody>
                        <tr>
                            <td style="width:30%; font-size: 14px !important; " >Nombre del instructor</td>
                            <td style="width:70%; font-size: 14px !important; " ></td>
                        </tr>
                        <tr>
                            <td style="width:30%; font-size: 14px !important; " >Fecha de Aplicación</td>
                            <td style="width:70%; font-size: 14px !important; " ></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;; font-size: 14px !important; " >Curso</td>
                            <td style="width: 70%;; font-size: 14px !important; " ></td>
                        </tr>
                        <tr>
                            <td style="width: 30%; font-size: 14px !important; " >Especialidad</td>
                            <td style="width: 70%; font-size: 14px !important; " ></td>
                        </tr>
                        <tr><td></td><td></td></tr>
                    </tbody>
                </table>

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
                        @if($evalAlumno != null)
                        @foreach($evalAlumno as $pregunta)
                        
                        @if(isset($pregunta->contenidoTematico))
                        <tr >
                            <td style="text-align:center;">{{$loop->index + 1}}</td>
                            <td>{{$pregunta->contenidoTematico ?? ''}}</td>
                            <td style="text-align:center;">{{$pregunta->respuesta ?? 'N/A'}}</td>
                        </tr>
                        @endif
                        @endforeach
                        @endif
                        
                    </tbody>
                </table>
            </div>
        </div>


        
    </div>

    
    <!--================================= end of page =================================-->
    <div class="page_break"></div>
    <!--================================= end of page =================================-->
    <br><br><br><br>
    <div class="marco">
        <div style="color:#692E41" align=center><br> 
            <h2>GUÍA DE OBSERVACIÓN DEL INSTRUCTOR</h2><br>
        </div>       
        <label style="text-align: justify;">Instrucciones: Observe si la ejecución de las actividades enunciadas las realiza 
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
                        <td style="text-align:center;">1</td>
                        <td>Comprende los beneficios</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">2</td>
                        <td>Comprende los objetivos</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">3</td>
                        <td>Identifica bien una estructura de clase.</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">4</td>
                        <td>Realiza bien la actividad planificada</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br><br><br><br><br>
        <div class="table table-responsive ">
            <table class="tablas" style="width:60%; text-align:center !important; font-size:14px; margin-left: auto;margin-right: auto;">
                <thead>
                    <tr>
                        <td>APLICÓ</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-top: 90px;">NOMBRE Y FIRMA DEL INSTRUCTOR</td>
                    </tr>
                </tbody>

            </table>
        </div>
   </div>

   
</body>
</html>