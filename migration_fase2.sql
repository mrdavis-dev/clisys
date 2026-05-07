-- ============================================================
-- Fase 2 Migration: Multi-tenancy
-- Run once against the production database.
-- ============================================================

-- 1. Clinics (one row per tenant)
CREATE TABLE IF NOT EXISTS clinics (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    subdomain  VARCHAR(60)  NOT NULL UNIQUE,
    active     TINYINT(1)   NOT NULL DEFAULT 1,
    plan       ENUM('free','basic','pro') NOT NULL DEFAULT 'basic',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Seed one default clinic so existing data keeps working
INSERT INTO clinics (name, subdomain, plan)
SELECT 'Clínica Anguizola', 'anguizola', 'pro'
WHERE NOT EXISTS (SELECT 1 FROM clinics WHERE subdomain = 'anguizola');

-- 3. Staff (replaces hardcoded doctor dropdown)
CREATE TABLE IF NOT EXISTS staff (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id INT UNSIGNED NOT NULL,
    name      VARCHAR(120) NOT NULL,
    role      VARCHAR(60)  NOT NULL DEFAULT 'doctor',
    active    TINYINT(1)   NOT NULL DEFAULT 1,
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed existing hardcoded doctors into clinic 1
INSERT INTO staff (clinic_id, name, role)
SELECT 1, 'Dr. Júlio Anguizola Vial', 'doctor'
WHERE NOT EXISTS (SELECT 1 FROM staff WHERE name = 'Dr. Júlio Anguizola Vial' AND clinic_id = 1);

INSERT INTO staff (clinic_id, name, role)
SELECT 1, 'Dr. Miguel Anguizola Severino', 'doctor'
WHERE NOT EXISTS (SELECT 1 FROM staff WHERE name = 'Dr. Miguel Anguizola Severino' AND clinic_id = 1);

INSERT INTO staff (clinic_id, name, role)
SELECT 1, 'Dr. Amira Martínez de Anguizola', 'doctor'
WHERE NOT EXISTS (SELECT 1 FROM staff WHERE name = 'Dr. Amira Martínez de Anguizola' AND clinic_id = 1);

-- 4. Add clinic_id to all five tables
--    We use IF NOT EXISTS guards so the migration is idempotent.

-- pacientes
ALTER TABLE pacientes
    ADD COLUMN IF NOT EXISTS clinic_id INT UNSIGNED NOT NULL DEFAULT 1 AFTER id,
    ADD INDEX IF NOT EXISTS idx_pacientes_clinic (clinic_id);

-- citas_tabla
ALTER TABLE citas_tabla
    ADD COLUMN IF NOT EXISTS clinic_id INT UNSIGNED NOT NULL DEFAULT 1 AFTER id,
    ADD INDEX IF NOT EXISTS idx_citas_clinic (clinic_id);

-- pago
ALTER TABLE pago
    ADD COLUMN IF NOT EXISTS clinic_id INT UNSIGNED NOT NULL DEFAULT 1 AFTER id,
    ADD INDEX IF NOT EXISTS idx_pago_clinic (clinic_id);

-- consulta (odontogram)
ALTER TABLE consulta
    ADD COLUMN IF NOT EXISTS clinic_id INT UNSIGNED NOT NULL DEFAULT 1 AFTER id,
    ADD INDEX IF NOT EXISTS idx_consulta_clinic (clinic_id);

-- users
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS clinic_id INT UNSIGNED NOT NULL DEFAULT 1 AFTER id,
    ADD INDEX IF NOT EXISTS idx_users_clinic (clinic_id);

-- 5. Fix column name typos (rename, idempotent via stored procedure trick)
--    We check information_schema to avoid error if already renamed.

DROP PROCEDURE IF EXISTS fix_column_typos;
DELIMITER //
CREATE PROCEDURE fix_column_typos()
BEGIN
    -- ocuapacion -> ocupacion
    IF EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME   = 'pacientes'
          AND COLUMN_NAME  = 'ocuapacion'
    ) THEN
        ALTER TABLE pacientes CHANGE COLUMN ocuapacion ocupacion VARCHAR(100);
    END IF;

    -- motivo_consuta -> motivo_consulta
    IF EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME   = 'pacientes'
          AND COLUMN_NAME  = 'motivo_consuta'
    ) THEN
        ALTER TABLE pacientes CHANGE COLUMN motivo_consuta motivo_consulta TEXT;
    END IF;
END //
DELIMITER ;

CALL fix_column_typos();
DROP PROCEDURE IF EXISTS fix_column_typos;
