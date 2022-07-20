<hr style="border-color:dimgray">
<label>
    <h2>INFORMACIÓN TÉCNICA DEL CURSO</h2>
</label>

<hr style="border-color:dimgray">
<div class="form-row">
    <!-- Unidad -->
    <div class="form-group col-md-6">
        <label for="areaCursos" class="control-label">Nombre del curso</label>
        <input disabled placeholder="Nombre del curso" type="text" class="form-control" id="nombrecurso" name="nombrecurso" value="{{old('nombrecurso', $curso->nombre_curso)}}">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="entidadfederativa" class="contro-label">Entidad Federativa</label>
        <input placeholder="Entidad Federativa" type="text" class="form-control @error('entidadfederativa') is-invalid @enderror" id="entidadfederativa" name="entidadfederativa" value="{{old('entidadfederativa', $cartaDescriptiva->entidadfederativa ?? '')}}">
    </div>
    @error('entidadfederativa')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
    <div class="form-group col-md-4">
        <label for="cicloescolar" class="control-label">Ciclo Escolar</label>
        <input placeholder="Ciclo escolar" type="text" class="form-control @error('entidadfederativa') is-invalid @enderror" id="cicloescolar" name="cicloescolar" value="{{old('cicloescolar', $cartaDescriptiva->cicloescolar ?? '')}}">
    </div>
    @error('entidadfederativa')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
    <div class="form-group col-md-4">
        <label for="programaestrategico" class="control-label">Programa estrategico (Caso aplicable )</label>
        <input  placeholder="Programa Estrategico" type="text" class="form-control @error('entidadfederativa') is-invalid @enderror" id="programaestrategico" name="programaestrategico" value="{{old('programaestrategico', $cartaDescriptiva->programaestrategico ?? '')}}">
        @error('entidadfederativa')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-3">
        <label for="modalidad" class="control-label">Modalidad</label>
        <select disabled class="form-control" id="modalidad" name="modalidad">
            <option value="" selected disabled>--SELECCIONAR--</option>
            @if(isset ($cartaDescriptiva->modalidad) )
            <option value="EXT" {{$cartaDescriptiva->modalidad == 'EXT' ? 'selected' : ''}}>EXT</option>
            <option value="CAE" {{$cartaDescriptiva->modalidad == 'CAE' ? 'selected' : ''}}>CAE</option>
            @else
            <option value="EXT" {{$curso->modalidad == 'EXT' ? 'selected' : ''}}>EXT</option>
            <option value="CAE" {{$curso->modalidad == 'CAE' ? 'selected' : ''}}>CAE</option>
            @endif
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="tipo" class="contro-label">Tipo</label>
        <select class="form-control" id="tipo" name="tipo">
            <option value="" selected disabled>--SELECCIONAR--</option>
            <option value="A DISTANCIA" {{$curso->tipo_curso == 'A DISTANCIA' ? 'selected' : ''}}>A DISTANCIA</option>
            <option value="PRESENCIAL" {{$curso->tipo_curso == 'PRESENCIAL' ? 'selected' : ''}}>PRESENCIAL</option>
            <option value="PRESENCIAL Y A DISTANCIA" {{$curso->tipo_curso == 'PRESENCIAL Y A DISTANCIA' ? 'selected' : ''}}> PRESENCIAL Y A DISTANCIA</option>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="perfilidoneo" class="control-label">Perfil Idoneo del Instructor</label>
        <input disabled placeholder="Pefil idoneo" type="text" class="form-control" id="perfilidoneo" name="perfilidoneo" value="{{old('perfilidoneo', $curso->perfil ?? '') }}">
    </div>
    <div class="form-group col-md-3">
        <label for="duracion" class="control-label">Duracion en horas</label>
        <input disabled placeholder="Horas" type="number" class="form-control" id="duracion" name="duracion" value="{{old('duracion', $cartaDescriptiva->duracion ?? $curso->horas)}}">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="formacionlaboral" class="contro-label">Campo de Formacion Laboral Profesional</label>
        <input disabled placeholder="Formacion Laboral" type="text" class="form-control" id="formacionlaboral" name="formacionlaboral" value="{{ old('formacionlaboral', $area->formacion_profesional ?? '') }}">
    </div>
    <div class="form-group col-md-4">
        <label for="especialidad" class="control-label">Especialidad </label>
        <input disabled placeholder="Especialidad" type="text" class="form-control " id="especialidad" name="especialidad" onkeyup="buscarEspecialidad()" value="{{old('especialidad', $curso->especialidad ?? '')}}">
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
        <textarea placeholder="Objetivo General del Curso" class="form-control col-md-12" id="aprendizajeesperado" name="aprendizajeesperado">
        <?php if (isset($cartaDescriptiva->aprendizajeesperado)) echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->aprendizajeesperado)); ?>  
        </textarea>
    </div>
</div>


<div class="form-row">
    <div class="form-group col-md-12 col-sm-12">
        <label for="objetivos col-md-12" class="control-label">Objetivos especificos por tema:</label>
    </div>
    <div class="form-group col-md-12 col-sm-12">
        <textarea placeholder="Objetivos especificos por tema" class="form-control" id="objetivoespecifico" name="objetivoespecifico">
        <?php if (isset($cartaDescriptiva->objetivoespecifico)) echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->objetivoespecifico)); ?>  
        </textarea>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-12">
        <label for="transversabilidad" class="control-label">Transversabilidad con otros Cursos </label>
        <input placeholder="Transversabilidad" type="text" class="form-control" id="transversabilidad" name="transversabilidad" value="{{old('transversabilidad', $cartaDescriptiva->transversabilidad ?? '')}}">
    </div>
    <div class="form-group col-md-12">
        <label for="publico" class="control-label">Publico o personal al que va dirigido</label>
        <textarea placeholder="Publico o personal al que va dirigido" class="form-control" id="publico" name="publico" cols="15" rows="2">
        <?php if (isset($cartaDescriptiva->publico)) echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->publico)); ?>  
        </textarea>
    </div>
    <div class="form-group col-md-12">
        <label for="objetivos" class="control-label">Observaciones:</label>
        <textarea placeholder="Observaciones" class="form-control" id="observaciones" name="observaciones" cols="15" rows="5">
        <?php if (isset($cartaDescriptiva->observaciones)) echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->observaciones)); ?>  
        </textarea>
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
                    <td><a class="btn btn-success" onclick="agregarponderacion()" id="addPonderacion">Agregar</a></td>
                </tr>

            </tbody>
        </table>
        <div class="alert-custom" id="alert-evaluacion" style="display: none">
            <span class="closebtn-p" onclick="this.parentElement.style.display='none';">&times;</span>
            <strong><label for="" id="eval-msg"></label></strong>
        </div>
    </div>

    <input hidden="true" id="storePonderacionOld" class="@error('ponderacion')  is-invalid @enderror" value="{{$cartaDescriptiva->ponderacion ?? ''}}">
    <input hidden="true" name="ponderacion" id="storePonderacion" class="@error('ponderacion')  is-invalid @enderror">
</div>


<br><br>
<label>
    <h2>CONTENIDO TEMATICO</h2>
</label>
<hr style="border-color:dimgray">


<div class="form-row scrollable-cont-t">
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
                @if( isset($cartaDescriptiva->contenidoTematico) && $cartaDescriptiva->contenidoTematico!=null )

                @foreach($contenidoT as $value)
                <tr id="{{ $loop->index+1 }}">
                    <td data-toggle="modal" class="contenidoT" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">
                        <?php echo htmlspecialchars_decode(stripslashes($value->contenido)); ?>
                    </td>
                    <td data-toggle="modal" class="estrategiaD" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">
                        <?php echo htmlspecialchars_decode(stripslashes($value->estrategia)); ?>
                    </td>
                    <td data-toggle="modal" class="procesoE" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">
                        <?php echo htmlspecialchars_decode(stripslashes($value->proceso)); ?>
                    </td>
                    <td data-toggle="modal" class="duracion" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">
                        <?php echo htmlspecialchars_decode(stripslashes($value->duracion)); ?>
                    </td>
                    <td data-toggle="modal" class="contenidoE text-preview" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">
                        <?php echo htmlspecialchars_decode(stripslashes($value->contenidoExtra)); ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-tr" onclick="deleteRowContenidoT(this)">Eliminar</button>
                    </td>
                </tr>
                @endforeach
                @endif
                <tr class="body-element" id="1">
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

        <div class="alert-custom" id="alert-contenido" style="display: none">
            <span class="closebtn-p" onclick="this.parentElement.style.display='none';">&times;</span>
            <strong><label for="" id="contenido-msg"></label></strong>
        </div>
    </div>
</div>
<input hidden name="contenidoT" id="storeContenidoTOld" class="@error('contenidoT')  is-invalid @enderror" value="{{$cartaDescriptiva->contenidoTematico ?? ''}}">
<input hidden name="contenidoT" id="storeContenidoT" class="@error('contenidoT')  is-invalid @enderror">

<br><br>

<label>
    <h2>RECURSOS DIDACTICOS</h2>
</label>
<hr style="border-color:dimgray">
<div class="form-row ">
    <div class="form-group col-md-4 col-sm-6">
        <label for="elementoapoyo" class="control-label">Elementos de Apoyo</label>
        <textarea placeholder="Elementos de Apoyo" type="text" class="form-control" id="elementoapoyo" name="elementoapoyo">
            <?php if (isset($cartaDescriptiva->elementoapoyo)) echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->elementoapoyo)); ?>  
        </textarea>
    </div>
    <div class="form-group col-md-4 col-sm-6">
        <label for="auxenseñanza" class="control-label">Auxiliares de la enseñanza</label>
        <textarea placeholder="Auxiliares de le enseñanza" type="text" class="form-control" id="auxenseñanza" name="auxenseñanza">
            <?php if (isset($cartaDescriptiva->auxenseñanza)) echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->auxenseñanza)); ?>  
        </textarea>
    </div>
    <div class="form-group col-md-4 col-sm-6">
        <label for="referencias" class="control-label">Referencias</label>
        <textarea placeholder="Proceso Evaluacion" type="text" class="form-control" id="referencias" name="referencias">
            <?php if (isset($cartaDescriptiva->referencias)) echo htmlspecialchars_decode(stripslashes($cartaDescriptiva->referencias)); ?>  
        </textarea>
    </div>
</div>

<!-- Full Height Modal Right -->
<div class="modal fade bottom" id="modalTxtEditor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <!-- Add class .modal-full-height and then add class .modal-right (or other classes from list above) to set a position to the modal -->
    <div class="modal-dialog modal-full-height modal-bottom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="titleModal"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btnCloseModal">
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
                        <textarea placeholder="Introduzca el contenido" type="text" class="form-control" id="contenidoT-inp" name="contenidoT-inp">

                        </textarea>
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