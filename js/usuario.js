document.addEventListener("DOMContentLoaded", function(){

    // 🕒 RELOJ
    function actualizarReloj() {
        const ahora = new Date();

        const hora = ahora.toLocaleTimeString();

        const opciones = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };

        const fecha = ahora.toLocaleDateString('es-ES', opciones);

        const horaEl = document.getElementById("hora");
        const fechaEl = document.getElementById("fecha");

        if (horaEl && fechaEl) {
            horaEl.textContent = hora;
            fechaEl.textContent = fecha;
        }
    }

    setInterval(actualizarReloj, 1000);
    actualizarReloj();


    // 🚪 LOGOUT
    const btnLogout = document.getElementById("btnLogout");

    if (btnLogout) {
        btnLogout.addEventListener("click", function(){

            Swal.fire({
                title: "¿Cerrar sesión?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí",
                cancelButtonText: "Cancelar"
            }).then((result) => {

                if (result.isConfirmed) {

                    fetch("../PHP_USUARIOS/logout.php", {
                        method: "POST"
                    })
                    .then(res => res.json())
                    .then(data => {

                        if (data.status === "ok") {

                            Swal.fire("OK", "Sesión cerrada", "success");

                            setTimeout(() => {
                                window.location.href = "../html/login.html";
                            }, 1000);

                        }

                    });

                }

            });

        });
    }
const input = document.getElementById("buscadorLibro");
const resultados = document.getElementById("resultadosLibros");
const hiddenInput = document.getElementById("libroSeleccionado");

if (input) {

    input.addEventListener("input", function() {

        const query = input.value.trim();

        if (query.length < 2) {
            resultados.innerHTML = "";
            return;
        }

        fetch(`buscar_libros.php?q=${query}`)
            .then(res => res.json())
            .then(data => {

                resultados.innerHTML = "";

                data.forEach(libro => {

                    const item = document.createElement("a");
                    item.classList.add("list-group-item", "list-group-item-action");
                    item.textContent = libro.titulo;

                    item.addEventListener("click", () => {
                        input.value = libro.titulo;
                        hiddenInput.value = libro.id;
                        resultados.innerHTML = "";
                    });

                    resultados.appendChild(item);
                });

            });

    });

}
// 🌙 MODO OSCURO
const toggleBtn = document.getElementById("toggleTheme");

// aplicar tema guardado
if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark-mode");
    if (toggleBtn) toggleBtn.textContent = "☀️ Modo claro";
}

if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {

        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {
            localStorage.setItem("theme", "dark");
            toggleBtn.textContent = "☀️ Modo claro";
        } else {
            localStorage.setItem("theme", "light");
            toggleBtn.textContent = "🌙 Modo oscuro";
        }

    });
}
document.querySelectorAll(".btn-devolver").forEach(btn => {

    btn.addEventListener("click", function() {

        const id = this.dataset.id;

        Swal.fire({
            title: "¿Devolver libro?",
            text: "Esta acción actualizará el sistema",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, devolver",
            cancelButtonText: "Cancelar"
        }).then(result => {

            if (result.isConfirmed) {

                fetch("devolver.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id=${id}`
                })
                .then(res => res.json())
                .then(data => {

                    if (data.status === "ok") {

                        Swal.fire({
                            icon: "success",
                            title: "Devuelto",
                            text: "Libro devuelto correctamente"
                        }).then(() => {
                            location.reload();
                        });

                    } else {

                        Swal.fire({
                            icon: "error", 
                            title: "Error",
                            text: "No se pudo devolver"
                        });

                    }

                });

            }

        });

    });

});
const btnHist = document.getElementById("toggleHistorial");
const historial = document.getElementById("historial");

if (btnHist) {
    btnHist.addEventListener("click", () => {

        if (historial.style.display === "none") {
            historial.style.display = "block";
            btnHist.textContent = "Ocultar historial";
        } else {
            historial.style.display = "none";
            btnHist.textContent = "Ver historial";
        }

    });
}

    // 🔔 ALERTAS (UNA SOLA VEZ Y BIEN HECHAS)
    const params = new URLSearchParams(window.location.search);

    if (params.get("error") === "limite") {
        Swal.fire({
            icon: 'warning',
            title: 'Límite alcanzado',
            text: 'Ya tienes 3 libros prestados'
        });
    }

    if (params.get("error") === "stock") {
        Swal.fire({
            icon: 'error',
            title: 'Sin stock',
            text: 'Este libro no está disponible'
        });
    }

    if (params.get("error") === "general") {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un problema inesperado'
        });
    }

    if (params.get("ok") === "prestado") {
        Swal.fire({
            icon: 'success',
            title: 'Libro prestado',
            text: 'Disfruta tu lectura 📚'
        });
    }

    if (params.get("ok") === "devuelto") {
        Swal.fire({
            icon: 'success',
            title: 'Libro devuelto',
            text: 'Gracias por devolverlo 📚'
        });
    }
if (params.get("ok") === "actualizado") {
    Swal.fire({
        icon: 'success',
        title: 'Datos actualizados',
        text: 'Tu información fue actualizada correctamente'
    });
}

    // 🧹 LIMPIAR URL (PRO)
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
    }

});

document.getElementById("formPrestarLibro").addEventListener("submit", function(e) {
    e.preventDefault(); // evita recarga

    const libro_id = document.getElementById("libroSeleccionado").value;

    if (!libro_id) {
        document.getElementById("respuestaAjax").innerHTML =
            '<div class="alert alert-warning">Selecciona un libro primero</div>';
        return;
    }

    const formData = new FormData();
    formData.append("libro_id", libro_id);

    fetch("../PHP_USUARIOS/prestar.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === "ok") {
            document.getElementById("respuestaAjax").innerHTML =
                `<div class="alert alert-success">${data.message}</div>`;
                 setTimeout(() => {
            location.reload();
        }, 800);
        } else {
            document.getElementById("respuestaAjax").innerHTML =
                `<div class="alert alert-danger">${data.message}</div>`;
        }

        // limpiar selección
        document.getElementById("libroSeleccionado").value = "";

    })
    .catch(error => {
        document.getElementById("respuestaAjax").innerHTML =
            '<div class="alert alert-danger">Error en la petición</div>';
        console.error(error);
    });
});
function devolverLibro(id) {

    fetch("../CRUDS_BACK/prestamos.php?accion=devolver&id=" + id)
    .then(res => res.text())
    .then(data => {

        Swal.fire({
            icon: 'success',
            title: 'Libro devuelto',
            text: data
        });

        setTimeout(() => location.reload(), 5000);

    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo devolver el libro'
        });
    });
}