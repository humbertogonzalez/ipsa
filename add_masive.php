<?php
  $page_title = 'Alta de Material/Servicio';
  require_once('includes/load.php');
  $path = 'libs/PHPExcel/IOFactory.php';
  include $path;

  page_require_level("inventario");
  
  // Upload Excel masivo
  if(isset($_POST['submit'])) {
    $photo = new Media();
    $photo->upload($_FILES['file_upload']);
    if($photo->process_file()){
      $session->msg('s','Archivo Guardado.');
      redirect('add_masive.php');
    } else{
      $session->msg('d',join($photo->errors));
      redirect('add_masive.php');
    }
  }
  
  if(isset($_POST['process'])){
    /*$req_fields = array('marca','modelo','descripcion','um_almacen','tipo_de_articulo','proveedor');
    validate_fields($req_fields);
    if(empty($errors)){
      // Demás campos del formulario
      $marca  = remove_junk($db->escape($_POST['marca']));
      $modelo   = remove_junk($db->escape($_POST['modelo']));
      $descripcion   = remove_junk($db->escape($_POST['descripcion']));
      $um_almacen   = remove_junk($db->escape($_POST['um_almacen']));
      $um_compra  = remove_junk($db->escape($_POST['um_compra']));
      $um_surtir  = remove_junk($db->escape($_POST['um_surtir']));
      $tipo_de_articulo  = remove_junk($db->escape($_POST['tipo_de_articulo']));
      $sku_proveedor  = remove_junk($db->escape($_POST['sku_proveedor']));
      $proveedor  = remove_junk($db->escape($_POST['proveedor']));
      $punto_de_reorden  = remove_junk($db->escape($_POST['punto_de_reorden']));
      $stock_minimo  = remove_junk($db->escape($_POST['stock_minimo']));
      $stock_maximo  = remove_junk($db->escape($_POST['stock_maximo']));
      $cantidad_a_surtir  = remove_junk($db->escape($_POST['cantidad_a_surtir']));
      $costo_reposicion  = remove_junk($db->escape($_POST['costo_reposicion']));
      $costo_promedio  = remove_junk($db->escape($_POST['costo_promedio']));
      $costo_estandar  = remove_junk($db->escape($_POST['costo_estandar']));
      $clase_de_articulo  = remove_junk($db->escape($_POST['clase_de_articulo']));
      $fecha_ultima_compra  = remove_junk($db->escape($_POST['fecha_ultima_compra']));
      $existencia_actual  = remove_junk($db->escape($_POST['existencia_actual']));
      $comprometido  = remove_junk($db->escape($_POST['comprometido']));
      $disponible  = remove_junk($db->escape($_POST['disponible']));
      $entradas_en_el_mes  = remove_junk($db->escape($_POST['entradas_en_el_mes']));
      $salidas_en_el_mes  = remove_junk($db->escape($_POST['salidas_en_el_mes']));
      $entradas_en_el_ano  = remove_junk($db->escape($_POST['entradas_en_el_ano']));
      $salidas_en_el_ano  = remove_junk($db->escape($_POST['salidas_en_el_ano']));
      $saldo_inicial_del_mes  = remove_junk($db->escape($_POST['saldo_inicial_del_mes']));
      $estatus  = remove_junk($db->escape($_POST['estatus']));
      $fecha_utimo_cambio_en_registro  = remove_junk($db->escape($_POST['fecha_utimo_cambio_en_registro']));
      
      $find = "SELECT id FROM mst_item ORDER BY id DESC LIMIT 1;";
      $result = $db->query($find);
      $result = $db->fetch_assoc($result);
      $id = "IM0" . ($result['id'] + 1);
      $query  = "INSERT INTO mst_item(`mst_item_key`, `marca`, `modelo`, `descripcion`, `um_almacen`, `um_compra`, `um_surtir`, `tipo_de_articulo`, `sku_proveedor`, `proveedor`, `punto_de_reorden`, `stock_minimo`, `stock_maximo`, `cantidad_a_surtir`, `costo_reposicion`, `costo_promedio`, `costo_estandar`, `clase_de_articulo`, `fecha_ultima_compra`, `existencia_actual`, `comprometido`, `disponible`, `entradas_en_el_mes`, `salidas_en_el_mes`, `entradas_en_el_ano`, `salidas_en_el_ano`, `saldo_inicial_del_mes`, `foto_del_articulo`, `estatus`, `fecha_utimo_cambio_en_registro`) VALUES (";
      $query .= "'{$id}','{$marca}','{$modelo}','{$descripcion}','{$um_almacen}','{$um_compra}','{$um_surtir}','{$tipo_de_articulo}','{$sku_proveedor}','{$proveedor}','{$punto_de_reorden}','{$stock_minimo}','{$stock_maximo}','{$cantidad_a_surtir}','{$costo_reposicion}','{$costo_promedio}','{$costo_estandar}','{$clase_de_articulo}','{$fecha_ultima_compra}','{$existencia_actual}','{$comprometido}','{$disponible}','{$entradas_en_el_mes}','{$salidas_en_el_mes}','{$entradas_en_el_ano}','{$salidas_en_el_ano}','{$saldo_inicial_del_mes}','{$foto_del_articulo}','{$estatus}','{$fecha_utimo_cambio_en_registro}'";
      $query .= ")";

      if($db->query($query)){
        //$session->msg('s',"Producto Agregado");
        $photo = new Media();
        $photo->upload($_FILES['foto_del_articulo']);
        if($photo->process_product($id)){
          $session->msg('s','Producto Agregado');
        } else{
          $session->msg('d','Producto Agregado, ' . join($photo->errors));
        }
        redirect('add_producto.php', false);
      } else {
        $session->msg('d','Lo sentimos, ocurrió un error.');
        redirect('add_producto.php', false);
      }
    } else{
      $session->msg("d", $errors);
      redirect('add_producto.php',false);
    }*/
    // Procesamos Excel
    $ignored = array('.', '..', '.svn', '.htaccess');
    /*$files = scandir('uploads/masivo', SCANDIR_SORT_ASCENDING );
    $newest_file = $files[2];*/
    $dir = "uploads/masivo";
    $files = array();    
    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);
    $newest_file = $dir . "/" . $files[0];
    
		try {
			echo cargaMasiva($newest_file);
		} catch ( Exception $e ) {
			error_log('Ocurrió un Error: ' . $e->getMessage() . "\n", 3, "debug.log");
      $session->msg("d", $e->getMessage());
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
          <span>Alta Masiva de Inventario</span>
        </strong>
      </div>
      <div>
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="col-md-12">
              <form class="form-inline" action="add_masive.php" method="POST" enctype="multipart/form-data">
                <div class="form-group" style="margin-right: 10%;">
                  <label for="name">1 - Subir Archivo a procesar</label><br>
                  <small>Extensiones permitidas (xls, xlsx, csv)</small>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-btn">
                      <input type="file" name="file_upload" multiple="multiple" class="btn btn-primary btn-file"/>
                    </span>
                    <button type="submit" name="submit" class="btn btn-default">Subir Archivo</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <form method="post" action="add_masive.php" autocomplete="off">
          <div class="col-md-12">
            <div class="form-group">
              <div class="input-group">
                <button type="submit" id="process" name="process" class="btn btn-default">Cargar Inventario</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#process').click(function() {
        return window.confirm("¿Desea cargar el archivo?");
    });
});
</script>
<?php include_once('layouts/footer.php'); ?>