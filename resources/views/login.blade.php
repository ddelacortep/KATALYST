<!DOCTYPE html>
<html lang="es" style="background-color: #222222;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Básico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">

</head>

<body>

    <header>
        <button class="back" onclick="window.location='{{ route('index') }}'">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
            stroke="#8F8F8F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left-pipe">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M7 6v12" />
            <path d="M18 6l-6 6l6 6" />
        </svg>
    </button>
    </header>

    <main>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div id="container">
                <div class="left-login">
                    <div class="logo-container">
                        <img src="img/logo.png" alt="Imagen Katalyst">
                    </div>
                    <input type="text" name="username" placeholder="Username o Email" value="{{ old('username') }}" required>

                    <input type="password" name="password" placeholder="Contraseña" required>

                    <div class="submit-div">
                        <button id="submit" type="submit">Confirmar</button>
                    </div>
                </div>
                <div class="right-login">
                    <img src="img/gato_katalyst.png" alt="" class="gato_katalyst">
                </div>
            </div>
        </form>
    </main>
</body>
</html>
