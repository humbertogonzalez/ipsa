<style>
  .panel-6 {
    min-height: 600px;
  }
</style>
<?php
  $page_title = 'Editar Proveedor';
  require_once('includes/load.php');
  
  // Únicamente el Administrador puede ver funciones de Usuarios
  page_require_level("proveedores");
  
  $e_proveedor = find_by_id('proveedores',(int)$_GET['id']);
  $proveedores = find_all('proveedores');
  
  if(!$e_proveedor){
    $session->msg("d","No existe el proveedor.");
    redirect('users.php');
  }
  
  // Actualizar información del Proveedor
  if(isset($_POST['edit_proveedor'])) {
    $req_fields = array('razon_social','direccion_1','colonia','delegacion_municipio','estado','cp','telefono');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $razon_social = remove_junk($db->escape($_POST['razon_social']));
      $rfc = remove_junk($db->escape($_POST['rfc']));
      $direccion_1 = remove_junk($db->escape($_POST['direccion_1']));
      $direccion_2 = remove_junk($db->escape($_POST['direccion_2']));
      $colonia = remove_junk($db->escape($_POST['colonia']));
      $delegacion_municipio = remove_junk($db->escape($_POST['delegacion_municipio']));
      $poblacion = remove_junk($db->escape($_POST['poblacion']));
      $estado = remove_junk($db->escape($_POST['estado']));
      $cp = remove_junk($db->escape($_POST['cp']));
      $telefono = remove_junk($db->escape($_POST['telefono']));
      $web = remove_junk($db->escape($_POST['web']));
      $contacto = remove_junk($db->escape($_POST['contacto']));
      $correo_contacto = remove_junk($db->escape($_POST['correo_contacto']));
      $telefono_contacto = remove_junk($db->escape($_POST['telefono_contacto']));
      $extension = remove_junk($db->escape($_POST['extension']));
      $status = remove_junk($db->escape($_POST['status']));
      $sql = "UPDATE proveedores SET razon_social='{$razon_social}',rfc='{$rfc}',direccion_1='{$direccion_1}',direccion_2='{$direccion_2}',colonia='{$colonia}',delegacion_municipio='{$delegacion_municipio}',poblacion='{$poblacion}',estado='{$estado}',cp='{$cp}',telefono='{$telefono}',web='{$web}',contacto='{$contacto}',correo_contacto='{$correo_contacto}',telefono_contacto='{$telefono_contacto}',extension='{$extension}',status='{$status}' WHERE id='{$_GET['id']}';";
      $result = $db->query($sql);
      
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"Proveedor Actualizado");
        redirect('edit_proveedor.php?id='.(int)$e_proveedor['id'], false);
      } else {
        $session->msg('d',' Lo sentimos, ocurrió un error al intentar actualizar!');
        redirect('edit_proveedor.php?id='.(int)$e_proveedor['id'], false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_proveedor.php?id='.(int)$e_proveedor['id'],false);
    }
  }
  
  include_once('layouts/header.php');
?>
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
          <span><?php echo remove_junk($e_proveedor['razon_social']); ?></span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_proveedor.php?id=<?php echo (int)$e_proveedor['id'];?>" autocomplete="off">
          <input type="hidden" class="form-control" name="llave" value="<?php echo $e_proveedor['llave']; ?>">
          <div class="col-md-6">
            <div class="form-group">
              <label for="razon_social">Razón Social</label>
              <input type="text" class="form-control" name="razon_social" value="<?php echo $e_proveedor['razon_social']; ?>">
            </div>
            <div class="form-group">
              <label for="rfc">RFC</label>
              <input type="text" class="form-control" name="rfc" value="<?php echo $e_proveedor['rfc']; ?>">
            </div>
            <div class="form-group">
              <label for="direccion_1">Dirección 1</label>
              <input type="text" class="form-control" name="direccion_1" value="<?php echo $e_proveedor['direccion_1']; ?>">
            </div>
            <div class="form-group">
              <label for="direccion_2">Dirección 2</label>
              <input type="text" class="form-control" name="direccion_2" value="<?php echo $e_proveedor['direccion_2']; ?>">
            </div>
            <div class="form-group">
              <label for="colonia">Colonia</label>
              <input type="text" class="form-control" name="colonia" value="<?php echo $e_proveedor['colonia']; ?>">
            </div>
            <div class="form-group">
              <label for="delegacion_municipio">Delegación/Municipio</label>
              <input type="text" class="form-control" name="delegacion_municipio" value="<?php echo $e_proveedor['delegacion_municipio']; ?>">
            </div>
            <div class="form-group">
              <label for="poblacion">Población</label>
              <input type="text" class="form-control" name="poblacion" value="<?php echo $e_proveedor['poblacion']; ?>">
            </div>
            <div class="form-group">
              <label for="estado">Estado</label>
              <select name="estado" id="estado" class="form-control">
                <option value="">-- Seleccionar Estado --</option>
                <option value="Aguascalientes" <?php if($e_proveedor['estado'] == "Aguascalientes"){ echo 'selected="selected"'; } ?>>Aguascalientes</option>
                <option value="Baja California" <?php if($e_proveedor['estado'] == "Baja California"){ echo 'selected="selected"'; } ?>>Baja California</option>
                <option value="Baja California Sur" <?php if($e_proveedor['estado'] == "Baja California Sur"){ echo 'selected="selected"'; } ?>>Baja California Sur</option>
                <option value="Campeche" <?php if($e_proveedor['estado'] == "Campeche"){ echo 'selected="selected"'; } ?>>Campeche</option>
                <option value="Chiapas" <?php if($e_proveedor['estado'] == "Chiapas"){ echo 'selected="selected"'; } ?>>Chiapas</option>
                <option value="Chihuahua" <?php if($e_proveedor['estado'] == "Chihuahua"){ echo 'selected="selected"'; } ?>>Chihuahua</option>
                <option value="Ciudad de México" <?php if($e_proveedor['estado'] == "Ciudad de México"){ echo 'selected="selected"'; } ?>>Ciudad de México</option>
                <option value="Coahuila" <?php if($e_proveedor['estado'] == "Coahuila"){ echo 'selected="selected"'; } ?>>Coahuila</option>
                <option value="Colima" <?php if($e_proveedor['estado'] == "Colima"){ echo 'selected="selected"'; } ?>>Colima</option>
                <option value="Durango" <?php if($e_proveedor['estado'] == "Durango"){ echo 'selected="selected"'; } ?>>Durango</option>
                <option value="Guanajuato" <?php if($e_proveedor['estado'] == "Guanajuato"){ echo 'selected="selected"'; } ?>>Guanajuato</option>
                <option value="Guerrero" <?php if($e_proveedor['estado'] == "Guerrero"){ echo 'selected="selected"'; } ?>>Guerrero</option>
                <option value="Hidalgo" <?php if($e_proveedor['estado'] == "Hidalgo"){ echo 'selected="selected"'; } ?>>Hidalgo</option>
                <option value="Jalisco" <?php if($e_proveedor['estado'] == "Jalisco"){ echo 'selected="selected"'; } ?>>Jalisco</option>
                <option value="Estado de México" <?php if($e_proveedor['estado'] == "Estado de México"){ echo 'selected="selected"'; } ?>>Estado de México</option>
                <option value="Michoacán" <?php if($e_proveedor['estado'] == "Michoacán"){ echo 'selected="selected"'; } ?>>Michoacán</option>
                <option value="Morelos" <?php if($e_proveedor['estado'] == "Morelos"){ echo 'selected="selected"'; } ?>>Morelos</option>
                <option value="Nayarit" <?php if($e_proveedor['estado'] == "Nayarit"){ echo 'selected="selected"'; } ?>>Nayarit</option>
                <option value="Nuevo León" <?php if($e_proveedor['estado'] == "Nuevo León"){ echo 'selected="selected"'; } ?>>Nuevo León</option>
                <option value="Oaxaca" <?php if($e_proveedor['estado'] == "Oaxaca"){ echo 'selected="selected"'; } ?>>Oaxaca</option>
                <option value="Puebla" <?php if($e_proveedor['estado'] == "Puebla"){ echo 'selected="selected"'; } ?>>Puebla</option>
                <option value="Querétaro" <?php if($e_proveedor['estado'] == "Querétaro"){ echo 'selected="selected"'; } ?>>Querétaro</option>
                <option value="Quintana Roo" <?php if($e_proveedor['estado'] == "Quintana Roo"){ echo 'selected="selected"'; } ?>>Quintana Roo</option>
                <option value="San Luis Potosí" <?php if($e_proveedor['estado'] == "San Luis Potosí"){ echo 'selected="selected"'; } ?>>San Luis Potosí</option>
                <option value="Sinaloa" <?php if($e_proveedor['estado'] == "Sinaloa"){ echo 'selected="selected"'; } ?>>Sinaloa</option>
                <option value="Sonora" <?php if($e_proveedor['estado'] == "Sonora"){ echo 'selected="selected"'; } ?>>Sonora</option>
                <option value="Tabasco" <?php if($e_proveedor['estado'] == "Tabasco"){ echo 'selected="selected"'; } ?>>Tabasco</option>
                <option value="Tamaulipas" <?php if($e_proveedor['estado'] == "Tamaulipas"){ echo 'selected="selected"'; } ?>>Tamaulipas</option>
                <option value="Tlaxcala" <?php if($e_proveedor['estado'] == "Tlaxcala"){ echo 'selected="selected"'; } ?>>Tlaxcala</option>
                <option value="Veracruz" <?php if($e_proveedor['estado'] == "Veracruz"){ echo 'selected="selected"'; } ?>>Veracruz</option>
                <option value="Yucatán" <?php if($e_proveedor['estado'] == "Yucatán"){ echo 'selected="selected"'; } ?>>Yucatán</option>
                <option value="Zacatecas" <?php if($e_proveedor['estado'] == "Zacatecas"){ echo 'selected="selected"'; } ?>>Zacatecas</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="cp">CP</label>
              <input type="text" class="form-control" name="cp" value="<?php echo $e_proveedor['cp']; ?>">
            </div>
            <div class="form-group">
              <label for="telefono">Teléfono</label>
              <input type="text" class="form-control" name="telefono" value="<?php echo $e_proveedor['telefono']; ?>">
            </div>
            <div class="form-group">
              <label for="web">Web</label>
              <input type="text" class="form-control" name="web" value="<?php echo $e_proveedor['web']; ?>">
            </div>
            <div class="form-group">
              <label for="contacto">Contacto</label>
              <input type="text" class="form-control" name="contacto" value="<?php echo $e_proveedor['contacto']; ?>">
            </div>
            <div class="form-group">
              <label for="correo_contacto">Correo Contacto</label>
              <input type="text" class="form-control" name="correo_contacto" value="<?php echo $e_proveedor['correo_contacto']; ?>">
            </div>
            <div class="form-group">
              <label for="telefono_contacto">Teléfono Contacto</label>
              <input type="text" class="form-control" name="telefono_contacto" value="<?php echo $e_proveedor['telefono_contacto']; ?>">
            </div>
            <div class="form-group">
              <label for="extension">Extensión</label>
              <input type="text" class="form-control" name="extension" value="<?php echo $e_proveedor['extension']; ?>">
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status" class="form-control">
                <option value="0" <?php if($e_proveedor['status'] == 0){ echo 'selected="selected"'; } ?>>Inactivo</option>
                <option value="1" <?php if($e_proveedor['status'] == 1){ echo 'selected="selected"'; } ?>>Activo</option>
              </select>
            </div>
            <div class="form-group clearfix">
              <button type="submit" name="edit_proveedor" class="btn btn-primary">Guardar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
