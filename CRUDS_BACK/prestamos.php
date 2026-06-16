<?php
require_once "../CONFIG_DATABASE/config.php";

// Conexión usada como objeto principal ($conexion)
// Aquí hay uso de métodos ->query(), ->prepare(), etc.

ini_set('display_errors', 1);
error_reporting(E_ALL);

if(!isset($_GET['accion'])){
    die("Acción no definida");
}

// Atributo recibido desde URL
$accion = $_GET['accion'];


// ===============================
// LISTAR PRÉSTAMOS (ADMIN)
// ===============================
// Aquí se maneja una posible "clase" Prestamo desde la BD
if($accion == "listar"){

    $sql = "SELECT p.*, u.nombre AS usuario, l.titulo AS libro
            FROM prestamos p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            INNER JOIN libros l ON p.libro_id = l.id
            ORDER BY p.id DESC";

    // Método del objeto conexión
    $res = $conexion->query($sql);

    while($row = $res->fetch_assoc()){

        // Atributos del préstamo
        $slug    = strtolower($row['estado']);
        $entrega = $row['fecha_entrega'] ?: '—';

        // Encapsulamiento visual en botones
        $botones = "<div class='actions'>";

        // Polimorfismo básico según estado
        if($row['estado'] == 'prestado'){
            $botones .= "<button class='btn btn-info' onclick='devolver({$row['id']})'>Devolver</button>";
        }

        $botones .= "<button class='btn btn-danger' onclick='eliminar({$row['id']})'>Eliminar</button>";
        $botones .= "</div>";

        echo "<tr>
            <td style='color:#6b7280;font-size:12px'>#{$row['id']}</td>
            <td style='font-weight:500'>{$row['usuario']}</td>
            <td>{$row['libro']}</td>
            <td style='color:#6b7280'>{$row['fecha_prestamo']}</td>
            <td style='color:#6b7280'>{$entrega}</td>
            <td><span class='badge badge-{$slug}'>{$row['estado']}</span></td>
            <td>{$botones}</td>
        </tr>";
    }
}


// ===============================
// CREAR SOLICITUD (USUARIO)
// ===============================
// Relación entre posibles clases Usuario y Libro
if($accion == "solicitar"){

    // Atributos de la solicitud
    $usuario_id = $_POST['usuario_id'];
    $libro_id = $_POST['libro_id'];
    $fecha = date("Y-m-d");

    // Método prepare() del objeto conexión
    $check = $conexion->prepare("SELECT stock FROM libros WHERE id=?");

    // Encapsulamiento de parámetros
    $check->bind_param("i", $libro_id);

    $check->execute();

    // Método fetch_assoc()
    $res = $check->get_result()->fetch_assoc();

    // Validación de atributo stock
    if(!$res || $res['stock'] <= 0){
        echo "No hay stock disponible";
        exit;
    }

    // Creación lógica de objeto Solicitud
    $sql = "INSERT INTO solicitudes (usuario_id, libro_id, fecha_solicitud, estado)
            VALUES (?, ?, ?, 'pendiente')";

    $stmt = $conexion->prepare($sql);

    // Uso de métodos del objeto statement
    $stmt->bind_param("iis", $usuario_id, $libro_id, $fecha);

    if($stmt->execute()){
        echo "Solicitud enviada al administrador";
    } else {
        echo "Error al solicitar";
    }
}


// ===============================
// LISTAR SOLICITUDES (ADMIN)
// ===============================
// Asociación entre Usuario, Libro y Solicitud
if($accion == "listar_solicitudes"){

    $sql = "SELECT s.*, u.nombre, l.titulo
            FROM solicitudes s
            JOIN usuarios u ON s.usuario_id = u.id
            JOIN libros l ON s.libro_id = l.id
            WHERE s.estado = 'pendiente'
            ORDER BY s.id DESC";

    $res = $conexion->query($sql);

    while($row = $res->fetch_assoc()){

        // Comportamiento diferente según acciones
        echo "<tr>
            <td style='font-weight:500'>{$row['nombre']}</td>
            <td>{$row['titulo']}</td>
            <td style='color:#6b7280'>{$row['fecha_solicitud']}</td>
            <td>
                <div class='actions'>
                    <button class='btn btn-success' onclick='aprobarSolicitud({$row['id']})'>Aprobar</button>
                    <button class='btn btn-danger'  onclick='rechazarSolicitud({$row['id']})'>Rechazar</button>
                </div>
            </td>
        </tr>";
    }
}

// ===============================
// APROBAR SOLICITUD
// ===============================
// Simulación de método aprobar() en una clase Solicitud
if($accion == "aprobar_solicitud"){

    $id = $_GET['id'];

    // Objeto statement
    $stmt = $conexion->prepare("SELECT * FROM solicitudes WHERE id=?");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Obtención de atributos de la solicitud
    $sol = $stmt->get_result()->fetch_assoc();

    if(!$sol){
        echo "Solicitud no encontrada";
        exit;
    }

    // Atributos del objeto solicitud
    $usuario_id = $sol['usuario_id'];
    $libro_id = $sol['libro_id'];

    // Cambio de estado del libro
    $conexion->query("UPDATE libros SET stock = stock - 1 WHERE id = $libro_id");

    // Creación lógica de préstamo
    $fecha = date("Y-m-d");
    $entrega = date("Y-m-d", strtotime("+7 days"));

    $stmt = $conexion->prepare("
        INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, fecha_entrega, estado)
        VALUES (?, ?, ?, ?, 'prestado')
    ");

    // Encapsulamiento de datos
    $stmt->bind_param("iiss", $usuario_id, $libro_id, $fecha, $entrega);

    $stmt->execute();

    // Cambio de atributo estado
    $conexion->query("UPDATE solicitudes SET estado='aprobado' WHERE id=$id");

    echo "Solicitud aprobada";
}


// ===============================
// RECHAZAR SOLICITUD
// ===============================
// Método rechazar() de una posible clase Solicitud
if($accion == "rechazar_solicitud"){

    $id = $_GET['id'];

    $conexion->query("UPDATE solicitudes SET estado='rechazado' WHERE id=$id");

    echo "Solicitud rechazada";
}


// ===============================
// DEVOLVER LIBRO
// ===============================
// Método devolver() de la clase Prestamo
if($accion == "devolver"){

    $id = $_GET['id'];

    $stmt = $conexion->prepare("SELECT libro_id FROM prestamos WHERE id=?");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $data = $stmt->get_result()->fetch_assoc();

    // Atributo libro_id
    $libro_id = $data['libro_id'];

    // Cambio de estado del préstamo
    $fecha = date("Y-m-d");

    $stmt = $conexion->prepare("
        UPDATE prestamos 
        SET estado='devuelto', fecha_entrega=? 
        WHERE id=?
    ");

    $stmt->bind_param("si", $fecha, $id);

    $stmt->execute();

    // Actualización del atributo stock
    $conexion->query("UPDATE libros SET stock = stock + 1 WHERE id = $libro_id");

    echo "Libro devuelto";
}


// ===============================
// ELIMINAR PRÉSTAMO
// ===============================
// Método eliminar() relacionado a la entidad Prestamo
if($accion == "eliminar"){

    $id = $_GET['id'];

    $stmt = $conexion->prepare("DELETE FROM prestamos WHERE id=?");

    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        echo "Eliminado";
    } else {
        echo "Error";
    }
}
?>