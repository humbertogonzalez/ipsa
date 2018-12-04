<?php
  $page_title = 'Inventarios';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level("inventarios");
  $products = join_product_table();
  
  include_once('layouts/header.php');
?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Seleccione una Opción</span>
       </strong>
         <a href="add_product.php" class="btn btn-info pull-right tablesorter">Alta de Producto</a>
      </div>
      <div class="panel-body">
        
        <div class="col-md-6">
          <ul id="opInv">
            <li>
              <a href="admin.php">
                <i class="glyphicon glyphicon-home"></i>
                <span>Insumos/Materiales-Servicios</span>
              </a>
            </li>
            <li>
              <a href="admin.php">
                <i class="glyphicon glyphicon-home"></i>
                <span>Inventario Disponible</span>
              </a>
            </li>
            <li>
              <a href="admin.php">
                <i class="glyphicon glyphicon-home"></i>
                <span>Salidas</span>
              </a>
            </li>
            <li>
              <a href="admin.php">
                <i class="glyphicon glyphicon-home"></i>
                <span>Entradas</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
  <?php include_once('layouts/footer.php'); ?>
