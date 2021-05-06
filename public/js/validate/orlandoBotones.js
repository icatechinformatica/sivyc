//Creado por Orlando Chavez
$(function(){
    //metodo
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    //Boton de busqueda en frmpago
    $("#search_").click(function(e){
        e.preventDefault();
        $.ajax({
            type:'POST',
            url:'/pago/fill',
            data: { numero_contrato: $('#numero_contrato').val()},
            success: function(data){
                nombre = data.nombre + " " + data.apellido_paterno + " " + data.apellido_materno
                $('#numero_control').val(data.id)
                $('#nombre_instructor').val(nombre)
            },
        });
    });

    //Boton de Busqueda en frmcursosvalidados
    $("#search_cv").click(function(e){
        e.preventDefault();
        $.ajax({
            type:'POST',
            url:'/validar-curso/fill1',
            data: { numero_control: $('#numero_control').val()},
            success: function(data){
                $('#nombreins').val(data.nombre)
                $('#id_ins').val(data.id)
            },
        });
    });

    // Boton modificar en pagosmod
    $("#mod_").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#numero_contrato').prop("disabled",false)
                $('#numero_pago').prop("disabled",false)
                $('#fecha_pago').prop("disabled",false)
                $('#concepto').prop("disabled",false)
                $('#nombre_solicita').prop("disabled",false)
                $('#nombre_autoriza').prop("disabled",false)
                $('#reacd02').prop("disabled",false)
            }
        });
    });
    // Boton modificar en verinstructor
    $("#mod_instructor").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#nombre').prop("disabled", false)
                $('#apellido_paterno').prop("disabled", false)
                $('#apellido_materno').prop("disabled", false)
                $('#curp').prop("disabled",false)
                $('#rfc').prop("disabled",false)
                $('#folio_ine').prop("disabled",false)
                $('#sexo').prop("disabled",false)
                $('#estado_civil').prop("disabled",false)
                $('#fecha_nacimiento').prop("disabled",false)
                $('#lugar_nacimiento').prop("disabled",false)
                $('#lugar_residencia').prop("disabled",false)
                $('#domicilio').prop("disabled",false)
                $('#telefono').prop("disabled",false)
                $('#correo').prop("disabled",false)
                $('#banco').prop("disabled",false)
                $('#clabe').prop("disabled",false)
                $('#numero_cuenta').prop("disabled",false)
                $('#exp_laboral').prop("disabled",false)
                $('#exp_docente').prop("disabled",false)
                $('#cursos_recibidos').prop("disabled",false)
                $('#cursos_conocer').prop("disabled",false)
                $('#cursos_impartidos').prop("disabled",false)
                $('#capacitado_icatech').prop("disabled",false)
                $('#cursos_recicatech').prop("disabled",false)
                $('#tipo_honorario').prop("disabled",false)
                $('#registro_agente').prop("disabled",false)
                $('#uncap_validacion').prop("disabled",false)
                $('#memo_validacion').prop("disabled",false)
                $('#memo_mod').prop("disabled",false)
                $('#observacion').prop("disabled",false)
                $('#unidad_registra').prop("disabled",false)
                $('#honorario').prop("disabled",false)
                $('#arch_ine').prop("disabled",false)
                $('#arch_domicilio').prop("disabled",false)
                $('#arch_curp').prop("disabled",false)
                $('#arch_alta').prop("disabled",false)
                $('#arch_banco').prop("disabled",false)
                $('#arch_foto').prop("disabled",false)
                $('#arch_estudio').prop("disabled",false)
                $('#arch_id').prop("disabled",false)
                $('#entidad').prop("disabled",false)
                $('#municipio').prop("disabled",false)
                $('#asentamiento').prop("disabled",false)
            }
        });
    });
    i=0;
    m = $('#wa').val();
    //Botones en tabla modsupre
        $("#addmodsupre").click(function(){
            ++m;
            console.log(m);
            $("#dynamicTablemodsupre").append('<tr><td><input type="text" name="addmore['+m+'][folio]" id="addmore['+m+'][folio]" placeholder="folio" class="form-control" /></td><td><input type="text" name="addmore['+m+'][numeropresupuesto]" id="addmore['+m+'][numeropresupuesto]" placeholder="Numero Presupuesto" class="form-control" disabled value="12101" /></td><td><input type="text" name="addmore['+m+'][clavecurso]" id="addmore['+m+'][clavecurso]" placeholder="Clave curso" class="form-control" /></td><td><input type="text" name="addmore['+m+'][importe]" id="addmore['+m+'][importe]" placeholder="importe total" class="form-control" /><footer name="addmore['+m+'][aviso]" id="addmore['+m+'][aviso]" style="color: red"></footer></td><td><input type="text" name="addmore['+m+'][iva]" id="addmore['+m+'][iva]" placeholder="Iva" class="form-control" /></td><td><input type="text" name="addmore['+m+'][comentario]" id="addmore['+m+'][comentario]" placeholder="comentario" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-trmodsupre">Eliminar</button></td></tr>');

        });

        $("#mod_supre").click(function(e){
            e.preventDefault();
            $.ajax({
                success: function(){
                    $('#unidad_capacitacion').prop("disabled", false)
                    $('#no_memo').prop("disabled", false)
                    $('#fecha').prop("disabled", false)
                    $('#destino').prop("disabled", false)
                    $('#puesto_para').prop("disabled", false)
                    $('#remitente').prop("disabled",false)
                    $('#puesto_remitente').prop("disabled",false)
                    $('#nombre_valida').prop("disabled",false)
                    $('#puesto_valida').prop("disabled",false)
                    $('#nombre_elabora').prop("disabled",false)
                    $('#puesto_elabora').prop("disabled",false)
                    $('#nombre_ccp1').prop("disabled",false)
                    $('#puesto_ccp1').prop("disabled",false)
                    $('#nombre_ccp2').prop("disabled",false)
                    $('#puesto_ccp2').prop("disabled",false)
                    $('#btn_guardar_supre').prop("disabled",false)
                }
            });
        });

        $(document).on('click', '.remove-trmodsupre', function(){
            $(this).parents('tr').remove();
        });
    //END Botones en tabla modsupre

    //boton valsupre rechazar
    $("#valsupre_rechazar").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#divrechazar').prop("class", "form-row")
                $('#divconf_rechazar').prop("class", "form-row")
            }
        });
    });
    //boton valsupre confirmar
    $("#valsupre_validar").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#div1').prop("class", "form-row")
                $('#div2').prop("class", "form-row")
                $('#div3').prop("class", "form-row")
                $('#div4').prop("class", "form-row")
                $('#div5').prop("class", "form-row")
                $('#div6').prop("class", "form-row")
                $('#div7').prop("class", "form-row")
                $('#confval').prop("class", "form-row")
            }
        });
    });

    //boton mod_contrato
    $("#mod_contrato").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#numero_contrato').prop("disabled", false)
                $('#perfil_instructor').prop("disabled", false)
                $('#cantidad_numero').prop("disabled", false)
                $('#cantidad_letras').prop("disabled", false)
                $('#lugar_expedicion').prop("disabled", false)
                $('#fecha_firma').prop("disabled", false)
                $('#nombre_director').prop("disabled", false)
                $('#unidad_capacitacion').prop("disabled", false)
                $('#no_circulardir').prop("disabled", false)
                $('#testigo1').prop("disabled", false)
                $('#testigo2').prop("disabled", false)
                $('#testigo3').prop("disabled", false)
                $('#save-contrato').prop("disabled", false)
            }
        });
    });

    //boton Contrato Rechazar
    $("#rechazarContrato").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#rechazar_contrato').prop("class", "form-row")
                $('#btn_rechazar').prop("class", "form-row");
            }
        });
    });

    //Boton Contrato Validar
    $('#validarContratoModel').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        $('#validarForm').attr("action", "/contrato/valcontrato/" + id);
    });

    //Boton Insturctor alta/baja modal
    $('#alta_bajaModel').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        console.log(id);
        $('#validarForm').attr("action", "/instructor/baja-alta" + "/" + id);
    });

    //Boton Instructor Rechazar
    $("#instructor_rechazar").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#divrechazarins').prop("class", "form-row")
                $('#divconf_rechazarins').prop("class", "form-row")
            }
        });
    });

    //Boton Instructor Validar
    $("#instructor_validar").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#div1').prop("class", "form-row")
                $('#div2').prop("class", "form-row")
                $('#div3').prop("class", "form-row")
                $('#div4').prop("class", "form-row")
                $('#div5').prop("class", "form-row")
                $('#confvali').prop("class", "form-row")
            }
        });
    });

    //Boton Instructor Editar Fase2
    $('#mod_instructor_fase2').click(function(e){
        $('#nombre').prop("disabled", false)
        $('#apellido_paterno').prop("disabled", false)
        $('#apellido_materno').prop("disabled", false)
        $('#curp').prop("disabled", false)
        $('#rfc').prop("disabled", false)
        $('#folio_ine').prop("disabled", false)
        $('#sexo').prop("disabled", false)
        $('#estado_civil').prop("disabled", false)
        $('#fecha_nacimientoins').prop("disabled", false)
        $('#entidad').prop("disabled", false)
        $('#municipio').prop("disabled", false)
        $('#asentamiento').prop("disabled", false)
        $('#domicilio').prop("disabled", false)
        $('#telefono').prop("disabled", false)
        $('#correo').prop("disabled", false)
        $('#banco').prop("disabled", false)
        $('#clabe').prop("disabled", false)
        $('#numero_cuenta').prop("disabled", false)
        $('#arch_ine').prop("disabled", false)
        $('#arch_domicilio').prop("disabled", false)
        $('#arch_curp').prop("disabled", false)
        $('#arch_alta').prop("disabled", false)
        $('#arch_banco').prop("disabled", false)
        $('#arch_estudio').prop("disabled", false)
        $('#arch_foto').prop("disabled", false)
        $('#arch_rfc').prop("disabled", false)
        $('#arch_id').prop("disabled", false)
        $('#unidad_registra').prop("disabled", false)
        $('#honorario').prop("disabled", false)
        $('#savemodbuttonins').prop("disabled", false)
    });

    $( document ).on('input', function(){
        if(document.getElementById('curso') != null || document.getElementById('instructor') != null || document.getElementById('general') != null || document.getElementById('unidad') != null)
        {
            if (document.getElementById('curso').checked) {
                $('#div_curso').prop("class", "form-row")
                $('#div_instructor').prop("class", "form-row d-none d-print-none")
                $('#div_unidad').prop("class", "form-row d-none d-print-none")
            }
            else if (document.getElementById('instructor').checked) {
                $('#div_curso').prop("class", "form-row d-none d-print-none")
                $('#div_instructor').prop("class", "form-row")
                $('#div_unidad').prop("class", "form-row d-none d-print-none")
            }
            else if (document.getElementById('general').checked) {
                $('#div_curso').prop("class", "form-row d-none d-print-none")
                $('#div_instructor').prop("class", "form-row d-none d-print-none")
                $('#div_unidad').prop("class", "form-row d-none d-print-none")
            }
            else if (document.getElementById('unidad').checked) {
                $('#div_curso').prop("class", "form-row d-none d-print-none")
                $('#div_instructor').prop("class", "form-row d-none d-print-none")
                $('#div_unidad').prop("class", "form-row")
            }
        }
    });

   $( document ).on('input', function(){
        if(document.getElementById('curso') != null || document.getElementById('general') != null || document.getElementById('unidad') != null)
        {
            if (document.getElementById('curso').checked) {
                $('#div_curso').prop("class", "form-row")
                $('#div_instructor').prop("class", "form-row d-none d-print-none")
                $('#div_unidad').prop("class", "form-row d-none d-print-none")
            }
            else if (document.getElementById('general').checked) {
                $('#div_curso').prop("class", "form-row d-none d-print-none")
                $('#div_instructor').prop("class", "form-row d-none d-print-none")
                $('#div_unidad').prop("class", "form-row d-none d-print-none")
            }
            else if (document.getElementById('unidad').checked) {
                $('#div_curso').prop("class", "form-row d-none d-print-none")
                $('#div_instructor').prop("class", "form-row d-none d-print-none")
                $('#div_unidad').prop("class", "form-row")
            }
        }
    });
});
