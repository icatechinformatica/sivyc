<hr style="border-color:dimgray">
<label>
    <h2>Material Didactico del curso</h2>
</label>
<div class="alert-custom" id="alert-files" style="display: none">
    <span class="closebtn-p" onclick="this.parentElement.style.display='none';">&times;</span>
    <strong><label for="" id="files-msg"></label></strong>
</div>
<hr style="border-color:dimgray">

<table class="table table-striped col-md-10">
    <thead>
        <tr>
            <th class="h4" scope="col"></th>
            <th class="h4" scope="col"></th>
            <th class="h4 text-center" scope="col" colspan="2">Seleccionar</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class="h6" scope="row"> CARTA DESCRIPTIVA </th>
            <th></th>
            <th class="text-center">
                <a id="botonCARTADESCPDF" class="nav-link">
                    <i class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                </a>
            </th>
            <th class="text-center">
                <a id="botonCARTADESCWORD" class="nav-link">
                    <i class="fa fa-file-word-o  fa-2x fa-lg text-light"></i>
                </a>
            </th>
        </tr>

        <tr>
            <th class="h6" scope="row"> MANUAL DIDACTICO</th>
            <th></th>
            <th class="text-center">
                <a id="botonMANUALDIDPDF" class="nav-link">
                    <i class="fa fa-file-pdf-o  fa-2x fa-lg text-light"></i>
                </a>
            </th>
            <th class="text-center">
                <a id="botonMANUALWORD" class="nav-link">
                    <i class="fa fa-file-word-o  fa-2x fa-lg text-light"></i>
                </a>
            </th>
        </tr>
        <tr>
            <th class="h6" scope="row"> GUIA DE APRENDIZAJE</th>
            <th></th>
            <th class="text-center">
                <a id="botonMANUALDIDPDF" class="nav-link">
                    <i class="fa fa-file-powerpoint-o  fa-2x fa-lg text-light"></i>
                </a>
            </th>
            <th class="text-center">
                <a id="botonMANUALWORD" class="nav-link">
                    <i class="fa fa-file-word-o  fa-2x fa-lg text-light"></i>
                </a>
            </th>
        </tr>

        <tr>
            <th class="h6" scope="row"> EVALUACION ALUMNO </th>
            <th></th>
            <th class="text-center">
                <a id="botonEVALALUMNPDF" class="nav-link">
                    <i class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                </a>
            </th>
            <th class="text-center">
                <a id="botonEVALALUMNWORD" class="nav-link">
                    <i class="fa fa-file-word-o  fa-2x fa-lg text-light"></i>
                </a>
            </th>
        </tr>
        <tr>
            <th class="h6" scope="row"> EVALUACION DE CURSO E INSTRUCTOR </th>
            <th></th>
            <th class="text-center">
                <a id="botonEVALINSTRUCTORPDF" class="nav-link">
                    <i class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                </a>
            </th>
            <th class="text-center">
                <a id="botonEVALINSTRUCTORWORD" class="nav-link">
                    <i class="fa fa-file-word-o  fa-2x fa-lg text-light"></i>
                </a>
            </th>
        </tr>



        <tr>
            <th colspan="4"></th>
        </tr>
    </tbody>
</table>

<input type="text" hidden id="idCurso" value="{{$idCurso}}">