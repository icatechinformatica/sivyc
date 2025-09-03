{{-- Modal: Añadir observación al turnado (Bootstrap 4.4.1) --}}
<div class="modal fade" id="modalObservacionTurnado" tabindex="-1" role="dialog" aria-labelledby="modalObservacionTurnadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalObservacionTurnadoLabel">Observación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="d-flex p-3 align-items-center justify-content-around">
                <p id="estatusActualTurnado">Estatus Actual: <span class="p-2 rounded" id="estatusActualTurnadoValue"></span></p>
				<p class="mx-2" style="font-size: 1.25rem; line-height: 1;">⟶</p>
                <p id="estatusRegresarTurnado">Regresar a: <span class="p-2 rounded" id="estatusRegresarTurnadoValue"></span></p>
            </div>
            <form id="formObservacionTurnado" method="post">
                @csrf
                <div class="modal-body">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <label for="observacionTurnado" class="form-label mb-0">Observación</label>
                        <small class="text-muted"><span id="observacionTurnadoCount">0</span>/100</small>
                    </div>
                    <textarea class="form-control" name="observacion" id="observacionTurnado" maxlength="100"
                        placeholder="Escribe la observación" required
                        style="height: 80px; overflow-y: auto; resize: none;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancelarObservacion"
                        data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnAceptarObservacion">Aceptar</button>
                </div>
            </form>
        </div>
    </div>
    {{-- Fin modal --}}
</div>

{{-- Script: contador y limpieza al cancelar --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
		const textarea = document.getElementById('observacionTurnado');
		const counter = document.getElementById('observacionTurnadoCount');
		const form = document.getElementById('formObservacionTurnado');
		const btnCancelar = document.getElementById('btnCancelarObservacion');
		const modalEl = document.getElementById('modalObservacionTurnado');

		if (!textarea || !counter || !form || !modalEl) return;

		const updateCount = () => {
			const max = parseInt(textarea.getAttribute('maxlength')) || 100;
			const len = textarea.value.length;
			counter.textContent = len.toString();
			// Hard-limit in case of paste beyond maxlength (some browsers already enforce it)
			if (len > max) {
				textarea.value = textarea.value.substring(0, max);
				counter.textContent = max.toString();
			}
		};

		const resetForm = () => {
			form.reset();
			// For browsers that don't reset the counter automatically
			counter.textContent = '0';
			// Limpiar spans de estatus
			try {
				var a = document.getElementById('estatusActualTurnadoValue');
				var b = document.getElementById('estatusRegresarTurnadoValue');
				if (a) { a.textContent = ''; a.style.backgroundColor = ''; a.removeAttribute('style'); }
				if (b) { b.textContent = ''; b.style.backgroundColor = ''; b.removeAttribute('style'); }
			} catch (e) { /* noop */ }
		};

		textarea.addEventListener('input', updateCount);

		// Limpiar al presionar Cancelar
		if (btnCancelar) {
			btnCancelar.addEventListener('click', resetForm);
		}

		// Como refuerzo, limpiar al ocultar el modal (jQuery requerido en BS4)
		if (window.jQuery) {
			var $modal = $('#modalObservacionTurnado');
			// Al cerrar, limpiar y restaurar foco al elemento que abrió el modal
			$modal.on('hidden.bs.modal', function () {
				resetForm();
				try {
					if (window.lastTurnarTrigger && typeof window.lastTurnarTrigger.focus === 'function') {
						window.lastTurnarTrigger.focus();
					}
				} catch (e) { /* noop */ }
			});
			// Antes de ocultar, quitar foco de elementos dentro del modal para evitar aria-hidden warning
			$modal.on('hide.bs.modal', function () {
				var active = document.activeElement;
				if (active && $modal[0].contains(active) && typeof active.blur === 'function') {
					active.blur();
				}
			});
		}

		// Inicializar contador si el modal ya tiene contenido
		updateCount();
	});
</script>