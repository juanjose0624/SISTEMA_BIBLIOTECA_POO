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
    $res = $conexion->query("SELECT * FROM libros");

    while($row = $res->fetch_assoc()){
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['titulo']}</td>
            <td>{$row['autor']}</td>
            <td>{$row['categoria']}</td>
            <td>{$row['isbn']}</td>
            <td>{$row['editorial']}</td>
            <td>{$row['anio_publicacion']}</td>
            <td>{$row['stock']}</td>
            <td>
                <div class='actions'>
                    <button class='btn btn-warning' onclick='editar({$row['id']})'>Editar</button>
                    <button class='btn btn-danger' onclick='eliminar({$row['id']})'>Eliminar</button>
                </div>
            </td>
        </tr>";
    }
}
if($accion == "listarSelect"){
    $res = $conexion->query("SELECT id, titulo FROM libros WHERE stock > 0 ORDER BY titulo");
    echo "<option value=''>Seleccionar libro...</option>";
    while($row = $res->fetch_assoc()){
        echo "<option value='{$row['id']}'>{$row['titulo']}</option>";
    }
}
// ===============================
// GUARDAR (CREAR / EDITAR)
// ===============================
if($accion == "guardar"){

    $id = $_POST['id'] ?? "";
    $titulo = $_POST['titulo'] ?? "";
    $autor = $_POST['autor'] ?? "";
    $categoria = $_POST['categoria'] ?? "";
    $isbn = $_POST['isbn'] ?? "";
    $editorial = $_POST['editorial'] ?? "";
    $anio = $_POST['anio_publicacion'] ?? null;
    $stock = $_POST['stock'] ?? 1;

    // VALIDACIÓN BÁSICA
    if($titulo == "" || $autor == ""){
        echo "Título y autor son obligatorios";
        exit;
    }

    // CREAR
    if($id == ""){

        $sql = "INSERT INTO libros 
        (titulo, autor, categoria, isbn, editorial, anio_publicacion, stock) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssii", $titulo, $autor, $categoria, $isbn, $editorial, $anio, $stock);

        if($stmt->execute()){
            echo "Libro creado";
        } else {
            echo "Error al crear libro";
        }

    } else {
        // ACTUALIZAR
        $sql = "UPDATE libros SET 
            titulo=?,
            autor=?,
            categoria=?,
            isbn=?,
            editorial=?,
            anio_publicacion=?,
            stock=?
            WHERE id=?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssiii", $titulo, $autor, $categoria, $isbn, $editorial, $anio, $stock, $id);

        if($stmt->execute()){
            echo "Libro actualizado";
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

    $stmt = $conexion->prepare("SELECT * FROM libros WHERE id=?");
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

    $stmt = $conexion->prepare("DELETE FROM libros WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        echo "Libro eliminado";
    } else {
        echo "Error al eliminar";
    }
}
?>