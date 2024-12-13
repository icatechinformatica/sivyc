<div>
    <div class="d-none" id="vHTMLSignature"></div>
    <input class="d-none" id="token" name="token" type="text" value="{{ $tokenData }}">
    <a class="btn btn-success" href="javascript:;" onclick="abrirModal()"><i class="fas fa-signature"></i> Firmar</a>

    <input class="d-none" value="{{ $indice }}" name="idFile" id="idFile" type="text">
    <input class="d-none" value="{{ $cadenaOriginal }}" name="cadena" id="cadena" type="text">
    <input class="d-none" value="{{ $baseXml }}" name="xml" id="xml" type="text">
    {{-- <input class="d-none" value="{{ $curpFirmante }}" name="curp" id="curp" type="text"> --}}
    <input class="d-none" value="{{ $curpFirmante }}" name="curp" id="curp" type="text">
    <input class="d-none" value="{{ $id }}" name="idRf001" id="idRf001" type="text">
    <input class="d-none" value="{{ $duplicado }}" name="duplicados" id="duplicados" type="text">

    <form id="formSign" action="{{ route('firma.store.update') }}" method="post">
        @csrf
        <input class="d-none" id="fechaFirmado" name="fechaFirmado" type="text">
        <input class="d-none" id="serieFirmante" name="serieFirmante" type="text">
        <input class="d-none" id="firma" name="firma" type="text">
        <input class="d-none" id="curpObtenido" name="curpObtenido" type="text">
        <input class="d-none" id="getIdFile" name="getIdFile" type="text">
        <input class="d-none" id="certificado" name="certificado" type="text">
        <input class="d-none" id="idRf" name="idRf" type="text">
        <input class="d-none" id="duplicidad" name="duplicidad" type="text">
    </form>
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
    {{-- <script
        src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-generic-prueba/js/firmado_prueba.js">
    </script> --}}

    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-generic/js/firmado_produccion.js">
    </script>

    <script type="text/javascript">
        var arrayCadenasG = [];
        var curpG = '';
        let cadena = '',
            idfile = '',
            curp = '',
            xmlBase64 = '',
            idRf001 = '',
            duplicidad = false;
        let res = '';

        $(document).ready(function() {
            HTMLSignature();
            initElements();
            let folio = '';
            $('#btnsignature').attr('onclick', 'firmarElectronica();'); //boton firma modal

        });

        function abrirModal() {
            // $('#vHTMLSignature').removeClass('d-none');
            openModal();

            cadena = $('#cadena').val();
            curp = $('#curp').val();
            idfile = $('#idFile').val();
            idRf001 = $('#idRf001').val();
            duplicidad = $('#duplicados').val();
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
                $('#curpObtenido').val(curp);
                $('#certificado').val(response.certificated)
                $('#getIdFile').val(idfile);
                $('#idRf').val(idRf001);
                $('#duplicidad').val(duplicidad);
                $('#formSign').submit();
            } else {
                confirm(response.messageResponse + ' ' + response.descriptionResponse)
                location.reload;
            }
        }
        // funcion firmar
        function firmarElectronica() {
            firmarDocumento($('#token').val()).then(signature => {
                if (signature.codeResponse == '114') {
                    generarToken().then((value) => {
                        firmarDocumento(value).then(response => {
                            continueProcess(response);
                        }).catch(err => {
                            continueProcess(response);
                        });

                    }).catch((error) => {
                        continueProcess(response);
                    });
                } else if (signature.codeResponse !== '00') {
                    console.log(signature)
                } else {
                    continueProcess(signature);
                }
            }).catch(error => {
                console.error("Error:", error);
            });
        }

        // firmar documentos
        async function firmarDocumento(token) {
            try {
                const cadena = document.getElementById('cadena').value;
                const curp = document.getElementById('curp').value;
                const password = document.getElementById('txtpassword').value;
                const token = document.getElementById('token').value;
                const version = "87";
                // const version = "30";
                return await sign(cadena, curp, password, version, token);
            } catch (error) {
                console.error("Error en sign:", error);
                // Puedes retornar un valor predeterminado o propagar el error si es necesario
                throw error; // o return null; dependiendo de tus necesidades
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
    <link rel="stylesheet" type="text/css"
        href="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-generic/css/firma.css">
@endpush
