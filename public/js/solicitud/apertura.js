

$.validator.messages.required = "Dato requerido.";

$("#guardar" ).click(function() {
    $('#frm').validate({
        rules: {
            munidad: { required: true},
            hini: { required: true},
            hfin: { required: true},
            dia: { required: true},
            inicio: { required: true},
            termino: { required: true},
            plantel: { required: true},
            sector: { required: true},
            programa: { required: true},
            efisico: { required: true},
            id_municipio: { required: true},
            depen: { required: true},
            tcurso: { required: true},
            medio_virtual: { required: true},
            link_virtual: { required: true},
            instructor: { required: true},
            tdias: { required: true}
        }
    }); 
} ); 
