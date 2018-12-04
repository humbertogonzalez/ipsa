<?php
    $page_title = 'Inicio - Administrador';
    require_once('includes/load.php');
    
    // Checkin What level user has permission to view this page
    //page_require_level(1);
    
    $empresas = find_all('user_empresas');

    include_once('layouts/header.php');
?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">
    <form action="admin.php" method="post" id="frmSelectEmpresa">
        <input type="hidden" name="selEmpresa" id="selEmpresa" value="">
        <?php
            if($user['empresas'] == "") {
        ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="jumbotron text-center">
                                <p>No esta asignado a ninguna Empresa</p>
                                <a href="logout.php">
                                    <i class="glyphicon glyphicon-off"></i>
                                    Iniciar Sesión con otro usuario.
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            } else {
        ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="jumbotron text-center">
                                <p>Seleccione la Empresa sobre la cual desea trabajar:</p>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
                foreach($empresas AS $empresa) {
                
                    if (strpos($user['empresas'], $empresa['id']) !== false) {
                        // Solo se debem mostrar las empresas para las cuales el usuario fue dado de alta
            ?>
                        <div class="col-md-4">
                            <div class="panel panel-box clearfix">
                                <div class="panel-icon pull-left bg-green">
                                    <img id="imgEnterprises-<?php echo $empresa['id']; ?>" src="libs/images/enterprise.png" style="width: 100%;" onclick="empres(<?php echo $empresa['id']; ?>);">
                                </div>
                                <div class="panel-value pull-right" style="padding: 30px;">
                                    <!--<p class="text-muted"><?php echo $empresa["empresa"]; ?></p>-->
                                    <img id="imgEnterprises-<?php echo $empresa['id']; ?>" src="libs/images/logos/<?php echo $empresa["logo"];?>" style="width: 100%; max-height: 75px;cursor: pointer" onclick="empres(<?php echo $empresa['id']; ?>);">
                                </div>
                            </div>
                        </div>
        <?php
                    }
                }
            }
        ?>
    </form>
</div>
<div class="row"></div>
<script>
    $( "#menu" ).remove();
    
    function empres(id) {
        $('#selEmpresa').val(id);
        $('#frmSelectEmpresa').submit();  
    }
</script>
<?php include_once('layouts/footer.php'); ?>