<?php
require_once('includes/load.php');

if(isset($_POST['entrada'])){
    $query = "SELECT e.id AS id,e.sec AS sec, e.material_servicio AS material_servicio, e.remision AS remision, e.cantidad AS cantidad, e.um AS um, inv.no_serie AS no_serie
            FROM entradas AS e
            LEFT JOIN inventario AS inv
            ON e.sec = inv.sec
            WHERE e.remision='" . $_POST['entrada'] . "';";

    $result = $db->query($query);

    if($db->fetch_assoc($result)) {
        $body = '<table class="table table-bordered table-striped">';
        $body .= '<thead>';
        $body .= '<th></th>';
        $body .= '<th>No. de Serie</th>';
        $body .= '<th>Descripción</th>';
        $body .= '<th>Remisión</th>';
        $body .= '<th>Cantidad Salida</th>';
        $body .= '<th>Acciones</th>';
        $body .= '</thead>';
        $body .= '<tbody>';
        
        foreach($result AS $res) {
            $body .= '<tr>';
            $body .= '<td><input type="checkbox"></td>';
            $body .= '<td id="NoSerie-' . $res ["sec"] . '">' . $res ["no_serie"] . '<input type="hidden" name="cart[' . $res ["id"] . '][no_serie]" value="' . $res ["no_serie"] . '"></td>';
            $body .= '<td id="MaterialServicio-' . $res ["sec"] . '">' . $res ["material_servicio"] . '<input type="hidden" name="cart[' . $res ["id"] . '][descripcion]" value="' . $res ["material_servicio"] . '"></td>';
            $body .= '<td>' . $res ["remision"] . '<input type="hidden" name="cart[' . $res ["id"] . '][remision]" value="' . $res ["remision"] . '"></td>';
            $body .= '<td><input type="text" name="cart[' . $res ["id"] . '][cantidad_salida]" placeholder="' . $res ["cantidad"] . '"></td>';
            $body .= '<td id="Sustituir-' . $res ["sec"] . '"><div style="cursor: pointer; text-decoration: underline;" onclick="Sustituir(' . $res ["sec"] . ', ' . $res ["id"] . ');">Sustituir</div></td>';
            $body .= '<input type="hidden" name="cart[' . $res ["id"] . '][sec]" value="' . $res ["sec"] . '">';
            $body .= '<input type="hidden" name="cart[' . $res ["id"] . '][um]" value="' . $res ["um"] . '">';
            $body .= '</tr>';
        }
        
        $body .= '</tbody>';
        $body .= '</table>';

        echo (json_encode (array (
            'status' => 'SUCCESS', 
            'message' => "Si recibí el parámetro",
            'body' => $body
        )));
    } else {
        echo (json_encode (array (
            'status' => 'NORESULT', 
            'message' => "<div id='noresult'>La Búsqueda no dio resultados</div>",
        )));
    }
} else {
    echo (json_encode (array (
        'status' => 'EMPTY', 
        'message' => "El parámetro de Entrada esta vacío",
    )));
}
?>