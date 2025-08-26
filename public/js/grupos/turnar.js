const notyf = new Notyf({
    position: { x: 'right', y: 'top' },
    duration: 3000,
});

$(".turnar-btn").on('click', function () {
    const estatus_id = $(this).data('estatus-id');
    const grupo_id = $(this).data('grupo-id');
    console.log("Estatus obtenido: " + estatus_id);
    turnarGrupo(grupo_id, estatus_id);
});


function turnarGrupo(grupo_id, estado_id) {
    $.ajax({
        url: `/grupos/${grupo_id}/turnar`,
        type: 'POST',
        data: {
            estatus_id: estado_id,
            _token: registroBladeVars.csrfToken
        },
        success: function (response) {
            try {
                const msg = (response && (response.mensaje || response.message)) || 'Estatus actualizado correctamente.';
                notyf.success(msg);
            } catch (e) {
                // en caso de algún fallo al mostrar, aún recargamos
            }
            // recargar tras breve pausa para que el usuario vea la notificación
            setTimeout(() => location.reload(), 800);
        },
        error: function (error) {
            console.error("Error al turnar el grupo:", error);
            let msg = 'Error al turnar el grupo';
            try {
                if (error && error.responseJSON) {
                    msg = error.responseJSON.error || error.responseJSON.message || msg;
                } else if (error && error.responseText) {
                    // intentar parsear texto plano
                    const parsed = JSON.parse(error.responseText);
                    msg = parsed.error || parsed.message || msg;
                }
            } catch (e) { /* mantener msg por defecto */ }
            notyf.error(msg);
        }
    });
}