<script type="text/javascript" src="libs/js/tablefilter.js"></script>
<?php
    $page_title = 'Módulo de Proyectos';
    require_once('includes/load.php');

    // Checkin What level user has permission to view this page
    page_require_level("proyectos");

    // pull out all user form database
    $all_proyectos = 'SELECT * FROM `proyectos`';
    $rs_result = $db->query($all_proyectos);
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
                    <span>Proyectos</span>
                </strong>
                <a href="add_proyecto.php" class="btn btn-info pull-right tablesorter">Alta de Proyecto</a>
            </div>
            <div class="panel-body">
                <table id="tblProyectos" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">ID</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Presupuesto</th>
                            <th class="text-center" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $rs_result->fetch_assoc()) { ?>
                            <tr>
                                <td class="text-center"><?php echo $row["id"];?></td>
                                <td class="text-center"><?php echo $row["codigo"]?></td>
                                <td class="text-center"><?php echo $row["nombre"]?></td>
                                <td class="text-center"><?php echo money_format("$ %i", $row["presupuesto_asignado"]);?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_proyecto.php?id=<?php echo (int)$row['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                        <a href="delete_proyecto.php?id=<?php echo (int)$row['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script language="javascript" type="text/javascript">  
var filtersConfig = {
    base_path: 'proyectos/',
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
    col_widths: [
        '100px', '100px', '100px',
        '100px', '100px', '100px',
        '100px', '100px', '100px'
    ],
    default_date_type: 'YMD',
    extensions:[{ name: 'sort' }],
};

var tf = new TableFilter('tblProyectos', filtersConfig);
var tfConfig = { enable_empty_option: true, display_all_text: "Display all", };
tf.init();
</script>