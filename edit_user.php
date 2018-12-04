<style>
  .panel-6 {
    min-height: 600px;
  }
</style>
<?php
  $page_title = 'Editar Usuario';
  require_once('includes/load.php');
  
  // Únicamente el Administrador puede ver funciones de Usuarios
  page_require_level("usuarios");
  
  $e_user = find_by_id('users',(int)$_GET['id']);
  $groups  = find_all('user_groups');
  $puestos = find_all('user_puestos');
  $empresas = find_all('user_empresas');
  $users = find_all('users');
  
  if(!$e_user){
    $session->msg("d","Missing user id.");
    redirect('users.php');
  }
  
  // Update Profile Picture
  if(isset($_POST['submit'])) {
    $photo = new Media();
    $photo->upload($_FILES['file_upload']);
      if($photo->process_user((int)$e_user['id'])){
        $session->msg('s','La Foto ha sido actualizada.');
        redirect('edit_user.php?id='.(int)$e_user['id'], false);
      } else{
        $session->msg('d',join($photo->errors));
        redirect('edit_user.php?id='.(int)$e_user['id'], false);
      }
  }
  
  //Update User basic info
  if(isset($_POST['update'])) {
    $req_fields = array('name','username','level');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $id = (int)$e_user['id'];
      $name = remove_junk($db->escape($_POST['name']));
      $ap_paterno = remove_junk($db->escape($_POST['ap_paterno']));
      $ap_materno = remove_junk($db->escape($_POST['ap_materno']));
      $email = remove_junk($db->escape($_POST['email']));
      $puesto = remove_junk($db->escape($_POST['puesto']));
      $reporta = remove_junk($db->escape($_POST['reporta']));
      $_empresas = implode(",", $_POST['empresas']);
      $permisos = implode(",", $_POST['permisos']);
      $telefono = remove_junk($db->escape($_POST['telefono']));
      $fecha_nacimiento = remove_junk($db->escape($_POST['fecha_nacimiento']));
      $username = remove_junk($db->escape($_POST['username']));
      $level = (int)$db->escape($_POST['level']);
      $status   = (int)($db->escape($_POST['status']));
      $sql = "UPDATE users SET name ='{$name}',ap_paterno ='{$ap_paterno}',ap_materno ='{$ap_materno}', email='{$email}',puesto='{$puesto}', reporta='{$reporta}', empresas ='{$_empresas}', permisos ='{$permisos}', telefono='{$telefono}', fecha_nacimiento='{$fecha_nacimiento}',username ='{$username}',user_level='{$level}',status='{$status}' WHERE id='{$id}';";
      $result = $db->query($sql);
      
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"Usuario Actualizado");
        redirect('edit_user.php?id='.(int)$e_user['id'], false);
      } else {
        $session->msg('d',' Lo sentimos, ocurrió un error al intentar actualizar!');
        redirect('edit_user.php?id='.(int)$e_user['id'], false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_user.php?id='.(int)$e_user['id'],false);
    }
  }

  
  // Update user password
  if(isset($_POST['update-pass'])) {
    $req_fields = array('password');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $id = (int)$e_user['id'];
      $password = remove_junk($db->escape($_POST['password']));
      $h_pass   = sha1($password);
      $sql = "UPDATE users SET password='{$h_pass}' WHERE id='{$db->escape($id)}'";
      $result = $db->query($sql);
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"User password has been updated ");
        redirect('edit_user.php?id='.(int)$e_user['id'], false);
      } else {
        $session->msg('d',' Sorry failed to updated user password!');
        redirect('edit_user.php?id='.(int)$e_user['id'], false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_user.php?id='.(int)$e_user['id'],false);
    }
  }
  
  include_once('layouts/header.php');
?>
<div class="row">
  <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <?php echo remove_junk($e_user['name']) . " " . remove_junk($e_user['ap_paterno']) . " " . remove_junk($e_user['ap_materno']); ?>
        </strong>
      </div>
    </div>
  </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="col-md-4">
          <img class="img-circle img-size-2" src="uploads/users/<?php echo $user['image'];?>" alt="">
        </div>
        <div class="col-md-8">
          <form class="form-inline" action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-btn">
                  <input type="file" name="file_upload" multiple="multiple" class="btn btn-primary btn-file"/>
                </span>
                <button type="submit" name="submit" class="btn btn-default">Subir Foto</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <form method="post" action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" class="clearfix" enctype="multipart/form-data">
    <div class="col-md-6">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="name" class="control-label">Nombre</label>
            <input type="name" class="form-control" name="name" value="<?php echo remove_junk($e_user['name']); ?>">
          </div>
          <div class="form-group">
            <label for="name" class="control-label">Apellido Paterno</label>
            <input type="name" class="form-control" name="ap_paterno" value="<?php echo remove_junk($e_user['ap_paterno']); ?>">
          </div>
          <div class="form-group">
            <label for="name" class="control-label">Apellido Materno</label>
            <input type="name" class="form-control" name="ap_materno" value="<?php echo remove_junk($e_user['ap_materno']); ?>">
          </div>
          <div class="form-group">
            <label for="email" class="control-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo remove_junk($e_user['email']); ?>">
          </div>
          <div class="form-group">
            <label for="puesto">Puesto</label>
            <!--<input type="hidden" class="form-control" name="level" value="1">-->
            <input type="text" class="form-control" name="puesto" value="<?php echo remove_junk($e_user['puesto']); ?>">
          </div>
          <div class="form-group">
            <label for="reporta">Responsable directo</label>
            <!--<input type="hidden" class="form-control" name="level" value="1">-->
            <select class="form-control" name="reporta">
              <option>-- Seleccione un Responsable --</option>
              <?php
                foreach ($users as $us ):
                  $usName = "";
                  if($us["ap_materno"] == "") {
                    $usName = $us["name"] . " " . $us["ap_paterno"];
                  } else {
                    $usName = $us["name"] . " " . $us["ap_paterno"] . " " . $us["ap_materno"];
                  }
              ?>
                <option <?php if($us['username'] == $e_user['reporta']) echo 'selected="selected"';?> value="<?php echo $us['username'];?>"><?php echo $usName;?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label for="level">Grupo de Usuario</label>
            <select class="form-control" name="level">
              <option>-- Seleccione un Grupo --</option>
              <?php foreach ($groups as $group ):?>
                <option <?php if($group['group_level'] === $e_user['user_level']) echo 'selected="selected"';?> value="<?php echo $group['group_level'];?>"><?php echo $group['group_name'];?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label for="empresas">Empresa(s)</label><br>
            <?php foreach ($empresas as $empresa) { ?>
              <input type="checkbox" name="empresas[]" value="<?php echo $empresa["id"]; ?>" <?php if (strpos($e_user['empresas'], $empresa['id']) !== false) echo 'checked="checked"'; ?>/><?php echo $empresa["empresa"]; ?><br />
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="username" class="control-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" value="<?php echo remove_junk($e_user['telefono']); ?>">
          </div>
          <div class="form-group">
            <label for="username" class="control-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" name="fecha_nacimiento" value="<?php echo remove_junk($e_user['fecha_nacimiento']); ?>">
          </div>
          <div class="form-group">
            <label for="level">Permisos</label><br>
            <input type="checkbox" name="permisos[]" value="usuarios" <?php if (strpos($e_user['permisos'], "usuarios") !== false) echo 'checked="checked"'; ?>/>Usuarios<br />
            <input type="checkbox" name="permisos[]" value="requisiciones" <?php if (strpos($e_user['permisos'], "requisiciones") !== false) echo 'checked="checked"'; ?>/>Requisiciones<br />
            <input type="checkbox" name="permisos[]" value="ordenes_compra" <?php if (strpos($e_user['permisos'], "ordenes_compra") !== false) echo 'checked="checked"'; ?>/>Órdenes de Compra<br />
            <input type="checkbox" name="permisos[]" value="proyectos" <?php if (strpos($e_user['permisos'], "proyectos") !== false) echo 'checked="checked"'; ?>/>Proyectos<br />
            <input type="checkbox" name="permisos[]" value="inventarios" <?php if (strpos($e_user['permisos'], "inventarios") !== false) echo 'checked="checked"'; ?>/>Inventarios<br />
            <input type="checkbox" name="permisos[]" value="entradas" <?php if (strpos($e_user['permisos'], "entradas") !== false) echo 'checked="checked"'; ?>/>Entradas<br />
            <input type="checkbox" name="permisos[]" value="salidas" <?php if (strpos($e_user['permisos'], "salidas") !== false) echo 'checked="checked"'; ?>/>Salidas<br />
            <input type="checkbox" name="permisos[]" value="tesoreria" <?php if (strpos($e_user['permisos'], "tesoreria") !== false) echo 'checked="checked"'; ?>/>Tesorería<br />
            <input type="checkbox" name="permisos[]" value="proveedores" <?php if (strpos($e_user['permisos'], "proveedores") !== false) echo 'checked="checked"'; ?>/>Proveedores<br />
            <input type="checkbox" name="permisos[]" value="reportes" <?php if (strpos($e_user['permisos'], "reportes") !== false) echo 'checked="checked"'; ?>/>Reportes<br />
          </div>
          <div class="form-group">
            <label for="username" class="control-label">Usuario</label>
            <input type="text" class="form-control" name="username" value="<?php echo remove_junk($e_user['username']); ?>">
          </div>
          <!--<div class="form-group">
            <label for="level">Rol del Usuario</label>
            <select class="form-control" name="level">
              <?php foreach ($groups as $group ):?>
                <option <?php if($group['group_level'] === $e_user['user_level']) echo 'selected="selected"';?> value="<?php echo $group['group_level'];?>"><?php echo $group['group_name'];?></option>
              <?php endforeach;?>
            </select>
          </div>-->
          <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" name="status">
              <option <?php if($e_user['status'] === '1') echo 'selected="selected"';?>value="1">Activo</option>
              <option <?php if($e_user['status'] === '0') echo 'selected="selected"';?> value="0">Inactivo</option>
            </select>
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="update" class="btn btn-info">Actualizar</button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <!-- Change password form -->
  <!--<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Change <?php echo remove_junk(ucwords($e_user['name'])); ?> password
        </strong>
      </div>
      <div class="panel-body">
        <form action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" method="post" class="clearfix">
          <div class="form-group">
            <label for="password" class="control-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Type user new password">
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="update-pass" class="btn btn-danger pull-right">Change</button>
          </div>
        </form>
      </div>
    </div>
  </div>-->
</div>
<?php include_once('layouts/footer.php'); ?>
