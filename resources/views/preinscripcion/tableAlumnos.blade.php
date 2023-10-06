<table class="table table-striped col-md-12" id='tblAlumnos'>
  <thead>
    <tr>
      <th class="h6" scope="col">#</th>
      <th class="h6" scope="col">Curp</th>
      <th class="h6" scope="col">Nombre</th>
      <th class="h6" scope="col">Matricula</th>
      <th class="h6" scope="col">Sexo</th>
      <th class="h6" scope="col" width="8%">Fec. Nac.</th>
      <th class="h6" scope="col">Escolaridad</th>
      <th scope="col" class="h6">TIPO DE INSCRIPCI&Oacute;N</th>
      <th scope="col" class="h6 text-center" width="8%">COUTA</th>
      <th class="h6 text-center" scope="col"> @if($activar){{'Eliminar'}}@endif</th>
      <th class="h6 text-center" scope="col">SID</th>
      <th class="h6 text-center" scope="col">CURP</th>
      @if ($edicion)
        <th class="h6 text-center" scope="col">REMPLAZAR</th>
      @endif
      <!--<th class="h6 text-center" scope="col">Subir SID</th>--->
    </tr>
  </thead>
  <tbody>
    @if(count($alumnos)>0)
      @foreach($alumnos as $a)
        @php
          if ($costo < $a->costo) {
            $class= 'form-control numero bg-danger';
          } else {
            $class = 'form-control numero';
          }
        @endphp
        <tr id="{{$a->id_reg}}">
          <th scope="row"> {{ $consec++ }} </th>
          <th>{{ $a->curp }}</th>
          <th>{{ $a->apellido_paterno }} {{$a->apellido_materno}} {{$a->nombre}}</th>
          <th>{{ $a->no_control}}</th>
          <th>{{ $a->sex }}</th>
          <th>{{ $a->fnacimiento }}</th>
          <th>{{ $a->escolaridad }}</th>
          <th>{{$a->tinscripcion}}</th>
          <th class="text-center">
            {{ Form::number('costo['.$a->id_reg.']', $a->costo , ['id'=>'costo['.$a->id_reg.']', 'size' => 1, 'maxlength' => '7', 'class' => $class]) }}
          </th>
          <th class="text-center">
            @if($activar)
              <a class="nav-link" ><i class="fa fa-remove  fa-2x fa-lg text-danger" onclick="eliminar({{$a->id_reg}},'{{ route('preinscripcion.grupo.eliminar') }}');" title="Eliminar"></i></a>
            @endif
          </th>
          <th class="text-center">
            @if($a->id_cerss)
              <a target="_blank" href="{{route('documento.sid_cerrs', ['nocontrol' => base64_encode($a->id_reg)])}}" class="nav-link" ><i class="fa fa-print  fa-2x fa-lg text-info" title="Imprimir SID"></i></a>
            @else
              <a target="_blank" href="{{route('documento.sid', ['nocontrol' => base64_encode($a->id_reg)])}}" class="nav-link" ><i class="fa fa-print  fa-2x fa-lg text-info" title="Imprimir SID"></i></a>
            @endif
          </th>
          <!--
            <th class="text-center">
              <a class="nav-link" ><i class="fa fa-upload  fa-2x fa-lg text-danger" title="Cargar SID"></i></a>
            </th>
          -->
          <th class="text-center">
            @if (isset($a->requisitos))
              <?php
                $documento = json_decode($a->requisitos);
                $documento = $documento->documento;
              ?>
              <a target="_blank" href="{{$documento}}" class="nav-link"><i class="fa fa-print  fa-2x fa-lg text-info" title="Imprimir CURP"></i></a>
            @else
              <a target="_blank" href="{{$a->documento_curp}}" class="nav-link"><i class="fa fa-print  fa-2x fa-lg text-info" title="Imprimir CURP"></i></a>
            @endif
          </th>
          @if ($edicion)
            <th class="text-center">
              <a class="nav-link" ><i class="fa fa-edit  fa-2x fa-lg text-success" title="Editar" onclick="rem('{{$a->curp}}');"></i></a>
            </th>
          @endif
        </tr>
        <?php
          if(!$a->tinscripcion) $turnar=false;
        ?>
      @endforeach
    @endif
  </tbody>
</table>


<div class="d-flex flex-lg-row flex-column col-12 col-lg-12 justify-content-end">
    @if ($grupo)
        <button id="btnShowCalendar" type="button" class="btn btn-info mt-1">AGENDAR</button>
        @can('agenda.vinculacion')
        {{-- @can('agenda.vinculacion' || $slug == 'admin') temporalmente se comentó hasta que homologuen --}}
            @if ($grupo->cespecifico)
                    <button type="button" class="btn mt-1" id="gen_acta_acuerdo">ACTA DE ACUERDO</button>
                    {{-- <button type="button" class="btn mt-1" id="gen_convenio_esp">CONVENIO</button> --}}
            @endif
            <button type="button" class="btn mt-1" id="gape">GENERAR SOLICITUD</button>
        @endcan
        <button type="button" class="btn mt-1" id="generar">GENERAR LISTA</button>
    @endif

    <button type="button" class="btn mt-1" id="nuevo" >NUEVO</button> &nbsp;&nbsp;

    @if($activar AND $folio_grupo)
        <button type="submit" class="btn mt-1" id="update" >GUARDAR CAMBIOS </button> &nbsp;&nbsp;
        <button type="button" class="btn mt-1 bg-danger " id="turnar" >ENVIAR A LA UNIDAD >> </button>
    @endif
</div>


{{-- Agregamos el apartado de subir PDF Jose Luis Moreno Arcos--}}
{{-- temporalmente de comentó hasta que se homologue se revisa --}}
{{-- <br>
<div class="col-12 col-lg-4 mt-3 mt-lg-0 mb-lg-1 ml-lg-4 text-center text-lg-left">
    <b class="">SUBIR DOCUMENTOS FIRMADOS</b>
</div>
<div class="col-12 d-flex flex-lg-column">
    <div class="col-12">
        <div class="col-12 col-lg-4 d-flex flex-row mb-1 px-0">
            <form method="POST" class="" enctype="multipart/form-data" action="" id="doc_acta">
                <div class="">
                    <input type="file" id="pdfInputActa" accept=".pdf" style="display: none;" onchange="cargarNomFileActa()">
                    <input class="form-control py-2 mt-1" type="text" id="nomPdfActa" onclick="document.getElementById('pdfInputActa').click()" placeholder="PDF Acta de acuerdo">
                </div>
                <div class="">
                    <a class="btn px-1 py-1 mr-0" id="btnEnvPdfActa" onclick="upPdfActaFirm()"><i class="fa fa-cloud-upload fa-2x" aria-hidden="true"></i></a>
                    <input type="hidden" name="" id="url_acta_hiden" value="{{$url_pdf_acta != '' || $url_pdf_acta != null ? $url_pdf_acta : ''}}">
                    @if ($url_pdf_acta)
                        <a class="btn px-1 py-1" href="{{$url_pdf_acta != '' ? $url_pdf_acta : '#'}}" target="_blank"><i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i></a>
                    @endif
                </div>
            </form>
        </div>
        <div class="col-12 col-lg-4 d-flex flex-row mb-0">
            <form method="POST" class="d-flex row" enctype="multipart/form-data" action="" id="doc_convenio">
                <div class="">
                    <input type="file" id="pdfInputConvenio" accept=".pdf" style="display: none;" onchange="cargarNomFileConvenio()">
                    <input class="form-control py-2 mt-1" type="text" id="nomPdfConvenio" onclick="document.getElementById('pdfInputConvenio').click()" placeholder="PDF Convenio Especifico">
                </div>
                <div class="">
                    <a class="btn px-1 py-1 mr-0" id="btnEnvPdfConv" onclick="upPdfConvFirm()"><i class="fa fa-cloud-upload fa-2x" aria-hidden="true"></i></a>
                    <input type="hidden" name="" id="url_conv_hiden" value="{{$url_pdf_conv != '' || $url_pdf_conv != null ? $url_pdf_conv : ''}}">
                    @if ($url_pdf_conv)
                        <a class="btn px-1 py-1" href="{{$url_pdf_conv != '' ? $url_pdf_conv : '#'}}" target="_blank"><i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div> --}}
{{-- Termina el elemento de subir archivo --}}

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">REMPLAZO DE ALUMNO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row d-flex align-items-center">
            <div class="col-12">
                <form id="modal">
                    <div class="row">
                        <div class="col">
                            <label for="">CURP:</label>
                            <input type="text" id="curpo" name="curpo" class="form-control" readonly>
                        </div>
                        <div class="col">
                            <label for="">NUEVA CURP:</label>
                            <input name='busqueda1' id='busqueda1' oninput="validarRemplazo(this)" type="text" class="form-control" value="{{old('curp')}}"/>
                            <pre id="resultado1"></pre>
                        </div>
                    </div>
                </form>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        <button type="button" class="btn btn-primary" id="btnremplazo">REMPLAZAR</button>
      </div>
    </div>
  </div>
</div>


