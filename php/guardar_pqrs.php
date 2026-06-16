<?php
require_once "../CONFIG_DATABASE/config.php";

header('Content-Type: application/json');

$nombre  = $_POST['nombre'] ?? '';
$correo  = $_POST['correo'] ?? '';
$tipo    = $_POST['tipo'] ?? '';
$asunto  = $_POST['asunto'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

if (!$nombre || !$correo || !$tipo || !$asunto || !$mensaje) {
    echo json_encode(["status" => "error", "msg" => "Campos incompletos"]);
    exit;
}

$sql = "INSERT INTO pqrs (nombre, correo, tipo, asunto, mensaje)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssss", $nombre, $correo, $tipo, $asunto, $mensaje);

if ($stmt->execute()) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Error al guardar"]);
}