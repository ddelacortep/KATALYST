<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos</title>
    <link rel="stylesheet" href="{{ asset('css/proyectos/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <main class="container">
        <div class="header">
            <img class="logo" src="{{ asset('img/logo.png') }}" alt="">
            <div class="user-section">
                @if(session('usuario_nombre'))
                    <span class="username-display">{{ session('usuario_nombre') }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout" title="Cerrar sesión">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M9 12h12l-3 -3" />
                                <path d="M18 15l3 -3" />
                            </svg>
                        </button>
                    </form>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    </svg>
                @endif
            </div>
        </div>

        <div class="content">
            <div class="sidebar">
                <div class="sidebar-content">
                    <button id="btnFavoritos" class="sidebar-link" onclick="toggleFavoritos()">
                        <div class="sidebar-item sidebar-item-favoritos">
                            <span>⭐ Favoritos</span>
                        </div>
                    </button>
                </div>
                <a href="{{ route('create') }}">
                    <button class="btn-crear">Crear</button>
                </a>
            </div>

            <div class="main-content">
                <div class="project-grid" id="projectGrid">
                    @if(isset($proyectos))
                        @foreach($proyectos as $proyecto)
                            <div class="project-card" data-proyecto-id="{{ $proyecto->id_proyecto }}">
                                <button class="btn-favorito" onclick="toggleFavorito({{ $proyecto->id_proyecto }})" title="Agregar a favoritos">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="star-icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                    </svg>
                                </button>
                                <a href="{{ route('proyectos.show', $proyecto->id_proyecto) }}" class="project-link">
                                    <div class="project-card-content">
                                        <h3 class="project-title">{{ $proyecto->nom_proyecto }}</h3>
                                        @if(isset($proyecto->colaboradores))
                                            <p class="project-colaboradores">{{ $proyecto->colaboradores }}</p>
                                        @endif
                                    </div>
                                </a>
                                <form action="{{ route('proyectos.destroy', $proyecto->id_proyecto) }}" method="POST" class="delete-form" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este proyecto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Eliminar proyecto">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </main>

    @if(session('success'))
        <script>
            alert('{{ session('success') }}');
        </script>
    @endif

    @if(session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
    @endif

    <script src="{{ asset('js/proyectos/index.js') }}"></script>
</body>

</html>