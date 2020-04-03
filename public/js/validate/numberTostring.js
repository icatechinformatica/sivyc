$(function() {
    inputNumero = $("#inputNumero"),
    botonConvertir = $("#botonConvertir"),
    salida = $("#salida");

    // escuchar el click
    $("#inputNumero").keyup(function () {
        // Obtener valor que hay en el input
        valor = parseFloat($("#inputNumero").val());
        // Simple validación
        if (!valor) return alert("Escribe un valor");

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
