<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CARTA DESCRIPTIVA</title>
    <style>      
        body{margin-top: 50px; margin-bottom: 80px; }
        @page {margin: 40px 70px 10px 70px;}
            
        header { position: fixed; left: 0px;  right: 0px; text-align: center;}
        header h1{height:0; line-height: 14px; padding: 9px; margin: 0;}
        header h2{margin-top: 20px; font-size: 8px; border: 1px solid gray; padding: 12px; line-height: 18px; text-align: justify;}
        footer {position:fixed;   left:0px;   bottom:-50px;   height:150px;   width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
            
        img.derecha { float: right; width: 200px; height: 60px; }
        img.centro { float: right;width: 200px;height: 60px;}
        table { page-break-before: avoid !important;}
        .tablas {border-collapse: collapse; width: 100%; height: auto; margin-top: 20px; padding-bottom: 10px;}
        .tablas tr{ font-size: 12px; border: black 2px solid;  padding: 1px 1px;}
        .tablas th{ font-size: 12px; border: black 2px solid; text-align: center; padding: 1px 1px;}
        .tablas td{ font-size: 12px; border: black 2px solid; text-align: left; padding: 1px 1px;}

        .marco {border-style: solid; padding-right: 80px; padding-left: 80px; }
        .page_break { page-break-before: always; }

        .encabezado{position: fixed; top: -50; }
        .img-fondo{position: relative; left: -20;}
        .logos{position: absolute; top: 30; left: 0;}
        .contenido{font-weight: bold; font-size: 12px; }

        

        .segoeUI { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
        label { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-style: normal;}

        
        .top-title{text-transform:capitalize;line-height:normal; text-align:right; height:100px; }
        .header-image{ width:20%;margin-right:5%;margin-top:-5%;text-align:right; float: left;}
        .pic-img{height:10vw; width:19.15vw;}
        .header-titles{width:65%; margin-left:10%;text-align: left; float: left;}
        .tip-title{font-size:4.5vw; margin-bottom:5%;}
        .title-subheader{ font-size:2vw;}

        
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
            <div class="top-title">
                <div class="header-image">
                    <img   src='img/paqueterias/carta.png'>
                </div>
                <div class="header-titles">
                    <h2 class="segoeUI tip-tittle" >CARTA DESCRIPTIVA</h2>
                </div>
            </div>
            
            
            <div >
                <table class="tablas">
                    <thead>
                        <tr> <th colspan="10" class="segoeUI">DATOS GENERALES</th> </tr>
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
                            <td colspan="5"><label class="contenido">Tipo:  &nbsp;&nbsp;&nbsp;&nbsp;  Presencial</label> (@if($cartaDescriptiva->tipo== 'PRESENCIAL') X @endif)  <label class="contenido">&nbsp;nbsp;&nbsp;&nbsp; A Distancia</label>( @if($cartaDescriptiva->tipo == 'A DISTANCIA') X @endif)</td>
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
                            @if($cartaDescriptiva->ponderacion != null)
                            @foreach ($cartaDescriptiva->ponderacion as $ponderacion)
                            {{ $ponderacion->criterio }} &nbsp;&nbsp;.................... {{ $ponderacion->ponderacion }}% <br>
                            @endforeach
                            @endif
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
                        @if($cartaDescriptiva->contenidoTematico!=null)
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
                        @endif
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
                        
                        <tr>
                            <td colspan="3"> <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->elementoapoyo)) ?> </td>
                            <td colspan="4"> <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->auxenseñanza)) ?> </td>
                            <td colspan="3"> <?php echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->referencias)) ?>  </td>
                        </tr>
                        
                    </tbody>
                </table>
               
            </div> 
        </div>
    </div>


   
</body>
</html>