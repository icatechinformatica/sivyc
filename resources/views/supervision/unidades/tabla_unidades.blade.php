<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
<div class="table-responsive">
    <table class="table ">                
        <thead>            
            <tr>
                <th scope="col">UNIDADES</th>
                <th scope="col">CURSOS</th>
                <th scope="col">SUPERVISIONES<br/>PROGRAMADAS</th>
                <th scope="col">SUPERVISIONES<br/>REALIZADAS</th>
                <th scope="col">SUPERVISIONES<br/>PENDIENTES</th>                
                <th scope="col">INCIDENCIAS<br />ENCONTRADAS </th>
                <th scope="col">COBERTURA DE<br/>SUPERVISIONES%</th>
                <th scope="col">DIRECTOR(A)</th>
                <th width="15%">DETALLE</th>
            </tr>
        </thead>
        <tbody>            
            @foreach ($data as $item)
                <tr> 
                     <td>{{ $item->unidad }}</td>                
                     <td>{{ $item->cursos }}</td>
                     <td>{{ $item->cursos*3 }}</td>
                     <td>{{ $item->supervisiones }}</td>
                     <td>{{ $item->cursos*3-$item->supervisiones }}</td>
                     <td>{{ $item->incidencias }}</td>
                     <td>{{ ROUND(($item->supervisiones)*100/($item->cursos*3),0) }} %</td>
                     <td>{{ $item->dunidad }}</td>                 
                     <td>
                     <!--
                        <button type="button" id="btnURL"  name="btnURL" onclick="detalle('{{ $item->unidad }}' );"  class="btn" data-toggle="modal" data-target="#exampleModal" @if($item->supervisiones<1){{ 'disabled' }} @endif >
                          &nbsp;&nbsp;SUPERVISIONES&nbsp;&nbsp;
                        </button>
                     -->  
                        <button type="button" id="btnURL"  name="btnURL" onclick="detalle('{{ $item->unidad }}' );"  class="btn" data-toggle="modal" data-target="#exampleModal" @if($item->incidencias<1){{ 'disabled' }} @endif >
                          INCIDENCIAS
                        </button>                       
                     </td>
                </tr>
            @endforeach
                
        </tbody>
        <tfoot>             
        </tfoot>
    </table>
</div>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
  <div class="modal-dialog" role="document" style="max-width: 90%;">
    <div class="modal-content">
      <div class="modal-header">        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>      
      <div class="modal-body">
            <div id="ventana"> </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>        
      </div>
    </div>
  </div>
</div>

<script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
<script>
    function detalle(id) {       
        $.ajax({            
            data: {"fecha" : '{{$fecha}}'},            
            //url:   '/siceTest/public/supervision/unidades/cursos/YAJALON',//.id,
            url:   '/siceTest/public/supervision/unidades/detalle/'+id,                    
            type:  'GET',
            success:  function (response) {
                    //alert(response);exit;
                    //$('#textURL').val(response);
                      $("#ventana").html(response);
            }
        }); 
    }
    /*
    function cursos(id) {       
        $.ajax({            
            data: {"fecha" : '{{$fecha}}'},            
            //url:   '/siceTest/public/supervision/unidades/cursos/YAJALON',//.id,
            url:   '/siceTest/public/supervision/unidades/cursos/'+id,                    
            type:  'GET',
            success:  function (response) {
                    //alert(response);exit;
                    //$('#textURL').val(response);
                      $("#ventana").html(response);
            }
        }); 
    }
    */
</script>