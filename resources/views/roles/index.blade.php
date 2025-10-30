<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Roles - KATALYST</title>
    <link rel="stylesheet" href="{{ asset('css/roles.css') }}">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <h1>Gestión de Roles</h1>
                <a href="{{ route('proyectos') }}" class="btn-back">← Volver</a>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="section-header">
            <h2>Roles Disponibles</h2>
            <button class="btn-primary" onclick="toggleModal('modalRol')">+ Crear Rol</button>
        </div>

        <div class="roles-list">
            @forelse($roles as $rol)
                <div class="rol-card">
                    <h3>{{ $rol->nom_rols }}</h3>
                    <p class="rol-id">ID: {{ $rol->id_rols }}</p>
                    <div class="rol-actions">
                        <button class="btn-icon" onclick="editarRol({{ $rol->id_rols }}, '{{ $rol->nom_rols }}')" title="Editar">✏️</button>
                        <form action="{{ route('roles.destroy', $rol->id_rols) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon btn-danger" title="Eliminar">🗑️</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="empty-message">No hay roles creados. ¡Crea el primero!</p>
            @endforelse
        </div>
    </div>

    <!-- Modal Crear/Editar Rol -->
    <div id="modalRol" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleModal('modalRol')">&times;</span>
            <h2 id="tituloModalRol">Crear Rol</h2>
            <form id="formRol" action="{{ route('roles.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodRol" value="POST">
                
                <div class="form-group">
                    <label for="nom_rols">Nombre del Rol *</label>
                    <input type="text" id="nom_rols" name="nom_rols" required placeholder="Ej: Administrador, Editor, Viewer">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="toggleModal('modalRol')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/roles.js') }}"></script>
</body>
</html>
