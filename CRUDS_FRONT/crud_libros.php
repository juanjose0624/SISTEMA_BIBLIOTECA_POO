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
<title>CRUD Libros</title>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #111827;
    min-height: 100vh;
}

.container {
    width: 90%;
    max-width: 960px;
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

.btn-back:hover {
    background: #f3f4f6;
    color: #111827;
}

.btn-back svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
    flex-shrink: 0;
}

.page-title {
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    letter-spacing: .05em;
    text-transform: uppercase;
}

.card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}

.card-header {
    padding: 1rem 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
}

.card-header span {
    font-size: 15px;
    font-weight: 500;
    color: #111827;
}

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

.btn-primary { background: #1d4ed8; color: #eff6ff; border-color: #1e40af; }
.btn-primary:hover { background: #1e40af; color: #eff6ff; }

.btn-warning { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.btn-warning:hover { background: #fde68a; }

.btn-danger { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
.btn-danger:hover { background: #fecaca; }

.btn-success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
.btn-success:hover { background: #bbf7d0; }

.btn-secondary { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
.btn-secondary:hover { background: #e5e7eb; }

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
}

td {
    padding: 10px 14px;
    font-size: 14px;
    border-top: 1px solid #f3f4f6;
    vertical-align: middle;
}

tr:hover td { background: #f9fafb; }

.actions { display: flex; gap: 6px; }

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
    width: 480px;
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

.modal-header span:first-child {
    font-size: 14px;
    font-weight: 500;
    color: #111827;
}

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

.field input, .field select {
    width: 100%;
    padding: 8px 10px;
    font-size: 13px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #ffffff;
    color: #111827;
    outline: none;
    transition: border-color .15s;
}

.field input:focus, .field select:focus { border-color: #1d4ed8; }

.field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
}

.close {
    cursor: pointer;
    font-weight: 500;
    color: #6b7280;
    font-size: 18px;
}

.close:hover { color: #111827; }
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

    <div class="card">

        <div class="card-header">
            <span>Gestión de Libros</span>
            <button class="btn btn-primary" onclick="nuevo()">+ Nuevo libro</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>ISBN</th>
                    <th>Editorial</th>
                    <th>Año</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaLibros"></tbody>
        </table>

    </div>

</div>

<!-- MODAL -->
<div class="modal" id="modalLibro">
    <div class="modal-content">

        <div class="modal-header">
            <span id="modalTitle">Nuevo libro</span>
            <span class="close" onclick="cerrarModal()">✕</span>
        </div>

        <div class="modal-body">
            <input type="hidden" id="id">

            <div class="field-row">
                <div class="field">
                    <label>Título</label>
                    <input type="text" id="titulo" placeholder="Ej. Cien años de soledad">
                </div>
                <div class="field">
                    <label>Autor</label>
                    <input type="text" id="autor" placeholder="Ej. Gabriel García Márquez">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Categoría</label>
                    <input type="text" id="categoria" placeholder="Ej. Novela">
                </div>
                <div class="field">
                    <label>ISBN</label>
                    <input type="text" id="isbn" placeholder="Ej. 978-3-16-148410-0">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Editorial</label>
                    <input type="text" id="editorial" placeholder="Ej. Sudamericana">
                </div>
                <div class="field">
                    <label>Año de publicación</label>
                    <input type="number" id="anio_publicacion" placeholder="Ej. 1967">
                </div>
            </div>

            <div class="field">
                <label>Stock</label>
                <input type="number" id="stock" placeholder="Ej. 10" min="0">
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
            <button class="btn btn-success" onclick="guardar()">Guardar</button>
        </div>

    </div>
</div>

<script>

$(document).ready(function(){
    listar();
});

function listar(){
    $.get("../CRUDS_BACK/libros.php?accion=listar", function(data){
        $("#tablaLibros").html(data);
    });
}

function abrirModal(){
    $("#modalLibro").css("display", "flex");
}

function cerrarModal(){
    $("#modalLibro").hide();
}

function nuevo(){
    $("#id").val("");
    $("#titulo").val("");
    $("#autor").val("");
    $("#categoria").val("");
    $("#isbn").val("");
    $("#editorial").val("");
    $("#anio_publicacion").val("");
    $("#stock").val("");
    $("#modalTitle").text("Nuevo libro");
    abrirModal();
}

function guardar(){

    if($("#titulo").val() == "" || $("#autor").val() == ""){
        Swal.fire("Error", "Título y autor son obligatorios", "error");
        return;
    }

    let datos = {
        id:               $("#id").val(),
        titulo:           $("#titulo").val(),
        autor:            $("#autor").val(),
        categoria:        $("#categoria").val(),
        isbn:             $("#isbn").val(),
        editorial:        $("#editorial").val(),
        anio_publicacion: $("#anio_publicacion").val(),
        stock:            $("#stock").val()
    };

    $.post("../CRUDS_BACK/libros.php?accion=guardar", datos, function(res){
        Swal.fire({
            icon: "success",
            title: res,
            timer: 1500,
            showConfirmButton: false
        });
        cerrarModal();
        listar();
    });
}

function editar(id){
    $.get("../CRUDS_BACK/libros.php?accion=editar&id=" + id, function(data){
        $("#id").val(data.id);
        $("#titulo").val(data.titulo);
        $("#autor").val(data.autor);
        $("#categoria").val(data.categoria);
        $("#isbn").val(data.isbn);
        $("#editorial").val(data.editorial);
        $("#anio_publicacion").val(data.anio_publicacion);
        $("#stock").val(data.stock);
        $("#modalTitle").text("Editar libro");
        abrirModal();
    });
}

function eliminar(id){
    Swal.fire({
        title: "¿Eliminar?",
        text: "No podrás revertir esto",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí"
    }).then((result) => {
        if(result.isConfirmed){
            $.get("../CRUDS_BACK/libros.php?accion=eliminar&id=" + id, function(res){
                Swal.fire("Eliminado", res, "success");
                listar();
            });
        }
    });
}

</script>

</body>
</html>