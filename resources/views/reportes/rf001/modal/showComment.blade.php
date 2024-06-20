<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">OBSERVACIONES SOBRE EL FOLIO N°</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{ Form::open(['method' => 'POST', 'id' => 'sendComment_']) }}
                    {!! Form::token() !!}
                    <div class="new-comment-box mb-3">
                        {!! Form::textarea('observacion_', null,['id' =>'observacion_', 'class' => 'form-control','rows' =>'3']) !!}
                    </div>
                    <input type="submit" value="COMENTAR"/>
                {{  Form::close()  }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
