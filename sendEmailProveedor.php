<?php
require_once('includes/load.php');
require 'libs/PHPMailer-master/PHPMailerAutoload.php';

if(isset($_POST['email'])){
    $mail = new PHPMailer;
    $mail->CharSet = "UTF-8";
    // Set mailer to use SMTP
    $mail->isSMTP();
    // Specify main and backup SMTP servers
    $mail->Host = 'smtp.gmail.com';
    // Enable SMTP authentication
    $mail->SMTPAuth = true;
    // SMTP username
    $mail->Username = 'beto.gzz.10@gmail.com';
    // SMTP password
    $mail->Password = 'Hgonzalez11!';
    // Enable TLS encryption, `ssl` also accepted
    $mail->SMTPSecure = 'tls';
    // TCP port to connect to
    $mail->Port = 587;
    $mail->setFrom('beto.gzz.10@gmail.com', "Administración Grupo Ipsa");
    // Add a recipient
    //$mail->addAddress($_POST['email']);
    if($_POST['tipo'] == "interno") {
        $mail->addCC("comprasti@ipsacv.mx", "Compras TI");
    } else {
        $mail->addCC("daniel.lopez.al07@gmail.com", "Daniel López");
        //$mail->addCC("$_POST['email']", "Daniel López");
    }
    
    // Set email format to HTML
    if($_POST['orden_compra']) {
        $mail->addAttachment('docs/oc/OC-' . $_POST['orden_compra'] . '.pdf', 'OC' . $_POST['orden_compra'] . '.pdf');
    }
    
    $mail->isHTML(true);
    $mail->Subject = 'Orden de Compra # ' . $_POST['orden_compra'] . '';
    $mail->Body    = 'Adjunto Orden de Compra';
    
    if($mail->send()) {
        echo (json_encode (array (
            'status' => 'SUCCESS', 
            'message' => "Correo enviado"
        )));
    } else {
        echo (json_encode (array (
            'status' => 'ERROR', 
            'message' => "El correo no pudo ser enviado",
        )));
    }
} else {
    echo (json_encode (array (
        'status' => 'EMPTY', 
        'message' => "El parámetro de Correo Electrónico esta vacío",
    )));
}
?>