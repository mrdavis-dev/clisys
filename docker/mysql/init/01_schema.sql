-- ============================================================
-- ClíSys — Schema completo (instalación limpia)
-- Incluye todo desde Fase 1 + 2 + 3.
-- Para bases de datos existentes usar migration_fase2.sql
-- y migration_fase3.sql en vez de este archivo.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. Catálogo de especialidades
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS specialties (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    slug        VARCHAR(60)  NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO specialties (name, slug, description) VALUES
    ('Odontología',      'dental',           'Clínica dental con odontograma interactivo'),
    ('Medicina General', 'general_medicine', 'Consultas de medicina general'),
    ('Pediatría',        'pediatrics',       'Atención pediátrica');

-- ------------------------------------------------------------
-- 2. Catálogo de módulos funcionales
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS modules (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    slug        VARCHAR(60)  NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO modules (name, slug, description) VALUES
    ('Odontograma',    'odontogram',     'Mapa interactivo de piezas dentales'),
    ('Notas Clínicas', 'clinical_notes', 'Historia clínica de texto libre'),
    ('Pagos',          'payments',       'Gestión de pagos y facturación'),
    ('Historial',      'history',        'Historial de tratamientos');

-- ------------------------------------------------------------
-- 3. Planes de suscripción
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS plans (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(60)   NOT NULL UNIQUE,
    max_patients   INT           NOT NULL DEFAULT 0  COMMENT '0 = ilimitado',
    max_users      INT           NOT NULL DEFAULT 0  COMMENT '0 = ilimitado',
    price_monthly  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO plans (name, max_patients, max_users, price_monthly) VALUES
    ('free',  50,  2,  0.00),
    ('basic', 500, 10, 29.99),
    ('pro',   0,   0,  79.99);

-- ------------------------------------------------------------
-- 4. Clínicas (tenants)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clinics (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(120) NOT NULL,
    subdomain        VARCHAR(60)  NOT NULL UNIQUE,
    active           TINYINT(1)   NOT NULL DEFAULT 1,
    plan             ENUM('free','basic','pro') NOT NULL DEFAULT 'basic',
    specialty_id     INT UNSIGNED DEFAULT NULL,
    plan_id          INT UNSIGNED DEFAULT NULL,
    plan_expires_at  DATE         DEFAULT NULL,
    smtp_host        VARCHAR(120) DEFAULT NULL,
    smtp_port        SMALLINT     DEFAULT 587,
    smtp_username    VARCHAR(120) DEFAULT NULL,
    smtp_password    VARCHAR(255) DEFAULT NULL,
    smtp_from        VARCHAR(120) DEFAULT NULL,
    smtp_from_name   VARCHAR(120) DEFAULT NULL,
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id),
    FOREIGN KEY (plan_id)      REFERENCES plans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clínica demo — credenciales admin/admin123 (cambiar en producción)
INSERT INTO clinics (name, subdomain, plan, specialty_id, plan_id)
SELECT 'Clínica Anguizola', 'anguizola', 'pro',
       (SELECT id FROM specialties WHERE slug = 'dental'),
       (SELECT id FROM plans       WHERE name = 'pro');

-- ------------------------------------------------------------
-- 5. Feature flags por clínica
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clinic_modules (
    clinic_id  INT UNSIGNED NOT NULL,
    module_id  INT UNSIGNED NOT NULL,
    enabled    TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (clinic_id, module_id),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id),
    FOREIGN KEY (module_id) REFERENCES modules(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Habilitar todos los módulos para la clínica demo
INSERT INTO clinic_modules (clinic_id, module_id, enabled)
SELECT 1, id, 1 FROM modules;

-- ------------------------------------------------------------
-- 6. Staff (reemplaza doctores hardcodeados)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS staff (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id INT UNSIGNED NOT NULL,
    name      VARCHAR(120) NOT NULL,
    role      VARCHAR(60)  NOT NULL DEFAULT 'doctor',
    active    TINYINT(1)   NOT NULL DEFAULT 1,
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO staff (clinic_id, name, role) VALUES
    (1, 'Dr. Júlio Anguizola Vial',       'doctor'),
    (1, 'Dr. Miguel Anguizola Severino',   'doctor'),
    (1, 'Dr. Amira Martínez de Anguizola', 'doctor');

-- ------------------------------------------------------------
-- 7. Pacientes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pacientes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id   INT UNSIGNED NOT NULL DEFAULT 1,
    nombre      VARCHAR(120) NOT NULL,
    apellido    VARCHAR(120) NOT NULL,
    cedula      VARCHAR(30)  NOT NULL,
    direccion   VARCHAR(255) DEFAULT NULL,
    telefono    VARCHAR(30)  DEFAULT NULL,
    email       VARCHAR(120) DEFAULT NULL,
    ocupacion   VARCHAR(120) DEFAULT NULL,
    edad        VARCHAR(10)  DEFAULT NULL,
    motivo_consulta                             TEXT DEFAULT NULL,
    habitos_higienicos                          TEXT DEFAULT NULL,
    esta_bajo_tratamiento_actualmente           VARCHAR(5) DEFAULT NULL,
    Ha_sido_hospitalizado_quirurgicamente       VARCHAR(5) DEFAULT NULL,
    esta_tomando_algun_medicamento_o_droga      VARCHAR(5) DEFAULT NULL,
    presenta_algun_tipo_de_alergia              VARCHAR(5) DEFAULT NULL,
    Ha_tenido_algun_tipo_de_enfermedad_cardiaca VARCHAR(5) DEFAULT NULL,
    Es_usted_diabetico_                         VARCHAR(5) DEFAULT NULL,
    Ha_tenido_tuberculosis_o_hepatitis          VARCHAR(5) DEFAULT NULL,
    Ha_presentado_alteraciones_en_el_sangrado   VARCHAR(5) DEFAULT NULL,
    Ha_tenido_alguna_enfermedad_de_transmision_sexual VARCHAR(5) DEFAULT NULL,
    Tiene_algun_tipo_de_mal_habito              VARCHAR(5) DEFAULT NULL,
    INDEX idx_pacientes_clinic (clinic_id),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 8. Citas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS citas_tabla (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id       INT UNSIGNED NOT NULL DEFAULT 1,
    fecha_de_cita   DATE         NOT NULL,
    hora_de_cita    TIME         NOT NULL,
    nombre_paciente VARCHAR(255) NOT NULL,
    asunto_de_la_cita VARCHAR(255) DEFAULT NULL,
    doctor          VARCHAR(120) DEFAULT NULL,
    INDEX idx_citas_clinic (clinic_id),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 9. Pagos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pago (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id    INT UNSIGNED  NOT NULL DEFAULT 1,
    fecha        DATE          NOT NULL,
    nombre       VARCHAR(255)  NOT NULL,
    cedula       VARCHAR(30)   NOT NULL,
    monto        DECIMAL(10,2) NOT NULL DEFAULT 0,
    tipo_de_pago VARCHAR(60)   DEFAULT NULL,
    saldo        DECIMAL(10,2) NOT NULL DEFAULT 0,
    tratamiento  VARCHAR(255)  DEFAULT NULL,
    nota         TEXT          DEFAULT NULL,
    INDEX idx_pago_clinic (clinic_id),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 10. Odontograma / consultas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS consulta (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id   INT UNSIGNED NOT NULL DEFAULT 1,
    cedula      VARCHAR(30)  NOT NULL,
    tratamiento TEXT         DEFAULT NULL,
    imageType   VARCHAR(50)  DEFAULT NULL,
    imageData   LONGBLOB     DEFAULT NULL  COMMENT 'Legacy — usar imagen_path para registros nuevos',
    imagen_path VARCHAR(255) DEFAULT NULL  COMMENT 'Ruta relativa a admin/uploads/',
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_consulta_clinic (clinic_id),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 11. Usuarios del staff
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id INT UNSIGNED NOT NULL DEFAULT 1,
    username  VARCHAR(60)  NOT NULL,
    password  VARCHAR(255) NOT NULL,
    name      VARCHAR(120) NOT NULL,
    UNIQUE KEY uq_users_clinic_username (clinic_id, username),
    INDEX idx_users_clinic (clinic_id),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario admin demo: admin / admin123 (bcrypt cost 12) — CAMBIAR EN PRODUCCIÓN
INSERT INTO users (clinic_id, username, password, name)
VALUES (1, 'admin',
        '$2y$12$3HJayANV95vDyaTREGn5N.Fz8yhJAZPMviMaRD.86q.28CKrW5WMW',
        'Administrador');

-- ------------------------------------------------------------
-- 12. Notas clínicas (texto libre, para especialidades no-dentales)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clinic_notes (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id  INT UNSIGNED NOT NULL,
    cedula     VARCHAR(30)  NOT NULL,
    fecha      DATE         NOT NULL,
    contenido  TEXT         NOT NULL,
    created_by INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'users.id',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notes_clinic (clinic_id),
    INDEX idx_notes_cedula (cedula),
    FOREIGN KEY (clinic_id) REFERENCES clinics(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 13. Audit log
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_log (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL DEFAULT 0,
    action     VARCHAR(60)  NOT NULL COMMENT 'login, insert_patient, delete_cita…',
    entity     VARCHAR(60)  NOT NULL DEFAULT '',
    entity_id  VARCHAR(60)  NOT NULL DEFAULT '',
    ip         VARCHAR(45)  NOT NULL DEFAULT '',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_clinic  (clinic_id),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
