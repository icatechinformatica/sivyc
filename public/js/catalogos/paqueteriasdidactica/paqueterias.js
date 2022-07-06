var ponderacionTotal = 0;
var rowSelected;
/*
 *
 * ==========================================
 * CURSO JS
 * ==========================================
 *
 */

var idPonderacion = 0;
var valPonderacion = [];
var storePonderacion = document.getElementById('storePonderacion');

var idContenido = 1;
var valContenidoT = [];
var storeContenidoT = document.getElementById('storeContenidoT');

var idRecursos = 0;
var valRecursosD = [];
var storeRecursosD = document.getElementById('storeRecursosD');


$(document).ready(function () {

    if ($('#storePonderacionOld').val() != '') {

        var valuesPonderacion = Object.values(JSON.parse($('#storePonderacionOld').val()))
    
        valuesPonderacion.forEach(element => {
            $('#criterio').val(element.criterio)
            $('#ponderacion').val(element.ponderacion)
            document.getElementById('addPonderacion').click()
        });
    }

    if ($('#storeContenidoTOld').val() != '') {
        valContenidoT = JSON.parse($('#storeContenidoTOld').val() )
        idContenido = valContenidoT.length+1
        for(var i=0; i<valContenidoT.length; i++)
            valContenidoT[i].id = (i+1)//fix id's
        
        storeContenidoT.value = JSON.stringify(valContenidoT)
        addContenidoToSelect(JSON.parse(storeContenidoT.value));
        $('.body-element').attr("id",valContenidoT.length+1); 
        
    }


    if (evaluacion != '' || !evaluacion) {//auto completa formulario de evaluacion de alumno con datos de la DB// 
        
        $('#numPreguntas').val(evaluacion.length-1);
        console.log($('#numPreguntas').val());
        //evaluacion viene de paqueterias_didacticas.blade 
        var abecedario = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        for (let i = 0; i < evaluacion.length-1; i++) {
            const element = evaluacion[i];
            

                $('select[name="pregunta' + (i + 1) + '-tipo"] option[value="' + element.tipo + '"]').attr("selected", "selected");
                $('select[name="pregunta' + (i + 1) + '-contenidoT"]').children('option[value="'+element.contenidoTematico+'"]').attr("selected", "selected")
                
                $('input[name="pregunta' + (i + 1) + '"]').val(element.descripcion);

                var opciones = element.opciones
                
                $('#pregunta' + (i + 1) + '-opc .input-group').remove();
                for (var j = 0; j < opciones.length; j++) {
                    $('#pregunta' + (i + 1) + '-opc').append(opcionTemplate('pregunta' + (i + 1) + '-opc'));
                }
                var opcionesInp = $('#pregunta' + (i + 1) + '-opc').children('.input-group')
                for (let j = 0; j < opcionesInp.length; j++) {
                    $(opcionesInp[j]).children('.form-control').val(opciones[j])
                    if (element.respuesta == abecedario[j]) {
                        $(opcionesInp[j]).children('.input-group-text').children('input:radio').attr('checked', true);
                        var checkbox = $(opcionesInp[j]).children('.input-group-text').children('input:radio')[0]
                        setAceptedAnswer(checkbox)
                    }
                }
                // break;
                
                agregarPregunta()
            
        }
        $ultimaPregunta = $('.card-paq:last').remove()
        numPreguntas--;
        $('#numPreguntas').val(numPreguntas);
        
    }


});

function getID(e) {
    index = e;
}
function buscarEspecialidad() {
    var especialidad = document.getElementById("especialidad").value.toUpperCase()

    if (especialidad !== "") {
        $.ajax({
            url: '/especialidadBuscador/',
            type: 'get',
            data: { "especialidad": especialidad },
            dataType: 'json',
            success: function (response) {
                var len = response.length;
                $("#searchResult").empty();
                for (var i = 0; i < len; i++) {
                    var id = response[i]['id']
                    var nombre = response[i]['nombre'];
                    $("#searchResult").append("<li onclick='getID(this.id)' id='" + i + "' value='" + id + "'> " + nombre + "</li>");
                }
                $("#searchResult li").on("click", function () {
                    var especialidad = response[index]['nombre'];
                    $("#especialidad").val(especialidad);
                    $("#searchResult").empty();
                });
            }
        });
    } else {
        $("#searchResult").empty();
    }
}

function agregarponderacion() {
    var tbodyElement = document.getElementById('tEvaluacion');
    var numelement = tbodyElement.rows.length;

    if (!$('#criterio').val() || !$('#ponderacion').val()) {
        $('#alert-evaluacion').css('display', 'block');
        $('#eval-msg').text("No se aceptan valores vacios, intente de nuevo!");
        return
    } else if (parseInt($('#ponderacion').val()) + ponderacionTotal > 100) {
        $('#alert-evaluacion').css('display', 'block');
        $('#eval-msg').text("La sumatoria de los criterios debe ser 100%");
        return
    } else if (parseInt($('#ponderacion').val()) < 0 || parseInt($('#ponderacion').val()) == 0) {
        $('#alert-evaluacion').css('display', 'block');
        $('#eval-msg').text("Introduzca valores positivos por favor!");
        return
    } else
        $('#alert-evaluacion').css('display', 'none');

    // tbodyElement.appendChild(trElement);
    ponderacionTotal += parseInt($('#ponderacion').val());
    $('<tr id="criterio' + idPonderacion + '">' +
        '<td>' + $('#criterio').val() + '</td>' +
        '<td>' + $('#ponderacion').val() + '</td>' +
        '<td> ' +
        '<a class="btn btn-info btn-circle m-1 btn-circle-sm" onclick="removerCriterio(' + idPonderacion + ')">' +
        '   <i class="fa fa-window-close" aria-hidden="true"></i>' +
        '</a>' +
        '</td>' +
        '</tr>').prependTo(tbodyElement);


    valPonderacion.push({
        'id': idPonderacion,
        'criterio': $('#criterio').val(),
        'ponderacion': $('#ponderacion').val()
    });


    storePonderacion.value = JSON.stringify(valPonderacion);
    $('#criterio').val('');
    $('#ponderacion').val('');
    idPonderacion++;

}

function addRowContenidoT(row) {
    var contenidoVal = $(row).parents(':eq(1)').children()[0];
    var estrategiaVal = $(row).parents(':eq(1)').children()[1];
    var procesoVal = $(row).parents(':eq(1)').children()[2];
    var duracionVal = $(row).parents(':eq(1)').children()[3];
    var contenidoExtraVal = $(row).parents(':eq(1)').children()[4];




    // if(contenidoVal.includes("Click aqui para agregar")){
    //     $('#alert-contenido').css('display', 'block');
    //     $('#contenido-msg').text("No se aceptan valores vacios, intente de nuevo!");    
    //     return
    // }else{
    //     $('#alert-contenido').css('display', 'none');
    // }
    $(row).siblings('.remove-tr').css('display', 'block');//muestra boton eliminar tr 
    $(row).css('display', 'none');



    valContenidoT.push({
        'id': idContenido,
        'tema_principal': $('#inpTemaPrincipal').val(),
        'contenido': $('#contenidoValues').val(),
        'estrategia': $('#estrategiaValues').val(),
        'proceso': $('#procesoValues').val(),
        'duracion': $('#duracionValues').val(),
        'contenidoExtra': $('#contenidoExtraValues').val(),
    });
    
    storeContenidoT.value = JSON.stringify(valContenidoT);

    addContenidoToSelect(JSON.parse(storeContenidoT.value));

    idContenido++;

    var tbodyElement = document.getElementById('tTemario');
    var nuevaOpcion = $(
        '<tr id="' + idContenido + '">' +
        '<td data-toggle="modal" data-placement="top" class="contenidoT" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td data-toggle="modal" data-placement="top" class="estrategiaD" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td data-toggle="modal" data-placement="top" class="procesoE" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td data-toggle="modal" data-placement="top" class="duracion" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td data-toggle="modal" data-placement="top" class="contenidoE text-preview" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td>' +
        '<button type="button" class="btn btn-danger remove-tr" onclick="deleteRowContenidoT(this)" style="display: none;">Eliminar</button>' +
        '<button type="button" class="btn btn-success" onclick="addRowContenidoT(this)">Agregar</button>' +
        '</td>' +
        '</tr>'
    );
    $(tbodyElement).append(nuevaOpcion);
}



function deleteRowContenidoT(tr) {
    var divParent = $(tr).parents(':eq(1)')[0]; // 
    valContenidoT.forEach(function (item, index, object) {
        if (item.id == (divParent.id)) {
            object.splice(index, 1);
        }
    });
    storeContenidoT.value = JSON.stringify(valContenidoT);
    $(divParent).remove();
}

var getU = (idCurso) => {
    var myModalLabel = $("#titleModal");
}

function setValuesEditor() {

    if ($(rowSelected).hasClass('contenidoT')) $('#contenidoValues').val(editorContenidoT.getData())
    else if ($(rowSelected).hasClass('estrategiaD')) $('#estrategiaValues').val(editorContenidoT.getData())
    else if ($(rowSelected).hasClass('procesoE')) $('#procesoValues').val(editorContenidoT.getData())
    else if ($(rowSelected).hasClass('duracion')) $('#duracionValues').val(editorContenidoT.getData())
    else if ($(rowSelected).hasClass('contenidoE')) $('#contenidoExtraValues').val(editorContenidoT.getData())
    
    rowSelected.innerHTML = editorContenidoT.getData();
    if ($(rowSelected).hasClass('contenidoT')){

    }
    

    var idParent = $(rowSelected).parent()[0].id;

    if (valContenidoT.find(x => x.id == idParent)) {

        valContenidoT.forEach(function (item, index, object) {
            if (item.id == (idParent)) {
                
                if ($(rowSelected).hasClass('contenidoT')) item.contenido = editorContenidoT.getData()
                else if ($(rowSelected).hasClass('estrategiaD')) item.estrategia = editorContenidoT.getData()
                else if ($(rowSelected).hasClass('procesoE')) item.proceso = editorContenidoT.getData()
                else if ($(rowSelected).hasClass('duracion')) item.duracion = editorContenidoT.getData()
                else if ($(rowSelected).hasClass('contenidoE')) item.contenidoExtra = editorContenidoT.getData()

                if ($(rowSelected).hasClass('contenidoT'))
                    item.tema_principal = $('#inpTemaPrincipal').val()
            }
        });

        storeContenidoT.value = JSON.stringify(valContenidoT);

        addContenidoToSelect(JSON.parse(storeContenidoT.value));

    } else {
        
    }
    document.getElementById('btnCloseModal').click()
}
function showEditorTxtModal(row) {
    idContenido = $(row).parents(':eq(0)')[0].id
    if ($(row).hasClass('contenidoT')) {
        $('#temaPrincipal').css('display', 'block')
        valContenidoT.forEach(function (item, index, object) {
            if (item.id == idContenido ) {
                $('#inpTemaPrincipal').val(item.tema_principal)
            }
        });
    } else {
        $('#temaPrincipal').css('display', 'none')
    }
    var txtRow = $(row).html();
    if (txtRow.length > 0) {
        editorContenidoT.data.set(txtRow);
    }
    rowSelected = row;
}


function agregarRecursosD() {
    var tbodyElement = document.getElementById('tRecursosD');

    var trElement = document.createElement('tr');
    var elementoApoyo = document.createElement('td');
    var auxEnseñanza = document.createElement('td');
    var referencias = document.createElement('td');
    var accionElement = document.createElement('td');
    var aElement = document.createElement('a');
    var iElement = document.createElement('i');


    if (!editorElementoA.getData() || !editorAuxE.getData() || !editorReferencias.getData())
        return

    elementoApoyo.innerHTML = editorElementoA.getData();
    auxEnseñanza.innerHTML = editorAuxE.getData();
    referencias.innerHTML = editorReferencias.getData();


    trElement.setAttribute("id", 'recurso' + idRecursos);
    aElement.setAttribute("onclick", 'removerRecurso(' + idRecursos + ')');

    iElement.classList.add("material-icons");
    iElement.innerHTML = '&#xE872;';

    aElement.appendChild(iElement);
    accionElement.appendChild(aElement);

    trElement.appendChild(elementoApoyo);
    trElement.appendChild(auxEnseñanza);
    trElement.appendChild(referencias);
    trElement.appendChild(accionElement);
    tbodyElement.appendChild(trElement);


    valRecursosD.push({
        'id': idRecursos,
        'elementoapoyo': editorElementoA.getData(),
        'auxenseñanza': editorAuxE.getData(),
        'referencias': editorReferencias.getData(),
    });

    storeRecursosD.value = JSON.stringify(valRecursosD);

    editorElementoA.data.set("");
    editorAuxE.data.set("");
    editorReferencias.data.set("");

    idRecursos++;
}

function removerContenido(idContenido) {

    valContenidoT.forEach(function (item, index, object) {
        if (item.id === (idContenido)) {
            object.splice(index, 1);
        }
    });
    storeContenidoT.value = JSON.stringify(valContenidoT);

    var tr = document.getElementById('contenido' + idContenido);
    document.getElementById('tTemario').removeChild(tr);
    addContenidoToSelect(JSON.parse(storeContenidoT.value))
}

function removerCriterio(idCriterio) {
    var tr = document.getElementById('criterio' + idCriterio);
    document.getElementById('tEvaluacion').removeChild(tr);


    valPonderacion.forEach(function (item, index, object) {
        if (item.id === (idCriterio)) {
            ponderacionTotal -= item.ponderacion;
            object.splice(index, 1);
        }
    });
    storePonderacion.value = JSON.stringify(valPonderacion);
}

function removerRecurso(idRecurso) {
    var tr = document.getElementById('recurso' + idRecurso);
    document.getElementById('tRecursosD').removeChild(tr);

    valRecursosD.forEach(function (item, index, object) {
        if (item.id === (idRecurso)) {
            object.splice(index, 1);
        }
    });
    storeRecursosD.value = JSON.stringify(valRecursosD);
}

function changeSiblings(tr) {
    while (tr = tr.nextSibling) {
        var newNum = parseInt(tr.children[0].textContent) - 1;
        tr.children[0].innerHTML = newNum;
    }
}


/*
 *
 * ==========================================
 * EVAL ALUMNO JS
 * ==========================================
 *
 */

var idPregunta = 1;
var opcion = 0;
var numPreguntas = 1;


$('#preguntas-area-parent').on("click", 'div.card-paq', function (e) {

    var selected = $(this).find('div.card-paq, .hideable').show(200)
    var notselected = $("#preguntas-area-parent div.card-paq .hideable").not(selected).hide(200)
});

function addContenidoToSelect(contenido) {
    $('.contenidoTematicoPregunta')
        .find('option')
        .remove()
    for (var i = 0; i < contenido.length; i++) {
        var temaPrincpal = contenido[i].tema_principal;
        $('.contenidoTematicoPregunta').append($('<option>', {
            value: temaPrincpal,
            text: temaPrincpal
        }));
    }


}

function agregarPregunta(boton) {

    idPregunta ++
    var numChildren = idPregunta 
    numPreguntas++;
    console.log(idPregunta, numPreguntas);
    $('#numPreguntas').val(numPreguntas);

    var nuevaPregunta = $(
        '<div class="card-paq col-md-12">' +
        '<div class="contentBx col-md-12">' +
        '<br>' +
        '<div class="row col-md-12"  id="pregunta' + numChildren + '" >' +
        '<div class="form-row col-md-12 hideable">' +
        '<!-- selects -->' +

        '<div class="form-group col-md-6 col-sm-6">' +
        '<select onchange="cambiarTipoPregunta(this)" class="form-control" name="pregunta' + numChildren + '-tipo">' +
        '<option value="multiple" selected>Multiple</option>' +
        '<option value="abierta">Abierta</option>' +
        '</select>' +
        '</div>' +
        '<div class="form-group col-md-6 col-sm-6">' +

        '<select class="form-control contenidoTematicoPregunta" name="pregunta' + numChildren + '-contenidoT">' +
        '<option disbled selected value="">Contenido tematico de la pregunta</option>' +
        '</select>' +
        '</div>' +

        '</div>' +
        '<div class="form-row col-md-7 col-sm-12">' +
        '<div class="form-group col-md-12 col-sm-10">' +
        '<input placeholder="Pregunta sin texto" type="text" class="form-control resp-abierta g-input" name="pregunta' + numChildren + '">' +
        '</div>' +
        '</div>' +



        '<div class="form-row col-md-7 opcion-area-p' + numChildren + '" id="pregunta' + numChildren + '-opc">' +
        '<input type="text" hidden id="pregunta' + numChildren + '-opc-answer" name="pregunta' + numChildren + '-opc-answer">' +
        '<div class="input-group mb-3 ">' +

        '<div class="input-group-text">' +
        '<input type="radio" onclick="setAceptedAnswer(this)" name="pregunta' + numChildren + '-opc-correc[]">' +
        '</div>' +

        '&nbsp;&nbsp;&nbsp;' +
        '<input placeholder="Opcion" type="text" class="form-control resp-abierta multiple" name="pregunta' + numChildren + '-opc[]">' +
        '<a class="btn btn-warning btn-circle m-1 btn-circle-sm" onclick="removerOpcion(this)">' +
        '<i class="fa fa-minus"></i>' +
        '</a>' +
        '</div>' +
        '</div>' +

        '<div class="form-row col-md-6 opcion-area-pregunta' + numChildren + ' hideable">' +
        '<div class="input-group mb-3">' +
        '<a style="cursor: default;" onclick="agregarOpcion(this)">Agregar opcion</a>' +
        '</div>' +
        '</div>' +

        '<div class="form-row col-md-7 respuesta-abierta-area ra-p' + numChildren + '" style="display: none">' +
        '<div class="input-group mb-3">' +
        '<input  placeholder="Texto de la respuesta abierta" name="pregunta' + numChildren + '-resp-abierta" type="text" class="form-control resp-abierta">' +
        '</div>' +
        '</div>' +
        '</div>' +

        '<div class="row opciones col-md-12 hideable">' +
        '<div class="col-md-10">' +
        '<div class="form-group col-md-4">' +
        '<a style="cursor: default;" onclick="agregarPregunta(this)" class="btn btn-success">Agregar Pregunta</a>' +
        '</div>' +
        '</div>' +
        '<div class="form-row col-md-2">' +
        '<div class="form-group col-md-1 col-sm-6">' +

        '<button type="button" class="btn btn-danger" onclick="removerPregunta(this)">' +
        '<i class="fa fa-trash"></i>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>'
    );

    $('#preguntas-area-parent').append(nuevaPregunta);
    
    if (storeContenidoT.value != "") {
        addContenidoToSelect(JSON.parse(storeContenidoT.value));
    }
}

function agregarOpcion(opcion) {

    var divParent = $(opcion).parents(':eq(2)').children()[2];
    var idParent = divParent.id
    
    var nuevaOpcion = opcionTemplate(idParent)
    $(divParent).append(nuevaOpcion);
}

function opcionTemplate(idParent) {
    var nuevaOpcion = $(
        '<div class="input-group mb-3">' +
        '<div class="input-group-text">' +
        '<input type="radio" onclick="setAceptedAnswer(this)" name="' + idParent + '-correc[]">' +
        '</div>' +
        '&nbsp;&nbsp;&nbsp;' +
        '<input placeholder="Opcion" type="text" class="form-control resp-abierta multiple" name="' + idParent + '[]" >' +
        '<a class="btn btn-warning btn-circle m-1 btn-circle-sm" onclick="removerOpcion(this)" >' +
        '<i class="fa fa-minus"></i>' +
        '</a>' +
        '</div>'
    );
    return nuevaOpcion
}


function removerPregunta(btnEliminar) {
    var divParent = $(btnEliminar).parents(':eq(4)')[0]; //
    numPreguntas--;
    $('#numPreguntas').val(numPreguntas);
    $(divParent).remove();
    var lastPregunta = $('#preguntas-area-parent').children().last()[0]
    lastPregunta = $(lastPregunta).children().children()[2]
    $(lastPregunta).children().children().children().css('display', 'block')

}


function removerOpcion(opcion) {
    var divParent = $(opcion).parent()[0]
    $(divParent).remove();
}



function cambiarTipoPregunta(opcion) {
    var value = $(opcion).val();
    var divParent = $(opcion).parents(':eq(2)')[0]

    var preguntaAbierta = divParent.children[4]
    var opcionMultiple = divParent.children[2].id


    // if (value == 'abierta') {
    //     $(".opcion-area-p" + idPregunta + "").css('display', 'none');
    //     $(".ra-" + idPregunta).css('display', 'block');
    // } else {
    //     $(".ra-" + idPregunta).css('display', 'none');
    //     $(".opcion-area-p" + idPregunta).css('display', 'block');

    // }
    if (value == 'abierta') {
        $('#' + opcionMultiple).css('display', 'none');
        $(preguntaAbierta).css('display', 'block')
    } else {
        $('#' + opcionMultiple).css('display', 'block');
        $(preguntaAbierta).css('display', 'none')
    }
}


function setAceptedAnswer(checkboxSelected) {
    var divParent = $(checkboxSelected).parents(':eq(2)')[0]; // 
    var opciones = $(divParent).children();
    var input = document.getElementById($(divParent).children()[0].id)
    var abecedario = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];


    for (var i = 0; i < opciones.length; i++) {
        var opcion = opciones[i];

        var checkbox = $(opcion).children().children()[0];

        if (checkbox != checkboxSelected) {
            $(checkbox).prop('checked', false);
        } else {
            if ($(checkboxSelected).prop('checked')) {
                input.value = abecedario[i - 1];
            } else {
                input.value = 0;
            }
        }
    }
}



// var newId = id.replace(/\d+/, i);

/*
 *
 * ==========================================
 * EVAL ALUMNO Y CURSO JS
 * ==========================================
 *
 */


function confirmacion() {
    document.getElementById('creacion').submit();
}






