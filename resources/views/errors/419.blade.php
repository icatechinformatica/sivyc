@extends('errors.errors_layout')
<!--llamar a la plantilla -->
@section('title', 'Error 419 Pagina Expirada/Error Token CSRF  | Sivyc Icatech')
<!--seccion-->
@section('content')

<div class="error-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="error-card p-5">
                    <div class="decorative-elements">
                        <div class="circle-1"></div>
                        <div class="circle-2"></div>
                    </div>

                    <div class="text-center">
                        <div class="error-number">419</div>
                        <div class="accent-line"></div>
                        <div class="error-icon"></div>

                        <h1 class="error-title h2">
                            ¡Página expirada!
                        </h1>

                        <p class="error-description">
                            Lo sentimos, la página que está intentando acceder ha expirado.
                            Por favor, vuelva a cargar la página y pruebe nuevamente.
                        </p>

                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ url('/') }}" class="btn btn-custom">
                                <i class="fas fa-home me-2"></i>Ir al Inicio
                            </a>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary justify-content-center align-items-center d-flex">
                                <i class="fas fa-arrow-left me-2"></i>Regresar
                            </a>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Si necesita ayuda, contacte al administrador del sistema
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection