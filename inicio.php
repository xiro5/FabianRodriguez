<?php
// Iniciar la sesión
session_start();

// Si no existe una sesión de usuario, redirigir a la página de login.
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Obtiene el ID del usuario de la sesión
$id_usuario = $_SESSION['id'];

// Consultar los datos del usuario para mostrar el estado de su membresía
$sql_usuario = "SELECT nombre, estado, fecha_fin_suspension FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql_usuario);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result_usuario = $stmt->get_result();
$usuario_data = $result_usuario->fetch_assoc();
$stmt->close();

// Consultar los planes de membresía disponibles
$sql_membresias = "SELECT id, nombre, descripcion, precio FROM membresias_disponibles ORDER BY precio ASC";
$result_membresias = $conexion->query($sql_membresias);

// Variable para el mensaje de estado (éxito o error)
$mensaje_estado = '';
$clase_mensaje = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $mensaje_estado = '¡Compra realizada con éxito! ¡Bienvenido!';
        $clase_mensaje = 'success';
    } elseif ($_GET['status'] == 'error') {
        $mensaje_estado = 'Hubo un error al procesar tu compra. Intenta de nuevo.';
        $clase_mensaje = 'error';
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giuseppe Gym - Inicio Cliente</title>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #fff;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2em;
        }

        .container {
            background: rgba(30, 30, 30, 0.95);
            padding: 3em;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 900px;
            text-align: center;
        }

        h1 {
            color: #3498db;
            margin-bottom: 0.5em;
        }
        
        h2 {
            margin-top: 1.5em;
            color: #2ecc71;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5em;
        }
        
        p {
            font-size: 1.1em;
            margin: 1em 0;
        }
        
        .status {
            background-color: #2c3e50;
            padding: 1em;
            border-radius: 10px;
            margin-top: 1em;
        }

        .status p {
            font-weight: bold;
        }
        
        .status .activo {
            color: #27ae60;
        }
        
        .status .suspendido {
            color: #f1c40f;
        }

        .alert {
            padding: 15px;
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

        .planes-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 2em;
        }

        .plan-card {
            background-color: rgba(44, 62, 80, 0.8);
            border-radius: 8px;
            padding: 2em;
            width: 250px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
        }

        .plan-card h3 {
            color: #3498db;
            margin-bottom: 0.5em;
        }

        .plan-card .precio {
            font-size: 2.0em;
            font-weight: bold;
            color: #fff;
            margin-bottom: 0.5em;
        }

        .plan-card .precio small {
            font-size: 0.3em;
        }

        .plan-card p {
            font-size: 0.9em;
            margin-bottom: 1.5em;
            color: #ccc;
        }
        
        .buy-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 1em 2em;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .buy-btn:hover {
            background-color: #27ae60;
        }
        
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 1em 2em;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 2em;
        }

        .logout-btn:hover {
            background-color: #df7468ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($usuario_data['nombre']); ?>! 👋</h1>
        
        <?php if (!empty($mensaje_estado)): ?>
            <div class="alert <?php echo $clase_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje_estado); ?>
            </div>
        <?php endif; ?>

        <div class="status">
            <p>Estado de tu membresía: 
                <span class="<?php echo strtolower($usuario_data['estado']); ?>">
                    <?php echo htmlspecialchars($usuario_data['estado']); ?>
                </span>
            </p>
            <?php if ($usuario_data['estado'] == 'suspendido'): ?>
                <p>Tu membresía se reanudará el: <?php echo htmlspecialchars($usuario_data['fecha_fin_suspension']); ?></p>
            <?php endif; ?>
        </div>

        <h2>Compra tu Membresía</h2>
        <div class="planes-container">
            <?php while($plan = $result_membresias->fetch_assoc()): ?>
            <div class="plan-card">
                <h3><?php echo htmlspecialchars($plan['nombre']); ?></h3>
                <p><?php echo htmlspecialchars($plan['descripcion']); ?></p>
                <div class="precio">$<?php echo htmlspecialchars(number_format($plan['precio'], 2)); ?></div>
                <form action="procesar_compra.php" method="POST">
                    <input type="hidden" name="id_membresia" value="<?php echo $plan['id']; ?>">
                    <button type="submit" class="buy-btn">Comprar Ahora</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
        
        <?php $result_membresias->close(); ?>

        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </div>
</body>
</html>
