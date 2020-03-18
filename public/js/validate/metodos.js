$(function(){
    //boton valsupre rechazar
    $("#rechazarPago").click(function(e){
        e.preventDefault();
        $('#rechazar_pago').fadeIn('slow').removeClass('hide');
        $('#btn_rechazar').fadeIn('slow').removeClass('hide');
        $('#observaciones').rules('add',  { required: true });
    });
});
