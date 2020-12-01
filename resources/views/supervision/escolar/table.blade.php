<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->

<div class="table-responsive">
    <table class="table ">
        <thead>
            <tr>
                <th scope="col">UNIDAD</th>
                <th scope="col">CLAVE</th>
                <th scope="col">CURSO</th>
                <th scope="col">INSTRUCTOR</th>
                <th scope="col" width="86px">INICIO</th>
                <th scope="col" width="86px">TERMINO</th>
                <th scope="col" width="7%">HORARIO</th>
                <th>ESTATUS</th>
                <th width="15%">AL INSTRUCTOR</th>
                <th colspan="2" width="15%">A UN ALUMNO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>                
                     <td>{{ $item->unidad }}</td>
                     <td>{{ $item->clave }}</td>
                     <td><a target="blank" href="{{ url("/supervision/curso/{$item->clave}")}}" class="text-primary" title="Ver detalles del curso.."><b>{{ $item->curso }}</b></a></td>
                     <td>{{ $item->nombre }}</td>
                     <td>{{ $item->inicio }}</td>
                     <td>{{ $item->termino }}</td>
                     <td>{{ $item->hini }} - {{ $item->hfin }}</td>
                     <td>
                        <?php
                            $status_supervision = $path_file = "";
                            if($item->json_supervision){                          
                                $js =  json_decode($item->json_supervision);
                                $status_supervision = $js->original->status;
                                if(isset($js->original->archivo))$path_file = $js->original->archivo;
                            }
                        ?>
                        @if($status_supervision)                     
                            <button type="button" class="btn btn-danger" onclick="statusCurso({{$item->json_supervision}},'{{ asset($path_file) }}');"  data-toggle="modal" title="Ver">
                                {{ $status_supervision  }}                            
                            </button>
                        @else
                            <button type="button" id="bn{{$item->id}}"  onclick="document.getElementById('id_curso').value='{{$item->id}}'"  class="btn " data-toggle="modal" data-target="#ModalIniciado" >
                                &nbsp;INICIAR&nbsp;                          
                            </button>                        
                        @endif                                                
                     </td>
                     <td>
                        <button type="button" onclick="generarURL({{$item->id}},'instructor');" id="{{$item->id}}"  class="btn @if(time() > $item->ttl_instructor AND $item->ttl_instructor){{'bg-danger'}}@elseif($item->token_instructor){{'bg-warning'}}@endif" data-toggle="modal">
                            URL
                        </button>
                        @if($item->total>0)
                            <a class="btn text-white" href="{{ url('/supervision/instructor/revision/'.$item->id) }}" >
                                REVISAR({{$item->total}})
                            </a>
                        @else
                            <a href="#" class="btn disabled text-white" role="button">REVISAR&nbsp;&nbsp;&nbsp;&nbsp;</a>
                        @endif
                     </td>
                     <td>
                        
                        @if($item->ins_alumnos>0)
                            <button type="button" onclick="generarListaAlumnos({{$item->id}});"  class="btn @if($item->token_alumno){{'bg-warning'}}@endif" data-toggle="modal" data-target="#frmListaAlumnos">
                                URL ({{ $item->ins_alumnos}})
                            </button>
                        @else
                            <button type="button" class="btn" disabled >
                                &nbsp;URL ({{ $item->ins_alumnos}})
                            </button>                            
                        @endif
                        
                        @if($item->total_alumnos>0)
                            <a class="btn text-white" href="{{ url('/supervision/alumno/revision/'.$item->id) }}" >
                                REVISAR({{$item->total_alumnos}})
                            </a>
                        @else
                            <a href="#" class="btn disabled text-white" role="button">REVISAR&nbsp;&nbsp;&nbsp;&nbsp;</a>
                        @endif
                     </td>
                </tr>
            @endforeach
                <tr>
                    <td colspan="10" >
                       {{ $data->render() }}
                     </td>
                </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>

<!-- Modal LISTA ALUMNOS-->
<div class="modal fade " id="frmListaAlumnos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Seleccione a un alumno de la lista</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div id="group" class="list-group">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-gray-dark" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Genera URL-->
<div id="modalURL" class="modal face"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">URL Generada</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" id="textURL" name="textURL" rows="10" style="text-transform: none;" disabled></textarea>
      </div>
      <div class="modal-footer">
        <button id="btnCerrar" type="button" class="btn btn-gray-dark" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div id="ModalIniciado" class="modal face"  tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Iniciar curso</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <form method="POST" enctype="multipart/form-data" id="frm_iniciado">
                <div class="modal-body">               
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputmodalidad">Estatus:</label>
                            <select class="form-control" id="status_supervision" name="status_supervision">
                                <option value="">--SELECCIONAR--</option>
                                <option value="INICIADO" {{ old('status_supervision') == 'INICIADO' ? 'selected' : '' }}>INICIADO</option>
                                <option value="MODIFICADO" {{ old('status_supervision') == 'MODIFICADO' ? 'selected' : '' }}>MODIFICADO</option>
                                <option value="CANCELADO" {{ old('status_supervision') == 'CANCELADO' ? 'selected' : '' }}>CANCELADO</option>
                                <option value="EXTRAORDINARIO" {{ old('status_supervision') == 'EXTRAORDINARIO' ? 'selected' : '' }}>EXTRAORDINARIO</option>
                            </select>                             
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputarch_ine">Archivo Soporte:</label>
                            <input id="file_soporte" name="file_soporte" type="file" class="file-loading"/>
                        </div> 
                     </div>
                     <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputmodalidad">Observaci&oacute;n breve:</label>
                            <textarea class="form-control" id="textObs" name="textObs" rows="2" style="text-transform: none;"></textarea>
                        </div>
                    </div>
                
              </div>
              <div class="modal-footer">
                  <button type="button" onclick="updateCurso();" class="btn btn-primary" >Guardar</button>
                  <button id="btnCerrar2" type="button" class="btn btn-gray-dark" data-dismiss="modal">Cerrar</button>
              </div>
              <input id="id_curso" name="id_curso" type="hidden" value=""/>
        </form>
    </div>
  </div>
</div>

<div id="ModalEstatus" class="modal face"  tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
          <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Estatus del Curso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
          </div>            
                <div class="modal-body">               
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputEstatus">Estatus:</label>
                            <input id="status" class="form-control" type="text" readonly />                            
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputFecha">Fecha:</label>
                            <input id="fec" class="form-control" type="text" readonly />                            
                        </div>
                        <div class="form-group col-md-3">
                            <label for="inputarch_ine">Archivo Soporte:</label>
                            <a target="_blank" href="#" class="btn  btn-info text-white" role="button" id="link" download>&nbsp;ABRIR&nbsp;</a>
                        </div> 
                     </div>
                     <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputmodalidad">Observaci&oacute;n breve:</label>
                            <textarea class="form-control" id="obs" name="textObs" rows="2" style="text-transform: none;" readonly ></textarea>
                        </div>
                    </div>
                
              </div>
              <div class="modal-footer">                  
                  <button id="btnCerrar3" type="button" class="btn btn-gray-dark" data-dismiss="modal">Cerrar</button>
              </div>
        
    </div>
  </div>
</div>