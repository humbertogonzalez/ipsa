<?php
  $page_title = 'Edit Account';
  require_once('includes/load.php');
  $user = current_user();
  //page_require_level("usuarios");
  
  $id = (int)$_SESSION['user_id'];
  $sql = "SELECT working_on FROM users WHERE id='{$id}'";
  $result = $db->query($sql);
  $result = $db->fetch_assoc($result);
  $working_on = $result['working_on'];
  
  if($working_on == 0) {
    $session->msg("s", "Debe de seleccionar una Empresa para comenzar.");
    redirect('selectEmpresa.php',false);
  }
  
  //update user image
  if(isset($_POST['submit'])) {
    $photo = new Media();
    $user_id = (int)$_POST['user_id'];
    $photo->upload($_FILES['file_upload']);
    
    if($photo->process_user($user_id)){
      $session->msg('s','Foto actualizada exitosamente.');
      redirect('edit_account.php');
    } else{
      $session->msg('d',join($photo->errors));
      redirect('edit_account.php');
    }
  }
  
  if(isset($_POST['update'])){
    $req_fields = array('new-password','old-password','id' );
    validate_fields($req_fields);

    if(empty($errors)){
      error_log("new-password > " . $_POST['new-password'], 3, "debug.log");
      if(sha1($_POST['old-password']) !== current_user()['password'] ){
        $session->msg('d', "Tu Contrasela anterior es incorrecta.");
        redirect('edit_account.php',false);
      }

      $id = (int)$_POST['id'];
      $new = remove_junk($db->escape(sha1($_POST['new-password'])));
      $sql = "UPDATE users SET password ='{$new}' WHERE id='{$db->escape($id)}'";
      $result = $db->query($sql);
      
      if($result && $db->affected_rows() === 1):
        $session->logout();
        $session->msg('s',"Ingrese con su nueva contraseña.");
        redirect('index.php', false);
      else:
        $session->msg('d','Ocurrió un error, vuelva a intentar.');
        redirect('edit_account.php', false);
      endif;
    } else {
      $session->msg("d", $errors);
      redirect('edit_account.php',false);
    }
  }
  
  //update user other info
  /*if(isset($_POST['update'])){
    $req_fields = array('name','username' );
    validate_fields($req_fields);
    if(empty($errors)){
      $name = remove_junk($db->escape($_POST['name']));
      $username = remove_junk($db->escape($_POST['username']));
      $sql = "UPDATE users SET name ='{$name}', username ='{$username}' WHERE id='{$id}'";
      $result = $db->query($sql);
      
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"Acount updated ");
        redirect('edit_account.php', false);
      } else {
        $session->msg('d',' Sorry failed to updated!');
        redirect('edit_account.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_account.php',false);
    }
  }*/
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Editar Mi Cuenta</span>
        </strong>
      </div>
    </div>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="panel-heading clearfix">
      <span class="glyphicon glyphicon-camera"></span>
      <span>Cambiar mi Foto</span>
    </div>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-4">
          <img class="img-circle img-size-2" src="uploads/users/<?php echo $user['image'];?>" alt="">
      </div>
      <div class="col-md-8">
        <form class="form" action="edit_account.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <input type="file" name="file_upload" multiple="multiple" class="btn btn-default btn-file"/>
        </div>
        <div class="form-group">
          <input type="hidden" name="user_id" value="<?php echo $user['id'];?>">
           <button type="submit" name="submit" class="btn btn-warning">Modificar</button>
        </div>
       </form>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="glyphicon glyphicon-edit"></span>
      <span>Modificar Contraseña</span>
    </div>
    <div class="panel-body">
        <form method="post" action="edit_account.php" class="clearfix">
          <div class="form-group">
            <label for="oldPassword" class="control-label">Contraseña Anterior</label>
            <input type="password" class="form-control" name="old-password" placeholder="Contraseña Anterior">
          </div>
          <div class="form-group">
            <label for="newPassword" class="control-label">Contraseña Nueva</label>
            <input type="password" class="form-control" name="new-password" placeholder="Contraseña Nueva">
          </div>
          <div class="form-group clearfix">
            <input type="hidden" name="id" value="<?php echo (int)$user['id'];?>">
            <button type="submit" name="update" class="btn btn-info">Modificar</button>
          </div>
        </form>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
