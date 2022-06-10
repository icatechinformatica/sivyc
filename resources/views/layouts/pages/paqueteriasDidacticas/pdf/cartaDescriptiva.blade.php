<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CARTA DESCRIPTIVA</title>
    <style>      
        body{font-family: Gotham}
        @page {margin: 40px 30px 10px 30px;}
            header { position: fixed; left: 0px; top: 10px; right: 0px; text-align: center;}
            header h1{height:0; line-height: 14px; padding: 9px; margin: 0;}
            header h2{margin-top: 20px; font-size: 8px; border: 1px solid gray; padding: 12px; line-height: 18px; text-align: justify;}
            footer {position:fixed;   left:0px;   bottom:-170px;   height:150px;   width:100%;}
            footer .page:after { content: counter(page, sans-serif);}
            img.izquierda {margin-left: 300px; float: left;width: 200px;height: 60px;}
            img.izquierdabot {position: absolute;left: 50px;width: 350px;height: 60px;}
            img.derechabot {position: absolute;right: 50px;width: 350px;height: 60px;}
            img.derecha {float: right;width: 180px;height: 55px;}
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr{font-size: 8px; border: gray 1px solid; text-align: center; padding: 1px 1px;}
        .tablas th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 1px 1px;}
        .tablaf { border-collapse: collapse; width: 100%;}     
        .tablaf tr td { font-size: 8px; text-align: center; padding: 0px 0px;}
        .tablad { border-collapse: collapse;}     
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;}     
        .tablag tr td{ font-size: 8px; padding: 0px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
    </style>
</head>
<body>
    <header>
        <div class= "container g-pt-30">
            <div id="content">
                <img class="izquierda" src='img/instituto_oficial.png'>
                <img class="derecha" src='img/icatech-imagen.png'>+
            </div>

            <table >
                <tr>
                    <td>
                        DATOS GENERALES
                    </td>
                </tr>
                <tr>
                    <td class="first Mayuscula"  valign="top;"><input><strong>Tipo de atención: </strong></td>
                    <td class="second Mayuscula" valign="top;"><input><strong>Tipo recepción: </strong></td>
                </tr>
                <tr>
                    <td class="first Mayuscula" valign="top;"><input><strong>Asunto: </strong></td>
                    <td class="second Mayuscula" valign="top;"><input><strong>Tipo de violencia: </strong></td>
                </tr>
                <tr>
                    <td class="first Mayuscula" valign="top;"><input><strong>Temática de atención: </strong></td>
                    <td class="second Mayuscula" valign="top;"><input><strong>Modalidad de violencia: </strong></td>
                </tr>
            </table>
        </div>
    </header>
    <h1 class="center">CARTA DESCRIPTIVA</h1>
</body>
</html>