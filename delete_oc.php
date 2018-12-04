<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level("ordenes_compra");
?>
<?php
  //$delete_id = delete_by_id('ordenes_de_compra',(int)$_GET['id']);
  $deleteQuery = "UPDATE ordenes_de_compra SET status=7 WHERE id=" . (int)$_GET['id'] . ";";
  
  if($db->query($deleteQuery)){
      $session->msg("s","Órden de Compra " . $_GET['id'] . " eliminada.");

      // Modificamos cantidades en Tesorería para que no se tome en cuenta
      $updateQuery = "UPDATE tesoreria SET monto_pago=0.00 WHERE tipo_cuenta=0 AND descripcion='OC-" . (int)$_GET['id'] . "';";
      $db->query($updateQuery);
      redirect('oc.php');
  } else {
      $session->msg("d","Ocurrió un error.");
      redirect('oc.php');
  }
?>
