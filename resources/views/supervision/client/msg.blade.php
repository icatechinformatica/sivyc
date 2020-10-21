<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.sivycSuperv.layout')
@section('title', 'Registro de Instructor | Sivyc Icatech')
@section('content')         
    <section class="container g-py-40 g-pt-40 g-pb-0">
    
        <div class="card text-gray bg-warning">
            <div class="card-header">
                <div class="row warning">   
                    <div class="col-md-11 text-center">
                        <br />
                        <h5>
                        @if($msg)
                            {{ html_entity_decode($msg) }}
                        @endif
                        </h5>
                        <br />                  
                    </div>  
                </div>
            </div>
            
        </div>
        <br />
    
    </section>
    
@stop

