<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Modificación de la Validación de Suficiencia Presupuestal| Sivyc Icatech')
@section('content_script_css')
    <style>
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
            z-index: 9999; /* Asegura que esté por encima de otros elementos */
            display: none;
        }

        #loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 6px solid #fff;
            border-top: 6px solid #621132;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
@endsection
@section('content')
<div id="loader-overlay">
    <div id="loader"></div>
</div>
    <section class="container g-pt-50">
        <div class="text-center">
            <h1>Modificación de la Validación de Suficiencia Presupuestal</h1>
        </div>
        <br>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form method="POST" action="{{ route('supre-validado') }}">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="dropno_memo">Numero de Memorandum</label>
                <input name="no_memo" id="no_memo" type="text" disabled value="{{$data->no_memo}}" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label for="dropfecha_memo">Fecha de Memorandum</label>
                <input name="fecha_memo" id="fecha_memo" type="date" disabled value="{{$data->fecha}}" class="form-control">
                </div>
                <div class="form-group col-md-5">
                    <label for="dropcrit_pago">Criterio de Pago</label>
                    <input name="crit_pago" id="crit_pago" type="text" disabled value="{{$criterio_pago->cp}} - {{$criterio_pago->perfil_profesional}}" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="drouni_cap">Unidad de Capacitación</label>
                    <input name="uni_cap" id="uni_cap" type="text" disabled value="{{$data->unidad_capacitacion}}"  class="form-control">
                </div>
                <div class="form-group col-md-4">
                    <label for="droparea">Area de Adscripcion</label>
                    <input name="area" id="area" type="text" disabled value="{{$funcionarios['directorp']}}" class="form-control">
                </div>
                <div class="form-group col-md-4">
                    <label for="dropnombre_dir">Nombre del Director de Unidad</label>
                    <input name="nombre_dir" id="nombre_dir" type="text" disabled value="{{$funcionarios['director']}}" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    @php $supreIdB64 = base64_encode($data->id); @endphp
                    <a type="submit" id="btn_generar_supre" class="btn btn-primary" @if(is_null($data->doc_supre)) href="{{route('supre-pdf', ['id' => $supreIdB64])}}" @else href="{{$data->doc_supre}}" @endif target="_blank">Visualizar Solicitud</a>
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputfolio_validacion">Folio de Validación</label>
                    <input  type="text" name="folio_validacion" id="folio_validacion" class="form-control" value="{{$data->folio_validacion}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputfecha_validacion">Fecha de Validación</label>
                    <input name="fecha_val" id="fecha_val" type="date" class="form-control" value="{{$data->fecha_validacion}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputfinanciamiento">Fuente de Financiamiento</label>
                    <select class="form-control" name="financiamiento" id="financiamiento" required>
                        <option value="">SELECCIONE</option>
                        <option value="FEDERAL" @if($data->financiamiento == 'FEDERAL') selected @endif>FEDERAL</option>
                        <option value="ESTATAL" @if($data->financiamiento == 'ESTATAL') selected @endif>ESTATAL</option>
                        <option value="FEDERAL Y ESTATAL" @if($data->financiamiento == 'FEDERAL Y ESTATAL') selected @endif>FEDERAL Y ESTATAL</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputobservacion">Observación</label>
                    <textarea name="observacion" id="observacion" cols="6" rows="6" class="form-control">{{$data->observacion_validacion}}</textarea>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <h3>Con Copia Para El Delegado:</h3>
            <div id="div7" class="form-row ">
                <div class="form-group col-md-4">
                    <input  type="text" name="ccp4" id="ccp4" class="form-control" placeholder="Nombre Completo" value="{{$funcionarios['delegado']}}" readonly>
                </div>
                <div class="form-group col-md-4">
                    <input name="ccpa4" id="ccpa4" readonly class="form-control" placeholder="puesto" value="{{$funcionarios['delegadop']}}" readonly>
                </div>
            </div>
            <br><br>
            <div id="confval" class="form-row ">
                <div class="form-group col-md-6">
                    <div class="pull-right">
                        <button type="submit" class="btn" style="background-color: #12322B; color: white;" >Guardar Cambios</button>
                        <input hidden id="id" name="id" value="{{$data->id}}">
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <a type="submit" id="btn_generar_supre" class="btn btn-primary" href="{{route('valsupre-pdf', ['id' => $supreIdB64])}}"  target="_blank">Visualizar Validación PDF</a>
                </div>
            </form>
            @if($generarEfirmaValsupre)
                <div class="form-group col-md-3">
                    <form action="{{ route('valsupre-efirma') }}" method="post" id="registersolicitudpago">
                        @csrf
                        <input type="text" name="ids" id="ids" value='{{$data->id}}' hidden>
                        <input type="text" name="clave_curso" id="clave_curso" value='{{$clave}}' hidden>
                        <button button type="submit" class="btn btn-red" onclick="loader('show')">Generar Validación E.Firma</button>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script>
    function loader(make) {
            if(make == 'hide') make = 'none';
            if(make == 'show') make = 'block';
            document.getElementById('loader-overlay').style.display = make;
        }
</script>
@endsection
