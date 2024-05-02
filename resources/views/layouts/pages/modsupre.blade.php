@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('content')
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
 <div class="container g-pt-50">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
   <form action="{{ route('supre-mod-save') }}" id="regsupre" method="POST">
       @csrf
       <div style="text-align: right;width:82%">
           <label for="tituloSupre1"><h2>Modificación de Solicitud para Suficiencia Presupuestal</h2></label>
        </div>
        <br><br>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputobservacion" class="control-label"><b>Observaciones de Rechazo</b></label>
                <textarea cols="4" rows="4" type="text" class="form-control" readonly aria-required="true" onkeypress="return soloLetras(event)" id="observacion" name="observacion">{{ $getsupre->observacion}}</textarea>
            </div>
        </div>
        <br>
        <hr style="border-color:dimgray">
        <div class="form-row">
            {{-- <div class="form-group col-md-3">
                <label for="unidad" class="control-label">Unidad de Capacitacion </label>
                <select name="unidad" id="unidad" class="form-control">
                    <option selected value="{{$unidadsel->unidad}}">{{$unidadsel->unidad}}</option>
                    @foreach ($unidadlist as $data )
                        <option value="{{$data->unidad}}">{{$data->unidad}}</option>
                    @endforeach
                </select>
            </div> --}}
            <div class="form-group col-md-3">
                <label for="unidad" class="control-label">Unidad de Capacitación </label>
                <input type="text" class="form-control" id="unidad" name="unidad" value="{{$unidadsel->unidad}}" readonly>
            </div>
            <div class="form-group col-md-5">
                <label for="mamorandum" class="control-label">Memorandum No. </label>
                <input type="text" class="form-control" disabled id="no_memo" name="no_memo" aria-required="true" value="{{ $getsupre->no_memo }}" placeholder="ICATECH/0000/000/2020">
            </div>
            <div class="form-group col-md-2">
                <label for="fecha" class="control-label">Fecha</label>
                <input class="form-control" name="fecha" disabled type="date" aria-required="true" value="{{ $getsupre->fecha }}" id="fecha">
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
                    <td><input type="text" id="addmore[{{$key}}][folio]" name="addmore[{{$key}}][folio]" value="{{ $data->folio_validacion }}" placeholder="folio" class="form-control" /></td>
                    <td><input readonly type="text" id="addmore[{{$key}}][numeropresupuesto]" name="addmore[{{$key}}][numeropresupuesto]" value="12101" placeholder="numero presupuesto" class="form-control" /></td>
                    <td><input type="text" id="addmore[{{$key}}][clavecurso]" name="addmore[{{$key}}][clavecurso]" value="{{ $data->clave}}" placeholder="clave curso" class="form-control" /></td>
                    <td><input type="text" id="addmore[{{$key}}][importe]" name="addmore[{{$key}}][importe]" value="{{ $data->importe_total }}" placeholder="importe total" class="form-control" readonly /><footer name="addmore[0][aviso]" id="addmore[0][aviso]" style="color: red"></footer></td>
                    <td id="tdiva"><input type="text" id="addmore[{{$key}}][iva]" name="addmore[{{$key}}][iva]" value="{{ $data->iva }}" placeholder="Iva" class="form-control" readonly /></td>
                    <td><input type="text" id="addmore[{{$key}}][comentario]" name="addmore[{{$key}}][comentario]" value="{{ $data->comentario }}" placeholder="comentario" class="form-control" /></td>
                    <input hidden id="addmore[{{$key}}][id_cursos]" name="addmore[{{$key}}][id_cursos]" value="{{$data->id_cursos}}">
                    {{-- @if ($key == 0)
                    <td><button type="button" name="addmodsupre" id="addmodsupre" class="btn btn-success">Agregar</button></td>
                    @else
                    <td><button type="button" class="btn btn-danger remove-trmodsupre">Eliminar</button></td>
                    @endif --}}
                </tr>

                @endforeach
                @if(isset($key))
                    <input hidden id='wa' value={{$key}}>
                @else
                    <tr>
                        <td><input type="text" name="addmore[0][folio]" id="addmore[0][folio]" placeholder="folio" class="form-control" /><footer name="addmore[0][avisofolio]" id="addmore[0][avisofolio]" style="color: red"></footer></td>
                        <td><input type="text" name="addmore[0][numeropresupuesto]" id="addmore[0][numeropresupuesto]" placeholder="número presupuesto" class="form-control" disabled value="12101" /></td>
                        <td><input type="text" name="addmore[0][clavecurso]" id="addmore[0][clavecurso]" placeholder="clave curso" class="form-control claveCurso" /></td>
                        <td><input type="text" name="addmore[0][importe]" id="addmore[0][importe]" placeholder="importe total" class="form-control" readonly/><footer name="addmore[0][aviso]" id="addmore[0][aviso]" style="color: red"></footer></td>
                        <td><input type="text" name="addmore[0][iva]" id="addmore[0][iva]" placeholder="IVA" class="form-control" readonly /></td>
                        <td><input type="text" name="addmore[0][comentario]" id="addmore[0][comentario]" placeholder="Comentario" class="form-control" /></td>
                        <td><button type="button" name="add" id="add" class="btn btn-success">Agregar</button></td>
                    </tr>
                @endif
            </table>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6"> <!--  -->
                <label for="inputremitente" class="control-label">Remitente</label>
                <input type="text" class="form-control" readonly value="{{$funcionarios['director']}}" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre">
            </div>
            <div class="form-group col-md-6"> <!--  -->
                <label for="inputremitente" class="control-label">Puesto</label>
                <input type="text" class="form-control" readonly value="{{$funcionarios['directorp']}}" onkeypress="return soloLetras(event)" id="remitente_puesto" name="remitente_puesto" placeholder="Puesto">
                {{-- <input id="id_remitente" name="id_remitente" type="text" value="{{$getremitente->id}}" hidden> --}}
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="remitente" class="control-label">Nombre de Quien Valida</label>
                <input type="text" class="form-control" readonly value="{{$funcionarios['director']}}" onkeypress="return soloLetras(event)" id="nombre_valida" name="nombre_valida" placeholder="Nombre">
            </div>
            <div class="form-group col-md-4">
                <label for="remitente" class="control-label">Puesto de Quien Valida</label>
                <input type="text" class="form-control" readonly value="{{$funcionarios['directorp']}}" onkeypress="return soloLetras(event)" id="puesto_valida" name="puesto_valida" placeholder="Puesto">
                {{-- <input id="id_valida" name="id_valida" type="text" value="{{$getvalida->id}}" hidden> --}}
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="remitente" class="control-label">Nombre de Quien Elabora</label>
                <input type="text" class="form-control" readonly value="{{$funcionarios['delegado']}}" onkeypress="return soloLetras(event)" id="nombre_elabora" name="nombre_elabora" placeholder="Nombre">
            </div>
            <div class="form-group col-md-4">
                <label for="remitente" class="control-label">Puesto de Quien Elabora</label>
                <input type="text" class="form-control" readonly value="{{$funcionarios['delegadop']}}" onkeypress="return soloLetras(event)" id="puesto_elabora" name="puesto_elabora" placeholder="Puesto">
                {{-- <input id="id_elabora" name="id_elabora" type="text" value="{{$getelabora->id}}" hidden> --}}
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Datos de Pago de Curso
            <button type="button" class="btn btn-primary float-right" onclick="addField()">Añadir Movimiento</button>
        </h2>
        <div id="fieldsContainer">
            {{-- @if(isset($data->mov_bancario)) --}}
            {{-- @php $data->mov_bancario = json_decode($data->mov_bancario) @endphp --}}
                {{-- @foreach ($data->mov_bancario as $keygen => $movitem) --}}
                    <div class="form-row">
                        {{-- @if ($keygen == 0) --}}
                            <div class="form-group col-md-3">
                                <label for="inputnorecibo" class="control-label">Folio de Recibo de Pago</label>
                                <input type="text" name="no_recibo" id="no_recibo" value="{{$recibo->folio_recibo}}" class="form-control" readonly />
                            </div>
                        {{-- @endif --}}
                        {{-- <div class="form-group col-md-3">
                            <label for="movimiento_bancario_{{$keygen}}">Movimiento Bancario</label>
                            <input type="text" class="form-control" id="movimiento_bancario_0" value="{{$movitem->movimiento_bancario}}" name="movimiento_bancario_[{{$keygen}}]">
                        </div> --}}
                        <div class="form-group col-md-3">
                            <label for="fecha_expedicion">Fecha de Expedición</label>
                            <input type="date" class="form-control" id="fecha_expedicion" value="{{$recibo->fecha_expedicion}}" name="fecha_expedicion" readonly>
                        </div>
                    </div>
                {{-- @endforeach --}}
            {{-- @else
                <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="inputnorecibo" class="control-label">Numero de Recibo de Pago</label>
                            <input type="text" name="norecibo" id="norecibo" value="{{$data->folio_pago}}" class="form-control" readonly />
                        </div>
                    <div class="form-group col-md-3">
                        <label for="movimiento_bancario_0">Movimiento Bancario</label>
                        <input type="text" class="form-control" id="movimiento_bancario_0" value="{{$movitem->movimiento_bancario}}" name="movimiento_bancario_[0]">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_movimiento_bancario_{{$keygen}}">Fecha de Movimiento</label>
                        <input type="date" class="form-control" id="fecha_movimiento_bancario_0" value="{{$movitem->fecha_movimiento_bancario}}" name="fecha_movimiento_bancario_[0]">
                    </div>
                </div>
            @endif --}}
        </div>
        {{-- <input id="keyfield" name="keyfield" hidden value="{{$keygen}}"> --}}
        {{-- <input id="id_directorio" name="id_directorio" hidden value="{{$directorio->id}}"> --}}
        {{-- <button type="button" id="deleteButton" class="btn btn-danger btn-sm" onclick="deleteField()">Eliminar Ultimo Movimiento</button> --}}
        <br><br><br>
        <div class="form-row">
            <input hidden id=id_supre name="id_supre" value={{$getsupre->id}}>
            <div class="form-group col-md-7">
                <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
            </div>
            <div class="form-group col-md-2">
                <button type="submit" id="btn_guardar_supre" class="btn btn-primary" >Guardar</button>
            </div>
            </form>
            <div class="form-group col-md-3">
                <form action="{{ route('solicitud-pago-efirma') }}" method="post" id="registersolicitudpago">
                    @csrf
                    <input type="text" name="ids" id="ids" value='{{$getsupre->id}}' hidden>
                    <input type="text" name="clavecurso" id="clavecursos" value='{{$data->clave}}' hidden>
                    <button   button type="submit" class="btn btn-red" >Generar Suficiencia E.Firma</button>
                </form>
            </div>
        </div>
        <br>
    </form>
 </div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script type="text/javascript">
    let fieldCounter = document.getElementById("keyfield").value;

    function addField() {
        const fieldsContainer = document.getElementById("fieldsContainer");
        const formRow = document.createElement("div");
        formRow.className = "form-row";
        formRow.id = `formRow${fieldCounter}`;
        fieldsContainer.appendChild(formRow);

        const textFormGroup = createFormGroup(textLabel("Movimiento Bancario"), textInput(`movimiento_bancario_[${fieldCounter}]`));
        formRow.appendChild(textFormGroup);

        const dateFormGroup = createFormGroup(textLabel("Fecha de Movimiento"), dateInput(`fecha_movimiento_bancario_[${fieldCounter}]`));
        formRow.appendChild(dateFormGroup);

        fieldCounter++;
        updateDeleteButton();
    }

    function deleteField() {
        if (fieldCounter === 0) return;
        const formRow = document.getElementById(`formRow${--fieldCounter}`);
        formRow.remove();
        updateDeleteButton();
    }

    function updateDeleteButton() {
        const deleteButton = document.getElementById("deleteButton");
        if (fieldCounter === 0) {
            deleteButton.style.display = "none";
        } else {
            deleteButton.style.display = "inline-block";
        }
    }

    function createFormGroup(label, input) {
        const formGroup = document.createElement("div");
        formGroup.className = "form-group col-md-3";
        formGroup.appendChild(label);
        formGroup.appendChild(input);
        return formGroup;
    }

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
 </script>

@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/supre.js") }}"></script>
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/adrianValidate.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
<script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
@endsection

