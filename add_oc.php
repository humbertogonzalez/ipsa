<?php
  require_once('includes/load.php');
  page_require_level("ordenes_compra");
  $page_title = 'Alta de Orden de Compra';
  $empresas = find_all('user_empresas');
  $proyectos = find_all('proyectos');
  $proveedores = find_all_proveedores();
  $e_user = find_by_id('users',(int)$_SESSION['user_id']);
  
  if(!$e_user){
    $session->msg("d","No existe el ID de Usuario.");
    redirect('add_requisicion.php');
  }
  
  if(isset($_POST['add_articulos_requi'])) {
    $req_fields = array('requisicion','cart','proveedor');
    
    if(empty($errors)){
      $articulos = $_POST['cart'];
      $arr = array();
      
      foreach($articulos AS $index => $data) {
        if(array_key_exists('check', $data) && $data["precio"] == "") {
          $session->msg('d','El campo <b>Precio</b> debe de ser mayor a 0.');
          redirect('selectArtsOc.php', false);
        }
        if(array_key_exists('check', $data) && $data["cantidad"] != "" && $data["precio"] != "") {
          $articulos[$index]["descripcion"] = remove_junk($data["descripcion"]);
          $arr[] = $data["requisicion"];
          $arr_[] = $data["proyecto"];
        } else {
          unset($articulos[$index]);
        }
      }
      
      //$_articulos = json_encode($articulos, true);
      $_articulos = htmlspecialchars(json_encode($articulos), ENT_QUOTES, 'UTF-8');
      $requisicion = "";
      $proyecto_ = "";
      $unique_data = array_unique($arr);
      $unique_data_ = array_unique($arr_);
      
      foreach($unique_data as $val) {
        $requisicion.= $val . ", ";
      }
      
      foreach($unique_data_ as $val_) {
        $proyecto_.= $val_ . ", ";
      }
      
      $requisicion = rtrim($requisicion, ", ");
      $proyecto_ = rtrim($proyecto_, ", ");
    }
  }
  
  if(isset($_POST['add_oc'])){
    $req_fields = array('solicitante','requisicion','proyecto','LiqSelection');
    validate_fields($req_fields);

    if(empty($errors)){
      // Calculamos totales de la OC
      $fecha = remove_junk($db->escape($_POST['fecha']));
      $proveedor = find_by_id('proveedores',(int)$_POST['proveedor']);
      $proveedor = remove_junk($proveedor["razon_social"]);
      $_empresa = find_by_id('user_empresas',(int)$e_user["working_on"]);
      $empresa = remove_junk($_empresa["empresa"]);
      $rfc = remove_junk($db->escape($_POST['rfc']));
      $contacto = remove_junk($db->escape($_POST['contacto']));
      $telefono_proveedor = remove_junk($db->escape($_POST['telefono']));
      $requisicion = remove_junk($db->escape($_POST['requisicion']));
      $jsonArts = json_decode($_POST['LiqSelection'], true);
      $proyecto = (int)$db->escape($_POST['proyecto']);
      $entregar_en = remove_junk($db->escape($_POST['entregar']));

      switch ($entregar_en) {
        case 1:
          $queryEntregar = find_by_id('proyectos', (int)$proyecto);
          $entregar_en = $queryEntregar["direccion"];
          break;
        case 2:
          $entregar_en = "PONIENTE 122 NO. 437 - D, COL. INDUSTRIAL VALLEJO DELEGACION AZCAPOTZALCO, C.P. 02300,  MEXICO DISTRITO FEDERAL";
          break;
        case 3:
          $entregar_en = remove_junk($db->escape($_POST['entregar2']));
          break;
        default:
          $queryEntregar = find_by_id('proyectos', (int)$proyecto);
          $entregar_en = remove_junk($queryEntregar["direccion"]);
          break;
      }
      
      $recibe = remove_junk($db->escape($_POST['recibe']));
      $tel = remove_junk($db->escape($_POST['tel']));
      $condiciones_de_pago = remove_junk($db->escape($_POST['condiciones_de_pago']));
      $tiempo_pago = remove_junk($db->escape($_POST['tiempo_pago']));
      $tiempo_de_entrega = remove_junk($db->escape($_POST['tiempo_de_entrega']));
      $comentario = remove_junk($db->escape($_POST['comentario']));
      $cotizacion_proveedor = remove_junk($db->escape($_POST['cotizacion_proveedor']));
      $tipo_moneda =remove_junk($db->escape($_POST['tipo_moneda']));
      
      // Calculamos totales para la tabla general de OC
      $totalRow = 0.00;
      $subtotal_1 = 0.00;
      $descuento = 0.00;
      $subtotal_2 = 0.00;
      $iva = 0.00;
      $total = 0.00;
      
      foreach($jsonArts AS $index => $data) {
        $totalRow = $data["precio"] * $data["cantidad"];
        $subtotal_1 += $totalRow;
      }
      
      $subtotal_2 = $subtotal_1 - $descuento;
      $iva = $subtotal_2 * 0.16;
      $total = $subtotal_2 + $iva;
      $_idOc = "SELECT id FROM ordenes_de_compra ORDER BY oc DESC LIMIT 1";
      $_result = $db->query($_idOc);
      $_result = $db->fetch_assoc($_result);
      $oc = $_result["id"] + 1;

      // Explode Requisiciones
      $explodeR = explode(", ", $requisicion);
      $getSol = "SELECT solicitante FROM requisiciones WHERE id IN (" . $explodeR[0] . ");";
      $getSol = $db->query($getSol);
      $getSol = $db->fetch_assoc($getSol);
      $solicitante = $getSol["solicitante"];
      
      // Insertamos información de la OC
      $query = "INSERT INTO ordenes_de_compra (fecha,oc,proveedor,empresa,rfc,contacto,telefono_proveedor,solicitante,requisicion,proyecto,entregar_en,recibe,telefono,subtotal_1,descuento,subtotal_2,iva,total,condiciones_de_pago, tiempo_pago, tiempo_de_entrega,comentario,cotizacion_proveedor,tipo_moneda,status)";
      $query .=" VALUES ('{$fecha}','{$oc}','{$proveedor}','{$empresa}','{$rfc}','{$contacto}','{$telefono_proveedor}','{$solicitante}','{$requisicion}','{$proyecto}','{$entregar_en}','{$recibe}','{$tel}','{$subtotal_1}','{$descuento}','{$subtotal_2}','{$iva}','{$total}','{$condiciones_de_pago}','{$tiempo_pago}','{$tiempo_de_entrega}','{$comentario}','{$cotizacion_proveedor}','{$tipo_moneda}','2')";

      if($db->query($query)){
        $idOc = "SELECT id FROM ordenes_de_compra ORDER BY id DESC LIMIT 1";
        $result = $db->query($idOc);
        $result = $db->fetch_assoc($result);
        $cont = 1;
        $statusArtReq = 0;
        
        foreach($jsonArts AS $index => $data) {
          switch ($data["tipo_precio"]) {
            case "precio_unitario":
              $totalRow = $data["precio"] * $data["cantidad"];
              break;
            case "global":
              $totalRow = $data["precio"];
              break;
            default:
              $totalRow = $data["precio"] * $data["cantidad"];
              break;
          }
          
          $queryInsert = "INSERT INTO articulos_ordenes_de_compra (sec,oc,partida,cantidad,unidades,codigo,descripcion,tipo_precio,precio_unitario,importe, comentario)";
          $queryInsert.= "VALUES ('" . $data["sec"] . "','" . $result["id"] . "', '" . $cont . "' , '" . $data["cantidad"] . "','". $data["unidades"] . "','','" . $data["descripcion"] . "','" . $data["tipo_precio"] . "'," . $data["precio"] . ",'" . $totalRow . "','" . $data["comentarios"] . "')";
          
          $cont = $cont + 1;
          if($db->query($queryInsert)){
            // Llamamos función que crea PDF de requisición
            $session->msg('s',"La Orden de Compra " . $oc . " ha sido creada exitosamente.");
            $pdf = createOC($result['id'], $e_user["working_on"], $tipo_moneda);
            
            // Actualizamos status de productos de requisicion
            $_queryInsert = "UPDATE articulos_requisiciones SET oc=" . $result["id"] . ",mostrar=0 WHERE sec=" . $data["sec"] . " AND requisicion=" . $data["requisicion"] . ";";

            $db->query($_queryInsert);
          } else {
            //failed
            $session->msg('d',' Lo sentimos, ocurrió un error creado la Orden de Compra');
            redirect('oc.php', false);
          }
        }
        
        // Enviamos información a Cuentas por Cobrar
        switch($tiempo_pago){
          case 1:
            $days = 0;
            break;
          case 2:
            $days = 7;
            break;
          case 3:
            $days = 15;
            break;
          case 4:
            $days = 30;
            break;
          case 5:
            $days = 45;
            break;
          default:
            $days = 7;
            break;
        }
        
        $fecha_pago = strtotime("+" . $days . " days", strtotime($fecha));
        $fecha_pago = date("Y-m-d H:i:s", $fecha_pago);
        $_idTe = "SELECT id FROM tesoreria ORDER BY id DESC LIMIT 1";
        $_resultTe = $db->query($_idTe);
        $_resultTe = $db->fetch_assoc($_resultTe);
        $id = $_resultTe["id"] + 1;
        $insertCxP = "INSERT INTO tesoreria(id,tipo_cuenta,proveedor,nombre,descripcion,fecha_pago,abono,monto_pago,peridiocidad,tipo,comprobante) VALUES ('$id',0,'$proveedor','$proyecto','OC-" . $result["id"] . "','$fecha_pago',0.00,'$total','$tiempo_pago',0,'');";
        $db->query($insertCxP);
        redirect('oc.php', false);
      } else {
        //failed
        $session->msg('d',' Lo sentimos, ocurrió un error creado la Orden de Compra');
        redirect('oc.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('oc.php',false);
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
          <span>Alta de Órden de Compra</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_oc.php" autocomplete="off">
          <div class="col-md-12">
            <div class="form-group">
              <label for="fecha">Fecha</label>
              <input type="text" readonly="readonly" class="form-control" name="fecha" value="<?php echo date("Y-m-d"); ?>">
            </div>
            <div class="form-group">
              <label for="proveedor">Proveedor</label>
              <select class="form-control" name="proveedor" id="proveedor" required="required">
                <option value="">-- Seleccionar Proveedor --</option>
                <?php foreach ($proveedores as $proveedor) { ?>
                  <option value="<?php echo $proveedor["id"]; ?>" /><?php echo $proveedor["razon_social"]; ?><br />
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="rfc">RFC</label>
              <input type="text" class="form-control" name="rfc" id="rfc" value="" readonly="readonly">
            </div>
            <div class="form-group">
              <label for="contacto">Contacto</label>
              <input class="form-control" type="text" name="contacto" id="contacto" value="" readonly="readonly">
            </div>
            <div class="form-group">
              <label for="telefono">Tel&eacute;fono</label>
              <input class="form-control" type="text" name="telefono" id="telefono" value="" readonly="readonly">
            </div>
            <div class="form-group">
              <label for="solicitante">Solicitante</label>
              <input class="form-control" type="text" name="solicitante" id="solicitante" value="<?php echo $e_user["username"]; ?>" readonly="readonly">
            </div>
            <div class="form-group">
              <label for="requisicion">Requisicion</label>
              <input class="form-control" type="text" name="requisicion" id="requisicion" value="<?php echo $requisicion; ?>" readonly>
            </div>
            <div class="form-group">
              <label for="proyecto">Proyecto</label>
              <select class="form-control" name="proyecto" id="proyecto" readonly>
                <?php
                  foreach ($proyectos as $proyecto) {
                    if($proyecto["nombre"] == $proyecto_){
                ?>
                      <option value="<?php echo $proyecto["id"]; ?>" selected><?php echo $proyecto["nombre"]; ?><br />
                <?php } } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="entregar">Entregar en</label>
              <select class="form-control" name="entregar" id="entregar">
                <option value="">-- Seleccionar Opci&oacute;n --</option>
                <option value="1">Proyecto</option>
                <option value="2">Vallejo</option>
                <option value="3">Otro</option>
              </select>
              <div id="otherType" style="display:none;">
                <br>
                <input type="text" class="form-control" name="entregar2" id="entregar2" placeholder="Dirección"/>
              </div>
            </div>
            <div class="form-group">
              <label for="recibe">Recibe</label>
              <input class="form-control" type="text" name="recibe" id="recibe">
            </div>
            <div class="form-group">
              <label for="tel">Tel&eacute;fono</label>
              <input class="form-control" type="text" name="tel" id="tel" value="">
            </div>
            <div class="form-group">
              <label for="tiempo_pago">Tiempo de Pago</label>
              <select class="form-control" name="tiempo_pago">
                <option value="">-- Seleccionar el Rango --</option>
                <option value="1">0 d&iacute;as</option>
                <option value="2">7 d&iacute;as</option>
                <option value="3">15 d&iacute;as</option>
                <option value="4">30 d&iacute;as</option>
                <option value="5">45 d&iacute;as</option>
              </select>
            </div>
            <div class="form-group">
              <label for="condiciones_de_pago">Condiciones de Pago</label>
              <textarea class="form-control" type="text" name="condiciones_de_pago" id="condiciones_de_pago" value=""></textarea>
            </div>
            <div class="form-group">
              <label for="tiempo_de_entrega">Tiempo de Entrega</label>
              <input class="form-control" type="text" name="tiempo_de_entrega" id="tiempo_de_entrega" value="">
            </div>
            <div class="form-group">
              <label for="comentario">Comentario</label>
              <textarea class="form-control" type="text" name="comentario" id="comentario" value=""></textarea>
            </div>
            <div class="form-group">
              <label for="cotizacion_proveedor">Cotizaci&oacute;n Proveedor</label>
              <input class="form-control" type="text" name="cotizacion_proveedor" id="cotizacion_proveedor" value="">
            </div>
            <div class="form-group">
              <label for="tipo_moneda">Tipo de Moneda</label>
              <select class="form-control" name="tipo_moneda">
                <option value="">-- Seleccionar Moneda --</option>
                <option value="MXN">Pesos Mexicanos (MXN)</option>
                <option value="USD">D&oacute;lares (USD)</option>
              </select>
            </div>
            <div class="form-group clearfix">
              <input type="hidden" id="LiqSelection" name="LiqSelection" value='<?php echo $_articulos; ?>'/>
              <button type="submit" id="add_oc" name="add_oc" class="btn btn-primary">Crear Orden de Compra</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#add_oc').click(function() {
        return window.confirm("¿Desea terminar la Órden de Compra?");
    });
});

jQuery('#proveedor').change(function() {
  try{
    //var url = "http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/getDatosProveedor.php";
    var url = "http://localhost:8888/inventario/getDatosProveedor.php";
    jQuery.ajax({
      url: url,
      dataType: 'json',
      type: 'POST',
      data: {proveedor: jQuery('#proveedor').val()},
      success: function (data){
        if(data.status != "ERROR"){
          if(data.status == "SUCCESS"){
            jQuery('#rfc').val(data.rfc);
            jQuery('#contacto').val(data.contacto);
            jQuery('#telefono').val(data.telefono);
          } else if(data.status == "EMPTY"){
            jQuery('#rfc').empty();
          } else if(data.status == "NORESULT") {
            jQuery('#rfc').empty();
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

jQuery('#entregar').change(function() {
  var selected = jQuery(this).val();
  if(selected === '3') {
    jQuery('#otherType').css("display", "block");
    jQuery('#recibe').val("");
    jQuery('#tel').val("");
  } else {
    if(selected === '2') {
      jQuery('#recibe').val("SERGIO GUTIERREZ");
      jQuery('#tel').val("53683986");
    } else {
      jQuery('#recibe').val("");
      jQuery('#tel').val("");
    }
    jQuery('#otherType').css("display", "none");
  }
});
</script>
<?php include_once('layouts/footer.php'); ?>