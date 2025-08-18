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
            location.reload();
        },
        error: function (error) {
            console.error("Error al turnar el grupo:", error);
            const notyf = new Notyf({
                position: { x: 'right', y: 'top' },
                duration: 3000,
            });
            notyf.error("Error al turnar el grupo");
        }
    });
}