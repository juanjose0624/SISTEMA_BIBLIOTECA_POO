

const form = document.getElementById("formPQRS");

form.addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("../php/guardar_pqrs.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === "ok") {

            Swal.fire({
                icon: "success",
                title: "Enviado",
                text: "Tu solicitud fue enviada correctamente"
            });

            form.reset();

        } else {

            Swal.fire({
                icon: "error",
                title: "Error",
                text: data.msg || "Algo salió mal"
            });

        }

    })
    .catch(() => {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error de conexión"
        });
    });

});
