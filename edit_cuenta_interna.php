<style>
  .panel-6 {
    min-height: 600px;
  }
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
  }
  input[type="file"] {
    display: initial !important;
  }
  .btn {
    font-size: 10px !important;
  }
</style>
<?php
  $page_title = 'Editar Cuenta Interna';
  require_once('includes/load.php');
  
  // Únicamente el Administrador puede ver funciones de Usuarios
  if($_GET['id']== ""){
    $session->msg("d","No existe la cuenta por pagar.");
    redirect('tesoreria_internas.php');
  }
  
  // Obtenemos ID del proveedor
  $proveedor = "SELECT proveedor FROM tesoreria WHERE id=" . $_GET['id'];
  $resultP = $db->query($proveedor);
  $resultP = $db->fetch_assoc($resultP);
  
  // Obtenemos todos los registros de las OC (Gastos)
  $cuentas = "SELECT id,proveedor,nombre,fecha_pago,SUM(monto_pago) AS total,SUM(abono) AS abono,(SUM(monto_pago) - SUM(abono)) as saldo_restante,peridiocidad, GROUP_CONCAT(DISTINCT case when descripcion LIKE '%OC%' THEN descripcion END) AS descripcion FROM tesoreria WHERE `proveedor` LIKE '%" . $resultP["proveedor"] . "%'";
  $resultC = $db->query($cuentas);
  $resultC = $db->fetch_assoc($resultC);
  
  // Obtenemos todos los registros de los abonos
  $abonos = "SELECT DISTINCT descripcion AS desc2, descripcion, abono, fecha_pago, comprobante FROM `tesoreria` WHERE `tipo`=1 AND `proveedor` LIKE '%" . $resultP["proveedor"] . "%'";
  $resultA = $db->query($abonos);
  
  // Buscamos las OC, suma de abonos y monto total
  $totalOC = "SELECT id, descripcion, monto_pago, SUM(abono) AS sum_abono FROM `tesoreria` WHERE `proveedor` LIKE '%" . $resultP["proveedor"] . "%' GROUP BY descripcion HAVING monto_pago > SUM(abono);";
  $resultOC = $db->query($totalOC);
  $count = $resultOC->num_rows;
  $_desc = "";
  
  // Distinct
  $distinctA = "SELECT DISTINCT descripcion FROM `tesoreria` WHERE `proveedor` LIKE '%" . $resultP["proveedor"] . "%'";
  $resultdA = $db->query($distinctA);

  while($z = $resultdA->fetch_assoc()) {
    $desc = explode("-", $z["descripcion"]);
    $_desc .= $desc[1] . ",";
  }
  
  $bla = rtrim($_desc, ",");
  
  // Obtenemos el parámetro de referencia proveedor
  $rpS = "SELECT id, orden_de_compra, referencia_proveedor FROM entradas WHERE orden_de_compra IN (" . $bla . ") GROUP BY orden_de_compra;";  
  $RrpS = $db->query($rpS);
  
  // Buscamos información de las entradas relacionadas
  $dataEntradas = "SELECT proveedor FROM ordenes_de_compra WHERE oc LIKE '%%';";
  
  switch($resultC["peridiocidad"]){
    case 1:
      $per = 7;
      break;
    case 2:
      $per = 15;
      break;
    case 3:
      $per = 30;
      break;
    case 4:
      $per = 45;
      break;
    default:
      $per = 7;
      break;
  }
  
  // Agregar PDF o XML a referencia proveedor
  if(isset($_POST['submit2'])) {
    $allowed =  array('pdf','xml');
    $cuentas = $_POST['cart_'];
    
    foreach($cuentas AS $cuenta) {
      // Guardamos la imagen
      $i = 0;

      while($i < count($_FILES['upDoc']['name'])) {        
        $ext = pathinfo($_FILES['upDoc']['name'][$i], PATHINFO_EXTENSION);
                
        if(!in_array($ext,$allowed) ) {
          $session->msg("d","El archivo a subir debe ser PDF o XML");
          redirect('tesoreria_internas.php');
        }
        
        move_uploaded_file($_FILES['upDoc']['tmp_name'][$i], "docs/referencias/" . $cuenta["orden_de_compra"] . "_" . $cuenta["referencia_proveedor"] . "." . $ext);
        
        $i++;
      }
    }

    $session->msg('s','Archivo cargado exitosamente');
    redirect('edit_cuenta_interna.php?id='.(int)$_GET['id'], false);
  }
  
  // Agregar archivo de comprobante de abono
  if(isset($_POST['submit'])) {
    $cuentas = $_POST['cart'];
    
    foreach($cuentas AS $cuenta) {
      if(array_key_exists('check', $cuenta) && $cuenta["abono"] > 0) {
        $proveedor = $cuenta["proveedor"];
        $fecha_pago = $cuenta["fecha_pago"];
        $abono = $cuenta["abono"];
        $oc = $cuenta["descripcion"];
        $_idTe = "SELECT id FROM tesoreria ORDER BY id DESC LIMIT 1";
        $_resultTe = $db->query($_idTe);
        $_resultTe = $db->fetch_assoc($_resultTe);
        $id = $_resultTe["id"] + 1;
        $insertAbono = "INSERT INTO tesoreria(id,tipo_cuenta,proveedor,nombre,descripcion,fecha_pago,abono,monto_pago,peridiocidad,tipo,comprobante) VALUES ('$id',0,'$proveedor','','{$oc}','$fecha_pago','$abono','0.00','',1,'');";
        $db->query($insertAbono);
        
        // Guardamos la imagen
        $i = 0;

        while($i < count($_FILES['upfile']['name'])) {
          move_uploaded_file($_FILES['upfile']['tmp_name'][$i], "docs/abonos/" . $_FILES['upfile']['name'][$i]);
            
          // Buscamos ID del registr  o del abono
          $_idAbono = "SELECT id FROM tesoreria ORDER BY id DESC LIMIT 1";
          $_resultAbono = $db->query($_idAbono);
          $_resultAbono = $db->fetch_assoc($_resultAbono);
          $photo = new Media();
          
          if($photo->processAbono($_FILES['upfile']['name'][$i],$_FILES['upfile']['tmp_name'][$i],$_resultAbono["id"])){
            $session->msg('s','Abono ingresado correctamente');
            redirect('edit_cuenta_interna.php?id='.(int)$_GET['id'], false);
          } else{
            $session->msg('d',join($photo->errors));
            redirect('edit_cuenta_interna.php?id='.(int)$_GET['id'], false);
          }
          
          $i++;
        }
      }
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
          <span><?php echo "Cuenta por pagar: " . remove_junk($resultP["proveedor"]); ?></span>
        </strong>
        <a href="tesoreria_internas.php" class="btn btn-info pull-right tablesorter"><< VOLVER</a>
      </div>
      <div class="panel-body">
        <div class="col-md-6">
          <div class="form-group">
            <label for="proveedor">Proveedor</label>
            <input type="text" readonly="readonly" class="form-control" name="proveedor" value="<?php echo $resultP["proveedor"]; ?>">
          </div>
          <div class="form-group">
            <label for="total">Total a Pagar</label>
            <input readonly="readonly" type="text" class="form-control" name="total" value="<?php echo $resultC["total"];?>">
          </div>
          <div class="form-group">
            <label for="abono">Abono</label>
            <input type="text" readonly="readonly" class="form-control" name="responsable" value="<?php echo $resultC['abono']; ?>">
          </div>
          <div class="form-group">
            <label for="saldo_restante">Restante</label>
            <input type="text" readonly="readonly" class="form-control" name="saldo_restante" value="<?php echo $resultC['saldo_restante']; ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
              <label for="peridiocidad">Días de Crédito</label>
              <input type="text" readonly="readonly" class="form-control" name="peridiocidad" value="<?php echo $per; ?>">
          </div>
          <div class="form-group">
              <label for="fecha_pago">Fecha de Vencimiento</label>
              <input type="text" readonly="readonly" class="form-control" name="fecha_pago" value="<?php echo $resultC['fecha_pago']; ?>">
          </div>
          <div class="form-group">
              <label for="descripcion">OC</label>
              <input type="text" readonly="readonly" class="form-control" name="descripcion" value="<?php echo $resultC['descripcion']; ?>">
          </div>
          <div class="form-group">
              <label for="telefono">Requisiciones</label>
              <input type="text" readonly="readonly" class="form-control" name="telefono" value="">
          </div>
        </div>
      </div>
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Referencias Proveedor</span>
        </strong>
      </div>
      <div class="panel-body">
        <form class="form-inline" action="edit_cuenta_interna.php?id=<?php echo (int)$_GET['id'];?>" method="POST" enctype="multipart/form-data">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th class="text-center">Orden de Compra</th>
                <th class="text-center">Referencia Proveedor</th>
                <th class="text-center">Documento</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row2 = $RrpS->fetch_assoc()) { ?>
                <tr>
                  <input type="hidden" class="form-control" name="cart_[<?php echo $row2["id"]?>][orden_de_compra]" value="<?php echo $row2["orden_de_compra"]?>">
                  <input type="hidden" class="form-control" name="cart_[<?php echo $row2["id"]?>][referencia_proveedor]" value="<?php echo $row2["referencia_proveedor"]?>">
                  <td class="text-center"><?php echo $row2["orden_de_compra"]?></td>
                  <td class="text-center"><?php echo $row2["referencia_proveedor"]?></td>
                  <td class="text-center">
                    <input type="file" name="upDoc[]" class="btn btn-primary btn-file"/>
                    <button type="submit" name="submit2" class="btn btn-default">Subir Documento<br>(PDF/XML)</button><br><br>
                    <?php
                      // Sort in ascending order - this is default
                      $file = $row2["orden_de_compra"] . "_" . $row2["referencia_proveedor"];
                      $dir = scandir("docs/referencias/");
                      foreach (glob("*docs/referencias/$file.*") as $filename) {
                    ?>
                        <a href="<?php echo $filename; ?>" download>
                          <?php echo str_replace("docs/referencias/","",$filename); ?>
                        </a>
                        <br>
                    <?php
                      }
                    ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </form>
      </div>
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Historial de Pagos</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center">OC</th>
              <th class="text-center">Cantidad de Pago</th>
              <th class="text-center">Fecha de Aplicación</th>
              <th class="text-center">Comprobante</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $resultA->fetch_assoc()) { ?>
              <tr>
                <td class="text-center"><?php echo $row["descripcion"]?></td>
                <td class="text-center"><?php echo "$ " . $row["abono"]?></td>
                <td class="text-center"><?php echo $row["fecha_pago"]?></td>
                <td class="text-center">
                  <a href="/docs/abonos/<?php echo $row["comprobante"]?>" download>
                    <span class="glyphicon glyphicon-download-alt">
                  </a> ||
                  <a href="/docs/abonos/<?php echo $row["comprobante"]?>" download>
                    <?php echo $row["comprobante"]?>
                  </a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Ingreso de Abonos</span>
        </strong>
      </div>
      <div class="panel-body">
        <form class="form-inline" action="edit_cuenta_interna.php?id=<?php echo (int)$_GET['id'];?>" method="POST" enctype="multipart/form-data">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th class="text-center"></th>
                <th class="text-center">OC</th>
                <th class="text-center">Total a Pagar</th>
                <th class="text-center">Abonado</th>
                <th class="text-center">Cantidad a Abonar</th>
                <th class="text-center">Comprobante</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = $resultOC->fetch_assoc()) { ?>
                <tr>
                  <input type="hidden" class="form-control" name="cart[<?php echo $row["id"]?>][proveedor]" value="<?php echo $resultP["proveedor"]; ?>">
                  <input type="hidden" class="form-control" name="cart[<?php echo $row["id"]?>][fecha_pago]" value="<?php echo date("Y-m-d") ?>">
                  <input type="hidden" class="form-control" name="cart[<?php echo $row["id"]?>][peridiocidad]" value="<?php echo $per; ?>">
                  <input type="hidden" class="form-control" name="cart[<?php echo $row["id"]?>][tipo]" value="<?php echo "1"; ?>">
                  <input type="hidden" class="form-control" name="cart[<?php echo $row["id"]?>][descripcion]" value="<?php echo $row["descripcion"]?>">
                  <td class="text-center"><input type="checkbox" name="cart[<?php echo $row["id"]?>][check]"></td>
                  <td class="text-center"><?php echo $row["descripcion"]?></td>
                  <td class="text-center"><?php echo "$ " . $row["monto_pago"]?></td>
                  <td class="text-center"><?php echo "$ " . $row["sum_abono"]?></td>
                  <td class="text-center"><input type="number" class="form-control" name="cart[<?php echo $row["id"]?>][abono]" min="0" step="0.01" max="<?php echo $row["monto_pago"]?>" value=""></td>
                  <td class="text-center">
                    <input type="file" name="upfile[]" class="btn btn-primary btn-file"/>
                    <button type="submit" name="submit" class="btn btn-default">Subir Comprobante</button>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>