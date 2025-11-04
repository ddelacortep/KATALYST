# 📝 Sistema de Edición de Tareas con Gestión de Usuarios

## 🎯 Funcionalidad Implementada

El administrador ahora tiene la capacidad de **editar completamente las tareas**, incluyendo **reasignar usuarios** a las tareas del proyecto.

---

## 👥 Roles y Permisos

### 👑 **Administrador (ROL_ADMINISTRADOR = 1)**

**Permisos sobre tareas:**
- ✅ Crear tareas asignadas a cualquier usuario del proyecto
- ✅ Editar **todas las tareas** del proyecto (nombre + usuario asignado)
- ✅ Eliminar **todas las tareas** del proyecto
- ✅ Reasignar tareas entre usuarios

**Indicadores visuales:**
- Badge "👑 Admin" en cada tarea que puede editar
- Selector de usuario visible en el modal de edición

### 👤 **Participante (ROL_PARTICIPANTE = 2)**

**Permisos sobre tareas:**
- ✅ Crear tareas (se asignan automáticamente a sí mismo)
- ✅ Editar **solo sus propias tareas** (nombre únicamente)
- ✅ Eliminar **solo sus propias tareas**
- ❌ **NO puede reasignar tareas** a otros usuarios

**Indicadores visuales:**
- Badge "✏️ Tu tarea" en las tareas que le pertenecen
- Selector de usuario **oculto** en el modal de edición
- Mensaje: "Solo puedes editar el nombre de tu tarea"

---

## 🔧 Componentes Modificados

### 1. **Vista: `resources/views/proyecto/show.blade.php`**

#### Cambios en la tarjeta de tarea:
```blade
<div class="tarea-card">
    <div class="tarea-header">
        <h3>{{ $tarea->nom_tarea }}</h3>
        @if($permisos['es_administrador'])
            <span class="badge badge-admin" title="Como administrador, puedes editar todos los detalles">👑 Admin</span>
        @elseif($tarea->id_usuario == session('usuario_id'))
            <span class="badge badge-owner" title="Esta es tu tarea">✏️ Tu tarea</span>
        @endif
    </div>
    <!-- ... -->
</div>
```

#### Cambios en el botón de edición:
```blade
<button class="btn-icon" 
    onclick="editarTarea(
        {{ $tarea->id_tarea }}, 
        '{{ addslashes($tarea->nom_tarea) }}', 
        {{ $tarea->id_usuario ?? 'null' }}, 
        {{ $permisos['es_administrador'] ? 'true' : 'false' }}
    )" 
    title="Editar">
    ✏️
</button>
```

#### Modal de edición con selector condicional:
```blade
@if($permisos['es_administrador'])
    <div class="form-group" id="grupoUsuario">
        <label for="id_usuario">Asignar a</label>
        <select id="id_usuario" name="id_usuario">
            <option value="">Sin asignar</option>
            @foreach($proyecto->participar as $participacion)
                <option value="{{ $participacion->usuario->id_usuario }}">
                    {{ $participacion->usuario->nom_usuario }}
                </option>
            @endforeach
        </select>
    </div>
@else
    <input type="hidden" name="id_usuario" id="id_usuario_hidden" value="{{ session('usuario_id') }}">
    <p class="help-text" id="helpTextUsuario">✏️ La tarea se te asignará automáticamente</p>
@endif
```

---

### 2. **JavaScript: `public/js/proyectos/show.js`**

#### Función `editarTarea` actualizada:
```javascript
function editarTarea(id, nombre, idUsuario, esAdmin) {
    document.getElementById('tituloModalTarea').textContent = 'Editar Tarea';
    document.getElementById('id_tarea').value = id;
    document.getElementById('nom_tarea').value = nombre;
    
    // Si es administrador, mostrar el selector de usuario
    if (esAdmin) {
        const selectUsuario = document.getElementById('id_usuario');
        if (selectUsuario) {
            selectUsuario.value = idUsuario || '';
        }
        
        const grupoUsuario = document.getElementById('grupoUsuario');
        if (grupoUsuario) {
            grupoUsuario.style.display = 'block';
        }
    } else {
        // Si es participante, mantener el usuario oculto
        const inputUsuarioHidden = document.getElementById('id_usuario_hidden');
        if (inputUsuarioHidden) {
            inputUsuarioHidden.value = idUsuario || '';
        }
        
        const grupoUsuario = document.getElementById('grupoUsuario');
        if (grupoUsuario) {
            grupoUsuario.style.display = 'none';
        }
        
        const helpText = document.getElementById('helpTextUsuario');
        if (helpText) {
            helpText.textContent = '✏️ Solo puedes editar el nombre de tu tarea';
            helpText.style.display = 'block';
        }
    }
    
    // Cambiar la acción del formulario a PUT
    const form = document.getElementById('formTarea');
    form.action = `/tareas/${id}`;
    document.getElementById('methodTarea').value = 'PUT';
    
    toggleModal('modalTarea');
}
```

---

### 3. **CSS: `public/css/proyectos/proyecto-show.css`**

#### Nuevo estilo para badge de propietario:
```css
.badge-owner {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    font-size: 0.75rem;
    padding: 3px 8px;
    border-radius: 12px;
    margin-left: 10px;
}
```

---

### 4. **Controlador: `app/Http/Controllers/TareasController.php`**

#### Método `update` (ya existente - validación correcta):
```php
public function update(Request $request, $id)
{
    $tarea = Tareas::find($id);
    
    // Verificar permisos para editar
    if (!PermisosHelper::puedeEditarTarea($tarea)) {
        return redirect()->back()->with('error', 'No tienes permisos para editar esta tarea');
    }

    $request->validate([
        'nom_tarea' => 'sometimes|required|string|max:255',
        'id_usuario' => 'sometimes|integer|exists:usuario,id_usuario'
    ]);

    if ($request->has('nom_tarea')) {
        $tarea->nom_tarea = $request->nom_tarea;
    }
    
    // 🔑 CLAVE: Solo administrador puede reasignar tareas
    if ($request->has('id_usuario') && PermisosHelper::esAdministrador($tarea->id_proyecto)) {
        $tarea->id_usuario = $request->id_usuario;
    }

    $tarea->save();
    
    return redirect()->back()->with('success', 'Tarea actualizada correctamente');
}
```

---

## 🎨 Experiencia de Usuario

### Para Administradores:
1. Al hacer clic en **editar** (✏️) en cualquier tarea:
   - Modal se abre con el título "Editar Tarea"
   - Campo de nombre pre-rellenado
   - **Selector de usuario visible** con el usuario actual seleccionado
   - Puede cambiar el usuario asignado
   - Badge "👑 Admin" visible en todas las tareas

2. Al guardar:
   - Se actualiza el nombre y el usuario asignado
   - Mensaje de éxito: "Tarea actualizada correctamente"

### Para Participantes:
1. Al hacer clic en **editar** (✏️) en **su tarea**:
   - Modal se abre con el título "Editar Tarea"
   - Campo de nombre pre-rellenado
   - **Selector de usuario OCULTO**
   - Mensaje: "Solo puedes editar el nombre de tu tarea"
   - Badge "✏️ Tu tarea" visible solo en sus tareas

2. Al guardar:
   - Solo se actualiza el nombre de la tarea
   - El usuario asignado permanece sin cambios
   - Mensaje de éxito: "Tarea actualizada correctamente"

3. **Restricciones:**
   - No ve el botón de editar en tareas de otros usuarios
   - Solo ve botones de acción en sus propias tareas

---

## 🔐 Seguridad

### Validaciones Backend:
1. **PermisosHelper::puedeEditarTarea($tarea)**
   - Verifica que el usuario tenga permisos sobre la tarea
   - Administrador: siempre `true`
   - Participante: solo si `$tarea->id_usuario == $usuarioActual`

2. **Reasignación de usuarios:**
   ```php
   if ($request->has('id_usuario') && PermisosHelper::esAdministrador($tarea->id_proyecto)) {
       $tarea->id_usuario = $request->id_usuario;
   }
   ```
   - Doble verificación: campo presente + rol administrador
   - Si un participante intenta enviar `id_usuario`, se ignora

3. **Validación de datos:**
   ```php
   $request->validate([
       'nom_tarea' => 'sometimes|required|string|max:255',
       'id_usuario' => 'sometimes|integer|exists:usuario,id_usuario'
   ]);
   ```

---

## ✅ Estado Actual

### ✅ Completado:
- [x] Administrador puede editar nombre y usuario de todas las tareas
- [x] Participante puede editar solo el nombre de sus tareas
- [x] Selector de usuario visible/oculto según el rol
- [x] Badges visuales para identificar permisos
- [x] Validaciones de seguridad en backend
- [x] Feedback visual con mensajes de ayuda
- [x] JavaScript adaptativo según el rol

### 📋 Funcionalidades relacionadas ya existentes:
- [x] Sistema de permisos con PermisosHelper
- [x] Creación de tareas con asignación de usuario
- [x] Eliminación de tareas con validación de permisos
- [x] Gestión de usuarios en proyectos

---

## 🚀 Cómo Probarlo

1. **Como Administrador:**
   ```
   1. Inicia sesión como administrador de un proyecto
   2. Entra en un proyecto
   3. Crea una tarea asignada a otro usuario
   4. Edita esa tarea y cambia el usuario asignado
   5. Verifica que se actualiza correctamente
   ```

2. **Como Participante:**
   ```
   1. Inicia sesión como participante de un proyecto
   2. Entra en el proyecto
   3. Crea una tarea (se te asigna automáticamente)
   4. Edita tu tarea (solo puedes cambiar el nombre)
   5. Intenta editar una tarea de otro usuario (no deberías ver el botón)
   ```

---

## 📝 Notas Técnicas

- **Rutas utilizadas:**
  - `PUT /tareas/{id}` → `TareasController@update`
  
- **Campos del formulario:**
  - `nom_tarea`: siempre visible y editable
  - `id_usuario`: visible solo para administradores
  - `_method`: PUT para actualización
  - `id_proyecto`: hidden, requerido para validaciones

- **Tokens CSRF:** Protección automática de Laravel en todos los formularios

---

## 🎓 Arquitectura de Permisos

```
PermisosHelper
├── ROL_ADMINISTRADOR (1)
│   ├── puedeCrearTareas() → true
│   ├── puedeEditarTarea() → true (todas)
│   ├── puedeEliminarTarea() → true (todas)
│   └── puedeGestionarUsuarios() → true
│
└── ROL_PARTICIPANTE (2)
    ├── puedeCrearTareas() → true (asignadas a sí mismo)
    ├── puedeEditarTarea() → true (solo las propias)
    ├── puedeEliminarTarea() → true (solo las propias)
    └── puedeGestionarUsuarios() → false
```

---

## 🔄 Flujo de Edición

```mermaid
Usuario hace clic en Editar
    ↓
JavaScript: editarTarea(id, nombre, idUsuario, esAdmin)
    ↓
¿Es Administrador?
    ├─ Sí → Mostrar selector de usuario + pre-seleccionar usuario actual
    └─ No → Ocultar selector + mantener usuario en hidden
    ↓
Usuario modifica campos disponibles
    ↓
Form submit → PUT /tareas/{id}
    ↓
TareasController@update
    ↓
PermisosHelper::puedeEditarTarea($tarea)
    ├─ false → Error 403
    └─ true → Continuar
    ↓
Actualizar nom_tarea
    ↓
¿Es Admin Y tiene id_usuario en request?
    ├─ Sí → Actualizar id_usuario
    └─ No → Mantener id_usuario sin cambios
    ↓
Guardar tarea → Redirect con mensaje de éxito
```

---

✨ **Sistema completo y funcional con permisos basados en roles**
