<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "No autenticado"
    ]);
    exit;
}

require_once "../CONFIG_DATABASE/config.php";

date_default_timezone_set("America/Bogota");

$usuario_id = $_SESSION['usuario_id'];
$libro_id = $_POST['libro_id'] ?? null;

if (!$libro_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Libro no recibido"
    ]);
    exit;
}

# 🔒 1. Verificar límite de solicitudes activas
$sql = "SELECT COUNT(*) as total 
        FROM solicitudes 
        WHERE usuario_id = ? AND estado = 'pendiente'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['total'] >= 3) {
    echo json_encode([
        "status" => "error",
        "message" => "Tienes demasiadas solicitudes pendientes"
    ]);
    exit;
}

# 📚 2. Verificar stock (solo informativo, no descontar aún)
$sql = "SELECT stock FROM libros WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $libro_id);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro || $libro['stock'] <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Sin stock disponible"
    ]);
    exit;
}

# 📝 3. Crear SOLICITUD
$sql = "INSERT INTO solicitudes (usuario_id, libro_id, fecha_solicitud, estado)
        VALUES (?, ?, NOW(), 'pendiente')";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $libro_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "ok",
        "message" => "Solicitud enviada al administrador"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Error al enviar solicitud"
    ]);
}

$stmt->close();
$conexion->close();