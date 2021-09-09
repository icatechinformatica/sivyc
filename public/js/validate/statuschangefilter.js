//Creado por Orlando Chavez
$(function(){
    //metodo
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    document.getElementById('tipo_contrato').onchange = function() {
        var index = this.selectedIndex;
        var inputText = this.children[index].innerHTML.trim();
        if(inputText == 'FOLIO DE VALIDACIÓN')
        {
            $('#divstat').prop("class", "form-row d-none d-print-none")
            $('#divmes').prop("class", "form-row d-none d-print-none")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "")
        }
        else
        {
            $('#divstat').prop("class", "")
        }
        if(inputText == 'UNIDAD CAPACITACIÓN')
        {
            console.log('hola');
            $('#divunidades').prop("class", "")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
            $('#divmes').prop("class", "form-row d-none d-print-none")
        }
        if(inputText == 'MES')
        {
            $('#divmes').prop("class", "")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
        }
        if(inputText == 'FECHA')
        {
            $('#divstat').prop("class", "")
            $('#divmes').prop("class", "form-row d-none d-print-none")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "")
        }
        if(inputText == 'N° MEMORANDUM')
        {
            $('#divstat').prop("class", "")
            $('#divmes').prop("class", "form-row d-none d-print-none")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "")
        }
      }

});
