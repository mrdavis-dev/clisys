<?php

function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) continue;
        [$k, $v] = array_map('trim', $parts);
        if ($k !== '' && !array_key_exists($k, $_ENV)) {
            $_ENV[$k] = $v;
            putenv("$k=$v");
        }
    }
}
