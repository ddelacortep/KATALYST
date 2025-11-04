// Función para abrir/cerrar pestañas
function openTab(tabName) {
    // Ocultar todos los contenidos
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });

    // Desactivar todos los botones
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
    });

    // Mostrar el contenido seleccionado
    document.getElementById(tabName).classList.add('active');

    // Activar el botón correspondiente
    event.target.classList.add('active');
}

// Función para mostrar/ocultar modales
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal.style.display === 'block') {
        modal.style.display = 'none';
        // Resetear formulario si se cierra
        if (modalId === 'modalTarea') {
            resetFormTarea();
        } else if (modalId === 'modalRol') {
            resetFormRol();
        }
    } else {
        modal.style.display = 'block';
    }
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Función para editar tarea
function editarTarea(id, nombre, descripcion, idUsuario) {
    document.getElementById('tituloModalTarea').textContent = 'Editar Tarea';
    document.getElementById('id_tarea').value = id;
    document.getElementById('nom_tarea').value = nombre;
    document.getElementById('descripcion_tarea').value = descripcion || '';
    
    // Solo establecer el usuario si el selector existe (admin)
    const selectUsuario = document.getElementById('id_usuario');
    if (selectUsuario) {
        selectUsuario.value = idUsuario || '';
    }
    
    // Cambiar la acción del formulario y método
    const form = document.getElementById('formTarea');
    form.action = `/tareas/${id}`;
    document.getElementById('methodTarea').value = 'PUT';
    
    toggleModal('modalTarea');
}

// Función para resetear formulario de tarea
function resetFormTarea() {
    document.getElementById('tituloModalTarea').textContent = 'Nueva Tarea';
    document.getElementById('formTarea').reset();
    document.getElementById('id_tarea').value = '';
    
    // Obtener el ID del proyecto desde el input hidden
    const idProyecto = document.querySelector('input[name="id_proyecto"]').value;
    
    // Restaurar la acción del formulario
    const form = document.getElementById('formTarea');
    form.action = '/tareas';
    document.getElementById('methodTarea').value = 'POST';
}

// Función para editar rol
function editarRol(id, nombre) {
    document.getElementById('tituloModalRol').textContent = 'Editar Rol';
    document.getElementById('id_rol').value = id;
    document.getElementById('nom_rols').value = nombre;
    
    // Cambiar la acción del formulario y método
    const form = document.getElementById('formRol');
    form.action = `/roles/${id}`;
    document.getElementById('methodRol').value = 'PUT';
    
    toggleModal('modalRol');
}

// Función para resetear formulario de rol
function resetFormRol() {
    document.getElementById('tituloModalRol').textContent = 'Crear Rol';
    document.getElementById('formRol').reset();
    document.getElementById('id_rol').value = '';
    
    // Restaurar la acción del formulario
    const form = document.getElementById('formRol');
    form.action = '/roles';
    document.getElementById('methodRol').value = 'POST';
}

// Auto-ocultar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
});

// Confirmación antes de eliminar
document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const mensaje = this.getAttribute('onsubmit').match(/'([^']+)'/)[1];
        if (!confirm(mensaje)) {
            e.preventDefault();
        }
    });
});
