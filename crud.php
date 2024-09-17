<?php
session_start();
include 'conexion.php'; // Incluir la conexión a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: register.php");
    exit();
}

// Inicializar variables para manejar mensajes y datos
$message = '';
$error = '';
// Obtener el usuario de la sesión
$user = $_SESSION['username'];

// Crear un nuevo registro
if (isset($_POST['create'])) {
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];
    $contrasena = $_POST['contrasena'];

    if (!empty($usuario) && !empty($rol) && !empty($contrasena)) {
        if ($rol == "Admin") {
            $sql = "INSERT INTO usuarios_crud (usuario, rol, contrasena) VALUES ('$usuario', '$rol', '$contrasena')";
        } else {
            $sql = "INSERT INTO permiso (usuario, rol, contrasena) VALUES ('$usuario', '$rol', '$contrasena')";
        }
        if ($conn->query($sql) === TRUE) {
            $message = "Nuevo usuario creado con éxito.";
        } else {
            $error = "Error al crear el usuario: " . $conn->error;
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}

// Actualizar un registro existente
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rolrequerido'];
    $contrasena = $_POST['contrasena'];

    if (!empty($id) && !empty($usuario) && !empty($rol) && !empty($contrasena)) {
        $sql = "UPDATE permiso SET usuario='$usuario', rol='$rol', contrasena='$contrasena' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $message = "Usuario actualizado con éxito.";
        } else {
            $error = "Error al actualizar el usuario: " . $conn->error;
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
            $message = "Usuario eliminado con éxito.";
        } else {
            $error = "Error al eliminar el usuario: " . $conn->error;
        }
    } else {
        $error = "ID es obligatorio para eliminar un usuario.";
    }
}

// Configuración de paginación
$registros_por_pagina = 5; // Número de registros por página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1; // Página actual
$offset = ($pagina_actual - 1) * $registros_por_pagina; // Offset para la consulta

// Obtener el número total de registros
$sql_total = "SELECT COUNT(*) AS total FROM permiso";
$result_total = $conn->query($sql_total);
$total_registros = $result_total->fetch_assoc()['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Leer los registros existentes con límite y offset
$sql = "SELECT * FROM permiso LIMIT $offset, $registros_por_pagina";
$result = $conn->query($sql);

$conn->close(); // Cerrar la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- BOX ICONS -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Procesos de Vinculación</title>
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
            display: inline-block;
            padding: 10px 30px; /* Aumenta el área de clic */
            border-radius: 5px; /* Redondear bordes si es necesario */
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
        .user-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background-color: #006629;
            color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            z-index: 1000;
        }
        .user-info i {
            margin-right: 5px;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #003314;
            color: white;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #055223;
        }
        .pagination .current {
            background-color: #01953f ;
            padding: 8px 16px;
            margin: 0 5px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="menu-dashboard">
        <!-- TOP MENU -->
        <div class="top-menu">
            <div class="logo">
                <img src="./img/Logo_utm.png" alt="Logo">
                <span>Administrador de Procesos de Vinculación</span>
            </div>
        </div>
        <!-- MENU -->
        <div class="menu">
            <!-- Nueva opción para crear un usuario -->
            <div class="enlace">
                <i class="bx bx-user-plus"></i>
                <span onclick="toggleCrearUsuarioForm();">Crear Usuario</span>
            </div>
            <div class="enlace">
                <i class="bx bxs-exit"></i>
                <span onclick="location.href='logout.php';">Cerrar Sesión</span>
            </div>
        </div>
    </div>


    <div class="content">
        <h2>Gestión de Usuarios</h2>
        <?php if (!empty($message)) : ?>
            <div id="message" class="message"><?= $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)) : ?>
            <div id="error" class="error"><?= $error; ?></div>
        <?php endif; ?>

        <!-- Formulario para crear un nuevo usuario (inicialmente oculto) -->
        <form method="post" id="crearUsuarioForm" style="display: none;">
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
            <button type="submit" name="create">Crear Usuario</button>
            <button type="button" onclick="toggleCrearUsuarioForm();">Cerrar</button> <!-- Botón para cerrar -->
        </form>




        <h3>Usuarios Existentes</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['usuario']; ?></td>
                        <td><?= $row['rol']; ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <input type="text" name="usuario" value="<?= $row['usuario']; ?>" required>
                                <select name="rolrequerido" id="rolrequerido" required>
                                    <option value="Director" <?= ($row['rol'] == 'Director') ? 'selected' : ''; ?>>Director</option>
                                    <option value="Mentor" <?= ($row['rol'] == 'Mentor') ? 'selected' : ''; ?>>Mentor</option>
                                    <option value="Responsable" <?= ($row['rol'] == 'Responsable') ? 'selected' : ''; ?>>Responsable</option>
                                    <option value="Admin" <?= ($row['rol'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <input type="password" name="contrasena" value="<?= $row['contrasena']; ?>" required>
                                <button type="submit" name="update">Actualizar</button>
                                <button type="submit" name="delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_paginas; $i++) : ?>
                <?php if ($i == $pagina_actual) : ?>
                    <!-- Página actual, no es un enlace -->
                    <span class="current"><?= $i; ?></span>
                <?php else : ?>
                    <!-- Otras páginas, son enlaces -->
                    <a href="?pagina=<?= $i; ?>"><?= $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>

        <div class="user-info">
            <i class="bi bi-person-fill">Admin: </i>
            <?php echo htmlspecialchars($user); ?>
        </div>
    </div>

    <script>
        // Función para ocultar mensajes después de 5 segundos
        function hideMessages() {
            const message = document.getElementById('message');
            const error = document.getElementById('error');

            if (message) {
                setTimeout(() => {
                    message.style.display = 'none';
                }, 5000);
            }

            if (error) {
                setTimeout(() => {
                    error.style.display = 'none';
                }, 5000);
            }
        }

        // Llamar a la función al cargar la página
        window.onload = hideMessages;
    </script>

    <script>
    // Función para mostrar u ocultar el formulario
    function toggleCrearUsuarioForm() {
        const form = document.getElementById('crearUsuarioForm');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';  // Mostrar el formulario
            form.scrollIntoView({ behavior: 'smooth' });  // Hacer scroll al formulario
        } else {
            form.style.display = 'none';  // Ocultar el formulario
        }
    }
</script>


</body>
</html>
