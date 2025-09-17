//#region AUTOCOMPLETE_CLIENTES
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('busqueda');
    const suggestionsPanel = document.getElementById('suggestions');
    const codClienteInput = document.getElementById('codcliente');

    searchInput.addEventListener('input', function () {
        const searchQuery = this.value;
        if (searchQuery) {
            fetch(`autocomplete_cliente.php?q=${searchQuery}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsPanel.innerHTML = '';
                    data.forEach(item => {
                        const suggestion = document.createElement('div');
                        const clientInfo = `
                        <strong>${item.nombre}</strong><br>
                    `;
                        suggestion.innerHTML = clientInfo;
                        suggestion.addEventListener('click', () => {
                            // Al hacer clic en una sugerencia, actualizar los campos y ocultar las sugerencias
                            codClienteInput.value = item.idcliente; // Asumiendo que el objeto item tiene un campo 'id'
                            searchInput.value = `${item.nombre}`; // Puedes ajustar esto según tus necesidades
                            suggestionsPanel.innerHTML = '';
                            suggestionsPanel.style.display = 'none';
                        });
                        suggestionsPanel.appendChild(suggestion);
                    });
                    suggestionsPanel.style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        } else {
            suggestionsPanel.innerHTML = '';
            suggestionsPanel.style.display = 'none';
        }
    });

    document.addEventListener('click', function (event) {
        if (!searchInput.contains(event.target) && !suggestionsPanel.contains(event.target)) {
            suggestionsPanel.style.display = 'none';
        }
    });
});
//#endregion
