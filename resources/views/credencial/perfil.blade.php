@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .card {
            width: 350px;
            background-color: #efefef;
            border: none;
            cursor: pointer;
            transition: all 0.5s;
        }

        .image img {
            transition: all 0.5s
        }

        .card:hover .image img {
            transform: scale(1.5)
        }

        .btn-profile {
            height: 120px;
            width: 120px;
            border-radius: 50%;
            background-color: #E6E6E6;
        }

        .name {
            font-size: 22px;
            font-weight: bold
        }

        .idd {
            font-size: 14px;
            font-weight: 600
        }

        .idd1 {
            font-size: 12px
        }

        .number {
            font-size: 22px;
            font-weight: bold
        }

        .follow {
            font-size: 12px;
            font-weight: 500;
            color: #444444
        }

        .btn1 {
            height: 40px;
            width: 150px;
            border: none;
            background-color: #000;
            color: #aeaeae;
            font-size: 15px
        }

        .text span {
            font-size: 13px;
            color: #545454;
            font-weight: 500
        }

        .icons i {
            font-size: 19px
        }

        hr .new1 {
            border: 1px solid
        }

        .join {
            font-size: 14px;
            color: #a0a0a0;
            font-weight: bold
        }

        .date {
            background-color: #ccc
        }
    </style>
@endsection
@section('title', 'Perfil del Empleado | SIVyC Icatech')
@section('content')
    <div class="container mt-4 mb-4 p-3 d-flex justify-content-center">
        <div class="card p-4">
            <div class=" image d-flex flex-column justify-content-center align-items-center">
                <button class="btn-profile">
                    <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" height="80" width="80" />
                </button>
                <span class="name mt-3">{{ $perfil->nombre_trabajador }}</span>
                <span class="idd">{{ ($perfil->status) ? "ACTIVO" : "INACTIVO" }}</span>
                <div class="d-flex flex-row justify-content-center align-items-center gap-2">
                    <span
                        class="idd1">ENLACE N°: <b>{{ $perfil->clave_empleado }}</b>
                    </span>
                </div>
                <div class="d-flex flex-row justify-content-center align-items-center mt-3">
                    <span  class="follow">{{ $perfil->categoria_estatal }}</span>
                </div>
                <div class=" d-flex mt-1">
                    <span  class="follow">{{ $perfil->puesto_estatal }}</span>
                </div>
                <div class=" d-flex mt-1">
                    <span  class="follow">COMISIONADO A:</span> &nbsp;
                    <span  class="follow">{{ $perfil->comision_direccion_o_unidad ?? "NO HAY COMISIÓN" }}</span>
                </div>
                <div class="gap-3 mt-3 icons d-flex flex-row justify-content-center align-items-center">
                    <span><i class="fa fa-twitter"></i></span> &nbsp;
                    <span><i class="fa fa-facebook-f"></i></span> &nbsp;
                    <span><i class="fa fa-instagram"></i></span>
                </div>
                <div class=" px-2 rounded mt-4 date "> <span class="join">{{ $perfil->fecha_ingreso }}</span> </div>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
@endsection
