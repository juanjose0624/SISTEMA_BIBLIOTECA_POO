document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // 🕒 RELOJ / CALENDARIO
    // =========================
    function actualizarReloj() {
        const ahora = new Date();

        const hora = ahora.toLocaleTimeString('es-CO', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const fecha = ahora.toLocaleDateString('es-CO', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const horaEl = document.getElementById("hora");
        const fechaEl = document.getElementById("fecha");

        if (horaEl && fechaEl) {
            horaEl.textContent = hora;
            fechaEl.textContent = fecha;
        }
    }

    setInterval(actualizarReloj, 1000);
    actualizarReloj();

    // =========================
    // 🔐 LOGOUT CON SWEET + AJAX
    // =========================
    const btnLogout = document.getElementById("btnLogout");

    if (btnLogout) {
        btnLogout.addEventListener("click", function () {

            Swal.fire({
                title: "¿Cerrar sesión?",
                text: "Se cerrará tu sesión de administrador",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, salir",
                cancelButtonText: "Cancelar"
            }).then((result) => {

                if (result.isConfirmed) {

                    fetch("../PHP_USUARIOS/logout.php", {
                        method: "POST"
                    })
                    .then(res => res.json())
                    .then(data => {

                        if (data.status === "ok") {

                            Swal.fire({
                                icon: "success",
                                title: "Sesión cerrada",
                                timer: 1200,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                window.location.href = "../html/login_admin.html";
                            }, 1200);

                        } else {
                            throw new Error();
                        }

                    })
                    .catch(() => {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "No se pudo cerrar sesión"
                        });
                    });

                }

            });

        });
    }

});
// =========================
// 🌙 MODO OSCURO
// =========================
const btnDarkMode = document.getElementById("btnDarkMode");

// Cargar preferencia guardada
if (localStorage.getItem("modo") === "oscuro") {
    document.body.classList.add("dark-mode");
    btnDarkMode.textContent = "☀️";
}

if (btnDarkMode) {
    btnDarkMode.addEventListener("click", () => {

        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {
            localStorage.setItem("modo", "oscuro");
            btnDarkMode.textContent = "☀️";
        } else {
            localStorage.setItem("modo", "claro");
            btnDarkMode.textContent = "🌙";
        }

    });
}