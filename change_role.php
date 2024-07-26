<?php
session_start();
include 'conexion.php'; // Asegúrate de que este archivo contiene la lógica para conectar a tu base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_SESSION['usuario'];
    $nuevo_rol = $_POST['roles'];

    // Actualiza el rol en la base de datos
    $stmt = $conn->prepare("UPDATE permiso SET rol = ? WHERE usuario = ?");
    $stmt->bind_param("ss", $nuevo_rol, $usuario);
    $stmt->execute();
    $stmt->close();

    // Actualiza el rol en la sesión
    $_SESSION['rol'] = $nuevo_rol;

    // Redirige a la página principal o muestra un mensaje de éxito
    header("Location: index.php");
    exit();
}
?>
