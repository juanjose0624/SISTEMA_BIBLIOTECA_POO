    // Scroll reveal
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
        }
      });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal, .reveal-left, .reveal-right')
            .forEach(el => observer.observe(el));

    // Btn hover fix for dark section
    document.querySelectorAll('.book-section.paper .btn-leer').forEach(btn => {
      btn.addEventListener('mouseenter', () => btn.style.color = '#f5f0e8');
      btn.addEventListener('mouseleave', () => btn.style.color = '#7a3b2e');
    });