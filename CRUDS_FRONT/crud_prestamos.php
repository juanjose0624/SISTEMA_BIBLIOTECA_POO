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
<title>Gestión de Préstamos</title>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #111827;
    min-height: 100vh;
}

.container {
    width: 90%;
    max-width: 1100px;
    margin: 2rem auto;
}

.topbar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1rem;
}

.btn-back {
    display: flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: background .15s, color .15s;
}

.btn-back:hover { background: #f3f4f6; color: #111827; }

.btn-back svg {
    width: 14px; height: 14px;
    stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

.page-title {
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    letter-spacing: .05em;
    text-transform: uppercase;
}

/* TABS */
.tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.tab {
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    border: none;
    background: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    transition: color .15s, border-color .15s;
}

.tab:hover { color: #111827; }

.tab.active {
    color: #1d4ed8;
    border-bottom-color: #1d4ed8;
}

.tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fee2e2;
    color: #991b1b;
    font-size: 10px;
    font-weight: 600;
    min-width: 18px;
    height: 18px;
    border-radius: 999px;
    padding: 0 5px;
    margin-left: 6px;
}

/* CARD */
.card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    margin-bottom: 1.25rem;
}

.card-header {
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
}

.card-header span { font-size: 15px; font-weight: 500; }

/* BOTONES */
.btn {
    border: 1px solid #d1d5db;
    background: transparent;
    padding: 7px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    transition: background .15s;
}

.btn:hover { background: #f3f4f6; }

.btn-primary  { background: #1d4ed8; color: #eff6ff; border-color: #1e40af; }
.btn-primary:hover  { background: #1e40af; color: #eff6ff; }

.btn-success  { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
.btn-success:hover  { background: #bbf7d0; }

.btn-danger   { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
.btn-danger:hover   { background: #fecaca; }

.btn-info     { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
.btn-info:hover     { background: #bfdbfe; }

.btn-secondary { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
.btn-secondary:hover { background: #e5e7eb; }

/* TABLA */
table { width: 100%; border-collapse: collapse; }
thead { background: #f9fafb; }

th {
    padding: 10px 14px;
    font-size: 12px;
    font-weight: 500;
    text-align: left;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
}

td {
    padding: 10px 14px;
    font-size: 14px;
    border-top: 1px solid #f3f4f6;
    vertical-align: middle;
}

tr:hover td { background: #f9fafb; }
.actions { display: flex; gap: 6px; flex-wrap: wrap; }

/* BADGES */
.badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 500;
    padding: 2px 8px;
    border-radius: 999px;
    white-space: nowrap;
}

.badge-pendiente  { background: #fef3c7; color: #92400e; }
.badge-prestado   { background: #dbeafe; color: #1d4ed8; }
.badge-devuelto   { background: #dcfce7; color: #166534; }
.badge-rechazado  { background: #fee2e2; color: #991b1b; }

/* MODAL */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.4);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 100;
}

.modal-content {
    background: #ffffff;
    width: 460px;
    max-width: 95vw;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    overflow: hidden;
}

.modal-header {
    padding: .875rem 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header span:first-child { font-size: 14px; font-weight: 500; }
.modal-body { padding: 1.25rem; }

.modal-footer {
    padding: .875rem 1.25rem;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    border-top: 1px solid #e5e7eb;
}

.field { margin-bottom: .875rem; }

.field label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 4px;
}

.field select {
    width: 100%;
    padding: 8px 10px;
    font-size: 13px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #ffffff;
    color: #111827;
    outline: none;
    transition: border-color .15s;
    font-family: Arial, sans-serif;
}

.field select:focus { border-color: #1d4ed8; }

.close { cursor: pointer; color: #6b7280; font-size: 18px; }
.close:hover { color: #111827; }

.empty {
    padding: 2.5rem;
    text-align: center;
    color: #6b7280;
    font-size: 13px;
}
</style>

</head>
<body>

<div class="container">

    <div class="topbar">
        <button class="btn-back" onclick="history.back()">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Ir atrás
        </button>
        <span class="page-title">Panel de administración</span>
    </div>

    <!-- TABS -->
    <div class="tabs">
        <button class="tab active" onclick="mostrarTab('prestamos', this)">
            Préstamos activos
        </button>
        <button class="tab" onclick="mostrarTab('solicitudes', this)">
            Solicitudes pendientes
            <span class="tab-badge" id="contadorSolicitudes">0</span>
        </button>
    </div>

    <!-- TAB: PRÉSTAMOS -->
    <div id="tab-prestamos">
        <div class="card">
            <div class="card-header">
                <span>Préstamos</span>
                <button class="btn btn-primary" onclick="abrirSolicitud()">+ Nueva solicitud</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Libro</th>
                        <th>Fecha préstamo</th>
                        <th>Fecha entrega</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaPrestamos">
                    <tr><td colspan="7" class="empty">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: SOLICITUDES -->
    <div id="tab-solicitudes" style="display:none">
        <div class="card">
            <div class="card-header">
                <span>Solicitudes pendientes</span>
                <button class="btn btn-secondary" onclick="listarSolicitudes()">↻ Actualizar</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Libro</th>
                        <th>Fecha solicitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaSolicitudes">
                    <tr><td colspan="4" class="empty">Sin solicitudes pendientes</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL NUEVA SOLICITUD -->
<div class="modal" id="modalSolicitud">
    <div class="modal-content">

        <div class="modal-header">
            <span>Nueva solicitud de préstamo</span>
            <span class="close" onclick="cerrarModal()">✕</span>
        </div>

        <div class="modal-body">
            <div class="field">
                <label>Usuario</label>
                <select id="usuario_id">
                    <option value="">Cargando...</option>
                </select>
            </div>
            <div class="field">
                <label>Libro</label>
                <select id="libro_id">
                    <option value="">Cargando...</option>
                </select>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
            <button class="btn btn-success" onclick="solicitar()">Enviar solicitud</button>
        </div>

    </div>
</div>

<script>

$(document).ready(function(){
    listar();
    listarSolicitudes();
});

// =====================
// TABS
// =====================
function mostrarTab(tab, el){
    $("#tab-prestamos, #tab-solicitudes").hide();
    $("#tab-" + tab).show();
    $(".tab").removeClass("active");
    $(el).addClass("active");
}

// =====================
// LISTAR PRÉSTAMOS
// =====================
function listar(){
    $.get("../CRUDS_BACK/prestamos.php?accion=listar", function(data){
        $("#tablaPrestamos").html(data);
    });
}

// =====================
// LISTAR SOLICITUDES
// =====================
function listarSolicitudes(){
    $.get("../CRUDS_BACK/prestamos.php?accion=listar_solicitudes", function(data){
        $("#tablaSolicitudes").html(data || "<tr><td colspan='4' class='empty'>Sin solicitudes pendientes</td></tr>");

        // contar filas para el badge
        let total = $("#tablaSolicitudes tr").length;
        let sinData = $("#tablaSolicitudes .empty").length;
        $("#contadorSolicitudes").text(sinData ? 0 : total);
    });
}

// =====================
// MODAL NUEVA SOLICITUD
// =====================
function abrirSolicitud(){
    $.get("../CRUDS_BACK/usuarios.php?accion=listarSelect", function(data){
        $("#usuario_id").html(data);
    });
    $.get("../CRUDS_BACK/libros.php?accion=listarSelect", function(data){
        $("#libro_id").html(data);
    });
    $("#modalSolicitud").css("display", "flex");
}

function cerrarModal(){
    $("#modalSolicitud").hide();
}

// =====================
// SOLICITAR
// =====================
function solicitar(){
    if($("#usuario_id").val() == "" || $("#libro_id").val() == ""){
        Swal.fire("Error", "Selecciona usuario y libro", "error");
        return;
    }

    $.post("../CRUDS_BACK/prestamos.php?accion=solicitar", {
        usuario_id: $("#usuario_id").val(),
        libro_id:   $("#libro_id").val()
    }, function(res){
        Swal.fire({ icon: "success", title: res, timer: 1500, showConfirmButton: false });
        cerrarModal();
        listarSolicitudes();
    });
}

// =====================
// APROBAR SOLICITUD
// =====================
function aprobarSolicitud(id){
    Swal.fire({
        title: "¿Aprobar solicitud?",
        text: "Se creará el préstamo y se descontará el stock",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, aprobar",
        cancelButtonText: "Cancelar"
    }).then(r => {
        if(r.isConfirmed){
            $.get("../CRUDS_BACK/prestamos.php?accion=aprobar_solicitud&id=" + id, function(res){
                Swal.fire({ icon: "success", title: res, timer: 1500, showConfirmButton: false });
                listar();
                listarSolicitudes();
            });
        }
    });
}

// =====================
// RECHAZAR SOLICITUD
// =====================
function rechazarSolicitud(id){
    Swal.fire({
        title: "¿Rechazar solicitud?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, rechazar",
        cancelButtonText: "Cancelar"
    }).then(r => {
        if(r.isConfirmed){
            $.get("../CRUDS_BACK/prestamos.php?accion=rechazar_solicitud&id=" + id, function(res){
                Swal.fire({ icon: "info", title: res, timer: 1500, showConfirmButton: false });
                listarSolicitudes();
            });
        }
    });
}

// =====================
// DEVOLVER
// =====================
function devolver(id){
    Swal.fire({
        title: "¿Registrar devolución?",
        text: "Se sumará una unidad al stock del libro",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, devolver"
    }).then(r => {
        if(r.isConfirmed){
            $.get("../CRUDS_BACK/prestamos.php?accion=devolver&id=" + id, function(res){
                Swal.fire({ icon: "success", title: res, timer: 1500, showConfirmButton: false });
                listar();
            });
        }
    });
}

// =====================
// ELIMINAR
// =====================
function eliminar(id){
    Swal.fire({
        title: "¿Eliminar registro?",
        text: "No podrás revertir esto",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar"
    }).then(r => {
        if(r.isConfirmed){
            $.get("../CRUDS_BACK/prestamos.php?accion=eliminar&id=" + id, function(res){
                Swal.fire({ icon: "success", title: res, timer: 1500, showConfirmButton: false });
                listar();
            });
        }
    });
}

</script>

</body>
</html>