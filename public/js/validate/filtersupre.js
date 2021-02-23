//Creado por Orlando Chavez
$(function(){
    //metodo
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

      document.getElementById('tipo_suficiencia').onchange = function() {
        var index = this.selectedIndex;
        var inputText = this.children[index].innerHTML.trim();
        if(inputText == 'N° MEMORANDUM')
        {
            $('#divstat').prop("class", "form-row d-none d-print-none")
        }
        else
        {
            $('#divstat').prop("class", "")
        }
      }

});
