<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<style>
  .panel-6 {
    min-height: 600px;
  }
</style>
<?php
  $page_title = 'Editar Proyecto';
  require_once('includes/load.php');
  
  // Únicamente el Administrador puede ver funciones de Usuarios
  page_require_level("proyectos");
  
  $e_proyecto = find_by_id('proyectos',(int)$_GET['id']);
  $proyectos = find_all('proyectos');
  
  if($_GET['id']== ""){
    $session->msg("d","No existe el proyecto.");
    redirect('proyectos.php');
  }
  
  // Actualizar información del Proveedor
  if(isset($_POST['edit_proyecto'])) {
    $req_fields = array('nombre','codigo_ano','codigo_socio','codigo_cliente_final','codigo_fase','responsable','direccion','contacto','correo_contacto','telefono');
    validate_fields($req_fields);
    
    if(empty($errors)){
      $contrato = remove_junk($db->escape($_POST['contrato']));
      $fecha_inicio = remove_junk($db->escape($_POST['fecha_inicio']));
      $fecha_fin = remove_junk($db->escape($_POST['fecha_fin']));
      $nombre = remove_junk($db->escape($_POST['nombre']));
      $codigo_ano = remove_junk($db->escape($_POST['codigo_ano']));
      $codigo_socio = remove_junk($db->escape($_POST['codigo_socio']));
      $codigo_cliente_final = remove_junk($db->escape($_POST['codigo_cliente_final']));
      $codigo_fase = remove_junk($db->escape($_POST['codigo_fase']));
      $codigo = $codigo_ano . $codigo_socio . $codigo_cliente_final . $codigo_fase;
      $responsable = remove_junk($db->escape($_POST['responsable']));
      $presupuesto_asignado = remove_junk($db->escape($_POST['presupuesto_asignado']));
      $direccion = remove_junk($db->escape($_POST['direccion']));
      $contacto = remove_junk($db->escape($_POST['contacto']));
      $correo_contacto = remove_junk($db->escape($_POST['correo_contacto']));
      $telefono = remove_junk($db->escape($_POST['telefono']));
      $sql = "UPDATE proyectos SET contrato='{$contrato}', fecha_inicio='{$fecha_inicio}', fecha_fin='{$fecha_fin}',nombre='{$nombre}', codigo_ano='{$codigo_ano}', codigo_socio='{$codigo_socio}', codigo_cliente_final='{$codigo_cliente_final}', codigo_fase='{$codigo_fase}',codigo='{$codigo}',responsable='{$responsable}',presupuesto_asignado='{$presupuesto_asignado}',direccion='{$direccion}',contacto='{$contacto}',correo_contacto='{$correo_contacto}', telefono='{$telefono}'WHERE id='{$_GET['id']}';";
      $result = $db->query($sql);
      
      if($result && $db->affected_rows() === 1){
        $session->msg('s',"Proyecto Actualizado");
        redirect('edit_proyecto.php?id='.(int)$e_proyecto['id'], false);
      } else {
        $session->msg('d',' Lo sentimos, ocurrió un error al intentar actualizar!');
        redirect('edit_proyecto.php?id='.(int)$e_proyecto['id'], false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_proyecto.php?id='.(int)$e_proyecto['id'],false);
    }
  }
  
  // Buscamos las órdenes de compra del proyecto
  $ordenesCompra = "SELECT id, oc, SUM(total) AS total FROM ordenes_de_compra WHERE proyecto=" . $_GET['id'] . " GROUP BY proyecto ASC;";
  $rs_result = $db->query($ordenesCompra);
  
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
          <span><?php echo remove_junk($e_proyecto['nombre']); ?></span>
        </strong>
      </div>
      <div class="panel-body">
        <div class="panel">
          <div class="text-center">
            <p id="chartContainer" style="height: 300px; width: 100%;">></p>
            <?php
              $presupuesto = $e_proyecto['presupuesto_asignado'];
              $pa = 0;
              
              while($row = $rs_result->fetch_assoc()) {
                $pa = round($row["total"] / $presupuesto * 100, 2);
              }
              
              $total = 100 - $pa;
              
              $dataPoints = array(
                array("y" => $pa, "legendText" => $e_proyecto['nombre'], "label" => $e_proyecto['nombre'], "color" => "red"),
                array("y" => $total, "legendText" => "Presupuesto", "label" => "Presupuesto"),
              );
            ?>
          </div>
        </div>
        <form method="post" action="edit_proyecto.php?id=<?php echo (int)$e_proyecto['id'];?>" autocomplete="off">
          <div class="col-md-6">
            <div class="form-group">
              <label for="nombre">Contrato</label>
              <input type="text" class="form-control" name="contrato" value="<?php echo $e_proyecto['contrato']; ?>">
            </div>
            <div class="form-group">
              <label for="nombre">Fecha de Inicio</label>
              <input type="date" class="form-control" name="fecha_inicio" value="<?php echo $e_proyecto['fecha_inicio']; ?>">
            </div>
            <div class="form-group">
              <label for="nombre">Fecha fin</label>
              <input type="date" class="form-control" name="fecha_fin" value="<?php echo $e_proyecto['fecha_fin']; ?>">
            </div>
            <div class="form-group">
              <label for="nombre">Nombre</label>
              <input type="text" class="form-control" name="nombre" value="<?php echo $e_proyecto['nombre']; ?>">
            </div>
            <div class="form-group">
              <label for="codigo">Código</label>
              <!--<input readonly="readonly" type="text" class="form-control" name="codigo_ano" value="<?php echo date("Y");?>"><br>-->
              <select class="form-control" name="codigo_ano">
                  <option value="">-- Seleccione Socio de Negocio --</option>
                  <option value="00" <?php if($e_proyecto['codigo_ano'] == "2018") { ?> selected="selected" <?php } ?>>2018</option>
                  <option value="01" <?php if($e_proyecto['codigo_ano'] == "2017") { ?> selected="selected" <?php } ?>>2017</option>
              </select><br>
              <select class="form-control" name="codigo_socio">
                  <option value="">-- Seleccione Socio de Negocio --</option>
                  <option value="00" <?php if($e_proyecto['codigo_socio'] == "00") { ?> selected="selected" <?php } ?>>00-Ninguno</option>
                  <option value="01" <?php if($e_proyecto['codigo_socio'] == "01") { ?> selected="selected" <?php } ?>>01-Axtel</option>
                  <option value="02" <?php if($e_proyecto['codigo_socio'] == "02") { ?> selected="selected" <?php } ?>>02-ICOSA</option>
                  <option value="03" <?php if($e_proyecto['codigo_socio'] == "03") { ?> selected="selected" <?php } ?>>03-Servicios Axtel</option>
                  <option value="04" <?php if($e_proyecto['codigo_socio'] == "04") { ?> selected="selected" <?php } ?>>04-UTMCA</option>
                  <option value="05" <?php if($e_proyecto['codigo_socio'] == "05") { ?> selected="selected" <?php } ?>>05-CENTLA</option>
                  <option value="06" <?php if($e_proyecto['codigo_socio'] == "06") { ?> selected="selected" <?php } ?>>06-KIO</option>
                  <option value="07" <?php if($e_proyecto['codigo_socio'] == "07") { ?> selected="selected" <?php } ?>>07-TRUSTWAVE</option>
              </select><br>
              <select class="form-control" name="codigo_cliente_final">
                  <option value="">-- Seleccione Socio Final --</option>
                  <option value="00" <?php if($e_proyecto['codigo_cliente_final'] == "00") { ?> selected="selected" <?php } ?>>00-Ninguno</option>
                  <option value="01" <?php if($e_proyecto['codigo_cliente_final'] == "01") { ?> selected="selected" <?php } ?>>01-SEDATU</option>
                  <option value="02" <?php if($e_proyecto['codigo_cliente_final'] == "02") { ?> selected="selected" <?php } ?>>02-SEDESOL</option>
                  <option value="03" <?php if($e_proyecto['codigo_cliente_final'] == "03") { ?> selected="selected" <?php } ?>>03-C5 DURANGO</option>
                  <option value="04" <?php if($e_proyecto['codigo_cliente_final'] == "04") { ?> selected="selected" <?php } ?>>04-C4 GOMEZ PALACIO</option>
                  <option value="05" <?php if($e_proyecto['codigo_cliente_final'] == "05") { ?> selected="selected" <?php } ?>>05-C4 SAN PEDRO</option>
                  <option value="06" <?php if($e_proyecto['codigo_cliente_final'] == "06") { ?> selected="selected" <?php } ?>>06-IECDMX</option>
                  <option value="07" <?php if($e_proyecto['codigo_cliente_final'] == "07") { ?> selected="selected" <?php } ?>>07-SICODES</option>
              </select><br>
              <input type="number" min="1" max="999" class="form-control" name="codigo_fase" value="<?php echo $e_proyecto['codigo_fase']; ?>" placeholder="001 a 999">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="responsable">Responsable</label>
              <input type="text" class="form-control" name="responsable" value="<?php echo $e_proyecto['responsable']; ?>">
            </div>
            <div class="form-group">
              <label for="presupuesto_asignado">Presupuesto Asignado</label>
              <input type="text" class="form-control" name="presupuesto_asignado" value="<?php echo $e_proyecto['presupuesto_asignado']; ?>">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" class="form-control" name="direccion" value="<?php echo $e_proyecto['direccion']; ?>">
            </div>
            <div class="form-group">
                <label for="contacto">Contacto</label>
                <input type="text" class="form-control" name="contacto" value="<?php echo $e_proyecto['contacto']; ?>">
            </div>
            <div class="form-group">
                <label for="correo_contacto">Correo Contacto</label>
                <input type="text" class="form-control" name="correo_contacto" value="<?php echo $e_proyecto['correo_contacto']; ?>">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" class="form-control" name="telefono" value="<?php echo $e_proyecto['telefono']; ?>">
            </div>
            <div class="form-group clearfix">
                <button type="submit" name="edit_proyecto" class="btn btn-primary">Guardar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(function () {
        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            legend: {
                fontSize: 12,
                fontFamily: "Helvetica"
            },
            theme: "light1",
            data: [
            {
                type: "doughnut",
                color: "green",
                indexLabelFontFamily: "Garamond",
                indexLabelFontSize: 20,
                indexLabel: "{label} {y}%",
                startAngle: -20,
                //showInLegend: true,
                toolTipContent: "{legendText} {y}%",
                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
            }
            ]
        });
        chart.render();
    });
</script>
<?php include_once('layouts/footer.php'); ?>
