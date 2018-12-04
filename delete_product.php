<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level("inventarios");
?>
<?php
  //$product = find_by_id('inventario',(int)$_GET['id']);
  $product = "SELECT * FROM inventario WHERE sec=" . (int)$_GET['id'] . ";";
  $product = $db->query($product);
  
  if(!$product){
    $session->msg("d","No existe el producto");
    redirect('inventarios.php');
  }

  $delete_id = "DELETE FROM inventario WHERE sec=". (int)$_GET['id'] . ";";
  $db->query($delete_id);
  
  if($db->affected_rows() === 1){
      $session->msg("s","Producto Eliminado.");
      redirect('inventarios.php');
  } else {
      $session->msg("d","Error al intentar eliminar el producto.");
      redirect('inventarios.php');
  }
?>
