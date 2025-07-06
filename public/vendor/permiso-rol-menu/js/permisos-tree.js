class PermisoRolManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindCheckboxEvents();
        this.setupCSRFToken();
        this.bindCollapseEvents();
        this.bindToggleAllButton();
    }

    setupCSRFToken() {
        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
    }

    bindCheckboxEvents() {
        $(document).on('change', 'input[type="checkbox"][name^="menu["]', (e) => {
            this.handleCheckboxChange(e);
        });
    }

    bindToggleAllButton() {
        $(document).on('click', '#toggle-all-permisos', (e) => {
            this.handleToggleAll(e);
        });
    }

    handleToggleAll(e) {
        const button = $(e.target);
        const allCheckboxes = $('.permiso-checkbox');
        const checkedCount = allCheckboxes.filter(':checked').length;
        const totalCount = allCheckboxes.length;

        if (checkedCount === totalCount) {
            // Deseleccionar todos
            button.html('<i class="fas fa-square"></i> Seleccionar Todo');
            allCheckboxes.filter(':checked').each((index, checkbox) => {
                $(checkbox).prop('checked', false).trigger('change');
            });
        } else {
            // Seleccionar todos
            button.html('<i class="fas fa-check-square"></i> Deseleccionar Todo');
            allCheckboxes.filter(':not(:checked)').each((index, checkbox) => {
                $(checkbox).prop('checked', true).trigger('change');
            });
        }
    }

    bindCollapseEvents() {
        // Rotar iconos al colapsar/expandir
        $(document).on('click', '[data-toggle="collapse"]', function () {
            const target = $(this).find('i');
            setTimeout(() => {
                const isExpanded = $($(this).data('target')).hasClass('show');
                if (isExpanded) {
                    target.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                } else {
                    target.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                }
            }, 350);
        });
    }

    handleCheckboxChange(e) {
        const checkbox = e.target;
        const permisoId = checkbox.value;
        const rolId = this.getRolId();
        const isChecked = checkbox.checked;

        if (!permisoId || !rolId) {
            console.error('No se pudo obtener el ID del permiso o rol');
            return;
        }

        // Agregar clase de procesamiento
        $(checkbox).closest('li').addClass('processing');

        this.togglePermiso(rolId, permisoId, isChecked ? 'attach' : 'detach');
    }

    getRolId() {
        // Obtener el ID del rol desde la URL o desde un elemento hidden
        const url = window.location.pathname;
        const matches = url.match(/\/rol\/menus\/(\d+)/);
        return matches ? matches[1] : null;
    }

    togglePermiso(rolId, permisoId, action) {
        $.ajax({
            url: `/permiso-rol-menu/rol/${rolId}/permiso/${permisoId}/toggle`,
            method: 'POST',
            data: {
                action: action,
                _token: this.csrfToken
            },
            success: (response) => this.handleToggleSuccess(response, action, permisoId),
            error: (xhr) => this.handleToggleError(xhr, action, permisoId)
        });
    }

    handleToggleSuccess(response, action, permisoId) {
        // Quitar clase de procesamiento
        $(`.permiso-checkbox[value="${permisoId}"]`).closest('li').removeClass('processing');

        if (response.success) {
            if (action === 'attach') {
                // Marcar como seleccionados los permisos padre
                if (response.attached_ids) {
                    response.attached_ids.forEach(id => {
                        const checkbox = $(`.permiso-checkbox[value="${id}"]`);
                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                        }
                    });
                }
            } else {
                // Desmarcar los permisos hijo
                if (response.detached_ids) {
                    response.detached_ids.forEach(id => {
                        const checkbox = $(`.permiso-checkbox[value="${id}"]`);
                        if (checkbox.length) {
                            checkbox.prop('checked', false);
                        }
                    });
                }
            }

            // Mostrar mensaje de éxito
            this.showMessage(response.message, 'success');
        } else {
            this.showMessage(response.message || 'Error al procesar la solicitud', 'error');
        }
    }

    handleToggleError(xhr, action, permisoId) {
        // Quitar clase de procesamiento
        $(`.permiso-checkbox[value="${permisoId}"]`).closest('li').removeClass('processing');

        let message = 'Error al procesar la solicitud';

        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        }

        this.showMessage(message, 'error');

        // Revertir el estado del checkbox
        const checkbox = $(`.permiso-checkbox[value="${permisoId}"]`);
        if (checkbox.length) {
            checkbox.prop('checked', action === 'detach');
        }
    }

    showMessage(message, type) {
        // Remover mensajes anteriores
        $('.alert-dynamic').remove();

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show alert-dynamic" role="alert">
                <i class="fas ${iconClass} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        // Insertar el mensaje en la parte superior del contenido
        $('.alert-container').after(alertHtml);

        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            $('.alert-dynamic').fadeOut();
        }, 5000);
    }
}

// Inicializar cuando el DOM esté listo
$(document).ready(() => {
    new PermisoRolManager();
});
