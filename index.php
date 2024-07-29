<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "spv";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el usuario de la sesión
$usuario = $_SESSION['usuario'];

// Obtener los roles del usuario desde la base de datos
$sql = "SELECT rol FROM permiso WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->bind_result($roles);
$stmt->fetch();
$stmt->close();

// Dividir los roles en un array
$roles_array = explode(',', $roles);

// Actualizar el rol en la sesión si se selecciona un nuevo rol
if (isset($_POST['roles']) && in_array($_POST['roles'], $roles_array)) {
    $_SESSION['rol'] = $_POST['roles'];
}

$current_role = $_SESSION['rol'] ?? $roles_array[0]; // Primer rol por defecto

// Obtener los accesos basados en el rol
function obtenerAccesos($rol) {
    $accesos = [];
    if ($rol === 'Director') {
        $accesos = ['TAREAS', 'PROYECTOS', 'INSTITUCIONES'];
    } elseif ($rol === 'Mentor') {
        $accesos = ['PROYECTOS', 'INSTITUCIONES'];
    } elseif ($rol === 'Docente') {
        $accesos = ['INSTITUCIONES'];
    }
    return $accesos;
}

$accesos = obtenerAccesos($current_role);

$selected_dashboard = isset($_POST['dashboard']) ? $_POST['dashboard'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Dashboard</title>
    <!-- BOX ICONS -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="./css/estilos.css">
    <!-- CUSTOM JS -->
    <script src="./js/app.js" defer></script>
    <style>
        .user-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background-color: rgba(44, 62, 80, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }
        .user-info i {
            margin-right: 5px;
        }
        .menu .enlace, .menu .custom-select-container {
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
            display: flex;
            align-items: center;
            color: white;
            font-size: 16px;
        }
        .menu .enlace:hover,
        .menu .enlace.active,
        .menu .custom-select-container:hover,
        .menu .custom-select-container.active {
            background-color: white;
            color: #2c3e50;
        }
        .custom-select-container {
            position: relative;
            width: 100%;
        }
        .custom-select {
            background-color: transparent;
            border: none;
            color: inherit;
            font: inherit;
            width: 100%;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding: 10px 30px 10px 10px; 
        }
        .custom-select option {
            color: #2c3e50;
        }
        .custom-select-container::after {
            content: '\f078';
            font-family: 'FontAwesome';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: inherit;
        }
        .message-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
            font-size: 18px;
            color: black;
        }
    </style>
</head>
<body>
    <div class="menu-dashboard open">
        <!-- TOP MENU -->
        <div class="top-menu">
            <div class="logo">
                <img src="./img/Logo_utm.png" alt="">
                <span>Sistema de Procesos de Vinculacion</span>
            </div>
        </div>
        <!-- MENU -->
        <div class="menu">
            <br>
            <br>
            <br>
            <div class="enlace active">
                <i class="bx bx-grid-alt"></i>
                <span onclick="showDashboard(); return false">Dashboard</span>
            </div>
            <div class="enlace">
                <i class="bx bxs-exit"></i>
                <span onclick="location.href='logout.php';">Cerrar Sesion</span>
            </div>
        </div>
    </div>
    <div class="seccion">
        <div id="message-container" class="message-container">
            <?php
            if ($current_role == 'Director') {
                echo '<p>Bienvenido Director.</p>';
                echo '<form method="post" id="dashboard-form">
                        <select name="dashboard" onchange="this.form.submit();">
                            <option value="default">Seleccione un Dashboard:</option>
                            <option value="TAREAS" ' . ($selected_dashboard == 'TAREAS' ? 'selected' : '') . '>Tareas</option>
                            <option value="PROYECTOS" ' . ($selected_dashboard == 'PROYECTOS' ? 'selected' : '') . '>Proyectos</option>
                            <option value="INSTITUCIONES" ' . ($selected_dashboard == 'INSTITUCIONES' ? 'selected' : '') . '>Instituciones</option>
                        </select>
                      </form>';
            } elseif ($current_role == 'Mentor') {
                echo '<p>Bienvenido Mentor.</p>';
                echo '<form method="post" id="dashboard-form">
                        <select name="dashboard" onchange="this.form.submit();">
                            <option value="default">Seleccione un Dashboard:</option>
                            <option value="PROYECTOS" ' . ($selected_dashboard == 'PROYECTOS' ? 'selected' : '') . '>Proyectos</option>
                            <option value="INSTITUCIONES" ' . ($selected_dashboard == 'INSTITUCIONES' ? 'selected' : '') . '>Instituciones</option>
                        </select>
                      </form>';
            } elseif ($current_role == 'Docente') {
                echo '<p>Bienvenido Docente.</p>';
            }
            ?>
        </div>
        <?php if ($selected_dashboard || $current_role == 'Docente'): ?>
            <div class="tablero">
                <?php if ($selected_dashboard == 'TAREAS' && $current_role != 'Docente'): ?>
                    <iframe title="Dashboard_Vinculacion - Tareas" width="1140" height="541.25" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false" frameborder="0" allowFullScreen="true"></iframe>
                <?php elseif ($selected_dashboard == 'PROYECTOS'): ?>
                    <iframe title="Dashboard_Vinculacion - Proyectos" width="1140" height="541.25" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false&pageName=d3a902f0a34f1c82b329" frameborder="0" allowFullScreen="true"></iframe>
                <?php elseif ($selected_dashboard == 'INSTITUCIONES' || $current_role == 'Docente'): ?>
                    <iframe title="Dashboard_Vinculacion - Instituciones" width="1140" height="541.25" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false&pageName=192b6339f0de780f4904" frameborder="0" allowFullScreen="true"></iframe>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="user-info">
        <p><i class="bi bi-person-circle"></i>Usuario: <?php echo $_SESSION['usuario']; ?></p>
        <br>
        <p><i class="bi bi-gear"></i>Rol: <span id="current-role"><?php echo $current_role; ?></span></p>
    </div>
    <form method="post" style="display: none;" id="role-form">
        <input type="hidden" name="roles" id="hidden-role">
    </form>
    
    <script>
        document.getElementById('role-select').addEventListener('change', function() {
            var selectedRole = this.value;
            document.getElementById('hidden-role').value = selectedRole;
            document.getElementById('role-form').submit();
        });

        // Actualizar el rol en el pie de página al cambiar el rol
        document.getElementById('role-select').addEventListener('change', function() {
            var selectedRole = this.value;
            document.getElementById('current-role').textContent = selectedRole;

            var messageContainer = document.getElementById('message-container');
            if (selectedRole === 'Director') {
                messageContainer.innerHTML = '<p>Bienvenido Director</p>';
                messageContainer.innerHTML += '<form method="post" id="dashboard-form"><select name="dashboard" onchange="this.form.submit();"><option value="default">Seleccione un Dashboard:</option><option value="TAREAS">Tareas</option><option value="PROYECTOS">Proyectos</option><option value="INSTITUCIONES">Instituciones</option></select></form>';
            } else if (selectedRole === 'Mentor') {
                messageContainer.innerHTML = '<p>Bienvenido Mentor</p>';
                messageContainer.innerHTML += '<form method="post" id="dashboard-form"><select name="dashboard" onchange="this.form.submit();"><option value="default">Seleccione un Dashboard:</option><option value="PROYECTOS">Proyectos</option><option value="INSTITUCIONES">Instituciones</option></select></form>';
            } else if (selectedRole === 'Docente') {
                messageContainer.innerHTML = '<p>Bienvenido Docente</p>';
            }
        });
    </script>
</body>
</html>
