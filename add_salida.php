<?php
  $page_title = 'Registro de salida de material/servicio por Requisición';
  require_once('includes/load.php');
  $e_user = find_by_id('users',(int)$_SESSION['user_id']);
  
  // Validamos que cuente con permisos para esta seccion
  page_require_level("inventario");

  if(isset($_POST['add_salida'])){
    $req_fields = array('asignado_a','remision','entregado_a','requisicion');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $successMsg = "";
      $errorMsg = "";
      $articulos = $_POST['cart'];
      $asignado_a = $_POST["asignado_a"];
      $responsable = $_POST["responsable"];
      $entregado_a = $_POST["entregado_a"];
      $requisicion = $_POST["requisicion"];
      
      // Calculamos remisión
      $_idRem = "SELECT remision FROM salidas ORDER BY id DESC LIMIT 1";
      $resRem = $db->query($_idRem);
      $resRem = $db->fetch_assoc($resRem);
      $_remision = $resRem["remision"] + 1;
      $remision = $_remision;
      
      foreach($articulos AS $prod) {
        $fecha = date("Y-m-d");
        if(array_key_exists('check', $prod) && $prod["cantidad_salida"] > 0) {
          // Validamos que exista el no. de serie
          if(array_key_exists("sustituto",$prod)) {
            $getData = "SELECT sec, descripcion, um, no_serie FROM inventario WHERE no_serie='" . $prod["no_serie"] . "';";
            $result = $db->query($getData);
            
            if($db->num_rows($result) >= 1){
              if($db->fetch_assoc($result)) {
                foreach($result AS $rec) {
                  $sec = $rec["sec"];
                  $descripcion = $rec['descripcion'];
                  $um = $rec["um"];
                  $sku_serie = $prod["no_serie"];
                  $cantidad_salida = $prod["cantidad_salida"];
                }
              }
            } else {
              $sec = "";
              $errorMsg = "No existe el No. de Serie: " . $prod["no_serie"];
              $session->msg('d', $errorMsg);
            }
          } else {
            $descripcion = $prod['descripcion'];
            $cantidad_salida = $prod["cantidad_salida"];
            $sec = $prod["sec"];
            //$sku_serie = $prod["no_serie"];
            $sku_serie = "";
            $um = $prod["um"];
          }
          
          if($sec != "") {
            $query = "INSERT INTO salidas(`sec`,`asignado_a`,`responsable`,`remision`, `descripcion`, `sku_serie`,`cantidad`,`entregado_a`,`um`,`requisicion`, `fecha`) VALUES (";
            $query .= "'{$sec}','{$asignado_a}','{$responsable}','{$remision}','{$descripcion}','{$sku_serie}','{$cantidad_salida}','{$entregado_a}','{$um}','{$requisicion}','{$fecha}'";
            $query .= ");";
            
            if($db->query($query)){
              $successMsg = "Salida " . $remision . " creada exitosamente.\n<br>";
            } else {
              $errorMsg = "Lo sentimos, ocurrió un error generando la salida (Seleccione un artículo)";
            }
          }
        }
      }
      
      if($successMsg){
        // Creamos PDF con formato de Entrada
        $pdf = crearSalida($remision, $e_user);
        // Al insertar correctamente los datos, actualizamos el inventario
        foreach($articulos AS $prod) {
          if(array_key_exists('check', $prod) && $prod["cantidad_salida"] > 0 ) {
            echo "<pre>";
            print_r($prod);
            echo "</pre>";
            // Validamos que exista el no. de serie
            if(array_key_exists("sustituto",$prod)) {
              $getData = "SELECT sec, descripcion FROM inventario WHERE no_serie='" . $prod["no_serie"] . "';";
              $result = $db->query($getData);
              
              if($db->num_rows($result) >= 1){
                if($db->fetch_assoc($result)) {
                  foreach($result AS $rec) {
                    $sec = $rec["sec"];
                  }
                }
              } else {
                $sec = "";
                $errorMsg = "No existe el No. de Serie: " . $prod["no_serie"];
                $session->msg('d', $errorMsg);
              }
            } else {
              $cantidad_salida = $prod["cantidad_salida"];
              $sec = $prod["sec"];
            }
            
            if($sec != "") {
              $sql = "UPDATE inventario SET existencia = existencia-{$cantidad_salida} WHERE sec='{$sec}';";
              if($prod["no_serie"]) {
                $updAR = "UPDATE articulos_requisiciones SET cantidad_entregada=cantidad_entregada+{$cantidad_salida}, surtir=surtir-{$cantidad_salida} WHERE requisicion='{$requisicion}' AND descripcion LIKE '%" . $prod["sustituto"] . "%';";
              } else {
                $updAR = "UPDATE articulos_requisiciones SET cantidad_entregada=cantidad_entregada+{$cantidad_salida}, surtir=surtir-{$cantidad_salida} WHERE requisicion='{$requisicion}' AND sec='{$sec}';";
              }
              
              $db->query($updAR);
              
              // Al actualizar la cantidad entregada que se dio para la Requisición, verificamos si es necesario actualizar el status
              $selRe = "SELECT ar.sec AS sec, ar.requisicion AS requisicion, ar.cantidad AS cantidad, ar.cantidad_entregada AS cantidad_entregada
                FROM requisiciones AS r
                LEFT JOIN articulos_requisiciones AS ar
                ON r.no_requisicion = ar.requisicion
                WHERE r.no_requisicion='{$requisicion}' AND r.status NOT IN (1,3);";
  
              $rs_result = $db->query($selRe);
              $cQuery = $db->num_rows($rs_result);
              $countArt = 0;
              
              // Obtenemos todos los artículos de la OC
              while($row = $rs_result->fetch_assoc()) {
                // Si la cantidad de entrada es mayor o igual a la cantidad solicidada, se suma al contador de articulos total entregado.
                if($row["cantidad_entregada"] >= $row["cantidad"]) {
                  $countArt = $countArt + 1;
                }
              }
              
              // Si el contador de artículos es igual al contador de artículos totales, actualizamos status de la OC
              if($countArt == $cQuery) {
                $updSRe = "UPDATE requisiciones SET status=3 WHERE no_requisicion='{$requisicion}';";
                $db->query($updSRe);
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
        redirect('add_salida.php', false);
      } else {
        $errorMsg = "Lo sentimos, ocurrió un error generando la entrada (Seleccione un artículo)";
        $session->msg('d', $errorMsg);
        redirect('add_salida.php', false);
      }
    } else{
      $session->msg("d", $errors);
      redirect('add_salida.php',false);
    }
  }
  
  $salida = "SELECT id FROM salidas ORDER BY id DESC LIMIT 1;";
  $result = $db->query($salida);
  $result = $db->fetch_assoc($result);
  $ultimaSalida = "VAS-" . ($result['id'] + 1);
  $sql = "SELECT sec, descripcion FROM inventario WHERE existencia > 0 ORDER BY sec LIMIT 5";
  $rs_result = $db->query($sql);
  
  // Buscamos las Requisiciones que no se encuentren cerradas o rechazadas
  $reqs = "SELECT no_requisicion FROM requisiciones WHERE status IN (0,4,5,6);";
  $resultReqs = $db->query($reqs);
  
  include_once('layouts/header.php');
?>
<div class="row">
  <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Registro de salida de material/servicio por Requisición
        </strong>
      </div>
    </div>
  </div>
  <form method="post" action="add_salida.php" class="clearfix">
    <div class="col-md-12">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="entrada" class="control-label">Requisición</label>
            <!--<input type="text" class="form-control" name="requisicion" id="requisicion">-->
            <select class="form-control" name="requisicion" id="requisicion">
              <option value="">-- Seleccionar la Requisición --</option>
              <?php while($row = $resultReqs->fetch_assoc()) { ?>
                <option value="<?php echo $row["no_requisicion"]; ?>">Req - <?php echo $row["no_requisicion"]; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="asignado_a" class="control-label">Asignado a</label>
            <input type="text" class="form-control" name="asignado_a" id="asignado_a">
          </div>
          <div class="form-group">
            <label for="responsable" class="control-label">Entregado a</label>
            <input type="text" class="form-control" name="responsable" id="responsable">
          </div>
          <div class="form-group">
            <label for="remision" class="control-label">Remisión</label>
            <input type="text" class="form-control" name="remision" id="remision" value="<?php echo remove_junk($ultimaSalida); ?>" readonly="readonly">
          </div>
          <div class="form-group">
            <label for="entregado_a" class="control-label">Transporte</label>
            <input type="text" class="form-control" name="entregado_a" id="entregado_a">
          </div>
          <div class="form-group">
            <label for="clase_de_articulo" class="control-label">Artículos</label>
            <div id="tblArticulos"></div>
          </div>
          <div class="form-group clearfix">
            <input type="hidden" id="LiqSelection" name="LiqSelection"/>
            <button type="submit" id="add_salida" name="add_salida" class="btn btn-info">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script>
// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#add_salida').click(function() {
        return window.confirm("¿Desea terminar la Salida?");
    });
});

function Sustituir(sec, id, name) {
  //var url = "http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/getDropdown.php";
  var url = "http://127.0.0.1/inventario/getDropdown.php";
  var dropdown = "";
  jQuery.ajax({
    url: url,
    dataType: 'json',
    type: 'POST',
    data: {sec: sec, descripcion: name, id: id},
    success: function (data){
      console.log(data);
    if(data.status != "ERROR"){
      if(data.status == "SUCCESS"){
        dropdown = data.body;
        jQuery("#NoSerie-" + sec).replaceWith('<td>' + dropdown + '</td>');
      } else if(data.status == "EMPTY"){
        dropdown = data.message;
        jQuery("#NoSerie-" + sec).replaceWith('<td><input type="text" name="cart[' + id + '][no_serie]" value=""></td>');
      } else if(data.status == "NORESULT") {
        dropdown = data.message;
        jQuery("#NoSerie-" + sec).replaceWith('<td><input type="text" name="cart[' + id + '][no_serie]" value=""></td>');
      }
    } else if(data.status == "ERROR"){
      alert(data.message);
    }
  },
  });
  //jQuery("#NoSerie-" + sec).replaceWith('<td>' + dropdown + '</td>');
  jQuery("#MaterialServicio-" + sec).replaceWith('<td>' + name + '<input type="hidden" name="cart[' + id + '][sustituto]" value="' + name + '"></td>');
  jQuery("#Sustituir-" + sec).replaceWith('<td></td>');
}

jQuery('#requisicion').change(function() {
  try{
    //var url = "http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/getArticulosRequi.php";
    var url = "http://127.0.0.1/inventario/getArticulosRequi.php";
    jQuery.ajax({
      url: url,
      dataType: 'json',
      type: 'POST',
      data: {requisicion: jQuery('#requisicion').val()},
      success: function (data){
        if(data.status != "ERROR"){
          if(data.status == "SUCCESS"){
            jQuery('#tblArticulos').empty();
            jQuery("#tblArticulos").append(data.body);
            jQuery("#asignado_a").replaceWith(data.asignado);
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
      },
    });
  }catch (e){
    console.log(e);
  }
});

function AddToHidden(name, id, value){
  var curValue = $('#LiqSelection').val();
  var newArray = [];
  var newObject = {};
  if(curValue!=''){
    var existingId = false;
    
    // If ID already added in to hidden input, parse the array
    newArray = JSON.parse(curValue);
    for (index = 0; index < newArray.length; ++index){
      var curObject = newArray[index];
      if(curObject.Id === id){
        existingId = true;
        curObject.Value = value;
      }
    }
    
    // If ID isn't added in hidden input, create New Object 
    if(!existingId)
    {
      newObject.Name = name;
      newObject.Id = id;
      newObject.Qty = value;
      newArray.push(newObject);
    }
  } else {
    // Create New Object
    newObject.Name = name;
    newObject.Id = id;
    newObject.Qty = value;
    newArray.push(newObject);
  }
  $('#LiqSelection').val(JSON.stringify(newArray));		
}
</script>
<?php include_once('layouts/footer.php'); ?>