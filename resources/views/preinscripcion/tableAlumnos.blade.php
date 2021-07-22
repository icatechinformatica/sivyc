  <table class="table table-striped col-md-12" id='tblAlumnos'>
    <thead>
      <tr>
        <th class="h6" scope="col">#</th>            
        <th class="h6" scope="col">Curp</th>               
        <th class="h6" scope="col">Nombre</th>
        <th class="h6" scope="col">Sexo</th>
        <th class="h6" scope="col" width="8%">Fec. Nac.</th>
        <th class="h6" scope="col">Escolaridad</th>
        <th class="h6 text-center" scope="col"> @if($activar){{'Eliminar'}}@endif</th>                               
        <th class="h6 text-center" scope="col">SID</th>
        <!--<th class="h6 text-center" scope="col">Subir SID</th>--->
                                                  
      </tr>
    </thead>                                    
    <tbody>
      @foreach($alumnos as $a)                               
          <tr id="{{$a->id_reg}}">
            <th scope="row"> {{ $consec++ }} </th>
            <th>{{ $a->curp }}</th>
            <th>{{ $a->apellido_paterno }} {{ $a->apellido_materno }} {{ $a->nombre }}</th> 
            <th>{{ $a->sex }}</th>
            <th>{{ $a->fnacimiento }}</th>
            <th>{{ $a->ultimo_grado_estudios }}</th>
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
          </tr>
          <?php
                if(!$a->tinscripcion) $turnar=false;
          ?>
      @endforeach                                                                                              
    </tbody>
</table> 
    <div class="col-md-12 text-right">
        <button type="button" class="btn" id="nuevo" >NUEVO GRUPO</button> &nbsp;&nbsp;
        @if($activar AND $folio_grupo)
            <button type="submit" class="btn" id="update" >GUARDAR CAMBIOS </button> &nbsp;&nbsp;                        
            <button type="button" class="btn bg-danger " id="turnar" >ENVIAR A LA UNIDAD >> </button>
        @endif 
    </div>
                