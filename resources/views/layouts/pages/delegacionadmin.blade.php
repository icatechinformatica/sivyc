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
   <form method="POST" action="{{ route('solicitud-guardar') }}" id="regsupre">
       @csrf
       <div style="text-align: right;width:82%">
           <label for="tituloSupre1"><h2>Formulario para la Solicitud de Suficiencia Presupuestal</h2></label>
        </div>
        <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="unidad" class="control-label">Unidad de Capacitación </label>
                <select name="unidad" id="unidad" class="form-control">
                    <option value="sin especificar">SIN ESPECIFICAR</option>
                    @foreach ($unidades as $data )
                        <option value="{{$data->unidad}}">{{$data->unidad}}</option>
                    @endforeach
                </select>
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
        <div class="form-row">
            <div class="form-group col-md-6"> <!-- Destinatario -->
                <label for="inputdestino" class="control-label">Destinatario</label>
                <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="destino" name="destino" placeholder="Nombre">
            </div>
            <div class="form-group col-md-6"> <!-- Puesto-->
                <label for="inputpuesto_destino" class="control-label">Puesto</label>
                <input type="text" class="form-control" readonly onkeypress="return soloLetras(event)" id="destino_puesto" name="destino_puesto" placeholder="Puesto">
                <input id="id_destino" name="id_destino" type="text" hidden>
            </div>
        </div>
        <div class="field_wrapper">
            <table class="table table-bordered" id="dynamicTable">
                <tr>
                    <th>Folio</th>
                    <th>Partida/Concepto</th>
                    <th>Clave Curso</th>
                    <th>Importe total</th>
                    <th>Iva</th>
                    <th>Observación</th>
                    <th>Acción</th>
                </tr>
                <tr>
                    <td><input type="text" name="addmore[0][folio]" id="addmore[0][folio]" placeholder="folio" class="form-control" /><footer name="addmore[0][avisofolio]" id="addmore[0][avisofolio]" style="color: red"></footer></td>
                    <td><input type="text" name="addmore[0][numeropresupuesto]" id="addmore[0][numeropresupuesto]" placeholder="número presupuesto" class="form-control" disabled value="12101" /></td>
                    <td><input type="text" name="addmore[0][clavecurso]" id="addmore[0][clavecurso]" placeholder="clave curso" class="form-control" /></td>
                    <td><input type="text" name="addmore[0][importe]" id="addmore[0][importe]" placeholder="importe total" class="form-control" readonly/><footer name="addmore[0][aviso]" id="addmore[0][aviso]" style="color: red"></footer></td>
                    <td><input type="text" name="addmore[0][iva]" id="addmore[0][iva]" placeholder="IVA" class="form-control" readonly /></td>
                    <td><input type="text" name="addmore[0][comentario]" id="addmore[0][comentario]" placeholder="Comentario" class="form-control" /></td>
                    <td><button type="button" name="add" id="add" class="btn btn-success">Agregar</button></td>
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
        <!-- START CCP -->
            <label for="inputccp"><h3>Con Copia Para</h3></label>
            <br>
            <label><h4>Copia 1</h4></label>
            <div class="form-row">
                <div class="form-group col-md-4"> <!-- copia 1 -->
                    <label for="inputccp1" class="control-label">Nombre</label>
                    <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="nombre_ccp1" name="nombre_ccp1" placeholder="Nombre">
                </div>
                <div class="form-group col-md-4"> <!--  -->
                    <label for="inputccp1" class="control-label">Puesto</label>
                    <input type="text" readonly class="form-control" onkeypress="return soloLetras(event)" id="puesto_ccp1" name="puesto_ccp1" placeholder="Puesto">
                    <input id="id_ccp1" name="id_ccp1" type="text" hidden>
                </div>
            </div>
            <br>
            <label><h4>Copia 2</h4></label>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputccp2" class="control-label">Nombre</label>
                    <input type="text" class="form-control" onkeypress="return soloLetras(event)" id="nombre_ccp2" name="nombre_ccp2" placeholder="Nombre">
                </div>
                <div class="form-group col-md-4"> <!--  -->
                    <label for="inputccp2" class="control-label">Puesto</label>
                    <input type="text" readonly class="form-control" onkeypress="return soloLetras(event)" id="puesto_ccp2" name="puesto_ccp2" placeholder="Puesto">
                    <input id="id_ccp2" name="id_ccp2" type="text" hidden>
                </div>
            </div>
        <!--END CCP-->
        <br>
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
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script type="text/javascript">

     // Add the following code if you want the name of the file appear on select
     $(".custom-file-input").on("change", function() {
       var fileName = $(this).val().split("\\").pop();
       $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
     });
//hola
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
@endsection

