<?php
require_once('includes/load.php');
if(isset($_POST['descripcion'])){
    $query = "SELECT no_serie FROM inventario WHERE descripcion LIKE '%" . $_POST['descripcion'] . "%' AND existencia > 0;";
    $result = $db->query($query);
    $return = "";
    
    if($db->num_rows($result)){
        if($db->fetch_assoc($result)) {
            $return = '<select class="form-control" name="cart[' . $_POST["id"] . '][no_serie]">';
            $return.= '<option value="">-- Seleccione un No. de Serie --</option>';
            
            foreach($result AS $res) {
                $return.= '<option value="' . $res["no_serie"] . '">' . $res["no_serie"] . '</option>';
            }
            
            $return .= '</select>';
            
            echo (json_encode (array (
                'status' => 'SUCCESS', 
                'message' => "Si recibí el parámetro",
                'body' => $return,
            )));
        }
    } else {
        echo (json_encode (array (
            'status' => 'NORESULT', 
            'message' => "<div id='noresult'>La Búsqueda no dio resultados</div>",
        )));
    }
} else {
    echo (json_encode (array (
        'status' => 'EMPTY', 
        'message' => "El parámetro de Requisición esta vacío",
    )));
}
?>