@extends("theme.sivyc.layout")
@section('title', 'FIRMAR ELECTRONICAMENTE | Sivyc Icatech')

@section('content_script_css')

    <link rel="stylesheet" type="text/css" href="https://www.firmaelectronica.chiapas.gob.mx/tools/plugins/bootstrap-4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://www.firmaelectronica.chiapas.gob.mx/tools/plugins/jasny-bootstrap4/css/jasny-bootstrap.min.css" />

@endsection

@section('content')
    <div class="d-none" id="vHTMLSignature"></div>    

    <div class="container-fluid py-5">
        <div class="row">
            <div class="col">
                @if ($message = Session::get('warning'))
                    <div class="alert alert-info">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                @if ($message = Session::get('danger'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">Mis Documentos</div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        {{-- encabezado --}}
                        <nav>
                            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Por Firmar</a>
                                <a class="nav-item nav-link" id="nav-firmados-tab" data-toggle="tab" href="#nav-firmados" role="tab" aria-controls="nav-firmados" aria-selected="false">Firmados</a>
                                <a class="nav-item nav-link" id="nav-validados-tab" data-toggle="tab" href="#nav-validados" role="tab" aria-controls="nav-validados" aria-selected="false">Validados</a>
                            </div>
                        </nav>

                        {{-- contenido --}}
                        <div class="tab-content py-3 px-sm-0" id="nav-tabContent">
                            {{-- Por Firmar --}}
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                @if ($docsFirmar != "[]")
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Nombre del documento</th>
                                                    <th scope="col">Ver documento</th>
                                                    <th scope="col">Firmantes</th>
                                                    <th scope="col">Creado</th>
                                                    <th scope="col">Firmar</th>                                           
                                                </tr>
                                            </thead>
    
                                            <tbody>
                                                @foreach ($docsFirmar as $key => $docFirmar)
                                                    @php
                                                        $firmantes = '';
                                                        $nameArchivo = '';
                                                        $obj = json_decode($docFirmar->obj_documento, true);
                                                        $nameArchivo = $obj['archivo']['_attributes']['nombre_archivo'];

                                                        foreach ($obj['firmantes']['firmante'][0] as $value) {
                                                            if($value['_attributes']['email_firmante'] == $email){
                                                                $curp = $value['_attributes']['curp_firmante'];
                                                            }

                                                            if(empty($value['_attributes']['firma_firmante'])){
                                                                $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (NO), ';
                                                            } else {
                                                                $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (SI), ';
                                                            }
                                                        }
                                                        $firmantes = substr($firmantes, 0, -2);
                                                    @endphp
                                                    <tr>
                                                        <td><small>{{$nameArchivo}}</small></td>
                                                        <td>
                                                            <a href="{{ $docFirmar->link_pdf }}" target="_blank" rel="{{ $docFirmar->link_pdf }}">
                                                                <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                            </a>
                                                        </td>
                                                        <td><small>{{$firmantes}}</small></td>
                                                        <td><small>{{$docFirmar->created_at->format('d-m-Y')}}</small></td>
                                                        <td>
                                                            <button class="btn btn-outline-primary" href="#" data-toggle="modal" data-target="#mdlLoadViewSignature" onclick="abriModal('{{$key}}')">firmar</button>
                                                        </td>
                                                        <input class="d-none" value="{{$docFirmar->id}}" name="idFile{{$key}}" id="idFile{{$key}}" type="text">
                                                        <input class="d-none" value="{{$docFirmar->cadena_original}}" name="cadena{{$key}}" id="cadena{{$key}}" type="text">
                                                        <input class="d-none" value="{{$docFirmar->base64xml}}" name="xml{{$key}}" id="xml{{$key}}" type="text">
                                                        <input class="d-none" value="{{$curp}}" name="curp{{$key}}" id="curp{{$key}}" type="text">
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="row mt-5">
                                        <div class="col d-flex justify-content-center">
                                            <strong>Sin documentos por firmar</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Firmados --}}
                            <div class="tab-pane fade" id="nav-firmados" role="tabpanel" aria-labelledby="nav-home-tab">
                                @if ($docsFirmados != "[]")
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Nombre del documento</th>
                                                    <th scope="col">Ver documento</th>
                                                    <th scope="col">Firmantes</th>
                                                    <th scope="col">Creado</th>
                                                    <th scope="col">Validar</th>                                        
                                                </tr>
                                            </thead>
    
                                            <tbody>
                                                @foreach ($docsFirmados as $docFirmado)
                                                    @php
                                                        $sendValidation = true;
                                                        $firmantes = '';
                                                        $nameArchivo = '';
                                                        $obj = json_decode($docFirmado->obj_documento, true);
                                                        $obj2 = json_decode($docFirmado->obj_documento_interno, true);
                                                        $nameArchivo = $obj['archivo']['_attributes']['nombre_archivo'];

                                                        foreach ($obj['firmantes']['firmante'][0] as $value) {
                                                            if(empty($value['_attributes']['firma_firmante'])){
                                                                $sendValidation = false;
                                                                $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (NO), ';
                                                            } else {
                                                                $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].' (SI), ';
                                                            }
                                                        }
                                                        $firmantes = substr($firmantes, 0, -2);
                                                    @endphp

                                                    <tr>
                                                        <td><small>{{$nameArchivo}}</small></td>
                                                        <td>
                                                            <a href="{{ $docFirmado->link_pdf }}" target="_blank" rel="{{ $docFirmado->link_pdf }}">
                                                                <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                            </a>
                                                        </td>
                                                        <td><small>{{$firmantes}}</small></td>
                                                        <td><small>{{$docFirmado->created_at->format('d-m-Y')}}</small></td>
                                                        <td>
                                                            @if ($obj2['emisor']['_attributes']['email'] == $email)
                                                                @if ($sendValidation)
                                                                    <button type="button" onclick="validardocumento('{{$docFirmado->id}}')" class="btn btn-outline-primary">Validar</button>
                                                                @else
                                                                    Faltan Firmas
                                                                @endif
                                                            @else
                                                                No disponible
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="row mt-3">
                                        <div class="col d-flex justify-content-center">
                                            <strong>Sin Documentos Firmados</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Validados --}}
                            <div class="tab-pane fade" id="nav-validados" role="tabpanel" aria-labelledby="nav-home-tab">
                                @if ($docsValidados != "[]")
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Nombre del documento</th>
                                                    <th scope="col">Firmantes</th>
                                                    <th scope="col">Creado</th>
                                                    <th scope="col">Validado</th>
                                                    <th scope="col">Descargar</th>                                        
                                                </tr>
                                            </thead>
    
                                            <tbody>
                                                @foreach ($docsValidados as $docValidado)
                                                    @php
                                                        $firmantes = '';
                                                        $nameArchivo = '';
                                                        $obj = json_decode($docValidado->obj_documento, true);
                                                        $nameArchivo = $obj['archivo']['_attributes']['nombre_archivo'];

                                                        foreach ($obj['firmantes']['firmante'][0] as $value) {
                                                            $firmantes = $firmantes.$value['_attributes']['nombre_firmante'].', ';
                                                        }
                                                        $firmantes = substr($firmantes, 0, -2);
                                                    @endphp
                                                    <tr>
                                                        <td><small>{{$nameArchivo}}</small></td>
                                                        <td><small>{{$firmantes}}</small></td>
                                                        <td><small>{{$docValidado->created_at->format('d-m-Y')}}</small></td>
                                                        <td><small>{{$docValidado->fecha_sellado}}</small></td>
                                                        <td>
                                                            <button type="button" onclick="descargarDocumento('{{$docValidado->id}}')" class="btn btn-outline-success">Descargar</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="row mt-3">
                                        <div class="col d-flex justify-content-center">
                                            <strong>Sin Documentos Validados</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="formUpdate" action="{{route('firma.update')}}" method="post">
            @csrf
            <input class="d-none" id="fechaFirmado" name="fechaFirmado" type="text">
            <input class="d-none" id="serieFirmante" name="serieFirmante" type="text">
            <input class="d-none" id="firma" name="firma" type="text">
            <input class="d-none" id="curp" name="curp" type="text">
            <input class="d-none" id="idFile" name="idFile" type="text">
        </form>

        <form id="formSellar" action="{{route('firma.sellar')}}" method="post">
            @csrf
            <input class="d-none" id="txtIdFirmado" name="txtIdFirmado" type="text">
        </form>

        <form id="formGenerarPDF" action="{{route('firma.generarPdf')}}" method="post">
            @csrf
            <input class="d-none" id="txtIdGenerar" name="txtIdGenerar" type="text">
        </form>
    </div>

@endsection


@section('script_content_js')
    
    {{-- js bootstrap --}}
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/plugins/jquery-3.4.1/jquery-3.4.1.min.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/plugins/bootstrap-4.3.1/js/bootstrap.min.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/plugins/jasny-bootstrap4/js/jasny-bootstrap.min.js"></script>

    {{-- js para poder firmar --}}
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/sjcl.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/sha1_002.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/llave.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/jsbn.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/jsbn2.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/rsa.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/rsa2.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/base64_002.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/crypto-1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/asn1hex-1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/rsasign-1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/x509-1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/pbkdf2.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/tripledes_002.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/aes.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/rc2.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/asn1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/base64.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/hex_002.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/yahoo-min.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/hex.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/base64x-1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/x64-core.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/tripledes.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/core.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/md5.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/sha1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/sha256.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/ripemd160.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/sha512.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/enc-base64.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/hmac.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/pbkdf2_002.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/cipher-core.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/asn1-1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/rsapem-1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-sat/keyutil-1.js"></script>

    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/forge-0.7.1/forge-0.7.1.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-scg/mistake.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-scg/validate.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-scg/access.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-scg/dataSign.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/utilities-scg/dataTransportSign.js"></script>
    <script src="https://www.firmaelectronica.chiapas.gob.mx/tools/library/signedjs-2.1/signature-spv015_doctos.js"></script>
    

    <script>
        var cadena = '', xmlBase64 = '', curp = '', idFile = '';
        $(document).ready(function() {
            $('#btnsignature').attr('onclick', 'firmar();');
        });

        function abriModal(key) {
            $('#vHTMLSignature').removeClass('d-none');
            cadena = $('#cadena'+ key).val();
            xmlBase64 = $('#xml'+ key).val();
            curp = $('#curp'+ key).val();
            idFile = $('#idFile' + key).val();
            console.log(xmlBase64);
        }

        function firmar() {
            // console.log(cadena);
            // console.log(xmlBase64);
            // console.log(curp);

            var vresponseSignature = sign(
                cadena,
                xmlBase64,
                curp,
                $('#password').val(),
                39,
                'dwLChYOVylB9htqD9qIaSVHddKzWKiqXqmh7fFRHwFJk2x'
            );
            console.log(vresponseSignature);

            if (vresponseSignature.statusResponse) {
                $('#fechaFirmado').val(vresponseSignature.date);
                $('#serieFirmante').val(vresponseSignature.certifiedSeries)
                $('#firma').val(vresponseSignature.sign);
                $('#curp').val(vresponseSignature.curp);
                $('#idFile').val(idFile);
                $('#formUpdate').submit(); 
            } else {
                $('#txtcode').val(vresponseSignature.codeResponse);
                $('#txtdescription').val(vresponseSignature.descriptionResponse);
                var result = confirm(vresponseSignature.messageResponse);
                if (result) {
                    location.reload();
                } else {
                    location.reload();
                }
            }
        }

        function validardocumento(id) {
            $('#txtIdFirmado').val(id);
            $('#formSellar').submit();
        }

        function descargarDocumento(id) {
            $('#txtIdGenerar').val(id);
            $('#formGenerarPDF').submit();
        }
    </script>

@endsection