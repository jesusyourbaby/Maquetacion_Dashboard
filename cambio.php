<?php
session_start();
include 'conexion.php'; // Incluir la conexión a la base de datos
$error = '';

// Verificar si se ha enviado el formulario
if (isset($_POST['username']) || isset($_POST['email'])) {
    $user = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';

    // Lógica para buscar al usuario con el nombre o correo proporcionado
    if (!empty($user)) {
        // Buscar por nombre de usuario
        $sql = "SELECT * FROM permiso WHERE usuario='$user'";
    } else {
        // Buscar por correo electrónico (si lo usas en tu base de datos)
        $sql = "SELECT * FROM permiso WHERE email='$email'";
    }
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Usuario encontrado, mostrar mensaje de éxito o redirigir
        // Aquí podrías implementar un proceso para cambiar la contraseña o mostrar un formulario adicional
        header("Location: cambiar_contrasena.php");
        exit;
    } else {
        // Si no se encuentra el usuario
        $error = "Usuario o correo no encontrados";
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
    </style>
</head>
<body>
    <div class="login-box">
    <img src="./img/Logo_utm.png" class="avatar" alt="Avatar Image">
        <h1>Solicitud Credenciales</h1>
        <?php if (!empty($error)) : ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <form action="cambio.php" method="post">
            <!-- USERNAME OR EMAIL INPUT -->
            <div class="input-container">
                <i class="bi bi-person"></i>
                <input type="text" name="username" placeholder="Ingrese su Usuario" id="username">
                <input type="email" name="email" placeholder="O Ingrese su Correo" id="email">
            </div>
            <!-- SUBMIT BUTTON -->
            <div class="input-container">
                <input type="submit" value="Solicitar Cambio">
            </div>
        </form>
        <a>© 2024 Universidad Técnica de Manabí</a><br>
    </div>
</body>
</html>
