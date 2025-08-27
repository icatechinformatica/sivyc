{{-- Modal: Ver observación existente (solo lectura) --}}
<div class="modal fade" id="modalVerObservacion" tabindex="-1" role="dialog" aria-labelledby="modalVerObservacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerObservacionLabel">RETORNADO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-2 text-muted">Observación realizada</p>
                <div class="p-3 border rounded bg-light" id="contenidoVerObservacion" style="white-space: pre-wrap; word-break: break-word;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('script_sign')
<script>
    // Delegación por si la tabla se refresca
    document.addEventListener('click', function (e) {
        // Permite que el clic sea en el ícono, texto o cualquier hijo dentro del contenedor con .obs-icon
        const el = e.target && (e.target.closest ? e.target.closest('.obs-icon') : null);
        if (!el) return;
        const texto = el.getAttribute('data-observacion') || '';
        const cont = document.getElementById('contenidoVerObservacion');
        if (cont) cont.textContent = texto;
        if (window.jQuery) {
            $('#modalVerObservacion').modal('show');
        }
    }, true);

    // Accesibilidad con Enter/Espacio
    document.addEventListener('keydown', function (e) {
        const t = e.target;
        if (!t) return;
        if (e.key !== 'Enter' && e.key !== ' ') return;
        // Si el foco está en un hijo, encontrar el contenedor .obs-icon más cercano
        const el = (t.classList && t.classList.contains('obs-icon'))
            ? t
            : (t.closest ? t.closest('.obs-icon') : null);
        if (!el) return;
        e.preventDefault();
        el.click();
    });
</script>
@endpush
