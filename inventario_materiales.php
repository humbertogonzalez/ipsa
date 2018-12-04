<?php
  $page_title = 'Materiales/Servicios';
  require_once('includes/load.php');

  // Checkin What level user has permission to view this page
  page_require_level("inventarios");

  $all_users = find_all_user();
  //$inventario = find_all('inventario');
  $empresas = find_all('user_empresas');
  if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
  $results_per_page = 50;
  $start_from = ($page-1) * $results_per_page;
  $sql = "SELECT id,mst_item_key,marca,modelo,descripcion,tipo_de_articulo,existencia_actual FROM mst_item ORDER BY mst_item_key ASC LIMIT $start_from , " . $results_per_page;
  $rs_result = $db->query($sql);
  include_once('layouts/header.php');
?>
<script type="text/javascript" src="libs/js/tablefilter.js"></script>
<!--<script type="text/javascript" src="libs/js/paging.js"></script>-->
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
          <span>Materiales/Servicios</span>
        </strong>
        <a href="add_producto.php" class="btn btn-info pull-right tablesorter">Alta de material/servicio</a>
        <!--<a href="add_user.php" class="btn btn-info pull-right">Agregar Nuevo Usuario</a>-->
      </div>
      <div class="panel-body">
        <!--<input class="searchUser" type="text" id="searchUser" onkeyup="searchUsername();" placeholder="Búsqueda de Usuario"><br><br>-->
        <table id="tblInventarios" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;"></th>
              <th class="text-center">Marca</th>
              <th class="text-center">Modelo/Base</th>
              <th class="text-center">Descripción</th>
              <th class="text-center">Tipo de Artículo</th>
              <th class="text-center">Existencia Actual</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody id="myTable">
            <?php //foreach($inventario as $inventory) {?>
            <?php while($row = $rs_result->fetch_assoc()) { ?>
              <tr>
                <td class="text-center"><?php echo $row["mst_item_key"];?></td>
                <td><?php echo remove_junk($row["marca"])?></td>
                <td><?php echo remove_junk($row["modelo"])?></td>
                <td><?php echo remove_junk($row["descripcion"])?></td>
                <td><?php echo remove_junk($row["tipo_de_articulo"])?></td>
                <td><?php echo remove_junk($row["existencia_actual"])?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_product.php?id=<?php echo (int)$row['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$row['id'];?>" onclick="return confirmDelete(this);" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Baja">
                      <i class="glyphicon glyphicon-remove"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php 
            $sql = "SELECT COUNT(*) AS total FROM mst_item";
            $result = $db->query($sql);
            $row = $result->fetch_assoc();
            $total_pages = ceil($row["total"] / $results_per_page);
            
            echo '<div id="pageNavPosition">';
            
            for ($i=1; $i<=$total_pages; $i++) {
              echo "<a href='inventario_materiales.php?page=".$i."'";
              if ($i==$page)  echo " class='curPage'";
              echo ">".$i."</a> "; 
            };
            
            echo "</div>";
        ?>
      </div>
    </div>
  </div>
</div>
<script>
function confirmDelete(link) {
  if (confirm("¿Esta seguro de querer realizar la acción?")) {
      doAjax(link.href, "POST"); // doAjax needs to send the "confirm" field
  }
  return false;
}
</script>
<?php include_once('layouts/footer.php'); ?>
<script language="javascript" type="text/javascript">  
var filtersConfig = {
        base_path: 'inventario/',
        col_0: "none",
        col_1: 'select',
        col_2: 'select',
        col_3: 'select',
        col_4: 'none',
        col_5: 'none',
        col_6: 'none',
        col_7: 'none',
        col_8: 'none',
        col_types: [
            'string', 'string', 'number',
            'number', 'number', 'string',
            'string', 'number', 'number'
        ],
        /*custom_options: {
            cols:[5],
            texts: [[
                '0 - 25 000',
                '100 000 - 1 500 000'
            ]],
            values: [[
                '>2017-07-31 && <=25000',
                '>100000 && <=1500000'
            ]],
            sorts: [false]
        },*/
        
        extensions:[{ name: 'sort' }],
    };

    var tf = new TableFilter('tblInventarios', filtersConfig);
    var tfConfig = { enable_empty_option: true, display_all_text: "Display all", };
    tf.init();
</script>  