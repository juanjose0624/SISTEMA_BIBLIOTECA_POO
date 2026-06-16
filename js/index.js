
document.addEventListener("DOMContentLoaded", function () {

    // BOTÓN TOP
    const btnTop = document.getElementById("btnTop");

    window.onscroll = function () {
        if (document.documentElement.scrollTop > 200) {
            btnTop.style.display = "block";
        } else {
            btnTop.style.display = "none";
        }
    };

    btnTop.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });

    // CARRUSEL
    const carouselElement = document.querySelector('#carouselAutores');

    if (carouselElement) {
        new bootstrap.Carousel(carouselElement, {
            interval: 5000,
            ride: 'carousel',
            pause: false,
            wrap: true
        });
    }

});