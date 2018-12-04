<?php
  $page_title = 'Alta de Inventario';
  require_once('includes/load.php');
  
  // Checkin What level user has permission to view this page
  page_require_level("inventario");
  
  $all_items = find_all("item_type");

  //$all_photo = find_all('media');
  if(isset($_POST['add_inventario'])){
    $req_fields = array('descripcion');
    validate_fields($req_fields);
    
    if(empty($errors)){
      // Demás campos del formulario
      $tipo_de_articulo = remove_junk($db->escape($_POST['tipo_de_articulo']));
      $marca  = remove_junk($db->escape($_POST['marca']));
      $modelo_base = remove_junk($db->escape($_POST['modelo_base']));
      $sku = remove_junk($db->escape($_POST['sku']));
      $descripcion = remove_junk($db->escape($_POST['descripcion']));
      //$no_serie = remove_junk($db->escape($_POST['no_serie']));
      $no_serie = 0;
      /*$oc = remove_junk($db->escape($_POST['oc']));
      $remision_ingreso = remove_junk($db->escape($_POST['remision']));
      $fecha_ingreso   = remove_junk($db->escape($_POST['fecha_ingreso']));
      $cantidad = remove_junk($db->escape($_POST['cantidad']));*/
      $um = remove_junk($db->escape($_POST['um']));
      $caja = remove_junk($db->escape($_POST['caja']));
      $ubicacion = remove_junk($db->escape($_POST['ubicacion']));
      /*$asignado = remove_junk($db->escape($_POST['asignado']));
      $remision_salida = remove_junk($db->escape($_POST['remision_salida']));
      $cantidad_salida = remove_junk($db->escape($_POST['cantidad_salida']));
      $entregado = remove_junk($db->escape($_POST['entregado']));
      $fecha_salida = remove_junk($db->escape($_POST['fecha_salida']));
      $existencia = remove_junk($db->escape($_POST['existencia']));
      $observaciones = remove_junk($db->escape($_POST['observaciones']));*/
      
      // Buscamos último ID
      $find = "SELECT sec FROM inventario ORDER BY sec DESC LIMIT 1;";
      $result = $db->query($find);
      $result = $db->fetch_assoc($result);
      $id = $result['sec'] + 1;
      
      // Hacemos insert del producto en tabla inventario
      //$query  = "INSERT INTO `inventario`(`sec`, `tipo_de_articulo`, `marca`, `modelo_base`, `sku`, `descripcion`, `no_serie`, `orden_de_compra`,`remision_ingreso`, `fecha_de_ingreso`, `cantidad`, `um`, `caja`, `ubicacion`, `asignado_a`, `remision_salida`, `cantidad_salida`, `entregado`,`fecha_salida`, `existencia`, `observaciones`) VALUES (";
      $query  = "INSERT INTO `inventario`(`sec`, `tipo_de_articulo`, `marca`, `modelo_base`, `sku`, `descripcion`, `no_serie`, `um`,`caja`,`ubicacion`, `existencia`) VALUES (";
      $query .= "'{$id}','{$tipo_de_articulo}','{$marca}','{$modelo_base}','{$sku}','{$descripcion}','{$no_serie}','{$um}','{$caja}','{$ubicacion}',0";
      $query .= ")";

      if($db->query($query)){
        $session->msg('s','Inventario Agregado');
        redirect('add_inventario.php', false);
      } else {
        $session->msg('d','Lo sentimos, ocurrió un error.');
        redirect('add_inventario.php', false);
      }
    } else{
      $session->msg("d", $errors);
      redirect('add_inventario.php',false);
    }
  }
  
  include_once('layouts/header.php');
  include_once('search.php');
?>
<script type="text/javascript" src="libs/js/jquery-ui.js"></script>
<script type="text/javascript" src="libs/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="libs/css/jquery-ui.css"/>
<link rel="stylesheet" href="libs/css/jquery-ui.min.css"/>
<script>
  $(function() {
    var vec_pal = new Array();
    <?php
      for($p=0; $p < count($arreglo_php); $p++) {
    ?>
        vec_pal.push('<?php echo str_replace("&quot;",'"',$arreglo_php[$p]); ?>');  
    <?php } ?>
    $("#busca").autocomplete({
      source: vec_pal
    });
  });
</script>
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
          <span>Alta de Inventario</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_inventario.php" class="clearfix" enctype="multipart/form-data">
          <div class="col-md-12">
            <div class="form-group">
              <label for="tipo_de_articulo" class="control-label">Tipo de Artículo</label>
              <select class="form-control" name="tipo_de_articulo" id="tipo_de_articulo">
                <option value="">-- Seleccionar Tipo de Artículo --</option>
                <?php foreach ($all_items as $type) { ?>
                  <option value="<?php echo $type["clave"]; ?>" /><?php echo $type["descripcion"]; ?><br />
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="marca" class="control-label">Marca</label>
              <input type="text" class="form-control" name="marca">
            </div>
            <div class="form-group">
              <label for="modelo_base" class="control-label">Modelo/Base</label>
              <input type="text" class="form-control" name="modelo_base">
            </div>
            <div class="form-group">
              <label for="sku" class="control-label">SKU</label>
              <input type="text" class="form-control" name="sku">
            </div>
            <div class="form-group">
              <label for="descripcion" class="control-label">Descripción</label>
              <!--<textarea class="form-control" name="descripcion" cols="40" rows="5" required></textarea>-->
              <input type="text" class="form-control" id="busca" name="descripcion">
            </div>
            <!--<div class="form-group">
              <label for="no_serie" class="control-label">No. Serie</label>
              <input type="text" class="form-control" name="no_serie">
            </div>-->
            <!--<div class="form-group">
              <label for="oc" class="control-label">Orden de Compra</label>
              <input type="text" class="form-control" name="oc">
            </div>
            <div class="form-group">
              <label for="remision" class="control-label">Remisión Ingreso</label>
              <input type="text" class="form-control" name="remision" required>
            </div>
            <div class="form-group">
              <label for="fecha_ingreso" class="control-label">Fecha de Ingreso</label>
              <input type="text" class="form-control" name="fecha_ingreso" value="<?php echo date('Y-m-d');?>" readonly>
            </div>
            <div class="form-group">
              <label for="cantidad" class="control-label">Cantidad</label>
              <input type="text" class="form-control" name="cantidad">
            </div>-->
            <div class="form-group">
              <label for="um" class="control-label">UM</label>
              <!--<input type="text" class="form-control" name="um">-->
              <select id="um" name="um" class="form-control">
                <option value="">-- Seleccione una opción--</option>
                <option value="Bobina">Bobina</option>
                <option value="Bote">Bote</option>
                <option value="Frasco">Frasco</option>
                <option value="Hojas">Hojas</option>
                <option value="Juego">Juego</option>
                <option value="Kilo">Kilo</option>
                <option value="Kilos">Kilos</option>
                <option value="Kit">Kit</option>
                <option value="Licencias">Licencias</option>
                <option value="Lote">Lote</option>
                <option value="Metro">Metro</option>
                <option value="Metros">Metros</option>
                <option value="Pares">Pares</option>
                <option value="Pieza">Pieza</option>
                <option value="Piezas">Piezas</option>
              </select>
            </div>
            <div class="form-group">
              <label for="caja" class="control-label">Caja</label>
              <input type="text" class="form-control" name="caja">
            </div>
            <div class="form-group">
              <label for="ubicacion" class="control-label">Ubicación</label>
              <input type="text" class="form-control" name="ubicacion">
            </div>
            <div class="form-group clearfix">
              <button type="submit" id="add_inventario" name="add_inventario" class="btn btn-info">Guardar</button>
            </div>
          </div>
          <!--<div class="col-md-6">
            <div class="panel panel-default panel-6">
              <div class="panel-body">
                <div class="form-group">
                  <label for="um" class="control-label">UM</label>
                  <input type="text" class="form-control" name="um">
                </div>
                <div class="form-group">
                  <label for="caja" class="control-label">Caja</label>
                  <input type="text" class="form-control" name="caja">
                </div>
                <div class="form-group">
                  <label for="ubicacion" class="control-label">Ubicación</label>
                  <input type="text" class="form-control" name="ubicacion">
                </div>
                <div class="form-group">
                  <label for="asignado" class="control-label">Asignado A</label>
                  <input type="text" class="form-control" name="asignado">
                </div>
                <div class="form-group">
                  <label for="remision_salida" class="control-label">Remisión Salida</label>
                  <input type="text" class="form-control" name="remision_salida">
                </div>
                <div class="form-group">
                  <label for="cantidad_salida" class="control-label">Cantidad Salida</label>
                  <input type="text" class="form-control" name="cantidad_salida">
                </div>
                <div class="form-group">
                  <label for="entregado" class="control-label">Entregado</label>
                  <input type="text" class="form-control" name="entregado">
                </div>
                <div class="form-group">
                  <label for="fecha_salida" class="control-label">Fecha Salida</label>
                  <input type="date" class="form-control" name="fecha_salida">
                </div>
                <div class="form-group">
                  <label for="existencia" class="control-label">Existencia</label>
                  <input type="text" class="form-control" name="existencia">
                </div>
                <div class="form-group">
                  <label for="observaciones" class="control-label">Observaciones</label>
                  <textarea class="form-control" name="observaciones" cols="40" rows="5"></textarea>
                </div>
                <div class="form-group clearfix">
                  <button type="submit" name="add_inventario" class="btn btn-info">Guardar</button>
                </div>
              </div>
            </div>
          </div>-->
        </form>
      </div>
    </div>
  </div>
</div>
<script>
// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#add_inventario').click(function() {
        return window.confirm("¿Desea crear el Producto?");
    });
});
</script>
<?php include_once('layouts/footer.php'); ?>
