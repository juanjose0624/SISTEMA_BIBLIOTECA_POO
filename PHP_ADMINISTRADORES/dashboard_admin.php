<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../html/login_admin.html");
    exit;
}

$nombre = $_SESSION['admin_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin — Biblioteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body class="d-flex flex-column min-vh-100">

<!-- ═══════════════ NAVBAR ═══════════════ -->
<nav class="navbar navbar-dark shadow">
    <div class="container">
        <span class="navbar-brand fw-bold">🔐 Panel Administrador</span>

        <div class="d-flex align-items-center gap-2">
            <span class="text-white">
                Bienvenido, <strong><?= $nombre ?></strong>
            </span>
            <button id="btnDarkMode" class="btn btn-outline-light btn-sm">🌙</button>
            <button id="btnLogout" class="btn btn-danger btn-sm">Cerrar sesión</button>
        </div>
    </div>
</nav>

<!-- ═══════════════ CONTENIDO ═══════════════ -->
<section class="container my-5 flex-grow-1">

    <!-- BIENVENIDA HERO -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-hero shadow p-4">
                <h3>👋 Bienvenido de nuevo, <?= $nombre ?></h3>
                <p class="mb-0">
                    Gestiona usuarios, libros, préstamos y solicitudes desde este panel centralizado.
                </p>
            </div>
        </div>
    </div>

    <!-- RELOJ + ACCESOS RÁPIDOS -->
    <div class="row g-4">

        <!-- RELOJ -->
        <div class="col-md-4">
            <div class="card shadow p-4 text-center h-100 d-flex flex-column justify-content-center">
                <h5>📅 Fecha y Hora</h5>
                <h2 id="hora" class="fw-bold"></h2>
                <p id="fecha" class="mb-0"></p>
            </div>
        </div>

        <!-- ACCESOS RÁPIDOS -->
        <div class="col-md-8">
            <div class="card shadow p-4 h-100">
                <h5>⚙️ Accesos rápidos</h5>

                <div class="row g-3">
                    <div class="col-6">
                        <a href="../CRUDS_FRONT/crud_usuarios.php" class="quick-btn btn-primary">
                            👤 Usuarios
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="../CRUDS_FRONT/crud_libros.php" class="quick-btn btn-success">
                            📚 Libros
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="../CRUDS_FRONT/crud_prestamos.php" class="quick-btn btn-warning">
                            🔄 Préstamos
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="../CRUDS_FRONT/crud_pqrs.php" class="quick-btn btn-info">
                            📨 PQRS
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ESTADÍSTICAS + INFO -->
    <div class="row mt-4">

        <div class="col-md-6">
            <div class="card shadow p-4 text-center h-100 d-flex flex-column justify-content-between">
                <div>
                    <h5>📊 Estadísticas</h5>
                    <p style="color:var(--ink-soft);font-size:.88rem;">
                        Accede a métricas en tiempo real del sistema de préstamos.
                    </p>
                </div>
                <a href="estadisticas.php" class="btn btn-dark w-100">
                    Ver estadísticas avanzadas →
                </a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow p-4 h-100">
                <h5>💡 Capacidades del panel</h5>
                <div>
                    <div class="info-item">
                        <span class="info-dot"></span>
                        Gestiona altas, bajas y modificaciones de usuarios
                    </div>
                    <div class="info-item">
                        <span class="info-dot"></span>
                        Controla el catálogo completo de libros
                    </div>
                    <div class="info-item">
                        <span class="info-dot"></span>
                        Monitorea préstamos activos y vencidos
                    </div>
                    <div class="info-item">
                        <span class="info-dot"></span>
                        Administra y responde PQRS del sistema
                    </div>
                </div>
            </div>
        </div>

    </div>

</section>

<!-- ═══════════════ FOOTER ═══════════════ -->
<footer class="text-center mt-auto">
    © 2026 Biblioteca Inteligente — Panel Administrador
</footer>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/admin.js"></script>

</body>
</html>