<?php
  $page_title = 'Listado de Inventario';
  require_once('includes/load.php');

  // Checkin What level user has permission to view this page
  page_require_level("inventarios");

  $all_users = find_all_user();
  //$inventario = find_all('inventario');
  $empresas = find_all('user_empresas');
  
  if (isset($_GET["page"])) {
    $page  = $_GET["page"];
  } else {
    $page=1;
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
          <span>Inventario</span>
        </strong>
        <a href="add_masive.php" class="btn btn-info pull-right tablesorter">Alta Masiva de Inventario</a>
        <a href="add_inventario.php" class="btn btn-info pull-right tablesorter" style="margin-right: 20px;">Alta de Inventario</a>
      </div>
      <div class="panel-body">
        <div id="productiv"></div>
        <div id="toys-grid">
          <input type="hidden" name="rowcount" id="rowcount" />					
        </div>
      </div>
    </div>
  </div>
</div>
<script>
jQuery(document).ready(function() {
  jQuery(window).keydown(function(event){
    if(event.keyCode == 13) {
      jQuery("#goBtn2").click();
      event.preventDefault();
      return false;
    }
  });
});
  
function getresultInv(url) {    
  jQuery.ajax({
    url: url,
    type: "POST",
    data:  {rowcount:jQuery("#rowcount").val(),descripcion:jQuery("#descripcion").val(),sec:jQuery("#sec").val()},
    success: function(data){ jQuery("#toys-grid").html(data); jQuery('#add-form').hide();}
  });
}

getresultInv("getresultInv.php");
</script>
<?php include_once('layouts/footer.php'); ?>