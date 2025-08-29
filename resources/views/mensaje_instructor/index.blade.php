@extends('theme.sivycCredencial.layout') {{-- Usa tu layout base de Laravel --}}

@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero-section {
            height: 100vh;
            width: 100%;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 152, 133, 0.85), rgba(0, 120, 105, 0.95));
            z-index: 2;
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/convocatoriabg.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 20px;
            max-width: 800px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 15px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .btn-accept {
            background-color: #fff;
            color: rgb(0, 152, 133);
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            margin-top: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-accept:hover {
            background-color: #f8f9fa;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            color: rgb(0, 130, 115);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .hero-content h1 {
                font-size: 2.8rem;
            }

            .hero-content p {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }

            .hero-content p {
                font-size: 1.1rem;
            }

            .btn-accept {
                padding: 12px 30px;
                font-size: 1.1rem;
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 1.8rem;
            }

            .hero-content p {
                font-size: 1rem;
            }

            .btn-accept {
                padding: 10px 25px;
                font-size: 1rem;
            }
        }
    </style>
@endsection

@section('title', 'Perfil del Empleado | SIVyC Icatech')

@section('content')
    <div class="container-fluid p-0">
        <!-- Hero -->
        <section class="hero-section">
            <div class="background-image"></div>
            <div class="background-overlay"></div>

            <div class="hero-content">
                {{-- Si hay error, mostrar mensaje de error --}}
                @if ($error)
                    <h1>⚠️ Ocurrió un problema</h1>
                    <p>{{ $error }}</p>
                    <p><strong>Para obtener más información, favor de comunicarse a Dirección Técnica Académica para más información sobre su asignación al curso. Gracias por su paciencia.</strong></p>
                @else
                    {{-- Si el usuario existe y el token es válido --}}
                    <h1>Hola, <strong>{{ $usuario->name ?? 'Instructor' }}</strong></h1>
                    <p>{{ $usuario->puesto ?? 'Instructor Autorizado' }}</p>
                    <p>Asignado al curso:
                        <strong>{{ $usuario->curso ?? 'Curso no especificado' }}</strong>
                    </p>
                    <p><strong>¿Deseas aceptar la asignación del curso a instruir?</strong></p>

                    <form action="{{ route('instructor.aceptar.confirmacion') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-accept">Aceptar</button>
                        <input type="hidden" name="curso_id" id="curso_id" value="{{ $cursos->id }}">
                    </form>
                @endif
            </div>
        </section>
    </div>
@endsection
