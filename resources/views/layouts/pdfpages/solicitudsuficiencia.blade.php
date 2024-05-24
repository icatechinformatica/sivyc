<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            body{
                font-family: sans-serif;
            }
            @page {
                margin: 110px 40px 110px;
            }
            header { position: fixed;
                left: 0px;
                top: -100px;
                padding-left: 45px;
                height: 70px;
                width: 85%;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            }
            header h1{
                margin: 10px 0;
            }
            header h2{
                margin: 0 0 10px 0;
            }
            footer {
                position: fixed;
                left: 0px;
                bottom: -90px;
                right: 0px;
                height: 100px;
                width: 85%;
                padding-left: 45px;
                background-color: white;
                color: black;
                text-align: center;
            }
            footer .page:after {
                content: counter(page);
            }
            footer table {
                width: 100%;
            }
            footer p {
                text-align: right;
            }
            footer .izq {
                text-align: left;
                }
            img.izquierda {
                float: left;
                width: 100%;
                height: 100%;
            }

            img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 100%;
            }

            div.content
            {
                margin-top: 60%;
                margin-bottom: 70%;
                margin-right: 25%;
                margin-left: 0%;
            }
            .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 0px;
                left: 65px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }
        </style>
    </head>
    <body>
        <header>
            <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
            <br><h6>{{$distintivo}}</h6>
        </header>
        <div id="wrapper">
            {!!$bodyTabla!!}
            @if(!is_null($uuid))
            @else
                <div align=center> <b>SOLICITA
                    <br>
                    <br><small>C. {{$funcionarios['director']}}</small>
                    <br>________________________________________
                    <br><small>{{$funcionarios['directorp']}}</small>
                </div>
            @endif
        </div>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
            <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
        </footer>
    </body>
</html>
