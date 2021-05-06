@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Solicitud de Inscripción | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <form method="POST" id="paso2UploadFiles"  action="{{ route('alumnos.update.documentos.registro') }}" enctype="multipart/form-data">
            @csrf
            <div style="text-align: center;">
                <h3><b>DOCUMENTACIÓN ENTREGADA PARA {{ $alumnoPre->apellido_paterno }} {{ $alumnoPre->apellido_materno }} {{ $alumnoPre->nombre }}</b></h3>
            </div>
            <!--DOCUMENTACIÓN ENTREGADA-->
            <hr style="border-color:dimgray">

            <div class="form-row">
                <div class="form-group col-md-8">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" name="customFile">
                        <label class="custom-file-label" for="customFile">SELECCIONAR DOCUMENTO</label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="tipoDocumento" class="control-label">ELEGIR EL TIPO DE ARCHIVO</label>
                    <select class="form-control" id="tipoDocumento" name="tipoDocumento">
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
            <br>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <table  id="table-instructor" class="table table-bordered Datatables">
                        <caption>ARCHIVOS VINCULADOS</caption>
                        <thead>
                            <tr>
                                <th scope="col">DOCUMENTO</th>
                                <th scope="col">DESCARGAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($alumnoPre->chk_fotografia) && $alumnoPre->chk_fotografia == true)
                            <tr>
                                <td>FOTOGRAFÍA</td>
                                <td>
                                    <a href="{{ asset( $alumnoPre->fotografia )}}" class="btn btn-info btn-circle m-1 btn-circle-sm" download="{{ $alumnoPre->fotografia }}" data-toggle="tooltip" data-placement="top" title="FOTOGRAFÍA">
                                        <i class="fa fa-picture-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            @endif

                            @if (!empty($alumnoPre->chk_acta_nacimiento) && $alumnoPre->chk_acta_nacimiento == true)
                            <tr>
                                <td>
                                    ACTA DE NACIMIENTO
                                </td>
                                <td>
                                    <a href="{{ asset( $alumnoPre->acta_nacimiento )}}" download="acta_nacimiento_{{ $alumnoPre->curp }}.pdf" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="ACTA DE NACIMIENTO">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if (!empty($alumnoPre->chk_curp) && $alumnoPre->chk_curp == true)
                            <tr>
                                <td>DOCUMENTO CURP</td>
                                <td>
                                    <a href="{{ asset( $alumnoPre->documento_curp )}}" download="copia_curp_{{ $alumnoPre->curp }}.pdf" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="DOCUMENTO CURP">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if (!empty($alumnoPre->chk_comprobante_domicilio) && $alumnoPre->chk_comprobante_domicilio == true)
                            <tr>
                                <td>COMPROBANTE DE DOMICILIO</td>
                                <td>
                                    <a href="{{ asset( $alumnoPre->comprobante_domicilio )}}" download="comprobante_domicilio_{{ $alumnoPre->curp }}.pdf" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="COMPROBANTE DE DOMICILIO">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if (!empty($alumnoPre->chk_ine) && $alumnoPre->chk_ine == true)
                            <tr>
                                <td>CREDENCIAL DE ELECTOR</td>
                                <td>
                                    <a href="{{ asset( $alumnoPre->ine )}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" download="credencial_elector_{{ $alumnoPre->curp }}.pdf"  data-toggle="tooltip" data-placement="top" title="CREDENCIAL DE ELECTOR">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if (!empty($alumnoPre->chk_pasaporte_licencia) && $alumnoPre->chk_pasaporte_licencia == true)
                            <tr>
                                <td>(PASAPORTE, LICENCIA DE MANEJO)</td>
                                <td>
                                    <a href="{{ asset( $alumnoPre->pasaporte_licencia_manejo )}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" download="pasaporte_licencia_manejo_{{ $alumnoPre->curp }}.pdf" data-toggle="tooltip" data-placement="top" title="(PASAPORTE, LICENCIA DE MANEJO)">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if (!empty($alumnoPre->chk_comprobante_ultimo_grado) && $alumnoPre->chk_comprobante_ultimo_grado == true)
                            <tr>
                                <td>COMPROBANTE ÚLTIMO GRADO DE ESTUDIOS</td>
                                <td>
                                    <a href="{{ asset( $alumnoPre->comprobante_ultimo_grado )}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" download="comprobante_ultimo_grado_estudios_{{ $alumnoPre->curp }}.pdf"  data-toggle="tooltip" data-placement="top" title="COMPROBANTE ÚLTIMO GRADO DE ESTUDIOS">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            @endif

                            @if(!empty($alumnoPre->chk_comprobante_calidad_migratoria) && $alumnoPre->chk_comprobante_calidad_migratoria == true)
                            <tr>
                                <td>COMPROBANTE DE CALIDAD MIGRATORIA</td>
                                <td>
                                    <a href="{{ asset( $alumnoPre->comprobante_calidad_migratoria )}}" download="comprobante_calidad_migratoria_{{$alumnoPre->curp}}.pdf" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="COMPROBANTE DE CALIDAD MIGRATORIA">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
            <br>
            <!--botones de enviar y retroceder-->
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{ route('alumnos.index') }}">Regresar</a>
                    </div>
                    @can('alumno.cargar-documento')
                        <div class="pull-right">
                            <button id="submitDocs" type="submit" class="btn btn-primary" >Cargar Archivo</button>
                        </div>
                    @endcan

                </div>
            </div>
            <input type="hidden" name="alumno_id" id="alumno_id" value="{{ $id_prealumno }}">
        </form>
        <br>
    </div>
@endsection
@section('script_content_js')
    <script type="text/javascript">
        $(function(){
            // disabled button while submit
            $("#paso2UploadFiles").submit(function (e) {
                $("#submitDocs").attr("disabled", true);
                return true;
            });

            $.validator.addMethod('filesize', function (value, element, param) {
                return this.optional(element) || (element.files[0].size <= param)
            }, 'El TAMAÑO DEL ARCHIVO DEBE SER MENOR A 2 MB.');
            /**
            * validar formulario paso2UploadFiles
            */
            $("#paso2UploadFiles").validate({
                rules: {
                    customFile: {
                        extension: "pdf|png|jpg|jpeg",
                        filesize: 2000000,   //max size 2mb
                        required: true
                    },
                    tipoDocumento:{
                        required: true
                    }
                },
                messages: {
                    customFile: {
                        extension: "SÓLO SE PERMITEN PDF, PNG, JPG, JPEG",
                        required: "ANEXAR EL DOCUMENTO"
                    },
                    tipoDocumento: {
                        required: 'POR FAVOR, SELECCIONE EL TIPO DE DOCUMENTO',
                    }
                }
            });
        });
    </script>
@endsection
