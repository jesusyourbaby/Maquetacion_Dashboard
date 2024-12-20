<?php
session_start();
include 'conexion.php'; // Incluir la conexión a la base de datos

// Inicializar variables para manejar mensajes y datos
$error = '';

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validar que el nombre de usuario y la contraseña no estén vacíos
    if (!empty($username) && !empty($password)) {
        // Consultar la base de datos para verificar las credenciales
        $sql = "SELECT * FROM usuarios_crud WHERE usuario='$username' AND contrasena='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Crear una sesión y redirigir al usuario a `crud.php`
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: crud.php");
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}

$conn->close(); // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <style>
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

        .register-box {
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

        .register-box .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: absolute;
            top: -50px;
            left: calc(50% - 50px);
        }

        .register-box h1 {
            margin: 0;
            padding: 0 0 20px;
            text-align: center;
            font-size: 22px;
        }

        .register-box .input-container {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .register-box .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffffff;
        }

        .register-box .input-container input {
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

        .register-box .input-container input::placeholder {
            color: #ffffff;
            text-align: center;
        }

        .register-box input[type="submit"], .register-box input[type="button"] {
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

        .register-box input[type="submit"]:hover, .register-box input[type="button"]:hover {
            cursor: pointer;
            background: #055223;
            color: #ffffff;
        }

        .register-box a {
            text-decoration: none;
            font-size: 12px;
            line-height: 20px;
            color: rgb(255, 255, 255);
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .register-box a:hover {
            color: #fff;
        }

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #004d1f inset !important;
            -webkit-text-fill-color: #fff !important;
        }

        input:-webkit-autofill::first-line {
            font-family: 'Quicksand', sans-serif;
            font-size: 16px;
            color: #fff !important;
        }
    </style>
    <!-- BOX ICONS -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="register-body">
    <div class="register-box">
        <img src="./img/Logo_utm.png" class="avatar" alt="Avatar Image">
        <h1>Login Administrador</h1>
        <?php if (!empty($error)) : ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <form class="register-form" action="register.php" method="post">
            <!-- USERNAME INPUT -->
            <div class="input-container">
                <i class="bi bi-person"></i>
                <input type="text" name="username" placeholder="Ingrese su Usuario" required>
            </div>
            <!-- PASSWORD INPUT -->
            <div class="input-container">
                <i class="bi bi-lock"></i>
                <input type="password" name="password" placeholder="Ingrese su Contraseña" required>
            </div>
            <!-- REGISTER BUTTON -->
            <div class="input-container">
                <i class="bi bi-pencil-square" style="margin: 5px;"></i>
                <input type="submit" value="ENTRAR" class="register-btn">
            </div>
            <div class="input-container">
                <a href="login.php" style="color: #ffffff; text-decoration: none; font-size: 16px; margin-left: 10px;">
                Sistema de Procesos en Vinculacion
                </a>
            </div>
            <a>© 2024 Universidad Técnica de Manabí</a>
        </form>
    </div>
</body>
</html>
