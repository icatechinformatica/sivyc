<hr style="border-color:dimgray">
<label>
    <h2>EVALUACIÓN DE APRENDIZAJE AL ALUMNO</h2>
</label>

<hr style="border-color:dimgray">
<div class="form-row">
    <div class="card-paq col-md-12">
        <div class="contentBx col-md-12">
            <div class="form-group col-md-12 col-sm-12">
                <label for="instrucciones" class="control-label">INSTRUCCIONES</label>
                <textarea placeholder="Agrege aqui las instrucciones para la evaluacion del alumno" class="form-control" id="instrucciones" name="instrucciones" cols="15" rows="5"><?php if(isset($evaluacionAlumno->instrucciones)) echo $evaluacionAlumno->instrucciones;?></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row col-md-12" id="preguntas-area-parent">
    <input type="number" hidden id="numPreguntas" name="numPreguntas" value="1">
    <div class="card-paq col-md-12">
        <div class="contentBx col-md-12">
            <br>
            <div class="row col-md-12" id="pregunta1">
                <div class="form-row col-md-12 hideable">
                    <!-- selects -->

                    <div class="form-group col-md-6 col-sm-6">
                        <select onchange="cambiarTipoPregunta(this)" class="form-control" name="pregunta1-tipo">
                            <option value="multiple">Multiple</option>
                            <option value="abierta">Abierta</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6 col-sm-6">

                        <select class="form-control contenidoTematicoPregunta" name="pregunta1-contenidoT">
                            <option disbled selected value="">Contenido tematico de la pregunta</option>
                        </select>
                    </div>

                </div>
                <div class="form-row col-md-7 col-sm-12">
                    <div class="form-group col-md-12 col-sm-10">
                        <input placeholder="Pregunta sin texto" type="text" class="form-control resp-abierta g-input" name="pregunta1">
                    </div>
                </div>



                <div class="form-row col-md-7 opcion-area-p1" id="pregunta1-opc">
                    <input type="text" hidden id="pregunta1-opc-answer" name="pregunta1-opc-answer">
                    <div class="input-group mb-3 ">

                        <div class="input-group-text">
                            <input type="radio" onclick="setAceptedAnswer(this)" name="pregunta1-opc-correc[]">
                        </div>

                        &nbsp;&nbsp;&nbsp;
                        <input placeholder="Opcion" type="text" class="form-control resp-abierta multiple" name="pregunta1-opc[]">
                        <a class="btn btn-warning btn-circle m-1 btn-circle-sm" onclick="removerOpcion(this)">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                </div>

                <div class="form-row col-md-6 opcion-area-pregunta1 hideable">
                    <div class="input-group mb-3">
                        <a style="cursor: default;" onclick="agregarOpcion(this)">Agregar opcion</a>
                    </div>
                </div>

                <div class="form-row col-md-7 respuesta-abierta-area ra-p1" style="display: none">
                    <div class="input-group mb-3">
                        <input placeholder="Texto de la respuesta abierta" name="pregunta1-resp-abierta" type="text" class="form-control resp-abierta">
                    </div>
                </div>
            </div>

            <div class="row opciones col-md-12 hideable">
                <div class="col-md-10">
                    <div class="form-group col-md-4">
                        <a style="cursor: default;" onclick="agregarPregunta(this)" class="btn btn-success">Agregar Pregunta</a>
                    </div>
                </div>
                <div class="form-row col-md-2" style="display:none">
                    <div class="form-group col-md-1 col-sm-6">

                        <button type="button" class="btn btn-danger" onclick="removerPregunta(this)">
                            <i class="fa fa-trash"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>








<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-right">
            <!-- @can('cursos.store') -->
            
            <button type="submit" class="btn btn-primary">Guardar</button>
            <!-- @endcan -->
        </div>
    </div>
</div>
