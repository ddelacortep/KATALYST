function crearProyecto() {
    const grid = document.querySelector('.project-grid');
    const nuevoProyecto = document.createElement('div');
    nuevoProyecto.className = 'project-card';
    grid.appendChild(nuevoProyecto);
}