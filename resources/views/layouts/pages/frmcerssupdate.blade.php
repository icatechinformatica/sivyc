@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Formulario de CERSS | Sivyc Icatech')
<head>
    <style>
        .switch {
          position: relative;
          display: inline-block;
          width: 90px;
          height: 34px;
        }

        .switch input {
          opacity: 0;
          width: 0;
          height: 0;
        }

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        .slider:before {
          position: absolute;
          content: "";
          height: 26px;
          width: 26px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
        }

        input:checked + .slider {
          background-color: #2196F3;
        }

        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
          -webkit-transform: translateX(50px);
          -ms-transform: translateX(50px);
          transform: translateX(50px);
        }

        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }

        .slider.round:before {
          border-radius: 50%;
        }
    </style>
</head>
@section('content')
    <div class="container g-pt-50">
        <form action="{{ route('cerss.save-update') }}" method="post" id="registercerss">
            @csrf
            <div style="text-align: right;width:65%">
                <label for="tituloCERSS"><h1>Edición de CERSS</h1></label>
            </div>
            <hr style="border-color:dimgray">
            <h2>Status</h2>
            @if ($data->activo == true)
                <label class="switch">
                    <input id="status" name="status" type="checkbox" checked onclick="leyenda()">
                    <span class="slider round"></span>
                </label>
                <h5><p id="text1">CERSS Activo</p><p id="text2" style="display:none">CERSS Inactivo</p></h5>
            @else
                <label class="switch">
                    <input id="status" name="status" type="checkbox" onclick="leyenda()">
                    <span class="slider round"></span>
                </label>
                <h5><p id="text1" style="display:none">CERSS Activo</p><p id="text2">CERSS Inactivo</p></h5>
            @endif
            <br>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="nombre" class="control-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required value="{{$data->nombre}}" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="municipio" class="control-label">Municipio</label>
                    <select name="municipio" id="municipio" class="form-control" required disabled>
                        <option value={{$munisel->id}}>{{$munisel->muni}}</option>
                        @foreach ($muni as $cadwell )
                            <option value="{{$cadwell->id}}">{{$cadwell->muni}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="unidad" class="control-label">Unidad de Capacitación Asignada</label>
                    <select name="unidad" id="unidad" class="form-control" required>
                        <option value={{$unidadsel->id}}>{{$unidadsel->unidad}}</option>
                        @foreach ($unidad as $cadwell )
                            <option value="{{$cadwell->id}}">{{$cadwell->unidad}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="titular" class="control-label">Titular</label>
                    <input type="text" class="form-control" id="titular" name="titular" placeholder="Nombre del Titular" value="{{$data->titular}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="direccion" class="control-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" value="{{$data->direccion}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="telefono" class="control-label">Telefono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono" value="{{$data->telefono}}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                   <label for="telefono2" class="control-label">Telefono Adicional (opcional)</label>
                   <input type="text" class="form-control" id="telefono2" name="telefono2" placeholder="Telefono"  value="{{$data->telefono2}}">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                        <input type="text" name="idcerss" id="idcerss" hidden value="{{$data->id}}">
                    </div>
                </div>
            </div>
        </form>
        <br>
    </div>
    <script>
        function leyenda() {
          var checkBox = document.getElementById("status");
          var text1 = document.getElementById("text1");
          var text2 = document.getElementById("text2");
          if (checkBox.checked == true){
            text1.style.display = "block";
            text2.style.display = "none";
          } else {
             text1.style.display = "none";
             text2.style.display = "block";
          }
        }
        </script>
@endsection
