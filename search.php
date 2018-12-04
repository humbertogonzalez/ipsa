<?php
$connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
$connection->select_db(DB_NAME);
$sql = "SELECT descripcion FROM inventario ORDER BY sec";
$res = mysqli_query($connection, $sql);

$arreglo_php = array();

if(mysqli_num_rows($res)==0) {
    array_push($arreglo_php, "No hay datos");
} else {
    while($palabras = mysqli_fetch_array($res)) {
        array_push($arreglo_php, $palabras["descripcion"]);
    }
}
?>