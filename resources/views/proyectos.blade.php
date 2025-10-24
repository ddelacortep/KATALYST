<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos</title>
    <link rel="stylesheet" href="{{ asset('css/proyectos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <main class="container">
        <div class="header">
            <img class="logo" src="{{ asset('img/logo.png') }}" alt="">
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
            </svg>
        </div>

        <div class="content">
            <div class="sidebar">
                <div class="sidebar-content">
                    <div class="sidebar-item"></div>
                </div>
                <a href="{{ route('create') }}">
                    <button class="btn-crear">Crear</button>
                </a>
            </div>

            <div class="main-content">
                <div class="project-grid" id="projectGrid">
                    @if(isset($proyectos))
                        @foreach($proyectos as $proyecto)
                            <div class="project-card">
                                <div class="project-card-content">
                                    <h3 class="project-title">{{ $proyecto->nom }}</h3>
                                    @if($proyecto->colaboradores)
                                        <p class="project-colaboradores">{{ $proyecto->colaboradores }}</p>
                                    @endif
                                </div>
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
</body>

</html>