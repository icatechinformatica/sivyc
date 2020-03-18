$(function(){
    //boton valsupre rechazar
    $("#rechazarPago").click(function(e){
        e.preventDefault();
        $('#rechazar_pago').prop("class", "form-row");
        $('#btn_rechazar').prop("class", "form-row");
        //$('#observaciones').rules('add',  { required: true });
    });
});
