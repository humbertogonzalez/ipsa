<?php
    $page_title = 'Alta de Requisición';
    require_once('includes/load.php');

    // Checkin What level user has permission to view this page
    page_require_level("requisiciones");
    $empresas = find_all('user_empresas');
    $proyectos = find_all('proyectos');
    $proveedores = find_all_proveedores();
    $e_user = find_by_id('users', (int)$_SESSION['user_id']);

    if(!$e_user){
        $session->msg("d","No existe el ID de Usuario.");
        redirect('add_requisicion.php');
    }

    if(isset($_POST['add_requisicion'])){
      if($_POST['LiqSelection2'] == "") {
        $_POST['LiqSelection2'] = $_POST['LiqSelection'];
      }
        $req_fields = array('empresa','solicitante','proyecto','fecha_surtido', 'LiqSelection', 'LiqSelection2');
        validate_fields($req_fields);

        if(empty($errors)){
            // Obtenemos último ID de requisición
            $idReq = "SELECT id FROM requisiciones ORDER BY id DESC LIMIT 1";
            $result = $db->query($idReq);
            $result = $db->fetch_assoc($result);
            $no_requisicion = $result['id'] + 1;
            $p_empresa = (int)$db->escape($_POST['empresa']);
            $p_solicitante = remove_junk($db->escape($_POST['solicitante']));
            $p_proyecto = (int)$db->escape($_POST['proyecto']);
            $p_seccion= $db->escape($_POST['seccion']);
            $p_proveedor = (int)$db->escape($_POST['proveedor']);
            $p_fecha_surtido = remove_junk($db->escape($_POST['fecha_surtido']));
            $jsonArts = json_decode($_POST['LiqSelection2'], true);
            $fecha_creacion = date("Y-m-d");
            $query = "INSERT INTO requisiciones (no_requisicion,empresa,solicitante,proyecto,seccion,proveedor,fecha_surtido,status,fecha_creacion)";
            $query .=" VALUES ('{$no_requisicion}', '{$p_empresa}', '{$p_solicitante}', '{$p_proyecto}', '{$p_seccion}', '{$p_proveedor}', '{$p_fecha_surtido}','2','{$fecha_creacion}')";
            $cant = 0;

            if($db->query($query)){
                // Al ingresar correctamente la requisición, se ingresan los artículos
                foreach($jsonArts AS $art) {
                  // Calculamos cantidad necesaria para pedir
                    $getQty = "SELECT existencia FROM inventario WHERE sec=" . $art["Id"] . ";";
                    $rsQty = $db->query($getQty);
                    $rsQty = $db->fetch_assoc($rsQty);
                    $surtir = 0;
                    $faltan = 0;
                    
                    if($art["Qty"] > $rsQty["existencia"]) {
                        $surtir = $rsQty["existencia"];
                        $faltan = $art["Qty"] - $rsQty["existencia"];
                    } else {
                        $surtir = $art["Qty"];
                    }
                    
                    if($art["Serie"]) {
                      $multiDescQ = 'SELECT count(sec) AS count, sum(existencia) AS sumaE FROM inventario WHERE descripcion LIKE "%' . $art["Name"] . '%" AND existencia > 0;';
                      $multiDescQ = $db->query($multiDescQ);
                      $multiDescQ = $db->fetch_assoc($multiDescQ);

                      if($art["Qty"] > $multiDescQ["sumaE"]) {
                        if(is_null($multiDescQ["sumaE"])) {
                          $multiDescQ["sumaE"] = 0;
                        }
                        $surtir = $multiDescQ["sumaE"];
                        $faltan = $art["Qty"] - $multiDescQ["sumaE"];
                      } else {
                        $surtir = $art["Qty"];
                        $faltan = 0;
                      }
                    }
                    
                    $queryInsert = "INSERT INTO articulos_requisiciones (sec, requisicion, descripcion, unidad_medida, cantidad, surtir, faltan) VALUES ('" . $art["Id"] . "','" . $no_requisicion . "','" . $art["Name"]  . "','" . $art["Um"]   . "'," . $art["Qty"] . "," . $surtir . "," . $faltan . ");";
                    
                    if($db->query($queryInsert)){
                        // Llamamos función que crea PDF de requisición
                        $session->msg('s',"La Requisición " . $no_requisicion . " ha sido creada exitosamente.");
                        $pdf = createRequisicion($no_requisicion, $p_empresa);
                    } else {
                        //failed
                        $session->msg('d',' Lo sentimos, ocurrió un error creado la requisición');
                        redirect('add_requisicion.php', false);
                    }
                }

                redirect('requisiciones.php', false);
            } else {
                //failed
                $session->msg('d',' Lo sentimos, ocurrió un error creado la requisición');
                redirect('add_requisicion.php', false);
            }
        } else {
            $session->msg("d", $errors);
            redirect('add_requisicion.php',false);
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
          <span>Alta de Requisición</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_requisicion.php" autocomplete="off">
          <div class="col-md-12">
            <div class="form-group">
              <label for="name">Empresa</label>
              <?php                 
                foreach ($empresas as $empresa) {
                  if($empresa["id"] == $e_user["working_on"]) {
              ?>
                    <input type="text" autocomplete="off" class="form-control" name="empresa2" id="empresa2" placeholder="<?php echo $empresa["empresa"]; ?>" value="<?php echo $empresa["empresa"]; ?>" readonly="readonly">
                    <input type="hidden" autocomplete="off" class="form-control" name="empresa" id="empresa" placeholder="<?php echo $empresa["empresa"]; ?>" value="<?php echo $e_user["working_on"]; ?>" readonly="readonly">
              <?php
                  }
                }
              ?>
            </div>
            <div class="form-group">
              <label for="solicitante">Solicitante</label>
              <input type="text" autocomplete="off" class="form-control" name="solicitante" id="solicitante" placeholder="<?php echo $e_user["username"]; ?>" value="<?php echo $e_user["username"]; ?>" readonly="readonly">
            </div>
            <div class="form-group">
              <label for="proyecto">Proyecto</label>
              <select class="form-control" name="proyecto" required>
                <option value="">-- Seleccionar Proyecto --</option>
                <?php foreach ($proyectos as $proyecto) { ?>
                  <option value="<?php echo $proyecto["id"]; ?>" /><?php echo $proyecto["nombre"]; ?><br />
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="seccion">Secci&oacute;n</label>
              <input type="text" autocomplete="off" class="form-control" name="seccion" id="seccion">
            </div>
            <div class="form-group">
              <label for="proveedor">Proveedor Sugerido</label>
              <select class="form-control" name="proveedor">
                <option value="">-- Seleccionar Proveedor --</option>
                <?php foreach ($proveedores as $proveedor) { ?>
                  <option value="<?php echo $proveedor["id"]; ?>" /><?php echo $proveedor["razon_social"]; ?><br />
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="fecha_surtido">Debe surtirse el</label>
              <?php
                $todayDate = date('Y-m-d');
                $todayDate = date('Y-m-d', strtotime($todayDate. ' + 1 days'));
              ?>
              <input class="form-control" type="date" min="<?php echo $todayDate; ?>" name="fecha_surtido" id="fecha_surtido" value="" placeholder="Debe surtirse el" required>
            </div>
            <!--<div id="resultReq"></div>-->
            <div id="productiv"></div>
            <div class="form-group">
              <label id="selProds" for="prods">Seleccionar Productos</label>
            </div>
            <div id="toys-grid">
              <input type="hidden" name="rowcount" id="rowcount" />					
            </div>
            <div class="form-group clearfix" align="right">
              <input type="hidden" id="LiqSelection" name="LiqSelection"/>
              <input type="hidden" id="LiqSelection2" name="LiqSelection2"/>
              <button type="submit" id="add_requisicion" name="add_requisicion" class="btn btn-primary">Crear Requisición</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
// Prevenimos que den enter a botón para querer buscar
jQuery(document).ready(function() {
  jQuery(window).keydown(function(event){
    if(event.keyCode == 13) {
      jQuery("#goBtn").click();
      event.preventDefault();
      return false;
    }
  });
});

// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#add_requisicion').click(function() {
        return window.confirm("¿Desea terminar la Requisición?");
    });
});

function getresult(url) {    
  jQuery.ajax({
    url: url,
    type: "POST",
    data:  {rowcount:jQuery("#rowcount").val(),descripcion:jQuery("#descripcion").val(),sec:jQuery("#sec").val()},
    success: function(data){ jQuery("#toys-grid").html(data); jQuery('#add-form').hide();}
  });
}

getresult("getresult.php");

function AddToHidden(name, id, value, um, cant, no_serie){
  var curValue = jQuery('#LiqSelection').val();
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
        curObject.Qty = value;
      }
    }
    
    if(value > 0) {
      // If ID isn't added in hidden input, create New Object 
      if(!existingId){
        newObject.Name = name;
        newObject.Id = id;
        newObject.Qty = value;
        newObject.Um = um;
        newObject.Cant = cant;
        newObject.Serie = no_serie;
        newArray.push(newObject);
      }
    }
  } else {
    // Create New Object
    newObject.Name = name;
    newObject.Id = id;
    newObject.Qty = value;
    newObject.Um = um;
    newObject.Cant = cant;
    newObject.Serie = no_serie;
    newArray.push(newObject);
  }
  jQuery('#LiqSelection').val(JSON.stringify(newArray));
  
  // Armamos nueva tabla con los productos que se seleccionaron
  jQuery("#productiv").empty();
  var obj = jQuery.parseJSON( jQuery('#LiqSelection').val() );
  var div = "<table class='table table-bordered table-striped'><tr><td colspan='4' class='text-center'>Productos Seleccionados</td></tr><tr><th class='text-center'>#</th><th class='text-center'>Descripción Material/Servicio</th><th class='text-center'>Cantidad Solicitada</th><th class='text-center'></th></tr>";
  jQuery.each(obj, function(i, item) {
    div += "<tr id='prod"+item.Id+"'><td>"+item.Id+"</td><td>"+item.Name+"</td><td><input type='text' data-cant='"+item.Cant +"' data-um='"+item.Um +"' id='"+item.Id+"' name='"+item.Name+"' value='"+item.Qty+"' onchange='AddToHidden2(this.name, this.id, this.value, $(this).attr(\"data-um\"), $(this).attr(\"data-cant\"))' val='" + item.Qty + "' placeholder='" + item.Qty + "'></td><td><div onclick='deleteRow(&#39;prod"+item.Id+"&#39;);' style='cursor: pointer;'>Eliminar</div></td></tr>";
  });
  div += "</table>";
  jQuery('#LiqSelection2').val();
  jQuery("#productiv").append( div );
}

function AddToHidden2(name, id, value, um, cant, no_serie){
  var curValue = jQuery('#LiqSelection2').val();
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
        curObject.Qty = value;
      }
    }
    
    if(value > 0) {
      // If ID isn't added in hidden input, create New Object 
      if(!existingId){
        newObject.Name = name;
        newObject.Id = id;
        newObject.Qty = value;
        newObject.Um = um;
        newObject.Cant = cant;
        newObject.Serie = no_serie;
        newArray.push(newObject);
      }
    }
  } else {
    // Create New Object
    newObject.Name = name;
    newObject.Id = id;
    newObject.Qty = value;
    newObject.Um = um;
    newObject.Cant = cant;
    newObject.Serie = no_serie;
    newArray.push(newObject);
  }
  jQuery('#LiqSelection2').val(JSON.stringify(newArray));
}

function checkbox(id) {
  document.getElementById('check-' + id).click();
}

function deleteRow(divId) {
    jQuery("#"+divId+"").remove();
}

// Función para campo de productos vacío.
jQuery('form').submit(function () {
    // Get the Login Name value and trim it
    var prod = jQuery.trim(jQuery('#LiqSelection').val());

    // Check if empty of not
    if (prod === '') {
        alert('No ha seleccionado productos');
        return false;
    }
});
</script>
<?php include_once('layouts/footer.php'); ?>