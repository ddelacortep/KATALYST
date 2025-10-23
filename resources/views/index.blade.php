<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <header> 
        <h2>KATALYST</h2>
        <div class="button-group">
            <a href="{{ route('register') }}">
                <button type="button"><p id="btn">Sign in</p></button>
            </a>
            <a href="{{ route('login') }}">
                <button type="button"><p id="btn">Log in</p></button>
            </a>
        </div>
    </header>

    <div id="main">
        <h1>Welcome to Katalist</h1>
        <p>Your ultimate platform for managing and organizing your projects efficiently.</p>
        <div id="logoContainer">
            <img id="logo" src="../images/projects.png" alt="Katalist Logo">
        </div>

        <button id="getStarted">Get Started</button>

    </div>

    <div>
        <footer>
            <p>© 2024 Katalist. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>