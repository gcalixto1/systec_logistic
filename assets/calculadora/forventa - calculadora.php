<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
     if ($_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria" || $_SESSION["acceso"]=="cajero") {

$tra = new Login();
$ses = $tra->ExpiraSession(); 

$imp = new Login();
$imp = $imp->ImpuestosPorId();
$impuesto = $imp[0]['nomimpuesto'];
$valor = $imp[0]['valorimpuesto'];

$con = new Login();
$con = $con->ConfiguracionPorId();
$simbolo = $con[0]['simbolo'];

if(isset($_POST['btn-submit']))
{
$reg = $tra->RegistrarCotizaciones();
exit;
}
else if(isset($_POST['btn-update']))
{
$reg = $tra->ActualizarCotizaciones();
exit;
}  
else if(isset($_POST['btn-agregar']))
{
$reg = $tra->AgregarDetallesCotizaciones();
exit;
} 
else if(isset($_POST["btn-cliente"]))
{
$reg = $tra->RegistrarClientes();
exit;
}      
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Ing. Ruben Chirinos">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title></title>

    <!-- Menu CSS -->
    <link href="assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <!-- toast CSS -->
    <link href="assets/plugins/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">
    <!-- Sweet-Alert -->
    <link rel="stylesheet" href="assets/css/sweetalert.css">
    <!-- animation CSS -->
    <link href="assets/css/animate.css" rel="stylesheet">
    <!-- needed css -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- color CSS -->
    <link href="assets/css/default.css" id="theme" rel="stylesheet">
    
    <!-- script Calculadora -->
    <script src="assets/calculadora/jquery-2.1.4.min.js"></script>
    <link rel="stylesheet" href="assets/calculadora/prism.css">
    <script src="assets/calculadora/prism.js"></script>
    <link rel="stylesheet" href="assets/calculadora/SimpleCalculadorajQuery.css">
    <script src="assets/calculadora/SimpleCalculadorajQuery.js"></script>
       
</head>
  <body onLoad="muestraReloj()" class="fix-header">
    
   <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-boxed-layout="full" data-boxed-layout="boxed" data-header-position="fixed" data-sidebar-position="fixed" class="mini-sidebar"> 

<!-- sample modal content -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title text-white" id="myModalLabel"><i class="fa fa-pencil"></i> Nuevo Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src="assets/images/close.png"/></button>
            </div>
            
        <form class="form form-material" name="clientecotizacion" id="clientecotizacion" action="#">
                
               <div class="modal-body">
                  
                  <div id="savec">
                   <!-- error will be shown here ! -->
                  </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Tipo Documento: <span class="symbol required"></span></label>
                            <i class="fa fa-bars form-control-feedback"></i>
                            <select name="documcliente" id="documcliente" class="form-control" required="" aria-required="true">
                                <option value="">SELECCIONE</option>
                                <option value="DNI">DNI</option>
                                <option value="CUIT">CUIT</option>
                                <option value="RUC">RUC</option>
                                <option value="REGISTRO CIVIL">REGISTRO CIVIL</option>
                                <option value="TARJETA DE IDENTIDAD">TARJETA DE IDENTIDAD</option>
                                <option value="CI EXTRANJERA">CI EXTRANJERA</option>
                                <option value="EN TRAMITE">EN TRAMITE</option>
                                <option value="PASAPORTE">PASAPORTE</option>
                            </select>  
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Nº de Documento: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="dnicliente" id="dnicliente" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nº de Documento" autocomplete="off" required="" aria-required="true"/> 
                            <i class="fa fa-bolt form-control-feedback"></i> 
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Nombre de Cliente: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="nomcliente" id="nomcliente" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nombre de Cliente" autocomplete="off" required="" aria-required="true"/>  
                            <i class="fa fa-pencil form-control-feedback"></i> 
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Nº de Teléfono: </label>
                            <input type="text" class="form-control phone-inputmask" name="tlfcliente" id="tlfcliente" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nº de Teléfono" autocomplete="off" required="" aria-required="true"/>  
                            <i class="fa fa-phone form-control-feedback"></i> 
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Correo de Cliente: </label>
                            <input type="text" class="form-control" name="emailcliente" id="emailcliente" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Correo Electronico" autocomplete="off" required="" aria-required="true"/> 
                            <i class="fa fa-envelope-o form-control-feedback"></i>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Tipo de Cliente: <span class="symbol required"></span></label>
                            <i class="fa fa-bars form-control-feedback"></i>
                            <select name="tipocliente" id="tipocliente" class="form-control" required="" aria-required="true">
                                <option value="">SELECCIONE</option>
                                <option value="NATURAL">NATURAL</option>
                                <option value="JURIDICO">JURIDICO</option>
                            </select> 
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Provincia: <span class="symbol required"></span></label>
                            <i class="fa fa-bars form-control-feedback"></i>
                            <select name="id_provincia" id="id_provincia" onChange="CargaDepartamentos(this.form.id_provincia.value);" class='form-control' required="" aria-required="true">
                                <option value="">SELECCIONE</option>
                                <?php
                                $pro = new Login();
                                $pro = $pro->ListarProvincias();
                                for($i=0;$i<sizeof($pro);$i++){ ?>
                                <option value="<?php echo $pro[$i]['id_provincia'] ?>"><?php echo $pro[$i]['provincia'] ?></option>        
                                  <?php } ?>
                          </select> 
                      </div>
                   </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Departamentos: <span class="symbol required"></span></label>
                            <i class="fa fa-bars form-control-feedback"></i>
                            <select class="form-control" id="id_departamento" name="id_departamento" required="" aria-required="true">
                                <option value="">SIN RESULTADOS</option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Dirección Domiciliaria: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="direccliente" id="direccliente" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Dirección Domiciliaria" autocomplete="off" required="" aria-required="true"/> 
                            <i class="fa fa-map-marker form-control-feedback"></i>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Monto de Crédito: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="montocredito" id="montocredito" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Ingrese Monto de Crédito" autocomplete="off" required="" aria-required="true"/>  
                            <i class="fa fa-usd form-control-feedback"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="btn-cliente" id="btn-cliente" class="btn btn-primary"><span class="fa fa-save"></span> Guardar</button>
                <button class="btn btn-dark" type="reset" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-trash-o"></span> Cancelar</button>
            </div>
        </form>

    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal --> 


    
        <!-- INICIO DE MENU -->
        <?php include('menu.php'); ?>
        <!-- FIN DE MENU -->

        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb border-bottom">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
     <h5 class="font-medium text-uppercase mb-0"><i class="fa fa-tasks"></i> Gestión de Cotizaciones</h5>
                    </div>
                    <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
                        <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                            <ol class="breadcrumb mb-0 justify-content-end p-0">
                                <li class="breadcrumb-item">Cotizaciones</li>
                                <li class="breadcrumb-item active" aria-current="page">Cotización</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="page-content container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
               
<!-- Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card ">
            <div class="card-header bg-danger">
            <h4 class="card-title text-white"><i class="fa fa-pencil"></i> Gestión de Cotizaciones</h4>
            </div>

<?php if (isset($_GET['codcotizacion']) && isset($_GET['codsucursal']) && base64_decode($_GET["proceso"])=="U") {
      
$reg = $tra->CotizacionesPorId(); ?>
      
<form class="form form-material" method="post" action="#" name="updatecotizaciones" id="updatecotizaciones" data-id="<?php echo $reg[0]["codcotizacion"] ?>">

<?php } else if (isset($_GET['codcotizacion']) && isset($_GET['codsucursal']) && base64_decode($_GET["proceso"])=="A") {
      
$reg = $tra->CotizacionesPorId(); ?>
      
<form class="form form-material" method="post" action="#" name="agregacotizaciones" id="agregacotizaciones" data-id="<?php echo $reg[0]["codcotizacion"] ?>">
        
<?php } else { ?>
        
 <form class="form form-material" method="post" action="#" name="savecotizaciones" id="savecotizaciones">

<?php } ?>


<!-- sample modal content -->
<div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
            <h4 class="modal-title text-white" id="myModalLabel"><i class="fa fa-pencil"></i> Pago de Venta</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src="assets/images/close.png"/></button>
            </div>

            <div class="modal-body">

                <div id="save">
                   <!-- error will be shown here ! -->
                </div>

                <div class="row">
                    <div class="col-md-6"> 
                        <div class="form-group has-feedback"> 
                            <label class="control-label">Tipo de Compra: <span class="symbol required"></span></label>
                            <i class="fa fa-bars form-control-feedback"></i>
                            <select name="tipoventa" id="tipoventa" class="form-control" onChange="CargaFormaPagosVentas()" required="" aria-required="true">
                            <option value=""> -- SELECCIONE -- </option>
                            <option value="CONTADO">CONTADO</option>
                            <option value="CREDITO">CRÉDITO</option>
                            </select>
                        </div> 
                    </div>

                    <div class="col-md-6"> 
                        <div class="form-group has-feedback"> 
                            <label class="control-label">Forma de Pago: <span class="symbol required"></span></label>
                            <i class="fa fa-bars form-control-feedback"></i>
                            <select name="formaventa" id="formaventa" class="form-control" disabled="" required="" aria-required="true">
                            <option value=""> -- SELECCIONE -- </option>
                            <?php
                            $pago = new Login();
                            $pago = $pago->ListarMediosPagos();
                            for($i=0;$i<sizeof($pago);$i++){ ?>
                            <option value="<?php echo $pago[$i]['codmediopago'] ?>"><?php echo $pago[$i]['mediopago'] ?></option>       
                            <?php } ?>
                            </select>
                        </div> 
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6"> 
                         <div class="form-group has-feedback"> 
                            <label class="control-label">Fecha Vence Crédito: <span class="symbol required"></span></label> 
                            <input type="text" class="form-control calendario" name="fechavencecredito" id="fechavencecredito" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Fecha Vence Crédito" disabled="" required="" aria-required="true">
                            <i class="fa fa-calendar form-control-feedback"></i>  
                       </div> 
                    </div> 

                    <div class="col-md-6"> 
                        <div class="form-group has-feedback"> 
                           <label class="control-label">Abono Crédito: <span class="symbol required"></span></label>
                           <input class="form-control number" type="text" name="montoabono" id="montoabono" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Monto de Abono" value="0.00" disabled="" required="" aria-required="true"> 
                           <i class="fa fa-tint form-control-feedback"></i>
                        </div> 
                    </div>
                </div>

                <h2><b>Importe Total: <?php echo $simbolo; ?> <label id="lbltotal" name="lbltotal">0.00</label></b></h2>
                <h2><b>Monto Pagado: <?php echo $simbolo; ?> <label id="lblpagado" name="lblpagado">0.00</label></b></h2>
                <h2><b>Monto Devuelto: <?php echo $simbolo; ?> <label id="lbldevuelto" name="lbldevuelto">0.00</label></b></h2>

                <div class="row">
                    <div class="col-md-6"> 
                        <div class="form-group has-feedback"> 
                           <label class="control-label">Monto Pagado: <span class="symbol required"></span></label>
                           <input class="form-control calculodevolucion" type="text" name="montopagado" id="payment" aria-describedby="basic-addon3" oninput="$(this).calculateChange();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Monto Pagado por Cliente" required="" aria-required="true"> 
                           <i class="fa fa-tint form-control-feedback"></i>
                        </div> 
                    </div>

                    <div class="col-md-6"> 
                        <div class="form-group has-feedback"> 
                           <label class="control-label">Cambio Devuelto: <span class="symbol required"></span></label>
                           <input class="form-control number" type="text" name="montodevuelto" id="montodevuelto" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Cambio Devuelto a Cliente" disabled="disabled" readonly="readonly" value="0.00" aria-required="true"> 
                           <i class="fa fa-tint form-control-feedback"></i>
                        </div> 
                    </div>
                </div>
                
                <div class="row">
                    <div id="idCalculadora"></div>
                </div>

                <script>
                    $("#idCalculadora").Calculadora();        
                </script>
            </div>

            <div class="modal-footer">
            <button type="submit" name="btn-submit" id="btn-submit" class="btn btn-danger"><span class="fa fa-save"></span> Guardar</button>
            <button class="btn btn-dark" type="reset" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-trash-o"></span> Cancelar</button>
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->



               <div class="form-body">

            <div class="card-body">

        <input type="hidden" name="codcotizacion" id="codcotizacion" <?php if (isset($reg[0]['codcotizacion'])) { ?> value="<?php echo $reg[0]['codcotizacion']; ?>"<?php } ?>>

        <input type="hidden" name="codsucursal" id="codsucursal" <?php if (isset($reg[0]['codsucursal'])) { ?> value="<?php echo $reg[0]['codsucursal']; ?>" <?php } else { ?> value="<?php echo $_SESSION["codsucursal"]; ?>"<?php } ?>>
        
            <h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-user"></i> Detalle del Cliente</h3><hr>

              <div class="row">

                <div class="col-md-11">
                  <div class="form-group has-feedback">
                    <label class="control-label">Búsqueda de Clientes: <span class="symbol required"></span></label>
                    <input type="hidden" name="codcliente" id="codcliente" <?php if (isset($reg[0]['codcliente'])) { ?> value="<?php echo $reg[0]['codcliente']; ?>" <?php } ?>>
                    <input type="text" class="form-control" name="busqueda" id="busqueda" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Realice la Búsqueda del Cliente por Nº de Documento, Nombres o Apellidos" <?php if (isset($reg[0]['codcliente'])) { ?> value="<?php echo $reg[0]['documcliente'].": ".$reg[0]['dnicliente'].": ".$reg[0]['nomcliente']; ?>" <?php } ?> autocomplete="off" required="" aria-required="true"/> 
                    <i class="fa fa-search form-control-feedback"></i> 
                  </div>
                </div>

              <div class="col-md-1">
                <div class="form-group has-feedback">
                  <br>
                  <button type="button" class="btn btn-info waves-effect waves-light" data-placement="left" title="Nuevo Cliente" data-original-title="" data-href="#" data-toggle="modal" data-target="#myModal" data-backdrop="static" data-keyboard="false"><i class="fa fa-user-plus"></i></button>
                </div>
              </div>
                      
              </div>

<?php if (isset($_GET['codcotizacion']) && isset($_GET['codsucursal']) && base64_decode($_GET["proceso"])=="U") { ?>

<h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle del Comprobante</h3>

<div id="detallescotizacionesupdate">

        <div class="table-responsive m-t-20">
            <table class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Precio Unit.</th>
                        <th>Valor Total</th>
                        <th>Desc %</th>
                        <th><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?></th>
                        <th>Valor Neto</th>
<?php if ($_SESSION['acceso'] == "administradorS") { ?><th>Acción</th><?php } ?>
                    </tr>
                </thead>
                <tbody>
<?php 
$tra = new Login();
$detalle = $tra->VerDetallesCotizaciones();
$a=1;
for($i=0;$i<sizeof($detalle);$i++){  
    ?>
                                 <tr class="text-center">
      <td><input type="text" class="form-control" name="cantcotizacion[]" id="cantcotizacion_<?php echo $a; ?>" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Cantidad" value="<?php echo $detalle[$i]["cantcotizacion"]; ?>" style="width: 80px;" onfocus="this.style.background=('#B7F0FF')" onBlur="this.style.background=('#e4e7ea')" title="Ingrese Cantidad" required="" aria-required="true"></td>
      
      <td><input type="hidden" name="coddetallecotizacion[]" id="coddetallecotizacion" value="<?php echo $detalle[$i]["coddetallecotizacion"]; ?>"><?php echo $detalle[$i]['codproducto']; ?></td>
      
      <td><input type="hidden" name="preciocompra[]" id="preciocompra" value="<?php echo $detalle[$i]["preciocompra"]; ?>"><h5><?php echo $detalle[$i]['producto']; ?></h5><small>MARCA (<?php echo $detalle[$i]['nommarca'] == '' ? "*****" : $detalle[$i]['nommarca'] ?>) - MODELO (<?php echo $detalle[$i]['nommodelo'] == '' ? "*****" : $detalle[$i]['nommodelo'] ?>)</small></td>
      
      <td><input type="hidden" name="precioventa[]" id="precioventa" value="<?php echo $detalle[$i]["precioventa"]; ?>"><?php echo $simbolo." ".$detalle[$i]['precioventa']; ?></td>

       <td><input type="hidden" name="valortotal[]" id="valortotal" value="<?php echo $detalle[$i]["valortotal"]; ?>"><?php echo $simbolo." ".$detalle[$i]['valortotal']; ?></td>
      
      <td><input type="hidden" name="descproducto[]" id="descproducto" value="<?php echo $detalle[$i]["descproducto"]; ?>"><?php echo $simbolo." ".$detalle[$i]['totaldescuentov']; ?><sup><?php echo $detalle[$i]['descproducto']; ?>%</sup></td>

      <td><input type="hidden" name="ivaproducto[]" id="ivaproducto" value="<?php echo $detalle[$i]["ivaproducto"]; ?>"><?php echo $detalle[$i]['ivaproducto']; ?></td>

      <td><?php echo $simbolo." ".$detalle[$i]['valorneto']; ?></td>

 <?php if ($_SESSION['acceso'] == "administradorS") { ?><td>
<button type="button" class="btn btn-rounded btn-dark" onClick="EliminarDetallesCotizacionesAgregar('<?php echo base64_encode($detalle[$i]["coddetallecotizacion"]); ?>','<?php echo base64_encode($detalle[$i]["codcotizacion"]); ?>','<?php echo base64_encode($detalle[$i]["codsucursal"]); ?>','<?php echo base64_encode("DETALLESCOTIZACIONES") ?>')" title="Eliminar" ><i class="fa fa-trash-o"></i></button></td><?php } ?>
                                 </tr>
                     <?php } ?>
                </tbody>
            </table><hr>

             <table id="carritototal" class="table-responsive">
                <tr>
    <td width="50">&nbsp;</td>
    <td width="250">
    <h5><label>Total Gravado <?php echo $reg[0]['iva'] ?>%:</label></h5>
    </td>
                  
    <td width="250">
    <h5><?php echo $simbolo; ?> <label id="lblsubtotal" name="lblsubtotal"><?php echo $reg[0]['subtotalivasi'] ?></label></h5>
    <input type="hidden" name="txtsubtotal" id="txtsubtotal" value="<?php echo $reg[0]['subtotalivasi'] ?>"/>
    </td>

    <td width="250">
    <h5><label>Total Exento 0%:</label></h5>
    </td>
    
    <td width="250">
    <h5><?php echo $simbolo; ?> <label id="lblsubtotal2" name="lblsubtotal2"><?php echo $reg[0]['subtotalivano'] ?></label></h5>
    <input type="hidden" name="txtsubtotal2" id="txtsubtotal2" value="<?php echo $reg[0]['subtotalivano'] ?>"/>
    </td>

    <td class="text-center" width="250">
    <h2><b>Importe Total</b></h2>
    </td>
                </tr>
                <tr>
    <td>&nbsp;</td>
    <td>
    <h5><label><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?> <?php echo $reg[0]['iva'] ?>%:<input type="hidden" name="iva" id="iva" autocomplete="off" value="<?php echo $reg[0]['iva'] ?>"></label></h5>
    </td>
    
    <td>
    <h5><?php echo $simbolo; ?> <label id="lbliva" name="lbliva"><?php echo $reg[0]['totaliva'] ?></label></h5>
    <input type="hidden" name="txtIva" id="txtIva" value="<?php echo $reg[0]['totaliva'] ?>"/>
    </td>

    <td>
    <h5><label>Desc. Global <input class="number" type="text" name="descuento" id="descuento" onKeyPress="EvaluateText('%f', this);" style="border-radius:4px;height:30px;width:70px;" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="<?php echo $reg[0]['descuento'] ?>">%:</label></h5>
    </td>

    <td>
    <h5><?php echo $simbolo; ?> <label id="lbldescuento" name="lbldescuento"><?php echo $reg[0]['totaldescuento'] ?></label></h5>
    <input type="hidden" name="txtDescuento" id="txtDescuento" value="<?php echo $reg[0]['totaldescuento'] ?>"/>
    </td>

    <td class="text-center">
    <h2><?php echo $simbolo; ?> <label id="lbltotal" name="lbltotal"><?php echo $reg[0]['totalpago'] ?></label></h2>
    <input type="hidden" name="txtTotal" id="txtTotal" value="<?php echo $reg[0]['totalpago'] ?>"/>
    <input type="hidden" name="txtTotalCompra" id="txtTotalCompra" value="0.00"/>
    </td>
                    </tr>
                  </table>
        </div>
</div>


<?php } else if (isset($_GET['codcotizacion']) && isset($_GET['codsucursal']) && base64_decode($_GET["proceso"])=="A") { ?>

<h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle de Productos</h3>

<div id="detallescotizacionesagregar">

        <div class="table-responsive m-t-20">
            <table class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Nº</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Valor Total</th>
                        <th>Desc %</th>
                        <th><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?></th>
                        <th>Valor Neto</th>
<?php if ($_SESSION['acceso'] == "administradorS") { ?><th>Acción</th><?php } ?>
                    </tr>
                </thead>
                <tbody>
<?php 
$tra = new Login();
$detalle = $tra->VerDetallesCotizaciones();
$a=1;
for($i=0;$i<sizeof($detalle);$i++){  
    ?>
                                 <tr class="text-center">
      <td><?php echo $a++; ?></td>
      
      <td><?php echo $detalle[$i]['codproducto']; ?></td>
      
      <td><h5><?php echo $detalle[$i]['producto']; ?></h5>
    <small>MARCA (<?php echo $detalle[$i]['nommarca'] == '' ? "*****" : $detalle[$i]['nommarca'] ?>) - MODELO (<?php echo $detalle[$i]['nommodelo'] == '' ? "*****" : $detalle[$i]['nommodelo'] ?>)</small></td>

      <td><?php echo $detalle[$i]['cantcotizacion']; ?></td>
      
      <td><?php echo $simbolo." ".$detalle[$i]['precioventa']; ?></td>

       <td><?php echo $simbolo." ".$detalle[$i]['valortotal']; ?></td>
      
      <td><?php echo $simbolo." ".$detalle[$i]['totaldescuentov']; ?><sup><?php echo $detalle[$i]['descproducto']; ?>%</sup></td>

      <td><?php echo $detalle[$i]['ivaproducto']; ?></td>

      <td><?php echo $simbolo." ".$detalle[$i]['valorneto']; ?></td>

 <?php if ($_SESSION['acceso'] == "administradorS") { ?><td>
<button type="button" class="btn btn-rounded btn-dark" onClick="EliminarDetallesCotizacionesUpdate('<?php echo base64_encode($detalle[$i]["coddetallecotizacion"]); ?>','<?php echo base64_encode($detalle[$i]["codcotizacion"]); ?>','<?php echo base64_encode($detalle[$i]["codsucursal"]); ?>','<?php echo base64_encode("DETALLESCOTIZACIONES") ?>')" title="Eliminar" ><i class="fa fa-trash-o"></i></button></td><?php } ?>
                                 </tr>
                     <?php } ?>
                </tbody>
            </table>
                                <div class="col-md-12">
                                    <div class="pull-right text-right">
<p><b>Total Grabado <?php echo $reg[0]['iva'] ?>%:</b> <?php echo $simbolo." ".number_format($reg[0]['subtotalivasi'], 2, '.', ','); ?><p>
<p><b>Total Exento 0%:</b> <?php echo $simbolo." ".number_format($reg[0]['subtotalivano'], 2, '.', ','); ?></p>
<p><b>Total <?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?> (<?php echo $reg[0]['iva']; ?>%):</b> <?php echo $simbolo." ".number_format($reg[0]['totaliva'], 2, '.', ','); ?> </p>
<p><b>Desc. Global (<?php echo $reg[0]['descuento']; ?>%):</b> <?php echo $simbolo." ".number_format($reg[0]['totaldescuento'], 2, '.', ','); ?> </p>
                                        <hr>
<h3><b>Importe Total :</b> <?php echo $simbolo." ".number_format($reg[0]['totalpago'], 2, '.', ','); ?></h3> 
                                     </div>
                                </div>
                    </div>
      </div>

            <hr>

        <input type="hidden" name="codproducto" id="codproducto">
        <input type="hidden" name="producto" id="producto">
        <input type="hidden" name="marcas" id="marcas">
        <input type="hidden" name="modelos" id="modelos">
        <input type="hidden" name="preciocompra" id="preciocompra"> 
        <input type="hidden" name="precioconiva" id="precioconiva">
        <input type="hidden" name="ivaproducto" id="ivaproducto">

        <h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle del Comprobante</h3><hr>

        <div class="row">
            <div class="col-md-4"> 
                <div class="form-group has-feedback"> 
                  <label class="control-label">Realice la Búsqueda de Producto: <span class="symbol required"></span></label>
                  <input type="text" class="form-control" name="busquedaproductov" id="busquedaproductov" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Realice la Búsqueda por Código, Descripción o Nº de Barra">
                  <i class="fa fa-search form-control-feedback"></i> 
                </div> 
            </div>

            <div class="col-md-2"> 
                <div class="form-group has-feedback"> 
                 <label class="control-label">Precio Unitario: <span class="symbol required"></span></label>
                 <input class="form-control" type="text" name="precioventa" id="precioventa" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Precio Unitario" disabled="disabled" readonly="readonly">
                 <i class="fa fa-tint form-control-feedback"></i> 
               </div> 
            </div> 

            <div class="col-md-2"> 
                <div class="form-group has-feedback"> 
                 <label class="control-label">Stock Actual: <span class="symbol required"></span></label>
                 <input type="text" class="form-control" name="existencia" id="existencia" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Existencia" disabled="disabled" readonly="readonly">
                 <i class="fa fa-bolt form-control-feedback"></i> 
              </div> 
            </div>  

            <div class="col-md-2"> 
                <div class="form-group has-feedback"> 
                    <label class="control-label">Descuento: <span class="symbol required"></span></label>
                    <input class="form-control agregacotizacion" type="text" name="descproducto" id="descproducto" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Descuento">
                    <i class="fa fa-tint form-control-feedback"></i> 
                </div> 
            </div>

            <div class="col-md-2"> 
                <div class="form-group has-feedback"> 
                 <label class="control-label">Cantidad: <span class="symbol required"></span></label>
                 <input type="text" class="form-control agregacotizacion" name="cantidad" id="cantidad" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Cantidad">
                 <i class="fa fa-bolt form-control-feedback"></i> 
                </div> 
            </div>
        </div>

        <div class="pull-right">
    <button type="button" id="AgregaCotizacion" class="btn btn-info"><span class="fa fa-cart-plus"></span> Agregar</button>
        </div></br>

        <div class="table-responsive m-t-40">
            <table id="carrito" class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Precio Unit.</th>
                        <th>Valor Total</th>
                        <th>Desc %</th>
                        <th><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?></th>
                        <th>Valor Neto</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan=9><h4>NO HAY DETALLES AGREGADOS</h4></td>
                    </tr>
                </tbody>
              </table><hr>

             <table id="carritototal" class="table-responsive">
                <tr>
    <td width="50">&nbsp;</td>
    <td width="250">
    <h5><label>Total Gravado <?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>%:</label></h5>
    </td>
                  
    <td width="250">
    <h5><?php echo $simbolo; ?> <label id="lblsubtotal" name="lblsubtotal">0.00</label></h5>
    <input type="hidden" name="txtsubtotal" id="txtsubtotal" value="0.00"/>
    </td>

    <td width="250">
    <h5><label>Total Exento 0%:</label></h5>
    </td>
    
    <td width="250">
    <h5><?php echo $simbolo; ?> <label id="lblsubtotal2" name="lblsubtotal2">0.00</label></h5>
    <input type="hidden" name="txtsubtotal2" id="txtsubtotal2" value="0.00"/>
    </td>

    <td class="text-center" width="250">
    <h2><b>Importe Total</b></h2>
    </td>
                </tr>
                <tr>
    <td>&nbsp;</td>
    <td>
    <h5><label><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?> <?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>%:<input type="hidden" name="iva" id="iva" autocomplete="off" value="<?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>"></label></h5>
    </td>
    
    <td>
    <h5><?php echo $simbolo; ?> <label id="lbliva" name="lbliva">0.00</label></h5>
    <input type="hidden" name="txtIva" id="txtIva" value="0.00"/>
    </td>

    <td>
    <h5><label>Desc. Global <input class="number" type="text" name="descuento" id="descuento" onKeyPress="EvaluateText('%f', this);" style="border-radius:4px;height:30px;width:60px;" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="<?php echo $_SESSION['descsucursal'] ?>">%:</label></h5>
    </td>

    <td>
    <h5><?php echo $simbolo; ?> <label id="lbldescuento" name="lbldescuento">0.00</label></h5>
    <input type="hidden" name="txtDescuento" id="txtDescuento" value="0.00"/>
    </td>

    <td class="text-center">
    <h2><b><?php echo $simbolo; ?> <label id="lbltotal" name="lbltotal">0.00</label></b></h2>
    <input type="hidden" name="txtTotal" id="txtTotal" value="0.00"/>
    <input type="hidden" name="txtTotalCompra" id="txtTotalCompra" value="0.00"/>
    </td>
                    </tr>
                  </table>
        </div>


<?php } else { ?>

        <input type="hidden" name="codproducto" id="codproducto">
        <input type="hidden" name="producto" id="producto">
        <input type="hidden" name="marcas" id="marcas">
        <input type="hidden" name="modelos" id="modelos">
        <input type="hidden" name="preciocompra" id="preciocompra"> 
        <input type="hidden" name="precioconiva" id="precioconiva">
        <input type="hidden" name="ivaproducto" id="ivaproducto">

        <h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle del Comprobante</h3><hr>

        <div class="row">
            <div class="col-md-4"> 
                <div class="form-group has-feedback"> 
                  <label class="control-label">Realice la Búsqueda de Producto: <span class="symbol required"></span></label>
                  <input type="text" class="form-control" name="busquedaproductov" id="busquedaproductov" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Realice la Búsqueda por Código, Descripción o Nº de Barra">
                  <i class="fa fa-search form-control-feedback"></i> 
                </div> 
            </div>

            <div class="col-md-2"> 
                <div class="form-group has-feedback"> 
                 <label class="control-label">Precio Unitario: <span class="symbol required"></span></label>
                 <input class="form-control" type="text" name="precioventa" id="precioventa" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Precio Unitario" disabled="disabled" readonly="readonly">
                 <i class="fa fa-tint form-control-feedback"></i> 
               </div> 
            </div> 

            <div class="col-md-2"> 
                <div class="form-group has-feedback"> 
                 <label class="control-label">Stock Actual: <span class="symbol required"></span></label>
                 <input type="text" class="form-control" name="existencia" id="existencia" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Existencia" disabled="disabled" readonly="readonly">
                 <i class="fa fa-bolt form-control-feedback"></i> 
              </div> 
            </div>  

            <div class="col-md-2"> 
                <div class="form-group has-feedback"> 
                    <label class="control-label">Descuento: <span class="symbol required"></span></label>
                    <input class="form-control agregacotizacion" type="text" name="descproducto" id="descproducto" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Descuento">
                    <i class="fa fa-tint form-control-feedback"></i> 
                </div> 
            </div>

            <div class="col-md-2"> 
                <div class="form-group has-feedback"> 
                 <label class="control-label">Cantidad: <span class="symbol required"></span></label>
                 <input type="text" class="form-control agregacotizacion" name="cantidad" id="cantidad" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Cantidad">
                 <i class="fa fa-bolt form-control-feedback"></i> 
                </div> 
            </div>
        </div>

        <div class="pull-right">
    <button type="button" id="AgregaCotizacion" class="btn btn-info"><span class="fa fa-cart-plus"></span> Agregar</button>
        </div></br>

        <div class="table-responsive m-t-40">
            <table id="carrito" class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Precio Unit.</th>
                        <th>Valor Total</th>
                        <th>Desc %</th>
                        <th><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?></th>
                        <th>Valor Neto</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan=9><h4>NO HAY DETALLES AGREGADOS</h4></td>
                    </tr>
                </tbody>
              </table><hr>

             <table id="carritototal" class="table-responsive">
                <tr>
    <td width="50">&nbsp;</td>
    <td width="250">
    <h5><label>Total Gravado <?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>%:</label></h5>
    </td>
                  
    <td width="250">
    <h5><?php echo $simbolo; ?> <label id="lblsubtotal" name="lblsubtotal">0.00</label></h5>
    <input type="hidden" name="txtsubtotal" id="txtsubtotal" value="0.00"/>
    </td>

    <td width="250">
    <h5><label>Total Exento 0%:</label></h5>
    </td>
    
    <td width="250">
    <h5><?php echo $simbolo; ?> <label id="lblsubtotal2" name="lblsubtotal2">0.00</label></h5>
    <input type="hidden" name="txtsubtotal2" id="txtsubtotal2" value="0.00"/>
    </td>

    <td class="text-center" width="250">
    <h2><b>Importe Total</b></h2>
    </td>
                </tr>
                <tr>
    <td>&nbsp;</td>
    <td>
    <h5><label><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?> <?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>%:<input type="hidden" name="iva" id="iva" autocomplete="off" value="<?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>"></label></h5>
    </td>
    
    <td>
    <h5><?php echo $simbolo; ?> <label id="lbliva" name="lbliva">0.00</label></h5>
    <input type="hidden" name="txtIva" id="txtIva" value="0.00"/>
    </td>

    <td>
    <h5><label>Desc. Global <input class="number" type="text" name="descuento" id="descuento" onKeyPress="EvaluateText('%f', this);" style="border-radius:4px;height:30px;width:60px;" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="<?php echo $_SESSION['descsucursal'] ?>">%:</label></h5>
    </td>

    <td>
    <h5><?php echo $simbolo; ?> <label id="lbldescuento" name="lbldescuento">0.00</label></h5>
    <input type="hidden" name="txtDescuento" id="txtDescuento" value="0.00"/>
    </td>

    <td class="text-center">
    <h2><b><?php echo $simbolo; ?> <label id="lbltotal" name="lbltotal">0.00</label></b></h2>
    <input type="hidden" name="txtTotal" id="txtTotal" value="0.00"/>
    <input type="hidden" name="txtTotalCompra" id="txtTotalCompra" value="0.00"/>
    </td>
                    </tr>
                  </table>
        </div>


<?php } ?> 

<div class="clearfix"></div>
<hr>
              <div class="text-right">
<?php  if (isset($_GET['codcotizacion']) && base64_decode($_GET["proceso"])=="U") { ?>
<button type="submit" name="btn-update" id="btn-update" class="btn btn-danger"><span class="fa fa-edit"></span> Actualizar</button>
<button class="btn btn-dark" type="reset"><span class="fa fa-trash-o"></span> Cancelar</button> 
<?php } else if (isset($_GET['codcotizacion']) && base64_decode($_GET["proceso"])=="A") { ?>  
<button type="submit" name="btn-agregar" id="btn-agregar" class="btn btn-danger"><span class="fa fa-plus"></span> Agregar</button>
<button class="btn btn-dark" type="button" id="vaciar2"><span class="fa fa-trash-o"></span> Cancelar</button>
<?php } else { ?>  

<button type="button" class="btn btn-danger waves-effect waves-light" data-placement="left" title="Cobrar Venta" data-original-title="" data-href="#" data-toggle="modal" data-target="#myModal2" data-backdrop="static" data-keyboard="false"><span class="fa fa-save"></span> Cobrar</button>
<button class="btn btn-dark" type="button" id="vaciar"><i class="fa fa-trash-o"></i> Limpiar</button>
<?php } ?>
</div>

          </div>
       </div>
     </form>
   </div>
  </div>
<!--</div>
 End Row -->

<!-- Row 
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header bg-danger">
                <h4 class="card-title text-white"><i class="fa fa-tasks"></i> Medios de Pagos</h4>
            </div>

            <div class="form-body">

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                          <div class="btn-group m-b-20">
                            <a class="btn waves-effect waves-light btn-light" href="reportepdf?tipo=<?php echo base64_encode("MEDIOSPAGOS") ?>" target="_blank" rel="noopener noreferrer"  data-toggle="tooltip" data-placement="bottom" title="Exportar Pdf"><font color="black"><span class="fa fa-file-pdf-o"></span> Pdf</font></a>

                            <a class="btn waves-effect waves-light btn-light" href="reporteexcel?tipo=<?php echo base64_encode("MEDIOSPAGOS") ?>" data-toggle="tooltip" data-placement="bottom" title="Exportar Excel"><font color="black"><span class="fa fa-file-excel-o"></span> Excel</font></a>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese Criterio para tu Búsqueda" aria-describedby="basic-addon1" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" required="" aria-required="true">
                        </div>
                    </div>
                </div>

                <div id="delete"></div>
                <div id="mediospagos"></div>

            </div>
        </div>
     </div>
  </div>-->
</div>
<!-- End Row -->
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
                <i class="fa fa-copyright"></i> <span class="current-year"></span>.
            </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="assets/js/jquery.min.js"></script> 
    <script src="assets/script/jquery.min.js"></script> 
    <script src="assets/js/bootstrap.js"></script>
    <!-- apps -->
    <script src="assets/js/app_002.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="assets/js/perfect-scrollbar.js"></script>
    <script src="assets/js/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="assets/js/waves.js"></script>
    <!-- Sweet-Alert -->
    <script src="assets/js/sweetalert-dev.js"></script>
    <!--Menu sidebar -->
    <script src="assets/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="assets/js/custom.js"></script>

    <!-- script jquery -->
    <script type="text/javascript" src="assets/script/titulos.js"></script>
    <script type="text/javascript" src="assets/script/jquery.mask.js"></script>
    <script type="text/javascript" src="assets/script/script2.js"></script>
    <script type="text/javascript" src="assets/script/jscotizaciones.js"></script>
    <script type="text/javascript" src="assets/script/validation.min.js"></script>
    <script type="text/javascript" src="assets/script/script.js"></script>
    <!-- script jquery -->

    <!-- Calendario -->
    <link rel="stylesheet" href="assets/calendario/jquery-ui.css" />
    <script src="assets/calendario/jquery-ui.js"></script>
    <script src="assets/script/jscalendario.js"></script>
    <script src="assets/script/autocompleto.js"></script>
    <!-- Calendario -->

    <!-- jQuery -->
    <script src="assets/script/jquery.dashboard.js"></script>
    <script src="assets/plugins/noty/packaged/jquery.noty.packaged.min.js"></script>
    <!-- jQuery -->
    
  </body>
</html>

<?php } else { ?>   
        <script type='text/javascript' language='javascript'>
        alert('NO TIENES PERMISO PARA ACCEDER A ESTA PAGINA.\nCONSULTA CON EL ADMINISTRADOR PARA QUE TE DE ACCESO')  
        document.location.href='panel'   
        </script> 
<?php } } else { ?>
        <script type='text/javascript' language='javascript'>
        alert('NO TIENES PERMISO PARA ACCEDER AL SISTEMA.\nDEBERA DE INICIAR SESION')  
        document.location.href='logout'  
        </script> 
<?php } ?>