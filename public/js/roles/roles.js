// Función para mostrar/ocultar modales
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal.style.display === 'block') {
        modal.style.display = 'none';
        resetFormRol();
    } else {
        modal.style.display = 'block';
    }
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('modalRol');
    if (event.target === modal) {
        modal.style.display = 'none';
        resetFormRol();
    }
}

// Función para editar rol
function editarRol(id, nombre) {
    document.getElementById('tituloModalRol').textContent = 'Editar Rol';
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
