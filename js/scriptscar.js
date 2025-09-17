let cart = [];
const cartItems = document.getElementById('cart-items');
const subtotalElement = document.getElementById('subtotal');
const igvElement = document.getElementById('igv');
const totalElement = document.getElementById('total');

function addToCart(codproducto, descripcion, precio, cantidad = 1) {
    const existingProduct = cart.find(item => item.codproducto === codproducto);
    if (existingProduct) {
        existingProduct.cantidad = parseFloat((parseFloat(existingProduct.cantidad) + parseFloat(cantidad)).toFixed(2));
    } else {
        cart.push({ 
            codproducto, 
            descripcion, 
            precio: parseFloat(precio), 
            cantidad: parseFloat(cantidad).toFixed(2) 
        });
    }
    renderCart();
}

function removeFromCart(codproducto) {
    cart = cart.filter(item => item.codproducto !== codproducto);
    renderCart();
}

function updatecantidad(codproducto, change) {
    const product = cart.find(item => item.codproducto === codproducto);
    if (product) {
        let nuevaCantidad = parseFloat(product.cantidad) + parseFloat(change);
        if (nuevaCantidad <= 0) nuevaCantidad = 0.1;
        product.cantidad = parseFloat(nuevaCantidad.toFixed(2));
        renderCart();
    }
}

function handleManualChangeCantidad(input, codproducto) {
    const value = parseFloat(input.value);
    const product = cart.find(item => item.codproducto === codproducto);
    if (product) {
        if (!isNaN(value) && value > 0) {
            product.cantidad = parseFloat(value.toFixed(2));
            renderCart();
        } else {
            input.value = parseFloat(product.cantidad).toFixed(2);
        }
    }
}

function handleManualChangePrecio(input, codproducto) {
   const value = parseFloat(input.value);
    const product = cart.find(item => item.codproducto === codproducto);
    if (product) {
        if (!isNaN(value) && value >= 0) {
            product.precio = parseFloat(value.toFixed(2));
            input.value = product.precio.toFixed(2); // Asegura que el input también refleje el nuevo valor formateado
            renderCart();
        } else {
            input.value = product.precio.toFixed(2); // Restaura si es inválido
        }
    }
}

function renderCart() {
    cartItems.innerHTML = '';
    let subtotal = 0;

    cart.forEach((item, index) => {
        const cantidad = parseFloat(item.cantidad);
        const precio = parseFloat(item.precio);
        const total = cantidad * precio;
        subtotal += total;

        cartItems.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.descripcion}</td>
                <td hidden>${item.codBarra || ''}</td>
                <td>
                    <div class="input-group" style="max-width: 100px;">
                       
                        <input type="number" step="0.01" min="0.1" class="form-control" value="${cantidad.toFixed(2)}" onchange="handleManualChangeCantidad(this, ${item.codproducto})">
                    
                    </div>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control" value="${precio.toFixed(2)}" onchange="handleManualChangePrecio(this, ${item.codproducto})" style="max-width: 100px;">
                </td>
                <td>${total.toFixed(2)}</td>
                <td><button class="btn btn-danger" onclick="removeFromCart(${item.codproducto})">X</button></td>
            </tr>
        `;
    });

    const sbTotal = subtotal / 1.13;
    const igv = sbTotal * 0.13;
    const total = sbTotal + igv;

    subtotalElement.textContent = sbTotal.toFixed(2);
    igvElement.textContent = igv.toFixed(2);
    totalElement.textContent = total.toFixed(2);
}

// Bloquea Enter dentro de campos numéricos
document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter' && document.activeElement.type === 'number') {
        event.preventDefault();
    }
});

document.getElementById('saveventa').addEventListener('submit', function(event) {
    event.preventDefault();
    start_load();

    const data = {
        subtotal: $("#subtotal").text(),
        iva_impuesto: $("#igv").text(),
        totalpagar: $("#total").text(),
        codcliente: $("#codcliente").val(),
        prefix: $("#prefix").val(),
        forma_pago: $("#forma_pago").val(),
        csrf_token: $("#csrf_token").val(),
        detalle: JSON.stringify(cart)
    };

    $.ajax({
        url: 'ajax.php?action=save_venta_previa',
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

document.getElementById('busquedaproductov').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const codigo = this.value.trim();

        if (codigo !== "") {
            fetch('buscar_productoC.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'codigo=' + encodeURIComponent(codigo)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const producto = data.producto;
                    addToCart(producto.codproducto, producto.descripcion, parseFloat(producto.precio));
                    this.value = ""; // limpiar campo
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Producto no encontrado',
                        text: data.message || 'No se encontró el producto con ese código.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            })
            .catch(err => {
                console.error("Error al buscar producto:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al buscar el producto.',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    }
});
