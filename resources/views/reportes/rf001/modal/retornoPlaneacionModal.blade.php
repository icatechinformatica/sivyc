<div class="modal fade" id="modalPlaneacion" tabindex="-1" aria-labelledby="modalPlaneacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formPlaneacion">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPlaneacionLabel">RETORNO A PLANEACIÓN EL MEMORÁNDUM - <div
                            id="memorandum"></div>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="new-comment-box mb-3">
                        <p style="font-size: 22px;">¿Está seguro que desea realizar este proceso?</p>
                        <p style="font-size: 13px; color:crimson;">* NOTA: Retornar a planeación para modificar
                            concentrado
                            RF001</p>
                    </div>
                    <label for="observacion">Observaciones</label>
                    <textarea name="observacion" id="observacion" class="form-control" cols="2" rows="4"></textarea>
                    <input type="hidden" name="idRf001" id="idRf001">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn" id="retornoPlaneacion"> Enviar</button>
                    <button type="button" class="btn btn-danger actionClosePlaneacion"
                        data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
