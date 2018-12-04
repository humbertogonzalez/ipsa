<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) {
    redirect('home.php', false);
  }
  include_once('layouts/headerHome.php');
?>
<div class="login-page">
  <div class="text-center">
    <img class="logoLogin" src="libs/images/LogoIPSA.png">
    <h2 class="form-signin-heading">Recuperar Contraseña</h1>
  </div>
  <?php echo display_msg($msg); ?>
  <form class="clearfix" action="sendPassword.php" method="post">
    <div class="form-group">
      <label for="username" class="control-label">Correo eletrónico</label>
  	  <input type="text" name="email" class="form-control" placeholder="Correo electrónico">
    </div>
    <!--<div class="form-group">
      <label for="username" class="control-label">Usuario</label>
  	  <input type="text" name="username" class="form-control" placeholder="Usuario">
    </div>-->
    <div class="form-group">
      <button onclick="toLogin();" type="button" class="btn btn-info"><< Volver</button>
      <button type="submit" class="btn btn-info  pull-right">Enviar</button>
    </div>
  </form>
</div>
<?php include_once('layouts/footer.php'); ?>
<script>
function toLogin() {
    window.location.href = "index.php";
}
</script>