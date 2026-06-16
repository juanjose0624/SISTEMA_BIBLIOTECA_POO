document.getElementById("formRegistro").addEventListener("submit", function(e){
    e.preventDefault();

    const formData = new FormData(this);

    const nombre = formData.get("nombre").trim();
    const apellido = formData.get("apellido").trim();
    const edad = parseInt(formData.get("edad"));
    const num_doc = formData.get("num_doc");
    const celular = formData.get("celular");
    const correo = formData.get("correo");
    const password = formData.get("password");

    // Expresiones regulares
    const soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/;
    const soloNumeros = /^[0-9]+$/;
    const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // VALIDACIONES
    if (!soloLetras.test(nombre)) {
        return Swal.fire("Error", "El nombre solo debe contener letras", "error");
    }

    if (!soloLetras.test(apellido)) {
        return Swal.fire("Error", "El apellido solo debe contener letras", "error");
    }

    if (edad < 15) {
        return Swal.fire("Error", "Debes tener al menos 15 años", "error");
    }

    if (!soloNumeros.test(num_doc)) {
        return Swal.fire("Error", "El documento solo debe tener números", "error");
    }

    if (!soloNumeros.test(celular) || celular.length !== 10) {
        return Swal.fire("Error", "El celular debe tener 10 dígitos", "error");
    }

    if (!emailValido.test(correo)) {
        return Swal.fire("Error", "Correo inválido", "error");
    }

    if (password.length < 6) {
        return Swal.fire("Error", "La contraseña debe tener mínimo 6 caracteres", "error");
    }

    // ENVÍO
    fetch("../php/registro.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === "ok"){
            Swal.fire("Éxito", "Registro exitoso", "success");
            this.reset();
        } else {
            Swal.fire("Error", data.message, "error");
        }
    })
    .catch(() => {
        Swal.fire("Error", "Error del servidor", "error");
    });
});