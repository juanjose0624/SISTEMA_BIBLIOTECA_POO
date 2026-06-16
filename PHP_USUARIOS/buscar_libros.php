<?php
require_once "../CONFIG_DATABASE/config.php";

$busqueda = $_GET['q'] ?? '';

$sql = "SELECT id, titulo 
        FROM libros 
        WHERE titulo LIKE ? 
        AND stock > 0 
        LIMIT 10";

$stmt = $conexion->prepare($sql);
$param = "%" . $busqueda . "%";
$stmt->bind_param("s", $param);
$stmt->execute();

$result = $stmt->get_result();

$libros = [];

while($row = $result->fetch_assoc()) {
    $libros[] = $row;
}

echo json_encode($libros);