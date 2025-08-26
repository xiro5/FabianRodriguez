<?php
// Iniciar la sesión
session_start();

// Si no existe una sesión de usuario O si el rol no es 'admin', redirigir al login.
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Giuseppe Gym - Panel de Administración</title>
<style>
    body {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
        font-family: Arial, sans-serif;
        text-align: center;
        padding-top: 50px;
    }
    h1 {
        font-size: 2.5em;
        color: #e67e22;
        margin-bottom: 20px;
    }
    p {
        font-size: 1.2em;
        margin-bottom: 30px;
    }
    .container {
        padding: 20px;
    }
    .btn-group {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .btn {
        background: #3498db;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        color: white;
        font-size: 1em;
        transition: background-color 0.3s ease;
    }
    .btn:hover {
        background-color: #2980b9;
    }
</style>
</head>
<body>
    <div class="container">
        <h1>¡Bienvenido, Administrador <?php echo htmlspecialchars($_SESSION['nombre']); ?>! 👑</h1>
        <p>Tu rol es: <strong>Admin</strong></p>
        
        <div class="btn-group">
            <a href="agregar_usuario.php" class="btn">Gestionar Usuarios</a>
            <a href="membresias.php" class="btn">Gestión de Membresías</a>
            <a href="#" class="btn">Ver Reportes</a>
            <a href="logout.php" class="btn" style="background-color: #e74c3c;">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>
