<?php
session_start();
require_once "../CONFIG_DATABASE/config.php";

header("Content-Type: application/json");

$correo = $_POST['correo'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE correo = ? AND rol = 'admin'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $usuario = $result->fetch_assoc();

    if (password_verify($password, $usuario['password'])) {

        $_SESSION['admin_id'] = $usuario['id'];
        $_SESSION['admin_nombre'] = $usuario['nombre'];

        echo json_encode(["status" => "ok"]);
        exit;
    }
}

echo json_encode([
    "status" => "error",
    "message" => "Acceso denegado"
]);