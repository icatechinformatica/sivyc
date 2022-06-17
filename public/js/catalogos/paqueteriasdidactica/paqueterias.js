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
                    $("#especialidad").val(nombre);
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

    if (numelement > 4 || !$('#criterio').val() || !$('#ponderacion').val() || parseInt($('#ponderacion').val()) + ponderacionTotal > 100 || parseInt($('#ponderacion').val()) < 0 || parseInt($('#ponderacion').val()) == 0)
        return

    // tbodyElement.appendChild(trElement);
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

function addRowContenidoT() {
    var tbodyElement = document.getElementById('tTemario');

    var nuevaOpcion = $(
        '<tr id="contenido-'+idContenido+'">'+
            '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">'+
               ' Click aqui para agregar contenido'+
            '</td>'+
            '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">'+
               ' Click aqui para agregar contenido'+
            '</td>'+
            '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">'+
               ' Click aqui para agregar contenido'+
            '</td>'+
            '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">'+
               ' Click aqui para agregar contenido'+
            '</td>'+
            '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">'+
               ' Click aqui para agregar contenido'+
            '</td>'+
            '<td>'+
                '<button type="button" class="btn btn-danger remove-tr">Eliminar</button>'+
                '<button type="button" class="btn btn-success" onclick="addRowContenidoT()">Agregar</button>'+
            '</td>'+
        '</tr>'
    );
    $('#' + divParent).append(nuevaOpcion);

    idContenido++;
}

var getU = (idCurso) => {
    var myModalLabel = $("#titleModal");
}

function setValuesEditor() {
    rowSelected.innerHTML = editorContenidoE.getData();
}
function showEditorTxtModal(row) {
    var tr_parent = $(row).parent()[0].id
    
    var numRow = $(row).closest('tr')[0].id;
    var txtRow = $(row).html();
    if (txtRow.length > 0) {
        editorContenidoE.data.set(txtRow);
    }
    console.log(tr_parent)
    console.log("numRow: " + numRow);
    console.log(txtRow);
    rowSelected = row;
}

function agregarRecursosD() {

    valRecursosD.push({
        'id': idRecursos,
        // 'elementoapoyo': editorElementoA.getData(),
        // 'auxenseñanza': editorAuxE.getData(),
        // 'referencias': editorReferencias.getData(),
    });

    storeRecursosD.value = JSON.stringify(valRecursosD);

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

var contPreguntas = 1;
var opcion = 0;
var numPreguntas = 1;


function addContenidoToSelect(contenido) {
    $('.contenidoTematicoPregunta')
        .find('option')
        .remove()
        ;
    for (var i = 0; i < contenido.length; i++) {
        var temas = contenido[i].contenido;

        var contenidoT = temas.substring(
            temas.indexOf("<strong>") + 1,
            temas.lastIndexOf("</strong>")
        );
        contenidoT = contenidoT.replace('strong>', "");
        $('.contenidoTematicoPregunta').append($('<option>', {
            value: contenidoT,
            text: contenidoT
        }));
    }

}

function agregarPregunta() {
    var numChildren = contPreguntas + 1;
    numPreguntas++;
    $('#numPreguntas').val(numPreguntas);

    var nuevaPregunta = $(
        '<div class="card col-md-12">' +
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
        '<input placeholder="Pregunta sin texto" type="text" class="form-control resp-abierta g-input">' +
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

        '<div class="form-row col-md-6 opcion-area-pregunta' + numChildren + '">' +
        '<div class="input-group mb-3">' +
        '<a style="cursor: default;" onclick="agregarOpcion(this)">Agregar opcion</a>' +
        '</div>' +
        '</div>' +

        '<div class="form-row col-md-7 respuesta-abierta-area ra-p' + numChildren + '" style="display: none">' +
        '<div class="input-group mb-3">' +
        '<input disabled placeholder="Texto de la respuesta abierta" type="text" class="form-control resp-abierta">' +
        '</div>' +
        '</div>' +
        '</div>' +

        '<div class="row opciones col-md-12">' +
        '<div class="col-md-10">' +
        '<div class="form-group col-md-4">' +
        '<a style="cursor: default;" onclick="agregarPregunta()" class="btn btn-success">Agregar Pregunta</a>' +
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
    contPreguntas++
    if (storeContenidoT.value != "") {
        addContenidoToSelect(JSON.parse(storeContenidoT.value));
    }
}

function agregarOpcion(opcion) {
    var divParent = $('#' + idParent).children()[3].id;
    var numChildren = $('#' + divParent).children().length + 1;

    var nuevaOpcion = $(
        '<div class="input-group mb-3">' +
        '<div class="input-group-prepend">' +
        '<div class="input-group-text">' +
        '<input type="checkbox" onclick="setAceptedAnswer(this)" name="' + divParent + '-correc[]">' +
        '</div>' +
        '</div>' +
        '&nbsp;&nbsp;&nbsp;' +
        '<input placeholder="Opcion" type="text" class="form-control resp-abierta multiple" name="' + idParent + '-opc[]" >' +
        '<a class="btn btn-warning btn-circle m-1 btn-circle-sm" onclick="removerOpcion(this)" >' +
        '<i class="fa fa-minus"></i>' +
        '</a>' +
        '</div>'
    );
    $('#' + divParent).append(nuevaOpcion);
}


function removerPregunta(pregunta) {
    var divParent = $(pregunta).parents(':eq(4)')[0]; // 
    numPreguntas--;
    $('#numPreguntas').val(numPreguntas);
    $(divParent).remove();
}


function removerOpcion(opcion) {
    var divParent = $(opcion).parent()[0]
    $(divParent).remove();
}



function cambiarTipoPregunta(opcion) {
    var value = $(opcion).val();
    var divParent = $(opcion).parents(':eq(2)')[0]
    var preguntaAbierta = divParent.children[5]
    var opcionMultiple = divParent.children[3].id


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
    var divParent = $(checkboxSelected).parents(':eq(3)')[0]; // 
    var opciones = $(divParent).children();
    var input = document.getElementById($(divParent).children()[0].id)
    var abecedario = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    for (var i = 0; i < opciones.length; i++) {
        var opcion = opciones[i];
        var checkbox = $(opcion).children().children().children()[0];
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








