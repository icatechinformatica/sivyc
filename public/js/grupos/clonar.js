$('.btn-clonar-grupo').on('click', function () {
    const grupoId = $(this).data('grupo-id');
    $('#modalClonarGrupo').data('grupo-id', grupoId);
    $('#modalClonarGrupo').modal('show');
});

$('#confirmarClonarGrupo').on('click', function () {
    const grupoId = $('#modalClonarGrupo').data('grupo-id');
    $('#modalClonarGrupo').modal('hide');

    // Realizar la petición AJAX
    $.ajax({
        url: '/grupos/' + grupoId + '/clonar',
        type: 'POST',
        data: {
            _token: window.registroBladeVars.csrfToken
        },
        success: function (response) {
            // Redirigir a la página de edición del grupo clonado
            window.location.href = '/grupos/editar/' + response.grupo_id;
        },
        error: function (xhr) {
            alert('Error al clonar el grupo. Por favor, inténtalo de nuevo.');
        }
    });
});

// Manejar el foco cuando el modal se cierra para evitar warnings de accesibilidad
$('#modalClonarGrupo').on('hidden.bs.modal', function () {
    // Quitar el foco de cualquier elemento dentro del modal
    $(this).find('*').blur();
    // Devolver el foco al body o a un elemento seguro
    $('body').focus();
});

// También manejar cuando el modal se está cerrando
$('#modalClonarGrupo').on('hide.bs.modal', function () {
    // Quitar el foco de elementos activos dentro del modal antes de cerrarlo
    $(this).find('button:focus, .btn:focus').blur();
});