<?php
// ===============================
// usuarios.php (BACKEND CORREGIDO)
// ===============================
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
    $res = $conexion->query("SELECT * FROM usuarios");
    while($row = $res->fetch_assoc()){
        echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['nombre']} {$row['apellido']}</td>
        <td>{$row['correo']}</td>
        <td>{$row['rol']}</td>
        <td>
        <button class='btn btn-warning btn-sm' onclick='editar({$row['id']})'>Editar</button>
        <button class='btn btn-danger btn-sm' onclick='eliminar({$row['id']})'>Eliminar</button>
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
    $apellido = $_POST['apellido'] ?? "";
    $correo = $_POST['correo'] ?? "";
    $rol = $_POST['rol'] ?? "usuario";

    if($id == ""){
        $password = $_POST['password'] ?? "";
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios(nombre, apellido, correo, password, rol) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $apellido, $correo, $passHash, $rol);
        
        if($stmt->execute()){
            echo "Usuario creado";
        } else {
            echo "Error al crear usuario";
        }

    } else {
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, correo=?, rol=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $apellido, $correo, $rol, $id);
        
        if($stmt->execute()){
            echo "Usuario actualizado";
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
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
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

    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        echo "Usuario eliminado";
    } else {
        echo "Error al eliminar";
    }
}
if($accion == "listarSelect"){
    $res = $conexion->query("SELECT id, nombre FROM usuarios ORDER BY nombre");
    echo "<option value=''>Seleccionar usuario...</option>";
    while($row = $res->fetch_assoc()){
        echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
    }
}
?>