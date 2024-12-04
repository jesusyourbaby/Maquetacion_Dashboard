<?php
session_start();
include 'conexion.php'; // Conexión a la base de datos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer-master/src/PHPMailer.php';
require 'libs/PHPMailer-master/src/SMTP.php';
require 'libs/PHPMailer-master/src/Exception.php';

$error = ''; // Variable para almacenar errores
$success = ''; // Variable para almacenar el mensaje de éxito

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si los campos requeridos existen y no están vacíos
    $usuario = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($usuario) && empty($email)) {
        $error = "Por favor, ingresa un usuario o un correo válido.";
    } else {
        // Realizar la búsqueda del usuario o correo
        $sql = "SELECT * FROM permiso WHERE usuario = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usuario, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $email_to = $user_data['email'];

            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Actualizar token y expiración en la base de datos
            $update_sql = "UPDATE permiso SET reset_token = ?, token_expiry = ? WHERE usuario = ? OR email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssss", $token, $expiry, $usuario, $email);
            $update_stmt->execute();

            // Enviar el correo
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ivanxd247@gmail.com';
                $mail->Password = 'peyj dkfj wtqu etco'; // Contraseña de aplicación
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

                $mail->setFrom('ivanxd247@gmail.com', 'Soporte de Credenciales');
                $mail->addAddress($email_to);

                $mail->isHTML(true);
                $mail->Subject = 'Solicitud de cambio de contraseña';
                $mail->Body = "
                    Hola $usuario,<br><br>
                    Hemos recibido una solicitud para cambiar tu contraseña.<br>
                    Haz clic en el enlace siguiente para cambiar tu contraseña:<br>
                    <a href='http://localhost/Maquetacion_Dashboard/cambio.php?token=$token'>Cambiar contraseña</a><br><br>
                    Este enlace es válido por 1 hora.
                ";

                $mail->send();
                $success = "Solicitud enviada. Revisa tu correo para cambiar la contraseña.";
            } catch (Exception $e) {
                $error = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Usuario o correo no encontrado.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte de Credenciales</title>
    
    <style>
        /* Estilos similares a los del login.php */
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: 'Quicksand', sans-serif;
            height: 100vh;
            position: relative;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            width: 350px;
            height: 350px;
            background: url('./img/fondo.png') no-repeat bottom right;
            background-size: contain;
            opacity: 0.5;
        }

        /* Se agrega margen inferior solo al campo de correo */
        #email {
            margin-top: 15px;  /* Separar el campo de correo del de usuario */
        }

        .login-box {
            width: 320px;
            background: #003314;
            color: #fff;
            top: 50%;
            left: 50%;
            position: absolute;
            transform: translate(-50%, -50%);
            box-sizing: border-box;
            padding: 70px 30px;
            border-radius: 10px;
        }

        .login-box h1 {
            margin: 0;
            padding: 0 0 20px;
            text-align: center;
            font-size: 22px;
        }

        .login-box .input-container {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .login-box .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffffff;
        }

        .login-box .input-container input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            box-sizing: border-box;
            border: none;
            background: #008033;
            outline: none;
            height: 40px;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            text-align: center;
        }

        .login-box .input-container input::placeholder {
            color: #ffffff;
            text-align: center;
        }

        .login-box input[type="submit"] {
            border: none;
            outline: none;
            height: 40px;
            background: #008033;
            color: #fff;
            font-size: 18px;
            border-radius: 20px;
            width: 100%;
            margin-top: 10px;
        }

        .login-box input[type="submit"]:hover {
            cursor: pointer;
            background: #055223;
        }

        .login-box a {
            text-decoration: none;
            font-size: 12px;
            line-height: 20px;
            color: rgb(255, 255, 255);
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .login-box a:hover {
            color: #fff;
        }

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #004d1f inset !important;
            -webkit-text-fill-color: #fff !important;
        }
        .login-box .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: absolute;
            top: -50px;
            left: calc(50% - 50px);
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            color: white;
            font-size: 14px;
            text-align: center;
            margin-bottom: 20px;
        }
        
    </style>
    <!-- BOX ICONS -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="login-box">
    <img src="./img/Logo_utm.png" class="avatar" alt="Avatar Image">
        <h1>Solicitud Credenciales</h1>
        <?php if (!empty($error)) : ?>
            <div class="error"><?= $error; ?></div>
        <?php elseif (!empty($success)) : ?>
            <div class="success"><?= $success; ?></div>
        <?php endif; ?>
        <form action="" method="post"> <!-- Aquí no se hace redirección -->
            <!-- USERNAME OR EMAIL INPUT -->
            <div class="input-container">
                <i class="bi bi-person"></i>
                <input type="text" name="username" placeholder="Ingrese su Usuario" id="username">
            </div>

            <div class="input-container">
                <i class="bi bi-envelope-at" style="margin: 5px;"></i>
                <input type="email" name="email" placeholder="O Ingrese su Correo" id="email">
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="input-container">
                <i class="bi bi-send" style="margin: 5px;"></i>
                <input type="submit" value="Solicitar Cambio">
            </div>
            
            <div class="input-container">
                <a href="login.php" style="color: #ffffff; text-decoration: none; font-size: 16px; margin-left: 10px;">
                Cancelar
                </a><br>
            </div>


            <a>© 2024 Universidad Técnica de Manabí</a>
        </form>
    </div>
</body>
</html>
