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
                @if (!empty($alumnoPre[0]->chk_acta_nacimiento))
                    <div class="form-group col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">ACTA DE NACIMIENTO</h5>
                                <a href="#" class="card-link">DESCARGAR DOCUMENTO</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!empty($alumnoPre[0]->chk_curp))
                    <div class="form-group col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">DOCUMENTO CURP</h5>
                                <a href="#" class="card-link">DESCARGAR DOCUMENTO</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!empty($alumnoPre[0]->chk_comprobante_domicilio))
                    <div class="form-group col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">COMPROBANTE DOMICILIO</h5>
                                <a href="#" class="card-link">DESCARGAR DOCUMENTO</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!empty($alumnoPre[0]->chk_ine))
                    <div class="form-group col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">INE</h5>
                                <a href="#" class="card-link">DESCARGAR DOCUMENTO</a>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            <div class="form-row">
                @if (!empty($alumnoPre[0]->chk_pasaporte_licencia))
                    <div class="form-group col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">PASAPORTE O LICENCIA</h5>
                                <a href="#" class="card-link">DESCARGAR DOCUMENTO</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!empty($alumnoPre[0]->chk_fotografia))
                    <div class="form-group col-md-3">
                        <div class="card">
                            <img class="img-fluid img-thumbnail" src="https://source.unsplash.com/pWkk7iiCoDM/400x300" alt="Card image cap">
                            <div class="card-footer">
                                <small class="text-muted">FOTOGRAFÍAS</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!--formulario datos generales-->
            <div class="form-row">
                @if (empty($alumnoPre[0]->chk_acta_nacimiento))
                    <!--está null-->
                    <div class="form-group col-md-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="acta_nacimiento" name="acta_nacimiento">
                            <label class="custom-file-label" for="customFile">ACTA DE NACIMIENTO</label>
                        </div>
                    </div>
                @endif

                @if (empty($alumnoPre[0]->chk_curp))
                    <div class="form-group col-md-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="copia_curp" name="copia_curp">
                            <label class="custom-file-label" for="copia_curp">COPIA DE LA CURP</label>
                        </div>
                    </div>
                @endif

                @if (empty($alumnoPre[0]->chk_comprobante_domicilio))
                    <div class="form-group col-md-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="comprobante_domicilio" name="comprobante_domicilio">
                            <label class="custom-file-label" for="comprobante_domicilio">COMPROBANTE DE DOMICILIO</label>
                        </div>
                    </div>
                @endif

                @if (empty($alumnoPre[0]->chk_fotografia))
                    <div class="form-group col-md-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="fotografias" name="fotografias">
                            <label class="custom-file-label" for="fotografias">FOTOGRAFÍAS</label>
                        </div>
                    </div>
                @endif
            </div>
            <div class="form-row">
                @if (empty($alumnoPre[0]->chk_ine))
                    <div class="form-group col-md-4">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="ine" name="ine">
                            <label class="custom-file-label" for="ine">CREDENCIAL DE ELECTOR</label>
                        </div>
                    </div>
                @endif

                @if (empty($alumnoPre[0]->chk_pasaporte_licencia))
                    <div class="form-group col-md-4">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="licencia_manejo" name="licencia_manejo">
                            <label class="custom-file-label" for="licencia_manejo">(PASAPORTE, LICENCIA DE MANEJO)</label>
                        </div>
                    </div>
                @endif

            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <h5><b>EXTRANJEROS ANEXAR:</b></h5>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="comprobante_migratorio" name="comprobante_migratorio">
                        <label class="form-check-label" for="comprobante_migratorio">
                            COMPROBANTE DE CALIDAD MIGRATORIA CON LA QUE SE ENCUENTRA EN EL TERRITORIO NACIONAL
                        </label>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="documento_comprobante_migratorio" name="documento_comprobante_migratorio" disabled>
                        <label class="custom-file-label" id="lbl_documento_comprobante_migratorio" for="documento_comprobante_migratorio">COMPROBANTE MIGRATORIO</label>
                    </div>
                </div>
            </div>

            <!--botones de enviar y retroceder-->
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="alumno_id" id="alumno_id" value="{{ $id_prealumno }}">
        </form>
        <br>
    </div>
@endsection
