<?php
// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Variable para almacenar mensajes de error
$error = '';

// Verifica si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que existan los campos de email y clave
    if (isset($_POST['email']) && isset($_POST['clave'])) {
        $email = $conexion->real_escape_string($_POST['email']);
        $clave_plana = $_POST['clave'];

        // Buscar usuario por email
        $sql = "SELECT id, nombre, clave, rol FROM usuarios WHERE email = '$email'";
        $result = $conexion->query($sql);

        // Si se encuentra el usuario
        if ($result && $result->num_rows > 0) {
            $usuario = $result->fetch_assoc();

            // Verificar la contraseña hasheada
            if (password_verify($clave_plana, $usuario['clave'])) {
                // Iniciar la sesión y almacenar los datos del usuario
                session_start();
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];

                // Redirigir según el rol del usuario
                if ($usuario['rol'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: inicio.php");
                }
                exit();
            } else {
                $error = "Email o clave incorrectos.";
            }
        } else {
            $error = "Email no encontrado.";
        }
    } else {
        $error = "Debes ingresar tu email y clave.";
    }
}

// Cierra la conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giuseppe Gym - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            font-family: 'Arial', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .login-container {
            background: rgba(30, 30, 30, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 400px;
            text-align: center;
        }

        .logo-section {
            margin-bottom: 2em;
        }

        .logo-section img {
            height: 80px;
        }

        h1 {
            color: #3498db;
            margin-bottom: 1em;
            font-weight: 300;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            color: white;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-login {
            background: #2ecc71;
            border: none;
            padding: 15px;
            border-radius: 10px;
            color: white;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #27ae60;
        }

        .error-message {
            color: #e74c3c;
            margin-top: 15px;
            font-size: 0.9em;
        }

        .registration-message {
            margin-top: 20px;
            color: #ccc;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <img src="logo.png" alt="Giuseppe Gym Logo">
        </div>
        <h1>Iniciar Sesión</h1>
        <!-- Muestra el mensaje de error si existe -->
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <input type="email" name="email" id="email" class="form-control" placeholder="Correo Electrónico" required>
            <input type="password" name="clave" id="clave" class="form-control" placeholder="Contraseña" required>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
        <p class="registration-message">
            Si no tienes una cuenta, contacta a la administración.
        </p>
    </div>
</body>
</html>
