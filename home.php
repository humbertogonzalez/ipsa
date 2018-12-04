<?php
  $page_title = 'Inicio - Administrador';
  require_once('includes/load.php');
  include_once('layouts/header.php');
  
  $c_categorie = count_by_id('categories');
  $c_product = count_by_id('products');
  $c_sale = count_by_id('sales');
  $c_user = count_by_id('users');
  $e_user = find_by_id('users',(int)$_SESSION['user_id']);
  
  if(isset($_POST['selEmpresa'])){
    $workingOn = remove_junk($db->escape($_POST['selEmpresa']));
    $id = $user['id'];
    $sql = "UPDATE users SET working_on='{$workingOn}' WHERE id='{$db->escape($id)}'";
    $result = $db->query($sql);
  }
?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
    <div class="panel-heading clearfix">
      <strong>
       <span class="glyphicon glyphicon-th"></span>
       <span>Bienvenido: <?php echo remove_junk(ucfirst($user['name'])) . " " . remove_junk(ucfirst($user['ap_paterno'])); ?></span>
      </strong>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel">
      <div class="jumbotron text-center">
        <!--<div class="weather-wrapper" align="center" style="min-height: 100px;">-->
          <img src="" class="weather-icon" alt="Weather Icon" />
          <table width="100%" style="text-align: center;" align="center">
            <thead>
              <td>Ciudad</td>
              <td>Temperatura</th>
              <td>Descripción</td>
              <td>Humedad</td>
              <td>Velocidad del viento</td>
              <td>Salida del Sol</td>
              <td>Puesta del Sol</td>
            </thead>
            <tbody>
              <td><span class="weather-place"></span></td>
              <td><span class="weather-temperature"></span> (<span class="weather-min-temperature"></span> - <span class="weather-max-temperature"></span>)</td>
              <td><span class="weather-description capitalize"></span></td>
              <td><span class="weather-humidity"></span></td>
              <td><span class="weather-wind-speed"></span></td>
              <td><span class="weather-sunrise"></span></td>
              <td><span class="weather-sunset"></span></td>
            </tbody>
          </table>
        <!--</div>-->
      </div>
      <script src="libs/js/openweather.js"></script>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <?php if($e_user["user_level"] != 4) { ?>
      <div class="panel">
        <div class="jumbotron text-center">
          <p>NOTIFICACIONES</p>
          <?php
            // BUSCAMOS REQUISICIONES PENDIENTES POR APROBAR
            $reqPendUsr = 'SELECT no_requisicion FROM requisiciones WHERE solicitante IN (SELECT username FROM users WHERE username LIKE "%' . $e_user["username"] . '%") AND status="2"';
            $_result = $db->query($reqPendUsr);
            $result = $db->fetch_assoc($_result);
            $reqs = "";
            $countReqs = 0;
            
            foreach($_result AS $res) {
              $reqs .= $res["no_requisicion"] . ", ";
              $countReqs += 1;
            }
            
            if($countReqs > 0) {
              echo '<div class="alert alert-danger">
                    Tiene ' . $countReqs . ' requisicion(es) pendiente(s) de Aprobar. (' . rtrim($reqs,", ") . ')
                  </div>';
            }
            
            // BUSCAMOS OC PENDIENTES POR APROBAR
            $reqOcUsr = 'SELECT oc FROM ordenes_de_compra WHERE solicitante IN (SELECT username FROM users WHERE username LIKE "%' . $e_user["username"] . '%") AND status="2"';
            $_result = $db->query($reqOcUsr);
            $result = $db->fetch_assoc($_result);
            $ocs = "";
            $countOcs = 0;
            
            foreach($_result AS $res) {
              $ocs .= $res["oc"] . ", ";
              $countOcs += 1;
            }
            
            if($countOcs > 0) {
              echo '<div class="alert alert-danger">
                    Tiene ' . $countOcs . ' órdenes(es) de compra pendiente(s) de Aprobar. (' . rtrim($ocs,", ") . ')
                  </div>';
            }
          ?>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
<div class="row">
  <div class="col-md-3">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <a href="inventarios.php"><i class="glyphicon glyphicon-th-large"></i></a>
      </div>
      <div class="panel-value pull-right">
        <p class="text-muted-home">Inventarios</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <a href="oc.php"><i class="glyphicon glyphicon-duplicate"></i></a>
      </div>
      <div class="panel-value pull-right">
        <p class="text-muted-home">Órdenes de Compra</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <a href="proveedores.php"><i class="glyphicon glyphicon-edit"></i></a>
      </div>
      <div class="panel-value pull-right">
        <p class="text-muted-home">Proveedores</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <a href="proyectos.php"><i class="glyphicon glyphicon-list-alt"></i></a>
      </div>
      <div class="panel-value pull-right">
        <p class="text-muted-home">Proyectos</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <a href="requisiciones.php"><i class="glyphicon glyphicon-duplicate"></i></a>
      </div>
      <div class="panel-value pull-right">
        <p class="text-muted-home">Requisiciones</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <a href="tesoreria.php"><i class="glyphicon glyphicon-usd"></i></a>
      </div>
      <div class="panel-value pull-right">
        <p class="text-muted-home">Tesorería</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="panel panel-box clearfix">
      <div class="panel-icon pull-left bg-green">
        <a href="users.php"><i class="glyphicon glyphicon-user"></i></a>
      </div>
      <div class="panel-value pull-right">
        <p class="text-muted-home">Usuarios</p>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel">
      <div class="jumbotron text-center">
        <p>TIPO DE CAMBIO</p>
        <?php
          $from_Currency = urlencode("USD");
          $to_Currency = urlencode("MXN");
          $encode_amount = 1;
          $get = file_get_contents("https://finance.google.com/bctzjpnsun/converter?a=$encode_amount&from=$from_Currency&to=$to_Currency");
          $get = explode("<span class=bld>",$get);
          $get = explode("</span>",$get[1]);
          $rate= preg_replace("/[^0-9\.]/", null, $get[0]);
          $converted_amount = $encode_amount*$rate;
          $data = array( 'rate' => $rate, 'converted_amount' =>$converted_amount, 'from_Currency' => strtoupper($from_Currency), 'to_Currency' => strtoupper($to_Currency));
          echo money_format("USD: %i", $data["rate"]) . " MXN<br>";
          $from_Currency = urlencode("EUR");
          $to_Currency = urlencode("MXN");
          $encode_amount = 1;
          $get = file_get_contents("https://finance.google.com/bctzjpnsun/converter?a=$encode_amount&from=$from_Currency&to=$to_Currency");
          $get = explode("<span class=bld>",$get);
          $get = explode("</span>",$get[1]);
          $rate= preg_replace("/[^0-9\.]/", null, $get[0]);
          $converted_amount = $encode_amount*$rate;
          $data = array( 'rate' => $rate, 'converted_amount' =>$converted_amount, 'from_Currency' => strtoupper($from_Currency), 'to_Currency' => strtoupper($to_Currency));
          echo money_format("EU: %i", $data["rate"]) . " MXN<br>";
          ?>
      </div>
    </div>
  </div>
</div>
<div class="row"></div>
<script>
		$(function() {

			$('.weather-temperature').openWeather({
				key: 'c9d49310f8023ee2617a7634de23c2aa',
				city: 'Ciudad de México',
				descriptionTarget: '.weather-description',
				windSpeedTarget: '.weather-wind-speed',
				minTemperatureTarget: '.weather-min-temperature',
				maxTemperatureTarget: '.weather-max-temperature',
				humidityTarget: '.weather-humidity',
				sunriseTarget: '.weather-sunrise',
				sunsetTarget: '.weather-sunset',
				placeTarget: '.weather-place',
				iconTarget: '.weather-icon',
				customIcons: '../libs/images/weather/',
				success: function(data) {
					// show weather
					$('.weather-wrapper').show();
					console.log(data);
				},
				error: function(data) {
					console.log(data.error);
					$('.weather-wrapper').remove();
				}
			});

		});

	</script>
<?php include_once('layouts/footer.php'); ?>