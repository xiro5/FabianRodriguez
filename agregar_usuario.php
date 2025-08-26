<?php
// Inicia la sesión
session_start();

// Incluye el archivo de conexión a la base de datos.
include 'conexion.php';

// Variables para mensajes de estado
$message = '';
$message_class = '';

// Verifica si la sesión es válida y si el usuario es un administrador
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    // Si no es un admin, redirige al login para proteger la página
    header("Location: login.php");
    exit();
}

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene y escapa las entradas del usuario
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $apellido = $conexion->real_escape_string($_POST['apellido']);
    $email = $conexion->real_escape_string($_POST['email']);
    $password_plano = $_POST['password'];
    $rol = 'cliente'; // El rol predeterminado para los usuarios agregados por el admin

    // Hashea la contraseña de texto plano
    $password_hasheada = password_hash($password_plano, PASSWORD_DEFAULT);

    // Prepara la consulta SQL para insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (nombre, apellido, email, clave, rol) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $apellido, $email, $password_hasheada, $rol);

    // Ejecuta la consulta y verifica si fue exitosa
    if ($stmt->execute()) {
        $message = "¡Usuario agregado con éxito!";
        $message_class = 'success';
    } else {
        // Maneja el error, por ejemplo, si el email ya existe
        if ($conexion->errno == 1062) {
            $message = "Error: El correo electrónico ya está registrado.";
        } else {
            $message = "Error al agregar usuario: " . $stmt->error;
        }
        $message_class = 'error';
    }

    $stmt->close();
}

// Cierra la conexión
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giuseppe Gym - Agregar Usuario</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(30, 30, 30, 0.95);
            padding: 2.5em;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        h1 {
            margin-bottom: 1em;
            color: #3498db;
        }
        .message {
            margin-bottom: 1.5em;
            font-weight: bold;
        }
        .success {
            color: #2ecc71;
        }
        .error {
            color: #e74c3c;
        }
        .form-group {
            margin-bottom: 1.5em;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.8em;
            border: none;
            border-radius: 8px;
            background: #444;
            color: #fff;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.5);
        }
        button {
            width: 100%;
            padding: 1em;
            border: none;
            border-radius: 8px;
            background-color: #3498db;
            color: white;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2980b9;
        }
        .back-link {
            display: block;
            margin-top: 1.5em;
            color: #ccc;
            font-size: 0.9em;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agregar Nuevo Usuario</h1>
        <!-- Muestra el mensaje de estado si existe -->
        <?php if (!empty($message)): ?>
            <p class="message <?php echo $message_class; ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        
        <form action="agregar_usuario.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Agregar Usuario</button>
        </form>
        <a href="admin.php" class="back-link">Volver al Panel de Admin</a>
    </div>
</body>
</html>
