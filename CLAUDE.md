# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**ClíSys** is a multi-specialty SaaS clinic management system (PHP/MySQL). Originally a single dental clinic app (Clínica Anguizola), now fully multi-tenant. Handles patient records, appointments, dental odontograms, payments, clinical notes, audit logging, and staff administration across multiple clinics.

## Setup

```bash
# 1. Copy env template and fill in credentials
cp .env.example .env

# 2. Install PHP dependencies (PHPMailer, DOMPDF)
composer install -d admin/

# 3. Install Node dependencies (dom-to-image, Font Awesome)
npm install
```

No build step, test suite, linter, or CI pipeline. Deploy directly to a PHP-enabled web server.

## Security layer (`admin/core/`)

Six files bootstrap every request:

- `admin/core/env.php` — `loadEnv(path)` reads `.env` into `$_ENV`
- `admin/core/Database.php` — `Database::get()` singleton MySQLi; auto-called via `admin/conexion/config.php`
- `admin/core/Csrf.php` — `Csrf::generate()`, `Csrf::verify()`, `Csrf::field()`
- `admin/core/Auth.php` — `Auth::require()`, `Auth::requireSuperAdmin()`, `Auth::requireRole(array)`, `Auth::hasRole(array)`, `Auth::isSuperAdmin()`; also defines global `h(string): string`
- `admin/core/Tenant.php` — `Tenant::load($db)`, `Tenant::id()` — resolves clinic from session → subdomain → fallback id=1
- `admin/core/Module.php` — `Module::enabled(slug)`, `Module::require(slug)` — per-clinic feature flags
- `admin/core/Plan.php` — `Plan::withinLimit(type)`, `Plan::active()`, `Plan::name()` — subscription enforcement
- `admin/core/Audit.php` — `Audit::log(action, entity, entityId)` — writes to `audit_log` table

**Rules:**
- Every protected page starts with `Auth::require()` (30-min inactivity, redirect to login.php)
- Superadmin-only pages use `Auth::requireSuperAdmin()` instead
- Role-restricted pages call `Auth::requireRole(['admin','medico'])` after `Auth::require()`
- Module-gated pages call `Module::require('slug')` after auth
- Every POST mutation handler calls `Csrf::verify()` first
- Every form includes `<?= Csrf::field() ?>`
- All queries use `$db->prepare()` + `bind_param()` — never string concatenation
- All echoes of DB/user data use `h()` — never bare `echo $row[...]`
- All tenant-scoped queries filter by `clinic_id = Tenant::id()`

## Architecture

**Entry points:**
- `/index.php` — Public appointment booking form → `citas_tabla`
- `/signup.php` + `/signup_process.php` — Public SaaS onboarding (clinic + admin account creation)
- `/admin/index.php` — Staff login
- `/admin/superadmin/index.php` — Super admin dashboard (separate role, no clinic scope)

**Admin module files** (`/admin/*.php`): each page calls `Auth::require()`, includes `menu.php` (shared nav), renders HTML inline. No framework, ORM, or MVC separation.

**Key admin pages:**
| File | Purpose | Table(s) |
|---|---|---|
| `inicio.php` | Dashboard / appointments | `citas_tabla` |
| `pacientes.php` | Patient CRUD | `pacientes` |
| `odontograma.php` | Interactive tooth chart (module-gated) | `consulta` |
| `notas.php` | Clinical notes (module-gated: `clinical_notes`) | `clinic_notes` |
| `getinfo.php` / `pagos.php` | Payment entry | `pago` |
| `historial.php` | Treatment history | `pago` |
| `registro_user.php` | Staff account management | `users` |
| `audit.php` | Audit log viewer (admin only) | `audit_log` |

**Superadmin panel** (`/admin/superadmin/`):
| File | Purpose |
|---|---|
| `index.php` | Dashboard — counts all clinics/users/patients |
| `clinics.php` | CRUD for clinics |
| `clinic_users.php` | Manage users per clinic |
| `plans.php` | CRUD for subscription plans |
| `modules.php` | Toggle modules per clinic |

**AJAX search endpoints** (return HTML fragments, jQuery):
- `fetch.php` — patient search
- `consulta_odo.php` — odontogram search
- `viewhistorial.php` — payment history search
- `get_info_pago.php` — patient lookup for payment form
- `functions/funsaldo.php` — balance lookup

**Email** (`admin/insert_pagos_send.php`): PHPMailer via SMTP, credentials from `.env`.

**PDF generation**: DOMPDF (`admin/insert_pagos_send.php`), invoices.

**Odontogram** (`admin/odontograma.php`): AngularJS 1.x + `dom-to-image` for export + `admin/js/jquery-odontograma.js`. BLOB images in `consulta`. Requires `Module::enabled('odontogram')`.

## Database schema (13 tables)

All clinic-scoped tables have `clinic_id INT NOT NULL`.

| Table | Purpose |
|---|---|
| `specialties` | Clinic specialties (dental, general, etc.) |
| `modules` | Available feature modules (slug, name) |
| `plans` | Subscription plans (max_patients, max_users) |
| `clinics` | Tenant registry (subdomain, plan_id, plan_expires_at, active) |
| `clinic_modules` | Per-clinic module toggles |
| `staff` | Doctors/staff with specialties (replaces hardcoded names) |
| `pacientes` | Patients — 21 cols incl. medical history; typos: `ocuapacion`, `motivo_consuta` |
| `citas_tabla` | Appointments (no FK to pacientes, stores patient name) |
| `pago` | Payments; links via `cedula` string |
| `consulta` | Odontogram records + BLOB image; links via `cedula` |
| `users` | Staff accounts (bcrypt cost 12); role: admin/medico/recepcion/superadmin |
| `clinic_notes` | Clinical notes per patient |
| `audit_log` | Audit trail (clinic_id, user_id, action, entity, entity_id, ip) |

## Query pattern

```php
$stmt = $db->prepare('SELECT * FROM pacientes WHERE id = ? AND clinic_id = ?');
$stmt->bind_param('ii', $id, Tenant::id());
$stmt->execute();
$result = $stmt->get_result();
```

## Roles

| Role | Access |
|---|---|
| `superadmin` | All clinics, no Tenant scope, `admin/superadmin/` panel |
| `admin` | Full clinic access |
| `medico` | Patients, odontogram, notes |
| `recepcion` | Appointments, payments, history |

## Partials (`admin/partials/`)

- `flash.php` — flash message display
- `confirm_modal.php` — generic delete confirm modal
- `breadcrumb.php` — page breadcrumb
- `skip_nav.php` — accessibility skip nav
- `403.php` — forbidden page

## Roadmap

**Completed:** Multi-tenancy (Fase 2), specialty generalization, module system, SaaS plans, public onboarding, audit log (Fase 3).

**Next:** Billing integration, email notifications per clinic, advanced reporting per specialty.
