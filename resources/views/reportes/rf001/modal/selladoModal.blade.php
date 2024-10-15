<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">SELLAR LA SOLICITUD - <div id="memo"></div>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <form id="sendComment" method="POST" action="{{ route('administrativo.rf001.sellado') }}">
                        @csrf
                        <div class="new-comment-box mb-3">
                            <p style="font-size: 22px;">¿Está seguro que desea realizar este proceso?</p>
                            <p style="font-size: 13px; color:crimson;">* NOTA: después de realizar el proceso de sellado no habrá marcha atrás</p>
                        </div>
                        <input type="hidden" name="rf001Id" id="rf001Id">
                        <button type="submit" class="btn"><i class="fas fa-stamp fa-1x fa-sm" aria-hidden="true"
                            title="SELLADO DIGITAL" style="color:white"></i> Sellar</button>
                    </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger action-close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
