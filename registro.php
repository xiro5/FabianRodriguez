<?php
// Incluye el archivo de conexión a la base de datos
// Usa un bloque de try-catch para manejar errores de inclusión
try {
    include 'conexion.php';
    if (!isset($conexion) || $conexion->connect_error) {
        throw new Exception("Error: no se pudo establecer la conexión a la base de datos.");
    }
} catch (Exception $e) {
    // Si la conexión falla, muestra un mensaje de error y termina la ejecución
    // Esto evita que el resto del script se cargue si hay un problema
    die("Error crítico: " . $e->getMessage());
}

// Variable para almacenar mensajes de error o éxito
$message = '';

// Verifica si la solicitud es de tipo POST (es decir, el formulario se ha enviado)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene y escapa las entradas del usuario para prevenir inyecciones SQL
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $apellido = $conexion->real_escape_string($_POST['apellido']);
    $email = $conexion->real_escape_string($_POST['email']);
    $password_plano = $_POST['password'];

    // Hashea la contraseña de texto plano antes de guardarla
    // Esto es CRUCIAL para la seguridad
    $password_hasheada = password_hash($password_plano, PASSWORD_DEFAULT);

    // Rol por defecto para un nuevo usuario
    $rol = 'cliente';

    // Prepara y ejecuta la consulta SQL para insertar el nuevo usuario
    // Las sentencias preparadas evitan la inyección SQL
    $sql = "INSERT INTO usuarios (nombre, apellido, email, clave, rol) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $apellido, $email, $password_hasheada, $rol);

    if ($stmt->execute()) {
        $message = "¡Registro exitoso! Ahora puedes iniciar sesión.";
    } else {
        // Manejo de errores (por ejemplo, si el email ya existe)
        if ($conexion->errno == 1062) {
            $message = "Error: El correo electrónico ya está registrado.";
        } else {
            $message = "Error al registrarse: " . $stmt->error;
        }
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
    <title>Giuseppe Gym - Registro</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
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
            color: #2ecc71;
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
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.5);
        }
        button {
            width: 100%;
            padding: 1em;
            border: none;
            border-radius: 8px;
            background-color: #2ecc71;
            color: white;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #27ae60;
        }
        .login-link {
            display: block;
            margin-top: 1.5em;
            color: #ccc;
            font-size: 0.9em;
            text-decoration: none;
        }
        .login-link:hover {
            text-decoration: underline;
        }
        .message {
            margin-bottom: 1em;
        }
        .success {
            color: #2ecc71;
        }
        .error {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Registrarse</h1>
        <!-- Muestra el mensaje de éxito o error si existe -->
        <?php if (!empty($message)): ?>
            <p class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <form action="registro.php" method="POST">
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
            <button type="submit">Crear Cuenta</button>
        </form>
        <a href="login.php" class="login-link">¿Ya tienes una cuenta? Inicia sesión aquí</a>
    </div>
</body>
</html>
