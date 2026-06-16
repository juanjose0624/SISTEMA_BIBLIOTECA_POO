document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("formLoginAdmin");

    if (form) {

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const datos = new FormData(form);

            fetch("../PHP_ADMINISTRADORES/login_admin.php", {
                method: "POST",
                body: datos
            })
            .then(res => res.json())
            .then(data => {

                if (data.status === "ok") {

                    Swal.fire({
                        icon: "success",
                        title: "Bienvenido Admin",
                        text: "Acceso concedido",
                        timer: 1500,
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        window.location.href = "../PHP_ADMINISTRADORES/dashboard_admin.php";
                    }, 1500);

                } else {

                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message || "Credenciales incorrectas"
                    });

                }

            })
            .catch(() => {

                Swal.fire({
                    icon: "error",
                    title: "Error del servidor",
                    text: "Intenta nuevamente"
                });

            });

        });

    }

});