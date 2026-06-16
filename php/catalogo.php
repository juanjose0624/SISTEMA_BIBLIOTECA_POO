<?php
require_once "../CONFIG_DATABASE/config.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Libros — Biblioteca</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ── BASE ── */
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f7f4ef;
            color: #1a1a2e;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: #1a1a2e !important;
            border-bottom: 3px solid #c0392b;
            padding: .75rem 0;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            color: #fff !important;
        }

        .nav-link {
            font-size: .85rem;
            font-weight: 500;
            color: rgba(255,255,255,.65) !important;
            transition: color .18s;
        }

        .nav-link:hover, .nav-link.active { color: #fff !important; }

        .btn-outline-light.btn-sm {
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .05em;
            border-radius: 7px;
            padding: .38rem .9rem;
            border-color: rgba(255,255,255,.3);
            color: rgba(255,255,255,.8) !important;
            transition: all .18s;
        }

        .btn-outline-light.btn-sm:hover {
            background: #fff;
            color: #1a1a2e !important;
        }

        /* ── HERO HEADER ── */
        .catalogo-header {
            background: linear-gradient(160deg, #0b1120 0%, #1c2d50 100%);
            padding: 3rem 0 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .catalogo-header::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26,86,232,.2) 0%, transparent 70%);
            top: -120px; right: -80px;
        }

        .catalogo-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #fff;
            margin-bottom: .4rem;
            position: relative;
        }

        .catalogo-header p {
            color: rgba(255,255,255,.55);
            font-size: .88rem;
            position: relative;
        }

        /* ── BUSCADOR ── */
        .search-bar {
            background: #fff;
            border-radius: 12px;
            border: 1px solid rgba(26,26,46,.1);
            box-shadow: 0 4px 20px rgba(26,26,46,.08);
            padding: 1.25rem 1.5rem;
            margin: -1.5rem auto 2rem;
            position: relative;
            max-width: 860px;
        }

        .form-control, .form-select {
            border: 1.5px solid #dde1ea;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: .88rem;
            padding: .6rem .9rem;
            color: #1a1a2e;
            background: #fafbfc;
            transition: border-color .18s, box-shadow .18s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #1a1a2e;
            box-shadow: 0 0 0 3px rgba(26,26,46,.07);
            background: #fff;
            outline: none;
        }

        .form-control::placeholder { color: #b0b8c8; }

        /* icono dentro del input */
        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .bi {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #aab0be;
            font-size: .95rem;
            pointer-events: none;
        }

        .input-icon-wrap .form-control {
            padding-left: 2.4rem;
        }

        /* ── CARDS DE LIBROS (generadas por JS) ── */

        /* Estos estilos los aplica el JS al renderizar las cards */
        #resultados .book-card {
            background: #fff;
            border: 1px solid rgba(26,26,46,.09);
            border-radius: 14px;
            overflow: hidden;
            transition: transform .22s ease, box-shadow .22s ease;
        }

        #resultados .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 36px rgba(26,26,46,.12);
        }

        #resultados .book-card img {
            height: 260px;
            object-fit: cover;
            width: 100%;
            transition: transform .35s ease;
        }

        #resultados .book-card:hover img { transform: scale(1.04); }

        #resultados .card-body { padding: 1rem 1.2rem 1.2rem; }

        #resultados .card-title {
            font-family: 'Playfair Display', serif;
            font-size: .98rem;
            color: #1a1a2e;
            margin-bottom: .2rem;
        }

        #resultados .card-text {
            font-size: .8rem;
            color: #8e96a8;
        }

        #resultados .badge-cat {
            display: inline-block;
            background: #f0ede8;
            color: #5a5070;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            border-radius: 5px;
            padding: .25em .65em;
            margin-bottom: .65rem;
        }

        #resultados .stock-ok  { color: #15803d; font-size: .78rem; font-weight: 600; }
        #resultados .stock-no  { color: #b91c1c; font-size: .78rem; font-weight: 600; }

        /* ── ESTADO VACÍO ── */
        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
            color: #8e96a8;
        }

        .empty-state i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }
        .empty-state p { font-size: .9rem; }

        /* ── FOOTER ── */
        footer {
            background: #1a1a2e !important;
            color: rgba(255,255,255,.4) !important;
            font-size: .76rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 1rem !important;
            border-top: 3px solid #c0392b;
            margin-top: auto;
        }
    </style>
</head>

<body>

<!-- ═══ NAVBAR ═══ -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold" href="../index.html">
            📚 Biblioteca Inteligente
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="../index.html">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="../html/login.html">Iniciar sesión</a></li>
                <li class="nav-item"><a class="nav-link" href="../html/register.html">Registrarse</a></li>
                <li class="nav-item"><a class="nav-link" href="../html/pqrs.html">PQRS</a></li>
                <li class="nav-item"><a class="nav-link active" href="../php/catalogo.php">Catálogo</a></li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-light btn-sm" href="../html/login.html">Acceder</a>
                </li>
            </ul>
        </div>

    </div>
</nav>

<!-- ═══ HEADER ═══ -->
<div class="catalogo-header">
    <h2>Catálogo de libros</h2>
    <p>Explora nuestra colección y encuentra tu próxima lectura</p>
</div>

<!-- ═══ CONTENIDO ═══ -->
<div class="container flex-grow-1">

    <!-- BUSCADOR -->
    <div class="search-bar">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="input-icon-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="buscador" class="form-control"
                           placeholder="Buscar por título o autor…">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filtroCategoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php
                    $cats = $conexion->query("SELECT DISTINCT categoria FROM libros");
                    while($cat = $cats->fetch_assoc()):
                    ?>
                        <option value="<?= $cat['categoria'] ?>">
                            <?= $cat['categoria'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- RESULTADOS -->
    <div id="resultados" class="row g-4 pb-5"></div>

</div>

<!-- ═══ FOOTER ═══ -->
<footer class="text-center mt-auto">
    © 2026 Biblioteca Inteligente — Todos los derechos reservados
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/catalogo.js"></script>
</body>
</html>