<?php
// Incluir librerías de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer-master/src/PHPMailer.php';
require 'libs/PHPMailer-master/src/SMTP.php';
require 'libs/PHPMailer-master/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Crear una instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Servidor SMTP de Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'ivanxd247@gmail.com';  // Tu correo de Gmail
        $mail->Password = 'peyj dkfj wtqu etco';  // Contraseña de aplicación de Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Desactivar la verificación de certificado (solo como solución temporal)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Configurar el correo
        $mail->setFrom($email, $name);  // El correo que envía
        $mail->addAddress('ivanxd247@gmail.com');  // Tu correo de destino
        $mail->Subject = "Nuevo comentario de Feedback";
        $mail->Body = "Nombre: $name\nCorreo: $email\n\nComentario:\n$message";

        // Enviar el correo
        $mail->send();
        echo "Mensaje enviado con éxito";
    } catch (Exception $e) {
        echo "Hubo un error al enviar el mensaje: {$mail->ErrorInfo}";
    }
}
?>
