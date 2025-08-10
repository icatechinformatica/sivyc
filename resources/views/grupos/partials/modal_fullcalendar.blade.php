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
                        <div class="card h-100 border shadow-sm">
                            <div class="align-items-center p-3">
                                <div>
                                    <input type="color" id="color_fondo" name="color_fondo" value="#ff0000" class="select-color">
                                    <span class="ml-2">Color de fondo</span>
                                </div>
                                <div>
                                    <input type="color" id="color_texto" name="color_texto" value="#ff0000" class="select-color">
                                    <span class="ml-2">Color del texo</span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center h-100">
                                <h5 class="card-title text-primary mb-4 text-center">
                                    <i class="fa fa-info-circle mr-2"></i>Datos del grupo
                                </h5>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-secondary mr-2">Horas totales del grupo</span>
                                        <span id="fc-horas-totales" class="fw-bold fs-5 text-dark">{{
                                            decimal_a_hora($grupo->horasTotales()) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-secondary mr-2">Días seleccionados/hora</span>
                                        <span id="fc-dias-horas" class="fw-bold fs-5 text-dark">{{
                                            $grupo->contarFechasSeleccionadas() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-secondary mr-2">Horas por asignar restantes</span>
                                        <span id="fc-horas-restantes" class="fw-bold fs-5 text-dark">{{
                                            decimal_a_hora($grupo->curso->horas - $grupo->horasTotales()) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-secondary mr-2">Horas máximas del curso</span>
                                        <span id="fc-horas-maximas" class="fw-bold fs-5 text-dark">{{
                                            decimal_a_hora($grupo->curso->horas) }}</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center small text-muted">
                                    <i class="fa fa-calendar-alt mr-2"></i>Actualiza los datos seleccionando días y
                                    horas en el calendario
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>