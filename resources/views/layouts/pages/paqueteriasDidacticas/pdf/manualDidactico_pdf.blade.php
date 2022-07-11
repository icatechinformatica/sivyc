<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MANUAL DIDACTICO</title>
    <style>      
        body{margin-top: 50px; margin-bottom: 80px; }
        @page {margin: 40px 70px 10px 70px;}
            
        header { position: fixed; left: 0px;  right: 0px; text-align: center;}
        header h1{height:0; line-height: 14px; padding: 9px; margin: 0;}
        header h2{margin-top: 20px; font-size: 8px; border: 1px solid gray; padding: 12px; line-height: 18px; text-align: justify;}
        footer {position:fixed;   left:0px;   bottom:-50px;   height:100px;   width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
            
        img.derecha { float: right; width: 200px; height: 60px; }
        img.centro { float: right;width: 200px;height: 60px;}
        
        

        
        .tabla-indice td, th {  
            text-align: left;
            padding: 8px;
        }

        .tabla-indice td:not(:last-child),th:not(:last-child) {
         border-right: 1px solid black;
        }

        .tabla-indice {
            border-collapse: collapse;
        }
        
        .tabla-indice th{
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important; font-size:16px
        }
        .tabla-indice th p{
            font-size: 12px;
        }

        .marco {border-style: solid; padding-right: 80px; padding-left: 80px; }
        .page_break { page-break-before: always; }

        .encabezado{position: fixed; top: -50; }
        .img-fondo{position: relative; left: -20;}
        .logos{position: absolute; top: 30; left: 0;}
        .contenido{font-weight: bold; font-size: 12px; }

        

        .segoeUI { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
        label { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-style: normal;}

        
        .top-title{text-transform:capitalize;line-height:normal;   }
        .header-image{ width:20%;margin-right:5%;margin-top:-5%;text-align:right; float: left;}
        .pic-img{height:10vw; width:19.15vw;}
        .header-titles{width:65%; margin-left: auto; margin-right: auto;text-align: center;  }
        .tip-title{font-size:4.5vw; margin-bottom:5%; }
        .img-portada { display: flex; margin-right: auto;text-align: center; }
        .img-portada img{ width: 550px; height:600px;}
        .subtitle-portada{ float: right; position: fixed; bottom: 200; width: 100%; text-align: right;}
        
        .creditos {float: right; width: 100%; text-align: right; }
        .derechos-autor{float: right; width: 100%; text-align: right; }
        .derechos-autor-3 {margin-left: auto; margin-right: auto;text-align: justify;}
        .derechos-autor-3 {margin-left: auto; margin-right: auto;text-align: center;}

        .instrucciones{text-transform:capitalize; float: right; width: 100%; text-align: right; font-size:36px; color:#692E41}
        .objetivo-curso {color: #808080;}
        .objetivo-curso .duracion{font-size:36px;}
        .linea-hor {border: 5px solid #808080; }
        .instrucciones-guia {margin-left: auto; margin-right: auto; padding-top: 40px; padding-left: 40px; padding-right: 40px; }
        .instrucciones-guia .center{ text-align: center; font-weight: bold; font-size: 20px;}
        .instrucciones-guia .justify{ text-align: justify; }

        .tablaf { border-collapse: collapse; width: 100%; text-align: center; font-weight: bold; }     
        .tablaf tr { text-align: center;  }
        .tablaf th { text-align: center;  }
        .tablaf td { text-align: center;  }
        .tablaf td img{ width: 100px; height:80px}

        .glosario{text-transform:capitalize; width:65%; margin-left: auto; margin-right: auto;text-align: center;}
        
    </style>
</head>

<header class="encabezado">
    <img class="img-fondo" src="img/paqueterias/headerpaqdid.jpeg" align="center">
    <div class="logos"">
        <div align=center>
            <img class=" centro" src='img/icatech-imagen.png'>
    </div>
    <div align="right">
        <img class="derecha" src='img/instituto_oficial.png'>
    </div>
    </div>
</header>

<footer >
    <img class="img-fondo" src="img/paqueterias/footerpaqdid.jpg" style="height: 50px !important; width: 100%;" align="center">
</footer>

<body>
    <div class="container g-pt-30">
        <div id="content">
            <br>
            <br>

            <div class="portada">
                <div class="top-title">
                    <div class="header-titles">

                        <label style="font-size: 36px !important;">{{$curso->nombre_curso}}</label>
                    </div>
                </div>
                <div class="img-portada">
                    <img src="img/paqueterias/portada_man_didactico.png" alt="">
                </div>
                <div class="subtitle-portada">
                    <label style="font-size:28px !important">MANUAL DIDACTICO</label><br>
                    <label style="font-size:28px !important; color: #692E41;">EXTENSION</label>
                </div>
            </div>

            <div class="page_break"></div>

            <div class="creditos">
                <label style="color:#692E41; font-size:18px; font-weight:bold;">INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA </label> <br>
                <label style="color:#692E41; font-size:18px; font-weight:bold;">DEL ESTADO DE CHIAPAS</label>
                <br><br><br>
                <label style="font-size:18px; font-weight: bold;">Mtra. Fabiola Lizbeth Astudillo Reyes </label> <br>
                <label style="font-size:18px; font-weight: bold;"> Directora General.</label>
                <br><br><br>
                <label style="font-size:18px; font-weight: bold;">RESPONSABLE DE LA PAQUETERÍA DIDÁCTICA</label> <br>
                <label>Lic. Edgar Gonzalo Orozco Martínez</label><br>
                <label>Director de Técnica Académica.</label>
                <br><br><br>
                <label style="font-size:18px; font-weight: bold;">COORDINACIÓN DE ACADEMIA Y VALIDACIÓN.</label> <br>
                <label>Licda. Guadalupe Hernández López</label><br>
                <label>Titular del Departamento de Información e Innovación Académica.</label>
                <br><br><br>
                <label style="font-size:18px; font-weight: bold;">METODOLOGÍA Y TÉCNICA.</label> <br>
                <label>Licda. Rosa Idalia Castellanos de la Cruz</label><br>
                <label>LAnalista de Contenido Didáctico</label><br>
                <label>del Departamento de Información e Innovación Académica.</label>
                <br><br><br>
                <label style="font-size:18px; font-weight: bold;">COLABORACIÓN:</label> <br>
                <label>Licda. Rosa Idalia Castellanos de la Cruz</label><br>
                <label>Titular de la Dirección de la Unidad de Capacitación... </label><br>
                <label></label><br>
                <label>Titular del Departamento Académico de la Unidad de Capacitación de </label><br>
                <br>
            </div>

            <div class="page_break"></div>
            <div class="derechos-autor">
                <label style="color:#692E41; font-size:18px; font-weight:bold;">INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA </label><br>
                <label style="color:#692E41; font-size:18px; font-weight:bold;">DEL ESTADO DE CHIAPAS</label>
                <label>DIRECCIÓN TÉCNICA ACADÉMICA</label>
                <br><br><br><br><br><br><br><br><br>
                <label>DERECHOS RESERVADOS<label><br>
                        <label style="color:#692E41; ">ICATECH</label>
                        <br><br><br><br><br>
            </div>

            <div class="derechos-autor-2">

                <br><br><br><br><br>
                <label style="text-align:justify !important;">PROHIBIDA LA REPRODUCCIÓN TOTAL O PARCIAL DE ESTA OBRA INCLUIDA LA PORTADA, POR CUALQUIER MEDIO SIN AUTORIZACIÓN POR ESCRITO DEL ICATECH. LO CONTRARIO REPRESENTA UN ACTO DE PIRATERIA INTELECTUAL PERSEGUIDO POR LA LEY PENAL.</label>
            </div>
            <br><br><br><br><br>
            <div class="derechos-autor-3">
                <label style="text-align:center; font-weight: bold;">© INSTITUTO DE CAPACITACION Y VINCULACIÓN TECNOLÓGICA</label><br>
                <label style="text-align:center; font-weight: bold;">DEL ESTADO DE CHIAPAS.</label><br>
                <label style="text-align:center; font-weight: bold;">CIRCUNV. PICHUCALCO COL. MOCTEZUMA. TUXTLA GUTIÉRREZ CHIAPAS.</label><br>
                <label style="text-align:center; font-weight: bold;">TEL. Y FAX 612 16 21 • Ext 601</label><br>
                <label style="text-align:center; font-weight: bold;">EMAIL. icatech@icatech.gob.mx</label><br>

            </div>

            <div class="page_break"></div>
            <div class="indice">
                <label style="font-size: 43px; font-weight: bold; color: #C0C0C0;">INDICE</label><br><br><br><br><br>
                <label style="font-size: 20px; font-weight: bold;">INTRODUCCION</label><br><br><br>

                <table class="tabla-indice">
                    @foreach($contenidos as $manual)
                    <tr>
                        <th>
                            Modulo <?php echo htmlspecialchars_decode(stripslashes($manual->tema_principal)); ?><br>
                            <p><?php echo htmlspecialchars_decode(stripslashes($manual->contenido)); ?></p><br>

                        </th>
                        <th></th>

                    </tr>
                    @endforeach
                    <tr>
                        <th>
                        CONCLUSIÓN <br>
                        GLOSARIO <br>
                        BIBLIOGRAFÍA <br>
                        </th>
                        <th></th>
                    </tr>
                </table>
            </div>

            <div class="page_break"></div>
            <div class="instrucciones">
                <label>CURSO</label><br>
                <label style="font-size: 30px !important;">{{$curso->nombre_curso}}</label>
                <br><br>
                <label style="color: black">Modalida {{$curso->modalidad}}</label><br><br><br><br>
            </div>
            <div class="objetivo-curso">
                <label style="font-size: 36px;">OBJETIVO DEL CURSO</label><br>
                <hr class="linea-hor"><br>
                <label style="color:black; font-size:18px">{{$curso->objetivo}}</label><br><br><br><br><br>

                <div class="duracion">
                    <label for="">Duración: {{$curso->duracion}} Horas</label>
                </div>
            </div>

            <div class="page_break"></div>
            <div class="instrucciones-guia">
                <div class="center">
                    <label>INSTRUCCIONES PARA EL USO DE LA GUÍA</label><br><br>
                </div>
                <div class="justify">
                    <label>La guía de aprendizaje es un documento para a poyar tu proceso de capacitación.</label><br><br>

                    <label>En el siguiente apartado se encuentran los submódulos de aprendizaje y su cantidad equivale a las unidades de competencia que contempla tu calificación.</label><br><br>

                    <label>Cada submódulo inicia con un subobjetivo de aprendizaje esperado y contempla los siguientes símbolos que te ayudarán a detectar las actividades que habrás de realizar durante todo el curso.</label><br><br>
                </div>


                <table class="tablaf">
                    <tbody style="width: 100%;">
                        <tr>
                            <td colspan="2">SIMBOLOGIA</td>
                        </tr>
                        <tr>
                            <td>
                                <p>TRABAJO MANUAL</p>
                                <img src="img/paqueterias/trabajo-manual.png" alt="">
                            </td>
                            <td>
                                <p>IMPORTANTE</p>
                                <img src="img/paqueterias/importante.png" alt="">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>NOTAS</p>
                                <img src="img/paqueterias/notas.png" alt="">
                            </td>
                            <td>
                                <p>EJERCICIO</p>
                                <img src="img/paqueterias/ejercicios.png" alt="">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>EVALUACION</p>
                                <img src="img/paqueterias/evaluacion.png" alt="">
                            </td>
                            <td>
                                <p>SUBMODULO</p>
                                <img src="img/paqueterias/submodulo.png" alt="">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>TEMAS</p>
                                <img src="img/paqueterias/temas.png" alt="">
                            </td>
                            <td>
                                <p>MODULO</p>
                                <img src="img/paqueterias/modulo.png" alt="">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <br><br>
                <div class="justify">
                    <label for="">Al final de la guía encontrarás un Glosario, en el que podrás conocer una breve explicación de muchos términos y palabras técnicas o poco usadas en el lenguaje habitual.</label>
                </div>
                
            </div>

            <div class="page_break"></div>
            @foreach($contenidos as $manual)
            <?php echo htmlspecialchars_decode(stripslashes($manual->contenidoExtra)); ?>
            @endforeach

            <div class="page_break"></div>

            <label align="center" style="color: #808080; font-size: 36px; text-align: center;">BIBLIOGRAFIA</label> 
            <div class="justify">
            <?php echo htmlspecialchars_decode(stripslashes($carta_descriptiva->referencias)); ?>
            </div>
        </div>
    </div>
    </div>



</body>


</html>