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
        $accesos = ['TAREAS', 'PROYECTOS'];
    } elseif ($rol === 'Responsable') {
        $accesos = ['TAREAS', 'PROYECTOS', 'INSTITUCIONES'];
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
            /* background-color: #006629; */
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

        .menu-dashboard.open .menu .enlace span {
            opacity: 1;
            padding: 10px 30px; /* Aumenta el área de clic */
            border-radius: 5px; /* Redondear bordes si es necesario */
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
            padding: 10px;
            border: none;
            font-size: 16px;
            color: white; /* Color del texto por defecto */
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 150px;
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
        .user-menu {
            cursor: pointer;
            display: flex;
            align-items: center;
            color: white;
            gap: 0.5rem;
            padding: 5px;
            border-radius: 5px; /* Añadir bordes redondeados si es necesario */
            transition: background-color 0.3s, color 0.3s;
        }
        .user-menu:hover {
            background-color: rgba(255, 255, 255, 0.1);  /* Fondo suave al hacer hover */
            border-radius: 5px;  /* Asegúrate de que el borde se mantenga redondeado */
        }
        .user-menu .dropdown-menu {
            position: absolute;
            bottom: -45px; /* Ajusta esta distancia para alinearlo sobre el icono */
            right: 0;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            display: none;
            color: black;
            animation: slideUp 0.3s ease forwards;
            transform-origin: bottom;
            z-index: 1000;
            animation: slideDown 0.3s ease forwards;
        }
        .user-menu .dropdown-menu a {
            color: #333;
            text-decoration: none;
        }
        .user-menu .dropdown-menu a:hover {
            color: #006629;
        }
        .user-icon {
            position: relative;
            cursor: pointer;
        }
        .user-icon:hover {
            border: 2px solid #ccc;  /* Agrega un borde alrededor del icono */
            border-radius: 50%;  /* Hace el borde redondeado si el icono es circular */
            background-color: rgba(255, 255, 255, 0.1);  /* Cambio de color de fondo */
            padding: 5px;  /* Espacio extra para el borde */
        }
        /* Animación para deslizar hacia abajo */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Estilo para la ventana emergente */
        .help-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1001;
        }

        .help-modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 60%;
            max-width: 600px;
            text-align: center;
            color: #333;
        }

        .help-modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
        .help-modal-content button {
        background-color: #008033;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        }

        /* Estilo para centrar exclusivamente la ventana de feedback */
        #feedbackModal {
        display: none; /* Oculta la ventana por defecto */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        justify-content: center; /* Centrado horizontal */
        align-items: center; /* Centrado vertical */
        background-color: rgba(0, 0, 0, 0.5); /* Fondo semitransparente */
}

        /* Estilo del contenido dentro de #feedbackModal */
        #feedbackModal .help-modal-content {
            background-color: white;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 40%; /* Ajusta el ancho según sea necesario */
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #feedbackForm {
            display: flex; /* Activa flexbox */
            flex-direction: column; /* Organiza los elementos en una columna */
            gap: 15px; /* Espaciado entre los elementos */
        }

        #feedbackForm button {
            align-self: center; /* Centra el botón horizontalmente */
            padding: 10px 20px; /* Ajusta el tamaño del botón */
            background-color: #006629; /* Cambia el color del botón si es necesario */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        #feedbackForm button:hover {
            background-color: #004d20; /* Color más oscuro en hover */
        }

        #feedbackForm textarea {
            resize: none; /* Desactiva la capacidad de redimensionar */
        }

        /* Estilos del enlace FAQ */
        .faq-modal {
            display: none; /* Oculto por defecto */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .faq-modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            width: 100%;
            position: relative; /* Añadido para posicionar la X dentro de la caja */
            color: #333;
        }

        /* Estilo de la X de cierre */
        .faq-modal-close {
            font-size: 30px;
            border: none;
            background: none;
            cursor: pointer;
            color: #000;
            position: absolute;
            top: 10px;
            right: 20px;
        }

        /* Estilos generales para las preguntas y respuestas */
        .faq-item {
            margin-bottom: 15px;
        }

        .faq-question {
            width: 100%;
            text-align: left;
            padding: 10px;
            background-color: #f0f0f0;
            border: none;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .faq-question:hover {
            background-color: #e0e0e0;
        }

        .faq-answer {
            display: none;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 2px solid #ccc;
            margin-top: 5px;
        }

        .faq-answer p {
            font-size: 16px;
            color: #555;
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
                            <option value="TAREAS" <?= $selected_dashboard == 'TAREAS' ? 'selected' : '' ?>>‎ Tareas</option>
                            <option value="PROYECTOS" <?= $selected_dashboard == 'PROYECTOS' ? 'selected' : '' ?>>‎ Proyectos</option>
                            <option value="INSTITUCIONES" <?= $selected_dashboard == 'INSTITUCIONES' ? 'selected' : '' ?>>‎ Instituciones</option>
                        <?php elseif ($current_role == 'Mentor') : ?>
                            <option value="default">Dashboard</option>
                            <option value="TAREAS" <?= $selected_dashboard == 'TAREAS' ? 'selected' : '' ?>>‎ Tareas</option>
                            <option value="PROYECTOS" <?= $selected_dashboard == 'PROYECTOS' ? 'selected' : '' ?>>‎ Proyectos</option>
                        <?php elseif ($current_role == 'Responsable') : ?>
                            <option value="default">Dashboard</option>
                            <option value="TAREAS" <?= $selected_dashboard == 'TAREAS' ? 'selected' : '' ?>>‎ Tareas</option>
                            <option value="PROYECTOS" <?= $selected_dashboard == 'PROYECTOS' ? 'selected' : '' ?>>‎ Proyectos</option>
                            <option value="INSTITUCIONES" <?= $selected_dashboard == 'INSTITUCIONES' ? 'selected' : '' ?>>‎ Instituciones</option>
                        <?php endif; ?>
                    </select>
                </form>
            </div>

            <div class="enlace">
                <i class="bi bi-info-circle"></i>
                <span onclick="openHelpModal();">Ayuda</span>
            </div>

            <div class="enlace">
                <i class="bi bi-chat-left-dots"></i>
                <span onclick="openFeedbackModal();">Feedback</span>
            </div>

            <div class="enlace">
                 <i class="bi bi-person-raised-hand"></i>
                <span onclick="openFAQModal();">FAQ</span>
            </div>



            <!-- <div class="enlace">
                <i class="bi bi-display"></i>
                <span onclick="location.href='https://pasantias.utm.edu.ec/?#6334e5824c402';">Sistema PPP-VIN</span>
            </div> -->
        </div>
    </div>

        <!-- Ventana emergente de ayuda -->
    <div class="help-modal" id="helpModal">
        <div class="help-modal-content">
            <button class="help-modal-close" onclick="closeHelpModal();">&times;</button>
            <h2>Información de ayuda</h2>
            <p>Bienvenido al sistema de gestión de procesos de vinculación. En esta página principal, podrás:</p>
            <ul>
                <li><strong>Visualizar procesos activos:</strong> Explora la relación entre tareas, proyectos e instituciones en tiempo real.</li>
                <li><strong>Filtrar información:</strong> Usa herramientas de búsqueda y filtros para encontrar datos específicos rápidamente.</li>
                <li><strong>Reportes:</strong> Obtén reportes detallados sobre los procesos de vinculación para análisis o presentaciones.</li>
            </ul>
            <p>Si necesitas más detalles sobre cómo usar una funcionalidad específica, consulta la sección de ayuda o contacta al administrador del sistema.</p>
            <button onclick="window.location.href='index.php'">Entendido</button>
        </div>
    </div>

    <!-- Ventana emergente de feedback -->
    <div class="help-modal" id="feedbackModal">
        <div class="help-modal-content">
            <button class="help-modal-close" onclick="closeFeedbackModal();">&times;</button>
            <h2>¡Queremos tu opinión!</h2>
            <p>Tienes alguna opinión o sugerencia para mejorar el sistema. Envíanos tus comentarios:</p>
            <form id="feedbackForm" action="send_feedback.php" method="POST">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" placeholder="Tu nombre" required>

                <label for="email">Correo:</label>
                <input type="email" id="email" name="email" placeholder="Tu correo" required>

                <label for="message">Comentario:</label>
                <textarea id="message" name="message" rows="5" placeholder="Escribe tus comentarios aquí..." required></textarea>

                <button type="submit">Enviar</button>
            </form>
        </div>
    </div>


    <!-- Modal de FAQ -->
    <div class="faq-modal" id="faqModal">
        <div class="faq-modal-content">
            <!-- Botón de cerrar dentro de la caja -->
            <button class="faq-modal-close" onclick="closeFAQModal();">&times;</button>
            <h2>Preguntas Frecuentes</h2>
            
            <div class="faq-item">
                <button class="faq-question" onclick="toggleAnswer(0)">
                    ¿Cómo puedo restablecer mi contraseña?
                </button>
                <div class="faq-answer" id="answer0">
                    <p>Para restablecer tu contraseña, ve a la página de inicio de sesión y haz clic en "Olvidé mi contraseña". Recibirás un enlace para restablecer tu contraseña en tu correo electrónico.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleAnswer(1)">
                    ¿Cómo puedo actualizar mi perfil?
                </button>
                <div class="faq-answer" id="answer1">
                    <p>Para actualizar tu perfil, deberas hablar con un administrador, para cambiar ya sea tu usuario o clave.</p>
                </div>
            </div>

            

            <!-- Agregar más preguntas frecuentes según sea necesario -->
        </div>
    </div>




    <div class="seccion">
    <nav class="navbar navbar-expand-lg" style="background-color:#006629;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarSupportedContent">
                <div>
                    <?php
                    if ($current_role == 'Director') {
                        echo '<p>Bienvenido Director.</p>';
                    } elseif ($current_role == 'Mentor') {
                        echo '<p>Bienvenido Mentor.</p>';
                    } elseif ($current_role == 'Responsable') {
                        echo '<p>Bienvenido Responsable.</p>';
                    }
                    ?>
                </div>
                <!-- Icono de usuario y menú desplegable -->
                <!-- Icono de usuario y menú desplegable -->
                <div class="user-menu position-relative" onclick="toggleDropdown()">
                    <i class="bi bi-chevron-compact-down"></i>
                    <i class="bi bi-person-fill"></i>
                    <span class="user-name"><?php echo htmlspecialchars($usuario); ?></span>
                    <div class="dropdown-menu">
                        <a href="logout.php">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Otros elementos aquí, como los iframes -->
        <div class="tablero">
                <?php if ($selected_dashboard == 'TAREAS'): ?>
                    <iframe title="Dashboard_Vinculacion - Tareas" width="1250" height="600" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false" frameborder="0" allowFullScreen="true"></iframe>
                <?php elseif ($selected_dashboard == 'PROYECTOS'): ?>
                    <iframe title="Dashboard_Vinculacion - Proyectos" width="1250" height="600" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false&pageName=d3a902f0a34f1c82b329" frameborder="0" allowFullScreen="true"></iframe>
                <?php elseif ($selected_dashboard == 'INSTITUCIONES'): ?>
                    <iframe title="Dashboard_Vinculacion - Instituciones" width="1250" height="600" src="https://app.powerbi.com/reportEmbed?reportId=2f567d7d-83fe-4285-a804-87af34c1c389&autoAuth=true&ctid=d9a7c315-62a6-4cb6-b905-be798b1d5076&navContentPaneEnabled=false&pageName=192b6339f0de780f4904" frameborder="0" allowFullScreen="true"></iframe>
                <?php endif; ?>
            </div>
        <div class="user-info">
            <i>UTM Dirección de Vinculacion © 2024.</i>
            <!-- <?php echo htmlspecialchars($usuario); ?> -->
        </div>
    </div>

    <script>
        // Función para abrir la ventana emergente
        function openHelpModal() {
            document.getElementById('helpModal').style.display = 'flex';
        }

        // Función para cerrar la ventana emergente
        function closeHelpModal() {
            document.getElementById('helpModal').style.display = 'none';
        }
    </script>

    <script>
        
        // Feedback
 
        function openFeedbackModal() {
            const feedbackModal = document.getElementById("feedbackModal");
            feedbackModal.style.display = "flex"; // Cambia a flex para mostrarlo
        }

        function closeFeedbackModal() {
            const feedbackModal = document.getElementById("feedbackModal");
            feedbackModal.style.display = "none"; // Cambia a none para ocultarlo
        }


    </script>

    <script>

        //FAQ
        function openFAQModal() {
            const faqModal = document.getElementById("faqModal");
            faqModal.style.display = "flex"; // Mostrar el modal
        }

        function closeFAQModal() {
            const faqModal = document.getElementById("faqModal");
            faqModal.style.display = "none"; // Ocultar el modal
        }

        function toggleAnswer(index) {
            const answer = document.getElementById(`answer${index}`);
            const isVisible = answer.style.display === "block";

            // Ocultar todas las respuestas
            const allAnswers = document.querySelectorAll('.faq-answer');
            allAnswers.forEach(item => item.style.display = "none");

            // Mostrar la respuesta actual solo si no estaba visible
            if (!isVisible) {
                answer.style.display = "block";
            }
        }


    </script>

</body>
</html>
