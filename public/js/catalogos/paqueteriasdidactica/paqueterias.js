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

function agregarOpcion(pregunta) {
    var divParent = $(pregunta).parents(':eq(2)')[0].id
    console.log(divParent, pregunta)
    // var numChildren = $('#opc-' + divParent).children().length + 1;
    // console.log(numChildren, idPregunta);

    // var nuevaOpcion = $(
    //     '<div class="input-group mb-3 " id="opc-' + numChildren + '-' + idPregunta + '">' +
    //     '<div class="input-group-prepend">' +
    //     '<div class="input-group-text">' +
    //     '<input type="checkbox" onclick="setAceptedAnswer(this)" id="resp-' + numChildren + '-' + idPregunta + '">' +
    //     '</div>' +
    //     '</div>' +
    //     '&nbsp;&nbsp;&nbsp;' +
    //     '<input placeholder="Opcion" type="text" class="form-control resp-abierta" id="opcion-' + numChildren + '-' + idPregunta + '" name="opcion-' + numChildren + '-' + idPregunta + '">' +
    //     '<a class="btn btn-warning btn-circle m-1 btn-circle-sm" onclick="removerPregunta(\'opc-' + numChildren + '-' + idPregunta + '\')">' +
    //     '<i class="fa fa-minus"></i>' +
    //     '</a>' +
    //     '</div>'
    // );
    // $('#opc-' + idPregunta).append(nuevaOpcion);
}


function agregarPregunta() {
    var numChildren = $("#preguntas-area-parent").children().length + 1;
    var opcion = `p${numChildren}`;

    var nuevaPregunta = $(
        '<div class="row col-md-12" id = "pregunta' + numChildren + '" >' +
        '<div class="form-row col-md-7 col-sm-12">' +
        '<div class="form-group col-md-12 col-sm-10">' +
        '<label for="pregunta0" class="control-label">PREGUNTA</label>' +
        '<textarea placeholder="pregunta" class="form-control" id="p' + numChildren + '" name="p' + numChildren + '" cols="15" rows="2"></textarea>' +
        '</div>' +
        '</div>' +

        '<div class="form-row col-md-5 ">' +
        '<div class="form-group col-md-12 col-sm-6">' +
        '<label for="tipopregunta" class="control-label">TIPO DE PREGUNTA</label>' +
        '<select onchange="cambiarTipoPregunta(\'p' + numChildren + '\')" class="form-control" id="tipopregunta-p' + numChildren + '" name="tipopregunta-p' + numChildren + '">' +
        '<option value="multiple" selected>Multiple</option>' +
        '<option value="abierta">Abierta</option>' +
        '</select>' +
        '</div>' +
        '</div>' +


        '<div class="form-row col-md-7 opcion-area-p' + numChildren + '" id="opc-p' + numChildren + '">' +
        '<div class="input-group mb-3" id="opc-1-p' + numChildren + '">' +
        '<div class="input-group-prepend">' +
        '<div class="input-group-text">' +
        '<input type="checkbox" aria-label="Checkbox for following text input" id="resp-1-p' + numChildren + '">' +
        '</div>' +
        '</div>' +
        '&nbsp;&nbsp;&nbsp;' +
        '<input placeholder="Opcion" type="text" class="form-control resp-abierta" id="opcion1-p' + numChildren + '" name="opcion-1-p' + numChildren + '">' +
        '<a class="btn btn-warning btn-circle m-1 btn-circle-sm" >' +
        '<i class="fa fa-minus"></i>' +
        '</a>' +
        '</div>' +
        '</div>' +
        

        '<div class="form-row col-md-6 opcion-area-p' + numChildren + '">' +
        '<div class="input-group mb-3">' +
        '<a style="cursor: default;" onclick="agregarOpcion(\'p' + numChildren + '\')">Agregar opcion</a>' +
        '</div>' +
        '</div>' +

        '<div class="form-row col-md-7 respuesta-abierta-area ra-p' + numChildren + '" style="display: none">' +
        '<div class="input-group mb-3">' +
        '<input disabled placeholder="Texto de la respuesta abierta" type="text" class="form-control resp-abierta">' +
        '</div>' +
        '</div>' +
        '</div >');

    $('#preguntas-area-parent').append(nuevaPregunta);
}


function removerOpcion(idOpcion){
    
    var divParent = $(idOpcion).parents(':eq(1)')[0].id
    
    // var divParent = $('#'+idOpcion).parent().attr('id');
    $('#' + divParent).remove();

    // fixId(divParent)
}

function fixId(div){
    var numChildren = $('#'+div).children().length;
    for(var i = 1; i <= numChildren; i++){
        var id = $('#'+div).children().eq(i-1).attr('id');
        var newId = id.replace(/\d+/, i);
        $('#'+div).children().eq(i-1).attr('id', newId);
    }
}

function setAceptedAnswer(opcion) {
    console.log(opcion);
}


/*
 *
 * ==========================================
 * EVAL ALUMNO Y CURSO JS
 * ==========================================
 *
 */