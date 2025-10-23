<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
</head>

<body>
    <button class="back" onclick="window.location='{{ route('index') }}'">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left-pipe">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M7 6v12" />
            <path d="M18 6l-6 6l6 6" />
        </svg>
    </button>
    <main>
        <div class="logo-container">
            <img src="img/logo.png" alt="Imagen Katalyst">
        </div>
        <div id="container">
            <form action="" class="left-register">
                <label for="">Nombre *</label>
                <input type="text" placeholder="Nombre" required>
                <label for="">Apellidos *</label>
                <input type="text" placeholder="Apellidos" required>
                <label for="">Username *</label>
                <input type="text" placeholder="Username" required>
            </form>
            <form action="" class="right-register">
                <label for="">Mail *</label>
                <input type="mail" placeholder="Mail" required>
                <label for="">Contraseña *</label>
                <input type="password" placeholder="Contraseña" required>
                <label for="">Confirmar Constraseña *</label>
                <input type="password" placeholder="Confirmar Constraseña" required>
            </form>
        </div>
        <form action="">
            <div class="submit-div">
                <button id="submit">Confirmar</button>
            </div>
        </form>
    </main>
    <footer>
        <p>© 2024 Katalist. All rights reserved.</p>
    </footer>
</body>

</html>
