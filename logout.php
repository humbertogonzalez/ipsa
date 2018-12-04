<?php
  require_once('includes/load.php');

  $id = $_SESSION['user_id'];
  $sql = "UPDATE users SET working_on=0 WHERE id='{$id}'";
  $result = $db->query($sql);
  
  if(!$session->logout()) {
    redirect("index.php");
  }
?>
