<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.sivycSuperv.layout')
@section('title', 'Registro de Alumnos | Sivyc Icatech')
@section('content')         
    <section class="container g-py-40 g-pt-40 g-pb-0">
    @if(session('mensaje'))
        <div class="card text-gray bg-warning">
            <div class="card-header">
                <div class="row warning">   
                    <div class="col-md-9 ">
                        <br />
                        {{ html_entity_decode(session('mensaje')) }}
                        <br />  <br />                  
                    </div>  
                </div>
            </div>
            
        </div>
        <br />
    @else
         {{ Form::open(['url' => '/supervision/funcionario-guardar', 'method' => 'post', 'enctype' => 'multipart/form-data' ]) }}
            @csrf
            <div class="text-center">
                <h1>Cuestionario para el Personal del ICATECH<h1>
            </div>
            <div>
                <label><h4>DATOS GENERALES</h4></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputarch_ine">Foto...</label>
                    <input id="file_photo" name="file_photo" type="file" class="file-loading"/>
                </div>                
            </div>
            <div class="form-row">
                
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input name='apellido_paterno' id='apellido_paterno' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input name='apellido_materno' id='apellido_materno' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputnombre">Nombre Completo</label>
                    <input name='nombre' id='nombre' type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputrfc">RFC</label>
                    <input name='rfc' id='rfc' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputeenlace">Enlace</label>
                    <input name='enlace' id='enlace' type="text" class="form-control" aria-required="true">
                </div>
                 <div class="form-group col-md-4">
                    <label for="inputescolaridad">Escolaridad</label>
                    <input name='escolaridad' id='escolaridad' type="text" class="form-control" aria-required="true">
                </div>
            </div>
            
            <br />
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputpartida_presupuestal">1. Partida Presupuestal</label>
                    <input name='partida_presupuestal' id='partida_presupuestal' type="text" class="form-control" aria-required="true">
                </div> 
                <div class="form-group col-md-4">
                    <label for="inputantiguedad_gobierno">2. Antig&utilde;edad en Gobierno</label>
                    <input name='antiguedad_gobierno' id='antiguedad_gobierno' type="text" class="form-control" aria-required="true">
                </div>
                 <div class="form-group col-md-4">
                    <label for="inputantigueda_icatech">3. Antig&utilde;edad en ICATECH</label>
                    <input name='antigueda_icatech' id='antiguedad_icatech' type="text" class="form-control" aria-required="true">
                </div> 
            </div>
            <div class="form-row">            
                
                 <div class="form-group col-md-4">
                    <label for="inputcategoria">4. Categor&iacute;a Actual</label>
                    <input name='categoria' id='categoria' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto">5. Puesto Actual</label>
                    <input name='puesto' id='puesto' type="text" class="form-control" aria-required="true">
                </div>  
                 <div class="form-group col-md-4">
                    <label for="inputadscripcion_nominal">6. Adscripci&oacute;n Nominal Actual</label>
                    <input name='clave_oficina_nominal' id='clave_oficina_nominal' type="text" class="form-control" aria-required="true">
                </div>
            </div>
            
            <div class="form-row">            
                <div class="form-group col-md-4">
                    <label for="inputjefe_nominal">7. Jefe Inmediato seg&uacute;n N&oacute;mina</label>
                    <input name='jefe_nominal' id='jefe_nominal' type="text" class="form-control" aria-required="true">
                </div>  
                 <div class="form-group col-md-4">
                    <label for="inputclave_oficina_comision">8. Adscripci&oacute;n Funcional (Comisi&oacute;n)</label>
                    <input name='clave_oficina_comision' id='clave_oficina_comision' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputjefe_nominal">9. Jefe Inmediato en Comisi&oacute;n</label>
                    <input name='jefe_nominal' id='jefe_nominal' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputtiempo_comision">10. Tiempo de estar Comisionado</label>
                    <input name='tiempo_comision' id='tiempo_comision' type="text" class="form-control" aria-required="true">
                </div>
            </div>
            
            <div>
                <label><h4>ACTIVIDADES</h4></label>
            </div>
            <div class="form-row">  
                <table id="lista" class="table table-striped"> 
                     <thead>
                        <tr>
                           <th>#</th>
                           <th> 11. ACTIVIDADES QUE DESEMPE&Ntilde;A </th>
                           <th> FORMATO OFICIAL O SISTEMA INFORM&Aacute;TICO  QUE UTILIZA: </th>
                           <th> PERIODICIDAD:DIARIO, SEMANAL, MENSUAL, EVENTUAL </th> 
                           <th>  </th> 
                        </tr> 
                     </thead> 
                     <tbody> 
                        <tr>
                           <td>
                                <label name='numero[]' >1.</label>
                           </td>
                           <td>
                            <input name='actividad[]' type="text" placeholder="ACTIVIDAD" class="form-control actividad" aria-required="true" />
                              
                           </td> 
                           <td> 
                              <input name='formato[]' type="text" placeholder="FORMATO o SISTEM" class="form-control formato" aria-required="true" />                               
                           </td> 
                           <td> 
                              <input name='periodicidad[]' type="text" placeholder="PERIODICIDAD" class="form-control periodicidad" aria-required="true" />                              
                           </td> 
                           <td>  
                              <button type="button" class="btn btn-danger button_eliminar"> Eliminar </button>
                           </td> 
                        </tr> 
                     </tbody> 
                     <tfoot> 
                        <tr> 
                           <td colspan="4">  </td>
                           <td> 
                              <button type="button" class="btn btn-success button_agregar"> Agregar </button> 
                           </td> 
                       </tr> 
                    </tfoot> 
                </table> 
            </div>
            
            
            
            <div class="form-row">            
                <div class="form-group col-md-6">
                    <label for="inputmobiliario_equipo">12. &iquest;Qu&eacute; mobiliario y equipo tiene asignado bajo resguardo?</label>
                    <textarea class="form-control" name="mobiliario_equipo" rows="3" ></textarea>
                </div>  
                 <div class="form-group col-md-6">
                    <label for="inputreporta_Actividades">13. &iquest;A qui&eacute;n reporta sus actividades realizadas?, &iquest;con qu&eacute; frecuencia y de qu&eacute; manera?</label>
                    <textarea class="form-control" name="reporta_Actividades" rows="3" ></textarea>
                </div>
            </div>
            <div class="form-row">            
                <div class="form-group col-md-6">
                    <label for="inputcomision_fuera">14. &iquest;Es comisionado fuera de la ciudad de adscripci&oacute;n?, &iquest;con qu&eacute; frecuencia y por qu&eacute;?</label>
                    <textarea class="form-control" name="comisionado_fuera" rows="3" ></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputcuantas_personas">15. &iquest;Cu&aacute;ntas personas integran junto con usted el equipo de trabajo?</label>
                    <textarea class="form-control" name="cuantas_personas" rows="3" ></textarea>
                </div>
            </div>
       
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                      
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
            
            
            <br/>
        {!! Form::close() !!}
    @endif
    </section>
    
    
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script>
 
    function actividades(){ 
       var tbody = $('#lista tbody'); 
       var fila_contenido = tbody.find('tr').first().html();
       //Agregar fila nueva. 
       $('#lista .button_agregar').click(function(){ 
          var fila_nueva = $('<tr></tr>');
          fila_nueva.append(fila_contenido); 
          tbody.append(fila_nueva); 
       }); 
       //Eliminar fila. 
       $('#lista').on('click', '.button_eliminar', function(){
          $(this).parents('tr').eq(0).remove();
       });
    }
    
    
    $(document).ready(function(){
       actividades(); 
    }); 

    $( function() {
      var dateFormat = "dd-mm-yy",
        from = $( ".datepicker" )
          .datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'dd-mm-yy'
          })
          .on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          }),
        to = $( "#fecha_termino" ).datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          dateFormat: 'dd-mm-yy'
        })
        .on( "change", function() {
          from.datepicker( "option", "maxDate", getDate( this ) );
        });

      function getDate( element ) {
        var date;
        try {
          date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
          date = null;
        }

        return date;
      }
    } );
</script>

@stop

