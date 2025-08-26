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

// Variables para el mensaje de estado
$mensaje_estado = '';
$clase_mensaje = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion'])) {
    $id_usuario = $conexion->real_escape_string($_POST['id_usuario']);
    $accion = $conexion->real_escape_string($_POST['accion']);
    $motivo = isset($_POST['motivo']) ? $conexion->real_escape_string($_POST['motivo']) : NULL;
    $tiempo = isset($_POST['tiempo']) ? $conexion->real_escape_string($_POST['tiempo']) : NULL;

    // Lógica para cancelar
    if ($accion == 'cancelar') {
        $estado = 'inactivo';
        $sql = "UPDATE usuarios SET estado = ?, motivo_suspension = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssi", $estado, $motivo, $id_usuario);
        
        if ($stmt->execute()) {
            $mensaje_estado = "Membresía del usuario ID: $id_usuario ha sido cancelada.";
            $clase_mensaje = 'success';
        } else {
            $mensaje_estado = "Error al cancelar la membresía: " . $stmt->error;
            $clase_mensaje = 'error';
        }
        $stmt->close();
    }
    
    // Lógica para suspender
    else if ($accion == 'suspender') {
        $estado = 'suspendido';
        $fecha_fin_suspension = date('Y-m-d', strtotime("+$tiempo"));
        $sql = "UPDATE usuarios SET estado = ?, motivo_suspension = ?, fecha_fin_suspension = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $estado, $motivo, $fecha_fin_suspension, $id_usuario);
        
        if ($stmt->execute()) {
            $mensaje_estado = "Membresía del usuario ID: $id_usuario ha sido suspendida hasta el $fecha_fin_suspension.";
            $clase_mensaje = 'success';
        } else {
            $mensaje_estado = "Error al suspender la membresía: " . $stmt->error;
            $clase_mensaje = 'error';
        }
        $stmt->close();
    }
    
    // Lógica para reactivar
    else if ($accion == 'reactivar') {
        $estado = 'activo';
        $sql = "UPDATE usuarios SET estado = ?, motivo_suspension = NULL, fecha_fin_suspension = NULL WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $estado, $id_usuario);

        if ($stmt->execute()) {
            $mensaje_estado = "Membresía del usuario ID: $id_usuario ha sido reactivada.";
            $clase_mensaje = 'success';
        } else {
            $mensaje_estado = "Error al reactivar la membresía: " . $stmt->error;
            $clase_mensaje = 'error';
        }
        $stmt->close();
    }
}

// Consultar usuarios con estado "suspendido" o "inactivo" para la sección de reactivación
$sql_inactivos = "SELECT id, nombre, estado, motivo_suspension, fecha_fin_suspension FROM usuarios WHERE estado IN ('suspendido', 'inactivo') ORDER BY nombre ASC";
$result_inactivos = $conexion->query($sql_inactivos);

// Cierra la conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giuseppe Gym - Gestión de Membresías</title>
    <style>
        body {
            background-color: #111;
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
            padding: 2em;
        }

        h1 {
            font-size: 2em;
            margin-top: 20px;
        }

        h2 {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5em;
            margin-bottom: 1em;
        }

        .container {
            background: rgba(30, 30, 30, 0.95);
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            max-width: 800px;
            margin: 2em auto;
        }

        .alert {
            padding: 1em;
            border-radius: 8px;
            margin-bottom: 1.5em;
            font-weight: bold;
        }

        .alert.success {
            background-color: #2ecc71;
            color: white;
        }
        .alert.error {
            background-color: #e74c3c;
            color: white;
        }

        .form-section {
            margin-bottom: 2em;
        }

        .campo {
            margin: 15px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        label {
            background-color: transparent;
            border: 1px solid white;
            border-radius: 20px 0 0 20px;
            padding: 8px 15px;
            font-size: 0.9em;
        }

        input, select {
            border: 1px solid white;
            border-left: none;
            border-radius: 0 20px 20px 0;
            padding: 8px;
            width: 200px;
            background-color: #333;
            color: white;
        }

        button {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            margin: 15px;
            transition: background-color 0.3s ease;
        }

        button.cancelar-btn {
            background-color: #e74c3c;
        }
        button.suspender-btn {
            background-color: #f39c12;
        }
        button.reactivar-btn {
            background-color: #2ecc71;
        }

        .retroceder {
            background-color: red;
            margin-top: 20px;
        }

        a {
            color: cyan;
            text-decoration: none;
            display: block;
            margin-top: 20px;
        }

        .reactivacion-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1em;
        }

        .reactivacion-tabla th, .reactivacion-tabla td {
            padding: 12px;
            border: 1px solid #444;
            text-align: left;
        }

        .reactivacion-tabla th {
            background-color: #34495e;
        }

        .reactivacion-tabla tr:nth-child(even) {
            background-color: #2c3e50;
        }

        .reactivacion-tabla .btn-cell {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Membresías</h1>

        <?php if (!empty($mensaje_estado)): ?>
            <div class="alert <?php echo $clase_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje_estado); ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h2>Cancelar o Suspender Membresía</h2>
            <form action="membresias.php" method="post">
                <div class="campo">
                    <label>ID usuario:</label>
                    <input type="text" name="id_usuario" required>
                </div>
                <div class="campo">
                    <label>Motivo:</label>
                    <input type="text" name="motivo" required>
                </div>
                <div class="campo">
                    <label>Tiempo de suspensión:</label>
                    <select name="tiempo">
                        <option value="1 week">1 semana</option>
                        <option value="1 month">1 mes</option>
                        <option value="3 months">3 meses</option>
                    </select>
                </div>
                <button type="submit" name="accion" value="suspender" class="suspender-btn">Suspender</button>
                <button type="submit" name="accion" value="cancelar" class="cancelar-btn">Cancelar</button>
            </form>
        </div>

        <div class="reactivacion-section">
            <h2>Reactivar Membresías</h2>
            <?php if ($result_inactivos->num_rows > 0): ?>
                <table class="reactivacion-tabla">
                    <thead>
                        <tr>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Motivo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result_inactivos->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['estado']); ?></td>
                                <td><?php echo htmlspecialchars($row['motivo_suspension']); ?></td>
                                <td class="btn-cell">
                                    <form action="membresias.php" method="post">
                                        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <button type="submit" name="accion" value="reactivar" class="reactivar-btn">Reactivar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php $result_inactivos->close(); ?>
            <?php else: ?>
                <p>No hay membresías suspendidas o canceladas para reactivar.</p>
            <?php endif; ?>
        </div>
        
        <a href="admin.php" class="retroceder">Volver al Panel de Admin</a>
    </div>
</body>
</html>
