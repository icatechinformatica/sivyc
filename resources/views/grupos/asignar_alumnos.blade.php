@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.css">
<link rel="stylesheet" href="{{ asset('css/grupos/agenda_fullcalendar.css') }}">
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-8">
        <span>Grupos / Asignar Alumnos</span>
    </div>
    <div class="d-flex align-items-center">
        <span class="font-weight-bold mr-3">Curso:</span>
        <span class="badge badge-secondary p-2 mr-4" style="font-size:1rem;">Adobe Photoshop</span>
        <span class="font-weight-bold mr-2">Folio Grupo:</span>
        <span class="badge badge-info p-2" style="font-size:1rem;">ICATECH-2025-001</span>
    </div>
</div>
<div class="row mb-3 px-5">
    <div class="d-flex">
        <a href="{{ route('grupos.crear') }}" class="btn-regresar">
            <i class="fa fa-arrow-left mr-2"></i> Regresar
        </a>
    </div>
</div>
<div class="card card-body mt-3">
    <div class="col-md-12 mb-3 d-flex justify-content-between align-items-center px-0">
        <div class="flex-grow-1">
            <form class="form-inline" method="POST" action="">
                @csrf
                <div class="input-group">
                    <input type="text" name="curp" class="form-control" placeholder="Ingrese CURP" maxlength="18" required>
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded" type="submit" name="action" value="agregar">Agregar</button>
                        <button class="btn btn-success rounded" type="submit" name="action" value="registrar">Registrar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="d-flex align-items-center p-0 m-0">
            <button class="btn btn-outline-info rounded" style="margin-right:0;">
                <i class="fa fa-share mr-2"></i> Exportar grupo (alumnos)
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Curp</th>
                    <th>Matrícula</th>
                    <th>Nombre</th>
                    <th>Sexo</th>
                    <th>Fec. Nac.</th>
                    <th>Edad</th>
                    <th>Escolaridad</th>
                    <th>Gpo. Vulnerable</th>
                    <th>Otro. Gpo.</th>
                    <th>Nac.</th>
                    <th>Tipo Inscrip.</th>
                    <th>Cuota</th>
                    <th>Eliminar</th>
                    <th>SID</th>
                    <th>CURP</th>
                </tr>
            </thead>
            <tbody>
                {{-- Si no hay alumnos inscritos --}}
                <tr>
                    <td colspan="15" class="text-center">No hay alumnos inscritos aún</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mt-4 gap-2">
        <button class="btn border border-danger text-danger bg-white" style="margin-right: 10px;">
            <i class="fa fa-file-pdf mr-2" style="color: #d32f2f;"></i> ACTA DE ACUERDO
        </button>
        <button class="btn border border-danger text-danger bg-white" style="margin-right: 10px;">
            <i class="fa fa-file-pdf mr-2" style="color: #d32f2f;"></i> SOLICITUD APERTURA
        </button>
        <button class="btn border border-danger text-danger bg-white" style="margin-right: 10px;">
            <i class="fa fa-file-pdf mr-2" style="color: #d32f2f;"></i> LISTA ALUMNOS
        </button>
    </div>
</div>
@endsection