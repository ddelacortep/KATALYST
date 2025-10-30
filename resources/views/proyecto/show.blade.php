<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $proyecto->nom_proyecto }} - KATALYST</title>
    <link rel="stylesheet" href="{{ asset('css/proyecto-show.css') }}">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <h1>{{ $proyecto->nom_proyecto }}</h1>
                <a href="{{ route('proyectos') }}" class="btn-back">← Volver a Proyectos</a>
            </div>
        </header>

        <!-- Mensajes de éxito/error -->
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

        <!-- Pestañas -->
        <div class="tabs">
            <button class="tab-button active" onclick="openTab('tareas')">Tareas</button>
            <button class="tab-button" onclick="openTab('usuarios')">Usuarios</button>
            <button class="tab-button" onclick="openTab('roles')">Roles</button>
        </div>

        <!-- Contenido de Tareas -->
        <div id="tareas" class="tab-content active">
            <div class="section-header">
                <h2>Tareas del Proyecto</h2>
                @if($permisos['puede_crear_tareas'])
                    <button class="btn-primary" onclick="toggleModal('modalTarea')">+ Nueva Tarea</button>
                @endif
            </div>

            @if($permisos['es_visualizador'])
                <div class="alert alert-info">
                    <strong>👁️ Modo Visualizador:</strong> Solo puedes ver las tareas. No puedes crear ni editar.
                </div>
            @elseif($permisos['es_editor'])
                <div class="alert alert-info">
                    <strong>✏️ Modo Editor:</strong> Puedes crear tareas y editarlas. Solo el administrador puede eliminarlas.
                </div>
            @endif

            <div class="tareas-list">
                @forelse($proyecto->tareas as $tarea)
                    <div class="tarea-card">
                        <div class="tarea-header">
                            <h3>{{ $tarea->nom_tarea }}</h3>
                        </div>
                        <div class="tarea-footer">
                            <span class="tarea-usuario">
                                👤 {{ $tarea->usuario->nom_usuario ?? 'Sin asignar' }}
                            </span>
                            <div class="tarea-actions">
                                @php
                                    $puedeEditar = $permisos['es_administrador'] || ($permisos['es_editor'] && $tarea->id_usuario == session('usuario_id'));
                                @endphp
                                
                                @if($puedeEditar)
                                    <button class="btn-icon" onclick="editarTarea({{ $tarea->id_tarea }}, '{{ addslashes($tarea->nom_tarea) }}', {{ $tarea->id_usuario ?? 'null' }})" title="Editar">
                                        ✏️
                                    </button>
                                @endif
                                
                                @if($permisos['puede_eliminar_tareas'])
                                    <form action="{{ route('tareas.destroy', $tarea->id_tarea) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-danger" title="Eliminar">🗑️</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No hay tareas en este proyecto. @if($permisos['puede_crear_tareas'])¡Crea la primera!@endif</p>
                @endforelse
            </div>
        </div>

        <!-- Contenido de Usuarios -->
        <div id="usuarios" class="tab-content">
            <div class="section-header">
                <h2>Usuarios del Proyecto</h2>
                @if($permisos['puede_gestionar_usuarios'])
                    <button class="btn-primary" onclick="toggleModal('modalUsuario')">+ Agregar Usuario</button>
                @endif
            </div>

            @unless($permisos['puede_gestionar_usuarios'])
                <div class="alert alert-info">
                    <strong>ℹ️ Solo el administrador puede gestionar usuarios</strong>
                </div>
            @endunless

            <div class="usuarios-list">
                @forelse($proyecto->participar as $participacion)
                    <div class="usuario-card">
                        <div class="usuario-info">
                            <h3>{{ $participacion->usuario->nom_usuario }}</h3>
                            <p>{{ $participacion->usuario->email }}</p>
                        </div>
                        <div class="usuario-rol">
                            @if($permisos['puede_gestionar_usuarios'] && $participacion->id_rols != 1)
                                <form action="{{ route('proyectos.usuarios.actualizarRol', [$proyecto->id_proyecto, $participacion->id_usuario]) }}" method="POST" class="form-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="id_rol" class="select-rol" onchange="this.form.submit()">
                                        @foreach($roles as $rol)
                                            <option value="{{ $rol->id_rols }}" {{ $participacion->id_rols == $rol->id_rols ? 'selected' : '' }}>
                                                {{ $rol->nom_rols }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <span class="badge-rol badge-{{ $participacion->id_rols }}">
                                    {{ $participacion->rol->nom_rols ?? 'Sin rol' }}
                                    @if($participacion->id_rols == 1) 👑 @endif
                                </span>
                            @endif
                        </div>
                        <div class="usuario-actions">
                            @if($permisos['puede_gestionar_usuarios'] && $participacion->id_rols != 1)
                                <form action="{{ route('proyectos.usuarios.eliminar', [$proyecto->id_proyecto, $participacion->id_usuario]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario del proyecto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-danger" title="Eliminar">🗑️</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="empty-message">No hay usuarios en este proyecto.</p>
                @endforelse
            </div>
        </div>

        <!-- Contenido de Roles -->
        <div id="roles" class="tab-content">
            <div class="section-header">
                <h2>Gestión de Roles</h2>
                <button class="btn-primary" onclick="toggleModal('modalRol')">+ Crear Rol</button>
            </div>

            <div class="roles-list">
                @forelse($roles as $rol)
                    <div class="rol-card">
                        <h3>{{ $rol->nom_rols }}</h3>
                        <p>ID: {{ $rol->id_rols }}</p>
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
                    <p class="empty-message">No hay roles creados.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Nueva Tarea -->
    <div id="modalTarea" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleModal('modalTarea')">&times;</span>
            <h2 id="tituloModalTarea">Nueva Tarea</h2>
            <form id="formTarea" action="{{ route('tareas.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodTarea" value="POST">
                <input type="hidden" name="id_proyecto" value="{{ $proyecto->id_proyecto }}">
                <input type="hidden" name="id_tarea" id="id_tarea" value="">
                
                <div class="form-group">
                    <label for="nom_tarea">Nombre de la Tarea *</label>
                    <input type="text" id="nom_tarea" name="nom_tarea" required placeholder="Ej: Diseñar interfaz">
                </div>

                @if($permisos['es_administrador'])
                    <div class="form-group">
                        <label for="id_usuario">Asignar a</label>
                        <select id="id_usuario" name="id_usuario">
                            <option value="">Sin asignar</option>
                            @foreach($proyecto->participar as $participacion)
                                <option value="{{ $participacion->usuario->id_usuario }}">
                                    {{ $participacion->usuario->nom_usuario }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="id_usuario" value="{{ session('usuario_id') }}">
                    <p class="help-text">✏️ La tarea se te asignará automáticamente</p>
                @endif

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="toggleModal('modalTarea')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Agregar Usuario -->
    <div id="modalUsuario" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleModal('modalUsuario')">&times;</span>
            <h2>Agregar Usuario al Proyecto</h2>
            <form action="{{ route('proyectos.usuarios.agregar', $proyecto->id_proyecto) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="id_usuario_agregar">Seleccionar Usuario *</label>
                    <select id="id_usuario_agregar" name="id_usuario" required>
                        <option value="">Selecciona un usuario</option>
                        @foreach($todosUsuarios as $usuario)
                            @php
                                $yaParticipa = $proyecto->participar->contains('id_usuario', $usuario->id_usuario);
                            @endphp
                            @if(!$yaParticipa)
                                <option value="{{ $usuario->id_usuario }}">
                                    {{ $usuario->nom_usuario }} ({{ $usuario->email }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_rol_agregar">Rol *</label>
                    <select id="id_rol_agregar" name="id_rol" required>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rols }}">{{ $rol->nom_rols }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="toggleModal('modalUsuario')">Cancelar</button>
                    <button type="submit" class="btn-primary">Agregar</button>
                </div>
            </form>
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
                <input type="hidden" name="id_rol" id="id_rol" value="">
                
                <div class="form-group">
                    <label for="nom_rols">Nombre del Rol *</label>
                    <input type="text" id="nom_rols" name="nom_rols" required>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="toggleModal('modalRol')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/proyecto-show.js') }}"></script>
</body>
</html>
