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

        
        .top-title{text-transform:capitalize;line-height:normal;   }
        .header-image{ width:20%;margin-right:5%;margin-top:-5%;text-align:right; float: left;}
        .pic-img{height:10vw; width:19.15vw;}
        .header-titles{width:65%; margin-left: auto; margin-right: auto;text-align: center;  }
        .tip-title{font-size:4.5vw; margin-bottom:5%; }
        .img-portada { display: flex; margin-right: auto;text-align: center; }
        .img-portada img{ width: 550px; height:600px;}
        .subtitle-portada{ float: right; position: fixed; bottom: 200; width: 100%; text-align: right;}

        
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
                    <label style="font-size:28px !important" >MANUAL DIDACTICO</label><br>
                    <label style="font-size:28px !important; color: #692E41;" >EXTENSION</label>
                </div>
            </div>


            
            <div class="page_break"></div>
            @foreach($contenidos as $manual)
            <?php echo htmlspecialchars_decode(stripslashes($manual->contenidoExtra)); ?>
            @endforeach
        </div>
        </div>
    </div>


   
</body>
</html>