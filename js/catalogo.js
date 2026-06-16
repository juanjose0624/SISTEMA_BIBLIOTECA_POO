const buscador = document.getElementById("buscador");
const filtro = document.getElementById("filtroCategoria");
const resultados = document.getElementById("resultados");

// 🚀 función principal
function cargarLibros() {

    const q = buscador.value;
    const categoria = filtro.value;

    fetch(`../php/buscar_catalogo.php?q=${q}&categoria=${categoria}`)
        .then(res => res.json())
        .then(data => {

            resultados.innerHTML = "";

            if (data.length === 0) {
                resultados.innerHTML = `
                    <p class="text-center text-muted">
                        No se encontraron libros 📚
                    </p>
                `;
                return;
            }

            data.forEach(libro => {

                const card = `
                <div class="col-md-4">
                    <div class="card shadow p-3 h-100">
                        <h5>${libro.titulo}</h5>
                        <p><strong>Autor:</strong> ${libro.autor}</p>
                        <p><strong>Categoría:</strong> ${libro.categoria}</p>
                        <span class="badge bg-success">Disponible</span>
                    </div>
                </div>
                `;

                resultados.innerHTML += card;

            });

        });
}

// 🔄 eventos
buscador.addEventListener("input", cargarLibros);
filtro.addEventListener("change", cargarLibros);

// 🚀 carga inicial
cargarLibros();