# 🔐 Sistema de Gestión de Módulos y Permisos

Este sistema permite gestionar módulos, permisos y roles de forma sencilla mediante comandos Artisan. 

## 📋 Comandos Disponibles

### 1. 🆕 Agregar Módulos - `module:add`

Crea un nuevo módulo con sus permisos correspondientes.

**Sintaxis:**
```powershell
php artisan module:add {nombre} {nombre_español} --actions={acciones} [--user-actions={acciones_user}]
```

**Parámetros:**
- `{nombre}`: Nombre del módulo (en inglés, sin espacios)
- `{nombre_español}`: Nombre descriptivo en español (entre comillas si tiene espacios)
- `--actions`: Lista de acciones separadas por comas (obligatorio)
- `--user-actions`: Acciones que tendrá el rol 'user' (opcional)

**¿Qué hace este comando?**
1. ✅ Crea el módulo en la tabla `modules`
2. ✅ Crea los permisos en la tabla `permissions` (formato: `modulo.accion`)
3. ✅ Asigna **TODOS** los permisos al rol `admin` automáticamente
4. ✅ Asigna solo los permisos especificados al rol `user` (si se indica)

**Ejemplos:**
```powershell
# Módulo básico (solo admin tendrá permisos)
php artisan module:add reportes "Reportes" --actions=index,create,edit,trash

# Módulo con permisos específicos para user
php artisan module:add inventario "Inventario" --actions=index,create,edit,trash,export --user-actions=index,create

# Módulo de solo lectura para user
php artisan module:add configuracion "Configuración" --actions=index,edit --user-actions=index

# Módulo complejo
php artisan module:add gastos "Gestión de Gastos" --actions=index,create,edit,trash,approve,export --user-actions=index,create,edit
```

---

### 2. 📋 Listar Módulos - `module:list`

Muestra todos los módulos existentes y sus permisos.

**Sintaxis:**
```powershell
php artisan module:list [--permissions] [--role={nombre_rol}]
```

**Opciones:**
- `--permissions`: Muestra las acciones de cada módulo
- `--role={nombre}`: Muestra qué permisos tiene un rol específico

**Ejemplos:**
```powershell
# Listar todos los módulos
php artisan module:list

# Listar módulos con sus acciones
php artisan module:list --permissions

# Ver qué permisos tiene el rol admin
php artisan module:list --role=admin

# Ver qué permisos tiene el rol user
php artisan module:list --role=user

# Combinado: ver módulos con acciones y permisos del user
php artisan module:list --permissions --role=user
```

**Salida de ejemplo:**
```
📋 Módulos existentes:

🔹 reportes (Reportes)
   Acciones:
     • index → reportes.index
     • create → reportes.create
     • edit → reportes.edit
     • delete → reportes.delete
   Permisos del rol 'user':
     ✓ reportes.index

🔹 inventario (Inventario)
   Acciones:
     • index → inventario.index
     • create → inventario.create
     • edit → inventario.edit
   Permisos del rol 'user':
     ✓ inventario.index
     ✓ inventario.create
```

---

### 3. ⚙️ Gestionar Permisos de Roles - `role:permissions`

Permite agregar, quitar o listar permisos de un rol específico.

**Sintaxis:**
```powershell
php artisan role:permissions {accion} {rol} [--permissions={permisos}]
```

**Parámetros:**
- `{accion}`: `add`, `remove`, o `list`
- `{rol}`: Nombre del rol (`admin`, `user`, etc.)
- `--permissions`: Lista de permisos separados por comas (para add/remove)

**Ejemplos:**
```powershell
# Listar permisos de un rol
php artisan role:permissions list admin
php artisan role:permissions list user

# Agregar permisos a un rol
php artisan role:permissions add user --permissions=reportes.create,reportes.edit
php artisan role:permissions add user --permissions=inventario.delete

# Quitar permisos de un rol
php artisan role:permissions remove user --permissions=reportes.delete
php artisan role:permissions remove user --permissions=inventario.export
```

**Salida de ejemplo:**
```powershell
# php artisan role:permissions list user
Permisos del rol 'user':
  - reportes.index
  - reportes.create
  - inventario.index
  - inventario.create
  - gastos.index
```

---

## 🚀 Flujo de Trabajo Típico

### 1. **Configuración Inicial (solo una vez)**
```powershell
# Crear roles básicos y usuarios de ejemplo
php artisan db:seed --class=RoleAndPermissionSeeder
```

### 2. **Agregar Módulos Nuevos**
```powershell
# Crear módulo de reportes
php artisan module:add reportes "Reportes" --actions=index,create,edit,delete --user-actions=index

# Crear módulo de inventario
php artisan module:add inventario "Inventario" --actions=index,create,edit,delete,export --user-actions=index,create,edit

# Verificar que se creó correctamente
php artisan module:list --permissions
```

### 3. **Ajustar Permisos (si es necesario)**
```powershell
# Dar más permisos al user
php artisan role:permissions add user --permissions=reportes.create,reportes.edit

# Quitar permisos si es necesario
php artisan role:permissions remove user --permissions=inventario.delete

# Verificar cambios
php artisan role:permissions list user
```

---

## 📊 Estructura de Base de Datos

### Tabla `modules`
```sql
id | name      | nombre_es | actions (JSON)
1  | reportes  | Reportes  | {"index":"reportes.index","create":"reportes.create",...}
2  | inventario| Inventario| {"index":"inventario.index","create":"inventario.create",...}
```

### Tabla `permissions`
```sql
id | name              | guard_name
1  | reportes.index    | web
2  | reportes.create   | web
3  | reportes.edit     | web
4  | inventario.index  | web
```

### Tabla `role_has_permissions`
```sql
permission_id | role_id
1            | 1        # admin tiene reportes.index
2            | 1        # admin tiene reportes.create
1            | 2        # user tiene reportes.index
4            | 2        # user tiene inventario.index
```

---

## ✅ Ventajas del Sistema

- **🎯 Simplicidad**: Un comando para crear módulos completos
- **🔒 Seguridad**: Admin siempre tiene todos los permisos
- **🔧 Flexibilidad**: Control granular de permisos por rol
- **🚀 Escalabilidad**: Agregar módulos sin afectar existentes
- **📋 Visibilidad**: Comandos para listar y verificar permisos
- **🛡️ No Destructivo**: Nunca elimina permisos existentes

---

## 💡 Casos de Uso Comunes

### Módulo Solo para Admin
```powershell
php artisan module:add configuracion "Configuración del Sistema" --actions=index,edit,backup,restore
# Solo admin tendrá estos permisos
```

### Módulo con Permisos Limitados para User
```powershell
php artisan module:add clientes "Gestión de Clientes" --actions=index,create,edit,delete,export --user-actions=index,create,edit
# User puede ver, crear y editar, pero no eliminar ni exportar
```

### Módulo de Solo Lectura para User
```powershell
php artisan module:add estadisticas "Estadísticas" --actions=index,export,pdf --user-actions=index
# User solo puede ver estadísticas, no exportar
```

### Ajustar Permisos Después
```powershell
# Dar permiso de exportar estadísticas al user
php artisan role:permissions add user --permissions=estadisticas.export

# Verificar que se aplicó
php artisan role:permissions list user
```

---

## 🔍 Troubleshooting

### Error: "El rol 'admin' no existe"
```powershell
# Ejecutar primero la configuración inicial
php artisan db:seed --class=RoleAndPermissionSeeder
```

### Ver todos los módulos y permisos
```powershell
php artisan module:list --permissions --role=admin
php artisan module:list --permissions --role=user
```

### Verificar permisos de un usuario específico
```php
// En tu código PHP
$user = User::find(1);
$permissions = $user->getAllPermissions()->pluck('name');
dd($permissions);
```
