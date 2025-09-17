
document.getElementById('saveventa').addEventListener('submit', function(event) {
    event.preventDefault();
    start_load();
    const data = {
        idfacturaV: $("#idfacturaventa").val()
    };

    $.ajax({
        url: 'ajax.php?action=obtenerFactura',
        method: 'POST',
        data: data,
        success: function(resp) {
            let response = JSON.parse(resp);
            let idfactura = response.idfactura;
            if (response.success) {
                uni_modal_generador("Cobrar venta", "ventas.php?idfactura=" + idfactura);
                end_load();
            }
        }
    });
});