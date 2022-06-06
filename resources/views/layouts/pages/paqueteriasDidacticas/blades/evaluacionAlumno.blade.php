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

    <div class="row col-md-12" id="pregunta1">
        <div class="form-row col-md-7 col-sm-12">
            <div class="form-group col-md-12 col-sm-10">
                <label for="p1" class="control-label">PREGUNTA</label>
                <textarea placeholder="pregunta" class="form-control" id="p1" name="p1" cols="15" rows="2"></textarea>
            </div>
        </div>

        <div class="form-row col-md-5 ">
            <div class="form-group col-md-12 col-sm-6">
                <label for="tipopregunta" class="control-label">TIPO DE PREGUNTA</label>
                <select onchange="cambiarTipoPregunta('p1')" class="form-control" id="tipopregunta-p1" name="tipopregunta-p1">
                    <option value="multiple" selected>Multiple</option>
                    <option value="abierta">Abierta</option>
                </select>
            </div>
        </div>


        <div class="form-row col-md-7 opcion-area-p1" id="opc-p1">
            <div class="input-group mb-3 ">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" onclick="setAceptedAnswer(this)"  id="resp-1-p1">
                    </div>
                </div>
                &nbsp;&nbsp;&nbsp;
                <input placeholder="Opcion" type="text" class="form-control resp-abierta multiple" >
                <a class="btn btn-warning btn-circle m-1 btn-circle-sm" onclick="removerOpcion(this)" >
                    <i class="fa fa-minus"></i>
                </a>
            </div>
        </div>

        <div class="form-row col-md-6 opcion-area-p1">
            <div class="input-group mb-3">
                <a style="cursor: default;" onclick="agregarOpcion(this)">Agregar opcion</a>
            </div>
        </div>
        <!--se oculta por medio del class-->
        <div class="form-row col-md-7 respuesta-abierta-area ra-p1" style="display: none">
            <div class="input-group mb-3">
                <input disabled placeholder="Texto de la respuesta abierta" type="text" class="form-control resp-abierta">
            </div>
        </div>
    </div>

</div>


<div class="form-group col-md-2 col-sm-6">
    <a style="cursor: default;" onclick="agregarPregunta()" class="btn btn-primary">Agregar Pregunta</a>
</div>