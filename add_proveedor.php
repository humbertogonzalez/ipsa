<?php
    $page_title = 'Alta de Proveedor';
    require_once('includes/load.php');

    // Checkin What level user has permission to view this page
    page_require_level("proveedores");
    $proveedores = find_all('proveedores');
    $e_user = find_by_id('users',(int)$_SESSION['user_id']);

    if(!$e_user){
        $session->msg("d","No existe el ID de Usuario.");
        redirect('add_proveedor.php');
    }

    if(isset($_POST['add_proveedor'])){
        //$req_fields = array('razon_social','rfc','direccion_1','colonia','delegacion_municipio','estado','cp','telefono','contacto','correo_contacto','telefono_contacto');
        $req_fields = array('razon_social','rfc','direccion_1','colonia','delegacion_municipio','estado','cp');
        validate_fields($req_fields);

        if(empty($errors)){
            $razon_social = remove_junk($db->escape($_POST['razon_social']));
            $rfc = remove_junk($db->escape($_POST['rfc']));
            $direccion_1 = remove_junk($db->escape($_POST['direccion_1']));
            $direccion_2 = remove_junk($db->escape($_POST['direccion_2']));
            $colonia = remove_junk($db->escape($_POST['colonia']));
            $delegacion_municipio = remove_junk($db->escape($_POST['delegacion_municipio']));
            $poblacion = remove_junk($db->escape($_POST['poblacion']));
            $estado = remove_junk($db->escape($_POST['estado']));
            $cp = remove_junk($db->escape($_POST['cp']));
            $telefono = remove_junk($db->escape($_POST['telefono']));
            $web = remove_junk($db->escape($_POST['web']));
            $contacto = remove_junk($db->escape($_POST['contacto']));
            $correo_contacto = remove_junk($db->escape($_POST['correo_contacto']));
            $telefono_contacto = remove_junk($db->escape($_POST['telefono_contacto']));
            $extension = remove_junk($db->escape($_POST['extension']));
            $find = "SELECT id FROM proveedores ORDER BY id DESC LIMIT 1;";
            $result = $db->query($find);
            $result = $db->fetch_assoc($result);
            $llave = "MV000" . ($result['id'] + 1);
            $query = "INSERT INTO proveedores (llave,razon_social,rfc,direccion_1,direccion_2,colonia,delegacion_municipio,poblacion,estado,cp,telefono,web,contacto,correo_contacto,telefono_contacto,extension,status)";
            $query .=" VALUES ('{$llave}','{$razon_social}','{$rfc}','{$direccion_1}','{$direccion_2}','{$colonia}','{$delegacion_municipio}','{$poblacion}','{$estado}','{$cp}','{$telefono}','{$web}','{$contacto}','{$correo_contacto}','{$telefono_contacto}','{$extension}','1')";
            
            if($db->query($query)){
                //sucess
                $session->msg('s',"Proveedor agregado exitosamente");
                redirect('add_proveedor.php', false);
            } else {
                //failed
                $session->msg('d','Lo sentimos, ocurrió un error agregando el proveedor');
                redirect('add_proveedor.php', false);
            }
        } else {
            $session->msg("d", $errors);
            redirect('add_proveedor.php',false);
        }
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
                    <span>Alta de Proveedor</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_proveedor.php" autocomplete="off">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="razon_social">Razón Social</label>
                            <input type="text" class="form-control" name="razon_social" value="">
                        </div>
                        <div class="form-group">
                            <label for="rfc">RFC</label>
                            <input type="text" class="form-control" name="rfc" value="">
                        </div>
                        <div class="form-group">
                            <label for="direccion_1">Dirección 1</label>
                            <input type="text" class="form-control" name="direccion_1" value="">
                        </div>
                        <div class="form-group">
                            <label for="direccion_2">Dirección 2</label>
                            <input type="text" class="form-control" name="direccion_2" value="">
                        </div>
                        <div class="form-group">
                            <label for="colonia">Colonia</label>
                            <input type="text" class="form-control" name="colonia" value="">
                        </div>
                        <div class="form-group">
                            <label for="delegacion_municipio">Delegación/Municipio</label>
                            <input type="text" class="form-control" name="delegacion_municipio" value="">
                        </div>
                        <div class="form-group">
                            <label for="poblacion">Población</label>
                            <input type="text" class="form-control" name="poblacion" value="">
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="" selected="selected">-- Seleccionar Estado --</option>
                                <option value="Aguascalientes">Aguascalientes</option>
                                <option value="Baja California">Baja California</option>
                                <option value="Baja California Sur">Baja California Sur</option>
                                <option value="Campeche">Campeche</option>
                                <option value="Chiapas">Chiapas</option>
                                <option value="Chihuahua">Chihuahua</option>
                                <option value="Ciudad de México">Ciudad de México</option>
                                <option value="Coahuila">Coahuila</option>
                                <option value="Colima">Colima</option>
                                <option value="Durango">Durango</option>
                                <option value="Guanajuato">Guanajuato</option>
                                <option value="Guerrero">Guerrero</option>
                                <option value="Hidalgo">Hidalgo</option>
                                <option value="Jalisco">Jalisco</option>
                                <option value="Estado de México">Estado de México</option>
                                <option value="Michoacán">Michoacán</option>
                                <option value="Morelos">Morelos</option>
                                <option value="Nayarit">Nayarit</option>
                                <option value="Nuevo León">Nuevo León</option>
                                <option value="Oaxaca">Oaxaca</option>
                                <option value="Puebla">Puebla</option>
                                <option value="Querétaro">Querétaro</option>
                                <option value="Quintana Roo">Quintana Roo</option>
                                <option value="San Luis Potosí">San Luis Potosí</option>
                                <option value="Sinaloa">Sinaloa</option>
                                <option value="Sonora">Sonora</option>
                                <option value="Tabasco">Tabasco</option>
                                <option value="Tamaulipas">Tamaulipas</option>
                                <option value="Tlaxcala">Tlaxcala</option>
                                <option value="Veracruz">Veracruz</option>
                                <option value="Yucatán">Yucatán</option>
                                <option value="Zacatecas">Zacatecas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cp">CP</label>
                            <input type="text" class="form-control" name="cp" value="">
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="">
                        </div>
                        <div class="form-group">
                            <label for="web">Web</label>
                            <input type="text" class="form-control" name="web" value="">
                        </div>
                        <div class="form-group">
                            <label for="contacto">Contacto</label>
                            <input type="text" class="form-control" name="contacto" value="">
                        </div>
                        <div class="form-group">
                            <label for="correo_contacto">Correo Contacto</label>
                            <input type="text" class="form-control" name="correo_contacto" value="">
                        </div>
                        <div class="form-group">
                            <label for="telefono_contacto">Teléfono Contacto</label>
                            <input type="text" class="form-control" name="telefono_contacto" value="">
                        </div>
                        <div class="form-group">
                            <label for="extension">Extensión</label>
                            <input type="text" class="form-control" name="extension" value="">
                        </div>
                        <div class="form-group clearfix">
                            <button type="submit" id="add_proveedor" name="add_proveedor" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
// ¿Estás seguro? Antes de terminar requisición
jQuery(function() {
    jQuery('#add_proveedor').click(function() {
        return window.confirm("¿Desea crear el Proveedor?");
    });
});
</script>
<?php include_once('layouts/footer.php'); ?>