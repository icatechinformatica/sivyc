var ponderacionTotal = 0;
   /*
    *
    * ==========================================
    * CURSO JS
    * ==========================================
    *
    */
    

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
    trElement.appendChild(contenidoExtra);
    trElement.appendChild(accionElement);
    tbodyElement.appendChild(trElement);

    $('#contenidotematico').val('');
    $('#estrategiadidactica').val('');
    $('#procesoevaluacion').val('');
    $('#duracionT').val('');
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


    if (!$('#elementoapoyo').val() || !$('#auxenseñanza').val() || !$('#referencias').val() )
        return


    elementoApoyo.innerText = $('#elementoapoyo').val();
    auxEnseñanza.innerText = $('#auxenseñanza').val();
    referencias.innerText = $('#referencias').val();


    trElement.setAttribute("id", 'recurso' + numelement);
    aElement.setAttribute("onclick", 'removerRecurso(' + numelement + ')');

    iElement.classList.add("material-icons");
    iElement.innerHTML = '&#xE872;';

    aElement.appendChild(iElement);
    accionElement.appendChild(aElement);

    trElement.appendChild(elementoApoyo);
    trElement.appendChild(auxEnseñanza);
    trElement.appendChild(referencias);
    trElement.appendChild(accionElement);
    tbodyElement.appendChild(trElement);

    $('#elementoapoyo').val('');
    $('#auxenseñanza').val('');
    $('#referencias').val('');
    
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
function removerRecurso(idRecurso) {
    var tr = document.getElementById('recurso' + idRecurso);
    changeSiblings(tr);
    document.getElementById('tRecursosD').removeChild(tr);
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
    
function cambiarTipoPregunta(idPregunta) {
    var value = $("#tipopregunta-"+idPregunta+" option:selected").text();
    
    if (value == 'Abierta') {
        console.log('hide multiple')
        $(".opcion-area-"+idPregunta+"").css('display', 'none');
        $(".ra-"+idPregunta).css('display', 'block');
    } else {
        $(".ra-"+idPregunta).css('display', 'none');
        $(".opcion-area-"+idPregunta).css('display', 'block');

    }
}

function agregarOpcion(idPregunta) {
    var numChildren = $('#opc-p'+idPregunta).children().length + 1;

    var nuevaOpcion = $(
        '<div class="input-group mb-3 " id="opc-'+numChildren+'-'+idPregunta+'">' +
        '<div class="input-group-prepend">' +
        '<div class="input-group-text">' +
        '<input type="checkbox" aria-label="Checkbox for following text input" id="resp-'+numChildren+'-'+idPregunta+'">' +
        '</div>' +
        '</div>' +
        ' <input placeholder="Opcion" type="text" class="form-control" id="opcion-'+numChildren+'-'+idPregunta+'" name="opcion-'+numChildren+'-'+idPregunta+'">' +
        '</div>');
    $('#opc-'+idPregunta).append(nuevaOpcion);
}


function agregarPregunta() {
    var numChildren = $("#preguntas-area-parent").children().length + 1;
    var opcion = `p${numChildren}`;
    
    var nuevaPregunta = $(
        '<div class="row col-md-12" id = "pregunta' + numChildren + '" >' +
        '<div class="form-row col-md-7 col-sm-12">' +
        '<div class="form-group col-md-12 col-sm-10">' +
        '<label for="pregunta0" class="control-label">PREGUNTA</label>' +
        '<textarea placeholder="pregunta" class="form-control" id="p'+numChildren+'" name="p'+numChildren+'" cols="15" rows="2"></textarea>' +
        '</div>' +
        '</div>' +

        '<div class="form-row col-md-5 ">' +
        '<div class="form-group col-md-12 col-sm-6">' +
        '<label for="tipopregunta" class="control-label">TIPO DE PREGUNTA</label>' +
        '<select onchange="cambiarTipoPregunta(\'p'+numChildren+'\')" class="form-control" id="tipopregunta-p'+numChildren+'" name="tipopregunta-p'+numChildren+'">' +
        '<option value="multiple" selected>Multiple</option>' +
        '<option value="abierta">Abierta</option>' +
        '</select>' +
        '</div>' +
        '</div>' +


        '<div class="form-row col-md-7 opcion-area-p'+numChildren+'" id="opc-p'+numChildren+'">' +
        '<div class="input-group mb-3" id="opc-1-p'+numChildren+'">' +
        '<div class="input-group-prepend">' +
        '<div class="input-group-text">' +
        '<input type="checkbox" aria-label="Checkbox for following text input" id="resp-1-p'+numChildren+'">' +
        '</div>' +
        '</div>' +
        '<input placeholder="Opcion" type="text" class="form-control" id="opcion1-p'+numChildren+'" name="opcion-1-p'+numChildren+'">' +
        '</div>' +
        '</div>' +

        '<div class="form-row col-md-6 opcion-area-p'+numChildren+'">' +
        '<div class="input-group mb-3">' +
        '<a style="cursor: default;" onclick="agregarOpcion(\'p'+numChildren+'\')">Agregar opcion</a>' +
        '</div>' +
        '</div>' +
        
        '<div class="form-row col-md-7 respuesta-abierta-area ra-p'+numChildren+'" style="display: none">'+
        '<div class="input-group mb-3">' +
        '<input disabled placeholder="Texto de la respuesta abierta" type="text" class="form-control resp-abierta">' +
        '</div>' +
        '</div>' +
        '</div >');

    $('#preguntas-area-parent').append(nuevaPregunta);
}


