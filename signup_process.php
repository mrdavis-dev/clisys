<?php
/**
 * signup_process.php — Public clinic onboarding handler.
 *
 * 1. Validates input
 * 2. Creates a row in `clinics` linked to the chosen specialty + plan
 * 3. Enables default modules for the chosen specialty
 * 4. Creates the first admin user for that clinic
 * 5. Redirects to the admin login page
 */
session_start();
require_once __DIR__ . '/admin/core/env.php';
require_once __DIR__ . '/admin/core/Csrf.php';
require_once __DIR__ . '/admin/core/Database.php';
require_once __DIR__ . '/admin/core/Tenant.php';
loadEnv(__DIR__ . '/.env');
Csrf::verify();

$db = Database::get();

// ── Collect & sanitize input ────────────────────────────────────────────────
$clinicName    = trim($_POST['clinic_name']     ?? '');
$subdomain     = strtolower(trim($_POST['subdomain']       ?? ''));
$specialtyId   = (int)($_POST['specialty_id']   ?? 0);
$plan          = $_POST['plan']                 ?? 'free';
$adminName     = trim($_POST['admin_name']      ?? '');
$adminUsername = trim($_POST['admin_username']  ?? '');
$adminPassword = $_POST['admin_password']       ?? '';
$adminPassword2= $_POST['admin_password2']      ?? '';

// ── Validate ────────────────────────────────────────────────────────────────
if (!$clinicName || !$subdomain || !$specialtyId || !$adminName
    || !$adminUsername || !$adminPassword) {
    header('Location: signup.php?error=empty');
    exit;
}

if (!preg_match('/^[a-z0-9-]{3,30}$/', $subdomain)) {
    header('Location: signup.php?error=subdomain_fmt');
    exit;
}

if ($adminPassword !== $adminPassword2) {
    header('Location: signup.php?error=password');
    exit;
}

if (strlen($adminPassword) < 8) {
    header('Location: signup.php?error=short_pwd');
    exit;
}

$allowedPlans = ['free', 'basic', 'pro'];
if (!in_array($plan, $allowedPlans, true)) {
    $plan = 'free';
}

// ── Check subdomain uniqueness ───────────────────────────────────────────────
$chk = $db->prepare('SELECT id FROM clinics WHERE subdomain = ?');
$chk->bind_param('s', $subdomain);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    $chk->close();
    header('Location: signup.php?error=subdomain');
    exit;
}
$chk->close();

// ── Resolve plan_id ──────────────────────────────────────────────────────────
$planRow = $db->prepare('SELECT id FROM plans WHERE name = ? LIMIT 1');
$planRow->bind_param('s', $plan);
$planRow->execute();
$planRow->bind_result($planId);
$planRow->fetch();
$planRow->close();
if (!$planId) { $planId = null; } // plan table might not exist yet

// ── Create clinic ────────────────────────────────────────────────────────────
$stmtClinic = $db->prepare(
    'INSERT INTO clinics (name, subdomain, active, plan, specialty_id, plan_id)
     VALUES (?, ?, 1, ?, ?, ?)'
);
$stmtClinic->bind_param('sssii', $clinicName, $subdomain, $plan, $specialtyId, $planId);
$stmtClinic->execute();
$clinicId = (int)$db->insert_id;
$stmtClinic->close();

// ── Enable modules based on specialty ───────────────────────────────────────
// Always enable: payments, history, clinical_notes
// Only dental gets: odontogram
$specialtySlugRow = $db->prepare('SELECT slug FROM specialties WHERE id = ? LIMIT 1');
$specialtySlugRow->bind_param('i', $specialtyId);
$specialtySlugRow->execute();
$specialtySlugRow->bind_result($specSlug);
$specialtySlugRow->fetch();
$specialtySlugRow->close();

$baseModules = ['payments', 'history', 'clinical_notes'];
if ($specSlug === 'dental') {
    $baseModules[] = 'odontogram';
}

$stmtModCheck = $db->prepare('SELECT id FROM modules WHERE slug = ? LIMIT 1');
$stmtModIns   = $db->prepare('INSERT IGNORE INTO clinic_modules (clinic_id, module_id, enabled) VALUES (?, ?, 1)');

foreach ($baseModules as $slug) {
    $stmtModCheck->bind_param('s', $slug);
    $stmtModCheck->execute();
    $stmtModCheck->bind_result($moduleId);
    $stmtModCheck->fetch();
    $stmtModCheck->free_result();

    if ($moduleId) {
        $stmtModIns->bind_param('ii', $clinicId, $moduleId);
        $stmtModIns->execute();
    }
    $moduleId = null;
}
$stmtModCheck->close();
$stmtModIns->close();

// ── Create admin user ────────────────────────────────────────────────────────
$hash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);
$stmtUser = $db->prepare(
    'INSERT INTO users (clinic_id, username, password, name) VALUES (?, ?, ?, ?)'
);
$stmtUser->bind_param('isss', $clinicId, $adminUsername, $hash, $adminName);
$stmtUser->execute();
$stmtUser->close();

// ── Done — redirect to admin login ───────────────────────────────────────────
header('Location: admin/index.php?registered=1');
exit;
