# 📁 Estructura del Proyecto KATALYST

## 🎨 Assets Organizados

### CSS (public/css/)
```
css/
├── auth/
│   ├── login.css          # Estilos página de login
│   └── register.css       # Estilos página de registro
├── proyectos/
│   ├── index.css          # Estilos listado de proyectos
│   └── proyecto-show.css  # Estilos detalle de proyecto (tabs: tareas, usuarios, roles)
├── roles/
│   └── roles.css          # Estilos gestión de roles
├── create.css             # Estilos formulario creación
├── index.css              # Estilos página principal
└── style.css              # Estilos globales
```

### JavaScript (public/js/)
```
js/
├── proyectos/
│   ├── index.js           # Lógica listado de proyectos
│   └── show.js            # Lógica detalle de proyecto (tabs, modales, CRUD tareas)
└── roles/
    └── roles.js           # Lógica gestión de roles
```

## 🏗️ Backend

### Controladores (app/Http/Controllers/)
```
Controllers/
├── AuthController.php           # Autenticación (login, logout, register)
├── ProyectoController.php       # CRUD proyectos + gestión usuarios
├── TareasController.php         # CRUD tareas con permisos
├── RolsController.php           # Gestión de roles
├── UsuarioController.php        # Gestión de usuarios
├── ParticiparController.php     # Relación usuarios-proyectos
└── EstadoTareaController.php    # Estados de tareas
```

### Modelos (app/Models/)
```
Models/
├── Proyecto.php         # Modelo de proyectos
├── Tareas.php          # Modelo de tareas
├── Usuario.php         # Modelo de usuarios
├── Rols.php            # Modelo de roles
├── Participar.php      # Modelo pivote usuarios-proyectos
├── EstadoTarea.php     # Modelo de estados
└── User.php            # Modelo de autenticación Laravel
```

### Helpers (app/Helpers/)
```
Helpers/
└── PermisosHelper.php  # Lógica centralizada de permisos
```

## 📄 Vistas (resources/views/)
```
views/
├── proyecto/
│   └── show.blade.php      # Vista detalle proyecto (3 tabs)
├── roles/
│   └── index.blade.php     # Vista gestión de roles
├── plantillas/             # Layouts y componentes reutilizables
├── create.blade.php        # Formulario creación
├── index.blade.php         # Página principal
├── login.blade.php         # Página de login
├── register.blade.php      # Página de registro
├── proyectos.blade.php     # Listado de proyectos
└── welcome.blade.php       # Landing page
```

## 🗄️ Base de Datos

### Tablas Principales
- **proyecto** - Proyectos
- **tareas** - Tareas de proyectos
- **usuario** - Usuarios del sistema
- **rols** - Roles disponibles (Administrador, Editor, Visualizador)
- **participar** - Relación usuarios-proyectos-roles
- **estado_tarea** - Estados de las tareas

### Scripts SQL (database/)
```
database/
├── migrations/              # Migraciones de Laravel
├── seeders/                # Seeders (RolesSeeder)
├── fix_proyecto_identity.sql
└── insert_estados_tarea.sql
```

## 🔐 Sistema de Permisos

### Roles Disponibles
1. **Administrador (ID: 1)**
   - Crear, editar y eliminar tareas
   - Asignar tareas a cualquier usuario
   - Gestionar usuarios del proyecto
   - No puede ser eliminado ni cambiar su rol

2. **Editor (ID: 2)**
   - Crear tareas
   - Solo puede asignarse tareas a sí mismo
   - Ver todas las tareas del proyecto

3. **Visualizador (ID: 3)**
   - Solo ver tareas
   - Sin permisos de edición

## 📋 Rutas Principales

### Autenticación
- `GET /login` - Formulario de login
- `POST /login` - Procesar login
- `POST /logout` - Cerrar sesión
- `GET /register` - Formulario de registro
- `POST /register` - Procesar registro

### Proyectos
- `GET /proyectos` - Listado de proyectos
- `GET /proyectos/{id}` - Detalle de proyecto
- `POST /proyectos` - Crear proyecto
- `PUT /proyectos/{id}` - Actualizar proyecto
- `DELETE /proyectos/{id}` - Eliminar proyecto

### Gestión de Usuarios en Proyectos
- `POST /proyectos/{id}/agregar-usuario` - Agregar usuario
- `DELETE /proyectos/{id}/eliminar-usuario/{userId}` - Eliminar usuario
- `PUT /proyectos/{id}/actualizar-rol-usuario/{userId}` - Cambiar rol

### Tareas
- `POST /tareas` - Crear tarea
- `PUT /tareas/{id}` - Actualizar tarea
- `DELETE /tareas/{id}` - Eliminar tarea

### Roles
- `GET /roles` - Gestión de roles
- `POST /roles` - Crear rol
- `PUT /roles/{id}` - Actualizar rol
- `DELETE /roles/{id}` - Eliminar rol

## 🎯 Características Principales

### Interfaz de Usuario
- ✅ Diseño consistente con paleta #67B0AD
- ✅ Sistema de tabs (Tareas, Usuarios, Roles)
- ✅ Modales para CRUD operations
- ✅ Feedback visual (mensajes de éxito/error)
- ✅ UI adaptativa según permisos del usuario

### Gestión de Tareas
- ✅ Creación automática de estado "Pendiente"
- ✅ Asignación de tareas a usuarios
- ✅ Transacciones para garantizar integridad
- ✅ Generación manual de IDs (compatible con SQL Server sin IDENTITY)

### Seguridad
- ✅ Validación de permisos en cada acción
- ✅ Protección CSRF
- ✅ Sesiones seguras
- ✅ Middleware de autenticación

## 🚀 Comandos Útiles

```bash
# Iniciar servidor
php artisan serve

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ejecutar seeders
php artisan db:seed --class=RolesSeeder

# Ejecutar migraciones
php artisan migrate
```

## 📝 Notas Técnicas

- **Framework**: Laravel 12.34.0
- **PHP**: 8.2.29
- **Base de Datos**: SQL Server (sqlsrv)
- **Peculiaridades**: SQL Server sin IDENTITY en PKs (IDs generados manualmente)
