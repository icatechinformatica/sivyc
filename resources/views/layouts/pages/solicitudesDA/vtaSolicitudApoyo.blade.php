@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Solicitud de apoyo | SIVyC Icatech')

@section('content')
    
    <div class="container mt-4">
        {{-- titulo --}}
        <div class="row pb-2">
            <div class="col text-center">
                <h2>SOLICITUD DE APOYO</h2>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif    
    </div>

@endsection