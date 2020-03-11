<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Generacion de Documentos | Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
            @csrf
            <div class="text-center">
                <h1>Generacion de Documentos<h1>
            </div>
            <hr style="border-color:dimgray">
            <div class="text-center">
                <a class="btn btn-danger btn-lg" href="">Solicitud de Suficiencia presupuestal</a>
            </div>
            <br>
            <br>
            <div class="text-center">
                <a class="btn btn-danger btn-lg" href="">Validacion de Suficiencia presupuestal</a>
            </div>
            <br>
            <br>
            <div class="text-center">
                <a class="btn btn-danger btn-lg" href="">Contrato Por Curso Validado</a>
            </div>
    </section>
@stop

