<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">OBSERVACIONES SOBRE EL FOLIO N° <div id="noFolio"></div>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @can('observaciones.rf001')
                    <form id="sendComment" method="POST">
                        @csrf
                        <div class="new-comment-box mb-3">
                            <label for="observacion">Comentario:</label>
                            <textarea name="observacion" id="observacion" class="form-control" rows="3" placeholder="Agregar un comentario" {{ ($estado == 'REVISION') ? 'disabled' : '' }}></textarea>
                        </div>
                        <input type="hidden" name="folio" id="folio">
                        <input type="hidden" name="memo" id="memo">
                        <input type="submit" class="btn" value="COMENTAR" {{ ($estado == 'REVISION') ? 'disabled' : '' }}>
                    </form>
                @endcan

                <div class="comments-container">
                    <div id="observacionesModal" class="scroll-vertical"></div>
                    {{-- delegado administrativo --}}
                    {{-- @if (!empty($observaciones))
                        @foreach ($observaciones as $item)
                            <ul id="comments-list" class="comments-list">
                                <li>
                                    <div class="comment-main-level">
                                        <!-- Contenedor del Comentario -->
                                        <div class="comment-box">
                                            <div class="comment-head">
                                                <h6 class="comment-name by-author">
                                                    <a>Observación:</a>
                                                </h6>
                                                <span>hace 20 minutos</span>
                                                <i class="fa fa-reply"></i>
                                            </div>
                                            <div class="comment-content">
                                                {{ $item['comentario1'] }}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        @endforeach
                    @else
                        <ul id="comments-list" class="comments-list">
                            <li>
                                <div class="comment-main-level">
                                    <!-- Contenedor del Comentario -->
                                    <div class="comment-box">
                                        <div class="comment-head">
                                        </div>
                                        <div class="comment-content">
                                            NO HAY OBSERVACIONES REGISTRADAS.
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endif --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger action-close" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
