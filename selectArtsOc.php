<?php
  $page_title = 'Registro de entrada de material/servicio';
  require_once('includes/load.php');
  
  // Checkin What level user has permission to view this page
  page_require_level("inventario");

  include_once('layouts/header.php');
  $e_user = find_by_id('users',(int)$_SESSION['user_id']);
  
  // Query para obtener artículos de Requisiciones Aprobadas
  $query = "SELECT ar.id AS id, ar.sec AS sec,ar.descripcion AS descripcion, ar.cantidad AS cantidad, ar.unidad_medida AS unidad_medida, r.proyecto AS proyecto, ar.requisicion AS requisicion, ar.unidad_medida AS medida, ar.sec AS sec, ar.faltan AS faltan";
  $query.= " FROM articulos_requisiciones AS ar INNER JOIN requisiciones AS r ON ar.requisicion=r.id WHERE r.status=0 AND ar.status NOT IN (2) AND ar.faltan > 0 AND ar.mostrar=1 AND r.empresa=" . $e_user["working_on"];
  $rs_result = $db->query($query);
?>
<div class="row">
  <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          Alta de Órden de Compra/Selección de Materiales
        </strong>
      </div>
    </div>
  </div>
  <form method="post" id="frmAddOc" action="add_oc.php" class="clearfix">
    <div class="col-md-12">
      <div class="panel panel-default panel-6">
        <div class="panel-body">
          <div class="form-group">
            <label for="prods">Seleccionar Productos</label><br>
            <table id="tblProdReq" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th class="text-center"></th>
                  <th class="text-center">Nombre del Material</th>
                  <th class="text-center">No. de Requisición</th>
                  <th class="text-center">Proyecto</th>
                  <th class="text-center">Unidad de Medida</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">Precio</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  while($row = $rs_result->fetch_assoc()) {
                    $proyecto = find_by_id('proyectos',(int)$row['proyecto']);
                ?>
                    <tr>
                      <td class="text-center">
                        <input class="checked" data-proyecto="<?php echo $proyecto["nombre"]?>" id="<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][check]" type='checkbox'>
                      </td>
                      <td>
                        <?php echo $row["descripcion"];?><br><br>
                        <textarea class="form-control" type="text" name="cart[<?php echo $row["id"]; ?>][comentarios]" id="product-<?php echo $row["id"]; ?>" placeholder="Comentarios"></textarea>
                      </td>
                      <td><?php echo (int)$row["requisicion"]?></td>
                      <td><?php echo $proyecto["nombre"]?></td>
                      <td><?php echo $row["unidad_medida"]?></td>
                      <td><input class="price" type="text" id="product-qty-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][cantidad]" value="<?php echo (int)$row ["faltan"]; ?>" placeholder="<?php echo (int)$row ["faltan"]; ?>" style="width: 90%;"><br><small>* Confirmar cantidad</small></td></td>
                      <td style="width: 20%;">
                          $ <input type="number" step="any" min="0" id="product-price-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][precio]" value="" placeholder="Asignar Precio s/IVA" style="width: 90%;">
                          <br><br>
                          <input type="radio" id="product-radio1-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][tipo_precio]" value="precio_unitario" checked="checked">Precio Unitario<br>
                          <input type="radio" id="product-radio2-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][tipo_precio]" value="global"> Global<br>
                      </td>
                      <td style="display: none;"><input type="text" id="product-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][unidades]" value="<?php echo $row["medida"]; ?>"></td>
                      <td style="display: none;"><input type="text" id="product-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][descripcion]" value="<?php echo remove_junk($row["descripcion"]); ?>"></td>
                      <td style="display: none;"><input type="text" id="product-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][requisicion]" value="<?php echo (int)$row["requisicion"]; ?>"></td>
                      <td style="display: none;"><input type="text" id="product-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][sec]" value="<?php echo (int)$row["sec"]; ?>"></td>
                      <td style="display: none;"><input type="text" id="product-<?php echo $row["id"]; ?>" name="cart[<?php echo $row["id"]; ?>][proyecto]" value="<?php echo remove_junk($proyecto["nombre"]); ?>"></td>
                    </tr>
                <?php }?>
              </tbody>
            </table>
          </div>
          <div class="form-group clearfix">
            <input type="submit" name="add_articulos_requi" id="add_articulos_requi" class="btn btn-info" value="Seleccionar">
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script>
jQuery( '.checked').on( 'click', function() {
  var favorite = [];
  
  jQuery.each(jQuery(".checked:checked"), function(){
    favorite.push(jQuery(this).attr("id"));
  });
    
  if (favorite.length === 0) {
    jQuery(".checked").prop('disabled', false);
  }
  
  var proyecto = jQuery(this).attr("data-proyecto");
  
  if( jQuery(this).is(':checked') ){
    // Enviamos a función countChecked, para deshabilitar checkbox que no sean del mismo proyecto
    countChecked(proyecto);
  } else {
    countUnchecked(proyecto);
  }
  
  function countChecked(proyecto) {
    jQuery('#tblProdReq .checked').each(function() {
      var id = jQuery(this).attr('id');
      
      if(jQuery(this).attr("data-proyecto") !== proyecto) {
        jQuery(this).prop('disabled', true);
        jQuery("#product-qty-" + id).prop('disabled', true);
        jQuery("#product-price-" + id).prop('disabled', true);
        jQuery("#product-radio1-" + id).prop('disabled', true);
        jQuery("#product-radio2-" + id).prop('disabled', true);
      }
    });
  }
  
  function countUnchecked(proyecto) {
    jQuery('#tblProdReq .checked').each(function() {
      var id = jQuery(this).attr('id');
      
      if(jQuery(this).attr("data-proyecto") !== proyecto) {
        jQuery(this).prop('disabled', false);
        jQuery("#product-qty-" + id).prop('disabled', false);
        jQuery("#product-price-" + id).prop('disabled', false);
        jQuery("#product-radio1-" + id).prop('disabled', false);
        jQuery("#product-radio2-" + id).prop('disabled', false);
      }
    });
  }
});

jQuery( "#add_articulos_requi" ).click(function(e) {
  var total = 0;
  jQuery.each(jQuery(".checked:checked"), function(){
    if (jQuery("#product-price-" + jQuery(this).attr("id")).val() == "") {
      console.log("Entra");
      total++;
    }
  });
  
  if (total > 0) {
    alert("El campo Precio debe de ser mayor a 0.");
    e.preventDefault();
  }
});
</script>
<?php include_once('layouts/footer.php'); ?>