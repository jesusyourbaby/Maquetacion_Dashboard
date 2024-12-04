<?php
session_start();
include 'conexion.php';  // Conexión a la base de datos

// Verificar si se ha pasado un token por la URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar el token en la base de datos
    $sql = "SELECT * FROM permiso WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token encontrado
        $user_data = $result->fetch_assoc();
        $expiry = $user_data['token_expiry'];

        // Verificar si el token ha expirado
        if (strtotime($expiry) > time()) {
            // El token es válido
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $new_password = $_POST['password'];

                // Actualizar la contraseña
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                // Usar el nombre correcto de la columna, que es 'contrasena'
                $update_sql = "UPDATE permiso SET contrasena = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $hashed_password, $token);
                $update_stmt->execute();

                // Mostrar el modal de éxito
                $success_message = true;
            }
        } else {
            $error_message = "El token ha expirado.";
        }
    } else {
        $error_message = "Token no válido.";
    }
} else {
    $error_message = "No se proporcionó un token.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Contraseña</title>
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

        .input-container {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffffff;
        }

        .input-container input {
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
        }

        .input-container input::placeholder {
            color: #ffffff;
            text-align: center;
        }

        .input-container input[type="submit"] {
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

        .input-container input[type="submit"]:hover {
            cursor: pointer;
            background: #055223;
            color: #ffffff;
        }

        .input-container a {
            text-decoration: none;
            font-size: 12px;
            line-height: 20px;
            color: rgb(255, 255, 255);
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .input-container a:hover {
            color: #fff;
        }

        .login-box .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: absolute;
            top: -50px;
            left: calc(50% - 50px);
        }
        /* Modal Estilo */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Asegura que el modal esté por encima de todo */
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            z-index: 10000; /* Para que el contenido del modal esté encima del fondo */
        }

        .modal button {
            background-color: #008033;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal button:hover {
            background-color: #055223;
        }

    </style>
</head>
<body>

    <?php if (isset($success_message)): ?>
        <div id="successModal" class="modal">
            <div class="modal-content">
                <h2>La contraseña se cambió con éxito</h2>
                <button onclick="window.location.href='login.php'">Entendido</button>
            </div>
        </div>
        <script>
            document.getElementById("successModal").style.display = "flex";
        </script>
    <?php elseif (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="login-box">
        <img src="./img/Logo_utm.png" class="avatar" alt="Avatar Image">
        <h1>Cambio de Contraseña</h1>
        <form action="cambio.php?token=<?php echo $token; ?>" method="POST">
            <div class="input-container">
                <i class="bi bi-lock"></i>
                <input type="password" name="password" placeholder="Nueva Contraseña" required>
            </div>
            <div class="input-container">
                <input type="submit" value="Cambiar Contraseña">
            </div>
        </form>
    </div>

</body>
</html>
