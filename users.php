<?php
  $page_title = 'Listado de Usuarios';
  require_once('includes/load.php');

  // Checkin What level user has permission to view this page
  page_require_level("usuarios");

  $all_users = find_all_user();
  $puestos = find_all('user_puestos');
  $empresas = find_all('user_empresas');
  
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
          <span>Usuarios</span>
        </strong>
        <a href="add_user.php" class="btn btn-info pull-right">Agregar Nuevo Usuario</a>
      </div>
      <div class="panel-body">
        <input class="searchUser" type="text" id="searchUser" onkeyup="searchUsername();" placeholder="Búsqueda de Usuario"><br><br>
        <table id="usersTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">ID</th>
              <th>Nombre Completo </th>
              <!--<th>Nombre Usuario</th>-->
              <!--<th class="text-center" style="width: 15%;">Rol del Usuario</th>-->
              <th class="text-center" style="width: 10%;">Puesto</th>
              <th style="width: 20%;">Empresas</th>
              <!--<th class="text-center" style="width: 10%;">Status</th>-->
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($all_users as $a_user) { ?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td><?php echo remove_junk($a_user['name']) . " " . remove_junk($a_user['ap_paterno'])  . " " . remove_junk($a_user['ap_materno'])?></td>
                <!--<td><?php echo remove_junk($a_user['username'])?></td>-->
                <!--<td class="text-center"><?php echo remove_junk(ucwords($a_user['group_name']))?></td>-->
                <td class="text-center">
                  <?php echo remove_junk($a_user['puesto']);?>
                </td>
                <!--<td><?php echo read_date($a_user['last_login'])?></td>-->
                <td>
                  <?php
                    $explodeEmpresas = explode(",", $a_user['empresas']);
                    foreach($explodeEmpresas AS $emp) {
                      foreach($empresas AS $empresa) {
                        if($emp == $empresa["id"]) {
                          echo " - " . $empresa["empresa"] . "<br>";
                        }
                      }
                    }
                  ?>
                </td>
                <!--<td class="text-center">
                  <?php if($a_user['status'] === '1'): ?>
                    <span class="label label-success"><?php echo "Active"; ?></span>
                  <?php else: ?>
                    <span class="label label-danger"><?php echo "Deactive"; ?></span>
                  <?php endif;?>
                </td>-->
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_user.php?id=<?php echo (int)$a_user['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                    <a href="delete_user.php?id=<?php echo (int)$a_user['id'];?>" onclick="return confirmDelete(this);" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Baja">
                      <i class="glyphicon glyphicon-remove"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
function searchUsername() {
  // Declare variables 
  var input, filter, table, tr, td, i;
  input = document.getElementById("searchUser");
  filter = input.value.toUpperCase();
  table = document.getElementById("usersTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
  }
}
function confirmDelete(link) {
  if (confirm("¿Esta seguro de querer realizar la acción?")) {
      doAjax(link.href, "POST"); // doAjax needs to send the "confirm" field
  }
  return false;
}
</script>
<?php include_once('layouts/footer.php'); ?>
