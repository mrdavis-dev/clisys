<?php
/**
 * Tenant.php — resolves the current clinic from the HTTP subdomain.
 *
 * Usage:
 *   Tenant::load($db);          // call once after DB is available
 *   $id = Tenant::id();         // INT clinic_id for WHERE clauses
 *   Tenant::requireSession();   // throws if no clinic resolved
 *
 * Resolution order:
 *   1. $_SESSION['clinic_id']  (already set after login)
 *   2. Subdomain of HTTP_HOST   (e.g. "anguizola" in anguizola.clisys.com)
 *   3. Fallback to clinic id=1  (single-clinic / local-dev mode)
 */
class Tenant
{
    private static ?int $clinicId = null;

    /**
     * Resolve and cache the clinic_id for this request.
     * Must be called after session_start() and after DB is available.
     */
    public static function load(mysqli $db): void
    {
        // 1. Already in session (set by login)
        if (isset($_SESSION['clinic_id'])) {
            self::$clinicId = (int)$_SESSION['clinic_id'];
            return;
        }

        // 2. Try to resolve from subdomain
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $parts = explode('.', $host);
        if (count($parts) >= 3) {
            $sub = $parts[0];
            $stmt = $db->prepare('SELECT id FROM clinics WHERE subdomain = ? AND active = 1');
            $stmt->bind_param('s', $sub);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                self::$clinicId = (int)$row['id'];
                $stmt->close();
                return;
            }
            $stmt->close();
        }

        // 3. Fallback: clinic id = 1 (local dev / single-tenant mode)
        self::$clinicId = 1;
    }

    /** Returns the resolved clinic_id (int). Throws if Tenant::load() was not called. */
    public static function id(): int
    {
        if (self::$clinicId === null) {
            throw new \RuntimeException('Tenant::load() must be called before Tenant::id()');
        }
        return self::$clinicId;
    }

    /** Returns true if a clinic has been resolved. */
    public static function resolved(): bool
    {
        return self::$clinicId !== null;
    }
}
