<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../html/login_admin.html");
    exit;
}

require_once "../CONFIG_DATABASE/config.php";

// Totales
$totalUsuarios = $conexion->query("SELECT COUNT(*) as t FROM usuarios")->fetch_assoc()['t'];
$totalLibros   = $conexion->query("SELECT COUNT(*) as t FROM libros")->fetch_assoc()['t'];
$prestados     = $conexion->query("SELECT COUNT(*) as t FROM prestamos WHERE estado='prestado'")->fetch_assoc()['t'];
$devueltos     = $conexion->query("SELECT COUNT(*) as t FROM prestamos WHERE estado='devuelto'")->fetch_assoc()['t'];
$atrasados     = $conexion->query("SELECT COUNT(*) as t FROM prestamos WHERE estado='prestado' AND fecha_entrega < CURDATE()")->fetch_assoc()['t'];

// Libros más prestados
$topLibros = $conexion->query("
    SELECT l.titulo, COUNT(p.id) as total
    FROM prestamos p
    JOIN libros l ON p.libro_id = l.id
    GROUP BY l.titulo
    ORDER BY total DESC
    LIMIT 5
");

$labelsLibros = [];
$dataLibros   = [];
while ($row = $topLibros->fetch_assoc()) {
    $labelsLibros[] = $row['titulo'];
    $dataLibros[]   = $row['total'];
}

// Categorías
$categorias = $conexion->query("
    SELECT l.categoria, COUNT(p.id) as total
    FROM prestamos p
    JOIN libros l ON p.libro_id = l.id
    GROUP BY l.categoria
    ORDER BY total DESC
");

$labelsCat = [];
$dataCat   = [];
while ($row = $categorias->fetch_assoc()) {
    $labelsCat[] = $row['categoria'];
    $dataCat[]   = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Estadísticas — BiblioPanel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  /* ── Reset & base ─────────────────────────── */
  *, *::before, *::after { box-sizing: border-box; }
  body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif;
         background: #f4f6fb; color: #1e2235; }

  /* ── Layout ───────────────────────────────── */
  .layout   { display: flex; min-height: 100vh; }
  .sidebar  { width: 220px; background: #1e2235; padding: 1.5rem 1rem;
              display: flex; flex-direction: column; gap: .35rem;
              position: sticky; top: 0; height: 100vh; flex-shrink: 0; }
  .main     { flex: 1; padding: 2rem 2.25rem; overflow-x: hidden; }

  /* ── Sidebar ──────────────────────────────── */
  .sidebar-logo { color: #fff; font-size: 15px; font-weight: 600;
                  padding: .75rem .5rem 1.25rem;
                  border-bottom: .5px solid rgba(255,255,255,.12);
                  margin-bottom: .5rem; display: flex; align-items: center; gap: 9px; }
  .sidebar-logo svg { opacity: .85; }
  .nav-item { display: flex; align-items: center; gap: 10px; padding: .55rem .75rem;
              border-radius: 8px; font-size: 13px; color: rgba(255,255,255,.55);
              text-decoration: none; transition: background .15s, color .15s; }
  .nav-item:hover  { background: rgba(255,255,255,.07); color: rgba(255,255,255,.9); }
  .nav-item.active { background: rgba(99,120,255,.22); color: #8fa4ff; }
  .nav-item svg    { width: 16px; height: 16px; flex-shrink: 0; }

  /* ── Top bar ──────────────────────────────── */
  .topbar   { display: flex; justify-content: space-between; align-items: center;
              margin-bottom: 2rem; }
  .topbar h1 { font-size: 20px; font-weight: 600; margin: 0; }
  .topbar small { font-size: 13px; color: #888; }

  /* ── Metric cards ─────────────────────────── */
  .metrics  { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px;
              margin-bottom: 2rem; }
  .m-card   { background: #fff; border-radius: 12px;
              border: .5px solid rgba(0,0,0,.07); padding: 1.1rem 1.25rem; }
  .m-label  { font-size: 12px; color: #888; margin-bottom: 8px; }
  .m-value  { font-size: 28px; font-weight: 600; margin-bottom: 6px; line-height: 1; }
  .m-badge  { font-size: 11px; padding: 3px 9px; border-radius: 20px;
              display: inline-block; font-weight: 500; }

  /* badge variants */
  .b-blue  { background: #eef2ff; color: #4361c2; }
  .b-green { background: #e6f4ef; color: #1a7f5a; }
  .b-amber { background: #fff6e0; color: #a06000; }
  .b-teal  { background: #e1f5f2; color: #0f7a6a; }
  .b-red   { background: #ffeaea; color: #c02020; }

  /* ── Chart cards ──────────────────────────── */
  .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
                 margin-bottom: 2rem; }
  .c-card   { background: #fff; border-radius: 12px;
              border: .5px solid rgba(0,0,0,.07); padding: 1.4rem 1.5rem; }
  .c-title  { font-size: 13px; font-weight: 600; color: #555; margin-bottom: 1.25rem; }
  .c-wrap   { position: relative; height: 230px; }
  .legend   { display: flex; flex-wrap: wrap; gap: 10px 16px; margin-top: 1rem;
              font-size: 11px; color: #888; }
  .legend span { display: flex; align-items: center; gap: 5px; }
  .legend-dot  { width: 9px; height: 9px; border-radius: 2px; }

  /* ── Responsive ───────────────────────────── */
  @media (max-width: 1100px) {
    .metrics { grid-template-columns: repeat(3, 1fr); }
  }
  @media (max-width: 768px) {
    .sidebar { display: none; }
    .main    { padding: 1.25rem; }
    .metrics { grid-template-columns: 1fr 1fr; }
    .charts-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 480px) {
    .metrics { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

<div class="layout">

  <!-- ── SIDEBAR ────────────────────────────── -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
           stroke="#8fa4ff" stroke-width="2" stroke-linecap="round">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
      </svg>
      BiblioPanel
    </div>

    <a href="dashboard_admin.php" class="nav-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
      </svg>
      Panel principal
    </a>

  

    <a href="estadisticas.php" class="nav-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="20" x2="18" y2="10"/>
        <line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="6"  y1="20" x2="6"  y2="14"/>
      </svg>
      Estadísticas
    </a>

 
  </aside>

  <!-- ── MAIN CONTENT ────────────────────────── -->
  <main class="main">

    <!-- Topbar -->
    <div class="topbar">
      <h1>Estadísticas generales</h1>
      <small id="fecha-hoy"></small>
    </div>

    <!-- Métricas -->
    <div class="metrics">

      <div class="m-card">
        <div class="m-label">Usuarios registrados</div>
        <div class="m-value" style="color:#4361c2"><?= $totalUsuarios ?></div>
        <span class="m-badge b-blue">Total</span>
      </div>

      <div class="m-card">
        <div class="m-label">Total de libros</div>
        <div class="m-value" style="color:#1a7f5a"><?= $totalLibros ?></div>
        <span class="m-badge b-green">Catálogo activo</span>
      </div>

      <div class="m-card">
        <div class="m-label">Préstamos activos</div>
        <div class="m-value" style="color:#a06000"><?= $prestados ?></div>
        <span class="m-badge b-amber">En circulación</span>
      </div>

      <div class="m-card">
        <div class="m-label">Devueltos</div>
        <div class="m-value" style="color:#0f7a6a"><?= $devueltos ?></div>
        <span class="m-badge b-teal">Histórico total</span>
      </div>

      <div class="m-card">
        <div class="m-label">Libros atrasados</div>
        <div class="m-value" style="color:#c02020"><?= $atrasados ?></div>
        <span class="m-badge b-red">Requieren aviso</span>
      </div>

    </div>

    <!-- Gráficas -->
    <div class="charts-grid">

      <!-- Libros más prestados -->
      <div class="c-card">
        <div class="c-title">Libros más prestados</div>
        <div class="c-wrap">
          <canvas id="graficoLibros" role="img"
                  aria-label="Gráfica de barras con los 5 libros más prestados">
            Datos de préstamos por libro.
          </canvas>
        </div>
        <div class="legend">
          <span>
            <span class="legend-dot" style="background:#5271d4"></span>
            Total de préstamos
          </span>
        </div>
      </div>

      <!-- Categorías populares -->
      <div class="c-card">
        <div class="c-title">Categorías populares</div>
        <div class="c-wrap">
          <canvas id="graficoCategorias" role="img"
                  aria-label="Gráfica de dona con distribución de categorías">
            Distribución de préstamos por categoría.
          </canvas>
        </div>
        <div class="legend" id="catLegend"></div>
      </div>

    </div>

    <!-- Volver -->
    <div style="text-align:center; margin-top:1.5rem;">
      <a href="dashboard_admin.php"
         style="display:inline-flex;align-items:center;gap:8px;padding:.55rem 1.5rem;
                border-radius:8px;background:#1e2235;color:#fff;font-size:14px;
                text-decoration:none;transition:opacity .15s;"
         onmouseover="this.style.opacity='.8'"
         onmouseout="this.style.opacity='1'">
        ← Volver al panel
      </a>
    </div>

  </main>
</div><!-- /.layout -->

<!-- ── SCRIPTS ─────────────────────────────── -->
<script>
/* Fecha */
document.getElementById('fecha-hoy').textContent =
  new Date().toLocaleDateString('es-CO', {
    weekday:'long', year:'numeric', month:'long', day:'numeric'
  });

/* Datos desde PHP */
const labelsLibros = <?= json_encode($labelsLibros) ?>;
const dataLibros   = <?= json_encode($dataLibros) ?>;
const labelsCat    = <?= json_encode($labelsCat) ?>;
const dataCat      = <?= json_encode($dataCat) ?>;

const palette = ['#5271d4','#1a9e75','#e07b2a','#c94f88','#73726c',
                 '#0099c6','#dd4477','#66aa00','#b82e2e','#316395'];

/* Gráfica de barras — libros más prestados */
new Chart(document.getElementById('graficoLibros'), {
  type: 'bar',
  data: {
    labels: labelsLibros,
    datasets: [{
      label: 'Préstamos',
      data: dataLibros,
      backgroundColor: '#5271d4',
      borderRadius: 6,
      borderSkipped: false
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: { label: ctx => '  ' + ctx.parsed.y + ' préstamos' }
      }
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { color: '#888', font: { size: 11 }, maxRotation: 30 }
      },
      y: {
        grid: { color: 'rgba(0,0,0,.05)' },
        ticks: { color: '#888', font: { size: 11 } },
        beginAtZero: true
      }
    }
  }
});

/* Gráfica de dona — categorías */
const coloresCat = palette.slice(0, labelsCat.length);

new Chart(document.getElementById('graficoCategorias'), {
  type: 'doughnut',
  data: {
    labels: labelsCat,
    datasets: [{
      data: dataCat,
      backgroundColor: coloresCat,
      borderWidth: 0,
      hoverOffset: 8
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '62%',
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => '  ' + ctx.label + ': ' + ctx.parsed
        }
      }
    }
  }
});

/* Leyenda dinámica — categorías */
const legend = document.getElementById('catLegend');
labelsCat.forEach((label, i) => {
  legend.innerHTML +=
    `<span>
      <span class="legend-dot" style="background:${coloresCat[i]}"></span>
      ${label}
    </span>`;
});
</script>

</body>
</html>