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