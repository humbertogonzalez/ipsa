<?php
  $page_title = 'Alta de Material/Servicio';
  require_once('includes/load.php');
  
  // Checkin What level user has permission to view this page
  page_require_level("inventario");

  //$all_photo = find_all('media');
  if(isset($_POST['add_producto'])){
    $req_fields = array('marca','modelo','descripcion','um_almacen','tipo_de_articulo','proveedor');
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
          Alta de Material/Servicio
        </strong>
      </div>
    </div>
  </div>
  <form method="post" action="add_producto.php" class="clearfix" enctype="multipart/form-data">
    <div class="col-md-6">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="marca" class="control-label">Marca</label>
            <input type="text" class="form-control" name="marca" required>
          </div>
          <div class="form-group">
            <label for="modelo" class="control-label">Modelo o Base</label>
            <input type="text" class="form-control" name="modelo" required>
          </div>
          <div class="form-group">
            <label for="descripcion" class="control-label">Descripción</label>
            <textarea class="form-control" name="descripcion" cols="40" rows="5" required></textarea>
          </div>
          <div class="form-group">
            <label for="um_almacen" class="control-label">UM Almacén</label>
            <input type="text" class="form-control" name="um_almacen" required>
          </div>
          <div class="form-group">
            <label for="um_compra" class="control-label">UM Compra</label>
            <input type="text" class="form-control" name="um_compra">
          </div>
          <div class="form-group">
            <label for="um_surtir" class="control-label">UM Surtir</label>
            <input type="text" class="form-control" name="um_surtir">
          </div>
          <div class="form-group">
            <label for="tipo_de_articulo" class="control-label">Tipo de Artículo</label>
            <input type="text" class="form-control" name="tipo_de_articulo" required>
          </div>
          <div class="form-group">
            <label for="sku_proveedor" class="control-label">SKU Proveedor</label>
            <input type="text" class="form-control" name="sku_proveedor">
          </div>
          <div class="form-group">
            <label for="proveedor" class="control-label">Proveedor</label>
            <input type="text" class="form-control" name="proveedor" required>
          </div>
          <div class="form-group">
            <label for="punto_de_reorden" class="control-label">Punto de Reorden</label>
            <input type="text" class="form-control" name="punto_de_reorden">
          </div>
          <div class="form-group">
            <label for="stock_minimo" class="control-label">Stock mínimo</label>
            <input type="text" class="form-control" name="stock_minimo">
          </div>
          <div class="form-group">
            <label for="stock_maximo" class="control-label">Stock máximo</label>
            <input type="text" class="form-control" name="stock_maximo">
          </div>
          <div class="form-group">
            <label for="cantidad_a_surtir" class="control-label">Cantidad a surtir</label>
            <input type="text" class="form-control" name="cantidad_a_surtir">
          </div>
          <div class="form-group">
            <label for="costo_reposicion" class="control-label">Costo reposición</label>
            <input type="text" class="form-control" name="costo_reposicion">
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="costo_promedio" class="control-label">Costo promedio</label>
            <input type="text" class="form-control" name="costo_promedio">
          </div>
          <div class="form-group">
            <label for="costo_estandar" class="control-label">Costo Estándar</label>
            <input type="text" class="form-control" name="costo_estandar">
          </div>
          <div class="form-group">
            <label for="clase_de_articulo" class="control-label">Clase de Artículo</label>
            <input type="text" class="form-control" name="clase_de_articulo">
          </div>
          <div class="form-group">
            <label for="fecha_ultima_compra" class="control-label">Fecha Última Compra</label>
            <input type="date" class="form-control" name="fecha_ultima_compra">
          </div>
          <div class="form-group">
            <label for="existencia_actual" class="control-label">Existencia Actual</label>
            <input type="text" class="form-control" name="existencia_actual">
          </div>
          <div class="form-group">
            <label for="comprometido" class="control-label">Comprometido</label>
            <input type="text" class="form-control" name="comprometido">
          </div>
          <div class="form-group">
            <label for="disponible" class="control-label">Disponible</label>
            <input type="text" class="form-control" name="disponible">
          </div>
          <div class="form-group">
            <label for="entradas_en_el_mes" class="control-label">Entradas en el mes</label>
            <input type="text" class="form-control" name="entradas_en_el_mes">
          </div>
          <div class="form-group">
            <label for="salidas_en_el_mes" class="control-label">Salidas en el Mes</label>
            <input type="text" class="form-control" name="salidas_en_el_mes">
          </div>
          <div class="form-group">
            <label for="entradas_en_el_ano" class="control-label">Entradas en el Ano</label>
            <input type="text" class="form-control" name="entradas_en_el_ano">
          </div>
          <div class="form-group">
            <label for="salidas_en_el_ano" class="control-label">Salidas en el Ano</label>
            <input type="text" class="form-control" name="salidas_en_el_ano">
          </div>
          <div class="form-group">
            <label for="saldo_inicial_del_mes" class="control-label">Saldo Inicial del Mes</label>
            <input type="text" class="form-control" name="saldo_inicial_del_mes">
          </div>
          <div class="form-group">
            <label for="foto_del_articulo" class="control-label">Foto del Artículo</label>
            <!--<input type="text" class="form-control" name="foto_del_articulo" required>-->
            <input type="file" name="foto_del_articulo" id="foto_del_articulo" class="btn btn-primary btn-file"/>
          </div>
          <div class="form-group">
            <label for="estatus" class="control-label">Estatus</label>
            <input type="text" class="form-control" name="estatus" required>
          </div>
          <div class="form-group">
            <label for="fecha_utimo_cambio_en_registro" class="control-label">Fecha último cambio en registro</label>
            <input type="date" class="form-control" name="fecha_utimo_cambio_en_registro">
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="add_producto" class="btn btn-info">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<?php include_once('layouts/footer.php'); ?>
