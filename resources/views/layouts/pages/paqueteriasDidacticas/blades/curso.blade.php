<hr style="border-color:dimgray">
<label>
    <h2>INFORMACIÓN TÉCNICA DEL CURSO</h2>
</label>

<hr style="border-color:dimgray">
<div class="form-row">
    <!-- Unidad -->
    <div class="form-group col-md-6">
        <label for="areaCursos" class="control-label">Nombre del curso</label>
        <input placeholder="Nombre del curso" type="text" class="form-control" id="nombrecurso" name="nombrecurso" value="{{old('nombrecurso', $curso->nombre_curso)}}">
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
        <label for="programaestrategico" class="control-label">Programa estrategico (Caso aplicable )</label>
        <input placeholder="Programa Estrategico" type="text" class="form-control" id="programaestrategico" name="programaestrategico">
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-3">
        <label for="modalidad" class="control-label">Modalidad</label>
        <select class="form-control" id="modalidad" name="modalidad">
            <option value="" selected disabled>--SELECCIONAR--</option>
            <option value="EXT" {{$curso->modalidad == 'EXT' ? 'selected' : ''}}>EXT</option>
            <option value="CAE" {{$curso->modalidad == 'CAE' ? 'selected' : ''}}>CAE</option>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="tipo" class="contro-label">Tipo</label>
        <select class="form-control" id="tipo" name="tipo">
            <option value="" selected disabled>--SELECCIONAR--</option>
            <option value="A DISTANCIA" {{$curso->tipo_curso == 'A DISTANCIA' ? 'selected' : ''}}>A DISTANCIA</option>
            <option value="PRESENCIAL" {{$curso->tipo_curso == 'PRESENCIAL' ? 'selected' : ''}}>PRESENCIAL</option>
            <option value="A DISTANCIA Y PRESENCIAL">A DISTANICA Y PRESENCIAL</option>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="perfilidoneo" class="control-label">Perfil Idoneo del Instructor</label>
        <input placeholder="Pefil idoneo" type="text" class="form-control" id="perfilidoneo" name="perfilidoneo">
    </div>
    <div class="form-group col-md-3">
        <label for="duracion" class="control-label">Duracion en horas</label>
        <input placeholder="Horas" type="number" class="form-control" id="duracion" name="duracion" value="{{old('duracion', $curso->duracion)}}">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="formacionlaboral" class="contro-label">Campo de Formacion Laboral Profesional</label>
        <input placeholder="Formacion Laboral" type="text" class="form-control" id="formacionlaboral" name="formacionlaboral">
    </div>
    <div class="form-group col-md-4">
        <label for="especialidad" class="control-label">Especialidad </label>
        <input placeholder="Especialidad" type="text" class="form-control " id="especialidad" name="especialidad" onkeyup="buscarEspecialidad()">
        <ul id="searchResult" class="searchResult"></ul>
    </div>
</div>

<br><br>
<label>
    <h2>INFORMACION ACADEMICA DEL CURSO</h2>
</label>
<hr style="border-color:dimgray">


<div class="form-row">
    <div class="form-group col-md-12 col-sm-12">
        <label for="aprendizajeesperado" class="control-label">Objetivo General (Aprendizaje Esperado)</label>
    </div>
    <div class="form-group col-md-12 col-sm-12">
        <textarea placeholder="Objetivo General del Curso" class="form-control col-md-12" id="aprendizajeesperado" name="aprendizajeesperado" c¿></textarea>
    </div>
</div>


<div class="form-row">
    <div class="form-group col-md-12 col-sm-12">
        <label for="objetivos col-md-12" class="control-label">Objetivos especificos por tema:</label>
    </div>
    <div class="form-group col-md-12 col-sm-12">
        <textarea placeholder="Objetivos especificos por tema" class="form-control" id="objetivoespecifico" name="objetivoespecifico"></textarea>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-12">
        <label for="transversabilidad" class="control-label">Transversabilidad con otros Cursos </label>
        <input placeholder="Transversabilidad" type="text" class="form-control" id="transversabilidad" name="transversabilidad">
    </div>
    <div class="form-group col-md-12">
        <label for="publico" class="control-label">Publico o personal al que va dirigido</label>
        <textarea placeholder="Publico o personal al que va dirigido" class="form-control" id="publico" name="publico" cols="15" rows="2"></textarea>
    </div>
    <div class="form-group col-md-12">
        <label for="objetivos" class="control-label">Observaciones:</label>
        <textarea placeholder="Observaciones" class="form-control" id="observaciones" name="observaciones" cols="15" rows="5"></textarea>
    </div>
</div>


<div class="form-row">
    <div class="form group col-md-12 col-sm-12">
        <label for="criterio" class="control-label">Proceso de evaluacion</label>
        <table id="" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Criterio</th>
                    <th>%</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tEvaluacion">
                <tr>
                    <td><input placeholder="P.E. Examen" type="text" class="form-control" id="criterio" name="criterio"></td>
                    <td><input placeholder="%" type="number" class="form-control" id="ponderacion" name="ponderacion"></td>
                    <td><a class="btn btn-success" onclick="agregarponderacion()">Agregar</a></td>

                </tr>
            </tbody>
        </table>
    </div>
    <input hidden="true" name="ponderacion" id="storePonderacion" class="@error('ponderacion')  is-invalid @enderror" value="{{old('ponderacion')}}">
</div>


<br><br>
<label>
    <h2>CONTENIDO TEMATICO</h2>
</label>
<hr style="border-color:dimgray">


<div class="form-row">
    <div class="form group col-md-12 col-sm-12">
        <table id="tableContenidoT" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Contenido Tematico</th>
                    <th>Estrategia Didactica</th>
                    <th>Proceso Evaluacion</th>
                    <th>Duracion</th>
                    <th>Contenido Extra</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tTemario">
                <tr id="1">
                    <td data-toggle="modal" class="contenidoT" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">Click aqui para agregar contenido</td>
                    <td data-toggle="modal" class="estrategiaD" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">Click aqui para agregar contenido</td>
                    <td data-toggle="modal" class="procesoE" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">Click aqui para agregar contenido</td>
                    <td data-toggle="modal" class="duracion" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">Click aqui para agregar contenido</td>
                    <td data-toggle="modal" class="contenidoE text-preview" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">Click aqui para agregar contenido</td>
                    <td>
                        <button type="button" class="btn btn-danger remove-tr" onclick="deleteRowContenidoT(this)" style="display: none;">Eliminar</button>
                        <button type="button" class="btn btn-success" onclick="addRowContenidoT(this)">Agregar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<input hidden name="contenidoT" id="storeContenidoT" class="@error('contenidoT')  is-invalid @enderror">

<br><br>

<div class="form-row ">
    <div class="form-group col-md-4 col-sm-6">
        <label for="elementoapoyo" class="control-label">Elementos de Apoyo</label>
        <textarea placeholder="Elementos de Apoyo" type="text" class="form-control" id="elementoapoyo" name="elementoapoyo"></textarea>
    </div>
    <div class="form-group col-md-4 col-sm-6">
        <label for="auxenseñanza" class="control-label">Auxiliares de la enseñanza</label>
        <textarea placeholder="Auxiliares de le enseñanza" type="text" class="form-control" id="auxenseñanza" name="auxenseñanza"></textarea>
    </div>
    <div class="form-group col-md-4 col-sm-6">
        <label for="referencias" class="control-label">Referencias</label>
        <textarea placeholder="Proceso Evaluacion" type="text" class="form-control" id="referencias" name="referencias"></textarea>
    </div>
    <div class="form-group col-md-1 col-sm-2">
        <a class="btn btn-warning" onclick="agregarRecursosD()">Agregar</a>
    </div>

</div>
<div class="form-row">
    <div class="form group col-md-12 col-sm-12">
        <table id="" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Elementos de Apoyo</th>
                    <th>Auxiliares de la enseñanzas</th>
                    <th>Referecias</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tRecursosD">
            </tbody>
        </table>
    </div>
</div>

<input hidden="true" name="recursosD" id="storeRecursosD" class="@error('recursosD')  is-invalid @enderror">



<!-- Full Height Modal Right -->
<div class="modal fade right" id="modalTxtEditor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <!-- Add class .modal-full-height and then add class .modal-right (or other classes from list above) to set a position to the modal -->
    <div class="modal-dialog modal-full-height modal-right" role="document" style="width: 650px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="titleModal"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-row">
                    <input type="text" name="" id="contenidoValues" hidden>
                    <input type="text" name="" id="estrategiaValues" hidden>
                    <input type="text" name="" id="procesoValues" hidden>
                    <input type="text" name="" id="duracionValues" hidden>
                    <input type="text" name="" id="contenidoExtraValues" hidden>


                    <div class="form-group col-md-12" style="display: none" id="temaPrincipal">
                        <label for="mainSubject" class="control-label">Tema Principal </label>
                        <input placeholder="Tema principal del modulo" type="text" class="form-control" id="inpTemaPrincipal">
                    </div>
                    <div class="form-group col-md-12 col-sm-6" id="contenidoT">
                        <textarea placeholder="Introduzca el contenido" type="text" class="form-control" id="contenidoT-inp" name="contenidoT-inp"></textarea>
                    </div>
                    <div class="form-group col-md-12 col-sm-2">
                        <a class="btn btn-warning" onclick="setValuesEditor()">Guardar</a>
                    </div>

                </div>
                <div id="contextoModalTextArea"></div>
            </div>
        </div>
    </div>
</div>
<!-- Full Height Modal Right -->