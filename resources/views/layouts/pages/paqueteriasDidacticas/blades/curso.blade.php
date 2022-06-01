
<hr style="border-color:dimgray">
<label>
    <h2>INFORMACIÓN TÉCNICA DEL CURSO</h2>
</label>

<hr style="border-color:dimgray">
<div class="form-row">
    <!-- Unidad -->
    <div class="form-group col-md-6">
        <label for="areaCursos" class="control-label">Nombre del curso</label>
        <input placeholder="Nombre del curso" type="text" class="form-control" id="nombrecurso" name="nombrecurso">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="entidadfederativa" class="contro-label">Entidad Federativa</label>
        <input placeholder="Entidad Federativa" type="text" class="form-control" id="entidadfederativa" name="entidadfederativa">
    </div>
    <div class="form-group col-md-4">
        <label for="cicloescolar" class="control-label">Ciclo Escolar</label>
        <input placeholder="Ciclo escolar" type="text" class="form-control" id="cicloescolar" name="cicloescolar">
    </div>
    <div class="form-group col-md-4">
        <label for="modalidad" class="control-label">Nivel</label>
        <select class="form-control" id="modalidad" name="modalidad">
            <option value="" selected disabled>--SELECCIONAR--</option>
            <option value="EXT">EXT</option>
            <option value="CAT">CAT</option>
        </select>
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-4">
        <label for="tipo" class="contro-label">Tipo</label>
        <select class="form-control" id="tipo" name="tipo">
            <option value="" selected disabled>--SELECCIONAR--</option>
            <option value="distancia">Distancia</option>
            <option value="presencial">Presencias</option>
            <option value="ambos">Distancia y Presencial</option>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="perfilidoneo" class="control-label">Perfil Idoneo del Instructor</label>
        <input placeholder="Pefil idoneo" type="text" class="form-control" id="perfilidoneo" name="perfilidoneo">
    </div>
    <div class="form-group col-md-4">
        <label for="duracion" class="control-label">Duracion en horas</label>
        <input placeholder="Horas" type="number" class="form-control" id="duracion" name="duracion">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="formacionlaboral" class="contro-label">Campo de Formacion Laboral Profesional</label>
        <input placeholder="Formacion Laboral" type="text" class="form-control" id="formacionlaboral" name="formacionlaboral">
    </div>
    <div class="form-group col-md-4">
        <label for="especialidad" class="control-label">Especialidad</label>
        <input placeholder="Especialidad" type="text" class="form-control" id="especialidad" name="especialidad">
    </div>
    <div class="form-group col-md-4">
        <label for="publico" class="control-label">Publico o personal al que va dirigido</label>
        <textarea placeholder="Publico o personal al que va dirigido" class="form-control" id="publico" name="publico" cols="15" rows="5"></textarea>
    </div>
</div>

<hr style="border-color:dimgray">
<label>
    <h2>INFORMACION ACADEMICA DEL CURSO</h2>
</label>
<hr style="border-color:dimgray">
<div class="form-row">

    <div class="row col-md-4 col-sm-12">
        <div class="form-group col-md-12 col-sm-12">
            <label for="aprendizajeesperado" class="control-label">Aprendizaje Esperado</label>
            <textarea placeholder="Aprendizaje Esperado" class="form-control" id="aprendizajeesperado" name="aprendizajeesperado" cols="15" rows="5"></textarea>
        </div>
    </div>

    <div class="row col-md-8 col-sm-12" id="row-criterios">
        <div class="form-group col-md-5 col-sm-5">
            <label for="criterio" class="control-label">Proceso de evaluacion</label>
            <input placeholder="P.E. Examen" type="text" class="form-control" id="criterio" name="criterio">
        </div>
        <div class="form-group col-md-4 col-sm-4">
            <label for="ponderacion" class="control-label">Ponderacion</label>
            <input placeholder="%" type="number" class="form-control" id="ponderacion" name="ponderacion">
        </div>

        <div class="form-group col-md-2 col-sm-2">
            <a class="btn btn-warning" onclick="agregarponderacion()">Agregar</a>
        </div>

        <div class="form group col-md-12 col-sm-12">
            <table id="" class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Criterio</th>
                        <th>%</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tEvaluacion">
                </tbody>
            </table>
        </div>

    </div>


</div>

<div class="form-row">
    <label for="objetivos" class="control-label">Objetivos especificos por tema:</label>
    <textarea placeholder="Objetivos especificos por tema" class="form-control" id="objetivos" name="objetivos" cols="15" rows="5"></textarea>
</div>

<br>
<div class="form-row">
    <div class="form-group col-md-3 col-sm-6">
        <label for="contenidotematico" class="control-label">Contenido Tematico</label>
        <input placeholder="Contenido Tematico" type="text" class="form-control" id="contenidotematico" name="contenidotematico">
    </div>
    <div class="form-group col-md-3 col-sm-6">
        <label for="estrategiadidactica" class="control-label">Estrategia Didactica</label>
        <input placeholder="Contenido Tematico" type="text" class="form-control" id="estrategiadidactica" name="estrategiadidactica">
    </div>
    <div class="form-group col-md-3 col-sm-6">
        <label for="procesoevaluacion" class="control-label">Proceso Evaluacion</label>
        <input placeholder="Proceso Evaluacion" type="text" class="form-control" id="procesoevaluacion" name="procesoevaluacion">
    </div>
    <div class="form-group col-md-2 col-sm-6">
        <label for="duracion" class="control-label">Duracion</label>
        <input placeholder="Duracion" type="text" class="form-control" id="duracionT" name="duracionT">
    </div>
    <div class="form-group col-md-1 col-sm-2">
        <a class="btn btn-warning" onclick="agregarContenidoT()">Agregar</a>
    </div>

</div>
<div class="form-row">
    <div class="form group col-md-12 col-sm-12">
        <table id="" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Contenido Tematico</th>
                    <th>Estrategia Didactica</th>
                    <th>Proceso Evaluacion</th>
                    <th>Duracion</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tTemario">
            </tbody>
        </table>
    </div>
</div>
