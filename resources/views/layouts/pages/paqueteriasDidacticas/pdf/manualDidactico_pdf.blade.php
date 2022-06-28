<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MANUAL DIDACTICO</title>
    <style>      
        body{margin-top: 50px; margin-bottom: 80px; }
        @page {margin: 40px 25px 10px 25px;}
            
        header { position: fixed; left: 0px;  right: 0px; text-align: center;}
        header h1{height:0; line-height: 14px; padding: 9px; margin: 0;}
        header h2{margin-top: 20px; font-size: 8px; border: 1px solid gray; padding: 12px; line-height: 18px; text-align: justify;}
        footer {position:fixed;   left:0px;   bottom:-50px;   height:150px;   width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        
        .logos{display: flex;justify-content: center; align-items: center; height:400px;border:solid 1px red; }
        img.derecha { float: right; width: 290px; height:100px; }
        img.izquierda { float: right;width: 290px;height: 100px;}
        table { page-break-before: avoid !important;}
        .tablas {border-collapse: collapse; width: 100%; height: auto; margin-top: 20px; padding-bottom: 10px;}
        .tablas tr{ font-size: 12px; border: black 2px solid;  padding: 1px 1px;}
        .tablas th{ font-size: 12px; border: black 2px solid; text-align: center; padding: 1px 1px;}
        .tablas td{ font-size: 12px; border: black 2px solid; text-align: left; padding: 1px 1px;}

        .marco {border-style: solid; padding-right: 80px; padding-left: 80px; }
        .page_break { page-break-before: always; }

        .encabezado{position: fixed; top: -20; }
        .img-fondo{position: relative;  width: 100%;}
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
        <div class="izquierda">
            <img src='img/icatech-imagen.png'>
        </div>
        <div class="derecha">
            <img src='img/instituto_oficial.png'>
        </div>
    </div>
</header>

<footer>
    <img class="img-fondo" src="img/paqueterias/footerpaqdid.jpg" style="height: 100px !important; width: 100%;" align="center">
</footer> 

<body>
    <div class= "container g-pt-30">
        <div id="content">
           
        </div>
    </div>


   
</body>
</html>