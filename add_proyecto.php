<?php
    $page_title = 'Alta de Proyecto';
    require_once('includes/load.php');

    // Checkin What level user has permission to view this page
    page_require_level("proyectos");
    $proveedores = find_all('proyectos');
    $e_user = find_by_id('users',(int)$_SESSION['user_id']);

    if(!$e_user){
        $session->msg("d","No existe el ID de Usuario.");
        redirect('add_proyecto.php');
    }

    if(isset($_POST['add_proyecto'])){
        $req_fields = array('nombre','codigo_ano','codigo_socio','codigo_cliente_final','codigo_fase','responsable','direccion','contacto','correo_contacto','telefono');
        validate_fields($req_fields);

        if(empty($errors)){
            $contrato = remove_junk($db->escape($_POST['contrato']));
            $fecha_inicio = remove_junk($db->escape($_POST['fecha_inicio']));
            $fecha_fin = remove_junk($db->escape($_POST['fecha_fin']));
            $nombre = remove_junk($db->escape($_POST['nombre']));
            $codigo_ano = remove_junk($db->escape($_POST['codigo_ano']));
            $codigo_socio = remove_junk($db->escape($_POST['codigo_socio']));
            $codigo_cliente_final = remove_junk($db->escape($_POST['codigo_cliente_final']));
            $codigo_fase = remove_junk($db->escape($_POST['codigo_fase']));
            $codigo = $codigo_ano . $codigo_socio . $codigo_cliente_final . $codigo_fase;
            $responsable = remove_junk($db->escape($_POST['responsable']));
            $presupuesto_asignado = remove_junk($db->escape($_POST['presupuesto_asignado']));
            $direccion = remove_junk($db->escape($_POST['direccion']));
            $contacto = remove_junk($db->escape($_POST['contacto']));
            $correo_contacto = remove_junk($db->escape($_POST['correo_contacto']));
            $telefono = remove_junk($db->escape($_POST['telefono']));
            $query = "INSERT INTO `proyectos`(`contrato`,`fecha_inicio`,`fecha_fin`,`nombre`, `codigo_ano`,`codigo_socio`,`codigo_cliente_final`,`codigo_fase`,`codigo`, `responsable`, `presupuesto_asignado`, `direccion`, `contacto`, `correo_contacto`, `telefono`) VALUES (";
            $query .="'{$contrato}','{$fecha_inicio}','{$fecha_fin}','{$nombre}','{$codigo_ano}','{$codigo_socio}','{$codigo_cliente_final}','{$codigo_fase}','{$codigo}','{$responsable}','{$presupuesto_asignado}','{$direccion}','{$contacto}','{$correo_contacto}','{$telefono}')";
            
            if($db->query($query)){
                //sucess
                $session->msg('s',"Proyecto agregado exitosamente");
                redirect('proyectos.php', false);
            } else {
                //failed
                $session->msg('d','Lo sentimos, ocurrió un error agregando el proyecto');
                redirect('add_proyecto.php', false);
            }
        } else {
            $session->msg("d", $errors);
            redirect('proyectos.php',false);
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
                    <span>Alta de Proyecto</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_proyecto.php" autocomplete="off">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Contrato</label>
                            <input type="text" class="form-control" name="contrato" value="">
                        </div>
                        <div class="form-group">
                            <label for="nombre">Fecha de Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" value="">
                        </div>
                        <div class="form-group">
                            <label for="nombre">Fecha fin</label>
                            <input type="date" class="form-control" name="fecha_fin" value="">
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" name="nombre" value="">
                        </div>
                        <div class="form-group">
                            <label for="codigo">Código</label>
                           <!-- <input readonly="readonly" type="text" class="form-control" name="codigo_ano" value="<?php echo date("Y");?>"><br>-->
                           <select class="form-control" name="codigo_ano">
                                <option value="">-- Seleccione el Año --</option>
                                <option value="2018">2018</option>
                                <option value="2017">2017</option>
                                <option value="2016">2016</option>
                            </select><br>
                            <select class="form-control" name="codigo_socio">
                                <option value="">-- Seleccione Socio de Negocio --</option>
                                <option value="00">00-Ninguno</option>
                                <option value="01">01-Axtel</option>
                                <option value="02">02-ICOSA</option>
                                <option value="03">03-Servicios Axtel</option>
                                <option value="04">04-UTMCA</option>
                                <option value="05">05-CENTLA</option>
                                <option value="06">06-KIO</option>
                                <option value="07">07-TRUSTWAVE</option>
                            </select><br>
                            <select class="form-control" name="codigo_cliente_final">
                                <option value="">-- Seleccione Socio Final --</option>
                                <option value="00">00-Ninguno</option>
                                <option value="01">01-SEDATU</option>
                                <option value="02">02-SEDESOL</option>
                                <option value="03">03-C5 DURANGO</option>
                                <option value="04">04-C4 GOMEZ PALACIO</option>
                                <option value="05">05-C4 SAN PEDRO</option>
                                <option value="06">06-IECDMX</option>
                                <option value="07">07-SICODES</option>
                            </select><br>
                            <input type="number" min="1" max="999" class="form-control" name="codigo_fase" value="" placeholder="001 a 999">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="responsable">Responsable</label>
                            <input type="text" class="form-control" name="responsable" value="">
                        </div>
                        <div class="form-group">
                            <label for="presupuesto_asignado">Presupuesto Asignado</label>
                            <input type="text" class="form-control" name="presupuesto_asignado" value="">
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" name="direccion" value="">
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
                            <label for="telefono">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="">
                        </div>
                        <div class="form-group clearfix">
                            <button type="submit" id="add_proyecto" name="add_proyecto" class="btn btn-primary">Guardar</button>
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
    jQuery('#add_proyecto').click(function() {
        return window.confirm("¿Desea crear el Proyecto?");
    });
});
</script>
<?php include_once('layouts/footer.php'); ?>