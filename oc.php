<script type="text/javascript" src="libs/js/tablefilter.js"></script>
<?php
  $page_title = 'Listado Órdenes de Compra';
  require_once('includes/load.php');

  // Checkin What level user has permission to view this page
  page_require_level("ordenes_compra");

  // pull out all user form database
  //$all_ordenes_de_compra = find_all('ordenes_de_compra');
  $e_user = find_by_id('users',(int)$_SESSION['user_id']);
  $e_empresa = find_by_id('user_empresas',(int)$e_user["working_on"]);

  // Empresas a las cuales esta asignado el usuario
  $userE = $e_user["empresas"];
  $query = "SELECT empresa FROM user_empresas WHERE id IN (" . $userE . ");";
  $rs_result = $db->query($query);
  $empresasArray = array();
  
  while($row = $rs_result->fetch_assoc()) {
    $empresasArray[] = $row["empresa"];
  }
  include_once('layouts/header.php');
  $oc = "SELECT * FROM ordenes_de_compra WHERE empresa = '" . $e_empresa["empresa"] . "';";
  $oc_result = $db->query($oc);
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
          <span>Órdenes de Compra</span>
        </strong>
        <a href="selectArtsOc.php" class="btn btn-info pull-right tablesorter">Generar Orden de Compra</a>
      </div>
      <div class="panel-body">
        <table id="tblOC" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">OC</th>
              <th class="text-center" data-placeholder="Select Empresa">Empresa</th>
              <th class="text-center">Fecha</th>
              <th class="text-center" style="width: 15%;">Proveedor</th>
              <th class="text-center" style="width: 10%;">Total c/IVA</th>
              <th class="text-center" style="width: 10%;">Status</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
              while($row = $oc_result->fetch_assoc()) {
                if(in_array ($row["empresa"],$empresasArray)) {
                  // Obtenemos correo del proveedor
                  $ccP = "SELECT correo_contacto FROM proveedores WHERE razon_social LIKE '%" . $row["proveedor"] . "%';";
                  $ccP_result = $db->query($ccP);
                  $correo_contacto = "";
                  
                  while($row_ = $ccP_result->fetch_assoc()) {
                    $correo_contacto = $row_["correo_contacto"];
                  }
            ?>
                <tr>
                  <td class="text-center">OC-<?php echo $row["oc"];?></td>
                  <td class="text-center"><?php echo $row["empresa"];?></td>
                  <td class="text-center"><?php echo $row["fecha"];?></td>
                  <td class="text-center"><?php echo $row["proveedor"];?></td>
                  <td class="text-center">
                    <?php
                      echo "$ " . number_format($row["total"],2);
                      
                      if($row["tipo_moneda"] == "USD") {
                        echo "<br>" . $row["tipo_moneda"];
                      }
                    ?>
                  </td>
                  <td class="text-center">
                    <?php
                      $stats = requisicionStatus();
                      echo $stats[$row["status"]];
                    ?>
                  </td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_oc.php?id=<?php echo $row['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                        <i class="glyphicon glyphicon-pencil"></i>
                      </a>
                      <a href="delete_oc.php?id=<?php echo $row['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Eliminar la Orden de Compra?');">
                        <i class="glyphicon glyphicon-remove"></i>
                      </a>
                      <!--<a href="" onclick="printExternal('http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/docs/oc/OC-<?php echo $row['id'] ?>.pdf')" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Imprimir">-->
                      <a href="" onclick="printExternal('http://localhost:8888/inventario/docs/oc/OC-<?php echo $row['id'] ?>.pdf')" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Imprimir">
                        <i class="glyphicon glyphicon-print"></i>
                      </a>
                      <a onclick="sendEmail('<?php echo $correo_contacto; ?>', '<?php echo $row['id'] ?>', 'proveedor')" class="btn btn-xs btn-success" data-toggle="tooltip" title="Enviar Correo a Proveedor">
                        <i class="glyphicon glyphicon-envelope"></i>
                      </a>
                      <a onclick="sendEmail('<?php echo $correo_contacto; ?>', '<?php echo $row['id'] ?>', 'interno')" class="btn btn-xs btn-default" data-toggle="tooltip" title="Enviar Correo Interno">
                        <i class="glyphicon glyphicon-envelope"></i>
                      </a>
                    </div>
                  </td>
                </tr>
            <?php
                }
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
function sendEmail(email, IdOC, tipo) {
  try{
    var url = "http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/sendEmailProveedor.php";
    //var url = "http://127.0.0.1/inventario/sendEmailProveedor.php";
    jQuery.ajax({
      url: url,
      dataType: 'json',
      type: 'POST',
      data: {email: email, orden_compra: IdOC, tipo: tipo},
      success: function (data){
        if(data.status != "ERROR"){
          if(data.status == "SUCCESS"){
            alert(data.message);
          } else if(data.status == "EMPTY"){
            alert(data.message);
          } else if(data.status == "ERROR") {
            alert(data.message);
          }
        } else if(data.status == "ERROR"){
          alert(data.message);
        }
      },error: function (){
        alert(data.message);
      }
    });
  }catch (e){
    console.log(e);
  }
};
</script>
<script language="javascript" type="text/javascript">  
var filtersConfig = {
        base_path: 'inventario/',
        col_0: "input",
        col_1: 'none',
        col_2: 'input',
        col_3: 'select',
        col_4: 'none',
        col_5: 'select',
        col_6: 'none',
        col_7: 'none',
        col_types: [
            'string', 'string', 'string',
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
        col_widths: [
            '3%'
        ],
        default_date_type: 'YMD',
        extensions:[{ name: 'sort' }],
    };

    var tf = new TableFilter('tblOC', filtersConfig);
    var tfConfig = { enable_empty_option: true, display_all_text: "Display all", };
    tf.init();
</script>
<script>
function printExternal(url) {
  var printWindow = window.open(url, "_blank", "toolbar=0,location=0,menubar=0");
  printWindow.addEventListener('load', function(){
    printWindow.print();
  }, true);
}
</script>