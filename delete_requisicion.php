<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level("requisiciones");
?>
<?php
  $deleteQuery = "UPDATE requisiciones SET status=7 WHERE id=" . (int)$_GET['id'] . ";";
  if($db->query($deleteQuery)){
      $session->msg("s","Requisición " . $_GET['id'] . " eliminada.");
      redirect('requisiciones.php');
  } else {
      $session->msg("d","Ocurrió un error.");
      redirect('requisiciones.php');
  }
?>
