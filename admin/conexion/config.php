<?php
require_once __DIR__ . '/../core/env.php';
require_once __DIR__ . '/../core/Database.php';
loadEnv(__DIR__ . '/../../.env');
$db = Database::get();
