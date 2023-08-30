document.addEventListener('DOMContentLoaded', function () {
    const fileInputs = [
        {
            inputId: 'solpa_pdf',
            outputId: 'solpa_name'
        },
        {
            inputId: 'contratof_pdf',
            outputId: 'contratof_name'
        },
        {
            inputId: 'asistencias_pdf',
            outputId: 'asistencias_name'
        },
        {
            inputId: 'evidencia_fotografica_pdf',
            outputId: 'evidencia_fotografica_name'
        },
        {
            inputId: 'factura_pdf',
            outputId: 'factura_name'
        },
        {
            inputId: 'factura_xml',
            outputId: 'factura_xml_name'
        },
        {
            inputId: 'solpa_pdfc',
            outputId: 'solpa_namec'
        },
        {
            inputId: 'contratof_pdfc',
            outputId: 'contratof_namec'
        },
        {
            inputId: 'calificaciones_pdfc',
            outputId: 'calificaciones_namec'
        },
        {
            inputId: 'factura_pdfc',
            outputId: 'factura_namec'
        },
        {
            inputId: 'factura_xmlc',
            outputId: 'factura_xml_namec'
        }
    ];

    fileInputs.forEach(inputPair => {
        const inputElement = document.getElementById(inputPair.inputId);
        const outputElement = document.getElementById(inputPair.outputId);

        handleFileInputChange(inputElement, outputElement);
    });
});

function handleFileInputChange(inputElement, outputElement) {
    inputElement.addEventListener('change', function () {
        if (this.files[0].name.length > 3) {
            outputElement.textContent = this.files[0].name.slice(0, 3) + "..."

        }
        else {
            outputElement.textContent = this.files[0].name;
        }
    });
}






