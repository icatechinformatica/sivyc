<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
<div class="table-responsive">
    <table class="table ">                
        <thead>
            <tr>                
                <th scope="col">CLAVE</th>
                <th scope="col">CURSO</th>
                <th scope="col">INSTRUCTOR</th>
                <th scope="col" width="86px">INICIO</th>
                <th scope="col" width="86px">TERMINO</th>
                <th scope="col" width="87px">HORARIO</th>                
            </tr>
        </thead>
        <tbody>            
            @foreach ($data as $item)
                <tr>                                    
                     <td>{{ $item->clave }}</td>
                     <td>{{ $item->curso }}</td>
                     <td>{{ $item->nombre }}</td>
                     <td>{{ $item->inicio }}</td>
                     <td>{{ $item->termino }}</td>
                     <td>{{ $item->hini }} - {{ $item->hfin }}</td>                     
                </tr>
            @endforeach
                <tr>
                    <td colspan="8" >
                       {{ $data->render() }} 
                     </td>
                </tr>
        </tbody>
        <tfoot>             
        </tfoot>
    </table>
</div>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">URL Generada</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" id="textURL" name="textURL" rows="3"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>        
      </div>
    </div>
  </div>
</div>

<script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
<script>
    function generar(id) {
       // alert(id); exit;
        $.ajax({                           
            //data: {"dato" : id},
            url:   '/siceTest/public/supervision/url/instructor/'.id,
            type:  'GET',
            success:  function (response) {
                    //alert("pasa");
                    $('#textURL').val(response);
            }
        }); 
    }
    /*
    $(document).ready(function(){            
        $("#btnURL" ).click(function() {
            $.ajax({   
                        
               //data: {"dato" : "0"},
               url:   'url/instructor',
               type:  'GET',
               success:  function (response) {
                    //alert("pasa");
                    $('#textURL').val(response);
               }
            });                
        });        
    });
    */
</script>