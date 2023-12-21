<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '')</title>
    <style>      
        body { font-family: sans-serif;}  
        content {font-family: sans-serif; font-size: 9px; width:100%; padding:0px; }
        header {text-align: center;}        
        footer { position: fixed; top: 14.2cm; width:100%;  background-color:green;}
        .direccion {position: absolute;  top: 1.3cm; width:300px; margin-left:25px; height:auto; font-family: sans-serif; font-size: 8px; color:#FFF; }
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
<body>    
    
    <header>
        <img src='img/formatos/bannervertical.jpeg' width="100%">
        <span style="margin-top:5px; padding:0px; font-size: 14px; font-style: italic; display:block;">            
            @if(isset($distintivo)){{ $distintivo }}@endif
            </span>
        @yield("head")
    </header>   
    
    <footer>
        @yield("footer")
        <div style="position: relative;";>
            <img style=" position: absolute;" src='img/formatos/footer_vertical.jpeg' width="100%">
            @php 
                $direccion = str_replace("*", " ", $direccion);                
            @endphp
            <p class='direccion'><b>{{$direccion}}</b></p>
        </div>
    </footer>       
    
    <content>
        @yield("content")
    </content>    
    @yield("body")
    @yield("js")
</body>
</html>

