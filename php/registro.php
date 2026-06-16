<?php
require_once "../CONFIG_DATABASE/config.php";
// Indicamos que la respuesta será JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit;
}

// Recibir datos
$nombre     = trim($_POST['nombre'] ?? '');
$apellido   = trim($_POST['apellido'] ?? '');
$edad       = intval($_POST['edad'] ?? 0);
$tipo_doc   = trim($_POST['tipo_doc'] ?? '');
$num_doc    = trim($_POST['num_doc'] ?? '');
$celular    = trim($_POST['celular'] ?? '');
$correo     = trim($_POST['correo'] ?? '');
$password   = $_POST['password'] ?? '';

// Validaciones básicas
if (!$nombre || !$apellido || !$edad || !$tipo_doc || !$num_doc || !$celular || !$correo || !$password) {
    echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios"]);
    exit;
}

if ($edad < 15) {
    echo json_encode(["status" => "error", "message" => "Debes tener al menos 15 años"]);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Correo inválido"]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(["status" => "error", "message" => "La contraseña debe tener al menos 6 caracteres"]);
    exit;
}
// SOLO LETRAS (nombre y apellido)
if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/", $nombre)) {
    echo json_encode(["status" => "error", "message" => "El nombre solo debe contener letras"]);
    exit;
}

if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/", $apellido)) {
    echo json_encode(["status" => "error", "message" => "El apellido solo debe contener letras"]);
    exit;
}

// SOLO NÚMEROS (documento)
if (!preg_match("/^[0-9]+$/", $num_doc)) {
    echo json_encode(["status" => "error", "message" => "El documento debe contener solo números"]);
    exit;
}

// CELULAR EXACTO 10 DÍGITOS
if (!preg_match("/^[0-9]{10}$/", $celular)) {
    echo json_encode(["status" => "error", "message" => "El celular debe tener 10 dígitos"]);
    exit;
}
// Encriptar contraseña
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "biblioteca");

// Verificar conexión
if ($conexion->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión"]);
    exit;
}

// Verificar si el correo ya existe
$sqlCheck = "SELECT id FROM usuarios WHERE correo = ?";
$stmtCheck = $conexion->prepare($sqlCheck);
$stmtCheck->bind_param("s", $correo);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "El correo ya está registrado"]);
    exit;
}

// Insertar usuario
$sql = "INSERT INTO usuarios 
(nombre, apellido, edad, tipo_doc, num_doc, celular, correo, password) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "ssisssss",
    $nombre,
    $apellido,
    $edad,
    $tipo_doc,
    $num_doc,
    $celular,
    $correo,
    $passwordHash
);

// Ejecutar
if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "message" => "Usuario registrado correctamente"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al registrar"]);
}

// Cerrar conexiones
$stmt->close();
$conexion->close();