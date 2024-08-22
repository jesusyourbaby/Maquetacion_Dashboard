<?php
include 'conexion.php'; // Incluir la conexión a la base de datos

// Inicializar variables para manejar mensajes y datos
$message = '';
$error = '';

// Crear un nuevo registro
if (isset($_POST['create'])) {
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];
    $contrasena = $_POST['contrasena'];

    if (!empty($usuario) && !empty($rol) && !empty($contrasena)) {
        $sql = "INSERT INTO permiso (usuario, rol, contrasena) VALUES ('$usuario', '$rol', '$contrasena')";
        if ($conn->query($sql) === TRUE) {
            $message = "Nuevo rol creado con éxito.";
        } else {
            $error = "Error al crear el rol: " . $conn->error;
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}

// Leer los registros existentes
$sql = "SELECT * FROM permiso";
$result = $conn->query($sql);

// Actualizar un registro existente
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];
    $contrasena = $_POST['contrasena'];

    if (!empty($id) && !empty($usuario) && !empty($rol) && !empty($contrasena)) {
        $sql = "UPDATE permiso SET usuario='$usuario', rol='$rol', contrasena='$contrasena' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $message = "Rol actualizado con éxito.";
        } else {
            $error = "Error al actualizar el rol: " . $conn->error;
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}

// Eliminar un registro existente
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    if (!empty($id)) {
        $sql = "DELETE FROM permiso WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $message = "Rol eliminado con éxito.";
        } else {
            $error = "Error al eliminar el rol: " . $conn->error;
        }
    } else {
        $error = "ID es obligatorio para eliminar un rol.";
    }
}

$conn->close(); // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Sistema de Procesos de Vinculación</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Quicksand', sans-serif;
            background-color: #f4f4f4;
        }

        .menu-dashboard {
            width: 250px;
            background-color: #003314;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            color: #fff;
            padding-top: 20px;
        }

        .menu-dashboard .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .menu-dashboard .logo img {
            width: 80px;
            height: auto;
        }

        .menu-dashboard .logo span {
            display: block;
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .menu-dashboard .enlace {
            padding: 15px;
            text-align: center;
            cursor: pointer;
            font-size: 18px;
            border-bottom: 1px solid #004d1f;
        }

        .menu-dashboard .enlace:hover {
            background-color: #055223;
        }

        .menu-dashboard .enlace i {
            margin-right: 10px;
        }

        .menu-dashboard .enlace span {
            vertical-align: middle;
        }

        .content {
            margin-left: 260px;
            padding: 20px;
        }

        .message {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #003314;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #055223;
            color: white;
        }

        form {
            margin-bottom: 20px;
        }

        form input[type="text"],
        form input[type="password"],
        form select {
            padding: 5px;
            margin-right: 10px;
        }

        form button {
            padding: 5px 10px;
            background-color: #003314;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #055223;
        }
    </style>
    <!-- BOX ICONS -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="menu-dashboard">
        <!-- TOP MENU -->
        <div class="top-menu">
            <div class="logo">
                <img src="./img/Logo_utm.png" alt="Logo">
                <span>CRUD</span>
                <span>Sistema de Procesos de Vinculación</span>
            </div>
        </div>
        <!-- MENU -->
        <div class="menu">
            <div class="enlace">
                <i class="bx bxs-exit"></i>
                <span onclick="location.href='logout.php';">Cerrar Sesión</span>
            </div>
        </div>
    </div>

    <div class="content">
        <h2>Gestión de Roles</h2>
        <?php if (!empty($message)) : ?>
            <div class="message"><?= $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)) : ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>

        <!-- Formulario para crear un nuevo rol -->
        <form method="post">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" required>
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="Director">Director</option>
                <option value="Mentor">Mentor</option>
                <option value="Responsable">Responsable</option>
                <option value="Admin">Admin</option>
            </select>
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" required>
            <button type="submit" name="create">Crear Rol</button>
        </form>

        <h3>Roles Existentes</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Contraseña</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['usuario']; ?></td>
                        <td><?= $row['rol']; ?></td>
                        <td><?= $row['contrasena']; ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <input type="text" name="usuario" value="<?= $row['usuario']; ?>" required>
                                <select name="rol" required>
                                    <option value="Director" <?= $row['rol'] == 'Director' ? 'selected' : ''; ?>>Director</option>
                                    <option value="Mentor" <?= $row['rol'] == 'Mentor' ? 'selected' : ''; ?>>Mentor</option>
                                    <option value="Responsable" <?= $row['rol'] == 'Responsable' ? 'selected' : ''; ?>>Responsable</option>
                                    <option value="Admin" <?= $row['rol'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <input type="password" name="contrasena" value="<?= $row['contrasena']; ?>" required>
                                <button type="submit" name="update">Actualizar</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <button type="submit" name="delete">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
