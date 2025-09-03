$(document).ready(function () {
    function agregarEventos(tablaContainer) {
        tablaContainer.querySelector(".btnAgregarFila").addEventListener("click", function() {
            let tbody = tablaContainer.querySelector("tbody");
            let columnas = tablaContainer.querySelectorAll("thead th");

            // Crear fila con inputs (excepto la columna Acción)
            let fila = document.createElement("tr");
            columnas.forEach((col, i) => {
                if (col.textContent.trim().toUpperCase() !== "ACCIÓN") {
                    fila.innerHTML += `
                        <td>
                            <input type="text" class="form-control" placeholder="${col.textContent}" required>
                            <div class="invalid-feedback">Este campo es obligatorio.</div>
                        </td>
                    `;
                } else {
                    fila.innerHTML += `
                        <td>
                            <button class="btn btn-success btnGuardar"><i class="fa fa-check"></i></button>
                            <button class="btn btn-danger btnCancelar"><i class="fa fa-times"></i></button>
                        </td>
                    `;
                }
            });
            tbody.appendChild(fila);

            // Guardar
            fila.querySelector(".btnGuardar").addEventListener("click", function() {
                let inputs = fila.querySelectorAll("input");
                let datos = [];
                let valido = true;
                let firstInvalid = null;

                inputs.forEach(input => {
                    input.classList.remove("is-invalid");
                    if (input.value.trim() === "") {
                        input.classList.add("is-invalid");
                        valido = false;
                        if (!firstInvalid) firstInvalid = input;
                    } else {
                        datos.push(input.value.trim());
                    }
                });

                if (!valido) {
                    firstInvalid.focus();
                    return;
                }

                let nuevaFila = document.createElement("tr");
                datos.forEach((dato, index) => {
                    let celda = document.createElement(index === 0 ? "th" : "td");
                    if (index === 0) celda.scope = "row";
                    celda.textContent = dato;
                    nuevaFila.appendChild(celda);
                });

                let tdAccion = document.createElement("td");
                tdAccion.innerHTML = `<button class="btn btn-danger rounded-circle btnEliminar"><i class="fa fa-times text-white"></i></button>`;
                nuevaFila.appendChild(tdAccion);

                fila.replaceWith(nuevaFila);
                agregarEventoEliminar(nuevaFila.querySelector(".btnEliminar"));
            });

            // Cancelar
            fila.querySelector(".btnCancelar").addEventListener("click", function() {
                fila.remove();
            });
        });
    }

    function agregarEventoEliminar(boton) {
        boton.addEventListener("click", function() {
            boton.closest("tr").remove();
        });
    }

    // Inicializar para todas las tablas
    document.querySelectorAll(".tablaEditable").forEach(tabla => {
        agregarEventos(tabla.closest("div"));
        tabla.querySelectorAll(".btnEliminar").forEach(boton => {
            agregarEventoEliminar(boton);
        });
    });

});
