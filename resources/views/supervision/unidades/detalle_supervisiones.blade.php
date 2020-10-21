<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
<html lang="es">

    <head>
        <title>@yield('title', '')</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
        <!-- Google Fonts Roboto -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">

        <!-- CSS Global Compulsory -->        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"/>        
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="{{asset('css/globals.css') }}" />
    </head>
    </head>
  
  <body>
      <div class="card-header">
        Cursos Vigentes {{ $fecha }}
    </div>
    <div class="card card-body">
        @if ($message = Session::get('success'))
            <div class="row">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>   
        @endif 
       
        <div class="row">                    
           <div class="table-responsive">
    <table class="table ">                
        <thead>
            <tr>                
                <th scope="col">CLAVE</th>
                <th scope="col">CURSO</th>
                <th scope="col">INSTRUCTOR</th>
                <th scope="col" width="86px">INICIO</th>
                <th scope="col" width="86px">TERMINO</th>
                <th scope="col" width="87px">HORARIO</th>                
            </tr>
        </thead>
        <tbody>            
            @foreach ($data as $item)
                <tr>                                    
                     <td>{{ $item->numero_apertura }}</td>
                     <td>{{ $item->nombre_curso }}</td>
                     <td>{{ $item->nombre }}</td>
                     <td>{{ $item->inicio_curso }}</td>
                     <td>{{ $item->termino_curso }}</td>
                     <td>{{ $item->hini_curso }} - {{ $item->hfin_curso }}</td>                     
                </tr>
            @endforeach
                
        </tbody>
        <tfoot>             
        </tfoot>
    </table>
</div>                    
        </div>
    </div>    
    <br>    
    
</body>

</html>
