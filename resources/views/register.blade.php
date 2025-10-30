<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100..900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div id="container">
                <div class="left-register">
                    <label for="username">Username *</label>
                    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
                    
                    <label for="email">Email *</label>
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                </div>
                <div class="right-register">
                    <label for="password">Contraseña *</label>
                    <input type="password" name="password" placeholder="Contraseña (mínimo 6 caracteres)" required>
                    
                    <label for="password_confirmation">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                </div>
            </div>
            <div class="submit-div">
                <button id="submit" type="submit">Confirmar</button>
            </div>
        </form>
    </main>
    <footer>
        <p>© 2024 Katalist. All rights reserved.</p>
    </footer>
</body>


</html>
