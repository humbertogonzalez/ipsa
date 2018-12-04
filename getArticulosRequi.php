<?php
require_once('includes/load.php');

if(isset($_POST['requisicion'])){
    $query = "SELECT ar.id AS id, ar.sec AS sec,ar.descripcion AS descripcion, ar.cantidad AS cantidad, ar.faltan AS faltan, r.proyecto AS proyecto, ar.requisicion AS requisicion, ar.unidad_medida AS medida, inv.no_serie AS no_serie, r.proyecto AS proyecto, ar.surtir AS surtir, ar.oc AS oc, inv.existencia AS existencia, ar.cantidad_entregada AS entregada
        FROM articulos_requisiciones AS ar
        INNER JOIN requisiciones AS r
        ON ar.requisicion=r.id
        LEFT JOIN inventario AS inv
        ON ar.sec = inv.sec
        WHERE ar.requisicion=" . $_POST['requisicion'] . " AND ar.cantidad_entregada < ar.cantidad;";

    $result = $db->query($query);
    $asignado_a = "";
    
    if($db->num_rows($result)){
        if($db->fetch_assoc($result)) {
            $body = '<table class="table table-bordered table-striped">';
            $body .= '<thead>';
            $body .= '<th></th>';
            $body .= '<th>No. de Serie</th>';
            $body .= '<th>Descripción</th>';
            $body .= '<th>Cantidad Salida</th>';
            $body .= '<th>Acciones</th>';
            $body .= '</thead>';
            $body .= '<tbody>';
            $proyecto = "";
            $var = 999999;
            
            foreach($result AS $res) {
                $proyecto = (int)$res['proyecto'];
                $body .= '<tr>';
                $body .= '<td><input type="checkbox" name="cart[' . $res ["id"] . '][check]"></td>';
                $body .= '<td id="NoSerie-' . $res ["sec"] . '"></td>';
                $body .= '<td id="MaterialServicio-' . $res ["sec"] . '">' . $res ["descripcion"] . '<input type="hidden" name="cart[' . $res ["id"] . '][descripcion]" value="' . $res ["descripcion"] . '"></td>';
                
                if($res ["no_serie"]) {
                    $body .= '<td><input type="text" name="cart[' . $res ["id"] . '][cantidad_salida]" value="1" placeholder="1"></td>';
                } else {
                    $body .= '<td><input type="text" name="cart[' . $res ["id"] . '][cantidad_salida]" value="' . $res["surtir"] . '" placeholder="' . $res["surtir"] . '"></td>';
                }
                
                $body .= '<td id="Sustituir-' . $res ["sec"] . '"><div style="cursor: pointer; text-decoration: underline;" onclick="Sustituir(' . $res ["sec"] . ', ' . $res ["id"] . ', &#39;' . $res["descripcion"] . '&#39;);">Sustituir</div></td>';
                $body .= '<input type="hidden" name="cart[' . $res ["id"] . '][sec]" value="' . $res ["sec"] . '">';    
                $body .= '<input type="hidden" name="cart[' . $res ["id"] . '][um]" value="' . $res ["medida"] . '">';
                $body .= '</tr>';
                
                if($res ["no_serie"] != "" && $res["surtir"] >= 2) {
                    for($i = 1; $i <= ($res["surtir"]-1); $i++) {
                        $var = $var + 1;
                        $body .= '<tr>';
                        $body .= '<td><input type="checkbox" name="cart[' . $var . '][check]"></td>';
                        $body .= '<td id="NoSerie-' . $res ["sec"] . '"></td>';
                        $body .= '<td id="MaterialServicio-' . $res ["sec"] . '">' . $res ["descripcion"] . '<input type="hidden" name="cart[' . $var . '][descripcion]" value="' . $res ["descripcion"] . '"></td>';
                        
                        if($res ["no_serie"]) {
                            $body .= '<td><input type="text" name="cart[' . $var . '][cantidad_salida]" value="1" placeholder="1"></td>';
                        } else {
                            $body .= '<td><input type="text" name="cart[' . $var . '][cantidad_salida]" value="' . $res["surtir"] . '" placeholder="' . $res["surtir"] . '"></td>';
                        }
                        
                        $body .= '<td id="Sustituir-' . $res ["sec"] . '"><div style="cursor: pointer; text-decoration: underline;" onclick="Sustituir(' . $res ["sec"] . ', ' . $var . ', &#39;' . $res["descripcion"] . '&#39;);">Sustituir</div></td>';
                        $body .= '<input type="hidden" name="cart[' . $var . '][sec]" value="' . $res ["sec"] . '">';    
                        $body .= '<input type="hidden" name="cart[' . $var . '][um]" value="' . $res ["medida"] . '">';
                        $body .= '</tr>';
                    }
                }
                
                /*if($res["surtir"] >= 1 && $res ["no_serie"] != "") {
                    $for = 0;
                    
                    if($res["faltan"] > 0) {
                        $for = $res["surtir"] - $res["entregada"];
                    } else {
                        $for = $res["cantidad"] - $res["entregada"];
                    }
                    
                    error_log("FOR > " . $for . "\n", 3, "debug.log");
                    
                    $querySec = "SELECT * FROM inventario WHERE descripcion LIKE '%" . $res ["descripcion"] . "%';";
                    $resultSec = $db->query($querySec);
                    
                    if($db->num_rows($resultSec)){
                        if($db->fetch_assoc($resultSec)) {
                            $cSec = 0;
                            
                            foreach($resultSec AS $rSec) {
                                if($cSec < ($for - 1)) {
                                    //for($i = 1; $i <= $for -1; $i++) {
                                    $body .= '<tr>';
                                    $body .= '<td>* <input type="checkbox" name="cart[' . $rSec ["sec"] . '][check]"></td>';
                                    //$body .= '<td id="NoSerie-' . $res ["sec"] . '">' . $res ["no_serie"] . '<input type="hidden" name="cart[' . $res ["sec"] . '][no_serie]" value="' . $res ["no_serie"] . '"></td>';
                                    $body .= '<td id="NoSerie-' . $res ["sec"] . '"></td>';
                                    $body .= '<td id="MaterialServicio-' . $res ["sec"] . '">' . $res ["descripcion"] . '<input type="hidden" name="cart[' . $rSec ["sec"] . '][descripcion]" value="' . $res ["descripcion"] . '"></td>';
                                    $body .= '<td><input type="text" name="cart[' . $rSec ["sec"] . '][cantidad_salida]" value="1" placeholder="1"></td>';
                                    $body .= '<td id="Sustituir-' . $res ["sec"] . '"><div style="cursor: pointer; text-decoration: underline;" onclick="Sustituir(' . $res ["sec"] . ', ' . $rSec ["sec"] . ', &#39;' . $res["descripcion"] . '&#39;);");">Sustituir</div></td>';
                                    $body .= '<input type="hidden" name="cart[' . $rSec ["sec"] . '][sec]" value="' . $res ["sec"] . '">';
                                    $body .= '<input type="hidden" name="cart[' . $rSec ["sec"] . '][um]" value="' . $res ["medida"] . '">';
                                    $body .= '<input type="hidden" name="cart[' . $rSec ["sec"] . '][hidden]" value="' . $res ["medida"] . '">';
                                    $body .= '</tr>';
                                    //}
                                }
                                $cSec = $cSec + 1;
                            }
                        }
                    }
                }*/
            }
            
            $body .= '</tbody>';
            $body .= '</table>';
            
            // Armamos select para devolver de Asignar A
            $proy = "SELECT id, nombre FROM proyectos;";
            $resultProy = $db->query($proy);
            $asignado_a .= '<select class="form-control" name="asignado_a" id="asignado_a">';
            
            while($row = $resultProy->fetch_assoc()) {
                if($proyecto == $row["id"]) {
                    $asignado_a .= '<option value="' . $row["nombre"] . '" selected="selected">' . $row["nombre"] . '</option>';
                } else {
                    $asignado_a .= '<option value="' . $row["nombre"] . '">' . $row["nombre"] . '</option>';
                }
            }
            
            $asignado_a .= '</select>';
            
            echo (json_encode (array (
                'status' => 'SUCCESS', 
                'message' => "Si recibí el parámetro",
                'body' => $body,
                'asignado' => $asignado_a
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