<?php
// Configuración de la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "biblioteca";

// Crear conexión
$conexion = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer codificación UTF-8
$conexion->set_charset("utf8mb4");