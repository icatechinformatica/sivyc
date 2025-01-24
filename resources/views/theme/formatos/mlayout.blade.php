<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>      
        body { font-family: sans-serif; margin:0px; padding: 0px;}  
        @page {margin: 0px; size: letter;}
        content {font-family: sans-serif; font-size: 9px; width:100%; padding:0px;}
        header {text-align: center;}        
        footer { position: fixed; top: 14.2cm; width:100%;  }
        .direccion { position: absolute; top: 1.3cm; width: 95%; height: auto; font-family: sans-serif; font-size: 9px; color: #000;}
        #fondo {
            background-image: url('img/membretado/recibo_pago.png');
            background-size: 100%;
            background-position: center;              
        }
    </style>
     @if(isset($marca))
        @if ($marca)
            <style>
                header:after {
                    content: "BORRADOR"; 
                    font-size: 16em;  
                    color: rgba(40, 40, 43, 0.35);
                    z-index: 9999;                
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: fixed;
                    top: 100%;
                    right: 0;
                    bottom: 0;
                    left: 120%;           
                    transform: rotate(-45deg);
                    font-weight: bold;
                }               
            </style>  
         @endif
    @endif
            
    @yield("css")
</head>
<body id="fondo">    
    <header>        
        <span style="margin-top:96px; padding:0px; font-size: 13px; font-style: italic; display:block;">            
        @php
            if(isset($distintivo)){
                $subcadena = explode("*", $distintivo);
                echo $distintivoConSaltos = implode("<br />", $subcadena);
            }
        @endphp
            </span>
        @yield("head")
    </header>   
    
    <footer>
        @yield("footer")
        <div style="position: relative; display: flex; justify-content: flex-end;">
            <p class='direccion' style="text-align: right;">            
            @php
                if(isset($direccion)){
                    $direccion = explode("*", $direccion);
                   echo $direccionConSaltos = implode("<br />", $direccion);
                }
            @endphp
            </p>
        </div>
    </footer>       
    
    <content>
        @yield("content")
    </content>    
    @yield("body")
    @yield("js")
</body>
</html>

