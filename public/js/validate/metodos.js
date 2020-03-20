$(function(){
    //boton valsupre rechazar
    $("#rechazarPago").click(function(e){
        e.preventDefault();
        $('#rechazar_pago').prop("class", "form-row");
        $('#btn_rechazar').prop("class", "form-row");
        //$('#observaciones').rules('add',  { required: true });
    });

    /**
     * documento de modal
    */
   $('#validarModel').on('show.bs.modal', function(event){
       var button = $(event.relatedTarget);
       var id = button.data('id');
    console.log(id);
       $('#validarForm').attr("action", "/pago/validacion" + "/" + id);
   });
});
