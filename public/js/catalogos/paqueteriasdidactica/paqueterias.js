var ponderacionTotal = 0;
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

var idContenido = 0;
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
    }else {        
        $("#searchResult").empty();
    }
}

function agregarponderacion() {
    var tbodyElement = document.getElementById('tEvaluacion');
    var numelement = tbodyElement.rows.length;

    var trElement = document.createElement('tr');
    var criterio = document.createElement('td');
    var porcentaje = document.createElement('td');
    var accionElement = document.createElement('td');
    var aElement = document.createElement('a');
    var iElement = document.createElement('i');


    if (numelement > 4 || !$('#criterio').val() || !$('#ponderacion').val() || parseInt($('#ponderacion').val()) + ponderacionTotal > 100 || parseInt($('#ponderacion').val()) < 0 || parseInt($('#ponderacion').val()) == 0)
        return

    ponderacionTotal += parseInt($('#ponderacion').val());
    console.log(ponderacionTotal);



    criterio.innerText = $('#criterio').val();
    porcentaje.innerText = $('#ponderacion').val();

    trElement.setAttribute("id", 'criterio' + idPonderacion);
    aElement.setAttribute("onclick", 'removerCriterio(' + idPonderacion + ')');

    iElement.classList.add("material-icons");
    iElement.innerHTML = '&#xE872;';

    aElement.appendChild(iElement);
    accionElement.appendChild(aElement);

    trElement.appendChild(criterio);
    trElement.appendChild(porcentaje);
    trElement.appendChild(accionElement);
    tbodyElement.appendChild(trElement);


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

function agregarContenidoT() {
    var tbodyElement = document.getElementById('tTemario');

    var trElement = document.createElement('tr');
    var contenidoT = document.createElement('td');
    var estrategiaD = document.createElement('td');
    var proceso = document.createElement('td');
    var duracion = document.createElement('td');
    var contenidoExtra = document.createElement('td');
    var accionElement = document.createElement('td');
    var aElement = document.createElement('a');
    var iElement = document.createElement('i');


    if (!$('#contenidotematico').val() || !$('#estrategiadidactica').val() || !$('#procesoevaluacion').val() || !$('#duracionT').val())
        return


    contenidoT.innerText = $('#contenidotematico').val();
    estrategiaD.innerText = $('#estrategiadidactica').val();
    proceso.innerText = $('#procesoevaluacion').val();
    duracion.innerText = $('#duracionT').val();
    contenidoExtra.innerText = $('#contenidoExtra').val();


    trElement.setAttribute("id", 'contenido' + idContenido);
    aElement.setAttribute("onclick", 'removerContenido(' + idContenido + ')');

    iElement.classList.add("material-icons");
    iElement.innerHTML = '&#xE872;';

    aElement.appendChild(iElement);
    accionElement.appendChild(aElement);

    trElement.appendChild(contenidoT);
    trElement.appendChild(estrategiaD);
    trElement.appendChild(proceso);
    trElement.appendChild(duracion);
    trElement.appendChild(contenidoExtra);
    trElement.appendChild(accionElement);
    tbodyElement.appendChild(trElement);


    valContenidoT.push({
        'id': idContenido,
        'contenido': $('#contenidotematico').val(),
        'estrategia': $('#estrategiadidactica').val(),
        'proceso': $('#procesoevaluacion').val(),
        'duracion': $('#duracionT').val(),
        'contenidoExtra': $('#contenidoExtra').val(),
    });


    storeContenidoT.value = JSON.stringify(valContenidoT);

    $('#contenidotematico').val('');
    $('#estrategiadidactica').val('');
    $('#procesoevaluacion').val('');
    $('#duracionT').val('');
    $('#contenidoExtra').val('');

    idContenido++;
}
function agregarRecursosD() {
    var tbodyElement = document.getElementById('tRecursosD');
    var numelement = tbodyElement.rows.length;
    var trElement = document.createElement('tr');
    var elementoApoyo = document.createElement('td');
    var auxEnseñanza = document.createElement('td');
    var referencias = document.createElement('td');
    var accionElement = document.createElement('td');
    var aElement = document.createElement('a');
    var iElement = document.createElement('i');


    if (!$('#elementoapoyo').val() || !$('#auxenseñanza').val() || !$('#referencias').val())
        return


    elementoApoyo.innerText = $('#elementoapoyo').val();
    auxEnseñanza.innerText = $('#auxenseñanza').val();
    referencias.innerText = $('#referencias').val();


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
        'elementoapoyo': $('#elementoapoyo').val(),
        'auxenseñanza': $('#auxenseñanza').val(),
        'referencias': $('#referencias').val(),
    });

    storeRecursosD.value = JSON.stringify(valRecursosD);

    $('#elementoapoyo').val('');
    $('#auxenseñanza').val('');
    $('#referencias').val('');

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
        console.log(tr.children[0])
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



function agregarPregunta() {
    var numChildren = contPreguntas + 1;

    var nuevaPregunta = $(
        '<div class="row col-md-12" id = "pregunta' + numChildren + '" >' +
        '<div class="form-row col-md-7 col-sm-12">' +
        '<div class="form-group col-md-12 col-sm-10">' +
        '<label for="pregunta0" class="control-label">PREGUNTA</label>' +
        '<textarea placeholder="pregunta" class="form-control" name="pregunta' + numChildren + '" cols="15" rows="2"></textarea>' +
        '</div>' +
        '</div>' +

        '<div class="form-row col-md-4 col-sm-6 ">' +
        '<div class="form-group col-md-12 col-sm-12">' +
        '<label for="tipopregunta" class="control-label">TIPO DE PREGUNTA</label>' +
        '<select onchange="cambiarTipoPregunta(this)" class="form-control" name="pregunta' + numChildren + '-tipo">' +
        '<option value="multiple" selected>Multiple</option>' +
        '<option value="abierta">Abierta</option>' +
        '</select>' +
        '</div>' +
        '</div>' +


        '<div class="form-row col-md-1 col-sm-6">' +
        '<div class="form-group col-md-1 col-sm-12>' +
        '<label for="">Eliminar pregunta</label>' +
        '<button type="button" class="btn btn-danger" onclick="removerPregunta(this)">' +
        '<i class="fa fa-trash"></i>' +
        '</div>' +
        '</div> ' +


        '<div class="form-row col-md-7 opcion-area-p' + numChildren + '" id="pregunta' + numChildren + '-opc">' +
        '<div class="input-group mb-3">' +
        '<div class="input-group-prepend">' +
        '<div class="input-group-text">' +
        '<input type="checkbox" onclick="setAceptedAnswer(this)" >' +
        '</div>' +
        '</div>' +
        '&nbsp;&nbsp;&nbsp;' +
        '<input placeholder="Opcion" type="text" class="form-control resp-abierta" name="pregunta' + numChildren + '-opc[]">' +
        '<a class="btn btn-warning btn-circle m-1 btn-circle-sm" onclick="removerOpcion(this)" >' +
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
        '</div >');

    $('#preguntas-area-parent').append(nuevaPregunta);
    contPreguntas++
}


function agregarOpcion(opcion) {
    var idParent = $(opcion).parents(':eq(2)')[0].id; // 
    // console.log(idParent);
    var divParent = $('#' + idParent).children()[3].id;
    var numChildren = $('#' + divParent).children().length + 1;

    var nuevaOpcion = $(
        '<div class="input-group mb-3">' +
        '<div class="input-group-prepend">' +
        '<div class="input-group-text">' +
        '<input type="checkbox" onclick="setAceptedAnswer(this)" >' +
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

    var idParent = $(pregunta).parents(':eq(2)')[0].id; // 
    var divMain = $('#' + idParent).parent().attr('id');

    $('#' + idParent).remove();

    fixAllIds(divMain)
}


function removerOpcion(opcion) {
    var divParent = $(opcion).parent()[0]
    $(divParent).remove();
}



function cambiarTipoPregunta(idPregunta) {
    var value = $("#tipopregunta-" + idPregunta + " option:selected").text();

    if (value == 'Abierta') {
        console.log('hide multiple')
        $(".opcion-area-" + idPregunta + "").css('display', 'none');
        $(".ra-" + idPregunta).css('display', 'block');
    } else {
        $(".ra-" + idPregunta).css('display', 'none');
        $(".opcion-area-" + idPregunta).css('display', 'block');

    }
}

function fixAllIds(divMain) {

    // pregunta1-opc
    // paqueterias.js:342 

    fixId(divMain)
}

function fixIdDigits(div) {
    var numChildren = $('#' + div).children().length;
    for (var i = 1; i <= numChildren; i++) {
        var id = $('#' + div).children().eq(i - 1).attr('id');
        var newId = id.replace(/\d+/, i);
        $('#' + div).children().eq(i - 1).attr('id', newId);
    }
}

function fixId(div) {
    var numChildren = $('#' + div).children().length;
    for (var i = 1; i <= numChildren; i++) {
        var id = $('#' + div).children().eq(i - 1).attr('id');
        var newId = id.replace(/.$/, i);
        $('#' + div).children().eq(i - 1).attr('id', newId);
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








