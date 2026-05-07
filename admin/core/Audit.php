<?php
/**
 * Audit.php — lightweight audit trail for critical actions.
 *
 * Usage:
 *   Audit::log('login',          'users',     (string)$userId);
 *   Audit::log('insert_patient', 'pacientes', (string)$newId);
 *   Audit::log('delete_cita',    'citas_tabla', (string)$id);
 *
 * Writes to the `audit_log` table. Failures are silently logged to
 * the PHP error log so that a broken audit does not break the request.
 *
 * Requires Tenant::load() and Database::get() to be available.
 * Session must be active (reads $_SESSION['id'] for the user FK).
 */
class Audit
{
    /**
     * Insert one audit record.
     *
     * @param string $action   Short verb+noun: 'login', 'insert_patient', 'delete_cita' …
     * @param string $entity   Table or resource name: 'pacientes', 'citas_tabla', …
     * @param string $entityId PK of the affected row (empty string if N/A)
     */
    public static function log(
        string $action,
        string $entity   = '',
        string $entityId = ''
    ): void {
        try {
            $db       = Database::get();
            $clinicId = Tenant::id();
            $userId   = (int)($_SESSION['id'] ?? 0);
            $ip       = self::resolveIp();

            $stmt = $db->prepare(
                'INSERT INTO audit_log (clinic_id, user_id, action, entity, entity_id, ip)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('iissss', $clinicId, $userId, $action, $entity, $entityId, $ip);
            $stmt->execute();
            $stmt->close();
        } catch (\Throwable $e) {
            error_log('Audit::log failed: ' . $e->getMessage());
        }
    }

    // ------------------------------------------------------------------ //
    //  Internal                                                            //
    // ------------------------------------------------------------------ //

    private static function resolveIp(): string
    {
        // Respect reverse-proxy headers (single-hop only)
        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                // X-Forwarded-For can be a CSV list; take the leftmost (client) IP
                $ip = explode(',', $_SERVER[$key])[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '';
    }
}
