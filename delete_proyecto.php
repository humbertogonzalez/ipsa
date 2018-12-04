<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level("proyectos");
?>
<?php
  $delete_id = delete_by_id('proyectos',(int)$_GET['id']);
  if($delete_id){
      $session->msg("s","Proyecto dado de Baja.");
      redirect('proyectos.php');
  } else {
      $session->msg("d","Ocurrió un error.");
      redirect('proyectos.php');
  }
?>
