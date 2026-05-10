-- ============================================================
-- ClíSys — Migration 03: superadmin desacoplado de clínica
-- Hace clinic_id nullable en users; el superadmin queda sin clínica.
-- ============================================================

-- 1. Quitar FK y unique key actuales
ALTER TABLE users
    DROP FOREIGN KEY users_ibfk_1,
    DROP INDEX uq_users_clinic_username;

-- 2. Cambiar clinic_id a nullable sin default
ALTER TABLE users
    MODIFY COLUMN clinic_id INT UNSIGNED NULL DEFAULT NULL;

-- 3. Restaurar unique por (clinic_id, username) — NULL en clinic_id es OK en MySQL unique
ALTER TABLE users
    ADD UNIQUE KEY uq_users_clinic_username (clinic_id, username);

-- 4. Restaurar FK (ahora permite NULL)
ALTER TABLE users
    ADD CONSTRAINT fk_users_clinic
    FOREIGN KEY (clinic_id) REFERENCES clinics(id);

-- 5. Mover superadmin fuera de clinic_id=1
UPDATE users SET clinic_id = NULL WHERE role = 'superadmin';
