function pulsar(e, valor) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 13) comprueba(valor)
}

$(document).ready(function() {

    $('#AgregaCompra').click(function() {
        AgregaCompras();
    });

    $('.agregacompra').keypress(function(e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13') {
          AgregaCompras();
          e.preventDefault();
          return false;
      }
  });

    function AgregaCompras () {

            var code = $('input#codproducto').val();
            var prod = $('input#producto').val();
            var cantp = $('input#cantidad').val();
            var exist = $('input#existencia').val();
            var prec = $('input#preciocompra').val();
            var prec2 = $('input#precioventa').val();
            var descuenfact = $('input#descfactura').val();
            var descuen = $('input#descproducto').val();
            var ivgprod = $('input#ivaproducto').val();
            var lote = $('input#lote').val();
            var tipo = $('input:radio[name=tipoentrada]:checked').val();
            var er_num = /^([0-9])*[.]?[0-9]*$/;
            cantp = parseInt(cantp);
            exist = parseInt(exist);
            cantp = cantp;

            if (code == "") {
                $("#busquedaproductoc").focus();
                $("#busquedaproductoc").css('border-color', '#ff7676');
                swal("Oops", "POR FAVOR REALICE LA BÚSQUEDA DEL PRODUCTO O INGREDIENTE CORRECTAMENTE!", "error");
                return false;
            

            } else if ($('#cantidad').val() == "" || $('#cantidad').val() == "0") {
                $("#cantidad").focus();
                $("#cantidad").css('border-color', '#ff7676');
                swal("Oops", "POR FAVOR INGRESE UNA CANTIDAD VÁLIDA EN COMPRAS!", "error");
                return false;

            } else if (isNaN($('#cantidad').val())) {
                $("#cantidad").focus();
                $("#cantidad").css('border-color', '#ff7676');
                swal("Oops", "POR FAVOR INGRESE SOLO DIGITOS EN CANTIDAD DE COMPRAS!", "error");
                return false;
                
            } else if(prec=="" || prec=="0.00"){
                $("#preciocompra").focus();
                $('#preciocompra').css('border-color','#ff7676');
                swal("Oops", "POR FAVOR INGRESE PRECIO DE COMPRA VALIDO!", "error");  
                return false;
                
            } else if(!er_num.test($('#preciocompra').val())){
                $("#preciocompra").focus();
                $('#preciocompra').css('border-color','#ff7676');
                $("#preciocompra").val("");
                swal("Oops", "POR FAVOR INGRESE SOLO NUMEROS POSITIVOS EN PRECIO COMPRA!", "error");  
                return false;

            } else if(tipo == "PRODUCTO" && prec2=="" || tipo == "PRODUCTO" && prec2=="0.00"){
                $("#precioventa").focus();
                $('#precioventa').css('border-color','#2cabe3');
                swal("Oops", "POR FAVOR INGRESE PRECIO DE VENTA VALIDO!", "error");
                return false;
                
            } else if(!er_num.test($('#precioventa').val()) && tipo == "PRODUCTO"){
                $("#precioventa").focus();
                $('#precioventa').css('border-color','#2cabe3');
                $("#precioventa").val("");
                swal("Oops", "POR FAVOR INGRESE SOLO NUMEROS POSITIVOS EN PRECIO VENTA!", "error");
                return false;

            } else if (parseFloat(prec) > parseFloat(prec2) && tipo == "PRODUCTO") {
                
                $("#precioventa").focus();
                $("#preciocompra").focus();
                $('#precioventa').css('border-color','#2cabe3');
                $('#preciocompra').css('border-color','#2cabe3');
                swal("Oops", "POR FAVOR EL PRECIO DE COMPRA NO PUEDE SER MAYOR AL PRECIO VENTA!", "error");
                return false;

            } else if(descuenfact==""){
                $("#descfactura").focus();
                $('#descfactura').css('border-color','#ff7676');
                alert("INGRESE DESCUENTO EN FACTURA DE COMPRA");
                return false;
                
            } else if(!er_num.test($('#descfactura').val())){
                $("#descfactura").focus();
                $('#descfactura').css('border-color','#ff7676');
                $("#descfactura").val("");
                swal("Oops", "POR FAVOR INGRESE SOLO NUMEROS POSITIVOS PARA DESCUENTO EN FACTURA DE COMPRA!", "error");
                return false;
                
            } else if(descuen==""){
                $("#descproducto").focus();
                $('#descproducto').css('border-color','#ff7676');
                swal("Oops", "POR FAVOR INGRESE DESCUENTO PARA VENTA!", "error");
                return false;
                
            } else if(!er_num.test($('#descproducto').val())){
                $("#descproducto").focus();
                $('#descproducto').css('border-color','#ff7676');
                $("#descproducto").val("");
                swal("Oops", "POR FAVOR INGRESE SOLO NUMEROS POSITIVOS EN DESCUENTO PARA VENTA!", "error");
                return false;
                
            } else if(ivgprod==""){
                $("#ivaproducto").focus();
                $('#ivaproducto').css('border-color','#ff7676');
                swal("Oops", "POR FAVOR SELECCIONE SI TIENE IMPUESTO EL PRODUCTO O INGREDIENTE!", "error");
                return false;

            } else if (lote == "") {
                $("#lote").focus();
                $("#lote").css('border-color', '#ff7676');
                swal("Oops", "POR FAVOR INGRESE LOTE DE PRODUCTO!", "error");                
                return false;

            } else {

                var Carrito = new Object();
                Carrito.Codigo = $('input#codproducto').val();
                Carrito.Producto = $('input#producto').val();
                Carrito.Codcategoria = $('input#codcategoria').val();
                Carrito.Categorias = $('input#categorias').val();
                Carrito.Precio      = $('input#preciocompra').val();
                Carrito.Precio2      = $('input#precioventa').val();
                Carrito.DescproductoFact      = $('input#descfactura').val();
                Carrito.Descproducto      = $('input#descproducto').val();
                Carrito.Ivaproducto = $('select#ivaproducto').val();
                Carrito.Precioconiva = $('input#precioconiva').val();
                Carrito.Lote = $('input#lote').val();
                Carrito.Fechaelaboracion = $('input#fechaelaboracion').val();
                Carrito.Fechaexpiracion = $('input#fechaexpiracion').val();
                Carrito.Tipoentrada      = $('input:radio[name=tipoentrada]:checked').val();
                Carrito.Cantidad = $('input#cantidad').val();
                Carrito.opCantidad = '+=';
                var DatosJson = JSON.stringify(Carrito);
                $.post('carritocompra.php', {
                        MiCarrito: DatosJson
                    },
                    function(data, textStatus) {
                        $("#carrito tbody").html("");
                        var TotalDescuento = 0;
                        var SubtotalFact = 0;
                        var BaseImpIva1 = 0;
                        var contador = 0;
                        var iva = 0;
                        var total = 0;
                        var TotalCompra = 0;

                        $.each(data, function(i, item) {
                            var cantsincero = item.cantidad;
                            cantsincero = parseInt(cantsincero);
                            if (cantsincero != 0) {
                                contador = contador + 1;

                //CALCULO DEL VALOR TOTAL
                var ValorTotal= parseFloat(item.precio) * parseFloat(item.cantidad);

                //CALCULO DEL TOTAL DEL DESCUENTO %
                var Descuento = ValorTotal * item.descproductofact / 100;
                TotalDescuento = parseFloat(TotalDescuento) + parseFloat(Descuento);
                
                //OBTENEMOS DESCUENTO INDIVIDUAL POR PRODUCTOS
                var descsiniva = item.precio * item.descproductofact / 100;
                var descconiva = item.precioconiva * item.descproductofact / 100;

                //CALCULO DE BASE IMPONIBLE IVA SIN PORCENTAJE
                var Operac= parseFloat(item.precio) - parseFloat(descsiniva);
                var Operacion= parseFloat(Operac) * parseFloat(item.cantidad);
                var Subtotal = Operacion.toFixed(2);

                //CALCULO DE BASE IMPONIBLE IVA CON PORCENTAJE
                var Operac3 = parseFloat(item.precioconiva) - parseFloat(descconiva);
                var Operacion3 = parseFloat(Operac3) * parseFloat(item.cantidad);
                var Subbaseimponiva = Operacion3.toFixed(2);

                //BASE IMPONIBLE IVA CON PORCENTAJE
                BaseImpIva1 = parseFloat(BaseImpIva1) + parseFloat(Subbaseimponiva);
                
                //CALCULO GENERAL DE IVA CON BASE IVA * IVA %
                var ivg = $('input#iva').val();
                ivg2  = ivg/100;
                TotalIvaGeneral = parseFloat(BaseImpIva1) * parseFloat(ivg2.toFixed(2));
                
                //SUBTOTAL GENERAL DE FACTURA
                SubtotalFact = parseFloat(SubtotalFact) + parseFloat(Subtotal);
                //BASE IMPONIBLE IVA SIN PORCENTAJE
                BaseImpIva2 = parseFloat(SubtotalFact) - parseFloat(BaseImpIva1);
                
                //CALCULAMOS DESCUENTO POR PRODUCTO
                var desc = $('input#descuento').val();
                desc2  = desc/100;
                
                //CALCULO DEL TOTAL DE FACTURA
                Total = parseFloat(BaseImpIva1) + parseFloat(BaseImpIva2) + parseFloat(TotalIvaGeneral);
                TotalDescuentoGeneral   = parseFloat(Total.toFixed(2)) * parseFloat(desc2.toFixed(2));
                TotalFactura   = parseFloat(Total.toFixed(2)) - parseFloat(TotalDescuentoGeneral.toFixed(2));


                var nuevaFila =
                    "<tr align='center'>" +
                        "<td>" +
                        '<button class="btn btn-info btn-xs" style="cursor:pointer;" onclick="addItem(' +
                        "'" + item.txtCodigo + "'," +
                        "'-1'," +
                        "'" + item.producto + "'," +
                        "'" + item.codcategoria + "'," +
                        "'" + item.categorias + "'," +
                        "'" + item.precio + "', " +
                        "'" + item.precio2 + "', " +
                        "'" + item.descproductofact + "', " +
                        "'" + item.descproducto + "', " +
                        "'" + item.ivaproducto + "', " +
                        "'" + item.precioconiva + "', " +
                        "'" + item.lote + "', " +
                        "'" + item.fechaelaboracion + "', " +
                        "'" + item.fechaexpiracion + "', " +
                        "'" + item.tipoentrada + "', " +
                        "'-'" +
                        ')"' +
                        " type='button'><span class='fa fa-minus'></span></button>" +
                        "<input type='text' id='" + item.cantidad + "' style='width:50px;height:24px;border:#ff7676;' value='" + item.cantidad + "'>" +
                        '<button class="btn btn-info btn-xs" style="cursor:pointer;" onclick="addItem(' +
                        "'" + item.txtCodigo + "'," +
                        "'+1'," +
                        "'" + item.producto + "'," +
                        "'" + item.codcategoria + "'," +
                        "'" + item.categorias + "'," +
                        "'" + item.precio + "', " +
                        "'" + item.precio2 + "', " +
                        "'" + item.descproductofact + "', " +
                        "'" + item.descproducto + "', " +
                        "'" + item.ivaproducto + "', " +
                        "'" + item.precioconiva + "', " +
                        "'" + item.lote + "', " +
                        "'" + item.fechaelaboracion + "', " +
                        "'" + item.fechaexpiracion + "', " +
                        "'" + item.tipoentrada + "', " +
                        "'+'" +
                        ')"' +
                        " type='button'><span class='fa fa-plus'></span></button></td>" +
                        "<td>" + item.txtCodigo + "</td>" +
                        "<td align='left'><h5>" + item.producto + "</h5><small>(" + item.categorias + ")</small></td>" +
                        "<td>" + item.precio + "<input type='hidden' value='" + item.precio2 + "'><input type='hidden' value='" + item.tipoentrada + "'></td>" +
                        "<td>" + ValorTotal.toFixed(2) + "<input type='hidden' value='" + item.lote + "'><input type='hidden' value='" + item.codcategoria + "'></td>" +
                        "<td>" + Descuento.toFixed(2) + "<sup>" + item.descproductofact + "%</sup><input type='hidden' value='" + item.fechaelaboracion + "'></td>" +
                        "<td>" + item.ivaproducto + "<input type='hidden' value='" + item.precioconiva + "'><input type='hidden' value='" + item.fechaexpiracion + "'></td>" +
                        "<td>" + Operacion.toFixed(2) + "</td>" +
                        "<td>" +
                        '<button class="btn btn-dark btn-xs" style="cursor:pointer;color:#fff;" ' +
                        'onclick="addItem(' +
                        "'" + item.txtCodigo + "'," +
                        "'0'," +
                        "'" + item.producto + "'," +
                        "'" + item.codcategoria + "'," +
                        "'" + item.categorias + "'," +
                        "'" + item.precio + "', " +
                        "'" + item.precio2 + "', " +
                        "'" + item.descproductofact + "', " +
                        "'" + item.descproducto + "', " +
                        "'" + item.ivaproducto + "', " +
                        "'" + item.precioconiva + "', " +
                        "'" + item.lote + "', " +
                        "'" + item.fechaelaboracion + "', " +
                        "'" + item.fechaexpiracion + "', " +
                        "'" + item.tipoentrada + "', " +
                        "'='" +
                        ')"' +
                        ' type="button"><span class="fa fa-trash-o"></span></button>' +
                                    "</td>" +
                                    "</tr>";
                                $(nuevaFila).appendTo("#carrito tbody");
                                    
                            $("#lblsubtotal").text(BaseImpIva1.toFixed(2));
                            $("#lblsubtotal2").text(BaseImpIva2.toFixed(2));
                            $("#lbliva").text(TotalIvaGeneral.toFixed(2));
                            $("#lbldescuento").text(TotalDescuentoGeneral.toFixed(2));
                            $("#lbltotal").text(TotalFactura.toFixed(2));
                            
                            $("#txtsubtotal").val(BaseImpIva1.toFixed(2));
                            $("#txtsubtotal2").val(BaseImpIva2.toFixed(2));
                            $("#txtIva").val(TotalIvaGeneral.toFixed(2));
                            $("#txtDescuento").val(TotalDescuentoGeneral.toFixed(2));
                            $("#txtTotal").val(TotalFactura.toFixed(2));

                            }

                        });

                        $("#busquedatipo").focus();
                        LimpiarTexto();
                    },
                    "json"
                );
                return false;
            }
        }

/* CANCELAR LOS ITEM AGREGADOS EN REGISTRO */
$("#vaciar").click(function() {
        var Carrito = new Object();
        Carrito.Codigo = "vaciar";
        Carrito.Producto = "vaciar";
        Carrito.Codcategoria = "vaciar";
        Carrito.Categorias = "vaciar";
        Carrito.Precio      = "vaciar";
        Carrito.Precio2      = "0.00";
        Carrito.DescproductoFact      = "0";
        Carrito.Descproducto      = "0";
        Carrito.Ivaproducto = "vaciar";
        Carrito.Precioconiva      = "0.00";
        Carrito.Lote = "0";
        Carrito.Fechaelaboracion = "vaciar";
        Carrito.Fechaexpiracion = "vaciar";
        Carrito.Tipoentrada      = "vaciar";
        Carrito.Cantidad = "0";
        var DatosJson = JSON.stringify(Carrito);
        $.post('carritocompra.php', {
                MiCarrito: DatosJson
            },
            function(data, textStatus) {
                $("#carrito tbody").html("");
                var nuevaFila =
         "<tr>"+"<td class='text-center' colspan=9><h4>NO HAY DETALLES AGREGADOS</h4></td>"+"</tr>";
                $(nuevaFila).appendTo("#carrito tbody");
                LimpiarTexto();
            },
            "json"
        );
        return false;
    });


$(document).ready(function() {
    $('#vaciar').click(function() {
        $("#carrito tbody").html("");
        var nuevaFila =
        "<tr>"+"<td class='text-center' colspan=9><h4>NO HAY DETALLES AGREGADOS</h4></td>"+"</tr>";
        $(nuevaFila).appendTo("#carrito tbody");
        $("#savecompras")[0].reset();
        $("#lblsubtotal").text("0.00");
        $("#lblsubtotal2").text("0.00");
        $("#lbliva").text("0.00");
        $("#lbldescuento").text("0.00");
        $("#lbltotal").text("0.00");

        $("#txtsubtotal").val("0.00");
        $("#txtsubtotal2").val("0.00");
        $("#txtIva").val("0.00");
        $("#txtDescuento").val("0.00");
        $("#txtTotal").val("0.00");
    });
});



/* CANCELAR LOS ITEM AGREGADOS EN AGREGAR DETALLES */
$("#vaciar2").click(function() {
        var Carrito = new Object();
        Carrito.Codigo = "vaciar";
        Carrito.Producto = "vaciar";
        Carrito.Codcategoria = "vaciar";
        Carrito.Categorias = "vaciar";
        Carrito.Precio      = "vaciar";
        Carrito.Precio2      = "0.00";
        Carrito.DescproductoFact      = "0";
        Carrito.Descproducto      = "0";
        Carrito.Ivaproducto = "vaciar";
        Carrito.Precioconiva      = "0.00";
        Carrito.Lote = "0";
        Carrito.Fechaelaboracion = "vaciar";
        Carrito.Fechaexpiracion = "vaciar";
        Carrito.Tipoentrada      = "vaciar";
        Carrito.Cantidad = "0";
        var DatosJson = JSON.stringify(Carrito);
        $.post('carritocompra.php', {
                MiCarrito: DatosJson
            },
            function(data, textStatus) {
                $("#carrito tbody").html("");
                var nuevaFila =
         "<tr>"+"<td class='text-center' colspan=9><h4>NO HAY DETALLES AGREGADOS</h4></td>"+"</tr>";
                $(nuevaFila).appendTo("#carrito tbody");
                LimpiarTexto();
            },
            "json"
        );
        return false;
    });

$(document).ready(function() {
    $('#vaciar2').click(function() {
        $("#carrito tbody").html("");
        var nuevaFila =
        "<tr>"+"<td class='text-center' colspan=9><h4>NO HAY DETALLES AGREGADOS</h4></td>"+"</tr>";
        $(nuevaFila).appendTo("#carrito tbody");
        $("#agregacompras")[0].reset();
        $("#lblsubtotal").text("0.00");
        $("#lblsubtotal2").text("0.00");
        $("#lbliva").text("0.00");
        $("#lbldescuento").text("0.00");
        $("#lbltotal").text("0.00");

        $("#txtsubtotal").val("0.00");
        $("#txtsubtotal2").val("0.00");
        $("#txtIva").val("0.00");
        $("#txtDescuento").val("0.00");
        $("#txtTotal").val("0.00");
    });
});

//FUNCION PARA CARGAR PRECIO CON IVA
$(document).ready(function() {
        $('#ivaproducto').on('change', function() {
        var valor = $("#ivaproducto").val();
        var precio = $("#preciocompra").val();
        var precioiva = $("#precioconiva").val();

       if (valor === "SI" || valor === true) {

           $("#precioconiva").val(precio); 
} else {
           $("#precioconiva").val("0.00"); 
             } 
       });
});

 //FUNCION PARA CALCULAR PRECIO VENTA
$(document).ready(function (){
          $('#preciocompra').keyup(function (){
        
            var iva = $('select#ivaproducto').val();
            var precio = $('input#preciocompra').val();

            //REALIZO LA ASIGNACION
            $("#precioconiva").val((iva != "" && iva == "NO") ? "0.00" : precio);

      });
 }); 

//FUNCION PARA ACTUALIZAR CALCULO EN FACTURA DE COMPRAS CON DESCUENTO
$(document).ready(function (){
          $('#descuento').keyup(function (){
        
            var txtsubtotal = $('input#txtsubtotal').val();
            var txtsubtotal2 = $('input#txtsubtotal2').val();
            var txtIva = $('input#txtIva').val();
            var desc = $('input#descuento').val();
            descuento  = desc/100;
                        
            //REALIZO EL CALCULO CON EL DESCUENTO INDICADO
            Subtotal = parseFloat(txtsubtotal) + parseFloat(txtsubtotal2) + parseFloat(txtIva); 
            TotalDescuentoGeneral   = parseFloat(Subtotal.toFixed(2)) * parseFloat(descuento.toFixed(2));
            TotalFactura   = parseFloat(Subtotal.toFixed(2)) - parseFloat(TotalDescuentoGeneral.toFixed(2));        
        
            $("#lbldescuento").text(TotalDescuentoGeneral.toFixed(2));
            $("#lbltotal").text(TotalFactura.toFixed(2));
            $("#txtDescuento").val(TotalDescuentoGeneral.toFixed(2));
            $("#txtTotal").val(TotalFactura.toFixed(2));
         });
 });


//FUNCION PARA ACTUALIZAR CALCULO EN FACTURA DE COMPRAS CON IVA
$(document).ready(function (){
          $('#iva').keyup(function (){
        
            var txtsubtotal = $('input#txtsubtotal').val();
            var txtsubtotal2 = $('input#txtsubtotal2').val();
            var txtIva = $('input#txtIva').val();
            var iva = $('input#iva').val();
            var desc = $('input#descuento').val();
            ivg2  = iva/100;
            descuento  = desc/100;
                        
            //REALIZO EL CALCULO CON EL IVA INDICADO
            TotalIvaGeneral = parseFloat(txtsubtotal) * parseFloat(ivg2.toFixed(2));

            Subtotal = parseFloat(txtsubtotal) + parseFloat(txtsubtotal2) + parseFloat(TotalIvaGeneral); 
            TotalDescuentoGeneral   = parseFloat(Subtotal.toFixed(2)) * parseFloat(descuento.toFixed(2));
            TotalFactura   = parseFloat(Subtotal.toFixed(2)) - parseFloat(TotalDescuentoGeneral.toFixed(2));        
        
            $("#lbliva").text(TotalIvaGeneral.toFixed(2));
            $("#txtIva").text(TotalIvaGeneral.toFixed(2));
            
            $("#lbldescuento").text(TotalDescuentoGeneral.toFixed(2));
            $("#txtDescuento").val(TotalDescuentoGeneral.toFixed(2));
            
            $("#lbltotal").text(TotalFactura.toFixed(2));
            $("#txtTotal").val(TotalFactura.toFixed(2));
         });
 });



    $("#carrito tbody").on('keydown', 'input', function(e) {
        var element = $(this);
        var pvalue = element.val();
        var code = e.charCode || e.keyCode;
        var avalue = String.fromCharCode(code);
        var action = element.siblings('button').first().attr('onclick');
        var params;
        if (code !== 14 && /[^\d]/ig.test(avalue)) {
            e.preventDefault();
            return;
        }
        if (element.attr('data-proc') == '1') {
            return true;
        }
        element.attr('data-proc', '1');
        params = action.match(/\'([^\']+)\'/g).map(function(v) {
            return v.replace(/\'/g, '');
        });
        setTimeout(function() {
            if (element.attr('data-proc') == '1') {
                var value = element.val() || 0;
                addItem(
                    params[0],
                    value,
                    params[2],
                    params[3],
                    params[4],
                    params[5],
                    params[6],
                    params[7],
                    params[8],
                    params[9],
                    params[10],
                    params[11],
                    params[12],
                    params[13],
                    params[14],
                    '='
                );
                element.attr('data-proc', '0');
            }
        }, 500);
    });
});

function LimpiarTexto() {
    $("#busquedatipo").val("");
    $("#codproducto").val("");
    $("#producto").val("");
    $("#codcategoria").val("");
    $("#categorias").val("");
    $("#preciocompra").val("");
    $("#precioventa").val("0.00");
    $("#descfactura").val("0.00");
    $("#descproducto").val("0.00");
    $("#ivaproducto").val("");
    $("#precioconiva").val("0.00");
    $("#lote").val("0");
    $("#fechaelaboracion").val("");
    $("#fechaexpiracion").val("");
    $("#cantidad").val("");
}


function addItem(codigo, cantidad, producto, codcategoria, categorias, precio, precio2, descproductofact, descproducto, ivaproducto, precioconiva, lote, fechaelaboracion, fechaexpiracion, tipoentrada, opCantidad) {
    var Carrito = new Object();
    Carrito.Codigo = codigo;
    Carrito.Producto = producto;
    Carrito.Codcategoria = codcategoria;
    Carrito.Categorias = categorias;
    Carrito.Precio = precio;
    Carrito.Precio2 = precio2;
    Carrito.DescproductoFact = descproductofact;
    Carrito.Descproducto = descproducto;
    Carrito.Ivaproducto = ivaproducto;
    Carrito.Precioconiva      = precioconiva;
    Carrito.Lote = lote;
    Carrito.Fechaelaboracion = fechaelaboracion;
    Carrito.Fechaexpiracion = fechaexpiracion;
    Carrito.Tipoentrada = tipoentrada;
    Carrito.Cantidad = cantidad;
    Carrito.opCantidad = opCantidad;
    var DatosJson = JSON.stringify(Carrito);
    $.post('carritocompra.php', {
            MiCarrito: DatosJson
        },
        function(data, textStatus) {
            $("#carrito tbody").html("");
            var TotalDescuento = 0;
            var SubtotalFact = 0;
            var BaseImpIva1 = 0;
            var contador = 0;
            var iva = 0;
            var total = 0;
            var TotalCompra = 0;

            $.each(data, function(i, item) {
                var cantsincero = item.cantidad;
                cantsincero = parseInt(cantsincero);
                if (cantsincero != 0) {
                    contador = contador + 1;


                //CALCULO DEL VALOR TOTAL
                var ValorTotal= parseFloat(item.precio) * parseFloat(item.cantidad);

                //CALCULO DEL TOTAL DEL DESCUENTO %
                var Descuento = ValorTotal * item.descproductofact / 100;
                TotalDescuento = parseFloat(TotalDescuento) + parseFloat(Descuento);
                
                //OBTENEMOS DESCUENTO INDIVIDUAL POR PRODUCTOS
                var descsiniva = item.precio * item.descproductofact / 100;
                var descconiva = item.precioconiva * item.descproductofact / 100;

                //CALCULO DE BASE IMPONIBLE IVA SIN PORCENTAJE
                var Operac= parseFloat(item.precio) - parseFloat(descsiniva);
                var Operacion= parseFloat(Operac) * parseFloat(item.cantidad);
                var Subtotal = Operacion.toFixed(2);

                //CALCULO DE BASE IMPONIBLE IVA CON PORCENTAJE
                var Operac3 = parseFloat(item.precioconiva) - parseFloat(descconiva);
                var Operacion3 = parseFloat(Operac3) * parseFloat(item.cantidad);
                var Subbaseimponiva = Operacion3.toFixed(2);

                //BASE IMPONIBLE IVA CON PORCENTAJE
                BaseImpIva1 = parseFloat(BaseImpIva1) + parseFloat(Subbaseimponiva);
                
                //CALCULO GENERAL DE IVA CON BASE IVA * IVA %
                var ivg = $('input#iva').val();
                ivg2  = ivg/100;
                TotalIvaGeneral = parseFloat(BaseImpIva1) * parseFloat(ivg2.toFixed(2));
                
                //SUBTOTAL GENERAL DE FACTURA
                SubtotalFact = parseFloat(SubtotalFact) + parseFloat(Subtotal);
                //BASE IMPONIBLE IVA SIN PORCENTAJE
                BaseImpIva2 = parseFloat(SubtotalFact) - parseFloat(BaseImpIva1);
                
                //CALCULAMOS DESCUENTO POR PRODUCTO
                var desc = $('input#descuento').val();
                desc2  = desc/100;
                
                //CALCULO DEL TOTAL DE FACTURA
                Total = parseFloat(BaseImpIva1) + parseFloat(BaseImpIva2) + parseFloat(TotalIvaGeneral);
                TotalDescuentoGeneral   = parseFloat(Total.toFixed(2)) * parseFloat(desc2.toFixed(2));
                TotalFactura   = parseFloat(Total.toFixed(2)) - parseFloat(TotalDescuentoGeneral.toFixed(2));


                   var nuevaFila =
                    "<tr align='center'>" +
                        "<td>" +
                        '<button class="btn btn-info btn-xs" style="cursor:pointer;" onclick="addItem(' +
                        "'" + item.txtCodigo + "'," +
                        "'-1'," +
                        "'" + item.producto + "'," +
                        "'" + item.codcategoria + "'," +
                        "'" + item.categorias + "'," +
                        "'" + item.precio + "', " +
                        "'" + item.precio2 + "', " +
                        "'" + item.descproductofact + "', " +
                        "'" + item.descproducto + "', " +
                        "'" + item.ivaproducto + "', " +
                        "'" + item.precioconiva + "', " +
                        "'" + item.lote + "', " +
                        "'" + item.fechaelaboracion + "', " +
                        "'" + item.fechaexpiracion + "', " +
                        "'" + item.tipoentrada + "', " +
                        "'-'" +
                        ')"' +
                        " type='button'><span class='fa fa-minus'></span></button>" +
                        "<input type='text' id='" + item.cantidad + "' style='width:50px;height:24px;border:#ff7676;' value='" + item.cantidad + "'>" +
                        '<button class="btn btn-info btn-xs" style="cursor:pointer;" onclick="addItem(' +
                        "'" + item.txtCodigo + "'," +
                        "'+1'," +
                        "'" + item.producto + "'," +
                        "'" + item.codcategoria + "'," +
                        "'" + item.categorias + "'," +
                        "'" + item.precio + "', " +
                        "'" + item.precio2 + "', " +
                        "'" + item.descproductofact + "', " +
                        "'" + item.descproducto + "', " +
                        "'" + item.ivaproducto + "', " +
                        "'" + item.precioconiva + "', " +
                        "'" + item.lote + "', " +
                        "'" + item.fechaelaboracion + "', " +
                        "'" + item.fechaexpiracion + "', " +
                        "'" + item.tipoentrada + "', " +
                        "'+'" +
                        ')"' +
                        " type='button'><span class='fa fa-plus'></span></button></td>" +
                        "<td>" + item.txtCodigo + "</td>" +
                        "<td align='left'><h5>" + item.producto + "</h5><small>(" + item.categorias + ")</small></td>" +
                        "<td>" + item.precio + "<input type='hidden' value='" + item.precio2 + "'><input type='hidden' value='" + item.tipoentrada + "'></td>" +
                        "<td>" + ValorTotal.toFixed(2) + "<input type='hidden' value='" + item.lote + "'><input type='hidden' value='" + item.codcategoria + "'></td>" +
                        "<td>" + Descuento.toFixed(2) + "<sup>" + item.descproductofact + "%</sup><input type='hidden' value='" + item.fechaelaboracion + "'></td>" +
                        "<td>" + item.ivaproducto + "<input type='hidden' value='" + item.precioconiva + "'><input type='hidden' value='" + item.fechaexpiracion + "'></td>" +
                        "<td>" + Operacion.toFixed(2) + "</td>" +
                        "<td>" +
                        '<button class="btn btn-dark btn-xs" style="cursor:pointer;color:#fff;" ' +
                        'onclick="addItem(' +
                        "'" + item.txtCodigo + "'," +
                        "'0'," +
                        "'" + item.producto + "'," +
                        "'" + item.codcategoria + "'," +
                        "'" + item.categorias + "'," +
                        "'" + item.precio + "', " +
                        "'" + item.precio2 + "', " +
                        "'" + item.descproductofact + "', " +
                        "'" + item.descproducto + "', " +
                        "'" + item.ivaproducto + "', " +
                        "'" + item.precioconiva + "', " +
                        "'" + item.lote + "', " +
                        "'" + item.fechaelaboracion + "', " +
                        "'" + item.fechaexpiracion + "', " +
                        "'" + item.tipoentrada + "', " +
                        "'='" +
                        ')"' +
                        ' type="button"><span class="fa fa-trash-o"></span></button>' +
                                    "</td>" +
                                    "</tr>";
                    $(nuevaFila).appendTo("#carrito tbody");
                                    
                $("#lblsubtotal").text(BaseImpIva1.toFixed(2));
                $("#lblsubtotal2").text(BaseImpIva2.toFixed(2));
                $("#lbliva").text(TotalIvaGeneral.toFixed(2));
                $("#lbldescuento").text(TotalDescuentoGeneral.toFixed(2));
                $("#lbltotal").text(TotalFactura.toFixed(2));
                
                $("#txtsubtotal").val(BaseImpIva1.toFixed(2));
                $("#txtsubtotal2").val(BaseImpIva2.toFixed(2));
                $("#txtIva").val(TotalIvaGeneral.toFixed(2));
                $("#txtDescuento").val(TotalDescuentoGeneral.toFixed(2));
                $("#txtTotal").val(TotalFactura.toFixed(2));

                }
            });
            if (contador == 0) {

                $("#carrito tbody").html("");

                var nuevaFila =
            "<tr>"+"<td class='text-center' colspan=9><h4>NO HAY DETALLES AGREGADOS</h4></td>"+"</tr>";
                $(nuevaFila).appendTo("#carrito tbody");

                //alert("ELIMINAMOS TODOS LOS SUBTOTAL Y TOTALES");
                $("#savecompras")[0].reset();
                $("#lblsubtotal").text("0.00");
                $("#lblsubtotal2").text("0.00");
                $("#lbliva").text("0.00");
                $("#lbldescuento").text("0.00");
                $("#lbltotal").text("0.00");
                
                $("#txtsubtotal").val("0.00");
                $("#txtsubtotal2").val("0.00");
                $("#txtIva").val("0.00");
                $("#txtDescuento").val("0.00");
                $("#txtTotal").val("0.00");
                $("#txtTotalCompra").val("0.00");

            }
            LimpiarTexto();
        },
        "json"
    );
    return false;
}