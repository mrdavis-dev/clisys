# ClíSys — Sistema de Gestión Clínica

Sistema PHP/MySQL para clínicas. Maneja pacientes, citas, odontogramas, pagos e historial de tratamientos. Diseñado como SaaS multi-clínica y multi-especialidad.

---

## Índice

1. [Stack técnico](#stack-técnico)
2. [Estructura del proyecto](#estructura-del-proyecto)
3. [Instalación](#instalación)
4. [Variables de entorno](#variables-de-entorno)
5. [Migraciones de base de datos](#migraciones-de-base-de-datos)
6. [Arquitectura](#arquitectura)
7. [Capa de seguridad](#capa-de-seguridad)
8. [Sistema de módulos](#sistema-de-módulos)
9. [Planes y límites](#planes-y-límites)
10. [Audit log](#audit-log)
11. [Onboarding de nuevas clínicas](#onboarding-de-nuevas-clínicas)
12. [Reglas de desarrollo](#reglas-de-desarrollo)
13. [Roadmap](#roadmap)

---

## Stack técnico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.x (sin framework) |
| Base de datos | MySQL 8 / MariaDB 10.6+ |
| Frontend | Bootstrap 4, jQuery 3, AngularJS 1.x (odontograma) |
| Email | PHPMailer (SMTP por clínica) |
| PDF | DOMPDF |
| Imágenes | Almacenamiento en disco (`admin/uploads/`) |

---

## Estructura del proyecto

```
clisys/
├── index.php                  # Formulario público de citas
├── signup.php                 # Registro público de nuevas clínicas
├── signup_process.php         # Handler POST del registro
├── insert_exterior.php        # Handler POST del formulario público de citas
├── migration_fase2.sql        # Migración multi-tenancy (clinic_id)
├── migration_fase3.sql        # Migración SaaS (módulos, planes, notas, audit)
├── .env                       # Credenciales reales (NO en git)
├── .env.example               # Plantilla de variables de entorno
│
└── admin/
    ├── index.php              # Login del staff
    ├── inicio.php             # Dashboard — próximas citas
    ├── pacientes.php          # CRUD de pacientes
    ├── odontograma.php        # Mapa dental interactivo (módulo opcional)
    ├── notas.php              # Notas clínicas genéricas (módulo opcional)
    ├── getinfo.php            # Búsqueda de paciente para pagos
    ├── pagos.php              # Formulario de pago
    ├── historial.php          # Historial de tratamientos
    ├── registro_user.php      # Gestión de usuarios del staff
    ├── audit.php              # Visor de audit log
    ├── menu.php               # Barra lateral (condicional por módulo)
    │
    ├── core/                  # Capa de seguridad y utilidades
    │   ├── env.php            # Cargador de .env
    │   ├── Database.php       # Singleton MySQLi
    │   ├── Auth.php           # Guard de sesión + helper h()
    │   ├── Csrf.php           # Generación y verificación de tokens CSRF
    │   ├── Tenant.php         # Resolución de clínica activa (subdomain / session)
    │   ├── Module.php         # Feature flags por clínica
    │   ├── Plan.php           # Límites de suscripción
    │   └── Audit.php          # Trail de acciones críticas
    │
    ├── conexion/
    │   └── config.php         # Bootstrap de BD + Tenant (incluido por todas las páginas)
    │
    ├── functions/             # Handlers de autenticación y registro
    │   ├── login.php
    │   ├── register.php
    │   ├── edit_paciente.php
    │   └── funsaldo.php
    │
    ├── uploads/               # Imágenes de odontograma (servidas directamente)
    │   ├── .htaccess          # Bloquea ejecución PHP en este directorio
    │   └── odontograma/
    │       └── {clinic_id}/   # Separación por clínica
    │
    └── vendor/                # Dependencias Composer (PHPMailer, DOMPDF)
```

---

## Instalación

```bash
# 1. Clona el repositorio
git clone https://github.com/mrdavis-dev/clisys.git
cd clisys

# 2. Copia la plantilla de variables de entorno y edita con tus credenciales
cp .env.example .env

# 3. Instala dependencias PHP (PHPMailer, DOMPDF)
composer install -d admin/

# 4. Instala dependencias Node (dom-to-image para exportar odontograma)
npm install

# 5. Ejecuta las migraciones en orden
mysql -u root -p tu_base_de_datos < migration_fase2.sql
mysql -u root -p tu_base_de_datos < migration_fase3.sql

# 6. Crea el directorio de uploads con los permisos correctos
mkdir -p admin/uploads/odontograma
chmod 755 admin/uploads/odontograma
```

No hay paso de compilación. Despliega directamente en un servidor con PHP habilitado.

---

## Variables de entorno

Copia `.env.example` a `.env` y completa los valores:

```ini
# Base de datos
DB_HOST=localhost
DB_NAME=clinica
DB_USER=root
DB_PASS=

# SMTP (puede sobreescribirse por clínica desde la tabla clinics)
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=usuario@example.com
SMTP_PASSWORD=secreto
SMTP_FROM=noreply@example.com
SMTP_FROM_NAME=ClíSys
```

---

## Migraciones de base de datos

Ejecuta las migraciones **en orden** sobre la base de datos existente:

| Archivo | Qué hace |
|---|---|
| `migration_fase2.sql` | Añade `clinic_id` a las 5 tablas, crea `clinics` y `staff`, renombra columnas con typos (`ocuapacion→ocupacion`, `motivo_consuta→motivo_consulta`) |
| `migration_fase3.sql` | Crea `specialties`, `modules`, `clinic_modules`, `plans`, `clinic_notes`, `audit_log`; extiende `clinics` con `specialty_id`, `plan_id`, columnas SMTP; añade `imagen_path` a `consulta` |

Las migraciones son idempotentes: se pueden ejecutar varias veces sin errores.

### Esquema de tablas

| Tabla | Descripción |
|---|---|
| `clinics` | Un registro por clínica (tenant) |
| `specialties` | Catálogo de especialidades (dental, general_medicine, pediatrics…) |
| `modules` | Catálogo de módulos funcionales |
| `clinic_modules` | Feature flags: qué módulos tiene activos cada clínica |
| `plans` | Tiers de suscripción (free / basic / pro) |
| `staff` | Doctores y personal de cada clínica |
| `pacientes` | Historia clínica completa |
| `citas_tabla` | Citas agendadas |
| `pago` | Pagos y facturación |
| `consulta` | Registros de odontograma |
| `clinic_notes` | Notas clínicas de texto libre |
| `users` | Cuentas de staff (bcrypt cost 12) |
| `audit_log` | Trail de acciones críticas |

---

## Arquitectura

### Dos puntos de entrada

| URL | Descripción |
|---|---|
| `/index.php` | Formulario público de citas (sin sesión) |
| `/signup.php` | Registro público de nuevas clínicas |
| `/admin/index.php` | Login del staff |

### Resolución de clínica (multi-tenancy)

```
1. $_SESSION['clinic_id']   ← establecido al hacer login
2. Subdominio HTTP_HOST      ← anguizola.clisys.com → clinic_id de "anguizola"
3. Fallback clinic_id = 1   ← desarrollo local / instancia single-tenant
```

`Tenant::id()` devuelve el INT que se usa en **todos** los WHERE/INSERT de la aplicación.

### Endpoints AJAX (retornan fragmentos HTML)

| Archivo | Uso |
|---|---|
| `fetch.php` | Búsqueda de pacientes en `pacientes.php` |
| `consulta_odo.php` | Búsqueda en odontograma |
| `viewhistorial.php` | Búsqueda en historial de pagos |
| `get_info_pago.php` | Lookup de paciente en formulario de pago |
| `functions/funsaldo.php` | Saldo actual del paciente |

---

## Capa de seguridad

Cuatro archivos en `admin/core/` que cada request debe usar:

### `Auth.php`
```php
// Al inicio de cada página protegida:
Auth::require();
// Hace: session_start(), verifica $_SESSION['loggedin'],
//       inactividad 30 min, redirect a login.php si falla.

// Helper global de escape:
echo h($dato_de_bd);   // htmlspecialchars con ENT_QUOTES + UTF-8
```

### `Csrf.php`
```php
// En formularios:
<?= Csrf::field() ?>   // <input type="hidden" name="csrf_token" value="...">

// En handlers POST:
Csrf::verify();        // HTTP 403 si el token no coincide
```

### `Database.php`
```php
$db = Database::get();   // Singleton MySQLi; utf8mb4; errores al error_log
```

### `Tenant.php`
```php
Tenant::load($db);   // Llamar una vez por request (config.php lo hace)
$id = Tenant::id();  // INT para usar en queries
```

### Reglas invariables
- Todo SELECT/INSERT/UPDATE/DELETE filtra por `clinic_id = Tenant::id()`
- Todo `echo` de datos de BD o `$_POST` usa `h()`
- Todos los handlers POST llaman `Csrf::verify()` antes de cualquier lógica
- Todas las queries usan `$db->prepare()` + `bind_param()` — nunca concatenación

---

## Sistema de módulos

Cada clínica tiene una lista de módulos habilitados en `clinic_modules`. Esto controla:
- Qué links aparecen en el menú lateral
- Qué páginas son accesibles

```php
// Verificar si un módulo está activo (con cache por request):
Module::enabled('odontogram')      // bool

// Redirigir a inicio.php si no está activo:
Module::require('clinical_notes')  // void
```

| Slug del módulo | Página |
|---|---|
| `odontogram` | `odontograma.php` |
| `clinical_notes` | `notas.php` |
| `payments` | `getinfo.php` / `pagos.php` |
| `history` | `historial.php` |

Las clínicas dentales tienen `odontogram` habilitado por defecto. Las de medicina general no.

---

## Planes y límites

```php
Plan::withinLimit('patients')  // false si se alcanzó el máximo del plan
Plan::withinLimit('users')     // false si se alcanzó el máximo de usuarios
Plan::active()                 // false si plan_expires_at < hoy
Plan::name()                   // "Free" | "Basic" | "Pro"
```

| Plan | Pacientes | Usuarios | Precio/mes |
|---|---|---|---|
| free | 50 | 2 | $0 |
| basic | 500 | 10 | $29.99 |
| pro | ∞ | ∞ | $79.99 |

Los handlers `insert_paciente.php` e `insert_user.php` verifican `Plan::withinLimit()` antes de insertar.

---

## Audit log

`Audit::log()` registra acciones críticas en `audit_log` sin interrumpir la request si falla:

```php
Audit::log('login',          'users',      (string)$userId);
Audit::log('insert_patient', 'pacientes',  (string)$newId);
Audit::log('delete_cita',    'citas_tabla', (string)$id);
```

Acciones registradas actualmente: `login`, `insert_cita`, `insert_patient`, `delete_patient`, `insert_pago`, `insert_odontogram`, `insert_note`, `delete_note`, `insert_user`.

El visor está en `admin/audit.php` (filtrable por acción, últimas 500 entradas).

---

## Onboarding de nuevas clínicas

`/signup.php` permite que una nueva clínica se auto-registre:

1. Completa: nombre de clínica, subdominio, especialidad, plan, datos del admin
2. `signup_process.php` crea la fila en `clinics`, habilita los módulos según la especialidad elegida y crea el primer usuario administrador
3. Redirige al login

**Módulos habilitados automáticamente por especialidad:**
- `dental` → odontogram + clinical_notes + payments + history
- otras → clinical_notes + payments + history

---

## Reglas de desarrollo

```php
// ✅ Correcto — prepared statement + clinic_id
$stmt = $db->prepare('SELECT * FROM pacientes WHERE id = ? AND clinic_id = ?');
$stmt->bind_param('ii', $id, Tenant::id());
$stmt->execute();
$result = $stmt->get_result();

// ✅ Correcto — escape de output
echo h($row['nombre']);

// ✅ Correcto — verificar módulo en página protegida
Auth::require();
require_once __DIR__ . '/conexion/config.php';
Module::require('odontogram');

// ❌ Nunca — concatenación en queries
$db->query("SELECT * FROM pacientes WHERE id = {$_GET['id']}");

// ❌ Nunca — echo sin escapar
echo $row['nombre'];
```

---

## Roadmap

| Fase | Estado | Rama |
|---|---|---|
| **Fase 1** — Seguridad y fundación (prepared statements, CSRF, Auth, env) | ✅ Merged | `main` |
| **Fase 2** — Multi-tenancy (clinic_id en todas las tablas, Tenant, staff dinámico) | ✅ Merged | `main` |
| **Fase 3** — SaaS (módulos, planes, notas clínicas, onboarding, imágenes en disco, audit) | ✅ En PR | `fase-3-saas` |

### Pendiente (post-Fase 3)
- Panel super-admin para gestionar clínicas y activar/desactivar módulos desde UI
- Configuración SMTP por clínica desde la interfaz (actualmente solo en BD)
- Historia clínica genérica estructurada (EAV o JSON) para especialidades no-dentales
- Facturación y cobro de suscripciones
- Script de migración de BLOBs existentes a disco
- Suite de tests automatizados
