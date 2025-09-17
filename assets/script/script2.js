//SELECCIONAR/DESELECCIONAR TODOS LOS CHECKBOX
$("#checkTodos").change(function () {
      $("input:checkbox").prop('checked', $(this).prop("checked"));
      //$("input[type='checkbox']:checked:enabled").prop('checked', $(this).prop("checked"));
  });

// FUNCION PARA LIMPIAR CHECKBOX ACTIVOS
function LimpiarCheckbox(){
$("input[type='checkbox']:checked:enabled").attr('checked',false); 
}

//BUSQUEDA EN CONSULTAS
$(document).ready(function () {
   (function($) {
       $('#FiltrarContenido').keyup(function () {
            var ValorBusqueda = new RegExp($(this).val(), 'i');
            $('.BusquedaRapida tr').hide();
             $('.BusquedaRapida tr').filter(function () {
                return ValorBusqueda.test($(this).text());
              }).show();
                })
      }(jQuery));
});















/////////////////////////////////// FUNCIONES DE USUARIOS //////////////////////////////////////

// FUNCION PARA MOSTRAR USUARIOS EN VENTANA MODAL
function VerUsuario(codigo){

$('#muestrausuariomodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaUsuarioModal=si&codigo='+codigo;

$.ajax({
            type: "GET",
                  url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestrausuariomodal').empty();
                $('#muestrausuariomodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR USUARIOS
function UpdateUsuario(codigo,dni,nombres,sexo,direccion,telefono,email,usuario,nivel,status,comision,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#saveuser #codigo").val(codigo);
  $("#saveuser #dni").val(dni);
  $("#saveuser #nombres").val(nombres);
  $("#saveuser #sexo").val(sexo);
  $("#saveuser #direccion").val(direccion);
  $("#saveuser #telefono").val(telefono);
  $("#saveuser #email").val(email);
  $("#saveuser #usuario").val(usuario);
  $("#saveuser #nivel").val(nivel);
  $("#saveuser #status").val(status);
  $("#saveuser #comision").val(comision);
  $("#saveuser #proceso").val(proceso);
}


/////FUNCION PARA ELIMINAR USUARIOS 
function EliminarUsuario(codigo,dni,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Usuario?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codigo="+codigo+"&dni="+dni+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $("#usuarios").load("consultas.php?CargaUsuarios=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Usuario no puede ser Eliminado, tiene registros relacionados!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Usuarios, no eres el Administrador del Sistema!", "error"); 

                }

            }
        })
    });
}











/////////////////////////////////// FUNCIONES DE PROVINCIAS //////////////////////////////////////

// FUNCION PARA ACTUALIZAR PROVINCIAS
function UpdateProvincia(id_provincia,provincia,proceso) 
{
  // aqui asigno cada valor a los campos correspondientes
  $("#saveprovincias #id_provincia").val(id_provincia);
  $("#saveprovincias #provincia").val(provincia);
  $("#saveprovincias #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR PROVINCIAS 
function EliminarProvincia(id_provincia,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Provincia?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "id_provincia="+id_provincia+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#provincias').load("consultas?CargaProvincias=si");
                  
          } else if(data==2){ 

             swal("Oops", "Esta Provincia no puede ser Eliminada, tiene Departamentos relacionados!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Provincias, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}











/////////////////////////////////// FUNCIONES DE DEPARTAMENTOS //////////////////////////////////////

// FUNCION PARA ACTUALIZAR DEPARTAMENTOS
function UpdateDepartamento(id_departamento,departamento,id_provincia,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savedepartamentos #id_departamento").val(id_departamento);
  $("#savedepartamentos #departamento").val(departamento);
  $("#savedepartamentos #id_provincia").val(id_provincia);
  $("#savedepartamentos #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR DEPARTAMENTOS 
function EliminarDepartamento(id_departamento,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Departamento de Provincia?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "id_departamento="+id_departamento+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#departamentos').load("consultas?CargaDepartamentos=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Departamento no puede ser Eliminado, tiene registros relacionados!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Departamento, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}

////FUNCION PARA MOSTRAR PROVINCIAS POR DEPARTAMENTOS
function CargaDepartamentos(id_provincia){

$('#id_departamento').html('<center><img src="assets/images/loading.gif" width="30" height="30"/></center>');
                
var dataString = 'BuscaDepartamentos=si&id_provincia='+id_provincia;

$.ajax({
            type: "GET",
                  url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#id_departamento').empty();
                $('#id_departamento').append(''+response+'').fadeIn("slow");
                
           }
      });
}




////FUNCION PARA MOSTRAR PROVINCIAS POR DEPARTAMENTOS #2
function CargaDepartamentos2(id_provincia2){

$('#id_departamento2').html('<center><img src="assets/images/loading.gif" width="30" height="30"/></center>');
                
var dataString = 'BuscaDepartamentos2=si&id_provincia2='+id_provincia2;

$.ajax({
            type: "GET",
                  url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#id_departamento2').empty();
                $('#id_departamento2').append(''+response+'').fadeIn("slow");
                
           }
      });
}

////FUNCION PARA MOSTRAR LOCALIDAD POR CIUDAD
function SelectDepartamento(id_provincia,id_departamento){

  $("#id_departamento").load("funciones.php?SeleccionaDepartamento=si&id_provincia="+id_provincia+"&id_departamento="+id_departamento);

}











/////////////////////////////////// FUNCIONES DE TIPOS DE DOCUMENTOS  //////////////////////////////////////

// FUNCION PARA ACTUALIZAR TIPOS DE DOCUMENTOS
function UpdateDocumento(coddocumento,documento,descripcion,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savedocumentos #coddocumento").val(coddocumento);
  $("#savedocumentos #documento").val(documento);
  $("#savedocumentos #descripcion").val(descripcion);
  $("#savedocumentos #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR TIPOS DE DOCUMENTOS 
function EliminarDocumento(coddocumento,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Tipo de Documento?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "coddocumento="+coddocumento+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#documentos').load("consultas?CargaDocumentos=si");
                  
           } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Tipos de Documentos, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}











/////////////////////////////////// FUNCIONES DE TIPOS DE MONEDA //////////////////////////////////////

// FUNCION PARA ACTUALIZAR TIPOS DE MONEDA
function UpdateTipoMoneda(codmoneda,moneda,siglas,simbolo,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savemonedas #codmoneda").val(codmoneda);
  $("#savemonedas #moneda").val(moneda);
  $("#savemonedas #siglas").val(siglas);
  $("#savemonedas #simbolo").val(simbolo);
  $("#savemonedas #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR TIPOS DE MONEDA 
function EliminarTipoMoneda(codmoneda,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Tipo de Moneda?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codmoneda="+codmoneda+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#monedas').load("consultas?CargaMonedas=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Tipo de Moneda no puede ser Eliminado, tiene Tipos de Cambio relacionadas!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Tipos de Moneda, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}











/////////////////////////////////// FUNCIONES DE TIPOS DE CAMBIO  //////////////////////////////////////

// FUNCION PARA ACTUALIZAR TIPOS DE CAMBIO
function UpdateTipoCambio(codcambio,descripcioncambio,montocambio,codmoneda,fechacambio,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savecambios #codcambio").val(codcambio);
  $("#savecambios #descripcioncambio").val(descripcioncambio);
  $("#savecambios #montocambio").val(montocambio);
  $("#savecambios #codmoneda").val(codmoneda);
  $("#savecambios #fechacambio").val(fechacambio);
  $("#savecambios #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR TIPOS DE CAMBIO 
function EliminarTipoCambio(codcambio,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Tipo de Cambio?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codcambio="+codcambio+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#cambios').load("consultas?CargaCambios=si");
                  
           } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Tipos de Cambio, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}











/////////////////////////////////// FUNCIONES DE MEDIOS DE PAGOS //////////////////////////////////////

// FUNCION PARA ACTUALIZAR MEDIOS DE PAGOS
function UpdateMedio(codmediopago,mediopago,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savemedios #codmediopago").val(codmediopago);
  $("#savemedios #mediopago").val(mediopago);
  $("#savemedios #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR MEDIOS DE PAGOS 
function EliminarMedio(codmediopago,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Medio de Pago?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codmediopago="+codmediopago+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#mediospagos').load("consultas?CargaMediosPagos=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Medio de Pago no puede ser Eliminado, tiene Ventas relacionados!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Medios de Pagos, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}












/////////////////////////////////// FUNCIONES DE IMPUESTOS //////////////////////////////////////

// FUNCION PARA ACTUALIZAR IMPUESTOS
function UpdateImpuesto(codimpuesto,nomimpuesto,valorimpuesto,statusimpuesto,fechaimpuesto,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#saveimpuestos #codimpuesto").val(codimpuesto);
  $("#saveimpuestos #nomimpuesto").val(nomimpuesto);
  $("#saveimpuestos #valorimpuesto").val(valorimpuesto);
  $("#saveimpuestos #statusimpuesto").val(statusimpuesto);
  $("#saveimpuestos #fechaimpuesto").val(fechaimpuesto);
  $("#saveimpuestos #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR IMPUESTOS
function EliminarImpuesto(codimpuesto,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Impuesto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codimpuesto="+codimpuesto+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#impuestos').load("consultas?CargaImpuestos=si");
                  
          } else if(data==2){ 

             swal("Oops", "Usted no tiene Acceso para Eliminar Impuestos, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}














/////////////////////////////////// FUNCIONES DE SALAS //////////////////////////////////////

// FUNCION PARA ACTUALIZAR SALAS
function UpdateSala(codsala,nomsala,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savesalas #codsala").val(codsala);
  $("#savesalas #nomsala").val(nomsala);
  $("#savesalas #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR SALAS 
function EliminarSala(codsala,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Sala?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codsala="+codsala+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#salas').load("consultas.php?CargaSalas=si");
            $("#savesalas")[0].reset();

          } else if(data==2) { 

             swal("Oops", "Esta Salas no puede ser Eliminada, tiene registros relacionados!", "error"); 

           } else {  

             swal("Oops", "Usted no tiene Acceso para Eliminar Salas, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}















/////////////////////////////////// FUNCIONES DE SALAS //////////////////////////////////////

// FUNCION PARA ACTUALIZAR SALAS
function UpdateMesa(codmesa,codsala,nommesa,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savemesas #codmesa").val(codmesa);
  $("#savemesas #codsala").val(codsala);
  $("#savemesas #nommesa").val(nommesa);
  $("#savemesas #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR SALAS 
function EliminarMesa(codmesa,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Mesa en Sala?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codmesa="+codmesa+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#mesas').load("consultas.php?CargaMesas=si");
            $("#savemesas")[0].reset();

          } else if(data==2) { 

             swal("Oops", "Esta Mesa no puede ser Eliminada, tiene registros relacionados!", "error"); 

           } else {  

             swal("Oops", "Usted no tiene Acceso para Eliminar Mesas en Salas, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}












/////////////////////////////////// FUNCIONES DE CATEGORIAS //////////////////////////////////////

// FUNCION PARA ACTUALIZAR CATEGORIAS
function UpdateCategoria(codcategoria,nomcategoria,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savecategorias #codcategoria").val(codcategoria);
  $("#savecategorias #nomcategoria").val(nomcategoria);
  $("#savecategorias #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR CATEGORIAS 
function EliminarCategoria(codcategoria,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Categoria de Producto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codcategoria="+codcategoria+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#categorias').load("consultas.php?CargaCategorias=si");
            $("#savecategorias")[0].reset();

          } else if(data==2) { 

             swal("Oops", "Esta Categoria no puede ser Eliminada, tiene registros relacionados!", "error"); 

           } else {  

             swal("Oops", "Usted no tiene Acceso para Eliminar Categorias de Productos, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}















/////////////////////////////////// FUNCIONES DE MEDIDAS //////////////////////////////////////

// FUNCION PARA ACTUALIZAR MEDIDAS
function UpdateMedida(codmedida,nommedida,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savemedidas #codmedida").val(codmedida);
  $("#savemedidas #nommedida").val(nommedida);
  $("#savemedidas #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR UNIDADES 
function EliminarMedida(codmedida,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Medida?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codmedida="+codmedida+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#medidas').load("consultas.php?CargaMedidas=si");
            $("#savemedidas")[0].reset();

          } else if(data==2) { 

             swal("Oops", "Esta Medida no puede ser Eliminada, tiene registros relacionados!", "error"); 

           } else {  

             swal("Oops", "Usted no tiene Acceso para Eliminar Medidas, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}











/////////////////////////////////// FUNCIONES DE CLIENTES //////////////////////////////////////

// FUNCION PARA MOSTRAR DIV DE CARGA MASIVA DE CLIENTES
function CargaDivClientes(){

$('#divcliente').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');
                
var dataString = 'BuscaDivCliente=si';

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#divcliente').empty();
                $('#divcliente').append(''+response+'').fadeIn("slow");
                
           }
      });
}

// FUNCION PARA LIMPIAR DIV DE CARGA MASIVA DE CLIENTES
function ModalCliente(){
  $("#divcliente").html("");
}

// FUNCION PARA MOSTRAR CLIENTES EN VENTANA MODAL
function VerCliente(codcliente){

$('#muestraclientemodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaClienteModal=si&codcliente='+codcliente;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestraclientemodal').empty();
                $('#muestraclientemodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR CLIENTES
function UpdateCliente(codcliente,documcliente,dnicliente,nomcliente,tlfcliente,id_provincia,
  direccliente,emailcliente,tipocliente,limitecredito,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#saveclientes #codcliente").val(codcliente);
  $("#saveclientes #documcliente").val(documcliente);
  $("#saveclientes #dnicliente").val(dnicliente);
  $("#saveclientes #nomcliente").val(nomcliente);
  $("#saveclientes #tlfcliente").val(tlfcliente);
  $("#saveclientes #id_provincia").val(id_provincia);
  $("#saveclientes #direccliente").val(direccliente);
  $("#saveclientes #emailcliente").val(emailcliente);
  $("#saveclientes #tipocliente").val(tipocliente);
  $("#saveclientes #limitecredito").val(limitecredito);
  $("#saveclientes #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR CLIENTES 
function EliminarCliente(codcliente,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Cliente?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codcliente="+codcliente+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#clientes').load("consultas.php?CargaClientes=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Cliente no puede ser Eliminado, tiene Ventas relacionados!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Clientes, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}











/////////////////////////////////// FUNCIONES DE PROVEEDORES //////////////////////////////////////

// FUNCION PARA MOSTRAR DIV DE CARGA MASIVA DE PROVEEDORES
function CargaDivProveedores(){

$('#divproveedor').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');
                
var dataString = 'BuscaDivProveedor=si';

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#divproveedor').empty();
                $('#divproveedor').append(''+response+'').fadeIn("slow");
                
           }
      });
}


// FUNCION PARA LIMPIAR DIV DE CARGA MASIVA DE PROVEEDORES
function ModalProveedor(){
  $("#divproveedor").html("");
}

// FUNCION PARA MOSTRAR PROVEEDORES EN VENTANA MODAL
function VerProveedor(codproveedor){

$('#muestraproveedormodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaProveedorModal=si&codproveedor='+codproveedor;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestraproveedormodal').empty();
                $('#muestraproveedormodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR PROVEEDORES
function UpdateProveedor(codproveedor,documproveedor,cuitproveedor,nomproveedor,tlfproveedor,id_provincia,
  direcproveedor,emailproveedor,vendedor,tlfvendedor,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#saveproveedores #codproveedor").val(codproveedor);
  $("#saveproveedores #documproveedor").val(documproveedor);
  $("#saveproveedores #cuitproveedor").val(cuitproveedor);
  $("#saveproveedores #nomproveedor").val(nomproveedor);
  $("#saveproveedores #tlfproveedor").val(tlfproveedor);
  $("#saveproveedores #id_provincia").val(id_provincia);
  $("#saveproveedores #direcproveedor").val(direcproveedor);
  $("#saveproveedores #emailproveedor").val(emailproveedor);
  $("#saveproveedores #vendedor").val(vendedor);
  $("#saveproveedores #tlfvendedor").val(tlfvendedor);
  $("#saveproveedores #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR PROVEEDORES 
function EliminarProveedor(codproveedor,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Proveedor?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codproveedor="+codproveedor+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#proveedores').load("consultas.php?CargaProveedores=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Proveedor no puede ser Eliminado, tiene Productos relacionados!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Proveedores, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}












/////////////////////////////////// FUNCIONES DE INGREDIENTES //////////////////////////////////////

// FUNCION PARA MOSTRAR DIV DE CARGA MASIVA DE INGREDIENTES
function CargaDivIngredientes(){

$('#divingrediente').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');
                
var dataString = 'BuscaDivIngrediente=si';

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#divingrediente').empty();
                $('#divingrediente').append(''+response+'').fadeIn("slow");
                
           }
      });
}

// FUNCION PARA LIMPIAR DIV DE CARGA MASIVA DE INGREDIENTES
function ModalIngrediente(){
  $("#divingrediente").html("");
}

// FUNCION PARA MOSTRAR INGREDIENTES EN VENTANA MODAL
function VerIngrediente(codingrediente){

$('#muestraingredientemodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaIngredienteModal=si&codingrediente='+codingrediente;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestraingredientemodal').empty();
                $('#muestraingredientemodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR INGREDIENTES
function UpdateIngrediente(codingrediente) {

  swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Actualizar este Ingrediente?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Actualizar",
          confirmButtonColor: "#3085d6"
        }, function(isConfirm) {
    if (isConfirm) {
      location.href = "foringrediente?codingrediente="+codingrediente;
      // handle confirm
    } else {
      // handle all other cases
    }
  })
}


/////FUNCION PARA ELIMINAR DETALLE DE INGREDIENTES 
function EliminaDetalleIngredienteNuevo(codproducto,codingrediente,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Ingrediente del Producto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codproducto="+codproducto+"&codingrediente="+codingrediente+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $("#cargaingredientes").load("funciones.php?BuscaIngredienteNuevo=si&codproducto="+codproducto);

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Ingrediente, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}



/////FUNCION PARA ELIMINAR DETALLE DE INGREDIENTES 
function EliminaDetalleIngredienteAgrega(codproducto,codingrediente,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Ingrediente del Producto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codproducto="+codproducto+"&codingrediente="+codingrediente+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $("#cargaingredientes").load("funciones.php?BuscaIngredienteAgregar=si&codproducto="+codproducto);

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Ingrediente, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}
/////FUNCION PARA ELIMINAR INGREDIENTES 
function EliminarIngrediente(codingrediente,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Ingrediente?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codingrediente="+codingrediente+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#ingredientes').load("consultas.php?CargaIngredientes=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Ingrediente no puede ser Eliminado, tiene Productos relacionadas!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Ingrediente, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}


// FUNCION PARA BUSQUEDA DE KARDEX POR INGREDIENTES
function BuscaKardexIngredientes(){

$('#muestrakardex').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var codingrediente = $("#codingrediente").val();
var dataString = $("#buscakardexingredientes").serialize();
var url = 'funciones.php?BuscaKardexIngrediente=si';

        $.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {
                $('#muestrakardex').empty();
                $('#muestrakardex').append(''+response+'').fadeIn("slow");
                
            }
      }); 
}

// FUNCION PARA BUSQUEDA DE INGREDIENTES VENDIDOS
function BuscaIngredientesVendidos(){
    
$('#muestraingredientesvendidos').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var desde = $("input#desde").val();
var hasta = $("input#hasta").val();
var dataString = $("#ingredientesvendidos").serialize();
var url = 'funciones.php?BuscaIngredientesVendidos=si';

        $.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {
                $('#muestraingredientesvendidos').empty();
                $('#muestraingredientesvendidos').append(''+response+'').fadeIn("slow");
                
            }
      }); 
}










/////////////////////////////////// FUNCIONES DE PRODUCTOS //////////////////////////////////////

//FUNCION PARA CALCULAR PRECIO VENTA
$(document).ready(function (){
          $('.calculoprecio').keyup(function (){
        
            var precio = $('input#preciocompra').val();
            var porcentaje = $('input#porcentaje').val()/100;

            //REALIZO EL CALCULO
            var calculo = parseFloat(precio)*parseFloat(porcentaje);
            precioventa = parseFloat(calculo)+parseFloat(precio);
            $("#precioventa").val((porcentaje == "0.00") ? "" : precioventa.toFixed(2));

      });
 }); 

// FUNCION PARA MOSTRAR DIV DE CARGA MASIVA DE PRODUCTOS
function CargaDivProductos(){

$('#divproducto').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');
                
var dataString = 'BuscaDivProducto=si';

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#divproducto').empty();
                $('#divproducto').append(''+response+'').fadeIn("slow");
                
           }
      });
}

// FUNCION PARA LIMPIAR DIV DE CARGA MASIVA DE PRODUCTOS
function ModalProducto(){
  $("#divproducto").html("");
}

// FUNCION PARA MOSTRAR PRODUCTOS EN VENTANA MODAL
function VerProducto(codproducto){

$('#muestraproductomodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaProductoModal=si&codproducto='+codproducto;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestraproductomodal').empty();
                $('#muestraproductomodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR PRODUCTOS
function UpdateProducto(codproducto) {

  swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Actualizar este Producto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Actualizar",
          confirmButtonColor: "#3085d6"
        }, function(isConfirm) {
    if (isConfirm) {
      location.href = "forproducto?codproducto="+codproducto;
      // handle confirm
    } else {
      // handle all other cases
    }
  })
}

// FUNCION PARA AGREGAR INGREDIENTES A PRODUCTOS
function AgregaIngrediente(codproducto) {

  swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Agregar Ingredientes a este Producto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Continuar",
          confirmButtonColor: "#3085d6"
        }, function(isConfirm) {
    if (isConfirm) {
      location.href = "foragrega?codproducto="+codproducto;
      // handle confirm
    } else {
      // handle all other cases
    }
  })
}

/////FUNCION PARA ELIMINAR PRODUCTOS 
function EliminarProducto(codproducto,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Producto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codproducto="+codproducto+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#productos').load("consultas.php?CargaProductos=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Producto no puede ser Eliminado, tiene Ventas relacionadas!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Productos, no eres el Administrador del Sistema!", "error"); 

                }
            }
        })
    });
}


// FUNCION PARA BUSQUEDA DE KARDEX POR PRODUCTOS
function BuscaKardexProductos(){

$('#muestrakardex').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var codproducto = $("#codproducto").val();
var dataString = $("#buscakardexproductos").serialize();
var url = 'funciones.php?BuscaKardexProducto=si';

        $.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {
                $('#muestrakardex').empty();
                $('#muestrakardex').append(''+response+'').fadeIn("slow");
                
            }
      }); 
}

// FUNCION PARA BUSQUEDA DE PRODUCTOS VENDIDOS
function BuscaProductosVendidos(){
    
$('#muestraproductosvendidos').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var desde = $("input#desde").val();
var hasta = $("input#hasta").val();
var dataString = $("#productosvendidos").serialize();
var url = 'funciones.php?BuscaProductosVendidos=si';

        $.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {
                $('#muestraproductosvendidos').empty();
                $('#muestraproductosvendidos').append(''+response+'').fadeIn("slow");
                
            }
      }); 
}

// FUNCION PARA BUSQUEDA DE PRODUCTOS POR MONEDA
function BuscaProductosxMoneda(){
    
$('#muestraproductosxmoneda').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var codmoneda = $("select#codmoneda").val();
var dataString = $("#productosxmoneda").serialize();
var url = 'funciones.php?BuscaProductosxMoneda=si';

        $.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {
                $('#muestraproductosxmoneda').empty();
                $('#muestraproductosxmoneda').append(''+response+'').fadeIn("slow");
                
            }
      }); 
}


// FUNCION PARA CARGAR PRODUCTOS POR FAMILIAS EN VENTANA MODAL
function CargaProductos(){

$('#loadproductos').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var dataString = "Productos_Familias=si";

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#loadproductos').empty();
                $('#loadproductos').append(''+response+'').fadeIn("slow");
                
            }
      });
}

















/////////////////////////////////// FUNCIONES DE COMPRAS //////////////////////////////////////

// FUNCION PARA MOSTRAR FORMA DE PAGO EN COMPRAS
function CargaFormaPagosCompras(){

  var valor = $("#tipocompra").val();

      if (valor === "" || valor === true) {
         
          $("#formacompra").attr('disabled', true);
          $("#fechavencecredito").attr('disabled', true);

      } else if (valor === "CONTADO" || valor === true) {
         
          $("#formacompra").attr('disabled', false);
          $("#fechavencecredito").attr('disabled', true);

      } else {

          $("#formacompra").attr('disabled', true);
          $("#fechavencecredito").attr('disabled', false);
      }
}

// FUNCION PARA MOSTRAR COMPRA PAGADA EN VENTANA MODAL
function VerCompraPagada(codcompra){

$('#muestracompramodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaCompraPagadaModal=si&codcompra='+codcompra;

$.ajax({
            type: "GET",
                  url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestracompramodal').empty();
                $('#muestracompramodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}


// FUNCION PARA MOSTRAR COMPRA PENDIENTE EN VENTANA MODAL
function VerCompraPendiente(codcompra){

$('#muestracompramodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaCompraPendienteModal=si&codcompra='+codcompra;

$.ajax({
            type: "GET",
                  url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestracompramodal').empty();
                $('#muestracompramodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR COMPRAS
function UpdateCompra(codcompra,proceso,status) {

  swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Actualizar esta Compra de Producto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Actualizar",
          confirmButtonColor: "#3085d6"
        }, function(isConfirm) {
    if (isConfirm) {
      location.href = "forcompra?codcompra="+codcompra+"&proceso="+proceso+"&status="+status;
      // handle confirm
    } else {
      // handle all other cases
    }
  })
}

/////FUNCION PARA ELIMINAR DETALLES DE COMPRAS PAGADAS EN VENTANA MODAL
function EliminarDetallesComprasPagadasModal(coddetallecompra,codcompra,codproveedor,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Detalle de Compra?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "coddetallecompra="+coddetallecompra+"&codcompra="+codcompra+"&codproveedor="+codproveedor+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#muestracompramodal').load("funciones.php?BuscaCompraPagadaModal=si&codcompra="+codcompra); 
            $('#compras').load("consultas.php?CargaCompras=si");

          } else if(data==2){ 

             swal("Oops", "No puede Eliminar todos los Detalles de Compras en este Módulo, realice la Eliminación completa de la Compra!", "error"); 

          } else { 

             swal("Oops", "No tiene Acceso para Eliminar Detalles de Compras, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}


/////FUNCION PARA ELIMINAR DETALLES DE COMPRAS PENDIENTES EN VENTANA MODAL
function EliminarDetallesComprasPendientesModal(coddetallecompra,codcompra,codproveedor,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Detalle de Compra?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "coddetallecompra="+coddetallecompra+"&codcompra="+codcompra+"&codproveedor="+codproveedor+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#muestracompramodal').load("funciones.php?BuscaCompraPendienteModal=si&codcompra="+codcompra); 
            $('#cuentasxpagar').load("consultas?CargaCuentasxPagar=si");

          } else if(data==2){ 

             swal("Oops", "No puede Eliminar todos los Detalles de Compras en este Módulo, realice la Eliminación completa de la Compra!", "error"); 

          } else { 

             swal("Oops", "No tiene Acceso para Eliminar Detalles de Compras, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}


/////FUNCION PARA ELIMINAR DETALLES DE COMPRAS EN ACTUALIZAR
function EliminarDetallesComprasUpdate(coddetallecompra,codcompra,codproveedor,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Detalle de Compra?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "coddetallecompra="+coddetallecompra+"&codcompra="+codcompra+"&codproveedor="+codproveedor+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#detallescomprasupdate').load("funciones.php?MuestraDetallesComprasUpdate=si&codcompra="+codcompra); 
          
          } else if(data==2){ 

             swal("Oops", "No puede Eliminar todos los Detalles de Compras en este Módulo, realice la Eliminación completa de la Compra!", "error"); 

          } else { 

             swal("Oops", "No tiene Acceso para Eliminar Detalles de Compras, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}

/////FUNCION PARA ELIMINAR COMPRAS 
function EliminarCompra(codcompra,codproveedor,status,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Compra?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codcompra="+codcompra+"&codproveedor="+codproveedor+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            if (status=="P") {
            $('#compras').load("consultas.php?CargaCompras=si");
            } else {
            $('#cuentasxpagar').load("consultas?CargaCuentasxPagar=si");
            }
            
                  
          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Compras de Productos, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}

/////FUNCION PARA PAGAR FACTURA DE COMPRAS 
function PagarCompra(codcompra,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Pagar Esta Factura de Compra?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Pagar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codcompra="+codcompra+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Factura Pagada!", "La Compra a sido Pagada con éxito!", "success");
            $('#cuentasxpagar').load("consultas.php?CargaCuentasxPagar=si");
                  
          } else { 

             swal("Oops", "Usted no tiene Acceso para Pagar Compras de Productos, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}


// FUNCION PARA BUSQUEDA DE COMPRAS POR PROVEEDORES
function BuscarComprasxProveedores(){
                        
$('#muestracomprasxproveedores').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');
                
var codproveedor = $("select#codproveedor").val();
var dataString = $("#comprasxproveedores").serialize();
var url = 'funciones.php?BuscaComprasxProvedores=si';


$.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {            
                $('#muestracomprasxproveedores').empty();
                $('#muestracomprasxproveedores').append(''+response+'').fadeIn("slow");
                
             }
      });
}


// FUNCION PARA BUSQUEDA DE COMPRAS POR FECHAS
function BuscarComprasxFechas(){
                        
$('#muestracomprasxfechas').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var desde = $("input#desde").val();
var hasta = $("input#hasta").val();
var dataString = $("#comprasxfechas").serialize();
var url = 'funciones.php?BuscaComprasxFechas=si';


$.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {            
                $('#muestracomprasxfechas').empty();
                $('#muestracomprasxfechas').append(''+response+'').fadeIn("slow");
                
             }
      });
}
















/////////////////////////////////// FUNCIONES DE CAJAS DE VENTAS //////////////////////////////////////

// FUNCION PARA MOSTRAR CAJAS DE VENTAS EN VENTANA MODAL
function VerCaja(codcaja){

$('#muestracajamodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaCajaModal=si&codcaja='+codcaja;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestracajamodal').empty();
                $('#muestracajamodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR CAJAS DE VENTAS
function UpdateCaja(codcaja,nrocaja,nomcaja,codigo,proceso) 
{
  // aqui asigno cada valor a los campos correspondientes
  $("#savecajas #codcaja").val(codcaja);
  $("#savecajas #nrocaja").val(nrocaja);
  $("#savecajas #nomcaja").val(nomcaja);
  $("#savecajas #codigo").val(codigo);
  $("#savecajas #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR CAJAS DE VENTAS 
function EliminarCaja(codcaja,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Caja para Ventas?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codcaja="+codcaja+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#cajas').load("consultas?CargaCajas=si");
                  
          } else if(data==2){ 

             swal("Oops", "Esta Caja para Venta no puede ser Eliminada, tiene Ventas relacionados!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Cajas para Ventas, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}
















/////////////////////////////////// FUNCIONES DE ARQUEOS DE CAJAS PARA VENTAS //////////////////////////////////////

// FUNCION PARA MOSTRAR ARQUEOS DE CAJAS PARA VENTAS EN VENTANA MODAL
function VerArqueo(codarqueo){

$('#muestraarqueomodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaArqueoModal=si&codarqueo='+codarqueo;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestraarqueomodal').empty();
                $('#muestraarqueomodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR ARQUEOS DE CAJAS PARA VENTAS
function CerrarArqueo(codarqueo,nrocaja,responsable,montoinicial,ingresos,egresos,creditos,abonos,estimado,fechaapertura) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savecerrararqueo #codarqueo").val(codarqueo);
  $('label[id*="nrocaja"]').text(nrocaja);
  $("#savecerrararqueo #responsable").val(responsable);
  $("#savecerrararqueo #montoinicial").val(montoinicial);
  $("#savecerrararqueo #ingresos").val(ingresos);
  $("#savecerrararqueo #egresos").val(egresos);
  $("#savecerrararqueo #creditos").val(creditos);
  $("#savecerrararqueo #abonos").val(abonos);
  $("#savecerrararqueo #estimado").val(estimado);
  $("#savecerrararqueo #fechaapertura").val(fechaapertura);
}

//FUNCION PARA CALCULAR LA DIFERENCIA EN CIERRE DE CAJA
$(document).ready(function (){
          $('.cierrecaja').keyup(function (){
      
      var efectivo = $('input#dineroefectivo').val();
      var estimado = $('input#estimado').val();
            
      //REALIZO EL CALCULO Y MUESTRO LA DEVOLUCION
      total=efectivo - estimado;
      var original=parseFloat(total.toFixed(2));
      $("#diferencia").val(original.toFixed(2));/**/
      
          });
});

//FUNCION PARA BUSQUEDA DE ARQUEOS DE CAJAS POR FECHAS PARA REPORTES
function BuscarArqueosxFechas(){
                  
$('#muestraarqueosxfechas').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var codcaja = $("#codcaja").val();
var desde = $("input#desde").val();
var hasta = $("input#hasta").val();
var dataString = $("#arqueosxfechas").serialize();
var url = 'funciones.php?BuscaArqueosxFechas=si';

$.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {            
                $('#muestraarqueosxfechas').empty();
                $('#muestraarqueosxfechas').append(''+response+'').fadeIn("slow");
                
               }
      }); 
}














/////////////////////////////////// FUNCIONES DE MOVIMIENTOS EN CAJAS DE VENTAS //////////////////////////////////////

// FUNCION PARA MOSTRAR MOVIMIENTO EN CAJAS DE VENTAS EN VENTANA MODAL
function VerMovimiento(codmovimiento){

$('#muestramovimientomodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaMovimientoModal=si&codmovimiento='+codmovimiento;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestramovimientomodal').empty();
                $('#muestramovimientomodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR MOVIMIENTOS EN CAJAS DE VENTAS
function UpdateMovimiento(codmovimiento,codcaja,tipomovimiento,descripcionmovimiento,montomovimiento,codmediopago,fechamovimiento,proceso) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savemovimiento #codmovimiento").val(codmovimiento);
  $("#savemovimiento #codcaja").val(codcaja);
  $("#savemovimiento #tipomovimiento").val(tipomovimiento);
  $("#savemovimiento #descripcionmovimiento").val(descripcionmovimiento);
  $("#savemovimiento #montomovimiento").val(montomovimiento);
  $("#savemovimiento #montomovimientodb").val(montomovimiento);
  $("#savemovimiento #codmediopago").val(codmediopago);
  $("#savemovimiento #fecharegistro").val(fechamovimiento);
  $("#savemovimiento #proceso").val(proceso);
}

/////FUNCION PARA ELIMINAR MOVIMIENTOS EN CAJAS DE VENTAS 
function EliminarMovimiento(codmovimiento,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Movimiento en Caja para Ventas?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codmovimiento="+codmovimiento+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#movimientos').load("consultas?CargaMovimientos=si");
                  
          } else if(data==2){ 

             swal("Oops", "Este Movimiento en Caja para Venta no puede ser Eliminado, se encuentra Desactivado!", "error"); 

          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Movimiento en Cajas para Ventas, no eres el Administrador o Cajero del Sistema!", "error"); 

                }
            }
        })
    });
}

//FUNCION PARA BUSQUEDA DE MOVIMIENTOS DE CAJAS POR FECHAS PARA REPORTES
function BuscarMovimientosxFechas(){
                  
$('#muestramovimientosxfechas').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var codcaja = $("#codcaja").val();
var desde = $("input#desde").val();
var hasta = $("input#hasta").val();
var dataString = $("#movimientosxfechas").serialize();
var url = 'funciones.php?BuscaMovimientosxFechas=si';

$.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {            
                $('#muestramovimientosxfechas').empty();
                $('#muestramovimientosxfechas').append(''+response+'').fadeIn("slow");
                
               }
      }); 
}





















/////////////////////////////////// FUNCIONES DE VENTAS //////////////////////////////////////

// FUNCION PARA MOSTRAR VENTAS DE PRODUCTOS EN VENTANA MODAL
function RecibeMesa(codmesa){

$('#muestradetallemesa').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaMesaReservada=si&codmesa='+codmesa;

$.ajax({
            type: "GET",
      url: "funciones.php",
            data: dataString,
            success: function(response) { 
                $("#loading").load("salas_mesas.php?CargaProductos=si");           
                $('#muestradetallemesa').empty();
                $('#muestradetallemesa').append(''+response+'').fadeIn("slow");
           }
      });
}


////FUNCION MUESTRA BOTON
function mostrar(){

     var botonAccion =  document.getElementById('boton');
     var div = document.getElementById('remision');

     if(div.style.display==='block'){

       div.style.display = "none";
       //Actualizamos el nombre del botón
       botonAccion.value = 'Ver Mesas';
       $('#loading').load("salas_mesas?CargaProductos=si");

       } else {

       div.style.display = "block";
       //Actualizamos el nombre del botón
       botonAccion.value= 'Ver Productos';
       $('#loading').load("salas_mesas?CargaMesas=si");

      }
}

/////FUNCION PARA CANCELAR PEDIDOS EN MESA
function CancelarPedido(codpedido,codmesa,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Cancelar todo el Pedido en esta Mesa?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Continuar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codpedido="+codpedido+"&codmesa="+codmesa+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Entregado!", "El Pedido en Mesa fue Cancelado Exitosamente!", "success");
            $('#muestradetallemesa').html("<center>SELECCIONE MESA PARA CONTINUAR -></center>");
            $('#loading').load("salas_mesas?CargaMesas=si");   

           }
         }
      })
  });
}


/////FUNCION PARA ENTREGAR PEDIDOS DE MOSTRADOR
function EntregarPedidos(codpedido,pedido,delivery,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Realizar la Entrega de este Pedido?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Continuar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codpedido="+codpedido+"&pedido="+pedido+"&delivery="+delivery+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Entregado!", "El Pedido en Mesa fue entregado Exitosamente!", "success");
            $('#mostrador').load("consultas.php?CargaMostrador=si");    
          
          } else if(data==2){

            swal("Entregado!", "El Pedido de Delivery fue entregado Exitosamente!", "success");
            $('#mostrador').load("consultas.php?CargaMostrador=si");  

             }
          }
        })
    });
}


/////FUNCION PARA ENTREGAR PEDIDOS DE DELIVERY
function EntregarDelivery(codpedido,pedido,delivery,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Realizar la Entrega de este Pedido al Cliente?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Continuar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codpedido="+codpedido+"&pedido="+pedido+"&delivery="+delivery+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Entregado!", "El Pedido fue entregado al Cliente Exitosamente!", "success");
            $('#delivery').load("consultas.php?CargaDelivery=si");    
          
            }
          }
        })
    });
}


// FUNCION PARA MOSTRAR VENTAS EN VENTANA MODAL
function VerVenta(codventa){

$('#muestraventamodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaVentaModal=si&codventa='+codventa;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestraventamodal').empty();
                $('#muestraventamodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ACTUALIZAR VENTAS
function UpdateVenta(codventa,proceso) {

  swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Actualizar esta Venta de Producto?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Actualizar",
          confirmButtonColor: "#3085d6"
        }, function(isConfirm) {
    if (isConfirm) {
      location.href = "forventa?codventa="+codventa+"&proceso="+proceso;
      // handle confirm
    } else {
      // handle all other cases
    }
  })
}

// FUNCION PARA AGREGAR DETALLES A VENTAS
function AgregaDetalleVenta(codventa,proceso) {

  swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Agregar Detalles de Productos a esta Venta?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Continuar",
          confirmButtonColor: "#3085d6"
        }, function(isConfirm) {
    if (isConfirm) {
      location.href = "forventa?codventa="+codventa+"&proceso="+proceso;
      // handle confirm
    } else {
      // handle all other cases
    }
  })
}

/////FUNCION PARA ELIMINAR DETALLE DE PEDIDOSS EN VENTAS 
function EliminaDetallePedido(codmesa,codpedido,pedido,codcliente,codproducto,cantventa,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Detalle en Pedido?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codmesa="+codmesa+"&codpedido="+codpedido+"&pedido="+pedido+"&codcliente="+codcliente+"&codproducto="+codproducto+"&cantventa="+cantventa+"&tipo="+tipo,
                  success: function(data){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#muestradetalles').load("funciones.php?CargaDetallesPedido=si&codmesa="+codmesa); 

            }
        })
    });
}

/////FUNCION PARA ELIMINAR DETALLES DE VENTAS EN VENTANA MODAL
function EliminarDetallesVentaModal(coddetalleventa,codventa,codcliente,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Detalle de Venta?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "coddetalleventa="+coddetalleventa+"&codventa="+codventa+"&codcliente="+codcliente+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#muestraventamodal').load("funciones.php?BuscaVentaModal=si&codventa="+codventa); 
            $('#ventas').load("consultas.php?CargaVentas=si");    
          
          } else if(data==2){ 

             swal("Oops", "No puede Eliminar todos los Detalles de Ventas en este Módulo, realice la Eliminación completa de la Venta!", "error"); 

          } else { 

             swal("Oops", "No tiene Acceso para Eliminar Detalles de Ventas, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}


/////FUNCION PARA ELIMINAR DETALLES DE VENTAS EN ACTUALIZAR
function EliminarDetallesVentaUpdate(coddetalleventa,codventa,codcliente,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Detalle de Venta?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "coddetalleventa="+coddetalleventa+"&codventa="+codventa+"&codcliente="+codcliente+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#detallesventasupdate').load("funciones.php?MuestraDetallesVentasUpdate=si&codventa="+codventa); 
          
          } else if(data==2){ 

             swal("Oops", "No puede Eliminar todos los Detalles de Ventas en este Módulo, realice la Eliminación completa de la Venta!", "error"); 

          } else { 

             swal("Oops", "No tiene Acceso para Eliminar Detalles de Ventas, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}

/////FUNCION PARA ELIMINAR DETALLES DE VENTAS EN AGREGAR
function EliminarDetallesVentaAgregar(coddetalleventa,codventa,codcliente,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar este Detalle de Venta?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "coddetalleventa="+coddetalleventa+"&codventa="+codventa+"&codcliente="+codcliente+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#detallesventasagregar').load("funciones.php?MuestraDetallesVentasAgregar=si&codventa="+codventa); 
          
          } else if(data==2){ 

             swal("Oops", "No puede Eliminar todos los Detalles de Ventas en este Módulo, realice la Eliminación completa de la Venta!", "error"); 

          } else { 

             swal("Oops", "No tiene Acceso para Eliminar Detalles de Ventas, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}

/////FUNCION PARA ELIMINAR VENTAS 
function EliminarVenta(codventa,codcliente,tipo) {
        swal({
          title: "¿Estás seguro?", 
          text: "¿Estás seguro de Eliminar esta Venta?", 
          type: "warning",
          showCancelButton: true,
          cancelButtonText: "Cancelar",
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          confirmButtonText: "Eliminar",
          confirmButtonColor: "#3085d6"
        }, function() {
             $.ajax({
                  type: "GET",
                  url: "eliminar.php",
                  data: "codventa="+codventa+"&codcliente="+codcliente+"&tipo="+tipo,
                  success: function(data){

          if(data==1){

            swal("Eliminado!", "Datos eliminados con éxito!", "success");
            $('#ventas').load("consultas.php?CargaVentas=si");
                  
          } else { 

             swal("Oops", "Usted no tiene Acceso para Eliminar Ventas de Productos, no eres el Administrador!", "error"); 

                }
            }
        })
    });
}


//FUNCION PARA BUSQUEDA DE VENTAS POR CAJAS Y FECHAS
function BuscarVentasxCajas(){
                  
$('#muestraventasxcajas').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var codcaja = $("#codcaja").val();
var desde = $("input#desde").val();
var hasta = $("input#hasta").val();
var dataString = $("#ventasxcajas").serialize();
var url = 'funciones.php?BuscaVentasxCajas=si';

$.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {            
                $('#muestraventasxcajas').empty();
                $('#muestraventasxcajas').append(''+response+'').fadeIn("slow");
                
               }
      }); 
}

// FUNCION PARA BUSQUEDA DE VENTAS POR FECHAS
function BuscarVentasxFechas(){
                        
$('#muestraventasxfechas').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var desde = $("input#desde").val();
var hasta = $("input#hasta").val();
var dataString = $("#ventasxfechas").serialize();
var url = 'funciones.php?BuscaVentasxFechas=si';

$.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {            
                $('#muestraventasxfechas').empty();
                $('#muestraventasxfechas').append(''+response+'').fadeIn("slow");
                
             }
      });
}















/////////////////////////////////// FUNCIONES DE CREDITOS //////////////////////////////////////

// FUNCION PARA MOSTRAR VENTA DE CREDITO EN VENTANA MODAL
function VerCredito(codventa){

$('#muestracreditomodal').html('<center><i class="fa fa-spin fa-spinner"></i> Cargando información, por favor espere....</center>');

var dataString = 'BuscaCreditoModal=si&codventa='+codventa;

$.ajax({
            type: "GET",
            url: "funciones.php",
            data: dataString,
            success: function(response) {            
                $('#muestracreditomodal').empty();
                $('#muestracreditomodal').append(''+response+'').fadeIn("slow");
                
            }
      });
}

// FUNCION PARA ABONAR PAGO A CREDITOS
function AbonoCredito(codcliente,codventa,totaldebe,dnicliente,nomcliente,nroventa,totalfactura,fechaventa,totalabono,debe) 
{
    // aqui asigno cada valor a los campos correspondientes
  $("#savepago #codcliente").val(codcliente);
  $("#savepago #codventa").val(codventa);
  $("#savepago #totaldebe").val(totaldebe);
  $("#savepago #dnicliente").val(dnicliente);
  $("#savepago #nomcliente").val(nomcliente);
  $("#savepago #nroventa").val(nroventa);
  $("#savepago #totalfactura").val(totalfactura);
  $("#savepago #fechaventa").val(fechaventa);
  $("#savepago #totalabono").val(totalabono);
  $("#savepago #debe").val(debe);
}

//FUNCION PARA BUSQUEDA DE CREDITOS POR CLIENTES Y FECHAS
function BuscarCreditosxClientes(){
                  
$('#muestracreditosxclientes').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var codcliente = $("#codcliente").val();
var dataString = $("#creditosxclientes").serialize();
var url = 'funciones.php?BuscaCreditosxClientes=si';

$.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {            
                $('#muestracreditosxclientes').empty();
                $('#muestracreditosxclientes').append(''+response+'').fadeIn("slow");
                
               }
      }); 
}

// FUNCION PARA BUSQUEDA DE CREDITOS POR FECHAS
function BuscarCreditosxFechas(){
                        
$('#muestracreditosxfechas').html('<center><i class="fa fa-spin fa-spinner"></i> Procesando información, por favor espere....</center>');

var desde = $("input#desde").val();
var hasta = $("input#hasta").val();
var dataString = $("#creditosxfechas").serialize();
var url = 'funciones.php?BuscaCreditosxFechas=si';

$.ajax({
            type: "GET",
      url: url,
            data: dataString,
            success: function(response) {            
                $('#muestracreditosxfechas').empty();
                $('#muestracreditosxfechas').append(''+response+'').fadeIn("slow");
                
             }
      });
}
