<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos</title>
    <link rel="stylesheet" href="css/proyectos.css">
    <link rel="stylesheet" href="css/style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <main class="container">
        <div class="header">
            <img class="logo" src="img/logo.png" alt="">
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
                <button class="btn-crear" onclick="crearProyecto()">Crear</button>
            </div>

            <div class="main-content">
                <div class="project-grid" id="projectGrid">
                </div>
            </div>

            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                ...
            </div>
        </div>
    </main>
</body>
<script src="proyectos.js"></script>


</html>
