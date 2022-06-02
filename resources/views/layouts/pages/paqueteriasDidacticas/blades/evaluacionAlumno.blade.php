<hr style="border-color:dimgray">
<label>
    <h2>EVALUACIÓN DE APRENDIZAJE AL ALUMNO</h2>
</label>

<hr style="border-color:dimgray">
<div class="form-row">
    <div class="form-group col-md-12 col-sm-12">
        <label for="instrucciones" class="control-label">INSTRUCCIONES</label>
        <textarea placeholder="Agrege aqui las instrucciones para la evaluacion del alumno" class="form-control" id="instrucciones" name="instrucciones" cols="15" rows="5"></textarea>
    </div>
</div>

<div class="row col-md-12" id="preguntas-area-parent">

    <div class="row col-md-12" id="preguntas-area-children1">
        <div class="form-row col-md-7 col-sm-12">
            <div class="form-group col-md-12 col-sm-10">
                <label for="pregunta0" class="control-label">PREGUNTA</label>
                <textarea placeholder="pregunta" class="form-control" id="pregunta0" name="pregunta0" cols="15" rows="2"></textarea>
            </div>
        </div>

        <div class="form-row col-md-5 ">
            <div class="form-group col-md-12 col-sm-6">
                <label for="tipopregunta" class="control-label">TIPO DE PREGUNTA</label>
                <select onchange="cambiarTipoPregunta()" class="form-control" id="tipopregunta" name="tipopregunta">
                    <option value="multiple" selected>Multiple</option>
                    <option value="abierta">Abierta</option>
                </select>
            </div>
        </div>


        <div class="form-row col-md-7 respuestas-area" id="parent-resp">
            <div class="input-group mb-3" id="child-resp1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" aria-label="Checkbox for following text input" id="respuesta1-p1">
                    </div>
                </div>
                <input placeholder="Opcion" type="text" class="form-control" id="opcion1-p1" name="opcion1-p1">
            </div>
        </div>

        <div class="form-row col-md-6 respuestas-area">
            <div class="input-group mb-3">
                <a style="cursor: default;" onclick="agregarOpcion()">Agregar opcion</a>
            </div>
        </div>
        <!--se oculta por medio del class-->
        <div class="form-row col-md-7 respuesta-abierta-area" style="display: none">
            <div class="input-group mb-3">
                <input disabled placeholder="Texto de la respuesta abierta" type="text" class="form-control resp-abierta">
            </div>
        </div>
    </div>

</div>


<div class="form-group col-md-2 col-sm-6">
    <a style="cursor: default;" onclick="agregarPregunta()" class="btn btn-primary">Agregar Pregunta</a>
</div>|