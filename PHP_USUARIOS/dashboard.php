<?php
session_start();

// 🔐 Proteger acceso
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

// 👤 Datos del usuario
$usuario_id = $_SESSION['usuario_id'];
$nombre = $_SESSION['usuario_nombre'];
$apellido = $_SESSION['usuario_apellido'];

// 🔌 Conexión
require_once "../CONFIG_DATABASE/config.php";

/*
🚫 FINALIZAR SANCIONES VENCIDAS
*/
$sqlUpdateSanciones = "UPDATE sanciones
SET estado = 'cumplida'
WHERE fecha_fin < CURDATE()
AND estado = 'activa'";

$conexion->query($sqlUpdateSanciones);

/*
🚫 VERIFICAR SI EL USUARIO TIENE SANCIONES
*/
$sqlSancion = "SELECT *
FROM sanciones
WHERE usuario_id = ?
AND estado = 'activa'
AND fecha_fin >= CURDATE()
LIMIT 1";

$stmtSancion = $conexion->prepare($sqlSancion);
$stmtSancion->bind_param("i", $usuario_id);
$stmtSancion->execute();

$resultSancion = $stmtSancion->get_result();

$tieneSancion = $resultSancion->num_rows > 0;

$sancion = $resultSancion->fetch_assoc();

$stmtSancion->close();

/*
📚 LIBROS EN ESPERA
*/
$sqlEspera = "SELECT s.*, l.titulo, l.autor, l.categoria
FROM solicitudes s
JOIN libros l ON s.libro_id = l.id
WHERE s.usuario_id = ?
AND s.estado = 'pendiente'
ORDER BY s.id DESC";

$stmtEspera = $conexion->prepare($sqlEspera);
$stmtEspera->bind_param("i", $usuario_id);
$stmtEspera->execute();

$resultEspera = $stmtEspera->get_result();

$stmtEspera->close();

/*
📚 PRÉSTAMOS ACTIVOS
*/
$sqlPrestamos = "SELECT p.*, l.titulo, l.autor, l.categoria
FROM prestamos p
JOIN libros l ON p.libro_id = l.id
WHERE p.usuario_id = ?
AND p.estado = 'prestado'";

$stmtPrestamos = $conexion->prepare($sqlPrestamos);
$stmtPrestamos->bind_param("i", $usuario_id);
$stmtPrestamos->execute();

$result = $stmtPrestamos->get_result();

$totalPrestados = $result->num_rows;

$stmtPrestamos->close();

/*
🔒 TOTAL DE LIBROS ACTIVOS
*/
$sqlTotal = "SELECT 
(
    SELECT COUNT(*)
    FROM prestamos
    WHERE usuario_id = ?
    AND estado = 'prestado'
)
+
(
    SELECT COUNT(*)
    FROM solicitudes
    WHERE usuario_id = ?
    AND estado = 'pendiente'
)
AS total";

$stmtTotal = $conexion->prepare($sqlTotal);

$stmtTotal->bind_param(
    "ii",
    $usuario_id,
    $usuario_id
);

$stmtTotal->execute();

$resultTotal = $stmtTotal->get_result();

$totalActivos = $resultTotal->fetch_assoc()['total'];

$stmtTotal->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Biblioteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/usuario.css">
</head>

<body class="d-flex flex-column min-vh-100">

<!-- ═══════════════ NAVBAR ═══════════════ -->
<nav class="navbar navbar-dark shadow">
    <div class="container">
        <span class="navbar-brand fw-bold">📚 Biblioteca Inteligente</span>

        <div class="d-flex align-items-center gap-2">
            <span class="text-white small">
                Hola, <strong><?= $nombre ?></strong>
            </span>
            <button id="toggleTheme" class="btn btn-light btn-sm">🌙 Oscuro</button>
            <button id="btnLogout" class="btn btn-light btn-sm">Cerrar sesión</button>
        </div>
    </div>
</nav>

<!-- ═══════════════ SANCIÓN ═══════════════ -->
<?php if($tieneSancion): ?>
<div class="container mt-4">
    <div class="alert alert-danger shadow text-center">
        <h5>🚫 Cuenta suspendida</h5>
        <p class="mb-1 small">No puedes solicitar libros mientras tengas una sanción activa.</p>
        <strong>Sanción activa hasta: <?= $sancion['fecha_fin'] ?></strong>
    </div>
</div>
<?php endif; ?>

<!-- ═══════════════ PERFIL + RELOJ ═══════════════ -->
<section class="container my-5 flex-grow-1">
    <div class="row g-4">

        <!-- PERFIL -->
        <div class="col-md-6">
            <div class="card shadow p-4 text-center h-100 d-flex flex-column justify-content-center">
                <h4>👤 Mi perfil</h4>
                <div class="mb-3">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--paper-alt);border:2px solid var(--border);display:flex;align-items:center;justify-content:center;margin:0 auto .75rem;font-size:1.6rem;">
                        <?= strtoupper(substr($nombre, 0, 1)) ?>
                    </div>
                    <p class="fw-semibold mb-0" style="font-size:1.05rem;"><?= $nombre . " " . $apellido ?></p>
                    <small style="color:var(--ink-soft)">Usuario activo</small>
                </div>
                <a href="actualizar_user.php" class="btn btn-primary">Actualizar datos</a>
            </div>
        </div>

        <!-- RELOJ -->
        <div class="col-md-6">
            <div class="card shadow p-4 text-center h-100 d-flex flex-column justify-content-center">
                <h4>📅 Fecha y hora</h4>
                <h2 id="hora" class="fw-bold my-2"></h2>
                <p id="fecha" class="mb-0"></p>
            </div>
        </div>

    </div>
</section>

<!-- ═══════════════ SOLICITAR LIBRO ═══════════════ -->
<div class="container mb-5">
    <div class="card shadow p-4">

        <h4>📚 Solicitar un libro</h4>

        <form id="formPrestarLibro">
            <div class="mb-3">
                <label class="form-label">Buscar libro</label>
                <input type="text" id="buscadorLibro" class="form-control"
                    placeholder="Escribe el título o autor…">
                <div id="resultadosLibros" class="list-group mt-2"></div>
                <input type="hidden" name="libro_id" id="libroSeleccionado" required>
            </div>

            <button type="submit" class="btn btn-primary w-100"
                <?= ($totalActivos >= 3 || $tieneSancion) ? 'disabled' : '' ?>>
                Solicitar libro
            </button>

            <?php if ($totalActivos >= 3): ?>
                <p class="text-danger text-center mt-2">
                    ⚠️ Ya alcanzaste el límite de 3 libros activos
                </p>
            <?php endif; ?>
        </form>

        <div id="respuestaAjax" class="mt-3"></div>
    </div>
</div>

<!-- ═══════════════ EN ESPERA ═══════════════ -->
<div class="container mb-5">
    <div class="card shadow p-4">

        <h4>⏳ Solicitudes pendientes</h4>

        <div class="table-responsive">
            <table class="table table-striped text-center mb-0">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Categoría</th>
                        <th>Fecha solicitud</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($resultEspera->num_rows > 0): ?>
                    <?php while($row = $resultEspera->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['titulo'] ?></td>
                            <td><?= $row['autor'] ?></td>
                            <td><?= $row['categoria'] ?></td>
                            <td><?= $row['fecha_solicitud'] ?></td>
                            <td><span class="badge bg-warning">En revisión</span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-muted py-4">
                            No tienes solicitudes pendientes 📚
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ═══════════════ MIS PRÉSTAMOS ═══════════════ -->
<div class="container mb-5">
    <div class="card shadow p-4">

        <h4>📖 Mis libros prestados</h4>

        <div class="table-responsive">
            <table class="table table-striped text-center mb-0">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Categoría</th>
                        <th>Préstamo</th>
                        <th>Entrega</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($totalPrestados > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php
                        $hoy = date("Y-m-d");
                        $retrasado = ($row['estado'] == 'prestado' && $row['fecha_entrega'] < $hoy);
                        ?>
                        <tr class="<?= $retrasado ? 'table-danger' : '' ?>">
                            <td><?= $row['titulo'] ?></td>
                            <td><?= $row['autor'] ?></td>
                            <td><?= $row['categoria'] ?></td>
                            <td><?= $row['fecha_prestamo'] ?></td>
                            <td><?= $row['fecha_entrega'] ?></td>
                            <td>
                                <?php if($row['estado'] == 'prestado'): ?>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="devolverLibro(<?= $row['id'] ?>)">
                                        Devolver
                                    </button>
                                    <?php if($retrasado): ?>
                                        <span class="badge bg-danger">Atrasado</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Prestado</span>
                                    <?php endif; ?>
                                <?php elseif($row['estado'] == 'devuelto'): ?>
                                    <span class="badge bg-success">Devuelto</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">En revisión</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            📚 No tienes libros prestados actualmente
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h5 class="mt-3 text-center">📊 Libros activos: <?= $totalActivos ?> / 3</h5>

        <button id="toggleHistorial" class="btn btn-secondary btn-sm mb-3">
            Ver historial completo
        </button>

        <?php
        $sqlHist = "SELECT p.*, l.titulo, l.autor, l.categoria
                    FROM prestamos p
                    JOIN libros l ON p.libro_id = l.id
                    WHERE p.usuario_id = ? AND p.estado = 'devuelto'";

        $stmtHist = $conexion->prepare($sqlHist);
        $stmtHist->bind_param("i", $usuario_id);
        $stmtHist->execute();
        $resultHist = $stmtHist->get_result();
        ?>

        <div id="historial" style="display:none;">
            <h5 class="mt-3">📜 Historial de devoluciones</h5>
            <div class="table-responsive">
                <table class="table table-striped text-center mb-0">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Fecha préstamo</th>
                            <th>Fecha entrega</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $resultHist->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['titulo'] ?></td>
                                <td><?= $row['fecha_prestamo'] ?></td>
                                <td><?= $row['fecha_entrega'] ?></td>
                                <td><span class="badge bg-success">Devuelto</span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ═══════════════ FOOTER ═══════════════ -->
<footer class="text-center mt-auto">
    © 2026 Biblioteca Inteligente — Panel de Usuario
</footer>

<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const params = new URLSearchParams(window.location.search);

if (params.get("error") === "limite") {
    Swal.fire({ icon: 'warning', title: 'Límite alcanzado', text: 'Ya tienes 3 libros prestados' });
}
if (params.get("error") === "stock") {
    Swal.fire({ icon: 'error', title: 'Sin stock', text: 'Este libro no está disponible' });
}
if (params.get("error") === "general") {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un problema' });
}
if (params.get("ok") === "prestado") {
    Swal.fire({ icon: 'success', title: 'Libro solicitado', text: 'Disfruta tu lectura 📚' });
}
if (params.get("ok") === "devuelto") {
    Swal.fire({ icon: 'success', title: 'Libro devuelto', text: 'Gracias por devolverlo 📚' });
}

if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.pathname);
}
</script>

<!-- JS -->
<script defer src="../js/usuario.js"></script>

</body>
</html>