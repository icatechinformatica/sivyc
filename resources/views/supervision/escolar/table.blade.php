<!--Elabor� Romelia P�rez Nang�el� - rpnanguelu@gmail.com-->

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
                <th width="15%">AL INSTRUCTOR</th>
                <th colspan="2" width="20%">A UN ALUMNO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                     <td>{{ $item->unidad }}</td>
                     <td>{{ $item->clave }}</td>
                     <td>{{ $item->curso }}</td>
                     <td>{{ $item->nombre }}</td>
                     <td>{{ $item->inicio }}</td>
                     <td>{{ $item->termino }}</td>
                     <td>{{ $item->hini }} - {{ $item->hfin }}</td>
                     <td>
                        <button type="button" id="btnURL"  name="btnURL" onclick="generarURL({{$item->id}},'instructor');"  class="btn @if($item->token_instructor){{'bg-warning'}}@endif" data-toggle="modal">
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
                        <button type="button" id="btnURL"  name="btnURL" onclick="generarListaAlumnos({{$item->id}});"  class="btn @if($item->token_alumno){{'bg-warning'}}@endif" data-toggle="modal" data-target="#frmListaAlumnos">
                          URL
                        </button>

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
        <textarea class="form-control" id="textURL" name="textURL" rows="3" style="text-transform: none;"></textarea>
      </div>
      <div class="modal-footer">
        <button id="btnCerrar" type="button" class="btn btn-gray-dark" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
