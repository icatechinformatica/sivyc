<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">    
    <title>@yield('title', '')</title>
    <style>   

    @page { margin: 5px 30px 80px 30px; }
    body { margin-top: 120px; font-family: sans-serif; font-size: 8px; width:100%; }
    header { position: fixed; top: 0cm; text-align: center; }
    footer { position: fixed; top: 17.7cm; width:100%;}
    .direccion {position: absolute;  top: 1.3cm; width:380px; margin-left:28px; height:auto; font-family: sans-serif; font-size: 9px; color:#FFF; }
    </style>
    @yield("content_script_css")   
</head>
<body>
    <header>
        <img src='img/formatos/bannerhorizontal.jpeg' width="100%">       
        <p style="text-align: center; font-weight: bold; font-style: italic; margin-top:-10px; padding:0px;">{{$distintivo}}</p>        
    </header> 
    <footer>       
        <div style="position: relative;";>            
            <img style=" position: absolute;" src='img/formatos/footer_horizontal.jpeg' width="100%"> 
            <p class='direccion'>{{ $direccion }}</p>
        </div>
    </footer>
    @yield("content")
    
    @yield("script_content_js")  
</body>
</html>