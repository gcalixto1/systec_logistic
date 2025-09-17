let cart = [];
const cartItems = document.getElementById('cart-items');
const subtotalElement = document.getElementById('subtotal');
const igvElement = document.getElementById('igv');
const totalElement = document.getElementById('total');

function addToCart(codproducto, descripcion, precio) {
    const existingProduct = cart.find(item => item.codproducto === codproducto);
    if (existingProduct) {
        existingProduct.cantidad++;
    } else {
        cart.push({ codproducto, descripcion, precio, cantidad: 1 });
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
        product.cantidad += change;
        if (product.cantidad <= 0) {
            removeFromCart(codproducto);
        } else {
            renderCart();
        }
    }
} 

function handleManualChangeCantidad(input, codproducto) {
    const value = parseInt(input.value);
    const product = cart.find(item => item.codproducto === codproducto);
    if (product) {
        if (!isNaN(value) && value > 0) {
            product.cantidad = value;
            renderCart();
        } else {
            removeFromCart(codproducto);
        }
    }
}

function handleManualChangePrecio(input, codproducto) {
    const value = parseFloat(input.value);
    const product = cart.find(item => item.codproducto === codproducto);
    if (product && !isNaN(value) && value >= 0) {
        product.precio = value;
        renderCart();
    }
}

function renderCart() {
    cartItems.innerHTML = '';
    let subtotal = 0;

    cart.forEach((item, index) => {
        const total = item.precio * item.cantidad;
        subtotal += total;

        cartItems.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.descripcion}</td>
                <td hidden>${item.codBarra || ''}</td>
                <td>
                    <div class="input-group" style="max-width: 130px;">
                        <div class="input-group-prepend" style="cursor: pointer;" onclick="updatecantidad(${item.codproducto}, -1)">
                            <span class="input-group-text">-</span>
                        </div>
                        <input type="number" class="form-control" value="${item.cantidad}" onchange="handleManualChangeCantidad(this, ${item.codproducto})">
                        <div class="input-group-append" style="cursor: pointer;" onclick="updatecantidad(${item.codproducto}, 1)">
                            <span class="input-group-text">+</span>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control" value="${item.precio.toFixed(2)}" onchange="handleManualChangePrecio(this, ${item.codproducto})" style="max-width: 100px;">
                </td>
                <td>${total.toFixed(2)}</td>
                <td><button class="btn btn-danger" onclick="removeFromCart(${item.codproducto})">X</button></td>
            </tr>
        `;
    });

    const sbTotal = subtotal / 1.13;
    const igv = sbTotal * 0.13;
    const total = sbTotal + igv;

    subtotalElement.textContent = `${sbTotal.toFixed(2)}`;
    igvElement.textContent = `${igv.toFixed(2)}`;
    totalElement.textContent = `${total.toFixed(2)}`;
}

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
                Swal.fire({
                    title: 'Éxito!',
                    text: 'El registro se guardó con éxito.',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
                end_load();
            }
        }
    });
});
