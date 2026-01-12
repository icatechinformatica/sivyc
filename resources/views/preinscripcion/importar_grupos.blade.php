@extends('theme.sivyc.layout')

@section('title', 'Importar Grupos | Sivyc Icatech')

@section('content')
<div class="card-header">
    <h3 class="card-title">Importar Grupos por Excel</h3>
</div>
<div class="card card-light">
    <div class="card-body">
        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ session('message') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ session('error') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <h4>Instrucciones</h4>
                <p>El archivo Excel debe contener las siguientes columnas en este orden exacto:</p>
                <ol>
                    <li><strong>UNIDAD</strong>: Nombre de la unidad móvil</li>
                    <li><strong>CURSO</strong>: Nombre exacto del curso (case-insensitive)</li>
                    <li><strong>INICIO</strong>: Fecha de inicio (formato: DD/MM/YYYY o YYYY-MM-DD)</li>
                    <li><strong>FIN</strong>: Fecha de fin (formato: DD/MM/YYYY o YYYY-MM-DD)</li>
                    <li><strong>HORA INICIO</strong>: Hora de inicio (ejemplo: "11:00 a.m.")</li>
                    <li><strong>HORA FIN</strong>: Hora de fin (ejemplo: "04:00 p.m.")</li>
                    <li><strong>CURP</strong>: CURP del instructor</li>
                </ol>
                
                <div class="alert alert-info mt-3">
                    <strong>Nota:</strong> Los siguientes campos se generarán automáticamente:
                    <ul class="mb-0">
                        <li>ID de curso (formato YYUUUXXXX)</li>
                        <li>Folio de grupo (formato CCT-AÑOXXXX)</li>
                        <li>Modalidad: EXT (Extensión)</li>
                        <li>Medio virtual: ZOOM</li>
                        <li>Tipo: CURSO</li>
                        <li>Organismo: CAPACITACION ABIERTA</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Cargar Archivo</h5>
                        <form action="{{ route('preinscripcion.importar_grupos.preview') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="archivo_excel">Seleccionar archivo Excel:</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('archivo_excel') is-invalid @enderror" 
                                           id="archivo_excel" name="archivo_excel" accept=".xlsx,.xls" required>
                                    <label class="custom-file-label" for="archivo_excel">Elegir archivo...</label>
                                </div>
                                @error('archivo_excel')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Formatos aceptados: .xlsx, .xls (Máx. 10MB)</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-eye"></i> Previsualizar Datos
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update label when file is selected
    document.getElementById('archivo_excel').addEventListener('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Elegir archivo...';
        var label = e.target.nextElementSibling;
        label.textContent = fileName;
    });
</script>
@endsection
