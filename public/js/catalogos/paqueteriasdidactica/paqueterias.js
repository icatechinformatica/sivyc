var ponderacionTotal = 0;
$(document).ready(function () {
    console.log('paqueterias')

});

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



    criterio.innerText = $('#criterio').val();
    porcentaje.innerText = $('#ponderacion').val();

    trElement.setAttribute("id", 'criterio' + numelement);
    aElement.setAttribute("onclick", 'removerCriterio(' + numelement + ')');

    iElement.classList.add("material-icons");
    iElement.innerHTML = '&#xE872;';

    aElement.appendChild(iElement);
    accionElement.appendChild(aElement);

    trElement.appendChild(criterio);
    trElement.appendChild(porcentaje);
    trElement.appendChild(accionElement);
    tbodyElement.appendChild(trElement);

    $('#criterio').val('');
    $('#ponderacion').val('');


}

function agregarContenidoT() {
    var tbodyElement = document.getElementById('tTemario');
    var numelement = tbodyElement.rows.length;
    var trElement = document.createElement('tr');
    var contenidoT = document.createElement('td');
    var estrategiaD = document.createElement('td');
    var proceso = document.createElement('td');
    var duracion = document.createElement('td');
    var accionElement = document.createElement('td');
    var aElement = document.createElement('a');
    var iElement = document.createElement('i');


    if (!$('#contenidotematico').val() || !$('#estrategiadidactica').val() || !$('#procesoevaluacion').val() || !$('#duracionT').val())
        return


    contenidoT.innerText = $('#contenidotematico').val();
    estrategiaD.innerText = $('#estrategiadidactica').val();
    proceso.innerText = $('#procesoevaluacion').val();
    duracion.innerText = $('#duracionT').val();


    trElement.setAttribute("id", 'contenido' + numelement);
    aElement.setAttribute("onclick", 'removerContenido(' + numelement + ')');

    iElement.classList.add("material-icons");
    iElement.innerHTML = '&#xE872;';

    aElement.appendChild(iElement);
    accionElement.appendChild(aElement);

    trElement.appendChild(contenidoT);
    trElement.appendChild(estrategiaD);
    trElement.appendChild(proceso);
    trElement.appendChild(duracion);
    trElement.appendChild(accionElement);
    tbodyElement.appendChild(trElement);

    $('#contenidotematico').val('');
    $('#estrategiadidactica').val('');
    $('#procesoevaluacion').val('');
    $('#duracionT').val('');
}

function removerContenido(idContenido) {
    var tr = document.getElementById('contenido' + idContenido);
    console.log(tr)
    changeSiblings(tr);
    document.getElementById('tTemario').removeChild(tr);
}

function removerCriterio(idCriterio) {
    var tr = document.getElementById('criterio' + idCriterio);
    changeSiblings(tr);
    document.getElementById('tEvaluacion').removeChild(tr);
}

function changeSiblings(tr) {
    while (tr = tr.nextSibling) {
        var newNum = parseInt(tr.children[0].textContent) - 1;
        tr.children[0].innerHTML = newNum;
    }
}

function cambiarTipoPregunta() {
    var value = $("#tipopregunta option:selected").text();
    if (value == 'Abierta') {
        console.log('hide multiple')
        $(".respuestas-area").css('display', 'none');
        $(".respuesta-abierta-area").css('display', 'block');
    } else {
        $(".respuesta-abierta-area").css('display', 'none');
        $(".respuestas-area").css('display', 'block');

    }
}

function agregarOpcion() {
    var nuevaOpcion = $(
        '<div class="input-group mb-3" id="child-resp0">' +
        '<div class="input-group-prepend">' +
        '<div class="input-group-text">' +
        '<input type="checkbox" aria-label="Checkbox for following text input" id="respuesta0">' +
        '</div>' +
        '</div>' +
        ' <input placeholder="Opcion" type="text" class="form-control" id="opcion0" name="opcion0">' +
        '</div>');
    $('#parent-resp').append(nuevaOpcion);
}


function agregarPregunta() {
    var numChildren = $("#preguntas-area-parent").children()+1;
    var nuevaPregunta = $(
        '<div class="row col-md-12" id = "preguntas-area-children'+numChildren+'" >'+
            '<div class="form-row col-md-7 col-sm-12">'+
                '<div class="form-group col-md-12 col-sm-10">'+
                    '<label for="pregunta0" class="control-label">PREGUNTA</label>'+
                    '<textarea placeholder="pregunta" class="form-control" id="pregunta0" name="pregunta0" cols="15" rows="2"></textarea>'+
                '</div>'+
            '</div>'+

            '<div class="form-row col-md-5 ">'+
                '<div class="form-group col-md-12 col-sm-6">'+
                    '<label for="tipopregunta" class="control-label">TIPO DE PREGUNTA</label>'+
                    '<select onchange="cambiarTipoPregunta()" class="form-control" id="tipopregunta" name="tipopregunta">'+
                        '<option value="multiple" selected>Multiple</option>'+
                        '<option value="abierta">Abierta</option>'+
                    '</select>'+
                '</div>'+
            '</div>'+


            '<div class="form-row col-md-7 respuestas-area" id="parent-resp">'+
                '<div class="input-group mb-3" id="child-resp1">'+
                    '<div class="input-group-prepend">'+
                        '<div class="input-group-text">'+
                            '<input type="checkbox" aria-label="Checkbox for following text input" id="respuesta1-p1">'+
                        '</div>'+
                    '</div>'+
                    '<input placeholder="Opcion" type="text" class="form-control" id="opcion1-p1" name="opcion1-p1">'+
                '</div>'+
            '</div>'+

            '<div class="form-row col-md-6 respuestas-area">'+
                '<div class="input-group mb-3">'+
                    '<a style="cursor: default;" onclick="agregarOpcion()">Agregar opcion</a>'+
                '</div>'+
            '</div>'+
        '<!--se oculta por medio del class-- >'+
        '<div class="form-row col-md-7 respuesta-abierta-area" style="display: none">'+
            '<div class="input-group mb-3">'+
                '<input disabled placeholder="Texto de la respuesta abierta" type="text" class="form-control resp-abierta">'+
            '</div>'+
        '</div>'+
    '</div >');

    $('#preguntas-area-parent').append(nuevaPregunta);    
}


