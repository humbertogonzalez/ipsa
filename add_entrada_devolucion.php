<?php
  $page_title = 'Registro de Entrada por Devolución';
  require_once('includes/load.php');
  
  // Checkin What level user has permission to view this page
  page_require_level("requisiciones");
  $e_user = find_by_id('users', (int)$_SESSION['user_id']);
  
  if(!$e_user){
    $session->msg("d","No existe el ID de Usuario.");
    redirect('add_requisicion.php');
  }
  
  if(isset($_POST['add_entrada'])){
    $req_fields = array('LiqSelection');
    validate_fields($req_fields);
        
    if(empty($errors)){
      $jsonArts = json_decode($_POST['LiqSelection'], true);
      $cant = 0;
      
      
      foreach($jsonArts AS $art) {
        if($art["Qty"] > 0 && $art["Id"] != "") {
          $cant = $art["Qty"];
          $sec = $art["Id"];
          $desc = $art["Name"];
          $sql = "UPDATE inventario SET existencia = existencia+{$cant} WHERE sec='{$sec}';";
          
          if($db->query($sql)) {
            $successMsg .= "Inventario actualizado exitosamente (" . $desc . ").\n";
          } else {
            $errorMsg = "Lo sentimos, ocurrió un error generando la entrada";
          }
        }
      }
      
      if($successMsg) {
        $session->msg('s', $successMsg);
        redirect('add_entrada_devolucion.php', false);
      } elseif($errorMsg) {
        $session->msg('d', $errorMsg);
        redirect('add_entrada.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('add_entrada_devolucion.php',false);
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
          <span>Registro de Entrada por Devolución</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_entrada_devolucion.php" autocomplete="off">
          <div class="form-group clearfix" align="right">
            <input type="hidden" id="LiqSelection" name="LiqSelection"/>
            <button type="submit" id="add_entrada" name="add_entrada" class="btn btn-primary">Crear Entrada</button>
          </div>
          <div id="productiv"></div>
          <div class="form-group">
            <label for="prods">Seleccionar Productos</label>
            <div id="toys-grid">
              <input type="hidden" name="rowcount" id="rowcount" />					
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
    jQuery('#add_entrada').click(function() {
        return window.confirm("¿Desea terminar la Entrada?");
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
    
    getresult("getresultEntrada.php");
    
    function add() {
      var valid = validate();
      
      if(valid) {
        jQuery.ajax({
          url: "add.php",
          type: "POST",
          data:  {descripcion:jQuery("#add-descripcion").val(),sec:jQuery("#add-sec").val(),category:jQuery("#category").val(),price:jQuery("#price").val(),stock_count:jQuery("#stock_count").val()},
          success: function(data){ getresult("getresultEntrada.php"); }
        });
      }
    }
    
    function showEdit(id) {
      jQuery.ajax({
        url: "show_edit.php?id="+id,
        type: "POST",
        success: function(data){ jQuery("#toy-"+id).html(data); }
      });
    }

    function edit(id) {
      var valid = validate();
      if(valid) {
        jQuery.ajax({
          url: "edit.php?id="+id,
          type: "POST",
          data:  {descripcion:jQuery("#add-descripcion").val(),sec:jQuery("#add-sec").val(),category:jQuery("#category").val(),price:jQuery("#price").val(),stock_count:jQuery("#stock_count").val()},
          success: function(data){ jQuery("#toy-"+id).html(data); }
        });
      }
    }	

    function del(id) {
      jQuery.ajax({
        url: "delete.php?id="+id,
        type: "POST",
        success: function(data){ jQuery("#toy-"+id).html(''); }
      });
    }

    function validate() {
      var valid = true;	
      jQuery(".demoInputBox").css('background-color','');
      jQuery(".info").html('');

      if(!jQuery("#add-descripcion").val()) {
        jQuery("#descripcion-info").html("(required)");
        jQuery("#add-descripcion").css('background-color','#FFFFDF');
        valid = false;
      }

      if(!jQuery("#add-sec").val()) {
        jQuery("#sec-info").html("(required)");
        jQuery("#add-sec").css('background-color','#FFFFDF');
        valid = false;
      }

      if(!jQuery("#category").val()) {
        jQuery("#category-info").html("(required)");
        jQuery("#category").css('background-color','#FFFFDF');
        valid = false;
      }

      if(!jQuery("#price").val()) {
        jQuery("#price-info").html("(required)");
        jQuery("#price").css('background-color','#FFFFDF');
        valid = false;
      }	

      if(!jQuery("#stock_count").val()) {
        jQuery("#stock_count-info").html("(required)");
        jQuery("#stock_count").css('background-color','#FFFFDF');
        valid = false;
      }	
    
      return valid;
    }
  </script>
<script>
function AddToHidden(name, id, value, um, cant){
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
    newArray.push(newObject);
  }
  jQuery('#LiqSelection').val(JSON.stringify(newArray));
  
  // Armamos nueva tabla con los productos que se seleccionaron
  jQuery("#productiv").empty();
  var obj = jQuery.parseJSON( jQuery('#LiqSelection').val() );
  var div = "<table class='table table-bordered table-striped'><tr><td colspan='3' class='text-center'>Productos Seleccionados</td></tr><tr><th class='text-center'>#</th><th class='text-center'>Descripción Material/Servicio</th><th class='text-center'>Cantidad Solicitada</th></tr>";
  jQuery.each(obj, function(i, item) {
    div += "<tr><td>"+item.Id+"</td><td>"+item.Name+"</td><td><input type='text' data-cant='"+item.Cant +"' data-um='"+item.Um +"' id='"+item.Id+"' name='"+item.Name+"' val='"+item.Qty+"' onchange='AddToHidden2(this.name, this.id, this.value, $(this).attr(\"data-um\"), $(this).attr(\"data-cant\"))'> Selección previa: (" + item.Qty + ")</td></tr>";
  });
  div += "</table>";
  jQuery('#LiqSelection2').val();
  jQuery("#productiv").append( div );
}

function AddToHidden2(name, id, value, um, cant){
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
    newArray.push(newObject);
  }
  jQuery('#LiqSelection2').val(JSON.stringify(newArray));
}

/*var getSelected = function(table_id){
  var selected_items=[];
  var list = document.getElementById(table_id).getElementsByTagName("tr");

  for(var i=1;i<(list.length-1); i++){
    if(list[i].getElementsByTagName('input')[0].checked){
        selected_items.push({
            id:list[i].getElementsByTagName('input')[0].value,
            sec:list[i].getElementsByTagName('td')[1].innerHTML,
            name:list[i].getElementsByTagName('td')[3].innerHTML,
            qty:list[i].getElementsByTagName('td')[5].innerHTML
        });
    }
  }

  return selected_items;
}

var printPreview = function(items, container_id){
  var result_text = "<table class='table table-bordered table-striped'><tr><td colspan='3' align='center'>Productos Seleccionados</td></tr><tr><td>#</td><td>Descripción Material/Servicio</td><td>Cantidad Solicitada</td></tr>";
  
  for(var i=0; i<items.length; i++){
      result_text += "<tr><td>"+items[i].sec+"</td><td>"+items[i].name+"</td><td>"+items[i].qty+"</td></tr>";
  }
  
  result_text += "</table>";
  
  document.getElementById(container_id).innerHTML=result_text;
};

var refresh = function(){
  printPreview(getSelected('table-list'), 'resultReq');
}*/

function checkbox(id) {
  document.getElementById('check-' + id).click();
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