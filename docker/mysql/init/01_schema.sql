-- Base schema for Clínica Anguizola
-- Run before 02_migration_fase2.sql

CREATE TABLE IF NOT EXISTS pacientes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(120) NOT NULL,
    apellido    VARCHAR(120) NOT NULL,
    cedula      VARCHAR(30)  NOT NULL,
    direccion   VARCHAR(255),
    telefono    VARCHAR(30),
    email       VARCHAR(120),
    ocupacion   VARCHAR(120),
    edad        VARCHAR(10),
    motivo_consulta   TEXT,
    habitos_higienicos TEXT,
    esta_bajo_tratamiento_actualmente       VARCHAR(5),
    Ha_sido_hospitalizado_quirurgicamente   VARCHAR(5),
    esta_tomando_algun_medicamento_o_droga  VARCHAR(5),
    presenta_algun_tipo_de_alergia          VARCHAR(5),
    Ha_tenido_algun_tipo_de_enfermedad_cardiaca VARCHAR(5),
    Es_usted_diabetico_                     VARCHAR(5),
    Ha_tenido_tuberculosis_o_hepatitis      VARCHAR(5),
    Ha_presentado_alteraciones_en_el_sangrado VARCHAR(5),
    Ha_tenido_alguna_enfermedad_de_transmision_sexual VARCHAR(5),
    Tiene_algun_tipo_de_mal_habito          VARCHAR(5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS citas_tabla (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fecha_de_cita   DATE         NOT NULL,
    hora_de_cita    TIME         NOT NULL,
    nombre_paciente VARCHAR(255) NOT NULL,
    asunto_de_la_cita VARCHAR(255),
    doctor          VARCHAR(120)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pago (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fecha        DATE         NOT NULL,
    nombre       VARCHAR(255) NOT NULL,
    cedula       VARCHAR(30)  NOT NULL,
    monto        DECIMAL(10,2) NOT NULL DEFAULT 0,
    tipo_de_pago VARCHAR(60),
    saldo        DECIMAL(10,2) NOT NULL DEFAULT 0,
    tratamiento  VARCHAR(255),
    nota         TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS consulta (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cedula      VARCHAR(30)  NOT NULL,
    tratamiento TEXT,
    imageType   VARCHAR(50),
    imageData   LONGBLOB,
    imagen_path VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name     VARCHAR(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin user: admin / admin123 (bcrypt cost 12)
-- CHANGE PASSWORD after first login!
INSERT INTO users (username, password, name)
SELECT 'admin',
       '$2y$12$3HJayANV95vDyaTREGn5N.Fz8yhJAZPMviMaRD.86q.28CKrW5WMW',
       'Administrador'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');
