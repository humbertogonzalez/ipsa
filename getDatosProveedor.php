<?php
require_once('includes/load.php');
if(isset($_POST['proveedor'])){
    $query = "SELECT * FROM proveedores WHERE id = " . $_POST['proveedor'];
    $result = $db->query($query);
    if($db->num_rows($result)){
        if($db->fetch_assoc($result)) {
            foreach($result AS $res) {
                $rfc = $res["rfc"];
                $contacto = $res["contacto"];
                $telefono = $res["telefono"];
            }
            echo (json_encode (array (
                'status' => 'SUCCESS', 
                'message' => "Si recibí el parámetro",
                'rfc' => $rfc,
                'contacto' => $contacto,
                'telefono' => $telefono
            )));
        }
    } else {
        echo (json_encode (array (
            'status' => 'NORESULT', 
            'message' => "La Búsqueda no dio resultados",
        )));
    }
} else {
    echo (json_encode (array (
        'status' => 'EMPTY', 
        'message' => "El parámetro de Orden de Compra esta vacío",
    )));
}
?>