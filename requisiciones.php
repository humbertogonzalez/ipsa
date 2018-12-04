<?php
    $page_title = 'Listado de Requisiciones';
    require_once('includes/load.php');

    // Revisión de permisos
    page_require_level("requisiciones");

    //$all_requisiciones = find_all('requisiciones');
    $all_proyectos = find_all('proyectos');
    $all_proveedores = find_all('proveedores');
    $empresas = find_all('user_empresas');
    $e_user = find_by_id('users',(int)$_SESSION['user_id']);

    // Empresas a las cuales esta asignado el usuario
    $userE = $e_user["empresas"];
    include_once('layouts/header.php');
    $requis = "SELECT * FROM requisiciones WHERE empresa = " . $e_user["working_on"] . ";";
    $requis_result = $db->query($requis);
?>
<script type="text/javascript" src="libs/js/tablefilter.js"></script>
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
                    <span>Requisiciones</span>
                </strong>
                <a href="add_requisicion.php" class="btn btn-info pull-right tablesorter">Alta de requisición</a>
            </div>
            <div class="panel-body">
                <table id="tblReq" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">ID</th>
                            <th class="text-center" data-placeholder="Select Empresa">Empresa</th>
                            <th class="text-center">Solicitante</th>
                            <th class="text-center" style="width: 15%;">Proyecto</th>
                            <th class="text-center" style="width: 10%;">Fecha de Creación</th>
                            <th class="text-center" style="width: 20%;">Debe surtirse el</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                            <th class="text-center" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //foreach($all_requisiciones as $a_requisicion):
                        while($row = $requis_result->fetch_assoc()) {
                            //if (strpos($userE, $a_requisicion["empresa"]) !== false) {
                        ?>
                                <tr>
                                    <td class="text-center"><?php echo $row["id"];?></td>
                                    <td class="text-center">
                                        <?php
                                            foreach($empresas AS $empresa) {
                                                if($empresa["id"] == $row['empresa']) {
                                                    echo remove_junk($empresa['empresa']);
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center"><?php echo remove_junk($row['solicitante']); ?></td>
                                    <td class="text-center">
                                        <?php
                                            foreach ($all_proyectos as $proyecto) {
                                                if($proyecto["id"] == $row['proyecto']) {
                                                    echo remove_junk($proyecto['nombre']);
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $row['fecha_creacion']; ?>
                                    </td>
                                    <td class="text-center"><?php echo $row['fecha_surtido']?></td>
                                    <td class="text-center">
                                        <?php
                                            $stats = requisicionStatus();
                                            echo $stats[$row['status']];
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="edit_requisicion.php?id=<?php echo $row['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Editar">
                                                <i class="glyphicon glyphicon-pencil"></i>
                                            </a>
                                            <a onclick="return confirm('¿Eliminar la Requisición?');" href="delete_requisicion.php?id=<?php echo $row['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Eliminar">
                                                <i class="glyphicon glyphicon-remove"></i>
                                            </a>
                                            <a href="" onclick="printExternal('http://localhost:8888/inventario/docs/requisiciones/Req-<?php echo $row['id'] ?>.pdf')" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Imprimir" onclick="return ConfirmDelete()">
                                            <!--<a href="" onclick="printExternal('http://ec2-34-216-42-75.us-west-2.compute.amazonaws.com/docs/requisiciones/Req-<?php echo $row['id'] ?>.pdf')" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Imprimir">-->
                                                <i class="glyphicon glyphicon-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php //} ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script language="javascript" type="text/javascript">  
    var filtersConfig = {
        base_path: 'inventario/',
        col_0: "none",
        col_1: 'select',
        col_2: 'select',
        col_3: 'select',
        col_4: 'select',
        col_5: 'none',
        col_6: 'select',
        col_7: 'none',
        col_types: [
            'string', 'string', 'number',
            'number', 'number', 'string',
            'string', 'number', 'number'
        ],
        /*custom_options: {
        cols:[5],
        texts: [[
        '0 - 25 000',
        '100 000 - 1 500 000'
        ]],
        values: [[
        '>2017-07-31 && <=25000',
        '>100000 && <=1500000'
        ]],
        sorts: [false]
        },*/
        col_widths: [
        '3%'
        ],
        default_date_type: 'YMD',
        extensions:[{ name: 'sort' }],
    };

    var tf = new TableFilter('tblReq', filtersConfig);
    var tfConfig = { enable_empty_option: true, display_all_text: "Display all", };
    tf.init();
</script>
<script>
    function printExternal(url) {
        var printWindow = window.open(url, "_blank", "toolbar=0,location=0,menubar=0");
        printWindow.addEventListener('load', function(){
        printWindow.print();
        }, true);
    }
</script>