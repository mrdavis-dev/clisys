# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Clínica Anguizola** is a PHP/MySQL dental clinic management system. It handles patient records, appointment scheduling, dental procedure tracking (odontograms), payment processing, and staff administration. Being modernized into a multi-specialty SaaS (see Roadmap below).

## Setup

```bash
# 1. Copy env template and fill in credentials
cp .env.example .env

# 2. Install PHP dependencies (PHPMailer, DOMPDF)
composer install -d admin/

# 3. Install Node dependencies (dom-to-image, Font Awesome)
npm install
```

There is no build step, test suite, linter, or CI pipeline. Deploy directly to a PHP-enabled web server.

## Security layer (`admin/core/`)

Four files that bootstrap every request:

- `admin/core/env.php` — `loadEnv(path)` reads `.env` into `$_ENV`
- `admin/core/Database.php` — `Database::get()` singleton MySQLi; called automatically via `admin/conexion/config.php` which all pages include
- `admin/core/Csrf.php` — `Csrf::generate()`, `Csrf::verify()`, `Csrf::field()` for CSRF protection on all forms
- `admin/core/Auth.php` — `Auth::require()` replaces the old copy-pasted session guard; also defines global `h(string): string` for XSS escaping

**Rules:**
- Every protected page starts with `Auth::require()` (handles `session_start()`, 30-min inactivity, redirect)
- Every POST mutation handler calls `Csrf::verify()` first
- Every form includes `<?= Csrf::field() ?>`
- All queries use `$db->prepare()` + `bind_param()` — never string concatenation
- All echoes of DB/user data use `h()` — never bare `echo $row[...]`

## Architecture

**Two entry points:**
- `/index.php` — Public appointment booking form; submits to `insert_exterior.php` → `citas_tabla` table
- `/admin/index.php` — Staff login

**Admin module files** (`/admin/*.php`): each page calls `Auth::require()`, then includes `menu.php` (shared nav), then renders HTML inline. No framework, ORM, or MVC separation.

**Key admin pages and their DB tables:**
| File | Purpose | Table(s) |
|---|---|---|
| `inicio.php` | Dashboard / appointments | `citas_tabla` |
| `pacientes.php` | Patient CRUD | `pacientes` |
| `odontograma.php` | Interactive tooth chart | `consulta` |
| `getinfo.php` / `pagos.php` | Payment entry | `pago` |
| `historial.php` | Treatment history | `pago` |
| `registro_user.php` | Staff account management | `users` |

**AJAX search endpoints** (return HTML fragments, called via jQuery):
- `fetch.php` — patient search for `pacientes.php`
- `consulta_odo.php` — patient + odontogram search
- `viewhistorial.php` — payment history search
- `get_info_pago.php` — patient lookup for payment form
- `functions/funsaldo.php` — balance lookup for payment form

**Email** (`admin/insert_pagos_send.php`): PHPMailer via SMTP, credentials from `.env`.

**PDF generation**: DOMPDF (`admin/insert_pagos_send.php`), used for invoices.

**Odontogram** (`admin/odontograma.php`): Uses AngularJS 1.x for the interactive tooth chart, `dom-to-image` npm package for image export, `admin/js/jquery-odontograma.js` for tooth state. Records stored as BLOBs in `consulta` table.

**Database schema** (5 tables, no foreign keys yet):
- `pacientes` — 21 columns including 10 yes/no medical history fields; note column name typos: `ocuapacion`, `motivo_consuta`
- `citas_tabla` — appointments (no FK to pacientes, only stores patient name)
- `pago` — payments; links to patient via `cedula` string
- `consulta` — odontogram records with BLOB image data; links via `cedula`
- `users` — staff accounts (bcrypt, cost 12)

## Query pattern

```php
$stmt = $db->prepare('SELECT * FROM pacientes WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result(); // or store_result() for num_rows
```

## Roadmap

**Fase 2 (next):** Multi-tenancy — add `clinics` table, `clinic_id` column to all 5 tables, subdomain routing, staff table replacing hardcoded doctor names, fix column typos in migration.

**Fase 3:** Specialty generalization — make odontogram a conditional module per `clinic.specialty`, generic clinical notes for non-dental, SaaS billing tiers, public onboarding.
