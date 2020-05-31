$(function(){
    var i = 0;

    $("#add").click(function(){

        ++i;

        $("#dynamicTable").append('<tr><td><input type="text" name="addmore['+i+'][folio]" placeholder="folio" class="form-control" /></td><td><input type="text" name="addmore['+i+'][numeropresupuesto]" placeholder="Numero Presupuesto" class="form-control" /></td><td><input type="text" name="addmore['+i+'][clavecurso]" placeholder="Clave curso" class="form-control" /></td><td><input type="text" name="addmore['+i+'][importe]" placeholder="importe total" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-tr">Eliminar</button></td></tr>');
        //<td><input type="text" name="addmore['+i+'][iva]" placeholder="Iva" class="form-control" /></td>
    });

    $(document).on('click', '.remove-tr', function(){
         $(this).parents('tr').remove();
    });
});
