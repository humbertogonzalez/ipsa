<?php
require_once('includes/load.php');

if(isset($_POST['orden_compra'])){
    $query = "SELECT aodc.partida AS partida, aodc.cantidad AS cantidad, aodc.cantidad_entrada AS cantidad_entrada, inv.modelo_base AS codigo, aodc.descripcion AS material_servicio, aodc.precio_unitario AS precio_unitario,
                aodc.importe AS importe, inv.no_serie AS no_serie, aodc.oc AS orden_de_compra, inv.marca AS marca,
                aodc.unidades AS um, inv.caja AS caja, inv.ubicacion AS ubicacion, aodc.sec AS sec, inv.tipo_de_articulo AS tipo_de_articulo
                FROM articulos_ordenes_de_compra AS aodc
                LEFT JOIN inventario AS inv
                ON aodc.sec = inv.sec
                WHERE aodc.oc = '" . $_POST['orden_compra'] . "' AND aodc.cantidad_entrada < aodc.cantidad;";

    $result = $db->query($query);
    
    if($db->fetch_assoc($result)) {
        $body = '<table class="table table-bordered table-striped">';
        $body .= '<thead>';
        $body .= '<th></th>';
        $body .= '<th>Partida</th>';
        $body .= '<th>Cantidad</th>';
        $body .= '<th>Cantidad Entrada</th>';
        $body .= '<th>Unidades</th>';
        $body .= '<th>Codigo</th>';
        $body .= '<th>Descripción</th>';
        $body .= '<th>No. de Serie</th>';
        $body .= '<th>Precio Unitario</th>';
        $body .= '<th>Importe</th>';
        $body .= '</thead>';
        $body .= '<tbody>';
        
        foreach($result AS $res) {
            $body .= '<tr>';
            $body .= '<td><input type="checkbox" name="cart[' . $res ["partida"] . '][check]"></td>';
            $body .= '<td>' . $res ["partida"] . '</td>';
            if($res["no_serie"]) {
                $body .= '<td><input type="text" id="product-' . $res["partida"] . '" name="cart[' . $res ["partida"] . '][cantidad]" value="1" placeholder="' . $res ["cantidad"] . '"></td>';
            } else {
                $body .= '<td><input type="text" id="product-' . $res["partida"] . '" name="cart[' . $res ["partida"] . '][cantidad]" value="' . $res ["cantidad"] . '" placeholder="' . $res ["cantidad"] . '"></td>';
            }
            
            $body .= '<td>' . $res['cantidad_entrada'] . '</td>';
            $body .= '<td>' . $res ["um"] . '<input type="hidden" name="cart[' . $res ["partida"] . '][um]" value="' . $res ["um"] . '"></td>';
            $body .= '<td>' . $res ["codigo"] . '<input type="hidden" name="cart[' . $res ["partida"] . '][codigo]" value="' . $res ["codigo"] . '"></td>';
            $body .= '<td>' . $res ["material_servicio"] . '<input type="hidden" name="cart[' . $res ["partida"] . '][material_servicio]" value="' . $res ["material_servicio"] . '"></td>';
            $body .= '<td><input type="text" name="cart[' . $res ["partida"] . '][no_serie]" value=""></td>';
            $body .= '<td>' . $res ["precio_unitario"] . '<input type="hidden" name="cart[' . $res ["partida"] . '][precio_unitario]" value="' . $res ["precio_unitario"] . '"></td>';
            $body .= '<td>' . $res ["importe"] . '<input type="hidden" name="cart[' . $res ["partida"] . '][importe]" value="' . $res ["importe"] . '"></td>';
            $body .= '<input type="hidden" name="cart[' . $res ["partida"] . '][sec]" value="' . $res ["sec"] . '">';
            $body .= '<input type="hidden" name="cart[' . $res ["partida"] . '][tipo_de_articulo]" value="' . $res ["tipo_de_articulo"] . '">';
            $body .= '<input type="hidden" name="cart[' . $res ["partida"] . '][caja]" value="' . $res ["caja"] . '">';
            $body .= '<input type="hidden" name="cart[' . $res ["partida"] . '][ubicacion]" value="' . $res ["ubicacion"] . '">';
            $body .= '<input type="hidden" name="cart[' . $res ["partida"] . '][marca]" value="' . $res ["marca"] . '">';
            $body .= '</tr>';
            
            if($res["no_serie"] && ($res["cantidad"] - $res["cantidad_entrada"]) >= 2) {
                $mostrar = $res["cantidad"] - $res["cantidad_entrada"];
                
                for($i = 1; $i <= ($mostrar-1); $i++) {
                    $body .= '<tr>';
                    $body .= '<td><input type="checkbox" name="cart[' . $res ["sec"] . '][check]"></td>';
                    $body .= '<td>' . $res ["partida"] . '</td>';
                    
                    if($res["no_serie"]) {
                        $body .= '<td><input type="text" id="product-' . $res["sec"] . '" name="cart[' . $res ["sec"] . '][cantidad]" value="1" placeholder="' . $res ["cantidad"] . '"></td>';
                    } else {
                        $body .= '<td><input type="text" id="product-' . $res["sec"] . '" name="cart[' . $res ["sec"] . '][cantidad]" value="' . $res ["cantidad"] . '" placeholder="' . $res ["cantidad"] . '"></td>';
                    }
                    
                    $body .= '<td>' . $res['cantidad_entrada'] . '</td>';
                    $body .= '<td>' . $res ["um"] . '<input type="hidden" name="cart[' . $res ["sec"] . '][um]" value="' . $res ["um"] . '"></td>';
                    $body .= '<td>' . $res ["codigo"] . '<input type="hidden" name="cart[' . $res ["sec"] . '][codigo]" value="' . $res ["codigo"] . '"></td>';
                    $body .= '<td>' . $res ["material_servicio"] . '<input type="hidden" name="cart[' . $res ["sec"] . '][material_servicio]" value="' . $res ["material_servicio"] . '"></td>';
                    $body .= '<td><input type="text" name="cart[' . $res ["sec"] . '][no_serie]"></td>';
                    $body .= '<td>' . $res ["precio_unitario"] . '<input type="hidden" name="cart[' . $res ["sec"] . '][precio_unitario]" value="' . $res ["precio_unitario"] . '"></td>';
                    $body .= '<td>' . $res ["importe"] . '<input type="hidden" name="cart[' . $res ["sec"] . '][importe]" value="' . $res ["importe"] . '"></td>';
                    $body .= '<input type="hidden" name="cart[' . $res ["sec"] . '][sec]" value="' . $res ["sec"] . '">';
                    $body .= '<input type="hidden" name="cart[' . $res ["sec"] . '][tipo_de_articulo]" value="' . $res ["tipo_de_articulo"] . '">';
                    $body .= '<input type="hidden" name="cart[' . $res ["sec"] . '][caja]" value="' . $res ["caja"] . '">';
                    $body .= '<input type="hidden" name="cart[' . $res ["sec"] . '][ubicacion]" value="' . $res ["ubicacion"] . '">';
                    $body .= '<input type="hidden" name="cart[' . $res ["sec"] . '][marca]" value="' . $res ["marca"] . '">';
                    $body .= '</tr>';
                }
            }
            /*$multiDescQ = 'SELECT sec, no_serie FROM inventario WHERE descripcion LIKE "%' . $res["material_servicio"] . '%"';
            $resMulti = $db->query($multiDescQ);
            $count = $db->num_rows($resMulti);
            
            if($count > 1 && $res["cantidad"] > 1) {
                $cT = 0;
                foreach ($resMulti AS $resMulti_) {
                    if($cT < $res["cantidad"]-1){
                        if($res ["no_serie"] != $resMulti_ ["no_serie"]) {
                            $body .= '<tr>';
                            $body .= '<td><input type="checkbox" name="cart[' . $resMulti_ ["sec"] . '][check]"></td>';
                            $body .= '<td>' . $res ["partida"] . '</td>';
                            $body .= '<td><input type="text" id="product-' . $resMulti_["sec"] . '" name="cart[' . $resMulti_ ["sec"] . '][cantidad]" value="1" placeholder="' . $res ["cantidad"] . '"></td>';
                            $body .= '<td>' . $res['cantidad_entrada'] . '</td>';
                            $body .= '<td>' . $res ["um"] . '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][um]" value="' . $res ["um"] . '"></td>';
                            $body .= '<td>' . $res ["codigo"] . '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][codigo]" value="' . $res ["codigo"] . '"></td>';
                            $body .= '<td>' . $res ["material_servicio"] . '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][material_servicio]" value="' . $res ["material_servicio"] . '"></td>';
                            $body .= '<td><input type="text" name="cart[' . $resMulti_ ["sec"] . '][no_serie]"></td>';
                            $body .= '<td>' . $res ["precio_unitario"] . '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][precio_unitario]" value="' . $res ["precio_unitario"] . '"></td>';
                            $body .= '<td>' . $res ["importe"] . '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][importe]" value="' . $res ["importe"] . '"></td>';
                            $body .= '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][sec]" value="' . $resMulti_ ["sec"] . '">';
                            $body .= '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][tipo_de_articulo]" value="' . $res ["tipo_de_articulo"] . '">';
                            $body .= '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][caja]" value="' . $res ["caja"] . '">';
                            $body .= '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][ubicacion]" value="' . $res ["ubicacion"] . '">';
                            $body .= '<input type="hidden" name="cart[' . $resMulti_ ["sec"] . '][marca]" value="' . $res ["marca"] . '">';
                            $body .= '</tr>';
                            $cT = $cT + 1;
                        }
                    }
                }
            }*/
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
            'status' => 'EMPTY', 
            'message' => "No se encontró la orden de compra",
            'body' => $body
        )));
    }
} else {
    echo (json_encode (array (
        'status' => 'NORESULT', 
        'message' => "<div id='noresult'>La Búsqueda no dio resultados</div>",
    )));
}
?>