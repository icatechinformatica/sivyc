class MenuTree {
    constructor() {
        this.init();
    }

    init() {
        this.bindStatusToggle();
        this.bindFormCollapse();
    }

    bindStatusToggle() {
        document.querySelectorAll('.status').forEach(statusElement => {
            statusElement.addEventListener('click', (e) => this.handleStatusToggle(e));
        });
    }

    bindFormCollapse() {
        $(document).on('show.bs.collapse', '[id^="add-form-"]', function () {
            $('[id^="add-form-"]').not(this).collapse('hide');
        });
    }

    handleStatusToggle(e) {
        const statusElement = e.target;
        const menuId = statusElement.getAttribute('data-id-menu');
        const currentStatus = statusElement.classList.contains('badge-success') ? 'Activo' : 'Inactivo';
        const newStatus = currentStatus === 'Activo' ? 'Inactivo' : 'Activo';

        if (newStatus === 'Activo' && !this.validateParentStatus(statusElement)) {
            return;
        }

        this.updateMenuStatus(menuId, newStatus);
    }

    validateParentStatus(statusElement) {
        let currentLi = statusElement.closest('li');
        let parentLi = currentLi.parentElement.closest('li');

        while (parentLi) {
            const parentStatus = parentLi.querySelector('.status');
            if (parentStatus && parentStatus.classList.contains('badge-danger')) {
                const parentName = parentLi.querySelector('.menu-name')
                    ? parentLi.querySelector('.menu-name').textContent.trim()
                    : 'el menú padre';
                const clickedName = currentLi.querySelector('.menu-name').textContent.trim();

                alert(`No se puede activar "${clickedName}" porque "${parentName}" está inactivo.`);
                return false;
            }
            parentLi = parentLi.parentElement.closest('li');
        }
        return true;
    }

    updateMenuStatus(menuId, newStatus) {
        $.ajax({
            url: `/permiso-rol-menu/menus/${menuId}/status-update`,
            method: 'POST',
            data: {
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => this.handleStatusUpdateSuccess(response, newStatus),
            error: () => alert('Error al cambiar el estado del menú.')
        });
    }

    handleStatusUpdateSuccess(response, newStatus) {
        if (response.success) {
            (response.updatedIds || []).forEach(id => {
                const el = document.getElementById(`status-${id}`);
                if (el) {
                    el.classList.toggle('badge-success');
                    el.classList.toggle('badge-danger');
                    el.textContent = newStatus;
                }
            });
        } else {
            alert('Error al cambiar el estado del menú.');
        }
    }
}

// Inicializar cuando el DOM esté listo
$(document).ready(() => new MenuTree());