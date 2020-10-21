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
            
                {{ Form::open(['route' => 'supervision.instructores', 'method' => 'post', 'class' => 'form-inline', 'enctype' => 'multipart/form-data' ]) }}                                                        
                    {{ Form::date('fecha', null , ['class' => 'form-control datepicker mr-sm-1', 'placeholder' => 'FECHA', 'readonly' =>'readonly']) }}                        
                    {{ Form::select('tipo_busqueda', array( 'nombre_instructor' => 'INSTRUCTOR','clave_curso' => 'CLAVE CURSO', 'nombre_curso' => 'CURSO' ), null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR POR'] ) }}                        
                    {{ Form::text('valor_busqueda', null, ['class' => 'form-control mr-sm-6', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) }}
                    {{ Form::button('FILTRAR', array('class' => 'btn', 'type' => 'submit')) }}
                {!! Form::close() !!}
                                 
                
        </div>
        <div class="row">                    
            @include('supervision.unidades.table_cursos')                    
        </div>
    </div>    
    <br>
    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>    
    <script src="{{ asset('js/supervision/datepicker.js') }}"></script>

</body>

</html>