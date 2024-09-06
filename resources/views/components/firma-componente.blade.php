<div>
    <div class="d-none" id="vHTMLSignature"></div>
    <a class="btn-sm btn-success" href="javascript:;"
        onclick="abrirModal('dsahlkdsajlkdsajlsdajlsdakjsdlakjsdalk')"><i class="fas fa-signature"></i> Firmar</a>
        {{-- <button type="button" style="color: #fff; background-color: #632327;" class="btn btn-sm" data-toggle="modal" data-target="#mdlLoadViewSignature" id="btnmodal" title="Firmar Electr&oacute;nicamente" ><i class="inverted pencil icon"></i> Firmar Electr&oacute;nicamente</button> --}}
        {{-- <button onclick="openModal()">Test</button> --}}
</div>

{{-- scripts del componente --}}

@push('script_sign')
    {{-- Todos estos links se ocupan en prueba y en producción --}}
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jquery-3.4.1/jquery-3.4.1.min.js"></script>
    <script
        src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/js/jasny-bootstrap.min.js">
    </script>
    {{-- <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/js/bootstrap.min.js"></script> --}}

    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sjcl.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha1_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/llave.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/jsbn.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/jsbn2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsa.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsa2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/crypto-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1hex-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsasign-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/x509-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/tripledes_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/aes.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rc2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hex_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/yahoo-min.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hex.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64x-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/x64-core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/tripledes.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/md5.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha256.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/ripemd160.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha512.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/enc-base64.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hmac.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/cipher-core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsapem-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/keyutil-1.js"></script>

    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/forge-0.7.1/forge-0.7.1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/mistake.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/validate.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/access.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/dataSign.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/dataTransportSign.js">
    </script>
    {{-- Para el envio por arreglo --}}
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/ChainTransport.js"></script>

    {{-- link de prueba signature-spv021_doctos-prueba --}}
    {{-- <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-2.1/signature-spv021_doctos-prueba.js"></script> --}}
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-generic-prueba/js/firmado_prueba.js"></script>

    <script type="text/javascript">
        var arrayCadenasG = [];
        var curpG = '';

        $(document).ready(function() {
            let folio = '';
            $('#btnsignature').attr('onclick', 'firmar();'); //boton firma modal

        });

        function abrirModal(array_cadena) {
            console.log('mostrar');
            // $('#vHTMLSignature').removeClass('d-none');
            openModal();

            arrayCadenasG = generarArray(array_cadena);
            curpG = $('#curpfir').val();
        }

        // Función para generar el array de cadenas
        function generarArray(array_cadena) {
            let chainTransports = [];
            Object.entries(array_cadena).forEach(([clave, valor], index) => {

                let nuevaInstancia = new ChainTransport();
                nuevaInstancia.Dataid_cadenaOriginal = clave;
                nuevaInstancia.DatacadenaOriginal = valor;
                chainTransports.push(nuevaInstancia);
            });
            return chainTransports;
        }
        // firmar documentos
        function firmarDocumento(token) {
            let vresponseSignature = sign(cadena, curp, $('#txtpassword').val(), '30', token);
            return vresponseSignature;
        }
        // generar token
        function generarToken() {
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    $.ajax({
                        method: 'POST',
                        url: "{{ route('firma.gettoken') }}",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        data: {
                            'nombre': '',
                            'key': '',
                            '_token': $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function(result) {
                            resolve(result);
                        },
                        error: function(jqXHR, textStatus) {
                            reject(`error ${textStatus}`);
                        }
                    });
                }, 2000); // dos segundos de espera
            });
        }

        // continuar proceso
        function continueProcess(response) {
            if (response.statusResponse) {
                $('#fechaFirmado').val(response.date);
                $('#serieFirmante').val(response.certifiedSeries)
                $('#firma').val(response.sign);
                $('#curp').val(curp);
                $('#certificado').val(response.certificated)
                $('#idFile').val(idFile);
                $('#formUpdate').submit();
            } else {
                confirm(response.messageResponse + ' ' + response.descriptionResponse)
                location.reload;
            }
        }
        // funcion firmar
        function firmarElectronica() {
            let response = firmarDocumento($('#token').val());
            if (response.codeResponse == '401') {
                generarToken().then((value) => {
                    response = firmarDocumento(value);
                    continueProcess(response);
                }).catch((error) => {
                    continueProcess(response);
                });
            } else {
                continueProcess(response);
            }
        }
    </script>
@endpush

{{-- estilos específicos del componente --}}
@push('content_css_sign')
    {{-- links de prueba y de produccion --}}
    {{-- <link rel="stylesheet" type="text/css"
        href="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/css/bootstrap.min.css" /> --}}
    <link rel="stylesheet" type="text/css"
        href="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/css/jasny-bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-generic-prueba/css/firma.css">
@endpush
