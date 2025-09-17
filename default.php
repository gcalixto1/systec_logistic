<?php
session_start();
if (!isset($_SESSION['login_idusuario'])) {
  header('location:login.php');
  exit();
}

?>
<meta content="" name="descriptison">
<meta content="" name="keywords">
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Palenzo</title>
  <script src="assets/vendor/jquery/jquery.min.js"></script>
  <script src="assets/DataTables/datatables.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/jquery.easing/jquery.easing.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/venobox/venobox.min.js"></script>
  <script src="assets/vendor/waypoints/jquery.waypoints.min.js"></script>
  <script src="assets/vendor/counterup/counterup.min.js"></script>
  <script src="assets/vendor/owl.carousel/owl.carousel.min.js"></script>
  <script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript" src="assets/js/select2.min.js"></script>
  <script type="text/javascript" src="assets/js/jquery.datetimepicker.full.min.js"></script>
  <script type="text/javascript" src="assets/font-awesome/js/all.min.js"></script>
  <script type="text/javascript" src="assets/js/jquery-te-1.4.0.min.js" charset="utf-8"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



</head>
<style>
  body {
    background: #80808045;
  }

  .modal-dialog.large {
    width: 80% !important;
    max-width: unset;
  }

  .modal-dialog.mid-large {
    width: 50% !important;
    max-width: unset;
  }
</style>

<body style="height:100px;background-image: url(assets/img/fondo_vectores.png);">

  <?php include 'menu.php' ?>
  <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white">
    </div>
  </div>


  <div id="preloader"></div>
  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

  <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmacion</h5>
        </div>
        <div class="modal-body">
          <div id="delete_content"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id='confirm' onclick="">Confirmar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id='submit' onclick="$('#uni_modal form').submit()"><i
              class="fa fa-save"></i> Guardar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
            Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="uni_modal_documentos" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id='submit'
            onclick="$('#uni_modal_documentos form').submit()"><i class="fa fa-save"></i> Guardar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
            Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="uni_modal_generador" role='dialog'>
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
      </div>
    </div>
  </div>
</body>
<script>
  window.start_load = function() {
    $('body').prepend('<di id="preloader2"></di>')
  }
  window.end_load = function() {
    $('#preloader2').fadeOut('fast', function() {
      $(this).remove();
    })
  }

  window.uni_modal = function($title = '', $url = '', $size = "") {
    start_load()
    $.ajax({
      url: $url,
      error: err => {
        console.log()
        alert("An error occured")
      },
      success: function(resp) {
        if (resp) {
          $('#uni_modal .modal-title').html($title)
          $('#uni_modal .modal-body').html(resp)
          if ($size != '') {
            $('#uni_modal .modal-dialog').addClass($size)
          } else {
            $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
          }
          $('#uni_modal').modal('show')
          end_load()
        }
      }
    })
  }
  window.uni_modal_documentos = function($title1 = '', $url1 = '', $size1 = "") {
    start_load()
    $.ajax({
      url: $url1,
      error: err => {
        console.log()
        alert("An error occured")
      },
      success: function(resp1) {
        if (resp1) {
          $('#uni_modal_documentos .modal-title').html($title1)
          $('#uni_modal_documentos .modal-body').html(resp1)
          if ($size1 != '') {
            $('#uni_modal_documentos .modal-dialog').addClass($size1)
          } else {
            $('#uni_modal_documentos .modal-dialog').removeAttr("class").addClass(
              "modal-dialog modal-xl")
          }
          $('#uni_modal_documentos').modal('show')
          end_load()
        }
      }
    })
  }

  window.uni_modal_generador = function($title1 = '', $url1 = '', $size1 = "") {
    start_load()
    $.ajax({
      url: $url1,
      error: err => {
        console.log()
        alert("An error occured")
      },
      success: function(resp1) {
        if (resp1) {
          $('#uni_modal_generador .modal-title').html($title1)
          $('#uni_modal_generador .modal-body').html(resp1)
          if ($size1 != '') {
            $('#uni_modal_generador .modal-dialog').addClass($size1)
          } else {
            $('#uni_modal_generador .modal-dialog').removeAttr("class").addClass(
              "modal-dialog modal-md")
          }
          $('#uni_modal_generador').modal('show')
          end_load()
        }
      }
    })
  }
  window._conf = function($msg = '', $func = '', $params = []) {
    $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")")
    $('#confirm_modal .modal-body').html($msg)
    $('#confirm_modal').modal('show')
  }
  window.alert_toast = function($msg = 'TEST', $bg = 'success') {
    $('#alert_toast').removeClass('bg-success')
    $('#alert_toast').removeClass('bg-danger')
    $('#alert_toast').removeClass('bg-info')
    $('#alert_toast').removeClass('bg-warning')

    if ($bg == 'success')
      $('#alert_toast').addClass('bg-success')
    if ($bg == 'danger')
      $('#alert_toast').addClass('bg-danger')
    if ($bg == 'info')
      $('#alert_toast').addClass('bg-info')
    if ($bg == 'warning')
      $('#alert_toast').addClass('bg-warning')
    $('#alert_toast .toast-body').html($msg)
    $('#alert_toast').toast({
      delay: 3000
    }).toast('show');
  }
  $(document).ready(function() {
    $('#preloader').fadeOut('fast', function() {
      $(this).remove();
    })
  })
  $('.datetimepicker').datetimepicker({
    format: 'Y/m/d H:i',
    startDate: '+3d'
  })
  $('.select2').select2({
    placeholder: "Please select here",
    width: "100%"
  })
</script>

</html>