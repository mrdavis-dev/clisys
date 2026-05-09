-- ============================================================
-- ClíSys — Migration 02: super-admin role
-- ============================================================

ALTER TABLE users
    ADD COLUMN role ENUM('admin','medico','recepcion','superadmin') NOT NULL DEFAULT 'admin' AFTER name;

INSERT INTO users (clinic_id, username, password, name, role) VALUES
(1, 'superadmin',
 '$2y$12$JQptcuhHZudW827IHaiGuePcbbFm5/3TQyTcrPJxG.NaD0KB2fy.K',
 'Super Administrador',
 'superadmin');
