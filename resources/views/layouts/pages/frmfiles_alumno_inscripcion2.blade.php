@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Solicitud de Inscripción | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <form method="POST" id="form-sid-paso2"  action="{{ route('alumnos.update.documentos.registro') }}" enctype="multipart/form-data">
            @csrf
            <div style="text-align: center;">
                <h3><b>DOCUMENTACIÓN ENTREGADA</b></h3>
            </div>
            <!--DOCUMENTACIÓN ENTREGADA-->
            <hr style="border-color:dimgray">

            <div class="form-row">
                <div class="form-group col-md-8">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" name="customFile">
                        <label class="custom-file-label" for="customFile">ACTA DE NACIMIENTO</label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="areaCursos" class="control-label">ELEGIR EL TIPO DE ARCHIVO</label>
                    <select class="form-control" id="areaCursos" name="areaCursos">
                        <option value="">--SELECCIONAR--</option>
                        <option value="acta_nacimiento">ACTA DE NACIMIENTO</option>
                        <option value="copia_curp">COPIA DE LA CURP</option>
                        <option value="comprobante_domicilio">COMPROBANTE DE DOMICILIO</option>
                        <option value="fotografia">FOTOGRAFÍA</option>
                        <option value="credencial_electoral">CREDENCIAL DE ELECTOR</option>
                        <option value="pasaporte_licencia_manejo">PASAPORTE, LICENCIA DE MANEJO</option>
                        <option value="ultimo_grado_estudios">ÚLTIMO GRADO DE ESTUDIOS</option>
                        <option value="comprobante_migratorio">COMPROBANTE MIGRATORIO</option>
                    </select>
                </div>
            </div>

            <!--botones de enviar y retroceder-->
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    @can('alumno.cargar-documento')
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary" >Guardar</button>
                        </div>
                    @endcan

                </div>
            </div>
            <input type="hidden" name="alumno_id" id="alumno_id" value="{{ $id_prealumno }}">
        </form>
        <br>
    </div>
@endsection
