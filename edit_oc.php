<?php
$page_title = 'Editar Orden de Compra';
require_once('includes/load.php');

// Checkin What level user has permission to view this page
page_require_level("ordenes_compra");
$empresas = find_all('user_empresas');
$proyectos = find_all('proyectos');
$proveedores = find_all_proveedores();
$e_user = find_by_id('users', (int)$_SESSION['user_id']);
$e_oc = find_by_id('ordenes_de_compra', (int)$_GET['id']);

if(!$e_oc){
  $session->msg("d","No existe el ID de Orden.");
  redirect('oc.php');
}

if(isset($_POST['edit_oc'])){
  $req_fields = array('solicitante','proyecto','proveedor');
  validate_fields($req_fields);

  if(empty($errors)){
        $fecha = remove_junk($db->escape($_POST['fecha']));
        $proveedor = find_by_id('proveedores',(int)$_POST['proveedor']);
        $proveedor = remove_junk($proveedor["razon_social"]);
        $rfc = remove_junk($db->escape($_POST['rfc']));
        $contacto = remove_junk($db->escape($_POST['contacto']));
        $telefono_proveedor = remove_junk($db->escape($_POST['telefono']));
        $solicitante = remove_junk($db->escape($_POST['solicitante']));
        $requisicion = remove_junk($db->escape($_POST['requisicion']));
        $proyecto = (int)$db->escape($_POST['proyecto']);
        $entregar_en = remove_junk($db->escape($_POST['entregar']));

        switch ($entregar_en) {
            case 1:
                $queryEntregar = find_by_id('proyectos', (int)$proyecto);
                $entregar_en = $queryEntregar["direccion"];
                break;
            case 2:
                $entregar_en = $_empresa["direccion"];
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
        $status = remove_junk($db->escape($_POST['status']));
        
        if(isset($_POST["cart"]) && !empty($_POST["cart"])) {
          foreach($_POST["cart"] AS $prod) {
            if($prod["precio_unitario"] > 0 AND $prod["cantidad"] > 0) {
              // Actualizamos las cantidades y montos del artículo modificado.
              $cantidad = $prod["cantidad"];
              $precio_unitario = $prod["precio_unitario"];
              $importe = $cantidad * $precio_unitario;
              $sqlAOC = "UPDATE articulos_ordenes_de_compra SET cantidad=" . $cantidad . ", precio_unitario=" . $precio_unitario . ", importe=" . $importe . "";
              $sqlAOC .= " WHERE id=" . $prod["id"] . ";";
              $resultAOC = $db->query($sqlAOC);
            }
          }
          
          // Buscamos todos los productos de la OC para volver a sumarizar
          $getAOC = "SELECT * FROM articulos_ordenes_de_compra WHERE oc=" . (int)$_GET['id'] . ";";
          $resultgetAOC = $db->query($getAOC);
          $subtotal_1 = "";
          
          foreach($resultgetAOC AS $rgAOC) {
            $totalRow = $rgAOC["precio_unitario"] * $rgAOC["cantidad"];
            $subtotal_1 += $totalRow;
          }
          
          $descuento = $db->escape($_POST['descuento']);
          $subtotal_2 = $subtotal_1 - $descuento;
          $iva = $subtotal_2 * 0.16;
          $total = $subtotal_2 + $iva;
          
          $updateOC = "UPDATE ordenes_de_compra SET subtotal_1=" . $subtotal_1 . ", subtotal_2=" . $subtotal_2 . ", iva=" . $iva . ", total=" . $total . "";
          $updateOC .= " WHERE oc=" . (int)$_GET['id'] . ";";
          $resultupdateOC = $db->query($updateOC);
        }
        
        $sql = "UPDATE ordenes_de_compra SET fecha='{$fecha}',proveedor='{$proveedor}',rfc='{$rfc}',contacto='{$contacto}',telefono_proveedor='{$telefono_proveedor}',proyecto='{$proyecto}',";
        $sql.= "entregar_en='{$entregar_en}',recibe='{$recibe}',telefono='{$tel}',condiciones_de_pago='{$condiciones_de_pago}',tiempo_pago='{$tiempo_pago}',tiempo_de_entrega='{$tiempo_de_entrega}',";
        $sql.= "comentario='{$comentario}',cotizacion_proveedor='{$cotizacion_proveedor}',tipo_moneda='{$tipo_moneda}',status='{$status}' WHERE id='{$e_oc['id']}';";
        $result = $db->query($sql);
        
        if($result){
            $pdf = createOC((int)$_GET['id'], $e_user["working_on"], $tipo_moneda);
            $session->msg('s',"OC " . $_GET['id'] . " Actualizada");
            redirect('edit_oc.php?id='.(int)$_GET['id'], false);
        } else {
            $session->msg('d',' Lo sentimos, ocurrió un error al intentar actualizar!');
            redirect('edit_oc.php?id='.(int)$_GET['id'], false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_oc.php?id='.(int)$_GET['id'], false);
    }
}

$sql = "SELECT * FROM articulos_ordenes_de_compra WHERE oc='" . $_GET['id'] ."'";
$rs_result = $db->query($sql);

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
          <span>Editando Orden de Compra <b>(OC-<?php echo $_GET['id']; ?>)</b></span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_oc.php?id=<?php echo (int)$e_oc['id'];?>" autocomplete="off">
        <input class="form-control" type="hidden" name="descuento" id="descuento" value="<?php echo $e_oc["descuento"]; ?>">
          <div class="col-md-12">
            <div class="form-group">
              <label for="fecha">Fecha</label>
              <input type="date" autocomplete="off" class="form-control" name="fecha" id="fecha" value="<?php echo $e_oc["fecha"]; ?>">
            </div>
            <div class="form-group">
              <!--<label for="proveedor">Proveedor</label>
              <input type="text" autocomplete="off" class="form-control" name="proveedor" id="proveedor" value="<?php echo $e_oc["proveedor"]; ?>">-->
                <label for="proveedor">Proveedor</label>
                <select class="form-control" name="proveedor" id="proveedor">
                    <option value="">-- Seleccionar Proveedor --</option>
                    <?php
                    foreach ($proveedores as $proveedor) {
                        if($proveedor["razon_social"] == $e_oc["proveedor"]) {
                            ?>
                            <option selected="selected" value="<?php echo $proveedor["id"]; ?>" /><?php echo $proveedor["razon_social"]; ?></option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $proveedor["id"]; ?>" /><?php echo $proveedor["razon_social"]; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
              <label for="rfc">RFC</label>
              <input type="text" autocomplete="off" class="form-control" name="rfc" id="rfc" value="<?php echo $e_oc["rfc"]; ?>" >
            </div>
            <div class="form-group">
              <label for="contacto">Contacto</label>
              <input class="form-control" type="text" name="contacto" id="contacto" value="<?php echo $e_oc["contacto"]; ?>"
            </div>
            <div class="form-group">
              <label for="telefono">Teléfono</label>
              <input class="form-control" type="text" name="telefono" id="telefono" value="<?php echo $e_oc["telefono_proveedor"]; ?>">
            </div>
            <div class="form-group">
              <label for="solicitante">Solicitante</label>
              <input type="text" autocomplete="off" class="form-control" name="solicitante" id="solicitante" value="<?php echo $e_oc["solicitante"]; ?>">
            </div>
            <div class="form-group">
              <label for="requisicion">Requisicion</label>
              <input class="form-control" type="text" name="requisicion" id="requisicion" value="<?php echo $e_oc["requisicion"]; ?>">
            </div>
            <div class="form-group">
              <label for="proyecto">Proyecto</label>
              <select class="form-control" name="proyecto">
                <option value="">-- Seleccionar Proyecto --</option>
                <?php
                  foreach ($proyectos as $proyecto) {
                    if($proyecto["id"] == $e_oc["proyecto"]) {  
                ?>
                      <option selected="selected" value="<?php echo $proyecto["id"]; ?>" /><?php echo $proyecto["nombre"]; ?></option>
                <?php
                    } else {
                ?>
                      <option value="<?php echo $proyecto["id"]; ?>" /><?php echo $proyecto["nombre"]; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="entregar">Entregar en</label>
              <input class="form-control" type="text" name="entregar" id="entregar" value="<?php echo $e_oc["entregar_en"]; ?>">
            </div>
            <div class="form-group">
              <label for="recibe">Recibe</label>
              <input class="form-control" type="text" name="recibe" id="recibe" value="<?php echo $e_oc["recibe"]; ?>">
            </div>
            <div class="form-group">
              <label for="tel">Teléfono</label>
              <input class="form-control" type="text" name="tel" id="tel" value="<?php echo $e_oc["telefono"]; ?>">
            </div>
              <div class="form-group">
                  <label for="tiempo_pago">Tiempo de Pago</label>
                  <select class="form-control" name="tiempo_pago">
                      <option value="">-- Seleccionar el Rango --</option>
                      <option value="1" <?php if($e_oc["tiempo_pago"] == 1) {?> selected="selected" <?php }?>>0 d&iacute;as</option>
                      <option value="2" <?php if($e_oc["tiempo_pago"] == 2) {?> selected="selected" <?php }?>>7 d&iacute;as</option>
                      <option value="3" <?php if($e_oc["tiempo_pago"] == 3) {?> selected="selected" <?php }?>>15 d&iacute;as</option>
                      <option value="4" <?php if($e_oc["tiempo_pago"] == 4) {?> selected="selected" <?php }?>>30 d&iacute;as</option>
                      <option value="5" <?php if($e_oc["tiempo_pago"] == 5) {?> selected="selected" <?php }?>>45 d&iacute;as</option>
                  </select>
              </div>
            <div class="form-group">
              <label for="condiciones_de_pago">Condiciones de Pago</label>
              <textarea class="form-control" type="text" name="condiciones_de_pago" id="condiciones_de_pago" value="<?php echo $e_oc["condiciones_de_pago"]; ?>"><?php echo $e_oc["condiciones_de_pago"]; ?></textarea>
            </div>
            <div class="form-group">
              <label for="tiempo_de_entrega">Tiempo de Entrega</label>
              <input class="form-control" type="text" name="tiempo_de_entrega" id="tiempo_de_entrega" value="<?php echo $e_oc["tiempo_de_entrega"]; ?>">
            </div>
            <div class="form-group">
              <label for="comentario">Comentario</label>
              <textarea class="form-control" type="text" name="comentario" id="comentario" value="<?php echo $e_oc["comentario"]; ?>"><?php echo $e_oc["comentario"]; ?></textarea>
            </div>
            <div class="form-group">
              <label for="cotizacion_proveedor">Cotización Proveedor</label>
              <input class="form-control" type="text" name="cotizacion_proveedor" id="cotizacion_proveedor" value="<?php echo $e_oc["cotizacion_proveedor"]; ?>">
            </div>
            <div class="form-group">
              <label for="tipo_moneda">Tipo de Moneda</label>
              <input class="form-control" type="text" name="tipo_moneda" id="tipo_moneda" value="<?php echo $e_oc["tipo_moneda"]; ?>">
            </div>
            <div class="form-group">
              <label for="prods">Productos Seleccionados</label>
              <table id="tblProdReq" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th class="text-center" data-placeholder="Select Empresa">Descripción Material/Servicio</th>
                    <th class="text-center">Unidad de Medida</th>
                    <th class="text-center">Precio</th>
                    <th class="text-center" style="width: 15%;">Cantidad Solicitada</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  while($row = $rs_result->fetch_assoc()) {
                    if($e_oc["status"] == "2") {
                ?>
                      <input type="hidden" name="cart[<?php echo $row["id"]?>][id]" value="<?php echo $row["id"]?>">
                      <tr>
                        <td><?php echo $row["sec"];?></td>
                        <td><?php echo $row["descripcion"]?></td>
                        <td><?php echo remove_junk($row["unidades"])?></td>
                        <td class="text-center"><input type="text" name="cart[<?php echo $row["id"]?>][precio_unitario]" value="<?php echo $row["precio_unitario"]?>"></td>
                        <td class="text-center"><input type="text" name="cart[<?php echo $row["id"]?>][cantidad]" value="<?php echo $row["cantidad"]?>"></td>
                      </tr>
                <?php
                    } else {
                ?>
                      <tr>
                        <td><?php echo $row["sec"];?></td>
                        <td><?php echo $row["descripcion"]?></td>
                        <td><?php echo remove_junk($row["unidades"])?></td>
                        <td><?php echo remove_junk($row["precio_unitario"])?></td>
                        <td><?php echo remove_junk($row["cantidad"])?></td>
                      </tr>
                <?php
                    }
                  }
                ?>
               </tbody>
             </table>
            </div>
            <div class="form-group">
              <label for="status">Estatus</label>
              <select class="form-control" name="status" <?php if($e_oc["status"] != "2") {?> disabled="disabled" <?php } ?>>
                <option value="0" <?php if($e_oc["status"] == "0") { ?> selected="selected" <?php } ?>>Autorizada</option>
                <option value="1" <?php if($e_oc["status"] == "1") { ?> selected="selected" <?php } ?>>Rechazada</option>
                <option value="2" <?php if($e_oc["status"] == "2") { ?> selected="selected" <?php } ?>>Abierta</option>
                <option value="3" <?php if($e_oc["status"] == "3") { ?> selected="selected" <?php } ?>>Cerrada</option>
                <!--<option value="4" <?php //if($e_oc["status"] == "4") { ?> selected="selected" <?php //} ?>>Autorizada para surtir</option>
                <option value="5" <?php //if($e_oc["status"] == "5") { ?> selected="selected" <?php //} ?>>Autorizada para compra</option>
                <option value="6" <?php //if($e_oc["status"] == "6") { ?> selected="selected" <?php //} ?>>Con observaciones</option>-->
                <option value="7" <?php if($e_oc["status"] == "7") { ?> selected="selected" <?php } ?>>Eliminada</option>
              </select>
            </div>
            <?php if($e_oc["status"] == "2") {?>
              <div class="form-group clearfix">
                <button type="submit" name="edit_oc" class="btn btn-primary">Actualizar Orden de Compra</button>
              </div>
            <?php } ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
    jQuery('#proveedor').change(function() {
        try{
            var url = "http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/getDatosProveedor.php";
            //var url = "http://127.0.0.1/inventario/getDatosProveedor.php";
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
</script>
<?php include_once('layouts/footer.php'); ?>
