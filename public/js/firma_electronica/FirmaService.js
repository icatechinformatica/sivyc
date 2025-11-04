
// ===============================
// Servicio de Firmado Electrónico
// ===============================

const FirmaService = {
    // Inicia el proceso de firma
    async iniciarFirma(arrayCadenas, curp, password, tokenInicial) {
        try {
            if (!curp || curp.trim() === '') {
                loader('hide');
                return {
                    success: false,
                    errores: 1,
                    correctos: 0,
                    message: "No puedes realizar el proceso de firmado ya que no coinciden tus datos con los documentos a firmar.",
                    respuesta: []
                };
            }

            loader('show');

            let token = tokenInicial;
            let response = await this.firmarDocumento(arrayCadenas, curp, password, token);

            // Si el token expira => generar uno nuevo
            if (response && response[0] && response[0].codeResponse == '401') {
                console.warn("Token inválido, generando uno nuevo...");
                token = await this.generarToken();
                response = await this.firmarDocumento(arrayCadenas, curp, password, token);
            }

            // Retornamos siempre la respuesta procesada
            return this.procesarRespuesta(response, curp);

        } catch (error) {
            console.error("Error en el proceso de firmado:", error);
            loader('hide');
            return {
                success: false,
                errores: 1,
                correctos: 0,
                message: "Ocurrió un error en el proceso de firmado. Inténtalo de nuevo.",
                respuesta: []
            };
        }
    },

    // Firma los documentos con el token dado
    firmarDocumento(arrayCadenas, curp, password, token) {
        return new Promise((resolve, reject) => {
            try {
                const sistema = '87'; // '87' Producción | '17' para pruebas
                const response = sigchains(arrayCadenas, curp, password, sistema, token);
                resolve(response);
            } catch (error) {
                reject(error);
            }
        });
    },

    // Generación de nuevo Token
    generarToken() {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'POST',
                url: "/firma/token", // <- Laravel route
                data: {
                    nombre: '',
                    key: '',
                    _token: $("meta[name='csrf-token']").attr("content"),
                },
                success: function(result) {
                    resolve(result);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error al generar token:", textStatus, errorThrown);
                    reject(errorThrown);
                }
            });
        });
    },

    // Procesa la respuesta del firmado y devuelve objeto
    procesarRespuesta(response, curp) {
        // console.log("Respuesta de firmado:", response);

        if (!response) {
            loader('hide');
            return {
                success: false,
                errores: 1,
                correctos: 0,
                message: "Error al obtener la respuesta del servidor.",
                respuesta: []
            };
        }

        let errores = 0, correctos = 0, message = '';
        let respuesta = [];

        response.forEach((element, index) => {
            if (element.statusResponse) {
                respuesta.push({
                    certificado: element.certificated,
                    no_seriefirmante: element.certifiedSeries,
                    fechafirma: element.date,
                    firma_cadena: element.sign,
                    idCadena: element.idCadena
                });
                correctos++;
            } else {
                errores++;
                message += `Respuesta ${index + 1} | Status: ${element.statusResponse} | Código: ${element.codeResponse} | Mensaje: ${element.descriptionResponse}\n`;
            }
        });

        return {
            success: correctos > 0,
            errores,
            correctos,
            message,
            curp,
            respuesta
        };
    }
};

// Lo exportamos al window para poder usarlo globalmente en Blade
window.FirmaService = FirmaService;
