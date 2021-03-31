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
            <img class="izquierda" src='img/logohorizontalica1.jpg'>
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
        
        <table class="tablad" bgcolor="black" style="width: 280px;">
                <tr>
                    <td colspan="4" style="color:white; max-width:80px;"><b>{{ $reg_unidad->direccion }}</b></td>
                </tr>
        </table>
               
        <img class="derecha" src='img/icatech-imagen.png'>
    </footer>
    <div class="contenedor">
        <div align=right style="font-size:11px;"><b>UNIDAD DE CAPACITACION {{ $reg_unidad->unidad }}</b></div>
        <div align=right style="font-size:11px;"><b>MEMORANDUM NO. {{ $numero_memo }}</b></div>                        
        <div align=right style="font-size:11px;"><b>{{ $reg_unidad->unidad }}, CHIAPAS; {{ $fecha_nueva }}</b></div>                        
        <br><br>
        <div align=left style="font-size:12px;"><b>{{ $reg_unidad->dacademico }}, {{ $reg_unidad->pdacademico }}</b></div>
        <div align=left style="font-size:11px;"><b>Asunto: Reporte de cursos finalizados de la  Unidad de Capacitación {{ $reg_unidad->unidad }}.</b></div>
        <br><br>
        <div align="justify" style="font-size:11px;">
            Derivado del proceso académico de entrega de información, adjunto al presente con firmas autógrafas y los sellos originales correspondientes:
        </div>
        <br>
        <table class="tablag">
            <thead>
                <tr>
                    <td style="font-size:11px;"><b>* RIAC-02 de inscripción</b></td>
                    <td style="font-size:11px;"><b>* LAD-04 Lista de asistencia</b></td>
                </tr> 
                <tr>
                    <td style="font-size:11px;"><b>* RIAC-02 de certificación</b></td>
                    <td style="font-size:11px;"><b>* RESD-05 calificaciones.</b></td>
                </tr>
                <tr>
                    <td style="font-size:11px;"><b>* RIAC-02 de acreditación</b></td>
                </tr>                                                
            </thead>                                
        </table>
        <br>
        <div align="justify" style="font-size:11px;">Del ciclo escolar {{ $reg_cursos[0]->ciclo }} en la Unidad de Capacitación {{ $reg_unidad->unidad }}, se reportan {{ $total }} para este mes</div>
        <br>
        
            <table class="tablas">
                <thead>                       
                    <tr>      	  
                        <th>UNIDAD O ACCION MOVIL</th>     
                        <th>CURSO</th>               
                        <th>MOD</th>                                   
                        <th>INICIO </th>  
                        <th>TERMINO</th>
                        <th>CUPO</th>
                        <th>INSTRUCTOR</th>
                        <th>CLAVE</th>
                        <th>OBSERVACIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reg_cursos as $a)
                    <tr>                           	  
                        <th>{{ $a->unidad }}</th>     
                        <th>{{ $a->curso }}</th>               
                        <th>{{ $a->mod }}</th>                                   
                        <th>{{ $a->inicio }}</th>  
                        <th>{{ $a->termino }}</th>
                        <th>{{ $a->cupo }}</th>
                        <th>{{ $a->nombre }}</th>
                        <th>{{ $a->clave }}</th>
                        <th>{{ $a->tnota }}</th>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <div style="font-size:11px;"> <b>Sin más por el momento, le envío un cordial saludo.</b> </div>
            <br><br>
            <div style="font-size:11px;"> <b>A T E N T A M E N T E</b> </div>
            <br><br><br>
            <div style="font-size:11px;"> <b>{{ $reg_unidad->dunidad }}</b> </div>
            <div style="font-size:11px;"> <b>{{ $reg_unidad->pdunidad }}</b> </div>
            <br><br><br>
            <div style="font-size:11px;"> <b>C.c.p ING. MARÍA TERESA JIMÉNEZ FONSECA , JEFE DEL DEPARTAMENTO DE CERTIFICACIÓN Y CONTROL. Para su conocimiento.</b> </div>
            <div style="font-size:11px;"> <b>Archivo / Minutario.</b> </div>
            <div style="font-size:11px;"> <b>Validó: {{ $reg_unidad->dunidad }}. {{ $reg_unidad->pdunidad }}</b> </div>
            <div style="font-size:11px;"> <b>Elaboró: {{ $reg_unidad->academico }}. {{ $reg_unidad->pacademico }}.</b></div>
            <br><br>
    </div>        
</body>
</html>