
const registro_curp = $('#registro_curp');
const btn_nuevo_registro_curp = $('#btn_nuevo_registro_curp');
const btn_iniciar_registro_curp = $('#btn_iniciar_registro_curp');
const btn_cerrar_registro_curp = $('#btn_cerrar_registro_curp');

btn_nuevo_registro_curp.on('click', function () {
    registro_curp.addClass('d-md-inline');
    btn_nuevo_registro_curp.addClass('d-none');
    btn_iniciar_registro_curp.removeClass('d-none');
    btn_cerrar_registro_curp.removeClass('d-none');
});

btn_cerrar_registro_curp.on('click', function () {
    registro_curp.removeClass('d-md-inline');
    btn_nuevo_registro_curp.removeClass('d-none');
    btn_iniciar_registro_curp.addClass('d-none');
    btn_cerrar_registro_curp.addClass('d-none');
});
