@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Inscripción Alumno | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12">
            <div class="card">
                <div class="card-header bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                    <h6 class="text-uppercase text-muted ls-1 mb-1">Performance</h6>
                    <h5 class="h3 mb-0">Total orders</h5>
                    </div>
                </div>
                </div>
                <div class="card-body">
                <!-- Chart -->
                <div class="chart">
                    <canvas id="chart-bars" class="chart-canvas"></canvas>
                </div>
                </div>
            </div>
            </div>
        </div>

        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include("theme.sivyc_admin.footer")
        <!-- FOOTER PORTAL DE GOBIERNO END-->
    </div>
@endsection
