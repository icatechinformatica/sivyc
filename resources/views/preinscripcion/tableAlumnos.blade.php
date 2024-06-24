<div class="table-responsive ">
<table class="table table-bordered table-striped" id='tblAlumnos'>
  <thead>
    <tr>
      <th class="h6" scope="col">#</th>
      <th class="h6" scope="col">Curp</th>      
      <th class="h6" scope="col">Matricula</th>
      <th class="h6" scope="col">Nombre</th>
      <th class="h6" scope="col">Sexo</th>
      <th class="h6" scope="col" width="7%">Fec. Nac.</th>      
      <th class="h6" scope="col">Edad</th>                
      <th class="h6" scope="col"  width="7%">Escolaridad</th>
      <th class="h6" scope="col">Gpo.<br/>Vulnerable</th>
      <th class="h6" scope="col">Otro. Gpo.</th>
      <th class="h6" scope="col">Nac.</th>
      <th class="h6" scope="col">Tipo Inscrip.</th>
      <th class="h6" scope="col">        
        <div style="width: 80px;">
          {{ Form::number('costoX', null , ['id'=>'costoX', 'maxlength' => '7', 'class' => 'form-control numero', 'placeholder' => 'Cuota']) }}
        </div>
      </th>
       @if($activar)<th class="h6" scope="col">{{'Eliminar'}}</th>@endif
       @if ($edicion)
        <th class="h6" scope="col">REMPLAZAR</th>
      @endif
      <th class="h6" scope="col">SID</th>
      <th class="h6 " scope="col">CURP</th>      
      <!--<th class="h6 text-center" scope="col">Subir SID</th>--->
    </tr>
  </thead>
  <tbody>
    @if(count($alumnos)>0)
      @foreach($alumnos as $a)
        @php
          if ($costo < $a->costo) {
            $class= 'form-control numero bg-danger costo';
          } else {
            $class = 'form-control numero costo';
          }
          if($grupo) $id_cerss = $grupo->id_cerss;
          else $id_cerss = null;
        @endphp
        <tr id="{{$a->id_reg}}">
          <th scope="row"> {{ $consec++ }} </th>
          <th class="text-left" style="word-wrap: break-word; max-width: 90px;">{{ $a->curp }}</th>
          <th style="word-wrap: break-word; max-width: 60px;">{{ $a->no_control}}</th>
          <th class="text-left">{{ $a->apellido_paterno }} {{$a->apellido_materno}} {{$a->nombre}}</th>          
          <th>{{ $a->sex }}</th>
          <th>{{ date('d/m/Y', strtotime($a->fnacimiento)) }}</th>
          <th>{{ $a->edad }} AÑOS</th>
          <th class="text-left">{{ $a->escolaridad }}</th>
          <th class="text-left">{{ $a->grupos }}</th>
          <td class="text-left">
            @if($a->inmigrante == true) INMIGRANTE <br/> @endif
            @if($a->familia_migrante == true) FAMILIA_MIGRANTE <br/> @endif
            @if($a->madre_soltera == true) MADRE_SOLTERA <br/> @endif
            @if($a->lgbt == true) LGBT <br/> @endif
            @if($a->es_cereso == true OR $id_cerss) CERSS @endif
          </td>
          <th>{{$a->nacionalidad}}</th>
          <th  style="word-wrap: break-word; max-width: 60px;">{{$a->tinscripcion}}</th>
          <th> 
            <div style="width: 80px;">           
              {{ Form::number('costo['.$a->id_reg.']', $a->costo , ['id'=>'costo['.$a->id_reg.']', 'size' => '7', 'maxlength' => '7', 'class' => $class]) }}            
            </div>
          </th>          
            @if($activar)
            <th>          
              <a class="nav-link" ><i class="fa fa-remove  fa-2x fa-lg text-danger" onclick="eliminar({{$a->id_reg}},'{{ route('preinscripcion.grupo.eliminar') }}');" title="Eliminar"></i></a>
            </th>
            @endif
          @if ($edicion)
            <th>
              <a class="nav-link" ><i class="fa fa-edit  fa-2x fa-lg text-success" title="Editar" onclick="rem('{{$a->curp}}');"></i></a>
            </th>
          @endif          
          <th>            
              <a target="_blank" href="{{route('documento.sid', ['nocontrol' => base64_encode($a->id_reg)])}}" class="nav-link" ><i class="fa fa-print  fa-2x fa-lg text-info" title="Imprimir SID"></i></a>            
          </th>
          <!--
            <th class="text-center">
              <a class="nav-link" ><i class="fa fa-upload  fa-2x fa-lg text-danger" title="Cargar SID"></i></a>
            </th>
          -->
          <th>
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
        </tr>
        <?php
          if(!$a->tinscripcion) $turnar=false;
        ?>
      @endforeach
    @endif
  </tbody>
</table>
</div>

<div class="d-flex flex-lg-row flex-column col-12 col-lg-4 justify-content-left mb-2 mt-3">
    {{-- Agregamos el apartado de subir PDF Jose Luis Moreno Arcos--}}  
    @can('agenda.vinculacion')        
            <div class="d-flex">
                <select name="subirPDF" id="subirPDF" class="form-control" onchange="selUploadPDF()">
                    <option value="0">SUBIR DOCUMENTOS FIRMADOS</option>
                    <option value="1?{{$linkPDF['acta']}}">Acta de Acuerdo</option>
                    <option value="2?{{$linkPDF['convenio']}}">Convenio Especifico</option>
                    <option value="3?{{$linkPDF['soli_ape']}}">Solicitud de Apertura</option>
                    <option value="4?{{$linkPDF['sid']}}">SID-01 Solicitud de Inscripcion</option>
                </select>            
                <form class="form-inline" method="POST" enctype="multipart/form-data" action="" id="form_doc12">
                    <div class="d-flex justify-content-center" id="formUpPdf">
                        <input type="file" name="pdfFile" accept=".pdf" id="pdfInputDoc" style="display: none;" onchange="checkIcon('iconCheck', 'pdfInputDoc')">
                        <button class="ml-2 btn-outline-primary btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc').click();">Archivo
                        <div id="iconCheck" style="display:none;"><i class="fas fa-check-circle"></i></div></button>

                        <button class="ml-1 bg-transparent border-0 mt-1" onclick="UploadPDF(event, '{{$linkPDF['status_dpto']}}')">
                        <i class="fa fa-cloud-upload fa-2x text-primary" aria-hidden="true"></i></button>
                    </div>
                    <a class="ml-1 pt-1 btn-circle btn-circle-sm d-none" data-toggle="tooltip"
                        data-placement="top" title="Ver pdf" id="verPdfLink"
                        href="" target="_blank">
                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                    </a>
                </form>
            </div>
        
    @endcan
    {{-- Termina subir archivo --}}
  </div>
  <div class="d-flex flex-lg-row flex-column col-12 col-lg-8 justify-content-end">    
    
    @can('agenda.vinculacion')
        {{-- @dd($grupo) --}}
        @if(isset($grupo->cespecifico) && $organismo != null)
            @if ($organismo == 'CAPACITACION ABIERTA')
                <button type="button" class="btn mt-3 bg-warning text-dark" id="gen_acta_acuerdo"> <i  class="far fa-file-pdf  fa-1x text-mute"></i> ACTA DE ACUERDO</button>
            @else
                <button type="button" class="btn mt-3 bg-warning text-dark d-none" id="gen_convenio_esp"><i  class="far fa-file-pdf  fa-1x text-mute"></i> CONVENIO</button>
            @endif
        @endif
        <button type="button" class="btn mt-3 bg-warning text-dark" id="gape"><i  class="far fa-file-pdf  fa-1x text-mute"></i> SOLICITUD APERTURA</button>
    @endcan

    @if($grupo)
        <button type="button" class="btn mt-3 bg-warning text-dark" id="generar"><i  class="far fa-file-pdf  fa-1x text-mute"></i> LISTA ALUMNOS</button>
        <button id="btnShowCalendar" type="button" class="btn btn-info mt-3">AGENDAR</button>
    @endif
    <button type="button" class="btn mt-3" id="nuevo" >NUEVO</button>
    @if($activar AND $folio_grupo)
        <button type="submit" class="btn mt-3" id="update" >GUARDAR CAMBIOS </button> &nbsp;&nbsp;
        <button type="button" class="btn mt-3 bg-danger " id="turnar" >ENVIAR A ACADEMICO >></button>
    @endif
</div>

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
                              <label for="">CURP ANTERIOR:</label>
                              <input type="text" id="curpo" name="curpo" class="form-control" readonly>
                          </div>
                          <div class="col">
                              <label for="">CURP NUEVA:</label>
                              <input name='busqueda1' id='busqueda1' oninput="validarRemplazo(this)" type="text" class="form-control" value="{{old('curp')}}"/>
                              <pre id="resultado1"></pre>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
        </div>
        <div class="modal-footer">          
          <button type="button" class="btn btn-danger" id="btnremplazo">REMPLAZAR</button>
          <button type="button" class="btn" data-dismiss="modal">CERRAR</button>
        </div>
      </div>
    </div>
</div>