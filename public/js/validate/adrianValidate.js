$(function(){
    var i = 0;

    $("#add").click(function(){

        ++i;
        $("#dynamicTable").append('<tr><td><input type="text" name="addmore['+i+'][folio]" id="addmore['+i+'][folio]" placeholder="folio" class="form-control" /></td><td><input type="text" name="addmore['+i+'][numeropresupuesto]" id="addmore['+i+'][numeropresupuesto]" placeholder="Numero Presupuesto" class="form-control" disabled value="12101" /></td><td><input type="text" name="addmore['+i+'][clavecurso]" id="addmore['+i+'][clavecurso]" placeholder="Clave curso" class="form-control" /></td><td><input type="text" name="addmore['+i+'][importe]" id="addmore['+i+'][importe]" placeholder="importe total" class="form-control" readonly /><footer name="addmore['+i+'][aviso]" id="addmore['+i+'][aviso]" style="color: red"></footer></td><td><input type="text" name="addmore['+i+'][iva]" id="addmore['+i+'][iva]" placeholder="Iva" class="form-control" readonly /></td><td><input type="text" name="addmore['+i+'][comentario]" id="addmore['+i+'][comentario]" placeholder="comentario" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-tr">Eliminar</button></td></tr>');
        //
    });

    $(document).on('click', '.remove-tr', function(){
         $(this).parents('tr').remove();
    });

});
