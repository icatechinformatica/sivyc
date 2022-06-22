<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CARTA DESCRIPTIVA</title>
    <style>      
        body{margin-top: 50px; margin-bottom: 80px; font-family: OpenSans, Gotham}
        @page {margin: 40px 30px 10px 30px;}
            
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
        .tablas tr{font-style: Gotham; font-size: 12px; border: black 2px solid;  padding: 1px 1px;}
        .tablas th{font-style: Gotham; font-size: 12px; border: black 2px solid; text-align: center; padding: 1px 1px;}
        .tablas td{font-style: Gotham; font-size: 12px; border: black 2px solid; text-align: left; padding: 1px 1px;}
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
        .titulos h3{ position: relative;  top: 18px;  left: 10px;}

        .contenido{font-weight: bold; font-size: 12px; }

        .preguntas{padding-right: 50px; padding: left 50px;  text-align: justify;}

        
        
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
            <br>
            <br>
            
            <div class="titulos" ><br> 
                <img class="" " src='img/paqueterias/carta.png'>
                <h2>CARTA DESCRIPTIVA</h2>
            </div>
            
            <br>
            <br>
            
            <div >
                <table class="tablas">
                    <thead>
                        <tr>
                            <th colspan="10">DATOS GENERALES</th>
                        </tr>

                    </thead>
                    <tbody>
                        
                        <tr>
                            <td colspan="2" rowspan="2"> <label class="contenido">Entidad Federativa:</label> {{ $cartaDescriptiva->entidadfederativa }}</td>
                            <td colspan="2" rowspan="2"> <label class="contenido">Ciclo Escolar:</label> {{ $cartaDescriptiva->cicloescolar }}</td>
                            <td colspan="2" rowspan="2"> <label class="contenido">Programa Estrategico</label> (en caso aplicable): </td>
                            <td colspan="4"> <label class="contenido">Modalidad</label> </td>
                        </tr>
                        <tr>
                            <td> <label class="contenido">EXT</label> </td>
                            <td>@if($cartaDescriptiva->modalidad == 'EXT') X @endif</td>
                            <td> <label class="contenido">CAE</label> </td>
                            <td>@if($cartaDescriptiva->modalidad == 'CAE') X @endif</td>
                        </tr>
                        <tr>
                            <td colspan="5"><label class="contenido">Tipo:  &nbsp;&nbsp;&nbsp;&nbsp;  Presencial</label> (@if($cartaDescriptiva->tipo== 'PRESENCIAL') X @endif)  <label class="contenido">&nbsp;&nbsp;&nbsp;&nbsp; A Distancia</label>( @if($cartaDescriptiva->tipo == 'A DISTANCIA') X @endif)</td>
                            <td colspan="5"> <label class="contenido">Especialidad:</label> {{ $cartaDescriptiva->especialidad }}</td>
                        </tr>
                        <tr>
                            <td colspan="7"><label class="contenido">Nombre del curso:</label> {{ $cartaDescriptiva->nombrecurso }} </td>
                            <td colspan="3"><label class="contenido">Duracion en horas: </label>{{ $cartaDescriptiva->duracion }} Hrs</td>
                        </tr>
                        <tr>
                            <td colspan="5"><label class="contenido">Campo de Formación Profesional:</label> {{ $cartaDescriptiva->formacionlaboral }}</td>
                            <td colspan="5"><label class="contenido">Especialidad:</label> {{ $cartaDescriptiva->especialidad }} </td>
                        </tr>
                    
                        <tr>
                    
                            <td colspan="10"><label class="contenido">Aprendizaje esperado:</label> <br>
                                <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->aprendizajeesperado));?>
                            </td>
                        </tr>
                    
                        <tr>
                            <td colspan="10"><label class="contenido">Objetivos especificos por tema: </label> <br>
                                <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->objetivoespecifico));?>  
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10"><label class="contenido">Transversabilidad con otros cursos:</label>
                                {{ $cartaDescriptiva->transversabilidad }} 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10"><label class="contenido">Publico o personal al que va dirigido: </label>
                                {{ $cartaDescriptiva->publico }} 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10"><label class="contenido">Proceso de evaluacion:</label> <br>
                            @foreach ($cartaDescriptiva->ponderacion as $ponderacion)
                            {{ $ponderacion->criterio }} &nbsp;&nbsp;.................... {{ $ponderacion->ponderacion }}% <br>
                            @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10"><label class="contenido">Observaciones:</label> <br>
                                <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->observaciones)); ?>  
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-family: serif, Ghandi;">Contenido Tematico</td>
                            <td colspan="2">Estrategia Didactica</td>
                            <td colspan="2">Proceso de Evaluacion </td>
                            <td colspan="4">Duracion (EN HORAS)</td>
                        </tr>

                        @foreach ($cartaDescriptiva->contenidoTematico as $contenido)
                        <tr>
                            <td colspan="2">
                                <label class="contenido">
                                    <?php echo htmlspecialchars_decode(stripslashes($contenido->contenido));?>
                                </label> 
                            </td>
                            <td colspan="2"><?php echo htmlspecialchars_decode(stripslashes($contenido->estrategia)); ?></td>
                            <td colspan="2"><?php echo htmlspecialchars_decode(stripslashes($contenido->proceso)); ?> </td>
                            <td colspan="4"><?php echo htmlspecialchars_decode(stripslashes($contenido->duracion)); ?> </td>
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
                            <td colspan="3" >Elementos de Apoyo: </td>
                            <td colspan="4" >Auxiliares de la enseñanza: </td>
                            <td colspan="3" >Referencias: </td>
                        </tr>
                        @foreach($cartaDescriptiva->recursosDidacticos as $recursos)
                        <tr>
                            <td colspan="3"> <?php echo htmlspecialchars_decode(stripslashes($recursos->elementoapoyo)) ?> </td>
                            <td colspan="4"> <?php echo htmlspecialchars_decode(stripslashes($recursos->auxenseñanza)) ?> </td>
                            <td colspan="3"> <?php echo htmlspecialchars_decode(stripslashes($recursos->referencias)) ?>  </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
               
            </div> 
        </div>
    </div>


   
</body>
</html>