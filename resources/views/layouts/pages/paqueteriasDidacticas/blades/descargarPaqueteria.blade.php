<hr style="border-color:dimgray">
<label>
    <h2>Material Didactico del curso</h2>
</label>
<hr style="border-color:dimgray">
<div class="form-row">
    <div class="form-group col-md-12 col-sm-12">
        <label for="instrucciones" class="control-label">Descargar Paqueterias:</label>
        <div class="thumb"><i class="mdi mdi-file-image"></i></div>
        <div class="details">
            <p class="file-name"></p>
            <div class="buttons">
                <a href="{{ route('DescargarPaqueteria',$curso->id) }}" class="view" target="_blank">View</a>
                <a href="{{ route('DescargarPaqueteria',$curso->id) }}" class="download">Download</a>
            </div>
        </div>
    </div>
</div>