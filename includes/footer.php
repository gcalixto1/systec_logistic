<style>
    body {
    background-image: url('img/fondo.jpg'); /* Ruta de la imagen de fondo */
    background-size: cover; /* Cubre todo el área del body */
    background-position: center; /* Centra la imagen */
    background-attachment: fixed; /* Fija la imagen de fondo para que no se desplace con el contenido */
    backdrop-filter: blur(500px); /* Aplica un desenfoque al fondo */
}
</style>
<body>
<!-- Footer -->
<footer class="bg-dark text-white text-center text-lg-start fixed-bottom">
  <div class="container my-auto">
  <div class="text-center">
    </div>
  </div>
</footer>
</body> 

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/all.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap4.min.js"></script>
<script src="js/sweetalert2@10.js"></script>
<script type="text/javascript" src="js/producto.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#table').DataTable({
      language: {
        "decimal": "",
        "emptyTable": "No hay datos",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
        "infoEmpty": "Mostrando 0 a 0 de 0 registros",
        "infoFiltered": "(Filtro de _MAX_ total registros)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Mostrar _MENU_ registros",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "search": "Buscar:",
        "zeroRecords": "No se encontraron coincidencias",
        "paginate": {
          "first": "Primero",
          "last": "Ultimo",
          "next": "Siguiente",
          "previous": "Anterior"
        },
        "aria": {
          "sortAscending": ": Activar orden de columna ascendente",
          "sortDescending": ": Activar orden de columna desendente"
        }
      }
    });
    var usuarioid = '<?php echo $_SESSION['idUser']; ?>';
    searchForDetalle(usuarioid);
  });
</script>

</body>

</html>