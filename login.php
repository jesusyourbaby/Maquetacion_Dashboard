<?php
session_start();

// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = ""; // Cambiar si se tiene una contraseña diferente
$dbname = "spv";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

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
        echo "Usuario o contraseña incorrecta";
    }
} else {
    // Redirigir al formulario de inicio de sesión si no se han enviado los datos
    header("Location: login.html");
    exit;
}

$conn->close();
?>
