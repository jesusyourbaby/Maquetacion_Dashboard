<?php
session_start(); // Inicio de Sesión y Verificación
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la Base de Datos
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
// Llama a esta función para obtener los accesos del rol actual.
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Menu Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
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
        .enlace.active {
            display: flex;
            align-items: center;
        }
        /* Cambiar a blanco el fondo y el texto a un color oscuro cuando se pasa el cursor */
        .menu .enlace:hover {
            background-color: white;
            color: #2c3e50; /* Color del texto al pasar el cursor */
        }

        /* Estilo para el select */
        .enlace.active select {
            background-color: transparent;
            border: none;
            font-size: 16px;
            color: white; /* Color del texto por defecto */
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding: 0; /* Elimina el padding interno */
        }

        /* Cambiar el color del select al pasar el cursor */
        .enlace.active:hover select {
            color: #2c3e50; /* Color del texto del select al pasar el cursor */
        }

        /* Estilo para las opciones del select */
        .enlace.active select option {
            background-color: white; /* Color de fondo de las opciones */
            color: black; /* Color del texto de las opciones */
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
        
        .navbar {
            width: 100%; /* Asegura que la navbar ocupe todo el ancho */
            position: fixed; /* Permite que la navbar se quede fija al hacer scroll */
            left: 0;
            padding-left: 320px;
            top: 0; /* Fija la navbar en la parte superior */
            background-color: #423187; /* Color de fondo de la navbar */
        }
        .tablero {
            margin-top: 70px; /* Ajusta este valor a la altura de tu navbar */
            flex: 1; /* Permite que el tablero ocupe el espacio restante */
            padding: 20px; /* Añade algo de padding para mayor separación */
            overflow: auto; /* Permite el desplazamiento si el contenido es más grande */
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
                <form method="post" id="dashboard-form">
                    <select name="dashboard" onchange="this.form.submit();">
                        <?php if ($current_role == 'Director') : ?>
                            <option value="default">Dashboard</option>
                            <option value="TAREAS" <?= $selected_dashboard == 'TAREAS' ? 'selected' : '' ?>>Tareas</option>
                            <option value="PROYECTOS" <?= $selected_dashboard == 'PROYECTOS' ? 'selected' : '' ?>>Proyectos</option>
                            <option value="INSTITUCIONES" <?= $selected_dashboard == 'INSTITUCIONES' ? 'selected' : '' ?>>Instituciones</option>
                        <?php elseif ($current_role == 'Mentor') : ?>
                            <option value="default">Dashboard</option>
                            <option value="PROYECTOS" <?= $selected_dashboard == 'PROYECTOS' ? 'selected' : '' ?>>Proyectos</option>
                            <option value="INSTITUCIONES" <?= $selected_dashboard == 'INSTITUCIONES' ? 'selected' : '' ?>>Instituciones</option>
                        <?php elseif ($current_role == 'Docente') : ?>
                            <option value="INSTITUCIONES" <?= $selected_dashboard == 'INSTITUCIONES' ? 'selected' : '' ?>>Instituciones</option>
                        <?php endif; ?>
                    </select>
                </form>
            </div>

            <div class="enlace">
                <i class="bx bxs-exit"></i>
                <span onclick="location.href='logout.php';">Cerrar Sesion</span>
            </div>
        </div>
    </div>
    <div class="seccion">
        <nav class="navbar navbar-expand-lg" style="background-color:#006629;">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <div>
                        <?php
                        if ($current_role == 'Director') {
                            echo '<p>Bienvenido Director.</p>';
                        } elseif ($current_role == 'Mentor') {
                            echo '<p>Bienvenido Mentor.</p>';
                        } elseif ($current_role == 'Docente') {
                            echo '<p>Bienvenido Docente.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </nav>
        <div class="tablero">
                <?php if ($selected_dashboard == 'TAREAS' && $current_role != 'Docente'): ?>
                    <iframe title="Dashboard_Vinculacion - Tareas" width="1250" height="600" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false" frameborder="0" allowFullScreen="true"></iframe>
                <?php elseif ($selected_dashboard == 'PROYECTOS'): ?>
                    <iframe title="Dashboard_Vinculacion - Proyectos" width="1250" height="600" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false&pageName=d3a902f0a34f1c82b329" frameborder="0" allowFullScreen="true"></iframe>
                <?php elseif ($selected_dashboard == 'INSTITUCIONES' || $current_role == 'Docente'): ?>
                    <iframe title="Dashboard_Vinculacion - Instituciones" width="1250" height="600" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false&pageName=192b6339f0de780f4904" frameborder="0" allowFullScreen="true"></iframe>
                <?php endif; ?>
            </div>
        <div class="user-info">
            <i class="bi bi-person-fill">User: </i>
            <?php echo htmlspecialchars($usuario); ?>
        </div>
    </div>
</body>
</html>
