<?php

session_start(); // Iniciar sesión

header('Content-Type: application/json; charset=utf-8');

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit;
}

// Recibir datos
$correo   = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

// Validación básica
if (!$correo || !$password) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios"]);
    exit;
}

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Correo inválido"]);
    exit;
}

// Conexión
define('APP_INIT', true);
require_once "../CONFIG_DATABASE/config.php";

// Buscar usuario
$sql = "SELECT id, nombre, apellido, password FROM usuarios WHERE correo = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {

    $usuario = $resultado->fetch_assoc();

    // Verificar contraseña
    if (password_verify($password, $usuario['password'])) {

        // Guardar datos en sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_apellido'] = $usuario['apellido'];

        echo json_encode([
            "status" => "ok",
            "message" => "Login exitoso"
        ]);

    } else {
        echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
}

// Cerrar
$stmt->close();
$conexion->close();