<?php
require_once("includes/load.php");
require_once("includes/dbcontroller.php");
require_once("includes/pagination.class.php");

$db_handle = new DBController();
$name = "";
$code = "";
$perPage = new PerPage2();
$queryCondition = "";

if(!empty($_POST["descripcion"])) {
	$name = $_POST["descripcion"];
	$queryCondition .= " WHERE descripcion LIKE '%" . $_POST["descripcion"] . "%'";
}

$orderby = " ORDER BY descripcion asc";
$sql = "SELECT sec, tipo_de_articulo, marca, modelo_base, sku, descripcion, no_serie, existencia FROM inventario " . $queryCondition;
$paginationlink = "getresultInv.php?page=";
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
<form name="frmSearch" method="post" action="inventarios.php">
	<div class="search-box">
		<p class="search">
			<input type="hidden" id="rowcount" name="rowcount" value="<?php echo $_GET["rowcount"]; ?>">
			<input class="searchUser" type="text" name="descripcion" id="descripcion" value="<?php echo $name; ?>" placeholder="Búsqueda de Producto">
			<input type="button" id="goBtn2" name="go" class="btn btn-primary" value="Buscar" onclick="getresultInv('<?php echo $paginationlink . $page; ?>')">
			<!--<input type="reset" class="btn btn-primary" value="Reset" onclick="getresult('getresult.php');">-->
		</p>
	</div>
	<table class="table table-bordered table-striped" id="table-list2">
		<thead>
			<tr>
				<th class="text-center" style="width: 50px;">Sec.</th>
				<th class="text-center">Tipo de Artículo</th>
				<th class="text-center">Marca</th>
				<th class="text-center">Modelo/Base</th>
				<th class="text-center">SKU</th>
				<th class="text-center">Descripción</th>
				<th class="text-center">No. de Serie</th>
				<th class="text-center">Existencia</th>
				<th class="text-center" style="width: 100px;"></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if(!empty($result)) {
					foreach($result as $k=>$v) {
			?>
						<tr id="toy-<?php echo $result[$k]["sec"]; ?>">
							<td><?php echo $result[$k]["sec"]; ?></td>
							<td><?php echo $result[$k]["tipo_de_articulo"]; ?></td>
							<td><?php echo $result[$k]["marca"]; ?></td>
							<td><?php echo $result[$k]["modelo_base"]; ?></td>
							<td><?php echo $result[$k]["sku"]; ?></td>
							<td><?php echo $result[$k]["descripcion"]; ?></td>
							<td><?php echo $result[$k]["no_serie"]?></td>
							<td><?php echo $result[$k]["existencia"]?></td>
							<td>
								<div class="btn-group">
									<a href="edit_product.php?id=<?php echo (int)$result[$k]["sec"];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
										<i class="glyphicon glyphicon-pencil"></i>
									</a>
									<a href="delete_product.php?id=<?php echo (int)$result[$k]["sec"]?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Eliminar la Orden de Compra?');">
										<i class="glyphicon glyphicon-remove"></i>
									</a>
								</div>
							</td>
						</tr>
			<?php
					}
				}
				
				if(isset($perpageresult)) {
			?>
					<tr>
						<td colspan="9" align=right> <?php echo $perpageresult; ?></td>
					</tr>
			<?php } ?>
		<tbody>
	</table>
</form>