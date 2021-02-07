<!--Creado por Orlando Chavez "Bit" - orlando@sidmac.com-->
@extends('theme.sivycSuperv.layout')
@section('title', 'Registro de Alumnos | Sivyc Icatech')
<head>
    <style>
        .checkbox-xl .custom-control-label::before,
        .checkbox-xl .custom-control-label::after {
        top: 1.2rem;
        width: 1.85rem;
        height: 1.85rem;
        }

        .checkbox-xl .custom-control-label {
        padding-top: 23px;
        padding-left: 10px;
        }

        td {
            text-align: center; /* center checkbox horizontally */
            vertical-align: middle; /* center checkbox vertically */
        }
        #choice-td{
            background-color: lightsteelblue;
        }
        table {
            border: 1px solid;
            width: 200px;
        }
        tr {
            height: 65px;
        }
        #form p {
        text-align: center;
        }

        #form label {
        font-size: 20px;
        }

        input[type="radio"] {
        display: none;
        }

        label.star {
        color: grey;
        font-size: 300%;
        }

        .clasificacion {
        direction: rtl;
        unicode-bidi: bidi-override;
        }

        label:hover,
        label:hover ~ label {
        color: orange;
        }

        input[type="radio"]:checked ~ label {
        color: orange;
        }
    </style>
</head>
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <div class="card-header text-center">
        <h1>{{$titulo->nombre}}</h1>
    </div>
    <div class="card card-body">
    @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
    @endif

    <section class="container g-py-40 g-pt-40 g-pb-0">
    @if(session('mensaje'))
        <div class="card text-gray bg-warning">
            <div class="card-header">
                <div class="row warning">
                    <div class="col-md-9 ">
                        <br />
                        {{ html_entity_decode(session('mensaje')) }}
                        <br />  <br />
                    </div>
                </div>
            </div>

        </div>
        <br />
    @else
        <form action="{{ route('encuesta.save') }}" method="post" id="frmencuesta" enctype="multipart/form-data" >
            @csrf
            @foreach ($encuesta as $count=>$data)
                @if ($data->respuestas == NULL && $data->dirigido_a == NULL)
                    <hr style="border-color:dimgray">
                    <div class="form-row">
                        <h2>{{$data->nombre}}</h2>
                    </div>
                @endif
                @if ($data->respuestas == NULL && $data->dirigido_a == 'abierto')
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="control-label">
                            <h3>{{$data->nombre}}</h3>
                        </label>
                    </div>
                    <div class="form-group col-md-8">
                        <textarea name="abierto" id="abierto" cols="60" rows="5" class="form-control" required></textarea>
                    </div>
                </div>
                @endif
                @if($data->respuestas != NULL)
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="control-label">
                                {{$data->nombre}}
                            </label>
                        </div>
                        <div class="form-group col-md-8">
                            <table  id="table-encuesta" class="table table-bordered table-responsive-md">
                                <thead>
                                    <tr>
                                        @foreach ($data->respuestas as $key=>$item)
                                            <th>{{$data->respuestas[$key]}}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->respuestas as $key=>$item)
                                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                                        <input type="radio" class="custom-control-input" id="{{$data->respuestas[$key]}}-{{$count}}" name="optradio[{{$data->id}}]" value="{{$data->respuestas[$key]}}" required>
                                            <label class="custom-control-label" for="{{$data->respuestas[$key]}}-{{$count}}"></label>
                                        </td>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
            <br>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <h2>Calificación del Instructor</h2>
                </div>
                <div class="form-group col-md-8">
                    <p class="clasificacion">
                        <input id="radio1" type="radio" name="estrellas" value="5">
                        <label for="radio1" class=star>★</label>
                        <input id="radio2" type="radio" name="estrellas" value="4">
                        <label for="radio2" class=star>★</label>
                        <input id="radio3" type="radio" name="estrellas" value="3">
                        <label for="radio3" class=star>★</label>
                        <input id="radio4" type="radio" name="estrellas" value="2">
                        <label for="radio4" class=star>★</label>
                        <input id="radio5" type="radio" name="estrellas" value="1">
                        <label for="radio5" class=star>★</label>
                    </p>
                </div>
            </div>
            <br>
            <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="control-label"><h3>Matricula</h3></label>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" name="matricula" id="matricula" class="form-control" required>
                    </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success">Finalizar</button>
                        <input type="text" name="token" id="token" hidden value="{{$urltoken}}">
                        <input type="text" name="id_encuesta" id="id_encuesta" hidden value="{{$titulo->id}}">
                    </div>
                    <div class="pull-left">
                        <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
                    </div>
                </div>
            </div>
        </form>
    @endif
    </section>
    </div>
   <script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
   <script src="{{ asset('js/supervisiones/validate.frmalumno.js') }}"></script>

@stop

