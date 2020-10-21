<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.globals.layout2')
@section('title', 'Supervisión de Unidades | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/oficinas.css') }}" />
    <div class="card-header">
        Supervisi&oacute;n de Funcionario
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
            <div class="accordion" id="accordionExample">
            <?php $n=$abierto=0;?>
            @foreach($data as $item)
                <?php
                    $c = explode('-',$item->clave);                    
                    //var_dump($c); exit;
                ?>
                @if($c[1]=="00" AND $c[2]=="00" )
                    <button class="btn btn-link btn-ofic" type="button" data-toggle="collapse" data-target="#collapse{{$n}}" aria-expanded="true" aria-controls="collapse{{$n}}">
                        {{ $item->oficina }}
                    </button> <br />
                @else
                    @if($item->niveles==false)
                        <button class="btn btn-link btn-ofic" type="button" data-toggle="collapse" data-target="#collapse{{$n}}" aria-expanded="true" aria-controls="collapse{{$n}}">
                        {{ $item->oficina }}
                        </button>
                        <br />
                    @else
                      <div class="card col-md-6">
                        <?php $n++; ?> 
                        @if($c[2]=="00")
                            @if($abierto==1)
                                </div>
                            </div>          
                            @endif              
                            <div class="card-header ofic-header" id="heading{{$n}}">
                              <h2 class="mb-6">                    
                                <button class="btn btn-link btn-ofic" type="button" data-toggle="collapse" data-target="#collapse{{$n}}" aria-expanded="true" aria-controls="collapse{{$n}}">
                                  {{ $item->oficina }}
                                </button>
                              </h2>
                            </div>
                            <div id="collapse{{$n}}" class="collapse" aria-labelledby="heading{{$n}}" data-parent="#accordionExample">
                              <div class="card-body">
                              <?php $abierto=1; ?>
                        @elseif($c[2]<>"00")                        
                                {{ $item->oficina }}                          
                        @endif
                      </div>
                  @endif
               @endif
            @endforeach
        </div>
        
        <div class="row">                    
                      
        </div>
    </div>    
    <br>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
@endsection