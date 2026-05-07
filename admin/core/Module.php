<?php
/**
 * Module.php — per-clinic feature flag system.
 *
 * Usage:
 *   Module::enabled('odontogram')   // bool — is module on for current clinic?
 *   Module::require('odontogram')   // redirect to inicio.php if not enabled
 *
 * Results are cached per request so repeated calls are free.
 * Requires Tenant::load() and Database::get() to be available.
 */
class Module
{
    /** @var array<string, bool>  "clinicId:slug" => bool */
    private static array $cache = [];

    /**
     * Returns true if the given module slug is enabled for the current clinic.
     * Defaults to false if the clinic has no row in clinic_modules for that slug.
     */
    public static function enabled(string $slug): bool
    {
        $cid = Tenant::id();
        $key = "{$cid}:{$slug}";

        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $db   = Database::get();
        $stmt = $db->prepare(
            'SELECT cm.enabled
               FROM clinic_modules cm
               JOIN modules m ON m.id = cm.module_id
              WHERE cm.clinic_id = ? AND m.slug = ?
              LIMIT 1'
        );
        $stmt->bind_param('is', $cid, $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        $stmt->close();

        self::$cache[$key] = $row ? (bool)$row['enabled'] : false;
        return self::$cache[$key];
    }

    /**
     * Redirects to $redirect (default: inicio.php) if the module is NOT enabled.
     * Call right after Auth::require() in protected pages.
     */
    public static function require(string $slug, string $redirect = 'inicio.php'): void
    {
        if (!self::enabled($slug)) {
            header("Location: {$redirect}");
            exit;
        }
    }

    /** Flush the in-request cache (useful in tests or after toggling modules). */
    public static function flush(): void
    {
        self::$cache = [];
    }
}
