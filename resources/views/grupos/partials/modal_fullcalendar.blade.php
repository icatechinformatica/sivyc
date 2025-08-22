<!-- Modal FullCalendar -->
<div class="modal fade" id="modalFullCalendar" tabindex="-1" role="dialog" aria-labelledby="modalFullCalendarLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFullCalendarLabel">Agenda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-8 col-md-8 mb-2 mb-md-0">
                        <div id="calendar"></div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="card border shadow-sm">
                            <div class="p-3">
                                <div class="mb-3 p-2 border-light rounded border">
                                    <h6 class="mb-2">Periodo seleccionado</h6>
                                    <div id="fc-seleccion-detalle" class="small text-black">Ninguno</div>
                                    <div class="mt-2 d-flex gap-2">
                                        <button id="btn-agenda-eliminar-periodo" type="button"
                                            class="btn btn-outline-danger btn-sm" disabled>
                                            Eliminar periodo seleccionado
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex px-2">
                                <div>
                                    <input type="color" id="color_fondo" name="color_fondo" value="#ff0000"
                                        class="select-color">
                                    <span class="ml-2">Color de fondo</span>
                                </div>
                                <div class="ml-2">
                                    <input type="color" id="color_texto" name="color_texto" value="#ff0000"
                                        class="select-color">
                                    <span class="ml-2">Color del texo</span>
                                </div>
                            </div>
                            <div class="align-items-center p-3">
                                <div class="mt-3">
                                    <h6 class="mb-2">Horario para la selección</h6>
                                    <div class="form-group mb-2">
                                        <label for="agenda_hora_inicio" class="mb-1 small text-muted">Hora
                                            inicio</label>
                                        <input type="time" id="agenda_hora_inicio" class="form-control form-control-sm"
                                            value="08:00">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="agenda_hora_fin" class="mb-1 small text-muted">Hora fin</label>
                                        <input type="time" id="agenda_hora_fin" class="form-control form-control-sm"
                                            value="10:00">
                                    </div>
                                    <button id="btn-agenda-aplicar-seleccion" type="button"
                                        class="btn btn-primary btn-block btn-sm" disabled>
                                        Aplicar a días seleccionados
                                    </button>
                                    <div class="small text-muted mt-2">
                                        Selecciona uno o varios días en el calendario y luego aplica el horario.
                                    </div>
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" value="" id="alimentos">
                                        <label class="form-check-label" for="alimentos">
                                            ¿Incluir hora de alimentos?
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="align-items-center p-3">
                                <h5 class="card-title text-primary mb-4 text-center">
                                    <i class="fa fa-info-circle mr-2"></i>Datos del grupo
                                </h5>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-secondary mr-2">Horas del curso</span>
                                        <span id="fc-horas-maximas" class="fw-bold fs-5 text-dark">{{
                                            decimal_a_hora($grupo->curso->horas) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-secondary mr-2">Horas agendadas</span>
                                        <span id="fc-horas-totales" class="fw-bold fs-5 text-dark">{{
                                            decimal_a_hora($grupo->horasTotales()) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-secondary mr-2">Dias totales</span>
                                        <span id="fc-dias-horas" class="fw-bold fs-5 text-dark">{{
                                            $grupo->contarFechasSeleccionadas() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-secondary mr-2">Horas por asignar restantes</span>
                                        <span id="fc-horas-restantes" class="fw-bold fs-5 text-dark">{{
                                            decimal_a_hora($grupo->curso->horas - $grupo->horasTotales()) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>