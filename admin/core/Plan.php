<?php
/**
 * Plan.php — subscription plan enforcement.
 *
 * Usage:
 *   Plan::withinLimit('patients')  // bool — can we add more patients?
 *   Plan::withinLimit('users')     // bool — can we add more staff users?
 *   Plan::active()                 // bool — is the plan subscription active (not expired)?
 *
 * Limits of 0 mean "unlimited" (used by the 'pro' plan).
 * Requires Tenant::load() and Database::get() to be available.
 */
class Plan
{
    /** @var array<string,mixed>|null  Cached plan row for this request */
    private static ?array $current = null;

    // ------------------------------------------------------------------ //
    //  Public API                                                          //
    // ------------------------------------------------------------------ //

    /**
     * Returns true if the clinic is within the plan limit for $type.
     * Supported types: 'patients', 'users'.
     */
    public static function withinLimit(string $type): bool
    {
        $plan = self::load();
        $db   = Database::get();
        $cid  = Tenant::id();

        switch ($type) {
            case 'patients':
                $max = (int)($plan['max_patients'] ?? 0);
                if ($max <= 0) return true; // unlimited
                $stmt = $db->prepare('SELECT COUNT(*) FROM pacientes WHERE clinic_id = ?');
                $stmt->bind_param('i', $cid);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();
                return (int)$count < $max;

            case 'users':
                $max = (int)($plan['max_users'] ?? 0);
                if ($max <= 0) return true;
                $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE clinic_id = ?');
                $stmt->bind_param('i', $cid);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();
                return (int)$count < $max;
        }

        return true; // unknown type → allow
    }

    /**
     * Returns true if the clinic's subscription has not expired.
     * If plan_expires_at is NULL the subscription is treated as perpetual.
     */
    public static function active(): bool
    {
        $plan = self::load();
        if (empty($plan['plan_expires_at'])) {
            return true;
        }
        return strtotime((string)$plan['plan_expires_at']) >= time();
    }

    /**
     * Returns a human-readable label for the current plan (e.g. "Pro").
     */
    public static function name(): string
    {
        $plan = self::load();
        return ucfirst((string)($plan['plan_name'] ?? 'free'));
    }

    /** Flush cached plan data (useful after plan upgrades in same request). */
    public static function flush(): void
    {
        self::$current = null;
    }

    // ------------------------------------------------------------------ //
    //  Internal                                                            //
    // ------------------------------------------------------------------ //

    private static function load(): array
    {
        if (self::$current !== null) {
            return self::$current;
        }

        $db   = Database::get();
        $cid  = Tenant::id();
        $stmt = $db->prepare(
            'SELECT p.name AS plan_name,
                    p.max_patients,
                    p.max_users,
                    c.plan_expires_at
               FROM clinics c
               JOIN plans   p ON p.id = c.plan_id
              WHERE c.id = ?
              LIMIT 1'
        );
        $stmt->bind_param('i', $cid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        $stmt->close();

        // Fallback defaults if clinic has no plan_id yet (pre-migration rows)
        self::$current = $row ?? [
            'plan_name'       => 'basic',
            'max_patients'    => 500,
            'max_users'       => 10,
            'plan_expires_at' => null,
        ];

        return self::$current;
    }
}
