# Plan: Modernización SaaS — Clínica Anguizola

## Context

Sistema PHP 5 años de antigüedad para clínica dental. Código procedural sin framework, con inyecciones SQL en 17+ archivos, credenciales hardcodeadas, sin CSRF ni escape de output. El objetivo es modernizarlo en fases hasta convertirlo en SaaS multi-clínica y multi-especialidad.

**Decisiones del usuario:**
- Stack: PHP puro mejorado (sin Laravel/Node)
- Multi-tenancy: BD compartida con `clinic_id` por tabla
- Odontograma: módulo opcional por especialidad
- Enfoque: por fases

---

## Fase 1: Seguridad y Fundación Técnica ✅ COMPLETADA

**Rama:** `fase-1-seguridad-fundacion`

### Archivos nuevos creados

```
/.env                          — credenciales reales (nunca en git)
/.env.example                  — plantilla con valores vacíos
/.gitignore                    — .env, vendor/, node_modules/
/admin/core/env.php            — cargador .env minimalista
/admin/core/Database.php       — singleton MySQLi (reemplaza 4+ mysqli_connect hardcodeados)
/admin/core/Csrf.php           — generar y verificar tokens CSRF
/admin/core/Auth.php           — session_start + guard + timeout 30min + función h()
```

### `admin/core/env.php`
```php
function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        if (!array_key_exists($k, $_ENV)) { $_ENV[$k] = $v; putenv("$k=$v"); }
    }
}
```

### `admin/core/Database.php`
Singleton que centraliza la conexión. Expone `Database::get(): mysqli`. Configura `utf8mb4`. Loguea errores al servidor (nunca al browser). Reemplaza los 4 `mysqli_connect` hardcodeados en: `admin/inicio.php:58`, `admin/consulta_odo.php:5`, `admin/viewhistorial.php:5`, y `admin/conexion/config.php`.

### `admin/core/Csrf.php`
- `Csrf::generate()` — crea token en `$_SESSION['csrf_token']` con `random_bytes(32)`
- `Csrf::verify()` — valida con `hash_equals()`; sale con HTTP 403 si falla
- `Csrf::field()` — emite `<input type="hidden" ...>` ya escapado

### `admin/core/Auth.php`
- `Auth::require()` — `session_start()`, check inactividad 30min, redirect a `login.php` si no hay sesión
- Helper global: `function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }`

---

### `admin/conexion/config.php` — cambio central

```php
require_once __DIR__ . '/../core/env.php';
require_once __DIR__ . '/../core/Database.php';
loadEnv(__DIR__ . '/../../.env');
$db = Database::get();
```

Todos los archivos que ya hacen `include 'conexion/config.php'` siguen funcionando — `$db` sigue disponible.

---

### Correcciones de seguridad aplicadas

#### SQL Injection → Prepared Statements (19 archivos)

| Archivo | Tipo de query | Tipo bind |
|---|---|---|
| `insert_exterior.php` | INSERT citas_tabla | `sssss` |
| `admin/functions/register.php` | INSERT + SELECT users | `sss` / `s` |
| `admin/functions/edit_paciente.php` | UPDATE pacientes (9 campos) | `sssssssi` |
| `admin/functions/funsaldo.php` | SELECT pago (subquery MAX) | `ss` |
| `admin/delete_paciente.php` | DELETE pacientes | `i` |
| `admin/delete_registro.php` | DELETE pago | `i` |
| `admin/fetch.php` | SELECT pacientes LIKE | `ss` |
| `admin/insert.php` | INSERT citas_tabla | `sssss` |
| `admin/insert_paciente.php` | INSERT pacientes (20 campos) | `ssssssssssssssssssss` |
| `admin/insert_odo.php` | INSERT consulta (BLOB) | `send_long_data` |
| `admin/insert_user.php` | INSERT users | `sss` |
| `admin/insert_pagos_send.php` | INSERT pago | `ssssssss` |
| `admin/consulta_odo.php` | SELECT pacientes + consulta LIKE | `s` / `s` |
| `admin/get_info_pago.php` | SELECT pacientes LIKE | `s` |
| `admin/viewhistorial.php` | SELECT pago LIKE | `ss` |
| `admin/inicio.php` | DELETE citas_tabla (loop) | `i` |
| `admin/historial.php` | DELETE pago | `i` |
| `admin/pacientes.php` | DELETE pacientes | `i` |
| `admin/registro_user.php` | DELETE users (loop) | `i` |

**Notas:**
- `register.php` + `insert_user.php`: bcrypt cost subido de `4` → `12`
- `insert_odo.php`: BLOB enviado con `$stmt->send_long_data()`, no con `addslashes`
- `pacientes.php`: eliminado `mysqli_select_db($db, 'clinica')` (nombre incorrecto)

#### CSRF

`Csrf::field()` emitido en todos los formularios mutantes. `Csrf::verify()` llamado como primer paso en todos los handlers POST:
`login.php`, `register.php`, `edit_paciente.php`, `insert.php`, `insert_exterior.php`, `insert_paciente.php`, `insert_odo.php`, `insert_user.php`, `insert_pagos_send.php`, `delete_paciente.php`, `delete_registro.php`, y en los bloques inline de `inicio.php`, `historial.php`, `pacientes.php`, `registro_user.php`.

#### Session guards

8 archivos migrados del bloque copy-paste con dead code (`$dni = $_SESSION['id']` después de `exit`) a `Auth::require()`:
`edit_paciente.php`, `inicio.php`, `historial.php`, `pacientes.php`, `odontograma.php`, `pagos.php`, `registro_user.php`, `getinfo.php`.

#### Output escaping (XSS)

`h()` aplicado a todos los `echo` de datos de BD en: `fetch.php`, `consulta_odo.php`, `viewhistorial.php`, `get_info_pago.php`, `edit_paciente.php` (+ fix atributos HTML sin comillas), `funsaldo.php`, `registro_user.php`, `inicio.php`, `historial.php`.

#### Credenciales SMTP → `.env`

`admin/insert_pagos_send.php`: SMTP config lee desde `$_ENV['SMTP_*']` en lugar de strings hardcodeados.

#### Fix `menu.php`

Corregida key `$_SESSION['uname']` → `$_SESSION['username']`. Añadido `Csrf::field()` al form de logout.

---

## Fase 2: Multi-Tenancy

**Issue:** [#1 — github.com/mrdavis-dev/clisys/issues/1](https://github.com/mrdavis-dev/clisys/issues/1)

### Base de datos

- Nueva tabla `clinics` (`id`, `name`, `subdomain`, `plan`, `active`, `created_at`)
- Nueva tabla `staff` (`id`, `clinic_id`, `name`, `specialty`, `active`) — reemplaza doctores hardcodeados
- Columna `clinic_id INT NOT NULL DEFAULT 1` en las 5 tablas existentes: `pacientes`, `citas_tabla`, `pago`, `consulta`, `users`
- FK `users.clinic_id → clinics.id`
- Script de migración SQL con backfill para datos existentes
- Aprovechar migración para renombrar typos: `ocuapacion → ocupacion`, `motivo_consuta → motivo_consulta`
- Renombrar `citas_tabla → citas`

### Autenticación y sesión

- `login.php`: cargar `clinic_id` al autenticar y guardarlo en `$_SESSION['clinic_id']`
- `Auth::require()`: exponer `$_SESSION['clinic_id']` de forma conveniente
- Nuevo `admin/core/Tenant.php`: detecta subdominio, resuelve `clinic_id` desde `clinics.subdomain`

### Queries

Añadir `AND clinic_id = $_SESSION['clinic_id']` a todos los SELECT, UPDATE y DELETE. Añadir `clinic_id` a todos los INSERT. Archivos afectados: todos los de admin.

### Dropdowns dinámicos

Reemplazar los tres doctores hardcodeados en `index.php`, `admin/inicio.php` y `admin/index.php` por SELECT dinámico desde tabla `staff` filtrado por `clinic_id`.

### Panel super-admin

Directorio `superadmin/` con sesión separada. Vistas: lista de clínicas, crear/desactivar, asignar plan.

### Verificación

- Login como clínica A → solo ve sus datos
- Login como clínica B → solo ve sus datos
- Dropdown de doctores muestra solo los de la clínica activa

---

## Fase 3: Generalización de Especialidades y SaaS

**Issue:** [#2 — github.com/mrdavis-dev/clisys/issues/2](https://github.com/mrdavis-dev/clisys/issues/2)

### Módulos por especialidad

- Tabla `specialties` (`id`, `name`, `slug`)
- `clinics.specialty_id FK → specialties.id`
- Tabla `modules` (`id`, `name`, `slug`) — ej. `odontogram`, `clinical_notes`
- Tabla `clinic_modules` (`clinic_id`, `module_id`) — feature flags
- `Tenant::hasModule(string $slug): bool`
- Odontograma condicional: `if (Tenant::hasModule('odontogram'))` en `menu.php` y `odontograma.php`

### Historia clínica genérica

- Tabla `clinical_notes` (`id`, `clinic_id`, `patient_id`, `author_id`, `content` TEXT, `created_at`)
- `admin/notas.php` + `admin/insert_nota.php` para clínicas no-dentales
- Migrar campos específicos de odontología de `pacientes` a JSON o tabla EAV `patient_attributes`

### Planes y billing

- Tabla `plans` (`id`, `name`, `max_patients`, `max_users`, `modules` JSON, `price_monthly`)
- `clinics.plan_id FK → plans.id` y `clinics.plan_expires_at`
- `Tenant::withinLimit(string $resource): bool`
- Página de upgrade al alcanzar límite del plan

### Onboarding público

- `register.php` (raíz): formulario de registro de nueva clínica
- `register_clinic.php`: crea `clinics` + usuario admin + email de bienvenida
- Validación AJAX de disponibilidad de subdominio

### Almacenamiento de imágenes

- Migrar BLOBs de `consulta.imageData` a disco: `storage/{clinic_id}/{cedula}/{filename}`
- `insert_odo.php`: guardar en disco, almacenar ruta en BD
- `consulta_odo.php`: servir desde ruta, no desde base64 de BLOB

### Audit log

- Tabla `audit_log` (`id`, `clinic_id`, `user_id`, `action`, `table_name`, `record_id`, `created_at`)
- Registrar inserciones y borrados de pacientes, pagos y usuarios

### SMTP por clínica

- Añadir a `clinics`: `smtp_host`, `smtp_user`, `smtp_password`, `smtp_port`, `smtp_from`
- `insert_pagos_send.php`: leer SMTP de la clínica activa; `.env` como fallback

### Verificación

- Clínica dental: odontograma visible y funcional
- Clínica medicina general: no ve odontograma, ve notas clínicas genéricas
- Registro público → nueva clínica operativa en minutos
- Plan free con límite de 50 pacientes: bloquea al 51
- Imágenes de odontograma servidas desde disco

---

## Archivos críticos por fase

| Archivo | Relevancia |
|---|---|
| `admin/conexion/config.php` | Bootstrap de BD — punto de entrada de toda la app |
| `admin/core/Auth.php` | Fase 2: añadir resolución de `clinic_id` aquí |
| `admin/core/Tenant.php` | Fase 2 (nuevo): routing por subdominio y feature flags |
| `insert_exterior.php` | Único endpoint público — alta superficie de ataque |
| `admin/insert_pagos_send.php` | Lógica más compleja: BD + email + PDF |
| `admin/menu.php` | Fase 3: condicionado por módulos del tenant |
