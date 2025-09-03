const notyf = new Notyf({
    position: { x: 'right', y: 'top' },
    duration: 3000,
});

$(".turnar-btn").on('click', function () {
    // Guardar quién abrió el modal para restaurar el foco al cerrar
    window.lastTurnarTrigger = this;
    const estatus_id = $(this).data('estatus-id');
    const grupo_id = $(this).data('grupo-id');

    // 1) Contexto principal: dentro de una tabla (fila <tr>)
    const $row = $(this).closest('tr');
    let $badgeActual = $row.find('[data-estatus-actual-id]');

    // 2) Fallback: contenedor ancestro con data-estatus-actual-id (ej. vistas sin tabla)
    if (!$badgeActual.length) {
        const $context = $(this).closest('[data-estatus-actual-id], .step-section, .card, .card-body');
        if ($context.length) {
            $badgeActual = $context.is('[data-estatus-actual-id]')
                ? $context
                : $context.find('[data-estatus-actual-id]').first();
        }
    }

    // 3) Extra fallback: buscar en todo el documento el primero (menos preferible)
    if (!$badgeActual.length) {
        $badgeActual = $('[data-estatus-actual-id]').first();
    }

    let estatus_actual;
    let actualText = '';
    let actualColor = '#6c757d';
    if ($badgeActual && $badgeActual.length) {
        const parsed = parseInt($badgeActual.data('estatus-actual-id'), 10);
        estatus_actual = Number.isNaN(parsed) ? undefined : parsed;
        // Permitir definir texto/color via data-atributos o derivarlos del badge
        actualText = ($badgeActual.data('estatus-actual-text')
            || ($badgeActual.text() || '').trim());
        actualColor = ($badgeActual.data('estatus-actual-color')
            || $badgeActual.css('background-color')
            || '#6c757d');
    }

    const targetText = ($(this).text() || '').trim();
    const targetColor = $(this).css('background-color') || '#007bff';

    if (typeof estatus_actual === 'number' && estatus_id < estatus_actual) {
        // Abrir modal para capturar observación cuando el movimiento es regresivo
        abrirModalObservacion({ grupo_id, estatus_id, actualText, actualColor, targetText, targetColor });
        return;
    }
    if (estatus_actual !== estatus_id) {
        turnarGrupo(grupo_id, estatus_id);
    }
});


function turnarGrupo(grupo_id, estado_id, observacion = null) {
    $.ajax({
        url: `/grupos/${grupo_id}/turnar`,
        type: 'POST',
        data: {
            estatus_id: estado_id,
            observacion: observacion,
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

// --- Modal Observación (Bootstrap 4.4.1) ---
let ultimoContextoTurnado = null;
function abrirModalObservacion({ grupo_id, estatus_id, actualText, actualColor, targetText, targetColor }) {
    ultimoContextoTurnado = { grupo_id, estatus_id };
    const modalEl = document.getElementById('modalObservacionTurnado');
    if (!modalEl) {
        console.error('No se encontró el modal de observación.');
        notyf.error('No se puede abrir el formulario de observación.');
        return;
    }
    // Poblar spans de estatus
    try {
        const actualSpan = document.getElementById('estatusActualTurnadoValue');
        const targetSpan = document.getElementById('estatusRegresarTurnadoValue');
        if (actualSpan) {
            actualSpan.textContent = actualText || '';
            actualSpan.style.backgroundColor = actualColor || '';
        }
        if (targetSpan) {
            targetSpan.textContent = targetText || '';
            targetSpan.style.backgroundColor = targetColor || '';
        }
    } catch (e) { /* noop */ }
    if (window.jQuery) {
        $('#modalObservacionTurnado').modal('show');
    }

    // Asegurar un solo listener de submit
    const form = document.getElementById('formObservacionTurnado');
    if (!form) return;
    form.onsubmit = function (e) {
        e.preventDefault();
        const textarea = document.getElementById('observacionTurnado');
        const texto = (textarea?.value || '').trim();
        // Observación opcional, pero si proveen, respetar límite
        if (texto.length > 150) {
            notyf.error('La observación no debe exceder 150 caracteres.');
            return;
        }
        const { grupo_id, estatus_id } = ultimoContextoTurnado || {};
        if (!grupo_id || !estatus_id) {
            notyf.error('No hay contexto de turnado.');
            return;
        }
        turnarGrupo(grupo_id, estatus_id, texto || null);
        // cerrar modal en BS4 via jQuery
        if (window.jQuery) {
            $('#modalObservacionTurnado').modal('hide');
        }
    };
}