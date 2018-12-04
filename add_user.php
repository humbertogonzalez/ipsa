<?php
  $page_title = 'Alta de Usuario';
  require_once('includes/load.php');
  
  // Checkin What level user has permission to view this page
  page_require_level("usuarios");
  $e_user = find_by_id('users',(int)$_GET['id']);
  $groups  = find_all('user_groups');
  $empresas = find_all('user_empresas');
  $users = find_all('users');
  
  if(isset($_POST['add_user'])){
    echo implode(",", $_POST['empresas']);
    $req_fields = array('name','ap_paterno','email','username','level','status','password');
    validate_fields($req_fields);

    if(empty($errors)){
      // Temporal
      $name       = remove_junk($db->escape($_POST['name']));
      $ap_paterno   = remove_junk($db->escape($_POST['ap_paterno']));
      $ap_materno   = remove_junk($db->escape($_POST['ap_materno']));
      $email   = remove_junk($db->escape($_POST['email']));
      $puesto = remove_junk($db->escape($_POST['puesto']));
      $reporta = remove_junk($db->escape($_POST['reporta']));
      $_empresas = implode(",", $_POST['empresas']);
      $telefono   = remove_junk($db->escape($_POST['telefono']));
      $fecha_nacimiento = remove_junk($db->escape($_POST['fecha_nacimiento']));
      $permisos = implode(",", $_POST['permisos']);
      $username = remove_junk($db->escape($_POST['username']));
      $level = (int)$db->escape($_POST['level']);
      $status   = (int)($db->escape($_POST['status']));
      $password = sha1($_POST['password']);
      $query = "INSERT INTO users (";
      $query .="name,ap_paterno,ap_materno,email,puesto,reporta,empresas,permisos,telefono,fecha_nacimiento,username,password,user_level,image,status";
      $query .=") VALUES (";
      $query .="'{$name}','{$ap_paterno}','{$ap_materno}','{$email}','{$puesto}','{$reporta}','{$_empresas}','{$permisos}','{$telefono}','{$fecha_nacimiento}','{$username}','{$password}','{$level}','no_image.jpg','{$status}'";
      $query .=")";
      
      if($db->query($query)){
        //sucess
        $session->msg('s',"Cuenta creada exitosamente ");
        redirect('add_user.php', false);
      } else {
        //failed
        $session->msg('d','Lo sentimos, ocurrió un error creando la cuenta');
        redirect('add_user.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_user.php',false);
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
          Alta de Usuario
        </strong>
      </div>
    </div>
  </div>
  <form method="post" action="add_user.php" class="clearfix" enctype="multipart/form-data">
    <div class="col-md-6">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="name" class="control-label">Nombre</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="form-group">
            <label for="ap_paterno" class="control-label">Apellido Paterno</label>
            <input type="text" class="form-control" name="ap_paterno" required>
          </div>
          <div class="form-group">
            <label for="ap_materno" class="control-label">Apellido Materno</label>
            <input type="text" class="form-control" name="ap_materno">
          </div>
          <div class="form-group">
            <label for="email" class="control-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="form-group">
            <label for="puesto">Puesto</label>
            <!--<input type="hidden" class="form-control" name="level" value="1">-->
            <input type="text" class="form-control" name="puesto" required>
          </div>
          <div class="form-group">
            <label for="reporta">Responsable directo</label>
            <!--<input type="text" class="form-control" name="reporta" required>-->
            <select class="form-control" name="level">
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
                <option value="<?php echo $us['username'];?>"><?php echo $usName;?></option>
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
              <input type="checkbox" name="empresas[]" value="<?php echo $empresa["id"]; ?>" /><?php echo $empresa["empresa"]; ?><br />
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="password" class="control-label">Contraseña</label>
            <input type="password" minlenght="12" class="form-control" name="password" required>
          </div>
          <div class="form-group">
            <label for="telefono" class="control-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" required>
          </div>
          <div class="form-group">
            <label for="fecha_nacimiento" class="control-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" name="fecha_nacimiento" required>
          </div>
          <div class="form-group">
            <label for="permisos">Permisos</label><br>
            <input type="checkbox" name="permisos[]" value="usuarios">Usuarios<br />
            <input type="checkbox" name="permisos[]" value="requisiciones">Requisiciones<br />
            <input type="checkbox" name="permisos[]" value="ordenes_compra">Órdenes de Compra<br />
            <input type="checkbox" name="permisos[]" value="proyectos">Proyectos<br />
            <input type="checkbox" name="permisos[]" value="inventarios">Inventarios<br />
            <input type="checkbox" name="permisos[]" value="entradas">Entradas<br />
            <input type="checkbox" name="permisos[]" value="salidas">Salidas<br />
            <input type="checkbox" name="permisos[]" value="tesoreria">Tesorería<br />
            <input type="checkbox" name="permisos[]" value="proveedores">Provedores<br />
            <input type="checkbox" name="permisos[]" value="reportes">Reportes<br />
          </div>
          <div class="form-group">
            <label for="username" class="control-label">Usuario</label>
            <input type="text" class="form-control" name="username" required>
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
            <select class="form-control" name="status" required>
              <option value="" selected="selected">-- Seleccione un Status --</option>
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <div class="form-group clearfix">
            <button type="submit" id="add_user" name="add_user" class="btn btn-info">Crear Usuario</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script>
// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#add_user').click(function() {
        return window.confirm("¿Desea crear el Usuario?");
    });
});
</script>
<?php include_once('layouts/footer.php'); ?>
