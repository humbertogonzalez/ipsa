<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level("proveedores");
?>
<?php
  $delete_id = inactive_by_id('proveedores',(int)$_GET['id']);
  if($delete_id){
      $session->msg("s","Proveedor dado de baja.");
      redirect('proveedores.php');
  } else {
      $session->msg("d","Ocurrió un error.");
      redirect('proveedores.php');
  }
?>
