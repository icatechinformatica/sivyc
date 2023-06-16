<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Planeación Pat | SIVyC Icatech')
    <!--seccion-->

@section('content')
    <style>
        .boton {
            font-size: 1.2em;
            background-color: #880e4f !important;
        }

        * {
            box-sizing: border-box;
        }
        .card-header{
                font-variant: small-caps;
                background-color: #621132;
                color: white;
                margin: 1.7% 1.7% 1% 1.7%;
                padding: 1.3% 39px 1.3% 39px;
                font-style: normal;
                font-size: 22px;
            }

            .card-body{
                margin: 1%;
                margin-left: 1.7%;
                margin-right: 1.7%;
                /* padding: 55px; */
                -webkit-box-shadow: 0 8px 6px -6px #999;
                -moz-box-shadow: 0 8px 6px -6px #999;
                box-shadow: 0 8px 6px -6px #999;
            }
            .card-body.card-msg{
                background-color: yellow;
                margin: .5% 1.7% .5% 1.7%;
                padding: .5% 5px .5% 25px;
            }

            body { background-color: #E6E6E6; }

            /* .btn, .btn:focus{ color: white; background: #12322b; font-size: 14px; border-color: #12322b; margin: 0 5px 0 5px; padding: 10px 13px 10px 13px; }
            .btn:hover { color: white; background:#2a4c44; border-color: #12322b; }

            .form-control { height: 40px; } */

    </style>

    <div class="container-fluid px-5 g-pt-30">

        <div class="card-header py-2">
            <h2>Validación de Metas y Avances </h2>
        </div>
        {{-- <div class="row mb-5">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h1>Planeación</h1>
                </div>

            </div>
        </div> --}}
        <div class="card card-body" style=" min-height:450px;">
            <div class="form-row d-flex justify-content-center mt-4">
                <div class="row mt-4">
                    <div>
                        <a href="{{ route('pat.metavance.mostrar') }}" id="btnIngresoNormal" class="text-white btn boton">REGISTRO DE METAS/AVANCES</a>
                    </div>

                    <div>
                        <button type="button" name="btnValidador" id="btnValidador" class="btn text-white boton">VALIDAR METAS/AVANCES</button>
                    </div>
                </div>
            </div>

            <div class="form-row d-flex justify-content-center my-4">
                <div class="row">
                    <form action="" class="d-none" method="post" id="formIngresarValid">
                        @csrf
                        <select name="selorganismos" id="selorganismos" class="form-control">
                            <option value="">SELECCIONE EL ORGANISMO A VALIDAR</option>
                            @foreach ($organismos as $item)
                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                            @endforeach
                        </select>
                        <div class="d-flex justify-content-center">
                            <button type="button" name="btnIngresaValid" id="btnIngresaValid" class="mt-4 btn btn-outline-primary">Ingresar</button>
                            <button type="button" name="btnIngresaValidCancel" id="btnIngresaValidCancel" class="mt-4 btn btn-outline-danger">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



    </div>


        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){

                $("#btnIngresaValid" ).click(function(){
                    $('#formIngresarValid').attr('action', "{{ route('pat.metavance.envioplane') }}");
                    $("#formIngresarValid").attr("target", '_self');
                    $('#formIngresarValid').submit();
                });
            });


            $("#btnValidador" ).click(function(){
                $('#formIngresarValid').removeClass('d-none');

            });

            $("#btnIngresaValidCancel").click(function(){
                $('#formIngresarValid').addClass('d-none');
            });

        </script>
        @endsection
@endsection
