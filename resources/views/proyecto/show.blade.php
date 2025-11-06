<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $proyecto->nom_proyecto }} - KATALYST</title>
    <link rel="stylesheet" href="{{ asset('css/proyectos/proyecto-show.css') }}">
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

            @if($permisos['es_participante'])
                <div class="alert alert-info">
                    <strong>� Modo Participante:</strong> Puedes crear tareas asignadas a ti y editar/eliminar solo tus propias tareas.
                </div>
            @endif

            <div class="tareas-table-container">
                @if($proyecto->tareas->count() > 0)
                    <table class="tareas-table">
                        <thead>
                            <tr>
                                <th class="col-numero">#</th>
                                <th class="col-tarea">Tarea</th>
                                <th class="col-asignado">Asignado a</th>
                                <th class="col-estado">Estado</th>
                                <th class="col-fecha">Fecha Creación</th>
                                @if($permisos['es_administrador'])
                                    <th class="col-permisos">Permisos</th>
                                @endif
                                <th class="col-acciones">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proyecto->tareas as $index => $tarea)
                                @php
                                    $puedeEditar = $permisos['es_administrador'] || ($permisos['es_participante'] && $tarea->id_usuario == auth()->id());
                                    $puedeEliminar = $permisos['es_administrador'] || ($permisos['es_participante'] && $tarea->id_usuario == auth()->id());
                                    $esMiTarea = $tarea->id_usuario == auth()->id();
                                @endphp
                                <tr class="{{ $esMiTarea ? 'mi-tarea' : '' }}">
                                    <td class="col-numero">{{ $index + 1 }}</td>
                                    <td class="col-tarea">
                                        <span class="tarea-nombre">{{ $tarea->nom_tarea }}</span>
                                    </td>
                                    <td class="col-asignado">
                                        <div class="usuario-cell">
                                            <span class="usuario-icon">👤</span>
                                            <span>{{ $tarea->usuario->nom_usuario ?? 'Sin asignar' }}</span>
                                            @if($esMiTarea)
                                                <span class="badge-mini badge-owner">Tú</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="col-estado">
                                        @php
                                            $estadoActual = $tarea->estadoTarea->nom_estat ?? 'Pendiente';
                                            $claseBadge = match($estadoActual) {
                                                'En Progreso' => 'badge-en-progreso',
                                                'Completada' => 'badge-completada',
                                                default => 'badge-pendiente'
                                            };
                                        @endphp
                                        
                                        @if($puedeEditar)
                                            <form action="{{ route('estado.update', $tarea->id_tarea) }}" method="POST" class="form-estado">
                                                @csrf
                                                @method('PUT')
                                                <select name="nom_estat" class="select-estado {{ $claseBadge }}" onchange="this.form.submit()">
                                                    <option value="Pendiente" {{ $estadoActual == 'Pendiente' ? 'selected' : '' }}>
                                                        🔴 Pendiente
                                                    </option>
                                                    <option value="En Progreso" {{ $estadoActual == 'En Progreso' ? 'selected' : '' }}>
                                                        🔵 En Progreso
                                                    </option>
                                                    <option value="Completada" {{ $estadoActual == 'Completada' ? 'selected' : '' }}>
                                                        ✅ Completada
                                                    </option>
                                                </select>
                                            </form>
                                        @else
                                            <span class="badge-estado {{ $claseBadge }}">
                                                {{ $estadoActual }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="col-fecha">
                                        {{ $tarea->fecha_creacion ? \Carbon\Carbon::parse($tarea->fecha_creacion)->format('d/m/Y') : '-' }}
                                    </td>
                                    @if($permisos['es_administrador'])
                                        <td class="col-permisos">
                                            <span class="badge badge-admin-mini">👑 Admin</span>
                                        </td>
                                    @endif
                                    <td class="col-acciones">
                                        <div class="acciones-group">
                                            @if($puedeEditar)
                                                <button class="btn-tabla btn-editar" 
                                                    onclick="editarTarea({{ $tarea->id_tarea }}, '{{ addslashes($tarea->nom_tarea) }}', {{ $tarea->id_usuario ?? 'null' }}, {{ $permisos['es_administrador'] ? 'true' : 'false' }})" 
                                                    title="Editar">
                                                    ✏️
                                                </button>
                                            @endif
                                            
                                            @if($puedeEliminar)
                                                <form action="{{ route('tareas.destroy', $tarea->id_tarea) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-tabla btn-eliminar" title="Eliminar">🗑️</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="empty-message">No hay tareas en este proyecto. @if($permisos['puede_crear_tareas'])¡Crea la primera!@endif</p>
                @endif
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
                                <form action="{{ route('participacion.updateRol', [$proyecto->id_proyecto, $participacion->id_usuario]) }}" method="POST" class="form-inline">
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
                                <form action="{{ route('participacion.destroy', [$proyecto->id_proyecto, $participacion->id_usuario]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario del proyecto?')">
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
                <h2>Roles del Sistema</h2>
                <p class="help-text">Los roles son predefinidos y no se pueden modificar</p>
            </div>

            <div class="roles-list">
                @forelse($roles as $rol)
                    <div class="rol-card">
                        <div class="rol-header">
                            <h3>{{ $rol->nom_rols }}</h3>
                            @if($rol->id_rols == 1)
                                <span class="badge badge-admin">👑 Sistema</span>
                            @else
                                <span class="badge badge-participant">👥 Predeterminado</span>
                            @endif
                        </div>
                        <p class="rol-descripcion">{{ $rol->descripcion }}</p>
                    </div>
                @empty
                    <p class="empty-message">No hay roles en el sistema.</p>
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
                    <div class="form-group" id="grupoUsuario">
                        <label for="id_usuario">Asignar a</label>
                        <select id="id_usuario" name="id_usuario">
                            @foreach($proyecto->participar as $participacion)
                                <option value="{{ $participacion->usuario->id_usuario }}" 
                                    {{ $participacion->usuario->id_usuario == auth()->id() ? 'selected' : '' }}>
                                    {{ $participacion->usuario->nom_usuario }}
                                    @if($participacion->usuario->id_usuario == auth()->id())
                                        (Tú)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="help-text">💡 Por defecto se te asigna a ti, pero puedes cambiarlo</p>
                    </div>
                @else
                    <input type="hidden" name="id_usuario" id="id_usuario_hidden" value="{{ auth()->id() }}">
                    <p class="help-text" id="helpTextUsuario">✏️ La tarea se te asignará automáticamente</p>
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
            <form action="{{ route('participacion.store', $proyecto->id_proyecto) }}" method="POST">
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

    <script src="{{ asset('js/proyectos/show.js') }}"></script>
</body>
</html>
