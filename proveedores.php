<script type="text/javascript" src="libs/js/tablefilter.js"></script>
<?php
    $page_title = 'Módulo de Proveedores';
    require_once('includes/load.php');

    // Checkin What level user has permission to view this page
    page_require_level("proveedores");

    // pull out all user form database
    $all_proveedores = find_all('proveedores');
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
                    <span>Proveedores</span>
                </strong>
                <a href="add_proveedor.php" class="btn btn-info pull-right tablesorter">Alta de Proveedor</a>
            </div>
            <div class="panel-body">
                <table id="tblProveedores" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">ID</th>
                            <th class="text-center">Razón Social</th>
                            <th class="text-center">Contacto</th>
                            <th class="text-center">Correo Electrónico</th>
                            <th class="text-center">Teléfono</th>
                            <th class="text-center" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($all_proveedores as $proveedores): ?>
                            <tr>
                                <td class="text-center"><?php echo (int)$proveedores["id"];?></td>
                                <td class="text-center"><?php echo remove_junk($proveedores["razon_social"])?></td>
                                <td class="text-center"><?php echo remove_junk($proveedores["contacto"])?></td>
                                <td class="text-center"><?php echo remove_junk($proveedores["correo_contacto"])?></td>
                                <td class="text-center"><?php echo $proveedores["telefono"]?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_proveedor.php?id=<?php echo (int)$proveedores['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                        <a href="delete_proveedor.php?id=<?php echo (int)$proveedores['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script language="javascript" type="text/javascript">  
var filtersConfig = {
    base_path: 'proveedores/',
    col_0: "none",
    col_1: 'select',
    col_2: 'select',
    col_3: 'none',
    col_4: 'none',
    col_5: 'none',
    col_6: 'none',
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

var tf = new TableFilter('tblProveedores', filtersConfig);
var tfConfig = { enable_empty_option: true, display_all_text: "Display all", };
tf.init();
</script>