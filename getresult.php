<?php
require_once("includes/load.php");
require_once("includes/dbcontroller.php");
require_once("includes/pagination.class.php");

$db_handle = new DBController();
$name = "";
$code = "";
$perPage = new PerPage();
$queryCondition = "";

if(!empty($_POST["descripcion"])) {
	$name = $_POST["descripcion"];
	$queryCondition .= " WHERE descripcion LIKE '%" . $_POST["descripcion"] . "%'";
}

$orderby = " ORDER BY descripcion asc";
$sql = "SELECT * FROM inventario " . $queryCondition;
$paginationlink = "getresult.php?page=";
$page = 1;

if(!empty($_GET["page"])) {
	$page = $_GET["page"];
}

$start = ($page-1) * $perPage->perpage;

if($start < 0) $start = 0;

$query =  $sql . $orderby .  " limit " . $start . "," . $perPage->perpage;
$result = $db_handle->runQuery($query);

if(empty($_GET["rowcount"])) {
	$_GET["rowcount"] = $db_handle->numRows($sql);
}

$perpageresult = $perPage->perpage($_GET["rowcount"], $paginationlink);
?>
<form name="frmSearch" method="post" action="ad_requisicion.php">
	<div class="search-box">
		<p class="search">
			<input type="hidden" id="rowcount" name="rowcount" value="<?php echo $_GET["rowcount"]; ?>">
			<input class="searchUser" type="text" name="descripcion" id="descripcion" value="<?php echo $name; ?>" placeholder="Búsqueda de Producto">
			<input type="button" id="goBtn" name="go" class="btn btn-primary" value="Buscar" onclick="getresult('<?php echo $paginationlink . $page; ?>')">
			<!--<input type="reset" class="btn btn-primary" value="Reset" onclick="getresult('getresult.php');">-->
		</p>
	</div>
	<table class="table table-bordered table-striped" id="table-list">
		<thead>
			<tr>
				<th class="text-center" style="width: 50px;"></th>
				<th class="text-center" style="width: 50px;">#</th>
				<th class="text-center" style="width: 15%;">SKU</th>
				<th class="text-center" data-placeholder="Select Empresa">Descripción Material/Servicio</th>
				<th class="text-center">Unidad de Medida</th>
				<th class="text-center" style="width: 15%;">Cantidad Solicitada</th>
				<th class="text-center">Disponible</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if(!empty($result)) {
					foreach($result as $k=>$v) {
			?>
						<tr id="toy-<?php echo $result[$k]["sec"]; ?>">
							<!--<td class="text-center"><input class="" type='checkbox' value="<?php echo $result[$k]["sec"]; ?>" onclick="re	fresh();"></td>-->
							<td class="text-center">
								<span class="glyphicon glyphicon-plus-sign" onclick="checkbox(<?php echo $result[$k]["sec"]; ?>);"></span>
								<input id="check-<?php echo $result[$k]["sec"]; ?>" type='checkbox' value="<?php echo $result[$k]["sec"]; ?>" onclick="refresh();" style="display: none;">
							</td>
							<td><?php echo $result[$k]["sec"]; ?></td>
							<td><?php echo $result[$k]["modelo_base"]; ?></td>
							<td><?php echo $result[$k]["descripcion"]; ?></td>
							<td><?php echo $result[$k]["um"]; ?></td>
							<td><input type="text" data-serie="<?php echo $result[$k]["no_serie"]; ?>" data-cant="<?php echo (int)$result[$k]["cantidad"]; ?>" data-um="<?php echo $result[$k]["um"]; ?>" id="<?php echo (int)$result[$k]["sec"]; ?>" name="<?php echo remove_junk($result[$k]["descripcion"])?>" val="" onchange="AddToHidden(this.name, this.id, this.value, $(this).attr('data-um'), $(this).attr('data-cant'), $(this).attr('data-serie'));"></td>
							<td><?php echo (int)$result[$k]["existencia"]?></td>
						</tr>
			<?php
					}
				}
				
				if(isset($perpageresult)) {
			?>
					<tr>
						<td colspan="7" align=right> <?php echo $perpageresult; ?></td>
					</tr>
			<?php } ?>
		<tbody>
	</table>
</form>