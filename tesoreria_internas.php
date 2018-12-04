<script type="text/javascript" src="libs/js/tablefilter.js"></script>
<?php
    $page_title = 'Módulo de Proyectos';
    require_once('includes/load.php');
    include_once('layouts/header.php');
    
    // Checkin What level user has permission to view this page
    page_require_level("tesoreria");

    // pull out all user form database
    $cuentas_pagar = "SELECT id,proveedor,nombre,fecha_pago,SUM(monto_pago) AS total,SUM(abono) AS abono,(SUM(monto_pago) - SUM(abono)) as saldo_restante,peridiocidad FROM tesoreria WHERE tipo_cuenta=0 GROUP BY proveedor ASC;";
    $rs_result = $db->query($cuentas_pagar);
    
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
                    <span>Cuentas por Pagar Internas</span>
                </strong>
            </div>
            <div class="panel-body">
                <table id="tblProyectos" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Proveedor</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Abono</th>
                            <th class="text-center">Saldo Restante</th>
                            <th class="text-center">Días de crédito</th>
                            <th class="text-center">Fecha de vencimiento</th>
                            <th class="text-center" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($row = $rs_result->fetch_assoc()) {
                            switch($row["peridiocidad"]){
                                case 1:
                                    $days = 7;
                                    break;
                                case 2:
                                    $days = 15;
                                    break;
                                case 3:
                                    $days = 30;
                                    break;
                                case 4:
                                    $days = 45;
                                    break;
                                default:
                                    $days = 7;
                                    break;
                            }
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $row["proveedor"]?></td>
                                <td class="text-center"><?php echo "$ " . $row["total"]?></td>
                                <td class="text-center"><?php echo "$ " . $row["abono"]?></td>
                                <td class="text-center"><?php echo "$ " . $row["saldo_restante"]?></td>
                                <td class="text-center"><?php echo $days?></td>
                                <td class="text-center"><?php echo $row["fecha_pago"]?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_cuenta_interna.php?id=<?php echo (int)$row['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                        <a href="delete_cuenta_interna.php?id=<?php echo (int)$row['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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