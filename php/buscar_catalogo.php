<?php
require_once "../CONFIG_DATABASE/config.php";

$q = $_GET['q'] ?? '';
$categoria = $_GET['categoria'] ?? '';

$sql = "SELECT * FROM libros WHERE stock > 0";

$params = [];
$types = "";

// 🔍 búsqueda flexible
if (!empty($q)) {
    $sql .= " AND (titulo LIKE ? OR autor LIKE ?)";
    $qLike = "%" . $q . "%";
    $params[] = $qLike;
    $params[] = $qLike;
    $types .= "ss";
}

// 🎯 filtro categoría
if (!empty($categoria)) {
    $sql .= " AND categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

$sql .= " LIMIT 20";

$stmt = $conexion->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$libros = [];

while($row = $result->fetch_assoc()) {
    $libros[] = $row;
}

echo json_encode($libros);