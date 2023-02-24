@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'MOSTRAR ASPIRANTE CERSS | Sivyc Icatech')
<!--ÁREA EXTRA DONDE SE AGREGA CSS-->
@section('content_script_css')
    <style>
        .constancia_reclusion_tag{
            display: none;
        }
    </style>
@endsection
<!--ÁREA EXTRA DONDE SE AGREGA CSS ENDS-->
<!--contenido-->
@section('content')
    <div class="container g-pt-50">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div>
                    <h3><b>MOSTRAR DETALLES DE LA SOLICITUD DE INSCRIPCIÓN (SID) - CERSS</b></h3>
                </div>
            </div>

        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS GENERALES CERSS</b></h4>
        </div>
            <div class="form-row">
                <!--NOMBRE CERSS-->
                <div class="form-group col-md-4">
                    <label for="nombre_cerss " class="control-label">NOMBRE DEL CERSS</label>: &nbsp;&nbsp;
                    <b>{{$alumnoPre_show->nombre_cerss}}</b>
                </div>
                <!--NOMBRE CERSS END-->
                <div class="form-group col-md-8">
                    <label for="direcciones_cerss " class="control-label">DIRECCIÓN DEL CERSS</label><br>
                    <b>{{$alumnoPre_show->direccion_cerss}}</b>
                </div>
            </div>
            <div class="form-row">
                <!--TITULAR DEL CERSS-->
                <div class="form-group col-md-8">
                    <label for="titular_cerss " class="control-label">TITULAR DEL CERSS</label>: &nbsp;&nbsp;
                    <b>{{$alumnoPre_show->titular_cerss}}</b>
                </div>
                <!--TITULAR DEL CERSS END-->
            </div>

            <!--PERSONALES-->
            <hr style="border-color:dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS PERSONALES CERSS</b></h4>
            </div>
            <!--PERSONALES END-->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="numero_expediente_cerss" class="control-label">NÚMERO DE EXPEDIENTE</label>: &nbsp;&nbsp;
                    <b>{{$alumnoPre_show->numero_expediente}}</b>
                </div>
                <!--nombre aspirante-->
                <div class="form-group col-md-6">
                    <label for="nombre_aspirante_cerss " class="control-label">NOMBRE COMPLETO</label>: &nbsp;&nbsp;
                    <b>{{$alumnoPre_show->nombre}} {{$alumnoPre_show->apellido_paterno}} {{$alumnoPre_show->apellido_materno}}</b>
                </div>
                <!--nombre aspirante END-->
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="fechanacimiento" class="control-label">FECHA DE NACIMIENTO</label>: &nbsp; &nbsp; <b>{{$alumnoPre_show->fecha_nacimiento}}</b>
                </div>
                <div class="form-group col-md-6">
                    <label for="nacionalidad_cerss" class="control-label">NACIONALIDAD</label>: &nbsp;&nbsp; <b>{{$alumnoPre_show->nacionalidad}}</b>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="fechanacimiento" class="control-label">ESTADO</label>: &nbsp; &nbsp; <b>{{$alumnoPre_show->estado}}</b>
                </div>
                <div class="form-group col-md-6">
                    <label for="nacionalidad_cerss" class="control-label">MUNICIPIO</label>: &nbsp;&nbsp; <b>{{$alumnoPre_show->municipio}}</b>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="genero_cerss" class="control-label">GENERO</label>: &nbsp;&nbsp; <b>{{$alumnoPre_show->sexo}}</b>
                </div>
                <div class="form-group col-md-4">
                    <label for="curp_cerss" class="control-label">CURP ASPIRANTE</label>: &nbsp; &nbsp; <b>{{$alumnoPre_show->curp}}</b>
                </div>
                <div class="form-group col-md-4">
                    <label for="curp_cerss" class="control-label">DISCAPACIDAD</label>: &nbsp; &nbsp; <b>{{$alumnoPre_show->discapacidad}}</b>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="rfc_cerss" class="control-label">RFC ASPIRANTE</label>: &nbsp; &nbsp; <b>{{$alumnoPre_show->rfc_cerss}}</b>
                </div>
                <div class="form-group col-md-6">
                    <label for="ultimo_grado_estudios_cerss" class="control-label">ÚLTIMO GRADO DE ESTUDIOS</label>: &nbsp; &nbsp; <b>{{$alumnoPre_show->ultimo_grado_estudios}}</b>
                </div>
            </div>
            <!---->
            <div class="form-row">

                <div class="form-group col-md-6">
                    <div class="custom-file">
                        <label for="file_upload " class="control-label">FICHA IDENTIFICACIÓN CERSS</label>: &nbsp;&nbsp;
                        @if ($alumnoPre_show->chk_ficha_cerss)
                            <a href="{{ asset( $alumnoPre_show->ficha_cerss )}}" download="ficha_identificacion_cerss_{{ $id_prealumno }}.pdf" class="btn btn-danger btn-circle m-1 btn-circle-sm" target="_blank" data-toggle="tooltip" data-placement="top" title="FICHA IDENTIFICACIÓN CERSS">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <!--botones de enviar y retroceder-->
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{route('alumnos.index')}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-warning" href="{{route('alumnos.cerss.update', ['id' => base64_encode($id_prealumno)])}}">Modificar</a>
                    </div>
                </div>
            </div>
            <input type="hidden" name="is_cerrs" id="is_cerrs" value="true">

    </div>
@endsection
