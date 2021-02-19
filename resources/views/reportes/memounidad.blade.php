<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FORMATO T</title>
    <style>      
        body{font-family: sans-serif}
        @page {margin: 20px 50px 120px 50px;size: letter;}
        header { position: fixed; left: 0px; top: 30px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        img.izquierda {float: left;width: 200px;height: 60px;}
        img.izquierdabot {position:fixed;left: 50px;width: 350px;height: 60px;}
        img.derechabot {position:fixed;right: 50px;width: 350px;height: 60px;}
        img.derecha {float: right;width: 200px;height: 60px;}
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;} 
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}     
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}     
        .tablag tr td { font-size: 8px; padding: 0px;}
        footer { position:fixed;left:0px;bottom:0px;height:0px;width:100%;}
        footer .page:after { content: counter(page, sans-serif);}   
        .contenedor {  
        position:RELATIVE;
        top:120px;  
        width:100%;   
        margin:auto;
        
        /* Propiedad que ha sido agreda*/
        
        }  
    }
    </style>
</head>
<body>
<header>
            <img class="izquierda" src='img/logohorizontalica1.png'>
            <img class="derecha" src='img/chiapas.png'>
            <br>
            <h6>"2021, AÑO DE LA INDEPENDENCIA"</h6>
    </header>
    <footer>  
        <script type="text/php">
            if (isset($pdf)) 
            {
                $x = 275;
                $y = 725;
                $text = "Hoja {PAGE_NUM} de {PAGE_COUNT}";
                $font = "Arial";
                $size = 11;
                $color = array(0,0,0);
                $word_space = 0.0;  //  default
                $char_space = 0.0;  //  default
                $angle = 0.0;   //  default
                $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            }
        </script>
        
        <table class="tablad" bgcolor="black">
                <tr>
                    <td colspan="4" style="color:white;"><b>AV. PASO LIMON NO. 1581 JUNTO AL COLEGIO LAURELES </b></td>
                </tr> 
                <tr>
                    <td colspan="4" style="color:white;"><b>COL. COMISION FEDERAL DE ELECTRICIDAD C.P. 29049.</b></td>
                </tr>
                <tr>
                    <td colspan="4" style="color:white;"><b>TUXTLA GUTIERREZ, CHIAPAS</b></td>
                </tr>
        </table>
               
        <img class="derecha" src='img/icatech-imagen.png'>
    </footer>
    <div class= "contenedor">
        <div align=right style="font-size:11px;"><b>DIRECCION TECNICA ACADEMICA</b></div>
        <div align=right style="font-size:11px;"><b>MEMORANDUM NO. {{ $numero_memo }}</b></div>                        
        <div align=right style="font-size:11px;"><b>{{ $reg_unidad->unidad }}, CHIAPAS; {{ $fecha_nueva }}</b></div>                        
        <br><br>
            <table class="tablag">
                <body>
                    <tr>
                        <td align="right" style="font-size:11px;"><b>DIRECCION TECNICA ACADEMICA</b></td>
                    </tr> 
                    <tr>
                        <td align="right" style="font-size:11px;"><b>MEMORANDUM NO. ICATECH/600/0020/2021</b></td>                        
                    </tr>
                    <tr>
                        <td align="right" style="font-size:11px;"><b>TUXTLA GUTIERREZ,CHIAPAS; 28 DE ENERO DE 2021</b></td>                        
                    </tr>                                                
                </body>                
            </table>
            <br><br>
            <table class="tablag">
                <body>
                    <tr>
                        <td style="font-size:12px;"><b>LIC. MARIA DEL ROSARIO URIBE BARRAGAN.<br> DIRECTOR(A) DE LA UNIDAD DE CAPACITACION TAPACHULA</b></td>
                    </tr> 
                    <tr>
                        <td style="font-size:11px;"><b>PRESENTE.</b></td>                        
                    </tr>                                               
                </body>                
            </table>
            <br><br>
            <div align="justify" style="font-size:11px;">
                En seguimiento a la integración del Formato T del mes de Agosto del presente 
                año de su Unidad de Capacitación, recibido el pasado 27 de agosto al correo electronico 
                clavestapachula.dta@hotmail.com, le informo que fueron recibidos los formatos RIACD-02 INSCRIPCION,
                RIAC-02 ACREDITACION, RIAC-02 CERTIFICACION, LAD-04 LISTA DE ASISTENCIA, RESD-05 CALIFICACIONES
                digitalizados con firmas y sellos de 4 cursos pertencientes a la Unidad TAPACHULA. De lo anterior,
                hago de su conocimiento que, una vez revisada la informacion le comento, se reportaron a la Dirección
                de Planeación de este Instituto 2 cursos y 2 no se reportaron de acuerdo a las siguientes observaciones
            </div>
            <br>
            <div class="table-responsive-sm">
                <table class="tablas">
                    <tbody>                       
                        <tr>      	  
                            <th>No.</th>     
                            <th>MES</th>     
                            <th>UNIDAD/ACCION MOVIL</th>               
                            <th>ESPECIALIDAD</th>                                   
                            <th>CURSO</th>  
                            <th>CLAVE</th>
                            <th>ESTATUS</th>
                        </tr>
                        <tr>      
                            <th>1</th>       
                            <th>FEBRERO</th>     
                            <th>TAPACHULA</th>               
                            <th>OFIMATICA</th>                                   
                            <th>HOJA DE CALCULO</th>  
                            <th>1C-20-OFIM-EXT-0001</th>
                            <th>REPORTADO</th>
                        </tr>
                    </tbody>                                               
                </table>
                <br>
                <div align="justify" style="font-size:11px;">
                    No omito manifestar sobre la información antes mencionada, que se encuentran pendientes los documentos
                    originales de las entregas realizadas por su Unidad de Capacitacón para el reporte estadístico una vez
                    reintegrados al trabajo presencial.
                </div>
                <br>
                <div style="font-size:11px;">Sin más por el momento, agradezco su atención y le envío un cordial saludo.</div>
                <br><br>
                <div style="font-size:11px;"> <b>A T E N T A M E N T E</b> </div>
                <br><br><br>
                <div style="font-size:11px;"> <b>DR. ERICH ARMANDO CRUZ CASTELLANOS</b> </div>
                <div style="font-size:11px;"> <b>DIRECTOR(A) TECNICO ACADEMICO</b> </div>
                <br><br><br>
                <div style="font-size:11px;"> <b>C.c.p Mtra. Fabiola Lizbeth Astudillo Reyes ,Directora General del ICATECH. Para su conocimiento. - Ciudad</b> </div>
                <div style="font-size:11px;"> <b>Lic. YESENIA FUZIKO KOMUKAI HORITA. Jefe del Departamento Académico.</b> </div>
                <div style="font-size:11px;"> <b>Archivo / Minutario.</b> </div>
                <div style="font-size:11px;"> <b>Validó: ING. MARIA TERESA JIMENEZ FONSECA. Jefe del Departamento De Certificación y Control.</b> </div>
                <div style="font-size:11px;"> <b>Elaboró: ING. JULIO CESAR ALCARAZ SANCHEZ. ANALISTA TECNICO B.</b> </div>
            </div><br><br>
            <div class="ex1" style="font-size:8px;float:left;"><br>Dirección<br>Col. XXXX. XXX, Chiapas. C.P. 29000<br>Correo electronico: academicoXXXXX@hotmail.com<br></div>
            <img class="derecha" src='img/icatech-imagen.png'>
    </div>
</body>
</html>