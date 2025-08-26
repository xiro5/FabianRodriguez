<?php
$host = "192.168.0.200"; // Servidor de MySQL
$usuario = "Estudiante";   // Usuario de MySQL por defecto en XAMPP
$password = "Estudiante2025";      // Contraseña (vacía por defecto en XAMPP)
$base_datos = "GYM"; // Nombre de tu base de datos

// Crear la conexión
$conexion = new mysqli($host, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

// Opcional: definir el charset para acentos y ñ
$conexion->set_charset("utf8");
?>
