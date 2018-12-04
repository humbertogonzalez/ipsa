<?php
  $page_title = 'Editar Requisición';
  require_once('includes/load.php');
  
  // Validar nivel de permisos
  page_require_level("requisiciones");
  $empresas = find_all('user_empresas');
  $proyectos = find_all('proyectos');
  $proveedores = find_all_proveedores();
  $e_user = find_by_id('users', (int)$_SESSION['user_id']);
  $e_requisiciones = find_by_id('requisiciones', (int)$_GET['id']);
  
  if(!$e_requisiciones){
    $session->msg("d","No existe el ID de Requisición.");
    redirect('add_requisicion.php');
  }
  
  if(isset($_POST['edit_requisicion'])){
    $req_fields = array('empresa','solicitante','proyecto','fecha_surtido');
    validate_fields($req_fields);
        
    if(empty($errors)){
      $p_empresa = (int)$db->escape($_POST['empresa']);
      $p_solicitante = remove_junk($db->escape($_POST['solicitante']));
      $p_proyecto = (int)$db->escape($_POST['proyecto']);
      $p_seccion = $db->escape($_POST['seccion']);
      $p_proveedor = (int)$db->escape($_POST['proveedor']);
      $p_fecha_surtido = remove_junk($db->escape($_POST['fecha_surtido']));
      $p_status = remove_junk($db->escape($_POST['status']));
      $sql = "UPDATE requisiciones SET empresa='{$p_empresa}',solicitante='{$p_solicitante}',proyecto='{$p_proyecto}',seccion='{$p_seccion}',proveedor='{$p_proveedor}',fecha_surtido='{$p_fecha_surtido}',status='{$p_status}' WHERE id='{$_GET['id']}';";
      $result = $db->query($sql);
      
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"Requisición " . $_GET['id'] . " Actualizada");
        redirect('edit_requisicion.php?id='.(int)$_GET['id'], false);
      } else {
        $session->msg('d',' Lo sentimos, ocurrió un error al intentar actualizar!');
        redirect('edit_requisicion.php?id='.(int)$_GET['id'], false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_requisicion.php',false);
    }
  }
  
  $sql = "SELECT * FROM articulos_requisiciones WHERE requisicion='" . $_GET['id'] ."' ORDER BY sec ASC";
  $rs_result = $db->query($sql);

  // Obtenemos las OC de esta Requisición
  $sql1 = "SELECT GROUP_CONCAT(DISTINCT oc) AS o_c FROM ordenes_de_compra WHERE requisicion='" . $_GET['id'] ."'";
  $rs_result1 = $db->query($sql1);
  $rs_result1 = $db->fetch_assoc($rs_result1);
  
  // Obtenemos las entradas de la requisición
  if($rs_result1['o_c'] != "") {
    $sql2 = "SELECT GROUP_CONCAT(DISTINCT remision) AS remision FROM entradas WHERE orden_de_compra IN (" . $rs_result1['o_c'] .");";
    $rs_result2 = $db->query($sql2);
    $rs_result2 = $db->fetch_assoc($rs_result2);
  }
  
  // Obtenemos las salidas de la requisición
  $sql3 = "SELECT GROUP_CONCAT(DISTINCT remision) AS remision FROM salidas WHERE requisicion IN (" . $_GET['id'] .");";
  $rs_result3 = $db->query($sql3);
  $rs_result3 = $db->fetch_assoc($rs_result3);
  
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
          <span>Editando Requisición <b>(Req-<?php echo $_GET['id']; ?>)</b></span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_requisicion.php?id=<?php echo (int)$e_requisiciones['id'];?>" autocomplete="off">
          <div class="col-md-12">
            <div class="form-group">
              <label for="name">Empresa</label>
              <select class="form-control" name="empresa" <?php if($e_requisiciones["status"] != "2") {?> disabled="disabled" <?php } ?>>
                <option value="">-- Seleccionar Empresa --</option>
                <?php
                  foreach ($empresas as $empresa) {
                    if($empresa["id"] == $e_requisiciones["empresa"]) {
                ?>
                    <option selected="selected" value="<?php echo $empresa["id"]; ?>" /><?php echo $empresa["empresa"]; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="solicitante">Solicitante</label>
              <input type="text" autocomplete="off" class="form-control" name="solicitante" id="solicitante" placeholder="<?php echo $e_requisiciones["solicitante"]; ?>" value="<?php echo $e_requisiciones["solicitante"]; ?>" readonly="readonly">
            </div>
            <div class="form-group">
              <label for="proyecto">Proyecto</label>
              <select class="form-control" name="proyecto" <?php if($e_requisiciones["status"] != "2") {?> disabled="disabled" <?php } ?>>
                <option value="">-- Seleccionar Proyecto --</option>
                <?php
                  foreach ($proyectos as $proyecto) {
                    if($proyecto["id"] == $e_requisiciones["proyecto"]) {  
                ?>
                      <option selected="selected" value="<?php echo $proyecto["id"]; ?>" /><?php echo $proyecto["nombre"]; ?></option>
                <?php
                    } else {
                ?>
                      <option value="<?php echo $proyecto["id"]; ?>" /><?php echo $proyecto["nombre"]; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="seccion">Secci&oacute;n</label>
              <input type="text" autocomplete="off" class="form-control" name="seccion" id="seccion" placeholder="<?php echo $e_requisiciones["seccion"]; ?>" value="<?php echo $e_requisiciones["seccion"]; ?>">
            </div>
            <div class="form-group">
              <label for="proveedor">Proveedor Sugerido</label>
              <select class="form-control" name="proveedor" <?php if($e_requisiciones["status"] != "2") {?> disabled="disabled" <?php } ?>>
                <option value="">-- Seleccionar Proveedor --</option>
                <?php
                  foreach ($proveedores as $proveedor) {
                    if($proveedor["id"] == $e_requisiciones["proveedor"]) {
                ?>
                      <option selected="selected" value="<?php echo $proveedor["id"]; ?>" /><?php echo $proveedor["razon_social"]; ?></option>
                <?php
                    } else {
                  ?>
                      <option value="<?php echo $proveedor["id"]; ?>" /><?php echo $proveedor["razon_social"]; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="fecha_surtido">Debe surtirse el</label>
              <input class="form-control" type="date" name="fecha_surtido" id="fecha_surtido" value="<?php echo $e_requisiciones["fecha_surtido"]; ?>" placeholder="Debe surtirse el" <?php if($e_requisiciones["status"] != "2") {?> disabled="disabled" <?php } ?>>
            </div>
            <div class="form-group">
              <label for="prods">Productos Seleccionados</label>
              <table id="tblProdReq" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th class="text-center" data-placeholder="Select Empresa">Descripción Material/Servicio</th>
                    <th class="text-center">Unidad de Medida</th>
                    <th class="text-center">Cantidad Solicitada</th>
                    <th class="text-center">Por surtir</th>
                    <th class="text-center">Faltan</th>
                    <th class="text-center">Entregado</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  while($row = $rs_result->fetch_assoc()) {
                ?>
                  <tr>
                    <td><?php echo $row["sec"];?></td>
                    <td><?php echo $row["descripcion"]?></td>
                    <td><?php echo $row["unidad_medida"]?></td>
                    <td><?php echo $row["cantidad"]?></td>
                    <td><?php echo $row["surtir"]?></td>
                    <td><?php echo $row["faltan"]?></td>
                    <td><?php echo $row["cantidad_entregada"]?></td>
                  </tr>
                <?php }?>
               </tbody>
             </table>
            </div>
            <div class="form-group">
              <label for="prods">Documentos Relacionados</label>
              <table id="tblProdReq" class="table table-bordered table-striped">
                <tbody>
                  <tr>
                    <td style="width: 50%;">OC</td>
                    <td><?php echo $rs_result1["o_c"]?></td>
                  </tr>
                  <tr>
                    <td>Entradas</td>
                    <td><?php echo $rs_result2["remision"]?></td>
                  </tr>
                  <tr>
                    <td>Salidas</td>
                    <td><?php echo $rs_result3["remision"]?></td>
                  </tr>
               </tbody>
             </table>
            </div>
            <div class="form-group">
              <label for="status">Estatus</label>
              <select class="form-control" id="status" name="status" <?php if($e_requisiciones["status"] != "2") {?> disabled="disabled" <?php } ?>>
                <option value="">-- Actualizar Estatus --</option>
                <option value="0" <?php if($e_requisiciones["status"] == "0") { ?> selected="selected" <?php } ?>>Autorizada</option>
                <option value="1" <?php if($e_requisiciones["status"] == "1") { ?> selected="selected" <?php } ?>>Rechazada</option>
                <option value="2" <?php if($e_requisiciones["status"] == "2") { ?> selected="selected" <?php } ?>>Abierta</option>
                <option value="3" <?php if($e_requisiciones["status"] == "3") { ?> selected="selected" <?php } ?>>Cerrada</option>
                <option value="4" <?php if($e_requisiciones["status"] == "4") { ?> selected="selected" <?php } ?>>Autorizada Crítica</option>
                <option value="5" <?php if($e_requisiciones["status"] == "5") { ?> selected="selected" <?php } ?>>Autorizada Urgente</option>
                <option value="6" <?php if($e_requisiciones["status"] == "6") { ?> selected="selected" <?php } ?>>Autorizada Normal</option>
                <option value="7" <?php if($e_requisiciones["status"] == "7") { ?> selected="selected" <?php } ?>>Eliminada</option>
              </select>
              <div id="otherType" style="display:none;">
                <br>
                <textarea class="form-control" type="text" name="observaciones" id="observaciones" placeholder="Observaciones" value="<?php echo $observaciones; ?>"></textarea>
              </div>
            </div>
            <?php if($e_requisiciones["status"] == "2") {?>
              <div class="form-group clearfix">
                <button type="submit" name="edit_requisicion" class="btn btn-primary">Actualizar Requisición</button>
              </div>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
jQuery('#status').change(function() {
  var selected = jQuery(this).val();
  if(selected === '6') {
    jQuery('#otherType').css("display", "block");
  } else {
    jQuery('#otherType').css("display", "none");
  }
});
</script>
<?php include_once('layouts/footer.php'); ?>