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
function editarTarea(id, nombre, idUsuario, esAdmin) {
    document.getElementById('tituloModalTarea').textContent = 'Editar Tarea';
    document.getElementById('id_tarea').value = id;
    document.getElementById('nom_tarea').value = nombre;
    
    // Si es administrador, mostrar el selector de usuario con el valor actual
    if (esAdmin) {
        const selectUsuario = document.getElementById('id_usuario');
        if (selectUsuario) {
            selectUsuario.value = idUsuario || '';
        }
        
        // Mostrar el grupo de usuario si está oculto
        const grupoUsuario = document.getElementById('grupoUsuario');
        if (grupoUsuario) {
            grupoUsuario.style.display = 'block';
        }
    } else {
        // Si es participante, mantener el usuario oculto
        const inputUsuarioHidden = document.getElementById('id_usuario_hidden');
        if (inputUsuarioHidden) {
            inputUsuarioHidden.value = idUsuario || '';
        }
        
        // Ocultar el grupo de usuario
        const grupoUsuario = document.getElementById('grupoUsuario');
        if (grupoUsuario) {
            grupoUsuario.style.display = 'none';
        }
        
        // Cambiar el texto de ayuda
        const helpText = document.getElementById('helpTextUsuario');
        if (helpText) {
            helpText.textContent = '✏️ Solo puedes editar el nombre de tu tarea';
            helpText.style.display = 'block';
        }
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
    
    // Restaurar visibilidad del grupo de usuario si es admin
    const grupoUsuario = document.getElementById('grupoUsuario');
    if (grupoUsuario) {
        grupoUsuario.style.display = 'block';
    }
    
    // Restaurar la selección del usuario al usuario actual (el que tiene "selected" en HTML)
    const selectUsuario = document.getElementById('id_usuario');
    if (selectUsuario) {
        // Buscar la opción con el atributo selected por defecto
        const defaultOption = selectUsuario.querySelector('option[selected]');
        if (defaultOption) {
            selectUsuario.value = defaultOption.value;
        }
    }
    
    // Restaurar texto de ayuda original del administrador
    const helpTextAdmin = document.querySelector('#grupoUsuario .help-text');
    if (helpTextAdmin) {
        helpTextAdmin.textContent = '💡 Por defecto se te asigna a ti, pero puedes cambiarlo';
    }
    
    // Restaurar texto de ayuda para participante
    const helpText = document.getElementById('helpTextUsuario');
    if (helpText) {
        helpText.textContent = '✏️ La tarea se te asignará automáticamente';
    }
    
    // Restaurar la acción del formulario
    const form = document.getElementById('formTarea');
    form.action = '/tareas';
    document.getElementById('methodTarea').value = 'POST';
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
