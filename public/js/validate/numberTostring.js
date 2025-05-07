$(function() {
    //console.log('aaa')
    inputNumero = $("#inputNumero"),
    botonConvertir = $("#botonConvertir"),
    salida = $("#cantidad_letras");

    // escuchar el click
    $(document).ready(function () {
        // Obtener valor que hay en el input
        valor = parseFloat($("#cantidad_numero").val());
        // Simple validación

        // Obtener la representación
        let letras = numeroLetras(valor, {
            plural: "PESOS",
            singular: "PESO",
            centPlural: "CENTAVOS",
            centSingular: "CENTAVO"
        });

        // Y a la salida ponerle el resultado
        salida.val(letras);

    });
});
