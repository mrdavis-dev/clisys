-- ============================================================
-- ClíSys — Schema completo Postgres (instalación limpia)
-- Incluye Fase 1 + 2 (role) + 3 (superadmin sin clínica).
-- Reemplaza docker/mysql/init/*.sql para el stack Postgres.
-- ============================================================

-- ------------------------------------------------------------
-- 1. Catálogo de especialidades
-- ------------------------------------------------------------
CREATE TABLE specialties (
    id          INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    slug        VARCHAR(60)  NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO specialties (name, slug, description) VALUES
    ('Odontología',      'dental',           'Clínica dental con odontograma interactivo'),
    ('Medicina General', 'general_medicine', 'Consultas de medicina general'),
    ('Pediatría',        'pediatrics',       'Atención pediátrica');

-- ------------------------------------------------------------
-- 2. Catálogo de módulos funcionales
-- ------------------------------------------------------------
CREATE TABLE modules (
    id          INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    slug        VARCHAR(60)  NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO modules (name, slug, description) VALUES
    ('Odontograma',    'odontogram',     'Mapa interactivo de piezas dentales'),
    ('Notas Clínicas', 'clinical_notes', 'Historia clínica de texto libre'),
    ('Pagos',          'payments',       'Gestión de pagos y facturación'),
    ('Historial',      'history',        'Historial de tratamientos');

-- ------------------------------------------------------------
-- 3. Planes de suscripción
-- ------------------------------------------------------------
CREATE TABLE plans (
    id             INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name           VARCHAR(60)   NOT NULL UNIQUE,
    max_patients   INTEGER       NOT NULL DEFAULT 0, -- 0 = ilimitado
    max_users      INTEGER       NOT NULL DEFAULT 0, -- 0 = ilimitado
    price_monthly  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO plans (name, max_patients, max_users, price_monthly) VALUES
    ('free',  50,  2,  0.00),
    ('basic', 500, 10, 29.99),
    ('pro',   0,   0,  79.99);

-- ------------------------------------------------------------
-- 4. Clínicas (tenants)
-- ------------------------------------------------------------
CREATE TABLE clinics (
    id               INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name             VARCHAR(120) NOT NULL,
    subdomain        VARCHAR(60)  NOT NULL UNIQUE,
    active           BOOLEAN      NOT NULL DEFAULT TRUE,
    plan             VARCHAR(20)  NOT NULL DEFAULT 'basic' CHECK (plan IN ('free','basic','pro')),
    specialty_id     INTEGER      DEFAULT NULL REFERENCES specialties(id),
    plan_id          INTEGER      DEFAULT NULL REFERENCES plans(id),
    plan_expires_at  DATE         DEFAULT NULL,
    smtp_host        VARCHAR(120) DEFAULT NULL,
    smtp_port        SMALLINT     DEFAULT 587,
    smtp_username    VARCHAR(120) DEFAULT NULL,
    smtp_password    VARCHAR(255) DEFAULT NULL,
    smtp_from        VARCHAR(120) DEFAULT NULL,
    smtp_from_name   VARCHAR(120) DEFAULT NULL,
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- Clínica demo — credenciales admin/admin123 (cambiar en producción)
INSERT INTO clinics (name, subdomain, plan, specialty_id, plan_id)
SELECT 'Clínica Anguizola', 'anguizola', 'pro',
       (SELECT id FROM specialties WHERE slug = 'dental'),
       (SELECT id FROM plans       WHERE name = 'pro');

-- ------------------------------------------------------------
-- 5. Feature flags por clínica
-- ------------------------------------------------------------
CREATE TABLE clinic_modules (
    clinic_id  INTEGER NOT NULL REFERENCES clinics(id),
    module_id  INTEGER NOT NULL REFERENCES modules(id),
    enabled    BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (clinic_id, module_id)
);

-- Habilitar todos los módulos para la clínica demo
INSERT INTO clinic_modules (clinic_id, module_id, enabled)
SELECT 1, id, TRUE FROM modules;

-- ------------------------------------------------------------
-- 6. Staff (reemplaza doctores hardcodeados)
-- ------------------------------------------------------------
CREATE TABLE staff (
    id        INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id INTEGER NOT NULL REFERENCES clinics(id),
    name      VARCHAR(120) NOT NULL,
    role      VARCHAR(60)  NOT NULL DEFAULT 'doctor',
    active    BOOLEAN      NOT NULL DEFAULT TRUE
);

INSERT INTO staff (clinic_id, name, role) VALUES
    (1, 'Dr. Júlio Anguizola Vial',       'doctor'),
    (1, 'Dr. Miguel Anguizola Severino',   'doctor'),
    (1, 'Dr. Amira Martínez de Anguizola', 'doctor');

-- ------------------------------------------------------------
-- 7. Pacientes
-- ------------------------------------------------------------
CREATE TABLE pacientes (
    id          INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id   INTEGER NOT NULL DEFAULT 1 REFERENCES clinics(id),
    nombre      VARCHAR(120) NOT NULL,
    apellido    VARCHAR(120) NOT NULL,
    cedula      VARCHAR(30)  NOT NULL,
    direccion   VARCHAR(255) DEFAULT NULL,
    telefono    VARCHAR(60)  DEFAULT NULL,
    email       VARCHAR(120) DEFAULT NULL,
    ocupacion   VARCHAR(120) DEFAULT NULL,
    edad        VARCHAR(10)  DEFAULT NULL,
    motivo_consulta                             TEXT DEFAULT NULL,
    habitos_higienicos                          TEXT DEFAULT NULL,
    esta_bajo_tratamiento_actualmente           VARCHAR(5) DEFAULT NULL,
    "Ha_sido_hospitalizado_quirurgicamente"       VARCHAR(5) DEFAULT NULL,
    esta_tomando_algun_medicamento_o_droga      VARCHAR(5) DEFAULT NULL,
    presenta_algun_tipo_de_alergia              VARCHAR(5) DEFAULT NULL,
    "Ha_tenido_algun_tipo_de_enfermedad_cardiaca" VARCHAR(5) DEFAULT NULL,
    "Es_usted_diabetico_"                         VARCHAR(5) DEFAULT NULL,
    "Ha_tenido_tuberculosis_o_hepatitis"          VARCHAR(5) DEFAULT NULL,
    "Ha_presentado_alteraciones_en_el_sangrado"   VARCHAR(5) DEFAULT NULL,
    "Ha_tenido_alguna_enfermedad_de_transmision_sexual" VARCHAR(5) DEFAULT NULL,
    "Tiene_algun_tipo_de_mal_habito"              VARCHAR(5) DEFAULT NULL
);

CREATE INDEX idx_pacientes_clinic ON pacientes(clinic_id);

-- ------------------------------------------------------------
-- 8. Citas
-- ------------------------------------------------------------
CREATE TABLE citas_tabla (
    id                INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id         INTEGER NOT NULL DEFAULT 1 REFERENCES clinics(id),
    fecha_de_cita     DATE         NOT NULL,
    hora_de_cita      TIME         NOT NULL,
    nombre_paciente   VARCHAR(255) NOT NULL,
    asunto_de_la_cita VARCHAR(255) DEFAULT NULL,
    doctor            VARCHAR(120) DEFAULT NULL
);

CREATE INDEX idx_citas_clinic ON citas_tabla(clinic_id);

-- ------------------------------------------------------------
-- 9. Pagos
-- ------------------------------------------------------------
CREATE TABLE pago (
    id           INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id    INTEGER       NOT NULL DEFAULT 1 REFERENCES clinics(id),
    fecha        DATE          NOT NULL,
    nombre       VARCHAR(255)  NOT NULL,
    cedula       VARCHAR(30)   NOT NULL,
    monto        DECIMAL(10,2) NOT NULL DEFAULT 0,
    tipo_de_pago VARCHAR(60)   DEFAULT NULL,
    saldo        DECIMAL(10,2) NOT NULL DEFAULT 0,
    tratamiento  VARCHAR(255)  DEFAULT NULL,
    nota         TEXT          DEFAULT NULL
);

CREATE INDEX idx_pago_clinic ON pago(clinic_id);

-- ------------------------------------------------------------
-- 10. Odontograma / consultas
-- ------------------------------------------------------------
CREATE TABLE consulta (
    id          INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id   INTEGER NOT NULL DEFAULT 1 REFERENCES clinics(id),
    cedula      VARCHAR(30)  NOT NULL,
    tratamiento TEXT         DEFAULT NULL,
    "imageType" VARCHAR(50)  DEFAULT NULL,
    "imageData" BYTEA        DEFAULT NULL, -- Legacy — usar imagen_path para registros nuevos
    imagen_path VARCHAR(255) DEFAULT NULL, -- Ruta relativa a admin/uploads/
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_consulta_clinic ON consulta(clinic_id);

-- ------------------------------------------------------------
-- 11. Usuarios del staff
-- ------------------------------------------------------------
CREATE TABLE users (
    id        INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id INTEGER      NULL DEFAULT NULL REFERENCES clinics(id),
    username  VARCHAR(60)  NOT NULL,
    password  VARCHAR(255) NOT NULL,
    name      VARCHAR(120) NOT NULL,
    role      VARCHAR(20)  NOT NULL DEFAULT 'admin' CHECK (role IN ('admin','medico','recepcion','superadmin')),
    CONSTRAINT uq_users_clinic_username UNIQUE (clinic_id, username)
);

CREATE INDEX idx_users_clinic ON users(clinic_id);

-- Usuario admin demo: admin / admin123 (bcrypt cost 12) — CAMBIAR EN PRODUCCIÓN
INSERT INTO users (clinic_id, username, password, name, role)
VALUES (1, 'admin',
        '$2y$12$3HJayANV95vDyaTREGn5N.Fz8yhJAZPMviMaRD.86q.28CKrW5WMW',
        'Administrador', 'admin');

-- Superadmin — sin clínica asignada
INSERT INTO users (clinic_id, username, password, name, role) VALUES
(NULL, 'superadmin',
 '$2y$12$JQptcuhHZudW827IHaiGuePcbbFm5/3TQyTcrPJxG.NaD0KB2fy.K',
 'Super Administrador',
 'superadmin');

-- ------------------------------------------------------------
-- 12. Notas clínicas (texto libre, para especialidades no-dentales)
-- ------------------------------------------------------------
CREATE TABLE clinic_notes (
    id         INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id  INTEGER NOT NULL REFERENCES clinics(id),
    cedula     VARCHAR(30)  NOT NULL,
    fecha      DATE         NOT NULL,
    contenido  TEXT         NOT NULL,
    created_by INTEGER      NOT NULL DEFAULT 0, -- users.id
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_notes_clinic  ON clinic_notes(clinic_id);
CREATE INDEX idx_notes_cedula ON clinic_notes(cedula);

-- ------------------------------------------------------------
-- 13. Audit log
-- ------------------------------------------------------------
CREATE TABLE audit_log (
    id         BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    clinic_id  INTEGER      NOT NULL,
    user_id    INTEGER      NOT NULL DEFAULT 0,
    action     VARCHAR(60)  NOT NULL, -- login, insert_patient, delete_cita…
    entity     VARCHAR(60)  NOT NULL DEFAULT '',
    entity_id  VARCHAR(60)  NOT NULL DEFAULT '',
    ip         VARCHAR(45)  NOT NULL DEFAULT '',
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_audit_clinic  ON audit_log(clinic_id);
CREATE INDEX idx_audit_created ON audit_log(created_at);
