# 🔒 Actualización de Permisos: Roles y Asignación de Tareas

## ✅ Cambios Implementados

### 1. **Participantes NO pueden editar roles de usuarios**

#### 🎯 Comportamiento Actual:
- **Administradores**: 
  - ✅ Ven selector dropdown para cambiar roles de usuarios
  - ✅ Pueden cambiar cualquier rol (excepto el del administrador principal)
  - ✅ Pueden eliminar usuarios del proyecto
  
- **Participantes**:
  - ❌ **NO ven** selector de roles
  - ✅ Solo ven un badge con el rol del usuario (no editable)
  - ❌ **NO pueden** eliminar usuarios
  - ℹ️ Mensaje informativo: "Solo el administrador puede gestionar usuarios"

#### 📝 Implementación:
```blade
<!-- En resources/views/proyecto/show.blade.php -->
<div class="usuario-rol">
    @if($permisos['puede_gestionar_usuarios'] && $participacion->id_rols != 1)
        <!-- SOLO ADMINISTRADORES ven este selector -->
        <select name="id_rol" class="select-rol" onchange="this.form.submit()">
            @foreach($roles as $rol)
                <option value="{{ $rol->id_rols }}">{{ $rol->nom_rols }}</option>
            @endforeach
        </select>
    @else
        <!-- TODOS (incluido participantes) ven esto -->
        <span class="badge-rol badge-{{ $participacion->id_rols }}">
            {{ $participacion->rol->nom_rols ?? 'Sin rol' }}
            @if($participacion->id_rols == 1) 👑 @endif
        </span>
    @endif
</div>
```

---

### 2. **Asignación automática de tareas al administrador creador**

#### 🎯 Comportamiento Nuevo:

**Para Administradores al CREAR tarea:**
- ✅ **Se pre-selecciona automáticamente a sí mismo** en el selector
- ✅ Puede cambiar el usuario asignado antes de crear la tarea
- ✅ Indicador visual: "(Tú)" junto a su nombre en el selector
- 💡 Mensaje: "Por defecto se te asigna a ti, pero puedes cambiarlo"

**Para Participantes al CREAR tarea:**
- ✅ Se asignan automáticamente a sí mismos (sin opción de cambio)
- ❌ NO ven selector de usuario
- ℹ️ Mensaje: "La tarea se te asignará automáticamente"

#### 📝 Implementación:

**Vista (resources/views/proyecto/show.blade.php):**
```blade
@if($permisos['es_administrador'])
    <div class="form-group" id="grupoUsuario">
        <label for="id_usuario">Asignar a</label>
        <select id="id_usuario" name="id_usuario">
            @foreach($proyecto->participar as $participacion)
                <option value="{{ $participacion->usuario->id_usuario }}" 
                    {{ $participacion->usuario->id_usuario == session('usuario_id') ? 'selected' : '' }}>
                    {{ $participacion->usuario->nom_usuario }}
                    @if($participacion->usuario->id_usuario == session('usuario_id'))
                        (Tú)
                    @endif
                </option>
            @endforeach
        </select>
        <p class="help-text">💡 Por defecto se te asigna a ti, pero puedes cambiarlo</p>
    </div>
@else
    <input type="hidden" name="id_usuario" id="id_usuario_hidden" value="{{ session('usuario_id') }}">
    <p class="help-text" id="helpTextUsuario">✏️ La tarea se te asignará automáticamente</p>
@endif
```

**Controlador (app/Http/Controllers/TareasController.php):**
```php
// Lógica existente - ya funcionaba correctamente:
if (PermisosHelper::esParticipante($request->id_proyecto, $usuarioId)) {
    // Participante: siempre se asigna a sí mismo
    $idUsuarioFinal = $usuarioId;
} else {
    // Administrador: usa el valor del selector (que por defecto es él mismo)
    if (empty($idUsuarioRequest)) {
        $idUsuarioFinal = $usuarioId;
    } else {
        $idUsuarioFinal = $idUsuarioRequest;
    }
}
```

**JavaScript (public/js/proyectos/show.js):**
```javascript
function resetFormTarea() {
    // ... código anterior ...
    
    // Restaurar la selección al usuario actual (pre-seleccionado en HTML)
    const selectUsuario = document.getElementById('id_usuario');
    if (selectUsuario) {
        const defaultOption = selectUsuario.querySelector('option[selected]');
        if (defaultOption) {
            selectUsuario.value = defaultOption.value;
        }
    }
    
    // Restaurar texto de ayuda
    const helpTextAdmin = document.querySelector('#grupoUsuario .help-text');
    if (helpTextAdmin) {
        helpTextAdmin.textContent = '💡 Por defecto se te asigna a ti, pero puedes cambiarlo';
    }
}
```

---

## 🎬 Flujos de Usuario

### 📋 Crear Tarea como Administrador:
1. Hacer clic en "➕ Nueva Tarea"
2. Modal se abre con:
   - Campo "Nombre de la Tarea" vacío
   - Selector de usuario **pre-seleccionado con tu nombre** + "(Tú)"
   - Mensaje: "💡 Por defecto se te asigna a ti, pero puedes cambiarlo"
3. Opciones:
   - **Opción A**: Dejar tu nombre → Tarea se asigna a ti
   - **Opción B**: Cambiar a otro usuario → Tarea se asigna a ese usuario
4. Click "Guardar" → Tarea creada con el usuario seleccionado

### 📋 Crear Tarea como Participante:
1. Hacer clic en "➕ Nueva Tarea"
2. Modal se abre con:
   - Campo "Nombre de la Tarea" vacío
   - **Sin selector de usuario** (campo hidden)
   - Mensaje: "✏️ La tarea se te asignará automáticamente"
3. Click "Guardar" → Tarea creada asignada a ti (sin opción de cambio)

### 👥 Ver Usuarios como Participante:
1. Click en pestaña "Usuarios"
2. Se muestra:
   - Lista de usuarios del proyecto
   - Badge con el rol de cada usuario (NO editable)
   - Mensaje: "ℹ️ Solo el administrador puede gestionar usuarios"
   - **Sin botón** "➕ Agregar Usuario"
   - **Sin botón** de eliminar usuarios
   - **Sin selector** de roles

### 👥 Gestionar Usuarios como Administrador:
1. Click en pestaña "Usuarios"
2. Se muestra:
   - Lista de usuarios del proyecto
   - **Selector dropdown** para cambiar roles (excepto admin principal)
   - Botón "🗑️ Eliminar" para cada usuario (excepto admin principal)
   - Botón "➕ Agregar Usuario"

---

## 🔐 Validaciones de Seguridad

### Backend (ya existentes):
✅ `PermisosHelper::puedeGestionarUsuarios()` - Solo admin
✅ `PermisosHelper::puedeCrearTareas()` - Admin y Participante
✅ `PermisosHelper::esAdministrador()` - Verificación de rol
✅ `PermisosHelper::esParticipante()` - Verificación de rol

### Frontend:
✅ Selectores de rol ocultos para participantes con `@if($permisos['puede_gestionar_usuarios'])`
✅ Botones de acción ocultos para participantes
✅ Pre-selección de usuario actual para administradores con `selected`
✅ Campo hidden para participantes fuerza asignación automática

---

## 📊 Matriz de Permisos

| Acción | Administrador | Participante |
|--------|--------------|--------------|
| Ver usuarios del proyecto | ✅ | ✅ |
| Ver roles de usuarios | ✅ | ✅ (solo vista) |
| **Editar roles de usuarios** | ✅ | ❌ **NO** |
| Agregar usuarios | ✅ | ❌ |
| Eliminar usuarios | ✅ | ❌ |
| Crear tarea asignada a sí mismo | ✅ | ✅ |
| Crear tarea asignada a otro | ✅ | ❌ |
| **Cambiar usuario al crear tarea** | ✅ | ❌ **NO** |
| Editar nombre de tarea propia | ✅ | ✅ |
| Editar nombre de tarea ajena | ✅ | ❌ |
| Reasignar tarea (cambiar usuario) | ✅ | ❌ |
| Eliminar tarea propia | ✅ | ✅ |
| Eliminar tarea ajena | ✅ | ❌ |

---

## 📁 Archivos Modificados

1. ✅ `resources/views/proyecto/show.blade.php`
   - Selector de usuario pre-seleccionado con usuario actual para admin
   - Indicador "(Tú)" en la opción del usuario actual
   - Mensaje de ayuda diferente para admin y participante

2. ✅ `public/js/proyectos/show.js`
   - Función `resetFormTarea()` restaura selección al usuario actual
   - Manejo de texto de ayuda según el rol

3. ✅ Vista ya tenía la lógica correcta para ocultar selectores de rol a participantes
4. ✅ Controlador ya tenía la lógica correcta de asignación automática

---

## ✨ Resumen de Mejoras UX

### Para Administradores:
- 🎯 **Menos clics**: Ya no necesita buscar su nombre en el selector
- 🔄 **Flexible**: Puede cambiar el usuario si lo necesita
- 👁️ **Visual**: Indicador "(Tú)" claramente visible
- 💡 **Informativo**: Mensaje explica el comportamiento

### Para Participantes:
- 🔒 **Restricción clara**: No ve controles que no puede usar
- 🚫 **Sin confusión**: No puede intentar cambiar roles (UI coherente con permisos)
- ℹ️ **Educativo**: Mensajes informativos explican las limitaciones
- 🎯 **Simplicidad**: Formularios más simples (sin opciones innecesarias)

---

✅ **Todos los cambios implementados y probados**
