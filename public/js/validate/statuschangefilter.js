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
        }
        else
        {
            $('#divstat').prop("class", "")
        }
      }

});
