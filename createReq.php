<?php
require_once('includes/load.php');
global $db;
$idReq = 3;
$empresa = 3;
$reqData = "SELECT * FROM requisiciones WHERE id='" . $idReq . "'";
echo $reqData;
$result = $db->query($reqData);
$result = $db->fetch_assoc($result);
$sol = "SELECT name, ap_paterno, ap_materno FROM users WHERE username='" . $result["solicitante"] . "';";
$resultSol = $db->query($sol);
$resultSol = $db->fetch_assoc($resultSol);
$solicitante = $resultSol["name"] . " " . $resultSol["ap_paterno"] . " " . $resultSol["ap_materno"];
$solicitante = rtrim($solicitante, " ");
$proyecto = find_by_id("proyectos",$result["proyecto"]);
$prov = find_by_id("proveedores",$result["proveedor"]);
$fecha_surtido = $result["fecha_surtido"];
$explodeDate = explode("-", $fecha_surtido);
$dia = $explodeDate[2];
$mes = $explodeDate[1];
$ano = $explodeDate[0];

// Datos para logo y dirección
$empresaData = "SELECT * FROM user_empresas WHERE id='" . $empresa . "'";
$resultEmpresa = $db->query($empresaData);
$resultEmpresa = $db->fetch_assoc($resultEmpresa);

// Armamos el Header
$html = '
    <html>
    <head>
        <style>
        #pTable {
            width: 100%;
        }
        .header1 {
            font-size: 12px;
            width: 25%;
        }
        .header2 {
            text-align: center;
            font-family: "Arial";
            font-weight: bold;
            color: #974706;
            width: 40%;
        }
        .header2 .c1{
            font-size: 15px;
            padding: 15px;
        }
        .header2 .c2{
            font-size: 9px;
        }
        .header2 .c3{
            font-size: 11px;
            padding: 15px;
            text-decoration: underline;
        }
        .header2 .c4{
            font-size: 11px;
            padding: 15px;
        }
        .header1 > img{
            width: 200px;
        }
        .header3 {
            border: black solid 2px;
            border-radius: 15%;
            font-size: 8px;
            text-align: center;
            vertical-align: top;
        }
        .header3 .c5{
            font-size: 8px;
            padding: 15px;
            font-weight: bold;
        }
        .header3 .c6{
            font-size: 10px;
            text-align: center;
            height: 35px;
        }
        table.tblArts, table.tblFooter, table.tblDatos, table.c6 {
            width: 100%;
            height: 30px;
            border-collapse: collapse;
        }
        table.tblFooter {
            font-family: "Arial";
            font-size: 10px;
            text-align: center;
            height: 100px;
        }
        td.tblDatos {
            font-family: "Arial";
            font-size: 10px;
            height: 50px;
        }
        td.tblFooter, td.tblDatos, td.c6 {
            border: 1px solid black;
            vertical-align: top;
        }
        table.tblArts, th.tblArts, td.tblArts{
            border: 1px solid black;
            font-family: "Arial";
            font-size: 10px;
        }
        .sp1 {
            font-size: 7px;
        }
        .cuadro1 {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            width: 75%;
        }
        .cuadro2 {
            border-right: 1px solid black;
        }
        .cuadro3 {
            width: 33%;
        }
        .cuadro4 {
            border-left: 1px solid black;
        }
        .cuadro5 {
            border-top: 1px solid black;
            width: 75%;
        }
        .cuadro6 {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            width: 100%;
        }
        td.tblArts {
            height: 20px;
        }
        </style>
    </head>
    <body>
        <table class="header" id="pTable">
        <tr>
            <td class="header1">
            <img src="libs/images/logos/'. $resultEmpresa["logo"] . '">
            </td>
            <td class="header2">
            <div class="c1">'. strtoupper($resultEmpresa["empresa"]) . '</div>
            <div class="c2">'. strtoupper($resultEmpresa["direccion"]) . '</div>
            <div class="c3">REQUISICION DE MATERIALES</div>
            <div class="c4">PARA COMPRA DE BIENES Ó CONTRATACIÓN DE SERVICIOS</div>
            </td>
            <td class="header3">
            <table class="c6">
                <tr>
                <td>PARA USO DEL SOLICITANTE</td>
                </tr>
            </table>
            <table class="c6">
                <tr>
                <td class="cuadro1 cuadro2" width="75%">REQUISICIÓN</td>
                <td class="cuadro1">HOJA</td>
                </tr>
                <tr>
                <td class="cuadro1 cuadro2" style="height: 30px; text-align: left;">ÁREA:</td>
                <td class="cuadro1"></td>
                </tr>
                <tr>
                <td class="cuadro1 cuadro2">FECHA</td>
                <td class="cuadro1">DE</td>
                </tr>
                <tr>
                <td class="cuadro5">
                    <table class="c6">
                    <tr>
                        <td class="cuadro3 cuadro2">DÍA</td>
                        <td class="cuadro3 cuadro2">MES</td>
                        <td class="cuadro3">AÑO</td>
                    </tr>
                    <tr>
                        <td class="cuadro3 cuadro2"><b>' . $dia . '</b></td>
                        <td class="cuadro3 cuadro2"><b>' . $mes . '</b></td>
                        <td class="cuadro3"><b>' . $ano . '</b></td>
                    </tr>
                    </table>
                </td>
                <td class="cuadro4"></td>
                </tr>
            </table>
            </td>
            <td class="header3">
            <table class="c6">
                <tr>
                <td>PARA USO EXCLUSIVO DE<br>RECURSOS MATERIALES</td>
                </tr>
            </table>
            <table class="c6">
                <tr>
                <td class="cuadro6">FOLIO DE RECEPCIÓN</td>
                </tr>
                <tr>
                <td class="cuadro6" style="height: 30px;"></td>
                </tr>
                <tr>
                <td class="cuadro6">FECHA</td>
                </tr>
                <tr>
                <td class="cuadro5">
                    <table class="c6">
                    <tr>
                        <td class="cuadro3 cuadro2">DÍA</td>
                        <td class="cuadro3 cuadro2">MES</td>
                        <td class="">AÑO</td>
                    </tr>
                    <tr>
                        <td class="cuadro3 cuadro2"><b>' . $dia . '</b></td>
                        <td class="cuadro3 cuadro2"><b>' . $mes . '</b></td>
                        <td class=""><b>' . $ano . '</b></td>
                    </tr>
                    </table>
                </td>
                </tr>
            </table>
            </td>
        </tr>
        </table>
        <br>
        <table class="tblDatos">
        <tr>
            <td class="tblDatos"><b>SOLICITANTE:<br><br>' . $solicitante . '</b></td>
            <td class="tblDatos"><b>PROYECTO / C.C.:<br><br>' . $proyecto["nombre"] . '</b></td>
            <td class="tblDatos"><b>PROVEEDOR SUGERIDO:<br><br>' . $prov["razon_social"] . '</b></td>
            <td class="tblDatos"><b>DEBE SURTIRSE EL:<br><br>' . $fecha_surtido . '</b></td>
        </tr>
        </table>
        <br>
        <table class="tblArts">
        <thead>
            <tr>
            <th class="tblArts">NUM.<br>CONS.</th>
            <th class="tblArts">DESCRIPCION DE LOS BIENES Y/O SERVICIOS<br><span class="sp1">DE SER NECESARIO ANEXAR MUESTRAS, CATALOGOS Ó MAYOR INFORMACION PARA IDENTIFICAR CLARAMENTE LOS BIENES Y/O SERVICIOS REQUERIDOS</span></th>
            <th class="tblArts">UNIDAD DE<br>MEDIDA</th>
            <th class="tblArts">CANTIDAD<br>SOLICITADA</th>
            <th class="tblArts">POR SURTIR</th>
            <th class="tblArts">FALTAN</th>
            <th class="tblArts">PARA USO DEL AREA DE ADQUSICIONES Y ALMACÉN</th>
            </tr>
        </thead>
        <tbody>';
        $artsReq = "SELECT * FROM articulos_requisiciones WHERE requisicion='" . $idReq . "'";
        $rs_result = $db->query($artsReq);
        $cons = 1;
        
        while($row = $rs_result->fetch_assoc()) {
            $html = $html . '<tr>
            <td class="tblArts">' . $cons . '</td>
            <td class="tblArts">' . $row["descripcion"] . '</td>
            <td class="tblArts">' . $row["unidad_medida"] . '</td>
            <td class="tblArts">' . $row["cantidad"] . '</td>
            <td class="tblArts">' . $row["surtir"] . '</td>
            <td class="tblArts">' . $row["faltan"] . '</td>
            <td class="tblArts"></td>
            </tr>';
            $cons = $cons + 1;
        }
        
        $html = $html . '
        </tbody>
        </table>
        <br>
        <table class="tblFooter">
        <tbody>
            <tr>
            <td class="tblFooter">SOLICITA:<br><br><br><br><br><br><b>NOMBRE/FECHA/FIRMA</b></td>
            <td class="tblFooter">AUTORIZO:<br><br><br><br><br><br><b>NOMBRE/FECHA/FIRMA</b></td>
            <td class="tblFooter">RECIBIÓ<br><br><br><br><br><br><b>NOMBRE/FECHA/FIRMA</b></td>
            </tr>
        </tbody>
        </table>
    </body>
    </html>';
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->set_paper('letter', 'landscape');
    $dompdf->render();
    $canvas = $dompdf->get_canvas();
    $canvas->page_text(5, 5, "Página {PAGE_NUM} de {PAGE_COUNT}", "Arial", 8, array(0, 0, 0));   
    $output = $dompdf->output();
    file_put_contents("docs/requisiciones/Req-" . $idReq . ".pdf", $output);
?>