@extends('theme.sivyc.layout') {{--AGC--}}
@section('title', 'Paqueterias Didacticas | SIVyC Icatech')
@section('css_content')
<link rel="stylesheet" href="{{ asset('css/paqueterias/paqueterias.css') }}" />
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />

@endsection
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />

<div class="card-header">
    Formulario de Paqueterias Didacticas
</div>
<div class="card card-body" style=" min-height:450px;">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div><br />
    @endif
    <form method="POST" action="{{route('paqueteriasGuardar',$idCurso)}}" id="creacion" enctype="multipart/form-data">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @csrf

        <div style="text-align: right;width:65%">
            <label for="tituloformulariocurso">

            </label>
        </div>

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-tecnico-tab" data-toggle="pill" href="#pills-tecnico" role="tab" aria-controls="pills-tecnico" aria-selected="true">Informacion Curso</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-evalalum-tab" data-toggle="pill" href="#pills-evalalum" role="tab" aria-controls="pills-evalalum" aria-selected="false">Evaluacion Alumno</a>
            </li>

            <li class="nav-item">
                <a class="nav-link " id="pills-paqdid-tab" data-toggle="pill" href="#pills-paqdid" role="tab" aria-controls="pills-paqdid" aria-selected="false">Paqueterias Didacticas</a>
            </li>
        </ul>
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        @endif
        @if ($message = Session::get('warning'))
        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>
        @endif
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-tecnico" role="tabpanel" aria-labelledby="pills-tecnico-tab">
                @include('layouts.pages.paqueteriasDidacticas.blades.curso')
            </div>
            <div class="tab-pane fade " id="pills-evalalum" role="tabpanel" aria-labelledby="pills-evalalum-tab">
                @include('layouts.pages.paqueteriasDidacticas.blades.evaluacionAlumno')
            </div>
            <div class="tab-pane fade" id="pills-paqdid" role="tabpanel" aria-labelledby="pills-paqdid-tab">
                @include('layouts.pages.paqueteriasDidacticas.blades.descargarPaqueteria')
            </div>
        </div>
    </form>
    <br>
</div>


@section('script_content_js')
<script src="{{asset('vendor/ckeditor5-decoupled-document/ckeditor.js') }}"></script>
<script>
    //inicializacion de text areas ckeditor 5
    var editorContenidoT;
    var editorEstrategiaD;
    var editorProcesoE;
    var editorDuracionT;
    var editorContenidoE;

    var editorElementoA;
    var editorAuxE;
    var editorReferencias;
    var idCurso = <?php echo json_encode($idCurso); ?>;
    //Define an adapter to upload the files
    class MyUploadAdapter {
        constructor(loader) {
            // The file loader instance to use during the upload. It sounds scary but do not
            // worry — the loader will be passed into the adapter later on in this guide.
            this.loader = loader;

            // URL where to send files.
            this.url = '{{ route('ckeditorUpload') }}';
            

            //
        }
        // Starts the upload process.
        upload() {
            return this.loader.file.then(
                (file) =>
                new Promise((resolve, reject) => {
                    this._initRequest();
                    this._initListeners(resolve, reject, file);
                    this._sendRequest(file);
                })
            );
        }
        // Aborts the upload process.
        abort() {
            if (this.xhr) {
                this.xhr.abort();
            }
        }
        // Initializes the XMLHttpRequest object using the URL passed to the constructor.
        _initRequest() {
            const xhr = (this.xhr = new XMLHttpRequest());
            // Note that your request may look different. It is up to you and your editor
            // integration to choose the right communication channel. This example uses
            // a POST request with JSON as a data structure but your configuration
            // could be different.
            // xhr.open('POST', this.url, true);
            xhr.open("POST", this.url, true);
            xhr.setRequestHeader("x-csrf-token", "{{ csrf_token() }}");
            xhr.responseType = "json";
        }
        // Initializes XMLHttpRequest listeners.
        _initListeners(resolve, reject, file) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Couldn't upload file: ${file.name}.`;
            xhr.addEventListener("error", () => reject(genericErrorText));
            xhr.addEventListener("abort", () => reject());
            xhr.addEventListener("load", () => {
                const response = xhr.response;
                // This example assumes the XHR server's "response" object will come with
                // an "error" which has its own "message" that can be passed to reject()
                // in the upload promise.
                //
                // Your integration may handle upload errors in a different way so make sure
                // it is done properly. The reject() function must be called when the upload fails.
                if (!response || response.error) {
                    return reject(response && response.error ? response.error.message : genericErrorText);
                }
                // If the upload is successful, resolve the upload promise with an object containing
                // at least the "default" URL, pointing to the image on the server.
                // This URL will be used to display the image in the content. Learn more in the
                // UploadAdapter#upload documentation.
                resolve({
                    default: response.url,
                });
            });
            // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
            // properties which are used e.g. to display the upload progress bar in the editor
            // user interface.
            if (xhr.upload) {
                xhr.upload.addEventListener("progress", (evt) => {
                    if (evt.lengthComputable) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                });
            }
        }
        // Prepares the data and sends the request.
        _sendRequest(file) {
            // Prepare the form data.
            
            const data = new FormData();
            data.append("upload", file);
            data.append("idCurso", idCurso);
            // Important note: This is the right place to implement security mechanisms
            // like authentication and CSRF protection. For instance, you can use
            // XMLHttpRequest.setRequestHeader() to set the request headers containing
            // the CSRF token generated earlier by your application.
            // Send the request.
            this.xhr.send(data);
        }
        // ...
    }

    function SimpleUploadAdapterPlugin(editor) {
        editor.plugins.get("FileRepository").createUploadAdapter = (loader) => {
            // Configure the URL to the upload script in your back-end here!
            return new MyUploadAdapter(loader);
        };
    }

    ClassicEditor
        .create(document.querySelector('#objetivoespecifico'), {language: 'es',})
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#aprendizajeesperado'), {language: 'es',})
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#observaciones'), {language: 'es',})
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('#contenidoT-inp'), {
            extraPlugins: [SimpleUploadAdapterPlugin],
            language: 'es',
        })
        .then(editor => {
            editorContenidoT = editor;
        })
        .catch(error => {
            console.error(error);
        });


    ClassicEditor
        .create(document.querySelector('#elementoapoyo'), {
            language: 'es',
        })
        .then(editor => {
            editorElementoA = editor;
        })
        .catch(error => {
            console.error(error);
        });
    ClassicEditor
        .create(document.querySelector('#auxenseñanza'), {
            language: 'es',
        })
        .then(editor => {
            editorAuxE = editor;
        })
        .catch(error => {
            console.error(error);
        });
    ClassicEditor
        .create(document.querySelector('#referencias'), {
            language: 'es',
        })
        .then(editor => {
            editorReferencias = editor;
        })
        .catch(error => {
            console.error(error);
        });

    $(document).ready(function() {
        $("#botonCARTADESCPDF").click(function() {
            $('#creacion').attr('action', "{{route('DescargarPaqueteria',$idCurso)}}");
            $('#creacion').attr('target', "_blank");
            
            $('#creacion').submit();
        });
        $("#botonEVALALUMNPDF").click(function() {
            $('#creacion').attr('action', "{{route('DescargarEvalAlumno',$idCurso)}}");
            $('#creacion').attr('target', "_blank");
            
            $('#creacion').submit();
        });
        $("#botonEVALINSTRUCTORPDF").click(function() {
            $('#creacion').attr('action', "{{route('DescargarEvalInstructor')}}");
            $('#creacion').attr('target', "_blank}");
            
            $('#creacion').submit();
        });
        $("#botonMANUALDIDPDF").click(function() {
            $('#creacion').attr('action', "{{route('DescargarManualDidactico',$idCurso)}}");
            $('#creacion').attr('target', "_blank");
            
            $('#creacion').submit();
            // $('#alert-files').css('display', 'block');
            // $('#files-msg').text("La generacion de este archivo estara disponible pronto!");
        });


    });

    function save(blade) {
        
        var $form = $("#creacion");
        $('#creacion').attr('action', "{{route('paqueteriasGuardar',$idCurso)}}");
        $('#creacion').removeAttr('target');
       
        $form.append("<input type='hidden' name='blade' value='"+blade+"'/>");
        $('#creacion').submit();
    }
</script>
<script src="{{asset('js/catalogos/paqueteriasdidactica/paqueterias.js')}}"></script>

<script defer>
    // $('#preguntas-area-parent .card-paq').remove()
    var evaluacion = Object.values(JSON.parse({!!json_encode($evaluacionAlumno, JSON_HEX_TAG) !!}));
    //   console.log(values.length
</script>
@endsection
@endsection