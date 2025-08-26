<?php
// Iniciar la sesión
session_start();

// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Verifica si la sesión es válida
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verifica si el formulario fue enviado y el ID de la membresía es válido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_membresia'])) {
    $id_usuario = $_SESSION['id'];
    $id_membresia = $conexion->real_escape_string($_POST['id_membresia']);

    // Obtener los datos del plan de membresía seleccionado
    $sql_plan = "SELECT precio, duracion_meses FROM membresias_disponibles WHERE id = ?";
    $stmt_plan = $conexion->prepare($sql_plan);
    $stmt_plan->bind_param("i", $id_membresia);
    $stmt_plan->execute();
    $result_plan = $stmt_plan->get_result();

    if ($result_plan->num_rows > 0) {
        $plan = $result_plan->fetch_assoc();
        $precio = $plan['precio'];
        $duracion_meses = $plan['duracion_meses'];
        
        // Calcular la fecha de vencimiento de la nueva membresía
        $nueva_fecha_vencimiento = date('Y-m-d H:i:s', strtotime("+$duracion_meses months"));
        
        // Actualizar el estado y fecha de vencimiento del usuario
        $sql_update = "UPDATE usuarios SET estado = 'activo', fecha_vencimiento = ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("si", $nueva_fecha_vencimiento, $id_usuario);
        $update_exitoso = $stmt_update->execute();
        
        // Registrar el pago en la tabla 'pagos'
        // NOTA: Se ha eliminado la columna 'descripcion' ya que no es necesaria para la compra.
        $sql_pago = "INSERT INTO pagos (id_usuario, id_membresia, monto, fecha) VALUES (?, ?, ?, NOW())";
        $stmt_pago = $conexion->prepare($sql_pago);
        $stmt_pago->bind_param("iid", $id_usuario, $id_membresia, $precio);
        $pago_exitoso = $stmt_pago->execute();

        $stmt_plan->close();
        $stmt_update->close();
        $stmt_pago->close();

        if ($update_exitoso && $pago_exitoso) {
            // Si todo fue bien, redirige al inicio con un mensaje de éxito
            header("Location: inicio.php?status=success");
            exit();
        }
    }
    // Si algo falló, redirige al inicio con un mensaje de error
    header("Location: inicio.php?status=error");
    exit();
} else {
    // Si no se recibió el ID de membresía, redirige con un error
    header("Location: inicio.php?status=error");
    exit();
}

// Cierra la conexión
$conexion->close();
?>