<?php
// Iniciar la sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Verificar si el usuario está autenticado y tiene rol de 'admin'
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Procesar el cambio de estado del ticket
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion_ticket'])) {
    $id_ticket = $conexion->real_escape_string($_POST['id_ticket']);
    $nuevo_estado = $conexion->real_escape_string($_POST['nuevo_estado']);

    $sql_update = "UPDATE tickets_soporte SET estado = ? WHERE id = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("si", $nuevo_estado, $id_ticket);
    $stmt_update->execute();
    $stmt_update->close();
}

// Consultar todos los tickets de soporte, uniendo con la tabla de usuarios para mostrar el nombre
$sql_tickets = "SELECT ts.id, ts.asunto, ts.mensaje, ts.estado, ts.fecha_creacion, u.nombre 
                FROM tickets_soporte ts
                JOIN usuarios u ON ts.id_usuario = u.id
                ORDER BY ts.fecha_creacion DESC";
$result_tickets = $conexion->query($sql_tickets);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giuseppe Gym - Soporte Admin</title>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            font-family: 'Arial', sans-serif;
            color: #fff;
            min-height: 100vh;
            padding: 2em;
        }
        .container {
            background: rgba(30, 30, 30, 0.95);
            padding: 3em;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            max-width: 1000px;
            margin: 0 auto;
        }
        h1 {
            color: #3498db;
            text-align: center;
        }
        .ticket-card {
            background-color: #2c3e50;
            padding: 1.5em;
            border-radius: 10px;
            margin-bottom: 1.5em;
            display: flex;
            flex-direction: column;
            gap: 1em;
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #444;
            padding-bottom: 0.5em;
            margin-bottom: 1em;
        }
        .ticket-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .ticket-info p {
            margin: 0.2em 0;
            font-size: 0.9em;
        }
        .ticket-info p span {
            font-weight: bold;
            color: #3498db;
        }
        .ticket-estado {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            text-transform: uppercase;
        }
        .estado-abierto { background-color: #e74c3c; }
        .estado-en_proceso { background-color: #f1c40f; }
        .estado-cerrado { background-color: #2ecc71; }
        .ticket-body {
            background-color: #333;
            padding: 1em;
            border-radius: 8px;
            text-align: left;
        }
        .ticket-body p {
            margin: 0;
            white-space: pre-wrap;
        }
        .ticket-actions {
            display: flex;
            gap: 1em;
            justify-content: flex-end;
        }
        .btn-action {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-action.process { background-color: #f39c12; }
        .btn-action.close { background-color: #27ae60; }
        .retroceder {
            display: inline-block;
            margin-top: 2em;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Tickets de Soporte</h1>

        <?php if ($result_tickets->num_rows > 0): ?>
            <?php while($ticket = $result_tickets->fetch_assoc()): ?>
                <div class="ticket-card">
                    <div class="ticket-header">
                        <div class="ticket-info">
                            <p><span>ID Ticket:</span> <?php echo htmlspecialchars($ticket['id']); ?></p>
                            <p><span>Usuario:</span> <?php echo htmlspecialchars($ticket['nombre']); ?></p>
                            <p><span>Fecha:</span> <?php echo htmlspecialchars($ticket['fecha_creacion']); ?></p>
                        </div>
                        <span class="ticket-estado estado-<?php echo str_replace(' ', '_', $ticket['estado']); ?>">
                            <?php echo htmlspecialchars($ticket['estado']); ?>
                        </span>
                    </div>
                    <div class="ticket-body">
                        <h4>Asunto: <?php echo htmlspecialchars($ticket['asunto']); ?></h4>
                        <p><?php echo htmlspecialchars($ticket['mensaje']); ?></p>
                    </div>
                    <div class="ticket-actions">
                        <form action="soporte_admin.php" method="POST">
                            <input type="hidden" name="id_ticket" value="<?php echo htmlspecialchars($ticket['id']); ?>">
                            <select name="nuevo_estado">
                                <option value="abierto" <?php echo ($ticket['estado'] == 'abierto') ? 'selected' : ''; ?>>Abierto</option>
                                <option value="en proceso" <?php echo ($ticket['estado'] == 'en proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                <option value="cerrado" <?php echo ($ticket['estado'] == 'cerrado') ? 'selected' : ''; ?>>Cerrado</option>
                            </select>
                            <button type="submit" name="accion_ticket" class="btn-action process">Actualizar Estado</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay tickets de soporte abiertos.</p>
        <?php endif; ?>

        <a href="admin.html" class="retroceder">Volver al Panel de Admin</a>
    </div>
</body>
</html>
