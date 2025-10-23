const btnCrear = document.getElementById('btnCrear');
        const modalOverlay = document.getElementById('modalOverlay');
        const btnCerrar = document.getElementById('btnCerrar');
        const btnGuardar = document.getElementById('btnGuardar');
        const projectGrid = document.getElementById('projectGrid');
        const nombreProyecto = document.getElementById('nombreProyecto');
        const descripcionProyecto = document.getElementById('descripcionProyecto');

        // Abrir modal
        btnCrear.addEventListener('click', () => {
            modalOverlay.style.display = 'flex';
        });

        // Cerrar modal
        btnCerrar.addEventListener('click', () => {
            modalOverlay.style.display = 'none';
        });

        // Guardar proyecto
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

        // Cerrar modal al hacer click fuera de la caja
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                modalOverlay.style.display = 'none';
            }
        });