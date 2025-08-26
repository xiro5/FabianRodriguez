<?php
// Iniciar la sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['asunto']) && isset($_POST['mensaje'])) {
    $id_usuario = $_SESSION['id'];
    $asunto = $conexion->real_escape_string($_POST['asunto']);
    $mensaje = $conexion->real_escape_string($_POST['mensaje']);
    $estado = 'abierto';

    // Preparar la consulta SQL para insertar el ticket
    $sql = "INSERT INTO tickets_soporte (id_usuario, asunto, mensaje, estado) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isss", $id_usuario, $asunto, $mensaje, $estado);

    if ($stmt->execute()) {
        // Redirigir a la página de soporte con un mensaje de éxito
        header("Location: soporte.php?status=success");
        exit();
    } else {
        // Redirigir con un mensaje de error
        header("Location: soporte.php?status=error");
        exit();
    }

    $stmt->close();
} else {
    // Si no se envió el formulario correctamente, redirigir al inicio
    header("Location: inicio.php");
    exit();
}

$conexion->close();
?>
