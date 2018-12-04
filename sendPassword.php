<?php
include_once('includes/load.php');
require 'libs/PHPMailer-master/PHPMailerAutoload.php';
$req_fields = array('email');
validate_fields($req_fields);
$email = remove_junk($_POST['email']);

// Validamos parámetros recibidos
if(isset($email)) {
    
    if(empty($errors)){
        $validateEmail = find_by_email($email);
        
        if($validateEmail) {
            $password = remove_junk($db->escape(generatePassword()));
            error_log("Password: " . $password, 3, "debug.log");
            $h_pass   = sha1($password);
            $sql = "UPDATE users SET password='{$h_pass}' WHERE email='{$email}'";
            $result = $db->query($sql);
            
            if($result && $db->affected_rows() === 1){
                $mail = new PHPMailer;
                //$mail->SMTPDebug = 4;
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
                $mail->addAddress($email);
                //$mail->addCC("daniel.lopez.al07@gmail.com", "Daniel López");
                // Set email format to HTML
                $mail->isHTML(true);
                $mail->Subject = 'Reestablecimiento de Contraseña';
                $mail->Body    = 'Su nueva contraseña es: <b>' . $password . "</b>.<br><br>Esta contraseña es temporal, deberá de cambiarla al momento de ingresar.";
                
                if(!$mail->send()) {
                    $session->msg('d','El correo con su nueva contraseña no pudo ser enviado: ' . $mail->ErrorInfo);
                    redirect('forgotpassword.php', false);
                } else {
                    $session->msg('s',"Contraseña enviada con éxito");
                    redirect('forgotpassword.php', false);
                }
            } else {
                $session->msg('d',' Ocurrió un error');
                redirect('forgotpassword.php', false);
            }
        } else {
            $session->msg('d','El correo electrónico: '. $email . ' no esta asociado a ninguna cuenta.');
            redirect('forgotpassword.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('forgotpassword.php',false);
    }
}
?>