<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Básico</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header>
        <button class="back" onclick="window.location='{{ route('index') }}'">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left-pipe">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M7 6v12" />
            <path d="M18 6l-6 6l6 6" />
        </svg>
    </button>
    </header>

    <main>
        <div>
            <img src="img/logo.png" alt="Logo de la aplicación">

            <form action="#" method="POST">

                <label for="">Usuario</label>
                <input type="text" placeholder="Username" required>

                <label for="">Contraseña</label>
                <input type="password" placeholder="password" required>

                <button id="submit">CONFIRMAR</button>

            </form>

        </div>
    </main>
    <footer>
        <p>© 2024 Katalist. All rights reserved.</p>
    </footer>

</body>

</html>
