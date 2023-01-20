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
<div class="col-md-12 text-right">
  @if ($grupo)
    <button id="btnShowCalendar" type="button" class="btn btn-info">AGENDAR</button>
    @can('agenda.vinculacion')
         <button type="button" class="btn" id="gape">GENERAR SOLICITUD DE APERTURA</button>
    @endcan
    <button type="button" class="btn" id="generar">GENERAR LISTA DE ALUMNOS</button>
  @endif
  <button type="button" class="btn" id="nuevo" >NUEVO GRUPO</button> &nbsp;&nbsp;
  @if($activar AND $folio_grupo)
    <button type="submit" class="btn" id="update" >GUARDAR CAMBIOS </button> &nbsp;&nbsp;                        
    <button type="button" class="btn bg-danger " id="turnar" >ENVIAR A LA UNIDAD >> </button>
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
