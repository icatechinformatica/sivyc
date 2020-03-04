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
 <!--empieza aquí-->
 <div class="container g-pt-50">
   <form action="{{ route('addsupre') }}" id="registersupre" method="POST">
       @csrf
       <div style="text-align: right;width:82%">
           <label for="tituloSupre1"><h2>Modificación de Solicitud para Suficiencia Presupuestal</h2></label>
        </div>
        <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="unidad" class="control-label">Unidad de Capacitacion </label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->unidad_capacitacion }}" onkeypress="return soloLetras(event)" id="unidad" name="unidad">
            </div>
            <div class="form-group col-md-5">
                <label for="mamorandum" class="control-label">Memorandum No. </label>
                <input type="text" class="form-control" disabled id="memorandum" name="memorandum" value="{{ $getsupre->no_memo }}" placeholder="ICATECH/0000/000/2020">
            </div>
            <div class="form-group col-md-2">
                <label for="fecha" class="control-label">Fecha</label>
                <input class="form-control" name="fecha" disabled type="date" value="{{ $getsupre->fecha }}" value="2020-01-01" id="fecha">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6"> <!-- Destinatario -->
                <label for="destino" class="control-label">Destinatario</label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->nombre_para }}" onkeypress="return soloLetras(event)" id="destino" name="destino" placeholder="Nombre">
            </div>
            <div class="form-group col-md-6"> <!-- Puesto-->
                <label for="puesto" class="control-label">Puesto</label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->puesto_para }}" onkeypress="return soloLetras(event)" id="destino_puesto" name="destino_puesto" placeholder="Puesto">
            </div>
        </div>
        <div class="field_wrapper">
            <table class="table table-bordered" id="dynamicTablemodsupre">
                <tr>
                    <th>Folio</th>
                    <th>Numero Presupuesto</th>
                    <th>Clave Curso</th>
                    <th>Importe total</th>
                    <th>Iva</th>
                    <th>Acción</th>
                </tr>
                @foreach ( $getfolios as $data )
                <tr>
                    <td><input type="text" name="addmore[0][folio]" disabled value="{{ $data->folio_validacion }}" placeholder="folio" class="form-control" /></td>
                    <td><input type="text" name="addmore[0][numeropresupuesto]" disabled value="{{ $data->numero_presupuesto }}" placeholder="numero presupuesto" class="form-control" /></td>
                    <td><input type="text" name="addmore[0][clavecurso]" disabled placeholder="clave curso" class="form-control" /></td>
                    <td><input type="text" name="addmore[0][importe]" disabled value="{{ $data->importe_total }}" placeholder="importe total" class="form-control" /></td>
                    <td><input type="text" name="addmore[0][iva]" disabled value="{{ $data->iva }}" placeholder="Iva" class="form-control" /></td>
                    <td><button type="button" name="addmodsupre" id="addmodsupre" class="btn btn-success">Agregar</button></td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6"> <!--  -->
                <label for="remitente" class="control-label">Remitente</label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->nombre_remitente }}" onkeypress="return soloLetras(event)" id="remitente" name="remitente" placeholder="Nombre">
            </div>
            <div class="form-group col-md-6"> <!--  -->
                <label for="remitente" class="control-label">Puesto</label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->puesto_remitente }}" onkeypress="return soloLetras(event)" id="remitente_puesto" name="remitente_puesto" placeholder="Puesto">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="remitente" class="control-label">Nombre de Quien Valida</label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->nombre_valida }}" onkeypress="return soloLetras(event)" id="nombre_valida" name="nombre_valida" placeholder="Nombre">
            </div>
            <div class="form-group col-md-4">
                <label for="remitente" class="control-label">Puesto de Quien Valida</label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->puesto_valida }}" onkeypress="return soloLetras(event)" id="puesto_valida" name="puesto_valida" placeholder="Puesto">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="remitente" class="control-label">Nombre de Quien Elabora</label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->nombre_elabora }}" onkeypress="return soloLetras(event)" id="nombre_elabora" name="nombre_elabora" placeholder="Nombre">
            </div>
            <div class="form-group col-md-4">
                <label for="remitente" class="control-label">Puesto de Quien Elabora</label>
                <input type="text" class="form-control" disabled value="{{ $getsupre->puesto_elabora }}" onkeypress="return soloLetras(event)" id="puesto_elabora" name="puesto_elabora" placeholder="Puesto">
            </div>
        </div>
        <hr style="border-color:dimgray">
        <!-- START CCP -->
            <label for="inputccp"><h3>Con Copia Para</h3></label>
            <br>
            <label><h4>Copia 1</h4></label>
            <div class="form-row">
                <div class="form-group col-md-4"> <!-- copia 1 -->
                    <label for="remitente" class="control-label">Nombre</label>
                    <input type="text" class="form-control" disabled value="{{ $getsupre->nombre_ccp1 }}" onkeypress="return soloLetras(event)" id="nombre_ccp1" name="nombre_ccp1" placeholder="Nombre">
                </div>
                <div class="form-group col-md-4"> <!--  -->
                    <label for="remitente" class="control-label">Puesto</label>
                    <input type="text" class="form-control" disabled value="{{ $getsupre->puesto_ccp1 }}" onkeypress="return soloLetras(event)" id="puesto_ccp1" name="puesto_ccp2" placeholder="Puesto">
                </div>
            </div>
            <br>
            <label><h4>Copia 2</h4></label>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="remitente" class="control-label">Nombre</label>
                    <input type="text" class="form-control" disabled value="{{ $getsupre->nombre_ccp2 }}" onkeypress="return soloLetras(event)" id="nombre_ccp2" name="nombre_ccp2" placeholder="Nombre">
                </div>
                <div class="form-group col-md-4"> <!--  -->
                    <label for="remitente" class="control-label">Puesto</label>
                    <input type="text" class="form-control" disabled value="{{ $getsupre->puesto_ccp2 }}" onkeypress="return soloLetras(event)" id="puesto_ccp2" name="puesto_ccp2" placeholder="Puesto">
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
    <input value="{{$cf}}" id='cf' name="cf" type="hidden">
    </form>
 </div>

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script type="text/javascript">

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

@stop

