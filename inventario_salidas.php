<?php
  $page_title = 'Salida de Materiales/Servicios';
  require_once('includes/load.php');

  // Checkin What level user has permission to view this page
  page_require_level("salidas");

  $all_users = find_all_user();
  //$salidas = find_all('salidas');
  $salidas = "SELECT remision, fecha FROM salidas GROUP BY remision ORDER BY id;";
  $rs_result = $db->query($salidas);
  include_once('layouts/header.php');
?>
<script type="text/javascript" src="libs/js/tablefilter.js"></script>
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
          <span>Salida de Materiales/Servicios</span>
        </strong>
        <a href="add_salida.php" class="btn btn-info pull-right tablesorter">Registro de Salida(s)</a>
      </div>
      <div class="panel-body">
        <!--<input class="searchUser" type="text" id="searchUser" onkeyup="searchUsername();" placeholder="Búsqueda de Usuario"><br><br>-->
        <table id="tblEntradas" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 200px;">ID</th>
              <!--<th class="text-center">Orden de Compra</th>-->
              <th class="text-center">Remisión</th>
              <th class="text-center">Fecha de Ingreso</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody id="myTable">
            <?php while($row = $rs_result->fetch_assoc()) { ?>
              <tr>
                <td class="text-center"><?php echo $row["remision"];?></td>
                <!--<td class="text-center"><?php //echo $row["orden_de_compra"]?></td>-->
                <td class="text-center"><?php echo "VAS-" . remove_junk($row["remision"])?></td>
                <td class="text-center"><?php echo remove_junk($row["fecha"])?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <!--<a href="edit_.php?id=<?php echo (int)$row['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </a>-->
                    <a href="delete_salida.php?id=<?php echo (int)$row['id'];?>" onclick="return confirmDelete(this);" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Baja">
                      <i class="glyphicon glyphicon-remove"></i>
                    </a>
                    <!--<a href="" onclick="printExternal('http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/docs/salidas/VAS-<?php echo $row['remision'] ?>.pdf')" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Imprimir">-->
                    <a href="" onclick="printExternal('http://127.0.0.1/inventario/docs/salidas/VAS-<?php echo $row['remision'] ?>.pdf')" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Imprimir">
                      <i class="glyphicon glyphicon-print"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
function printExternal(url) {
  var printWindow = window.open(url, "_blank", "toolbar=0,location=0,menubar=0");
  printWindow.addEventListener('load', function(){
    printWindow.print();
  }, true);
}

function confirmDelete(link) {
  if (confirm("¿Esta seguro de querer realizar la acción?")) {
      doAjax(link.href, "POST"); // doAjax needs to send the "confirm" field
  }
  return false;
}
</script>
<script language="javascript" type="text/javascript">  
var filtersConfig = {
        base_path: 'inventario/',
        col_0: "none",
        col_1: 'select',
        col_2: 'select',
        col_3: 'none',
        col_4: 'none',
        col_5: 'select',
        col_6: 'date',
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
        default_date_type: 'YYYYMMDD'
    };

    var tf = new TableFilter('tblEntradas', filtersConfig);
    var tfConfig = { enable_empty_option: true, display_all_text: "Display all", };
    tf.init();
</script>
<?php include_once('layouts/footer.php'); ?>