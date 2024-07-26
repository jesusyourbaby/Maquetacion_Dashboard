<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
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
            background-color: rgba(44, 62, 80, 0.8); /* Color transparente */
            color: white; /* Texto en blanco para contraste */
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Poppins', sans-serif; /* Asegurarse de que coincida con el estilo de texto */
            font-size: 14px;
        }
        .user-info i {
            margin-right: 5px;
        }
        .header-message {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: black;
            margin: 20px 0;
        }
        .footer-message {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            font-size: 14px;
            color: black;
            width: 100%;
            position: absolute;
            bottom: 0;
        }
        .footer-message-left {
            text-align: left;
        }
        .footer-message-right {
            text-align: right;
        }
        .menu .enlace, .menu .custom-select-container {
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
            display: flex;
            align-items: center;
            color: white;
            font-size: 16px; /* Ajustar tamaño de fuente */
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
        }
        .custom-select {
            background-color: transparent;
            border: none;
            color: inherit;
            font: inherit;
            width: calc(100% - 30px); /* Ajustar el ancho para que quepa el icono */
            margin-left: 10px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding: 0 10px; /* Ajustar el padding */
        }
        .custom-select option {
            color: #2c3e50; /* Opciones de color oscuro */
        }
        .custom-select-container::after {
            content: '\f078'; /* Icono de flecha hacia abajo */
            font-family: 'FontAwesome';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <div class="menu-dashboard open"> <!-- Añadido la clase 'open' por defecto -->
        <!-- TOP MENU -->
        <div class="top-menu">
            <div class="logo">
                <img src="./img/Logo_utm.png" alt="">
                <span>Sistema de Procesos de Vinculacion</span> <!-- Actualizado el nombre -->
            </div>
        </div>
        <!-- MENU -->
        <div class="menu">
            <br>
            <br>
            <br>
            <div class="enlace">
                <i class="bx bx-grid-alt"></i>
                <span onclick="showDashboard(); return false">Dashboard</span>
            </div>
            <div class="enlace">
                <i class='bx bxs-cog' ></i>
                <select id="role-select" class="custom-select">
                    <option value="default" selected>Rol</option>
                    <option value="Mentor">Mentor</option>
                    <option value="Docente">Docente</option>
                    <option value="Director">Director</option>
                </select>
            </div>
            <div class="enlace">
                <i class="bx bxs-exit"></i>
                <span onclick="location.href='logout.php';">Cerrar Sesion</span>
            </div>
        </div>
    </div>
    <div class="seccion">
        <div id="tablero" class="tablero" style="display: none">
            <iframe title="Dashboard_Vinculacion - copia" width="1140" height="541.25" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076" frameborder="0" allowFullScreen="true"></iframe>
        </div>
    </div>
    <div class="user-info">
        <p><i class="bi bi-person-circle"></i>Usuario: <?php echo $_SESSION['usuario']; ?></p>
        <br>
        <p><i class="bi bi-gear"></i>Rol: <?php echo $_SESSION['rol']; ?></p>
    </div>
</body>
</html>
