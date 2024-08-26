<?php
session_start();
include 'conexion.php'; // Incluir la conexión a la base de datos
// Datos de conexión a la base de datos
$error = '';

// Verificar si se han enviado los datos del formulario
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Obtener los datos del formulario
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Consultar en la base de datos
    $sql = "SELECT * FROM permiso WHERE usuario='$user' AND contrasena='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtener los datos del usuario
        $row = $result->fetch_assoc();
        
        // Iniciar sesión
        $_SESSION['usuario'] = $user;
        $_SESSION['rol'] = $row['rol'];
        
        // Redirigir a la página principal
        header("Location: index.php");
        exit;
    } else {
        // Usuario o contraseña incorrecta

        $error = "Usuario o contraseña incorrecta";
    }
} 

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;  /* Fondo blanco */
            font-family: 'Quicksand', sans-serif;
            height: 100vh;
            position: relative;
        }

        body::after {
            content: '';
            position: fixed;  /* Asegura que esté siempre en la misma posición en la ventana */
            bottom: 0;
            right: 0;
            width: 350px;  /* Ajusta el tamaño según tus necesidades */
            height: 350px;  /* Ajusta el tamaño según tus necesidades */
            background: url('./img/fondo.png') no-repeat bottom right;
            background-size: contain; /* Ajusta la imagen para que quepa en el contenedor */
            opacity: 0.5; /* Ajusta la opacidad según tus necesidades */
        }

        .login-box {
            width: 320px;
            background: #003314;  /* Fondo verde oscuro */
            color: #fff;
            top: 50%;
            left: 50%;
            position: absolute;
            transform: translate(-50%, -50%);
            box-sizing: border-box;
            padding: 70px 30px;
            border-radius: 10px;  /* Bordes redondeados */
        }

        .login-box .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: absolute;
            top: -50px;
            left: calc(50% - 50px);
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
            color: #ffffff;  /* Color claro para los íconos */
        }

        .login-box .input-container input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            box-sizing: border-box;
            border: none;
            background: #008033;  /* Fondo verde intermedio */
            outline: none;
            height: 40px;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;  /* Bordes redondeados */
            text-align: center;  /* Centrar el texto */
        }

        .login-box .input-container input::placeholder {
            color: #ffffff;  /* Color claro para el placeholder */
            text-align: center;  /* Centrar el placeholder */
        }

        .login-box input[type="submit"], .login-box input[type="button"] {
            border: none;
            outline: none;
            height: 40px;
            background: #008033;  /* Color original del botón */
            color: #fff;
            font-size: 18px;
            border-radius: 20px;  /* Bordes redondeados */
            width: 100%;
            margin-top: 10px;
        }

        .login-box input[type="submit"]:hover, .login-box input[type="button"]:hover {
            cursor: pointer;
            background: #055223;
            color: #ffffff;
        }

        .login-box a {
            text-decoration: none;
            font-size: 12px;
            line-height: 20px;
            color: rgb(255, 255, 255);
            display: block;
            text-align: center;
            margin-top: 20px;  /* Separación adicional del botón de login */
        }

        .login-box a:hover {
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
        /* Estilo por defecto del botón Ingresar */
        .login-btn {
            background: #5b856c; /* Color original del botón Ingresar */
        }

        /* Cambiar color al hacer hover o focus en el botón Ingresar */
        .login-btn:hover, .login-btn:focus {
            background: #003314; /* Color para hover y focus en el botón Ingresar */
        }

        /* Estilo por defecto del botón Registrarse */
        .register-btn {
            background: #FF5733; /* Color original del botón Registrarse */
        }

        /* Cambiar color al hacer hover o focus en el botón Registrarse */
        .register-btn:hover, .register-btn:focus {
            background: #C13B1A; /* Color para hover y focus en el botón Registrarse */
        }

        /* Evitar conflictos con estilos generales */
        .login-box input[type="submit"], .login-box input[type="button"] {
            border: none;
            outline: none;
            height: 40px;
            background: #047932;  /* Color original del botón */
            color: #fff;
            font-size: 18px;
            border-radius: 20px;  /* Bordes redondeados */
            width: 100%;
            margin-top: 10px;
        }

        /* Para hover en todos los botones que no sean el de Registrarse */
        .login-box input[type="submit"]:hover, .login-box input[type="button"]:hover:not(.register-btn) {
            cursor: pointer;
            background: #055223;
            color: #ffffff;
        }
    </style>
    <!-- BOX ICONS -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-box">
        <img src="./img/Logo_utm.png" class="avatar" alt="Avatar Image">
        <h1>Sistema de Procesos en Vinculacion</h1>
        <?php if (!empty($error)) : ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <form class="login-form" action="login.php" method="post">
            <!-- USERNAME INPUT -->
            <div class="input-container">
                <i class="bi bi-person"></i>
                <input type="text" name="username" placeholder="Ingrese su Usuario" id="username" required>
            </div>
            <!-- PASSWORD INPUT -->
            <div class="input-container">
                <i class="bi bi-lock"></i>
                <input type="password" name="password" placeholder="Ingrese su Contraseña" id="password" required>
            </div>
            <!-- LOGIN-ENTER INPUT -->
            <!-- REGISTER BUTTON -->
            <div class="input-container">
                <i class="bi bi-box-arrow-in-right" style="margin: 5px;"></i>
                <input type="submit" value="INGRESAR" class="login-btn">
            </div>

            <div class="input-container">
                <i class="bi bi-pencil-square" style="margin: 5px;"></i>
                <input type="button" value="ADMINISTRADOR" class="register-btn" onclick=" location.href='register.php';">
            </div>
            <a>© 2024 Universidad Técnica de Manabí</a><br>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var inputs = document.querySelectorAll('.input-container input');
            inputs.forEach(function(input) {
                input.addEventListener('focus', function() {
                    this.setAttribute('placeholder', '');
                });
                input.addEventListener('blur', function() {
                    if (this.id === 'username') {
                        this.setAttribute('placeholder', 'Ingrese su Usuario');
                    } else if (this.id === 'password') {
                        this.setAttribute('placeholder', 'Ingrese su Contraseña');
                    }
                });
            });
        });
    </script>
</body>
</html>
