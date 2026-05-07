-- ============================================================
-- Fase 3 Migration: SaaS Specialties, Modules, Plans, Notes, Audit
-- Run once after migration_fase2.sql.
-- ============================================================

-- 1. Specialties catalog
CREATE TABLE IF NOT EXISTS specialties (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    slug        VARCHAR(60)  NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO specialties (name, slug, description) VALUES
    ('Odontología',          'dental',           'Clínica dental con odontograma interactivo'),
    ('Medicina General',     'general_medicine', 'Consultas de medicina general'),
    ('Pediatría',            'pediatrics',       'Atención pediátrica')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 2. Feature modules catalog
CREATE TABLE IF NOT EXISTS modules (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    slug        VARCHAR(60)  NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO modules (name, slug, description) VALUES
    ('Odontograma',          'odontogram',       'Mapa interactivo de piezas dentales'),
    ('Notas Clínicas',       'clinical_notes',   'Historia clínica de texto libre'),
    ('Pagos',                'payments',          'Gestión de pagos y facturación'),
    ('Historial',            'history',           'Historial de tratamientos')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 3. Per-clinic module flags
CREATE TABLE IF NOT EXISTS clinic_modules (
    clinic_id  INT UNSIGNED NOT NULL,
    module_id  INT UNSIGNED NOT NULL,
    enabled    TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (clinic_id, module_id),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id),
    FOREIGN KEY (module_id) REFERENCES modules(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Plans / subscription tiers
CREATE TABLE IF NOT EXISTS plans (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(60)    NOT NULL UNIQUE,
    max_patients   INT            NOT NULL DEFAULT 0  COMMENT '0 = unlimited',
    max_users      INT            NOT NULL DEFAULT 0  COMMENT '0 = unlimited',
    price_monthly  DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    created_at     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO plans (name, max_patients, max_users, price_monthly) VALUES
    ('free',  50,  2,   0.00),
    ('basic', 500, 10,  29.99),
    ('pro',   0,   0,   79.99)
ON DUPLICATE KEY UPDATE max_patients = VALUES(max_patients),
                        max_users    = VALUES(max_users),
                        price_monthly = VALUES(price_monthly);

-- 5. Extend clinics table with specialty, plan FK, SMTP, and plan expiry
ALTER TABLE clinics
    ADD COLUMN IF NOT EXISTS specialty_id     INT UNSIGNED DEFAULT NULL AFTER active,
    ADD COLUMN IF NOT EXISTS plan_id          INT UNSIGNED DEFAULT NULL AFTER plan,
    ADD COLUMN IF NOT EXISTS plan_expires_at  DATE         DEFAULT NULL AFTER plan_id,
    ADD COLUMN IF NOT EXISTS smtp_host        VARCHAR(120) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS smtp_port        SMALLINT     DEFAULT 587,
    ADD COLUMN IF NOT EXISTS smtp_username    VARCHAR(120) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS smtp_password    VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS smtp_from        VARCHAR(120) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS smtp_from_name   VARCHAR(120) DEFAULT NULL;

-- Link Anguizola to dental specialty and pro plan
UPDATE clinics c
JOIN specialties s ON s.slug = 'dental'
JOIN plans p       ON p.name = 'pro'
SET c.specialty_id = s.id,
    c.plan_id      = p.id
WHERE c.subdomain = 'anguizola'
  AND c.specialty_id IS NULL;

-- 6. Enable default modules for Anguizola (dental clinic)
--    Dental gets: odontogram + clinical_notes + payments + history
INSERT IGNORE INTO clinic_modules (clinic_id, module_id, enabled)
SELECT 1, m.id, 1
FROM modules m
WHERE m.slug IN ('odontogram', 'clinical_notes', 'payments', 'history');

-- 7. Generic clinical notes (for non-dental specialties)
CREATE TABLE IF NOT EXISTS clinic_notes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id   INT UNSIGNED NOT NULL,
    cedula      VARCHAR(30)  NOT NULL,
    fecha       DATE         NOT NULL,
    contenido   TEXT         NOT NULL,
    created_by  INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'users.id',
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notes_clinic   (clinic_id),
    INDEX idx_notes_cedula   (cedula),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Audit log
CREATE TABLE IF NOT EXISTS audit_log (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL DEFAULT 0,
    action     VARCHAR(60)  NOT NULL COMMENT 'e.g. login, insert_patient, delete_cita',
    entity     VARCHAR(60)  NOT NULL DEFAULT '' COMMENT 'table or resource name',
    entity_id  VARCHAR(60)  NOT NULL DEFAULT '' COMMENT 'primary key of affected row',
    ip         VARCHAR(45)  NOT NULL DEFAULT '',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_clinic (clinic_id),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Add imagen_path to consulta for disk-based image storage
--    New inserts write here; old BLOB rows keep their data (graceful migration).
ALTER TABLE consulta
    ADD COLUMN IF NOT EXISTS imagen_path VARCHAR(255) DEFAULT NULL AFTER imageData;
