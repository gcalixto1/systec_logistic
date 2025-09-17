<style>
    .tooltip-suggestions {
        position: absolute;
        background-color: #fff;
        border: 1px solid #ccc;
        z-index: 1000;
        width: 100%;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
        display: none;
        /* Oculto por defecto */
    }

    .tooltip-suggestions div {
        padding: 8px;
        cursor: pointer;
    }

    .tooltip-suggestions div:hover {
        background-color: #f4f4f4;
    }

    .table {
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table th,
    .table td {
        border: 1px solid #fff;
        padding: 8px;
    }

    .table th {
        background-color: #272727;
        text-align: center;
    }

    .centrarcelda {
        text-align: center;
    }

    .cantidad-control {
        align-content: center;
    }

    .cantidad-control button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 5px;
        cursor: pointer;
    }

    .cantidad-control button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    .cantidad-control span {
        margin: 0 10px;
        font-size: 14px;
        font-weight: bold;
    }
</style>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title text-black"><i class="fa fa-cash-register"></i> POS de Ventas</h4>
            </div>
            <form class="form form-material" method="post" action="#" name="saveventa" id="saveventa">
                <div id="save">
                </div>
                <div class="form-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group has-feedback">
                                    <label class="control-label">Búsqueda de Clientes: </label>
                                    <div class="input-group">
                                        <input type="hidden" name="codcliente" id="codcliente" value="0">
                                        <input type="hidden" id="csrf_token" name="csrf_token"
                                            value="<?php echo md5($_SESSION['login_idusuario']); ?>">
                                        <input type="text" class="form-control" name="busqueda" id="busqueda"
                                            placeholder="Ingrese Criterio para la Búsqueda del Cliente"
                                            autocomplete="off" />
                                        <button type="button" class="btn btn-info waves-effect waves-light"
                                            id="new_cliente"><i class="fa fa-user-plus"></i></button>
                                        </span>
                                        <div id="suggestions" class="tooltip-suggestions"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label class="control-label">Seleccione Comprobante: </label>
                                    <select name="prefix" id="prefix" class="form-control" aria-required="true">
                                        <option value=""> -- SELECCIONE -- </option>
                                        <?php
                                        require_once("includes/class.php");
                                        $consecutivo = new Action();
                                        $consecutivo = $consecutivo->Listarconsecutivos();
                                        for ($i = 0; $i < sizeof($consecutivo); $i++) { ?>
                                            <option value="<?php echo $consecutivo[$i]['codigo_consecutivo']; ?>">
                                                <?php echo $consecutivo[$i]['descripcionconse'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group has-feedback">
                                    <label class="control-label">Realice la Búsqueda de Producto: </label>
                                    <input type="text" class="form-control" name="busquedaproductov"
                                        id="busquedaproductov" autocomplete="off"
                                        placeholder="Realice la Búsqueda por Código, Descripción o Nº de Barra">
                                    <div id="sugerencia" class="tooltip-suggestions"></div>
                                </div>
                            </div>
                        </div></br>

                        <div class="table-responsive">
                            <table id="carrito" class="table">
                                <colgroup>
                                    <col width="10%">
                                    <col width="30%">
                                    <col width="15%">
                                    <col width="20%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>Cantidad</th>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="table-responsive m-t-10">
                            <table id="carritototal" class="table">
                                <colgroup>
                                    <col width="25%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="20%">
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td colspan="2">
                                            <h6 class="text-right"><label>SubTotal :</label></h6>
                                        </td>
                                        <td colspan="2">
                                            <h6 class="text-right"><label id="subtotal" name="subtotal">0.00</label>
                                            </h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td colspan="2">
                                            <h6 class="text-right"><label>I.V.A. :</label></h6>
                                        </td>
                                        <td colspan="2">
                                            <h6 class="text-right"><label id="iva_impuesto"
                                                    name="iva_impuesto">0.00</label></h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td colspan="2">
                                            <h5 class="text-right"><label>Total :</label></h5>
                                        </td>
                                        <td colspan="2" class="total-cell">
                                            <h5 class="text-right total-amount"><label id="totalpagar"
                                                    name="totalpagar">0.00</label></h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            <?php
                            include('conexionfin.php');
                            $qry = $conexion->query("SELECT * FROM apertura_caja");
                            if ($qry->num_rows > 0) { // Verifica si hay registros
                                $hayCajaAbierta = false;
                                while ($row = $qry->fetch_assoc()) {
                                    if ($row['estado'] == "A") {
                                        $hayCajaAbierta = true;
                                        break; // Sale del bucle si encuentra una caja abierta
                                    }
                                }
                                if ($hayCajaAbierta) {
                                    // Caja abierta encontrada
                                    echo '<button type="submit" id="idpagar"   class="btn btn-warning"><span class="fa fa-calculator"></span> Pagar</button>';
                                } else {
                                    // No hay caja abierta
                                    echo '<button type="submit" id="idpagar" disabled class="btn btn-dark"><span class="fa fa-calculator"></span> Pagar</button>';
                                }
                            } else {
                                // No hay registros en la tabla
                                echo '<button type="submit" id="idpagar" disabled class="btn btn-dark"><span class="fa fa-calculator"></span> Pagar</button>';
                            }
                            ?>
                            <button type="button" class="btn btn-dark" id="vaciar"><span class="fa fa-trash"></span>
                                Cancelar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/script/autocompleto.js"></script>
<script>
    $('#new_cliente').click(function() {
        uni_modal("Gestion de Clientes", "manage_clientes.php")
    })
</script>
</body>

</html>