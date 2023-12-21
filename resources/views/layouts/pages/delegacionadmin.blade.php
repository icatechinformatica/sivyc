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
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
   <form method="POST" action="{{ route('solicitud-guardar') }}" id="regsupre">
       @csrf
       <div style="text-align: right;width:82%">
           <label for="tituloSupre1"><h2>Formulario para la Solicitud de Suficiencia Presupuestal</h2></label>
        </div>
        <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="unidad" class="control-label">Unidad de Capacitación </label>
                {{-- <select name="unidad" id="unidad" class="form-control">
                    <option value="sin especificar">SIN ESPECIFICAR</option>
                    @foreach ($unidades as $data )
                        <option value="{{$data->unidad}}">{{$data->unidad}}</option>
                    @endforeach
                </select> --}}
                <input type="text" class="form-control" id="unidad" name="unidad" value="{{$unidad->ubicacion}}" readonly>
            </div>
            <div class="form-group col-md-5">
                <label for="mamorandum" class="control-label">Memoramdum No. </label>
                <input type="text" class="form-control" id="memorandum" name="memorandum" placeholder="ICATECH/0000/000/2020">
            </div>
            <div class="form-group col-md-2">
                <label for="fecha" class="control-label">Fecha</label>
                <input class="form-control" name="fecha" type="date" value="2020-01-01" id="fecha">
            </div>
        </div>
        <div class="field_wrapper">
            <table class="table table-bordered" id="dynamicTable">
                <tr>
                    <th>Folio</th>
                    <th>Partida/Concepto</th>
                    <th>Clave Curso</th>
                    <th>Importe total</th>
                    <th id='thiva'>Iva</th>
                    <th>Observación</th>
                    {{-- <th>Acción</th> --}}
                </tr>
                <tr>
                    <td><input type="text" name="addmore[0][folio]" id="addmore[0][folio]" placeholder="folio" class="form-control" /><footer name="addmore[0][avisofolio]" id="addmore[0][avisofolio]" style="color: red"></footer></td>
                    <td><input type="text" name="addmore[0][numeropresupuesto]" id="addmore[0][numeropresupuesto]" placeholder="número presupuesto" class="form-control" disabled value="12101" /></td>
                    <td><input type="text" name="addmore[0][clavecurso]" id="addmore[0][clavecurso]" placeholder="clave curso" class="form-control claveCurso" /></td>
                    <td><input type="text" name="addmore[0][importe]" id="addmore[0][importe]" placeholder="importe total" class="form-control" readonly/><footer name="addmore[0][aviso]" id="addmore[0][aviso]" style="color: red"></footer></td>
                    <td id="tdiva"><input type="text" name="addmore[0][iva]" id="addmore[0][iva]" placeholder="IVA" class="form-control" readonly /></td>
                    <td><input type="text" name="addmore[0][comentario]" id="addmore[0][comentario]" placeholder="Comentario" class="form-control" /></td>
                    {{-- <td><button type="button" name="add" id="add" class="btn btn-success">Agregar</button></td> --}}
                </tr>
            </table>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputremitente" class="control-label">Remitente</label>
                <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre">
            </div>
            <div class="form-group col-md-6">
                <label for="inputremitente" class="control-label">Puesto</label>
                <input type="text" readonly class="form-control" onkeypress="return soloLetras(event)" id="remitente_puesto" name="remitente_puesto" placeholder="Puesto">
                <input id="id_remitente" name="id_remitente" type="text" hidden>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputvalida" class="control-label">Nombre de Quien Valida</label>
                <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="nombre_valida" name="nombre_valida" placeholder="Nombre">
            </div>
            <div class="form-group col-md-4">
                <label for="inputvalida" class="control-label">Puesto de Quien Valida</label>
                <input type="text" class="form-control" readonly onkeypress="return soloLetras(event)" id="puesto_valida" name="puesto_valida" placeholder="Puesto">
                <input id="id_valida" name="id_valida" type="text" hidden>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="inputelabora" class="control-label">Nombre de Quien Elabora</label>
                <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="nombre_elabora" name="nombre_elabora" placeholder="Nombre">
            </div>
            <div class="form-group col-md-4">
                <label for="inputelabora" class="control-label">Puesto de Quien Elabora</label>
                <input type="text" class="form-control" readonly onkeypress="return soloLetras(event)" id="puesto_elabora" name="puesto_elabora" placeholder="Puesto">
                <input id="id_elabora" name="id_elabora" type="text" hidden>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Datos de Pago de Curso
            {{-- <button type="button" class="btn btn-primary float-right" onclick="addField()">Añadir Movimiento</button> --}}
        </h2>
        <div id="fieldsContainer">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputnorecibo" class="control-label">Folio de Recibo de Pago</label>
                    <input type="text" name="no_recibo" id="no_recibo" placeholder="No.Recibo" class="form-control" readonly />
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha_movimiento_bancario_0">Fecha de Expedición</label>
                    <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion" readonly>
                </div>
                {{-- <div class="form-group col-md-3">
                    <label for="movimiento_bancario_0">Movimiento Bancario</label>
                    <input type="text" class="form-control" id="movimiento_bancario_0" name="movimiento_bancario_[0]">
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha_movimiento_bancario_0">Fecha de Movimiento</label>
                    <input type="date" class="form-control" id="fecha_movimiento_bancario_0" name="fecha_movimiento_bancario_[0]">
                </div> --}}
            </div>
        </div>
        {{-- <button type="button" id="deleteButton" class="btn btn-danger btn-sm" onclick="deleteField()">Eliminar Ultimo Movimiento</button> --}}
        <br><br><br>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" >Guardar</button>
                </div>
            </div>
        </div>
        <br>
    </form>
 </div>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/adrianValidate.js") }}"></script>
<script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
<script type="text/javascript">
    let fieldCounter = 1;

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

    // función sólo letras
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

    $(function(){
        $(function(){
    var i = 0;

    $( document ).on('input', function(){
        $('input').on('input', function(event){
            id = this.id;
            x = id.substring(8,10);
            comp = x.substring(1);
            if(comp == ']')
            {
                x = id.substring(8,9);
            }
            if (id == 'addmore['+x+'][clavecurso]') {
                var valor = (document.getElementById(id).value).toUpperCase();
                var datos = {valor: valor};
                var url = '/supre/busqueda/curso';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request.done(( respuesta) =>
                {
                    console.log(respuesta);
                    if (respuesta == 'N/A') {
                        document.getElementById('addmore['+x+'][importe]').value = null;
                        document.getElementById('addmore['+x+'][iva]').value = null;
                        document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Clave de Curso Invalida';
                    } else {
                        if(!respuesta.hasOwnProperty('error')){
                            // iva = respuesta[0] * 0.16;
                            // iva = parseFloat(iva).toFixed(2);
                            if(respuesta[1] == 'HONORARIOS' || respuesta[1] == 'HONORARIOS Y ASIMILADOS A SALARIOS') {
                                // total = respuesta[0]*1.16
                                document.getElementById('addmore['+x+'][iva]').value = respuesta['iva'];
                                if(respuesta['tabuladorConIva'] == true) {
                                    document.getElementById('tdiva').style.display = 'none';
                                    document.getElementById('thiva').style.display = 'none';
                                } else {
                                    document.getElementById('tdiva').style.display = 'table-cell';
                                    document.getElementById('thiva').style.display = 'table-cell';
                                }
                                // style="display:none;"
                            } else {
                                // total = respuesta[0]
                                document.getElementById('addmore['+x+'][iva]').value = 0.00;
                            }
                            // total = parseFloat(total).toFixed(2);

                            document.getElementById('addmore['+x+'][importe]').value = respuesta['importe_total'];
                            // document.getElementById('norecibo').value = respuesta['recibo'];
                            // document.getElementById('movimiento_bancario').value = respuesta['movimiento_bancario'];
                            // document.getElementById('fecha_movimiento_bancario').value = respuesta['fecha_movimiento_bancario'];
                            document.getElementById('addmore['+x+'][aviso]').innerHTML = null;
                            document.getElementById('no_recibo').value = respuesta['folio_recibo'];
                            document.getElementById('fecha_expedicion').value = respuesta['fecha_expedicion'];

                        }else{

                            //Puedes mostrar un mensaje de error en algún div del DOM
                        }
                    }
                });

            request.fail(( jqXHR, textStatus ) =>
            {
                //alert( "Hubo un error: " + textStatus );
            });

            } else {

            }
            /*var url = '/supre/busqueda/tipo_curso';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                request.done(( respuesta) =>
                {
                    if (respuesta == 'CERT')
                    {
                        document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Curso Certificado Extraordinario';
                    }
                    if (respuesta == 'NORMAL')
                    {
                        document.getElementById('addmore['+x+'][aviso]').innerHTML = null;
                    }
                });*/
        });
    });

   /* $( document ).on('input', function(){
        $('input').on('input', function(event){
            id = this.id;
            x = id.substring(8,10);
            comp = x.substring(1);
            if(comp == ']')
            {
                x = id.substring(8,9);
            }
            if (id == 'addmore['+x+'][folio]') {
                var valor = (document.getElementById(id).value).toUpperCase();
                var datos = {valor: valor};
                var url = '/supre/busqueda/folio';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                console.log('hola');
                request.done(( respuesta) =>
                {
                    console.log(respuesta);
                    if (respuesta != 'N/A') {
                        document.getElementById('addmore['+x+'][avisofolio]').innerHTML = 'Folio Existente';
                    } else {
                        if(!respuesta.hasOwnProperty('error')){
                            console.log('respuesta= ')
                            console.log(respuesta)
                            document.getElementById('addmore['+x+'][avisofolio]').innerHTML = null;
                        }else{

                            //Puedes mostrar un mensaje de error en algún div del DOM
                        }
                    }
                });

            request.fail(( jqXHR, textStatus ) =>
            {
                alert( "Hubo un error: " + textStatus );
            });

            } else {

            }
        });
    });*/

});
        // evento de cargar los datos en el elemento jquery con los inputs dinámicos
       /* $('.claveCurso').on('input', function(event){
            id = this.id;
            x = id.substring(8,10);
            comp = x.substring(1);
            if(comp == ']')
            {
                x = id.substring(8,9);
            }
            console.log('hola');
            if (id == 'addmore['+x+'][clavecurso]') {
                var valor = (document.getElementById(id).value).toUpperCase();
                var datos = {valor: valor, _token: "{{ csrf_token() }}"};
                var url = "{{ route('supre.busqueda.curso') }}";
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request.done(( respuesta) =>
                {
                    console.log(respuesta);
                    if (respuesta == 'N/A') {
                        document.getElementById('addmore['+x+'][importe]').value = null;
                        document.getElementById('addmore['+x+'][iva]').value = null;
                        document.getElementById('addmore['+x+'][aviso]').innerHTML = 'Clave de Curso Invalida';
                    } else {
                        if(!respuesta.hasOwnProperty('error')){
                            console.log('respuesta= ')
                            console.log(respuesta)
                            iva = respuesta * 0.16;
                            iva = parseFloat(iva).toFixed(2);
                            total = respuesta*1.16
                            total = parseFloat(total).toFixed(2);

                            document.getElementById('addmore['+x+'][importe]').value = total;

                            document.getElementById('addmore['+x+'][iva]').value = iva;

                            document.getElementById('addmore['+x+'][aviso]').innerHTML = null;
                        }else{
                            console.log("Esto es una respuesta de Error:" + respuesta);
                            //Puedes mostrar un mensaje de error en algún div del DOM
                        }
                    }
                });

            request.fail(( jqXHR, textStatus ) =>
            {
                alert( "Hubo un error: " + jqXHR.responseText );
            });

            } else {

            }
        });*/

    });
</script>
@endsection

