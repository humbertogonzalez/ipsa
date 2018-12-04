<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level("usuarios");
?>
<?php
  $delete_id = inactive_by_id('users',(int)$_GET['id']);
  if($delete_id){
      $session->msg("s","Usuario dado de Baja.");
      redirect('users.php');
  } else {
      $session->msg("d","Ocurrió un error.");
      redirect('users.php');
  }
?>
