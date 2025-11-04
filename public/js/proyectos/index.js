// Gestión de favoritos
let favoritos = JSON.parse(localStorage.getItem('favoritos') || '[]');
let mostrarSoloFavoritos = false;

// Inicializar favoritos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarFavoritos();
});

// Cargar estado de favoritos desde localStorage
function cargarFavoritos() {
    const cards = document.querySelectorAll('.project-card');
    cards.forEach(card => {
        const proyectoId = card.getAttribute('data-proyecto-id');
        const btnFavorito = card.querySelector('.btn-favorito');
        
        if (favoritos.includes(proyectoId)) {
            btnFavorito.classList.add('active');
        }
    });
}

// Toggle favorito individual
function toggleFavorito(proyectoId) {
    event.stopPropagation();
    event.preventDefault();
    
    const proyectoIdStr = proyectoId.toString();
    const card = document.querySelector(`[data-proyecto-id="${proyectoId}"]`);
    const btnFavorito = card.querySelector('.btn-favorito');
    
    if (favoritos.includes(proyectoIdStr)) {
        // Quitar de favoritos
        favoritos = favoritos.filter(id => id !== proyectoIdStr);
        btnFavorito.classList.remove('active');
    } else {
        // Agregar a favoritos
        favoritos.push(proyectoIdStr);
        btnFavorito.classList.add('active');
    }
    
    // Guardar en localStorage
    localStorage.setItem('favoritos', JSON.stringify(favoritos));
    
    // Si está activo el filtro de favoritos, actualizar vista
    if (mostrarSoloFavoritos) {
        filtrarProyectos();
    }
}

// Toggle vista de favoritos
function toggleFavoritos() {
    mostrarSoloFavoritos = !mostrarSoloFavoritos;
    const btnFavoritos = document.getElementById('btnFavoritos');
    const sidebarItem = btnFavoritos.querySelector('.sidebar-item-favoritos');
    
    if (mostrarSoloFavoritos) {
        sidebarItem.classList.add('active');
        sidebarItem.querySelector('span').textContent = '⭐ Todos los proyectos';
    } else {
        sidebarItem.classList.remove('active');
        sidebarItem.querySelector('span').textContent = '⭐ Favoritos';
    }
    
    filtrarProyectos();
}

// Filtrar proyectos según favoritos
function filtrarProyectos() {
    const cards = document.querySelectorAll('.project-card');
    
    cards.forEach(card => {
        const proyectoId = card.getAttribute('data-proyecto-id');
        
        if (mostrarSoloFavoritos) {
            // Mostrar solo favoritos
            if (favoritos.includes(proyectoId)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        } else {
            // Mostrar todos
            card.classList.remove('hidden');
        }
    });
}

// Código antiguo del modal (si existe)
const btnCrear = document.getElementById('btnCrear');
        const modalOverlay = document.getElementById('modalOverlay');
        const btnCerrar = document.getElementById('btnCerrar');
        const btnGuardar = document.getElementById('btnGuardar');
        const projectGrid = document.getElementById('projectGrid');
        const nombreProyecto = document.getElementById('nombreProyecto');
        const descripcionProyecto = document.getElementById('descripcionProyecto');

        // Abrir modal
        if (btnCrear) {
            btnCrear.addEventListener('click', () => {
                modalOverlay.style.display = 'flex';
            });
        }

        // Cerrar modal
        if (btnCerrar) {
            btnCerrar.addEventListener('click', () => {
                modalOverlay.style.display = 'none';
            });
        }

        // Guardar proyecto
        if (btnGuardar) {
            btnGuardar.addEventListener('click', () => {
                const nombre = nombreProyecto.value.trim();
                const descripcion = descripcionProyecto.value.trim();

                if (nombre === '' || descripcion === '') {
                    alert('Por favor completa todos los campos.');
                    return;
                }

                // Crear tarjeta en el grid
                const card = document.createElement('div');
                card.className = 'project-card';
                card.textContent = `${nombre} - ${descripcion}`;
                projectGrid.appendChild(card);

                // Limpiar y cerrar modal
                nombreProyecto.value = '';
                descripcionProyecto.value = '';
                modalOverlay.style.display = 'none';
            });
        }

        // Cerrar modal al hacer click fuera de la caja
        if (modalOverlay) {
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) {
                    modalOverlay.style.display = 'none';
                }
            });
        }