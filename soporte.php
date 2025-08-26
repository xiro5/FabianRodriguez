<?php
// Iniciar la sesión
session_start();

// Si no existe una sesión de usuario, redirigir a la página de login.
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Variables para el mensaje de estado
$mensaje_estado = '';
$clase_mensaje = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $mensaje_estado = '¡Tu ticket de soporte ha sido enviado con éxito! Un administrador te responderá pronto.';
        $clase_mensaje = 'success';
    } elseif ($_GET['status'] == 'error') {
        $mensaje_estado = 'Hubo un error al enviar tu ticket. Por favor, inténtalo de nuevo.';
        $clase_mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giuseppe Gym - Soporte</title>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            font-family: 'Arial', sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: rgba(30, 30, 30, 0.95);
            padding: 3em;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }
        h1 {
            color: #3498db;
            margin-bottom: 1em;
        }
        .form-group {
            margin-bottom: 1.5em;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 0.5em;
            color: #ccc;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #555;
            border-radius: 8px;
            background-color: #333;
            color: #fff;
            font-size: 1em;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 150px;
        }
        .btn-submit {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #27ae60;
        }
        .alert {
            padding: 1em;
            border-radius: 8px;
            margin-bottom: 1.5em;
        }
        .alert.success {
            background-color: #2ecc71;
            color: white;
        }
        .alert.error {
            background-color: #e74c3c;
            color: white;
        }
        .back-link {
            display: block;
            margin-top: 2em;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Soporte al Usuario</h1>
        <?php if (!empty($mensaje_estado)): ?>
            <div class="alert <?php echo $clase_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje_estado); ?>
            </div>
        <?php endif; ?>

        <form action="procesar_soporte.php" method="POST">
            <div class="form-group">
                <label for="asunto">Asunto:</label>
                <input type="text" id="asunto" name="asunto" required>
            </div>
            <div class="form-group">
                <label for="mensaje">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Enviar Ticket</button>
        </form>

        <a href="inicio.php" class="back-link">Volver al Inicio</a>
    </div>
</body>
</html>
