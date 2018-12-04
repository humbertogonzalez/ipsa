<?php
  $page_title = 'Registro de entrada de material/servicio';
  require_once('includes/load.php');
  $e_user = find_by_id('users',(int)$_SESSION['user_id']);
  
  if(isset($_POST['add_entrada'])){
    $req_fields = array('orden_de_compra');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $successMsg = "";
      $errorMsg = "";
      $articulos = $_POST['cart'];
      $oc = $_POST["orden_de_compra"];
      $referencia_proveedor = $_POST["referencia_proveedor"];
      
      // Calculamos remisión
      $_idRem = "SELECT remision FROM entradas ORDER BY id DESC LIMIT 1";
      $resRem = $db->query($_idRem);
      $resRem = $db->fetch_assoc($resRem);
      $_remision = $resRem["remision"] + 1;
      $remision = $_remision;
      
      // Por cada artículo se obtiene la información para hacer un insert.
      foreach($articulos AS $prod) {
        if(array_key_exists('check', $prod) && $prod["cantidad"] > 0) {
          $material_servicio = $prod['material_servicio'];
          $sku_serie = $prod["codigo"];
          $tipo_articulo = $prod["tipo_de_articulo"];
          $fecha_de_ingreso = date("Y-m-d");
          $cantidad = $prod["cantidad"];
          $um = $prod["um"];
          $caja = $prod["caja"];
          $ubicacion = $prod["ubicacion"];
          $sec = $prod["sec"];
          
          // Creamos el artículo con no. de serie, en caso de haber ingresado uno nuevo
          if($prod["no_serie"] != "") {
            $find = "SELECT sec FROM inventario ORDER BY sec DESC LIMIT 1;";
            $result = $db->query($find);
            $result = $db->fetch_assoc($result);
            $sec = $result['sec'] + 1;
            $marca = $prod["marca"];
            $modelo_base = $prod["codigo"];
            $sku = "";
            $descripcion = $prod["material_servicio"];
            $no_serie = $prod["no_serie"];
            $prod["sec"] = $sec;
            $newProd  = "INSERT INTO `inventario`(`sec`, `tipo_de_articulo`, `marca`, `modelo_base`, `sku`, `descripcion`, `no_serie`, `um`, `existencia`) VALUES (";
            $newProd .= "'{$sec}','{$tipo_articulo}','{$marca}','{$modelo_base}','{$sku}','{$descripcion}','{$no_serie}','{$um}',{$cantidad}";
            $newProd .= ")";
            $db->query($newProd);
          }
          
          if($cantidad > 0) {
            $query = "INSERT INTO entradas(`sec`,`material_servicio`, `sku_serie`, `tipo_articulo`, `no_serie`, `orden_de_compra`, `remision`, `fecha_de_ingreso`, `cantidad`, `um`, `caja`, `ubicacion`,`referencia_proveedor`) VALUES (";
            $query .= "'{$sec}','{$material_servicio}','{$sku_serie}','{$tipo_articulo}','{$no_serie}','{$oc}','{$remision}','{$fecha_de_ingreso}','{$cantidad}','{$um}','{$caja}','{$ubicacion}','{$referencia_proveedor}'";
            $query .= ");";
            
            if($db->query($query)){
              $successMsg = "Entrada " . $remision . " creada exitosamente.\n<br>";
            } else {
              $errorMsg = "Lo sentimos, ocurrió un error generando la entrada";
            }
          }
        }
      }
      
      if($successMsg){
        // Creamos PDF con formato de Entrada
        $pdf = createEntrada($remision, $e_user);
        
        // Al insertar correctamente los datos, actualizamos el inventario y la OC
        foreach($articulos AS $prod) {
          if(array_key_exists('check', $prod) && $prod["cantidad"] > 0) {
            $material_servicio = $prod['material_servicio'];
            $cantidad = $prod["cantidad"];
            $sec = $prod["sec"];
            
            /*if($prod["no_serie"] != "") {
              $find = "SELECT sec FROM inventario ORDER BY sec DESC LIMIT 1;";
              $result = $db->query($find);
              $result = $db->fetch_assoc($result);
              $sec = $result['sec'];
              $prod["sec"] = $sec;
            }*/
            
            if($cantidad > 0) {
              $stat = "SELECT sec, faltan, surtir FROM articulos_requisiciones WHERE sec=" . $sec . " AND oc=" . $oc . ";";
              $stat = $db->query($stat);
              $stat = $db->fetch_assoc($stat);
              
              if($prod["cantidad"] < $stat["faltan"]) {
                $statusArtReq = 1;
              } else {
                $statusArtReq = 2;
              }
              
              if($prod["no_serie"] == "") {
                $sql = "UPDATE inventario SET existencia = existencia+{$cantidad} WHERE sec='{$sec}';";
              }
              
              $updAOC = "UPDATE articulos_ordenes_de_compra SET cantidad_entrada=cantidad_entrada+{$cantidad} WHERE oc='{$oc}' AND sec='{$sec}';";
              $db->query($updAOC);
              //$updAR = "UPDATE articulos_requisiciones SET surtir=surtir+{$cantidad} WHERE oc='{$oc}' AND sec='{$sec}';";
              $_queryInsert = "UPDATE articulos_requisiciones SET status=" . $statusArtReq . ", surtir=surtir+" . $cantidad . ", faltan=faltan-" . $cantidad . " WHERE oc='{$oc}' AND sec='{$sec}';";
              $db->query($_queryInsert);
              
              // Al actualizar la cantidad de entrada que se dio para la OC, verificamos si es necesario actualizar el status
              $selOC = "SELECT aodc.sec AS sec, aodc.oc AS oc, aodc.cantidad AS cantidad, aodc.cantidad_entrada AS cantidad_entrada
                FROM ordenes_de_compra AS odc
                LEFT JOIN articulos_ordenes_de_compra AS aodc
                ON odc.oc = aodc.oc
                WHERE odc.oc='{$oc}' AND odc.status NOT IN (1,3,7);";
  
              $rs_result = $db->query($selOC);
              $cQuery = $db->num_rows($rs_result);
              $countArt = 0;
              
              // Obtenemos todos los artículos de la OC
              while($row = $rs_result->fetch_assoc()) {
                // Si la cantidad de entrada es mayor o igual a la cantidad solicidada, se suma al contador de articulos total entregado.
                if($row["cantidad_entrada"] >= $row["cantidad"]) {
                  $countArt = $countArt + 1;
                }
              }
              
              // Si el contador de artículos es igual al contador de artículos totales, actualizamos status de la OC
              if($countArt == $cQuery) {
                $updAOC = "UPDATE ordenes_de_compra SET status=3 WHERE oc='{$oc}';";
                $db->query($updAOC);
              }
              
              // Validamos ejecución de actualización de inventario.
              if($db->query($sql)) {
                //$successMsg .= "Inventario actualizado exitosamente.";
              } else {
                $errorMsg = "Lo sentimos, ocurrió un error generando la entrada";
              }
            }
          }
        }

        $session->msg('s', $successMsg);
        redirect('add_entrada.php', false);
      } else {
        $errorMsg = "Lo sentimos, ocurrió un error generando la entrada";
        $session->msg('d', $errorMsg);
        redirect('add_entrada.php', false);
      }
    } else{
      $session->msg("d", $errors);
      redirect('add_entrada.php',false);
    }
  }
  
  $ocs = "SELECT oc FROM ordenes_de_compra WHERE status = 0;";
  $rs_result = $db->query($ocs);
  include_once('layouts/header.php');
?>
<div class="row">
  <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Registro de entrada de material/servicio
        </strong>
      </div>
    </div>
  </div>
  <form method="post" action="add_entrada.php" class="clearfix">
    <div class="col-md-12">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="orden_de_compra" class="control-label">Orden de Compra</label>
            <!--<input type="text" class="form-control" name="orden_de_compra" id="orden_de_compra">-->
            <select class="form-control" name="orden_de_compra" id="orden_de_compra">
              <option value="">-- Seleccionar la Orden de Compra --</option>
              <?php while($row = $rs_result->fetch_assoc()) { ?>
                <option value="<?php echo $row["oc"]; ?>">OC - <?php echo $row["oc"]; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="referencia_proveedor" class="control-label">Referencia Proveedor</label>
            <input type="text" class="form-control" name="referencia_proveedor" id="referencia_proveedor">
          </div>
          <div class="form-group">
            <label for="clase_de_articulo" class="control-label">Artículos</label>
            <div id="tblArticulos"></div>
          </div>
          <div class="form-group clearfix">
            <input type="hidden" id="LiqSelection" name="LiqSelection"/>
            <button type="submit" id="add_entrada" name="add_entrada" class="btn btn-info">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script>
// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#add_entrada').click(function() {
        return window.confirm("¿Desea terminar la Entrada?");
    });
});

jQuery('#orden_de_compra').change(function() {
  try{
    //var url = "http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/getArticulosOrden.php";
    var url = "http://127.0.0.1/inventario/getArticulosOrden.php";
    jQuery.ajax({
      url: url,
      dataType: 'json',
      type: 'POST',
      data: {orden_compra: jQuery('#orden_de_compra').val()},
      success: function (data){
        if(data.status != "ERROR"){
          if(data.status == "SUCCESS"){
            jQuery('#tblArticulos').empty();
            jQuery("#tblArticulos").append(data.body);
          } else if(data.status == "EMPTY"){
            jQuery('#tblArticulos').empty();
            jQuery("#tblArticulos").append(data.message);
          } else if(data.status == "NORESULT") {
            jQuery('#tblArticulos').empty();
            jQuery("#tblArticulos").append(data.message);
          }
        } else if(data.status == "ERROR"){
          alert(data.message);
        }
      },error: function (){
        jQuery('#tblArticulos').empty();
        jQuery("#tblArticulos").append("No se encontró la orden de compra");
      }
    });
  }catch (e){
    console.log(e);
  }
});
</script>
<?php include_once('layouts/footer.php'); ?>