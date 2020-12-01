<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.sivycSuperv.layout')
@section('title', 'Detalles del Curso| SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>     
        .tabla{  border-collapse: collapse; width: 100%; }        
        .tabla tr td, .tabla tr th{ font-size: 12px; border: gray 1px solid; padding: 3px;}
        .tab{ margin-left: 10px; margin-right: 20px;}
     </style>
     @if($mensaje)
            <div class="card text-gray bg-warning">
                <br />
                    <div class="row warning">   
                        <div class="col-md-9 text-center">
                            <br />
                            {{ html_entity_decode($mensaje) }}
                            <br />  <br />                  
                        </div>  
                    </div>
                
                
            </div>
            <br />
     @else
        <div class="card-header">
            Reportes de Cursos Autorizados
            
        </div>
        <div class="card card-body" >            
             <table class="tabla">
                <thead>
                    <tr>
                        <th colspan="5">
                            <div> UNIDAD DE CAPACITACI&Oacute;N: <b>{{$curso->plantel}}&nbsp; {{ $curso->unidad }}</b></div>                            
                            <div> CURSO: <b>{{ $curso->curso }}</b>&nbsp;&nbsp;  CLAVE: <b>{{ $curso->clave }}</b></div>                           
                            <div>
                                AREA: <span class="tab">{{ $curso->area }}</span>                
                                ESPECIALIDAD: <span class="tab">{{ $curso->espe }}</span>
                                FECHA INICIO: <span class="tab"> {{ $curso->fechaini }}</span>
                                FECHA TERMINO: <span class="tab"> {{ $curso->fechafin }}</span>
                                HORARIO: <span class="tab"> {{ $curso->dia}} DE {{ $curso->hini }} A {{ $curso->hfin }}</span>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="5">
                         INSTRUCTOR: <b>{{ $curso->nombre }}</b>&nbsp;&nbsp; TELEFONO: <b>{{$instructor->telefono}}</b> &nbsp;&nbsp; CORREO: <b>{{$instructor->correo}}</b>
                        </th>
                        
                    </tr>               
                
                    <tr>
                        <th width="15px">#</th>
                        <th width="100px">NUMERO DE <br/>CONTROL</th>
                        <th width="320px">NOMBRE DEL ALUMNO</th>
                        <th width="320px">TELEFONO</th>
                        <th width="320px">CORREO ELECTRONICO</th>
                    </tr>               
                                    
                </thead>
                <tbody>   
                @foreach($alumnos as $a)         
                    <tr>
                        <td>{{ $consec++ }}</td>
                        <td>{{ $a->matricula }}</td>
                        <td>{{ $a->alumno }}</td>                        
                        <td>{{ $a->telefono }}</td>
                        <td>{{ $a->correo }}</td>
                    </tr> 
                    @endforeach
                </tbody> 
                <tfoot>            
                </tfoot>          
            </table>          
        </div>
    @endif    
@endsection
