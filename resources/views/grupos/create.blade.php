@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-8">
        <span>Grupos / Registro</span>
    </div>
</div>

<div class="card card-body">
    {{ html()->form('POST', route('grupos.store'))->open() }}
    <!-- Sección: Información general -->
    <div class="px-3 rounded" style="border-left: 4px solid #007bff;">
        <small class="text-primary font-weight-bold">Información general</small>
        <div class="row my-1">
            <div class="form-group col-md-2 mb-1">
                {{ html()->label('IMPARTICIÓN', 'imparticion')->class('form-label mb-1') }}
                {{ html()->select('imparticion', ['' => 'SELECCIONAR', 1 => 'PRESENCIAL', 2 =>'A DISTANCIA'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-2 mb-1">
                {{ html()->label('CURSO/CERTIFICACIÓN', 'tipo')->class('form-label mb-1') }}
                {{ html()->select('tipo', ['' => 'SELECCIONAR', 1 => 'CURSO', 2 => 'CERTIFICACIÓN'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-8 mb-1">
                {{ html()->label('CURSO', 'curso')->class('form-label mb-1') }}
                {{ html()->select('curso', ['' => 'SELECCIONAR', 1 => 'CURSO 1', 2 => 'CURSO 2'])->class('form-control form-control-sm')->required() }}
            </div>
        </div>
    </div>

    <!-- Sección: Ubicación -->
    <div class="px-3 rounded" style="border-left: 4px solid #28a745;">
        <small class="text-success font-weight-bold">Ubicación</small>
        <div class="row my-1">
            <div class="form-group col-md-3 mb-1">
                {{ html()->label('UNIDAD/ACCIÓN MÓVIL', 'unidad_accion_movil')->class('form-label mb-1') }}
                {{ html()->select('unidad_accion_movil', ['' => 'SELECCIONAR', 1 => 'UNIDAD 1', 2 => 'UNIDAD 2'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3 mb-1">
                {{ html()->label('MUNICIPIO', 'municipio')->class('form-label mb-1') }}
                {{ html()->select('municipio', ['' => 'SELECCIONAR', 1 => 'MUNICIPIO 1', 2 => 'MUNICIPIO 2'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3 mb-1">
                {{ html()->label('LOCALIDAD', 'localidad')->class('form-label mb-1') }}
                {{ html()->select('localidad', ['' => 'SELECCIONAR', 1 => 'LOCALIDAD 1', 2 => 'LOCALIDAD 2'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3 mb-1">
                {{ html()->label('MODALIDAD', 'modalidad')->class('form-label mb-1') }}
                {{ html()->select('modalidad', ['' => 'SELECCIONAR', 1 => 'EXTENCIÓN', 2 => 'CAE'])->class('form-control form-control-sm')->required() }}
            </div>
        </div>

        <div class="row my-1">
            <div class="form-group col-md-6 mb-1">
                {{ html()->label('NOMBRE DEL LUGAR O ESPACIO FÍSICO', 'nombre_lugar')->class('form-label mb-1') }}
                {{ html()->text('nombre_lugar')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-6 mb-1">
                {{ html()->label('CALLE Y NÚMERO', 'calle_numero')->class('form-label mb-1') }}
                {{ html()->text('calle_numero')->class('form-control form-control-sm')->required() }}
            </div>
        </div>
        <div class="row my-1">
            <div class="form-group col-md-6 mb-1">
                {{ html()->label('COLONIA O BARRIO', 'colonia')->class('form-label mb-1') }}
                {{ html()->text('colonia')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-6 mb-1">
                {{ html()->label('CÓDIGO POSTAL', 'codigo_postal')->class('form-label mb-1') }}
                {{ html()->text('codigo_postal')->class('form-control form-control-sm') }}
            </div>
        </div>
        <div class="form-group mb-1">
            {{ html()->label('REFERENCIAS ADICIONALES', 'referencias')->class('form-label mb-1') }}
            {{ html()->textarea('referencias')->class('form-control form-control-sm')->rows(2) }}
        </div>
    </div>

    <!-- Sección: Fechas y horarios -->
    <div class="p-2 mb-2 rounded" style="border-left: 4px solid #17a2b8;">
        <small class="text-info font-weight-bold">Fechas y horarios</small>
        <div class="form-row mt-2">
            <div class="form-group col-md-3">
                {{ html()->label('FECHA INICIO', 'fecha_inicio')->class('form-label mb-1') }}
                {{ html()->date('fecha_inicio')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3">
                {{ html()->label('FECHA FIN', 'fecha_fin')->class('form-label mb-1') }}
                {{ html()->date('fecha_fin')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3">
                {{ html()->label('HORA INICIO', 'hora_inicio')->class('form-label mb-1') }}
                {{ html()->time('hora_inicio')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3">
                {{ html()->label('HORA FIN', 'hora_fin')->class('form-label mb-1') }}
                {{ html()->time('hora_fin')->class('form-control form-control-sm')->required() }}
            </div>
        </div>
    </div>

    <!-- Sección: Representante y organización -->
    <div class="p-2 mb-2 rounded" style="border-left: 4px solid #ffc107;">
        <small class="text-warning font-weight-bold">Representante y organización</small>
        <div class="row mt-2">
            <div class="form-group col-md-4">
                {{ html()->label('ORGANIZO PUBLICO', 'organizo_publico')->class('form-label') }}
                {{ html()->select('organizo_publico', ['' => 'SELECCIONAR', 1 => 'ORGANIZO 1', 2 => 'ORGANIZO 2'])->class('form-control')->required() }}
            </div>
            <div class="form-group col-md-4">
                {{ html()->label('NOMBRE DEL REPRESENTANTE', 'nombre_representante')->class('form-label') }}
                {{ html()->text('nombre_representante')->class('form-control')->required() }}
            </div>
            <div class="form-group col-md-4">
                {{ html()->label('TELÉFONO DEL REPRESENTANTE', 'telefono_representante')->class('form-label') }}
                {{ html()->text('telefono_representante')->class('form-control')->required() }}
            </div>
        </div>
    </div>

    <!-- Sección: Opciones adicionales -->
    <div class="p-2 mb-2 rounded" style="border-left: 4px solid #6c757d;">
        <small class="text-secondary font-weight-bold">Opciones adicionales</small>
        <div class="form-group mt-2">
            <div class="d-flex align-items-center mb-2">
                {{ html()->checkbox('grupo_vulnerable', false, 'true')->class('form-check-input me-2') }}
                {{ html()->label('GRUPO VULNERABLE', 'grupo_vulnerable')->class('form-check-label me-3 mb-0') }}
                {{ html()->text('grupo_vulnerable')->class('form-control ms-2')->required()->disabled() }}
            </div>
        </div>
        <div class="form-group">
            {{ html()->label('MEDIO VIRTUAL', 'medio_virtual')->class('form-label') }}
            {{ html()->select('medio_virtual', ['' => 'SELECCIONAR', 1 => 'VIRTUAL 1', 2 => 'VIRTUAL 2'])->class('form-control')->required()->disabled() }}
        </div>
        <div class="form-group">
            {{ html()->label('ENLACE VIRTUAL', 'enlace_virtual')->class('form-label') }}
            {{ html()->text('enlace_virtual')->class('form-control')->required()->disabled() }}
        </div>
        <div class="form-group">
            {{ html()->label('CONVENIO ESPECIFICO', 'convenio_especifico')->class('form-label') }}
            {{ html()->text('convenio_especifico')->class('form-control')->required()->disabled() }}
        </div>
        <div class="form-group">
            {{ html()->label('FECHA DE CONVENIO ESPECIFICO', 'fecha_convenio')->class('form-label') }}
            {{ html()->text('fecha_convenio')->class('form-control')->required()->disabled() }}
        </div>
        <div class="form-group">
            <div class="d-flex align-items-center mb-2">
                {{ html()->checkbox('cerss', false, 'true')->class('form-check-input me-2') }}
                {{ html()->label('CERSS', 'cerss')->class('form-check-label me-3 mb-0') }}
                {{ html()->select('cerss', ['' => 'SELECCIONAR', 1 => 'CERS 1', 2 => 'CERS 2'])->class('form-control ms-2')->required()->disabled() }}
            </div>
        </div>
    </div>
    {{ html()->form()->close() }}
</div>

@endsection

@push('script_sign')
@endpush