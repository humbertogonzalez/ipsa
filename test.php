<?php
$datetime = DateTime::createFromFormat("Y-m-d H:i:s", "2018-11-27 22:29:17");
$x = $datetime->format(\DateTime::RFC3339);

print_r($x);

die();
require_once('includes/load.php');
require_once('libs/dompdf/dompdf_config.inc.php'); 
global $db;
$idOC = 14;
  $reqData = "SELECT * FROM ordenes_de_compra WHERE id='" . $idOC . "'";
  $result = $db->query($reqData);
  $result = $db->fetch_assoc($result);
  $fecha = $result["fecha"];
  $oc = $result["oc"];
  $proveedor = $result["proveedor"];
  // Obtenemos datos del proveedor
  $provData = "SELECT * FROM proveedores WHERE razon_social LIKE '%" . $proveedor . "%'";
  $resultProv = $db->query($provData);
  $resultProv = $db->fetch_assoc($resultProv);
  $direccion_1 = $resultProv["direccion_1"];
  $colonia = $resultProv["colonia"];
  $loc = $resultProv["delegacion_municipio"] . ", " . $resultProv["poblacion"] . ", " . $resultProv["estado"];
  $rfc = $result["rfc"];
  $contacto = $result["contacto"];
  $telefono_proveedor = $result["telefono_proveedor"];
  $correo_contacto = $resultProv["correo_contacto"];
  $sol = "SELECT name, ap_paterno, ap_materno FROM users WHERE username='" . $result["solicitante"] . "';";
  $resultSol = $db->query($sol);
  $resultSol = $db->fetch_assoc($resultSol);
  $solicitante = $resultSol["name"] . " " . $resultSol["ap_paterno"] . " " . $resultSol["ap_materno"];
  $solicitante = rtrim($solicitante, " ");
  $requisición = $result["requisicion"];
  $proyecto = $result["proyecto"];
  $proyData = "SELECT nombre FROM proyectos WHERE id='" . $proyecto . "'";
  $resultProy = $db->query($proyData);
  $resultProy = $db->fetch_assoc($resultProy);
  $_proyecto = $resultProy["nombre"];
  $sección = "";
  $entregar_en = $result["entregar_en"];
  $recibe = $result["recibe"];
  $telefono = $result["telefono"];
  $comentario = $result["comentario"];
  $cotizacion_proveedor = $result["cotizacion_proveedor"];
  $total = $result["total"];
  $subtotal_1 = $result["subtotal_1"];
  $descuento = $result["descuento"];
  $subtotal_2 = $result["subtotal_2"];
  $iva = $result["iva"];
  $condiciones_de_pago = $result["condiciones_de_pago"];
  
  // Obtenemos label de tiempo de pago
  $tiempo_pago = $result["tiempo_pago"];
  switch ($tiempo_pago) {
    case 1:
        $tiempo_pago = "7 días";
        break;
    case 2:
        $tiempo_pago = "15 días";
        break;
    case 3:
        $tiempo_pago = "30 días";
        break;
    case 4:
        $tiempo_pago = "45 días";
        break;
  }
  
  $tiempo_de_entrega = $result["tiempo_de_entrega"];
  $tm = "PESOS";
  $empresa = 3;
  // Datos para logo y dirección
  $empresaData = "SELECT * FROM user_empresas WHERE id='" . $empresa . "'";
  $resultEmpresa = $db->query($empresaData);
  $resultEmpresa = $db->fetch_assoc($resultEmpresa);
  
  if($tipo_moneda == "USD") {
    $tm = "DOLARES";
    $condiciones_de_pago .= $condiciones_de_pago . "<br>EL PAGO SE REALIZARÁ AL TIPO DE CAMBIO DEL DÍA DEL DIARIO OFICIAL";
  }

  //$obj = new NumeroALetras( $total , $tm, 'CENTAVOS');
  
  $html = '
  <html>
      <head>
          <style>
              #pTable {
                  width: 100%;
                  border-bottom: 2px solid black;
              }
              #pTable2 {
                  width: 100%;
              }
              #pTable2 .header1 {
                  width: 70%;
              }
              #pTable2 .header2 {
                  text-align: left;
              }
              #pTable2 .header2 .c1{
                  font-size: 10px;
                  padding: 0px;
                  padding-bottom: 10px;
              }
             #pTable2  .header2 .c2{
                  font-size: 10px;
              }
              .header1 {
                  font-size: 10px;
                  width: 20%;
              }
              .header2 {
                  text-align: center;
                  font-family: "Arial";
              }
              .header2 .c1{
                  font-size: 14px;
                  padding: 15px;
                  font-weight: bold;
              }
              .header2 .c2{
                  font-size: 10px;
              }
              .header2 .c3{
                  font-size: 10px;
                  padding: 15px;
              }
              .header2 .c4{
                  font-size: 10px;
                  padding: 15px;
              }
              .header1 > img{
                  width: 150px;
              }
              table.tblArts, table.tblFooter, table.tblDatos, table.c6, table.tblFooter2, table.tblFooter3 {
                  width: 100%;
              }
              table.tblFooter {
                  font-family: "Arial";
                  font-size: 10px;
                  text-align: center;
                  height: 100px;
              }
              table.tblFooter, td.tblFooter {
                  border: 1px solid black;
              }
              table.tblFooter2 {
                  font-family: "Arial";
                  font-size: 10px;
                  text-align: left;
                  height: 100px;
              }
              td.tblDatos {
                  font-family: "Arial";
                  font-size: 10px;
                  height: 50px;
                  width: 50%;
              }
              td.tblFooter, td.tblDatos, td.c6 {
                  vertical-align: middle;
              }
              table.tblArts, th.tblArts, td.tblArts{
                  border: 1px solid black;
                  font-family: "Arial";
                  font-size: 10px;
              }
              .sp1 {
                  font-size: 7px;
              }
              #pTable3 {
                  width: 100%;
                  font-size: 10px;
              }
              #pTable3 .hd1 {
                  width: 20%;
              }
              #pTable3 .fecha, #pTable3 .oc {
                  border: 2px solid black;
                  text-align: center;
              }
              #pTable3 .oc {
                  background-color: black;
                  color: white;
              }
              .inter {
                  height: 5px;
                  border-bottom: 2px solid black;
              }
              .data, .data2 {
                  height: 20px;
                  width: 100%;
                  font-size: 10px;
              }
              .subData1 {
                  width: 20%;
                  float: left;
              }
              table.tblArts, th.tblArts, td.tblArts {
                  border: 1px solid black;
              }
              .col1 {
                  width: 10%;
                  height: 20px;
              }
              .col2 {
                  width: 45%;
              }
              .col3 {
                  width: 15%;
              }
              .col4 {
                  width: 70%;
              }
              .col5 {
                  width: 30%;
              }
              .tblArts2 {
                  width: 100%;
                  font-family: "Arial";
                  font-size: 10px;
                  text-align: center;
              }
              .tblArts2 .col2 {
                  text-align: left;
              }
              table.tblFooter3 {
                  font-family: "Arial";
                  font-size: 10px;
                  text-align: center;
                  height: 100px;
              }
              .prov {
                font-size: 10px;
              }
          </style>
      </head>
      <body>
          <table id="pTable">
              <tr>
                  <td class="header1">
                      <img src="libs/images/logos/' . $resultEmpresa["logo"]. '">
                  </td>
                  <td class="header2">
                      <div class="c1">' . $resultEmpresa["empresa"] . '</div>
                      <div class="c2">' . $resultEmpresa["direccion"] . '</div>
                      <div class="c2">Teléfono: ' . $resultEmpresa["telefono"] . '</div>
                      <div class="c3">' . $resultEmpresa["rfc"] . '</div>
                  </td>
              </tr>
          </table>
          <div class="inter"></div>
          <table id="pTable2">
              <tr>
                  <td class="header1"></td>
                  <td class="header2">
                      <table id="pTable3">
                          <tr>
                              <td class="hd1">
                                  Fecha
                              </td>
                              <td class="fecha">
                                  ' . $fecha . '
                              </td>
                          </tr>
                          <tr>
                              <td class="hd1">
                                  OC
                              </td>
                              <td class="oc">
                                  ' . $oc . '
                              </td>
                          </tr>
                      </table>
                  </td>
              </tr>
          </table>
          <br>
          <table class="tblDatos">
              <tr>
                  <td class="tblDatos">
                      <div class="data"><b>Proveedor:</b></div>
                      <div class="data prov"><u>' . $proveedor . '</u><br></div>
                      <div class="data">' . $direccion_1 . '</div>
                      <div class="data">' . $colonia . '</div>
                      <div class="data">' . $loc . '</div>
                      <div class="data"><b>RFC:</b> ' . $rfc . '</div>
                      <div class="data"><b>Contacto:</b></div>
                      <div class="data"><u>' . $contacto . '</u></div>
                      <div class="data">Tel: ' . $telefono_proveedor . '</div>
                      <div class="data">Email: <u>' . $correo_contacto . '</u></div>
                  </td>
                  <td class="tblDatos">
                      <div class="data2">
                          <span class="subData1">Solicitante:</span>
                          <span>' . $solicitante . '</span>
                      </div>
                      <div class="data2">
                          <span class="subData1">ÁREA:</span>
                          <span>OPERACIONES</span><br>
                      </div>
                      <div class="data2">
                          <span class="subData1">Requisición:</span>
                          <span>' . $requisición . '</span>
                      </div>
                      <div class="data2">
                          <span class="subData1">Proyecto:</span>
                          <span>' . $_proyecto . '</span>
                      </div>
                      <div class="data2">
                          <span class="subData1">Sección:</span>
                          <span>VERACRUZ</span>
                      </div>
                      <div class="data2">
                          <span class="subData1">Entregar en:</span>
                          <span>' . $entregar_en . '</span>
                      </div>
                      <div class="data2"></div>
                      <div class="data2"></div>
                      <div class="data2">
                          <span class="subData1">Recibe:</span>
                          <span>' . $recibe . '</span>
                      </div>
                      <div class="data2">
                          <span class="subData1">Tel:
                          <span>' . $telefono . '</span>
                      </div>
                  </td>
              </tr>
          </table>
          <br>
          <table class="tblArts">
              <thead>
                  <tr>
                      <th class="tblArts col1">Partida</th>
                      <th class="tblArts col1">Cant.</th>
                      <th class="tblArts col1">U.</th>
                      <th class="tblArts col3">Código</th>
                      <th class="tblArts col2">Descripción</th>
                      <th class="tblArts col3">Precio Unitario</th>
                      <th class="tblArts col3">Importe</th>
                  </tr>
              </thead>
          </table>
          <table class="tblArts2">
            <tbody>';
              $artsOC = "SELECT * FROM articulos_ordenes_de_compra WHERE oc='" . $idOC . "'";
              $rs_result = $db->query($artsOC);
              while($row = $rs_result->fetch_assoc()) {
                $html = $html . '<tr>
                    <td class="col1">' . $row["partida"] . '</td>
                    <td class="col1">' . $row["cantidad"] . '</td>
                    <td class="col1">' . $row["unidades"] . '</td>
                    <td class="col3">' . $row["codigo"] . '</td>
                    <td class="col2">' . $row["descripcion"] . '<br>' . $row["comentario"] .'</td>
                    <td class="col3">' . $row["precio_unitario"] . '</td>
                    <td class="col3">' . $row["importe"] . '</td>
                </tr>';
              }
              $html = $html . '
            </tbody>
          </table>
          <br>
          <table class="tblFooter">
              <tbody>
                  <tr>
                      <td class="tblFooter col4" rowspan="2"><b>NOTA: ' . $comentario . '</b></td>
                      <td class="tblFooter col3">SUBTOTAL 1</td>
                      <td class="tblFooter col3">$ ' . $subtotal_1 . '</td>
                  </tr>
                  <tr>
                      <td class="tblFooter col3">DESCUENTO</td>
                      <td class="tblFooter col3">' . $descuento . '</td>
                  </tr>
                  <tr>
                      <td class="tblFooter col4">Esta OC fue elaborada con los datos indicados en su cotización: ' . $cotizacion_proveedor . '</td>
                      <td class="tblFooter col3">SUBTOTAL 2</td>
                      <td class="tblFooter col3">$ ' . $subtotal_2 . '</td>
                  </tr>
                  <tr>
                      <td class="tblFooter col4" rowspan="2">' . NumeroALetras::convertir($total, $tm, 'centavos') . '</td>
                      <td class="tblFooter col3">IVA</td>
                      <td class="tblFooter col3">$ ' . $iva . '</td>
                  </tr>
                  <tr>
                      <td class="tblFooter col3">TOTAL</td>
                      <td class="tblFooter col3">$ ' . $total . '</td>
                  </tr>
              </tbody>
          </table>
          <br>
          <table class="tblFooter2">
              <tbody>
                  <tr>
                      <td class="col5"><b>CONDICIONES DE PAGO:</b></td>
                      <td>' . $condiciones_de_pago . '</td>
                  </tr>
                  <tr>
                      <td class="col5"><b>TIEMPO DE PAGO:</b></td>
                      <td>' . $tiempo_pago . '</td>
                  </tr>
                  <tr>
                      <td class="col5"><b>TIEMPO DE ENTREGA:</b></td>
                      <td>' . $tiempo_de_entrega . '</td>
                  </tr>
                  <tr>
                      <td class="col5"><b>IMPORTANTE:</b></td>
                      <td>ENVIAR EVIDENCIA DE ENTREGA, FACTURA EN PDF Y XML POR CORREO ELECTRÓNICO<br>LAS FACTURAS ENTRAN A REVISIÓN LOS DÍAS MARTES.</td>
                  </tr>
              </tbody>
          </table>
          <br>
          <table class="tblFooter3">
              <tbody>
                  <tr>
                      <td class=""><br><b>________________________________<br>FABIOLA RUBIO<br>COMPRAS</b></td>
                      <td class=""><br><b>________________________________<br>LIC. HECTOR CAPETILLO<br>AUTORIZÓ</b></td>
                  </tr>
              </tbody>
          </table>
      </body>
  </html>';
  
  $dompdf = new DOMPDF();
  $dompdf->load_html($html);
  $dompdf->set_paper('letter', 'portrait');
  $dompdf->render();
  $canvas = $dompdf->get_canvas();
  $canvas->page_text(5, 5, "Página {PAGE_NUM} de {PAGE_COUNT}", "Arial", 8, array(0, 0, 0));  
  $output = $dompdf->output();
  file_put_contents("docs/oc/OC-" . $idOC . ".pdf", $output);
  ?>