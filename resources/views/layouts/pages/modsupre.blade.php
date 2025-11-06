@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<style>
    a.add_button, a.remove_button {
        position: absolute;
        left: 20px;
        top: 30px;
        z-index: 999;
        height: 34px;
        width: 34px;
      }
</style>
<div class="card-header">
    Modificación de Solicitud para Suficiencia Presupuestal
</div>
<div class="card card-body" style=" min-height:450px;">
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
   <form action="{{ route('supre-mod-save') }}" id="regsupre" method="POST">
       @csrf
        @if(!is_null($getsupre->observacion))
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputobservacion" class="control-label"><b>Observaciones de Rechazo</b></label>
                    <textarea cols="4" rows="4" type="text" class="form-control" readonly aria-required="true" onkeypress="return soloLetras(event)" id="observacion" name="observacion">{{ $getsupre->observacion}}</textarea>
                </div>
            </div>
        @endif
        <br>
        <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="unidad" class="control-label">Unidad de Capacitación </label>
                <input type="text" class="form-control" id="unidad" name="unidad" value="{{$unidadsel->unidad}}" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="mamorandum" class="control-label">Memoramdum No. </label>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <p class="form-control" style="border: 0px;">ICATECH/{{$unidadsel->clave_contrato}}/</p>
                    </div>
                    <div class="form-group col-md-2" style="margin-right: -10px;">
                        <input type="text" class="form-control" id="no_memo" name="no_memo" aria-required="true" value="{{ $getsupre->no_memo[2] }}" placeholder="ICATECH/0000/000/2020">
                    </div>
                    <div class="form-group col-md-2">
                        <p id="ejercicio" name="ejercicio" class="form-control" style="border: 0px;">/{{$getsupre->no_memo[3]}}</p>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-2">
                <label for="fecha" class="control-label">Fecha</label>
                <input class="form-control" name="fecha" type="date" aria-required="true" value="{{ $getsupre->fecha }}" id="fecha">
            </div>
        </div>
        <div class="field_wrapper">
            <table class="table table-bordered" id="dynamicTablemodsupre">
                <tr>
                    <th>Folio</th>
                    <th>Numero Presupuesto</th>
                    <th>Clave Curso</th>
                    <th>Importe total</th>
                    <th id='thiva'>Iva</th>
                    <th>Observación</th>
                    {{-- <th>Acción</th> --}}
                </tr>
                @foreach ( $getfolios as $key=>$data )
                <tr>
                    <td><input type="text" id="addmore[{{$key}}][folio]" name="addmore[{{$key}}][folio]" value="{{ $data->folio_validacion }}" placeholder="folio" class="form-control"/></td>
                    <td><input readonly type="text" id="addmore[{{$key}}][numeropresupuesto]" name="addmore[{{$key}}][numeropresupuesto]" value="12101" placeholder="numero presupuesto" class="form-control" /></td>
                    <td><input type="text" id="addmore[{{$key}}][clavecurso]" name="addmore[{{$key}}][clavecurso]" value="{{ $data->clave}}" placeholder="clave curso" class="form-control" /><small name="addmore[{{$key}}][aviso]" id="addmore[{{$key}}][aviso]" style="color: red; display: block;"></small></td>
                    <td><input type="text" id="addmore[{{$key}}][importe]" name="addmore[{{$key}}][importe]" value="{{ $data->importe_total }}" placeholder="importe total" class="form-control" readonly /></td>
                    <td id="tdiva"><input type="text" id="addmore[{{$key}}][iva]" name="addmore[{{$key}}][iva]" value="{{ $data->iva }}" placeholder="Iva" class="form-control" readonly /></td>
                    <td><input type="text" id="addmore[{{$key}}][comentario]" name="addmore[{{$key}}][comentario]" value="{{ $data->comentario }}" placeholder="comentario" class="form-control" /></td>
                    <input hidden id="addmore[{{$key}}][id_cursos]" name="addmore[{{$key}}][id_cursos]" value="{{$data->id_cursos}}">
                </tr>

                @endforeach
                @if(isset($key))
                    <input hidden id='wa' value={{$key}}>
                @else
                    <tr>
                        <td><input type="text" name="addmore[0][folio]" id="addmore[0][folio]" placeholder="folio" class="form-control" /><small name="addmore[0][avisofolio]" id="addmore[0][avisofolio]" style="color: red; display: block;"></small></td>
                        <td><input type="text" name="addmore[0][numeropresupuesto]" id="addmore[0][numeropresupuesto]" placeholder="número presupuesto" class="form-control" disabled value="12101" /></td>
                        <td><input type="text" name="addmore[0][clavecurso]" id="addmore[0][clavecurso]" placeholder="clave curso" class="form-control claveCurso" /><small name="addmore[0][aviso]" id="addmore[0][aviso]" style="color: red; display: block;"></small></td>
                        <td><input type="text" name="addmore[0][importe]" id="addmore[0][importe]" placeholder="importe total" class="form-control" readonly/></td>
                        <td><input type="text" name="addmore[0][iva]" id="addmore[0][iva]" placeholder="IVA" class="form-control" readonly /></td>
                        <td><input type="text" name="addmore[0][comentario]" id="addmore[0][comentario]" placeholder="Comentario" class="form-control" /></td>
                        <td><button type="button" name="add" id="add" class="btn btn-success">Agregar</button></td>
                    </tr>
                @endif
            </table>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputremitente" class="control-label">Remitente</label>
                <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre" readonly value="{{$funcionarios['director']}}">
            </div>
            <div class="form-group col-md-6">
                <label for="inputremitente" class="control-label">Puesto</label>
                <input type="text" readonly class="form-control" onkeypress="return soloLetras(event)" id="remitente_puesto" name="remitente_puesto" placeholder="Puesto" readonly value="{{$funcionarios['directorp']}}">
                <input id="id_remitente" name="id_remitente" type="text" hidden>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputvalida" class="control-label">Nombre de Quien Valida</label>
                <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="nombre_valida" name="nombre_valida" placeholder="Nombre" readonly value="{{$funcionarios['director']}}">
            </div>
            <div class="form-group col-md-4">
                <label for="inputvalida" class="control-label">Puesto de Quien Valida</label>
                <input type="text" class="form-control" readonly onkeypress="return soloLetras(event)" id="puesto_valida" name="puesto_valida" placeholder="Puesto" readonly value="{{$funcionarios['directorp']}}">
                <input id="id_valida" name="id_valida" type="text" hidden>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputelabora" class="control-label">Nombre de Quien Elabora</label>
                <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="nombre_elabora" name="nombre_elabora" placeholder="Nombre" readonly value="{{$funcionarios['delegado']}}">
            </div>
            <div class="form-group col-md-4">
                <label for="inputelabora" class="control-label">Puesto de Quien Elabora</label>
                <input type="text" class="form-control" readonly onkeypress="return soloLetras(event)" id="puesto_elabora" name="puesto_elabora" placeholder="Puesto" readonly value="{{$funcionarios['delegadop']}}">
                <input id="id_elabora" name="id_elabora" type="text" hidden>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Datos de Pago de Curso
        </h2>
        <div id="fieldsContainer">
                    <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="inputnorecibo" class="control-label">Folio de Recibo de Pago</label>
                                <input type="text" name="no_recibo" id="no_recibo" value="{{$recibo->folio_recibo}}" class="form-control" readonly />
                            </div>
                        <div class="form-group col-md-3">
                            <label for="fecha_expedicion">Fecha de Expedición</label>
                            <input type="date" class="form-control" id="fecha_expedicion" value="{{$recibo->fecha_expedicion}}" name="fecha_expedicion" readonly>
                        </div>
                    </div>
        </div>
        <br><br><br>
        @php $supreIdB64 = base64_encode($getsupre->id); @endphp
        <div class="form-row">
            <input hidden id=id_supre name="id_supre" value={{$getsupre->id}}>
            <div class="form-group col-md-8">
                <a class="btn" style="background-color: #12322B; color: white;" href="{{URL::previous()}}">Regresar</a>
            </div>
            <div class="form-group col-md-2">
                <button type="submit" id="btn_guardar_supre" class="btn" style="background-color: #12322B; color: white;" >Guardar</button>
            </div>
            <div class="form-group col-md-2">
                <a type="submit" id="btn_generar_supre" class="btn btn-primary" href="{{route('supre-pdf', ['id' => $supreIdB64])}}"  target="_blank">Visualizar PDF</a>
            </div>
            </form>
            @if($generarEfirmaSupre)
                <div class="form-group col-md-3">
                    <form action="{{ route('supre-efirma') }}" method="post" id="registersolicitudpago">
                        @csrf
                        <input type="text" name="ids" id="ids" value='{{$getsupre->id}}' hidden>
                        <input type="text" name="clave_curso" id="clave_curso" value='{{$data->clave}}' hidden>
                        <button button type="submit" class="btn btn-red" >Generar Suficiencia E.Firma</button>
                    </form>
                </div>
            @endif
        </div>
        <br>
    </form>
 </div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script type="text/javascript">

    function textInput(id) {
        const input = document.createElement("input");
        input.type = "text";
        input.className = 'form-control';
        input.id = id;
        input.name = id;
        return input;
    }

    function dateInput(id) {
        const input = document.createElement("input");
        input.type = "date";
        input.className = 'form-control';
        input.id = id;
        input.name = id;
        return input;
    }

    function textLabel(text) {
        const label = document.createElement("label");
        label.innerHTML = text;
        return label;
    }

     // Add the following code if you want the name of the file appear on select
     $(".custom-file-input").on("change", function() {
       var fileName = $(this).val().split("\\").pop();
       $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
     });

     function soloLetras(e) {
       key = e.keyCode || e.which;
       tecla = String.fromCharCode(key).toLowerCase();
       letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
       especiales = [8, 37, 39, 46];

       tecla_especial = false
       for(var i in especiales) {
           if(key == especiales[i]) {
               tecla_especial = true;
               break;
           }
       }

       if(letras.indexOf(tecla) == -1 && !tecla_especial)
           return false;
   }
   function limpia() {
       var val = document.getElementById("miInput").value;
       var tam = val.length;
       for(i = 0; i < tam; i++) {
           if(!isNaN(val[i]))
               document.getElementById("miInput").value = '';
       }
   }

    const fechaInput = document.getElementById('fecha');
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
@section('script_content_js')
<script src="{{ asset("js/validate/supre.js") }}"></script>
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/adrianValidate.js") }}"></script>
{{-- <script src="{{ asset("js/validate/orlandoBotones.js") }}"></script> --}}
<script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
@endsection

