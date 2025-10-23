<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página del Proyecto</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/create.css">
</head>

<body>
    <button class="back" onclick="window.location='{{ route('proyectos') }}'">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left-pipe">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M7 6v12" />
            <path d="M18 6l-6 6l6 6" />
        </svg>
    </button>
    <main class="container">
        <form action="">
            <label for="">TÍTULO DEL PROYECTO</label>
            <input type="text" placeholder="Nombre del proyecto">
            <label for="">AÑADIR COLABORADORES</label>
            <input type="text" placeholder="Buscar colaborador">
        </form>

        <button id="submit">Añadir</button>
    </main>
</body>

</html>
