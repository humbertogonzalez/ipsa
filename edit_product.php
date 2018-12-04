<?php
  $page_title = 'Edit product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level("inventarios");
  
  $e_user = find_by_id('users', (int)$_SESSION['user_id']);
  $product = find_by_sec((int)$_GET['id']);
  
  if(!$e_user) {
    $session->msg("d","Sesión Expirada / Usuario no existe.");
    redirect('index.php');
  }
  if(!$product){
    $session->msg("d","ID de Producto no existe.");
    redirect('inventario_materiales.php');
  }
  
  //update user image
  if(isset($_POST['submit'])) {
    $photo = new Media();
    $photo->upload($_FILES['file_upload']);
    if($photo->process_product($_GET['id'])){
      $session->msg('s','Imagen Actualizada.');
      redirect('edit_product.php?id=' . $_GET['id']);
    } else{
      $session->msg('d',join($photo->errors));
      redirect('edit_product.php?id=' . $_GET['id']);
    }
  }
  
  if(isset($_POST['edit_product'])){
    $req_fields = array('tipo_de_articulo','descripcion','existencia','um');
    validate_fields($req_fields);
  
    if(empty($errors)){
      $tipo_de_articulo  = remove_junk($db->escape($_POST['tipo_de_articulo']));
      $marca  = remove_junk($db->escape($_POST['marca']));
      $modelo_base   = remove_junk($db->escape($_POST['modelo_base']));
      $sku   = remove_junk($db->escape($_POST['sku']));
      $descripcion   = remove_junk($db->escape($_POST['descripcion']));
      $no_serie   = remove_junk($db->escape($_POST['no_serie']));
      $um   = remove_junk($db->escape($_POST['um']));
      $existencia  = remove_junk($db->escape($_POST['existencia']));
      $um  = remove_junk($db->escape($_POST['um']));
      $caja  = remove_junk($db->escape($_POST['caja']));
      $ubicacion  = remove_junk($db->escape($_POST['ubicacion']));
      $observaciones  = remove_junk($db->escape($_POST['observaciones']));
      
      // UPDATE      
      $sql = "UPDATE inventario SET tipo_de_articulo='{$tipo_de_articulo}',marca='{$marca}',modelo_base='{$modelo_base}',sku='{$sku}', ";
      $sql .= "descripcion='{$descripcion}',no_serie='{$no_serie}',existencia='{$existencia}',um='{$um}',caja='{$caja}',ubicacion='{$ubicacion}',observaciones='{$observaciones}' WHERE sec='{$_GET['id']}';";
      $result = $db->query($sql);
      
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"El Producto " . $descripcion . " ha sido actualizado");
        redirect("edit_product.php?id=".$_GET['id'], false);
      } else {
        $session->msg('d','Lo sentimos, ocurrió un error');
        redirect('edit_product.php?id='.$_GET['id'], false);
      }
    } else{
      $session->msg("d", $errors);
      redirect('edit_product.php?id='.$_GET['id'], false);
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
          <span>Editar Producto '<?php echo $product["descripcion"]; ?>'</span>
        </strong>
      </div>
      <!--<div>
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <div class="col-md-4" style="text-align: center;">
              <img class="" src="uploads/products/<?php echo $product['foto_del_articulo'];?>" alt="" style="width: 50%;">
            </div>
            <div class="col-md-8">
              <form class="form-inline" action="edit_product.php?id=<?php echo (int)$_GET['id'];?>" method="POST" enctype="multipart/form-data">
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
      </div>-->
      <div class="panel-body">
        <form method="post" action="edit_product.php?id=<?php echo (int)$_GET['id']?>">
          <div class="col-md-12">
            <div class="panel-body">
              <div class="form-group">
                <label for="tipo_de_articulo" class="control-label">Tipo de Artículo</label>
                <input type="text" class="form-control" name="tipo_de_articulo" required value="<?php echo $product["tipo_de_articulo"];?>">
              </div>
              <div class="form-group">
                <label for="marca" class="control-label">Marca</label>
                <input type="text" class="form-control" name="marca" value="<?php echo $product["marca"];?>">
              </div>
              <div class="form-group">
                <label for="modelo" class="control-label">Modelo o Base</label>
                <input type="text" class="form-control" name="modelo_base" value="<?php echo $product["modelo_base"];?>">
              </div>
              <div class="form-group">
                <label for="sku" class="control-label">SKU</label>
                <input type="text" class="form-control" name="sku" value="<?php echo $product["sku"];?>">
              </div>
              <div class="form-group">
                <label for="descripcion" class="control-label">Descripción</label>
                <textarea class="form-control" name="descripcion" cols="40" rows="5" required value="<?php echo $product["descripcion"];?>" placeholder="<?php echo $product["descripcion"];?>"><?php echo $product["descripcion"];?></textarea>
              </div>
              <div class="form-group">
                <label for="no_serie" class="control-label">No. de Serie</label>
                <input type="text" class="form-control" name="no_serie" value="<?php echo $product["no_serie"];?>">
              </div>
              <div class="form-group">
                <label for="existencia" class="control-label">Existencia</label>
                <input type="text" class="form-control" name="existencia" required value="<?php echo $product["existencia"];?>">
              </div>
              <div class="form-group">
                <label for="um" class="control-label">UM</label>
                <input type="text" class="form-control" name="um" required value="<?php echo $product["um"];?>">
              </div>
              <div class="form-group">
                <label for="caja" class="control-label">Caja</label>
                <input type="text" class="form-control" name="caja" value="<?php echo $product["caja"];?>">
              </div>
              <div class="form-group">
                <label for="ubicacion" class="control-label">Ubicación</label>
                <input type="text" class="form-control" name="ubicacion" value="<?php echo $product["ubicacion"];?>">
              </div>
              <div class="form-group">
                <label for="observaciones" class="control-label">Observaciones</label>
                <textarea class="form-control" name="observaciones" cols="40" rows="5" value="<?php echo $product["observaciones"];?>" placeholder="<?php echo $product["observaciones"];?>"><?php echo $product["observaciones"];?></textarea>
              </div>
              <div class="form-group clearfix" align="right">
                <button type="submit" name="edit_product" class="btn btn-info">Guardar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>