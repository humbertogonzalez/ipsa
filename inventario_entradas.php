<script type="text/javascript" src="libs/js/tablefilter.js"></script>
<?php
  $page_title = 'Entrada de Materiales/Servicios';
  require_once('includes/load.php');

  // Checkin What level user has permission to view this page
  page_require_level("entradas");

  $all_users = find_all_user();
  //$entradas = find_all('entradas');
  $entradas = "SELECT orden_de_compra, remision, fecha_de_ingreso FROM entradas GROUP BY remision ORDER BY id;";
  $rs_result = $db->query($entradas);
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
          <span>Entrada de Materiales/Servicios</span>
        </strong>
        <a href="add_entrada_devolucion.php" class="btn btn-info pull-right tablesorter">Registro de Entrada por Devolución</a>
        <a href="add_entrada.php" class="btn btn-info pull-right tablesorter" style="margin-right: 20px;">Registro de Entrada por OC</a>
      </div>
      <div class="panel-body">
        <table id="tblEntradas" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">ID</th>
              <th class="text-center">Orden de Compra</th>
              <th class="text-center">Remisión</th>
              <th class="text-center">Fecha de Ingreso</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody id="myTable">
            <?php
              while($row = $rs_result->fetch_assoc()) {
            ?>
              <tr>
                <td class="text-center"><?php echo $row["remision"];?></td>
                <td class="text-center"><?php echo $row["orden_de_compra"]?></td>
                <td class="text-center"><?php echo "EAV-" . remove_junk($row["remision"])?></td>
                <td class="text-center"><?php echo remove_junk($row["fecha_de_ingreso"])?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="delete_entrada.php?id=<?php echo (int)$row['id'];?>" onclick="return confirmDelete(this);" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Baja">
                      <i class="glyphicon glyphicon-remove"></i>
                    </a>
                    <!--<a href="" onclick="printExternal('http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/docs/entradas/EAV-<?php echo $row['remision'] ?>.pdf')" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Imprimir">-->
                    <a href="" onclick="printExternal('http://127.0.0.1/inventario/docs/entradas/EAV-<?php echo $row['remision'] ?>.pdf')" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Imprimir">
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
      doAjax(link.href, "POST");
  }
  return false;
}
</script>
<script language="javascript" type="text/javascript">  
var filtersConfig = {
        base_path: 'inventario/',
        col_0: "none",
        col_1: 'select',
        col_2: 'input',
        col_3: 'none',
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

    var tf = new TableFilter('tblEntradas', filtersConfig);
    var tfConfig = { enable_empty_option: true, display_all_text: "Display all", };
    tf.init();
</script>
<?php include_once('layouts/footer.php'); ?>