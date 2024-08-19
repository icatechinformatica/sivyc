<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Validación de Suficiencia Presupuestal| Sivyc Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <div class="card-header">
        <h1>Validacion de Suficiencia Presupuestal</h1>
    </div>
    <div class="card card-body" style=" min-height:450px;">
        <form method="POST" action="{{ route('supre-rechazo') }}" id="rechazosupre">
            @csrf
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="dropfecha_apertura">Fecha de Apertura ARC-01</label>
                    <input name="fecha_apertura" id="fecha_apertura" type="date" disabled value="{{$fecha_apertura}}" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="dropno_memo">Numero de Memorandum</label>
                    <input name="no_memo" id="no_memo" type="text" disabled value="{{$data->no_memo}}" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="dropfecha_memo">Fecha de Memorandum</label>
                    <input name="fecha_memo" id="fecha_memo" type="date" disabled value="{{$data->fecha}}" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
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
                <br>
                @php $supreIdB64 = base64_encode($data->id); @endphp
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <a class="btn btn-primary" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="form-group col-md-2">
                        <button type="button" id="valsupre_rechazar" name="valsupre_rechazar" class="btn btn-danger">Rechazar</a>
                    </div>
                    <div class="form-group col-md-5">
                        <button type="button" id="valsupre_validar" name="valsupre_validar" class="btn" style="background-color: #12322B; color: white;">Validar</a>
                    </div>
                    <div class="form-group col-md-3">
                        <a type="submit" id="btn_generar_supre" class="btn btn-primary" @if(is_null($data->doc_supre)) href="{{route('supre-pdf', ['id' => $supreIdB64])}}" @else href="{{$data->doc_supre}}" @endif target="_blank">Visualizar Solicitud</a>
                    </div>
                </div>
                <div id="divrechazar" class="form-row d-none d-print-none">
                    <div class="form-group col-md-6">
                        <label for="inputcomentario_rechazo">Describa el motivo de rechazo</label>
                        <textarea name="comentario_rechazo" id="comentario_rechazo" cols="6" rows="6" class="form-control"></textarea>
                    </div>
                </div>
                <div id="divconf_rechazar" class="form-row d-none d-print-none">
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-danger" >Confirmar Rechazo</button>
                        <input hidden id="id" name="id" value="{{$data->id}}">
                    </div>
                </div>
                <br>
        </form>
        <form method="POST" action="{{ route('supre-validado') }}" id="validadosupre">
            @csrf
                <div id="div1" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <label for="inputfolio_validacion">Folio de Validación</label>
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <p class="form-control" style="border: 0px; align-text:right;">ICATECH/500.1/H/</p>
                            </div>
                            <div class="form-group col-md-3" style="margin-right: -10px;">
                                <input  type="text" name="folio_validacion" id="folio_validacion" class="form-control" required>
                            </div>
                            <div class="form-group col-md-2">
                                <p id='ejercicio' name ='ejercicio' class="form-control" style="border: 0px;">/{{$year}}</p>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="form-group col-md-4">
                        <label for="inputfolio_validacion">Folio de Validación</label>
                        <input  type="text" name="folio_validacion" id="folio_validacion" class="form-control" required>
                    </div> --}}
                    <div class="form-group col-md-2">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input name="fecha_val" id="fecha_val" type="date" class="form-control" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputfinanciamiento">Fuente de Financiamiento</label>
                        <select class="form-control" name="financiamiento" id="financiamiento" required>
                            {{-- <option value="">SELECCIONE</option> --}}
                            <option value="FEDERAL" @if($criterio_pago->cp != '12') selected @endif>FEDERAL</option>
                            {{-- <option value="ESTATAL">ESTATAL</option> --}}
                            <option value="FEDERAL Y ESTATAL" @if($criterio_pago->cp == '12') selected @endif>FEDERAL Y ESTATAL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputobservacion">Observación</label>
                        <textarea style="text-transform: none;" name="observacion" id="observacion" cols="6" rows="6" class="form-control"></textarea>
                    </div>
                </div>
                <hr style="border-color:dimgray">
                <div id="div3" class="d-none d-print-none">
                    <h3>Con Copia Para El Delegado:</h3>
                </div>
                </div>
                <div id="div7" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp4" id="ccp4" class="form-control" placeholder="Nombre Completo" value="{{$funcionarios['delegado']}}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa4" id="ccpa4" readonly class="form-control" placeholder="puesto" value="{{$funcionarios['delegadop']}}" readonly>
                    </div>
                </div>
                <div id="confval" class="row d-none d-print-none">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            <button type="submit" class="btn" style="background-color: #12322B; color: white;">Confirmar Validación</button>
                            <input hidden id="id" name="id" value="{{$data->id}}">
                        </div>
                    </div>
                </div>
                <br>
        </form>
    </section>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
<script>
    // Obtener el elemento de input de fecha
    var inputFecha = document.getElementById('fecha_val');
    // Obtener la fecha actual en el formato 'YYYY-MM-DD'
    var fechaActual = new Date().toISOString().split('T')[0];
    // Establecer la fecha actual como valor inicial del campo de fecha
    inputFecha.value = fechaActual;

    const fechaInput = document.getElementById('fecha_val');
    const ejercicioP = document.getElementById('ejercicio');
    let previousYear = new Date(fechaInput.value).getFullYear();

    fechaInput.addEventListener('change', function() {
        const currentYear = new Date(fechaInput.value).getFullYear();
        if (currentYear !== previousYear) {
            // console.log('El año ha cambiado');
            ejercicioP.textContent = '/'+currentYear;
            previousYear = currentYear;
        }
    });
</script>
@endsection
