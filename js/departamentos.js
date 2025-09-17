function cargarMunicipios(seleccionado = null) {
    const departamentoSelect = document.getElementById("departamento");
    const municipioSelect = document.getElementById("municipio");
    const id_departamento = departamentoSelect.value;

    if (!id_departamento || id_departamento === "0") {
        municipioSelect.innerHTML = '<option value="0">-- SELECCIONE MUNICIPIO --</option>';
        return;
    }

    fetch('cargar_municipios.php?id_departamento=' + id_departamento)
        .then(response => response.json())
        .then(data => {
            municipioSelect.innerHTML = '<option value="0">-- SELECCIONE MUNICIPIO --</option>';
            data.forEach(municipio => {
                const option = document.createElement("option");
                option.value = municipio.codigo;
                option.textContent = municipio.valor;
                if (seleccionado && municipio.codigo == seleccionado) {
                    option.selected = true;
                }
                municipioSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar municipios:', error));
}

// Ejecutar al cargar la página en modo edición
document.addEventListener('DOMContentLoaded', function() {
    const depSelect = document.getElementById("departamento");
    const municipioSeleccionado = depSelect.getAttribute("data-municipio");

    if (depSelect.value !== "0") {
        cargarMunicipios(municipioSeleccionado);
    }
});