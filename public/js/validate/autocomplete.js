$(function(){
    //metodo
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#mod-datosBancarios").click(function(e){
        e.preventDefault();
        $.ajax({
            success: function(){
                $('#arch_bancario').prop("disabled", false)
                $('#arch_bancario').prop("required", true)
                $('#nombre_banco').prop("disabled", false)
                $('#nombre_banco').prop("required", true)
                $('#numero_cuenta').prop("disabled", false)
                $('#numero_cuenta').prop("required", true)
                $('#clabe').prop("disabled", false)
                $('#clabe').prop("required", true)
            }
        });
    });
    //autocomplete
    $( "#nombre_director" ).autocomplete({
    source: function( request, response ) {
        console.log(request);
        // Fetch data
        $.ajax({
        url:"/directorio/getdirectorio",
        type: 'post',
        dataType: "json",
        data: {
            search: request.term
        },
        success: function( data ) {
            response( data );
        }
        });
    },
    select: function (event, ui) {
        // Set selection
        $('#nombre_director').val(ui.item.label); // display the selected text
        $('#puesto_director').val(ui.item.charge);
        $('#id_director').val(ui.item.value); // save selected id to input
        return false;
    }
    });

    $( "#testigo1" ).autocomplete({
        source: function( request, response ) {
            console.log(request);
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#testigo1').val(ui.item.label); // display the selected text
            $('#puesto_testigo1').val(ui.item.charge);
            $('#id_testigo1').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#testigo2" ).autocomplete({
        source: function( request, response ) {
            console.log(request);
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#testigo2').val(ui.item.label); // display the selected text
            $('#puesto_testigo2').val(ui.item.charge);
            $('#id_testigo2').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#testigo3" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#testigo3').val(ui.item.label); // display the selected text
            $('#puesto_testigo3').val(ui.item.charge);
            $('#id_testigo3').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $('#destino').autocomplete({
        source: function(request, response) {
            // fetch data
            $.ajax({
                url: '/directorio/getdirectorio',
                type: 'post',
                dataType: "json",
                data: {
                    search: request.term
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        select: function(event, ui) {
            // Set selection
            $('#destino').val(ui.item.label); // display the selected text
            $('#destino_puesto').val(ui.item.charge);
            $('#id_destino').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#remitente" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#remitente').val(ui.item.label); // display the selected text
            $('#remitente_puesto').val(ui.item.charge);
           $('#id_remitente').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#nombre_valida" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#nombre_valida').val(ui.item.label); // display the selected text
            $('#puesto_valida').val(ui.item.charge);
           $('#id_valida').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#nombre_elabora" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#nombre_elabora').val(ui.item.label); // display the selected text
            $('#puesto_elabora').val(ui.item.charge);
           $('#id_elabora').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#nombre_ccp1" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#nombre_ccp1').val(ui.item.label); // display the selected text
            $('#puesto_ccp1').val(ui.item.charge);
           $('#id_ccp1').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#nombre_ccp2" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#nombre_ccp2').val(ui.item.label); // display the selected text
            $('#puesto_ccp2').val(ui.item.charge);
           $('#id_ccp2').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#nombre_firmante" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#nombre_firmante').val(ui.item.label); // display the selected text
            $('#puesto_firmante').val(ui.item.charge);
           $('#id_firmante').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#ccp1" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#ccp1').val(ui.item.label); // display the selected text
            $('#ccpa1').val(ui.item.charge);
           $('#id_ccp1').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#ccp2" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#ccp2').val(ui.item.label); // display the selected text
            $('#ccpa2').val(ui.item.charge);
           $('#id_ccp2').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#ccp3" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#ccp3').val(ui.item.label); // display the selected text
            $('#ccpa3').val(ui.item.charge);
           $('#id_ccp3').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#ccp4" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getdirectorio",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#ccp4').val(ui.item.label); // display the selected text
            $('#ccpa4').val(ui.item.charge);
           $('#id_ccp4').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#cursoaut" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getcurso",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#cursoaut').val(ui.item.label); // display the selected text
           $('#id_curso').val(ui.item.value); // save selected id to input
            return false;
        }
    });

    $( "#instructoraut" ).autocomplete({
        source: function( request, response ) {
            // Fetch data
            $.ajax({
            url:"/directorio/getins",
            type: 'post',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function( data ) {
                response( data );
            }
            });
        },
        select: function (event, ui) {
            // Set selection
            $('#instructoraut').val(ui.item.label); // display the selected text
           $('#id_instructor').val(ui.item.value); // save selected id to input
            return false;
        }
    });


});
