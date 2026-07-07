<?php
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Csrf.php';
Auth::require();
Auth::requireRole(['admin', 'recepcion']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Historial</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
	
    <link rel="stylesheet" href="css/layout.css">
	<link rel="stylesheet" href="css/main.css">
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include('partials/flash.php'); ?>
<?php include('menu.php'); ?>

        <!-- Page Content  -->
        <div id="content" class="p-4 p-md-5 pt-5 ">
          <div class="row border-bottom mb-3">
            <div class=" col-md-7 col-sm-12 animated fadeIn container centrar  ">
                <h1 class="page-title">Historial</h1>
            </div>
            <div class="col-md-5 col-sm-12 container centrar pt-3 row">
              <div class="col-10">
                <input class="form-control border" type="text" name="search_text" id="search_text" placeholder="Buscar por cédula o nombre..." autocomplete="off">
                <span id="search-spin" class="d-none ml-2 text-muted" aria-label="Cargando..."><i class="fa fa-spinner fa-spin"></i></span>
              </div>
              <div class="col-2">
                <a href="#" class=" btn btn-primary align-middle " ><i class="fa fa-search"></i></a>
              </div>
            </div>
            <div class=""></div>
          </div>
          <div class="container-fluid">

    			<div id="result">

    			</div>


          <script>
            $(function () {
                ajaxSearch({ url: 'viewhistorial.php', inputId: 'search_text', resultId: 'result', spinId: 'search-spin' });
            });
          </script>

            <div class="container p-1 mt-3 text-center">
              <input class="btn btn-lg btn-primary align-middle" type="submit" name="" onclick="location.reload();" value="Actualizar">
            </div>
            </form>
        </div>
    </div>
<div class="modal fade" id="modalEditPago" tabindex="-1" role="dialog" aria-labelledby="modalEditPagoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditPagoLabel">Editar pago</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="functions/edit_pago.php">
        <?= Csrf::field() ?>
        <input type="hidden" name="pago_id" id="edit-pago-id">
        <div class="modal-body">
          <div class="form-group">
            <label>Fecha</label>
            <input type="date" class="form-control border" name="fecha" id="edit-pago-fecha" required>
          </div>
          <div class="form-group">
            <label>Monto (B/.)</label>
            <input type="number" step="0.01" min="0" class="form-control border" name="monto" id="edit-pago-monto" required>
          </div>
          <div class="form-group">
            <label>Tipo de pago</label>
            <select class="form-control border" name="tipo_de_pago" id="edit-pago-tipo">
              <option value="Efectivo">Efectivo</option>
              <option value="Tarjeta">Tarjeta</option>
              <option value="Cheque">Cheque</option>
              <option value="Transferencia">Transferencia</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tratamiento</label>
            <input type="text" class="form-control" id="edit-pago-tratamiento-display" readonly>
          </div>
          <div class="form-group">
            <label>Nota</label>
            <textarea class="form-control border" name="nota" id="edit-pago-nota" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$('#modalEditPago').on('show.bs.modal', function (event) {
    var btn = $(event.relatedTarget);
    $('#edit-pago-id').val(btn.data('id'));
    $('#edit-pago-fecha').val(btn.data('fecha'));
    $('#edit-pago-monto').val(btn.data('monto'));
    var tipo = btn.data('tipo');
    $('#edit-pago-tipo').find('option').each(function () {
        $(this).prop('selected', $(this).val() === tipo);
    });
    $('#edit-pago-tratamiento-display').val(btn.data('tratamiento'));
    $('#edit-pago-nota').val(btn.data('nota'));
});
</script>
<script src="js/main.js?v=<?= filemtime(__DIR__ . '/js/main.js') ?>"></script>
</body>
</html>
