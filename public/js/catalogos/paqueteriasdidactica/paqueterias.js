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

function addRowContenidoT(row) {

    var contenidoVal = $(row).parents(':eq(1)').children()[0];
    var estrategiaVal = $(row).parents(':eq(1)').children()[1];
    var procesoVal = $(row).parents(':eq(1)').children()[2];
    var duracionVal = $(row).parents(':eq(1)').children()[3];
    var contenidoExtraVal = $(row).parents(':eq(1)').children()[4];

    console.log(contenidoVal, estrategiaVal, procesoVal, duracionVal, contenidoExtraVal)
    console.log($(contenidoVal).children())

    // if(contenidoVal.includes("Click aqui para agregar"))
    //     return
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
    console.log(storeContenidoT.value)
    addContenidoToSelect(JSON.parse(storeContenidoT.value));
    
    idContenido++;

    var tbodyElement = document.getElementById('tTemario');
    var nuevaOpcion = $(
        '<tr id="' + idContenido + '">' +
        '<td data-toggle="modal" data-placement="top" class="temario" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
        ' Click aqui para agregar contenido' +
        '</td>' +
        '<td data-toggle="modal" data-placement="top" data-target="#modalTxtEditor" onclick="showEditorTxtModal(this)">' +
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


function updateContenido(index){
    valContenidoT.forEach(function (item, index, object) {
        if (item.id === (idContenido)) {
            object.splice(index, 1);
        }
    });
    storeContenidoT.value = JSON.stringify(valContenidoT);

    addContenidoToSelect(JSON.parse(storeContenidoT.value))
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
    else if ( $(rowSelected).hasClass('duracion')) $('#duracionValues').val(editorContenidoT.getData())  
    else if ( $(rowSelected).hasClass('contenidoE')) $('#contenidoExtraValues').val(editorContenidoT.getData())  
    
    if ($(rowSelected).hasClass('contenidoT')) 
        rowSelected.innerHTML = '<h1><strong>' + $('#inpTemaPrincipal').val() + '</strong></h1>' + editorContenidoT.getData();
    else
        rowSelected.innerHTML =  editorContenidoT.getData();
    
    var idParent = $(rowSelected).parent()[0].id;
    
    if(valContenidoT.find(x => x.id == idParent)){
        console.log('editar')
    }else{
        console.log('nuevo')
    }
    $('#btnCloseModal').trigger('click');

    console.log(valContenidoT)

}
function showEditorTxtModal(row) {

    if ($(row).hasClass('contenidoT')) {
        $('#temaPrincipal').css('display', 'block')
    } else {
        $('#temaPrincipal').css('display', 'none')
    }
    var txtRow = $(row).html();
    if (txtRow.length > 0) {
        editorContenidoT.data.set(txtRow);
    }
    rowSelected = row;
}

function cerrarModal() {
    var className = $(rowSelected).attr('class');

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

var contPreguntas = 1;
var opcion = 0;
var numPreguntas = 1;


function addContenidoToSelect(contenido) {
    console.log(contenido);
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

function agregarPregunta() {
    var numChildren = contPreguntas + 1;
    numPreguntas++;
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

        '<div class="form-row col-md-6 opcion-area-pregunta' + numChildren + '">' +
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

    var divParent = $(opcion).parents(':eq(2)').children()[2];
    var idParent = divParent.id
    var nuevaOpcion = $(
        '<div class="input-group mb-3">' +
        '<div class="input-group-text">' +
        '<input type="radio" onclick="setAceptedAnswer(this)" name="' + divParent + '-correc[]">' +
        '</div>' +
        '&nbsp;&nbsp;&nbsp;' +
        '<input placeholder="Opcion" type="text" class="form-control resp-abierta multiple" name="' + idParent + '[]" >' +
        '<a class="btn btn-warning btn-circle m-1 btn-circle-sm" onclick="removerOpcion(this)" >' +
        '<i class="fa fa-minus"></i>' +
        '</a>' +
        '</div>'
    );
    $(divParent).append(nuevaOpcion);
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
    console.log(divParent);
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

    console.log(checkboxSelected)
    for (var i = 0; i < opciones.length; i++) {
        var opcion = opciones[i];
        console.log(opcion)
        var checkbox = $(opcion).children().children()[0];
        console.log(checkbox)
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








