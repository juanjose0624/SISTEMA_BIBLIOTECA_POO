document.getElementById("formLogin").addEventListener("submit", function(e){
    e.preventDefault(); // evita recarga

    const formData = new FormData(this); // 👈 AQUÍ se define

    fetch("../php/login.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {

        console.log("Respuesta servidor:", data);

        try {
            const json = JSON.parse(data);

            if(json.status === "ok"){
                Swal.fire({
                    icon: "success",
                    title: "Bienvenido"
                });

                setTimeout(() => {
                    window.location.href = "../PHP_USUARIOS/dashboard.php";
                }, 1500);

            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: json.message
                });
            }

        } catch (e) {
            console.error("Error JSON:", data);
            Swal.fire({
                icon: "error",
                title: "Error en servidor",
                text: "Revisa consola"
            });
        }

    })
    .catch(err => {
        console.error("Fetch error:", err);
        Swal.fire({
            icon: "error",
            title: "Error del servidor"
        });
    });

});