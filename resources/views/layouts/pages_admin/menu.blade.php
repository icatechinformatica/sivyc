<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Menus | Sivyc Icatech')
@stack('styles')
<!--contenido-->
@section('content')
<div class="container-fluid mt--6">

    @include('permiso-rol-menu::menu_arbol')

    <!-- FOOTER PORTAL DE GOBIERNO -->
    @include("theme.sivyc_admin.footer")
    <!-- FOOTER PORTAL DE GOBIERNO END-->
</div>
@endsection

<script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('vendor/jquery-migrate/jquery-migrate.min.js')}}"></script>
<script src="{{asset('js/components/popper.min.js')}}"></script>
@stack('scripts')
