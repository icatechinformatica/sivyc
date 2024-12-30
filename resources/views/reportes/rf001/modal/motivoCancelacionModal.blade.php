<div class="modal fade" id="cancelacionModal" tabindex="-1" role="dialog" aria-labelledby="cancelacionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelacionModalLabel">Cancelar <div id="memocancelar"></div>
                </h5>
                <button type="button" class="close btn-cancelar" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @can('validacion.rf001')
                    <form id="cancelComment" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="motivoCancelar">MOTIVO DE LA CANCELACIÓN!</label>
                            <textarea name="motivoCancelar" id="motivoCancelar" class="form-control" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="memoCancelacion" id="memoCancelacion">
                        <input type="submit" value="REALIZAR CAMBIOS" class="btn">
                    </form>
                @endcan
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-cancelar" data-dismiss="modal">CANCELAR</button>
            </div>
        </div>
    </div>
</div>
