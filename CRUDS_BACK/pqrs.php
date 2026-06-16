<?php
require_once "../CONFIG_DATABASE/config.php";

// Mostrar errores (solo desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validar acción
if(!isset($_GET['accion'])){
    die("Acción no definida");
}

$accion = $_GET['accion'];

// ===============================
// LISTAR
// ===============================
if($accion == "listar"){
    $res = $conexion->query("SELECT * FROM pqrs ORDER BY fecha DESC");

    while($row = $res->fetch_assoc()){

        $estadoSlug = strtolower(str_replace(" ", "-", $row['estado']));

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['nombre']}</td>
            <td style='color:#6b7280'>{$row['correo']}</td>
            <td>{$row['tipo']}</td>
            <td>{$row['asunto']}</td>
            <td><span class='badge badge-{$estadoSlug}'>{$row['estado']}</span></td>
            <td style='color:#6b7280'>{$row['fecha']}</td>
            <td>
                <div class='actions'>
                    <button class='btn btn-warning' onclick='editar({$row['id']})'>Editar</button>
                    <button class='btn btn-danger' onclick='eliminar({$row['id']})'>Eliminar</button>
                </div>
            </td>
        </tr>";
    }
}

// ===============================
// GUARDAR (CREAR / EDITAR)
// ===============================
if($accion == "guardar"){

    $id = $_POST['id'] ?? "";
    $nombre = $_POST['nombre'] ?? "";
    $correo = $_POST['correo'] ?? "";
    $tipo = $_POST['tipo'] ?? "";
    $asunto = $_POST['asunto'] ?? "";
    $mensaje = $_POST['mensaje'] ?? "";
    $estado = $_POST['estado'] ?? "Pendiente";

    // VALIDACIÓN BÁSICA
    if($nombre == "" || $correo == "" || $tipo == "" || $asunto == "" || $mensaje == ""){
        echo "Todos los campos son obligatorios";
        exit;
    }

    // CREAR
    if($id == ""){

        $sql = "INSERT INTO pqrs 
        (nombre, correo, tipo, asunto, mensaje, estado) 
        VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssss", $nombre, $correo, $tipo, $asunto, $mensaje, $estado);

        if($stmt->execute()){
            echo "PQRS creada";
        } else {
            echo "Error al crear";
        }

    } else {
        // ACTUALIZAR
        $sql = "UPDATE pqrs SET 
            nombre=?,
            correo=?,
            tipo=?,
            asunto=?,
            mensaje=?,
            estado=?
            WHERE id=?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssssi", $nombre, $correo, $tipo, $asunto, $mensaje, $estado, $id);

        if($stmt->execute()){
            echo "PQRS actualizada";
        } else {
            echo "Error al actualizar";
        }
    }
}

// ===============================
// EDITAR (DEVUELVE JSON)
// ===============================
if($accion == "editar"){

    header('Content-Type: application/json');

    $id = $_GET['id'];

    $stmt = $conexion->prepare("SELECT * FROM pqrs WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();

    echo json_encode($res->fetch_assoc());
}

// ===============================
// ELIMINAR
// ===============================
if($accion == "eliminar"){

    $id = $_GET['id'];

    $stmt = $conexion->prepare("DELETE FROM pqrs WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        echo "PQRS eliminada";
    } else {
        echo "Error al eliminar";
    }
}
?>