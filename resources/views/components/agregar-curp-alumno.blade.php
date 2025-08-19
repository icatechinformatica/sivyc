<div class="modal fade" id="addCurpModal" tabindex="-1" role="dialog" aria-labelledby="addCurpModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCurpModalLabel">Agregar Alumno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="form-group">
                        <div class="alert alert-info" role="alert">
                            <b>Nota:</b> El alumno no fue encontrado. Se recomienda iniciar el proceso de registro.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Curp:</label>
                        <input type="text" class="form-control" name="curpAConsultar" id="curpAConsultar"
                            value="{{ $curp }}">
                    </div>
                    <input type="hidden" name="grupo_id" id="grupo_id" value="{{ $grupoId }}">
                    <a href="{{ route('alumnos.nuevo.registro.alumno', [base64_encode($curp), base64_encode($grupoId)]) }}" class="btn btn-success">Iniciar Proceso</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

@push('script_sign')
    <script>
        // Aquí puedes agregar el código JavaScript que necesites
    </script>
@endpush
