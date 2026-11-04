-- Proyecto escolar Subas Art.
-- Selecciona una base de datos vacía en MySQL e importa este archivo.
-- Las tablas ya incluyen las relaciones y los datos iniciales.
SET NAMES utf8mb4;

-- Roles y permisos.
CREATE TABLE rol (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE permiso (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    clave VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE rolxpermiso (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    idrol INTEGER NOT NULL,
    idpermiso INTEGER NOT NULL,
    UNIQUE (idrol, idpermiso),
    FOREIGN KEY (idrol) REFERENCES rol(id) ON DELETE CASCADE,
    FOREIGN KEY (idpermiso) REFERENCES permiso(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cuentas de acceso y perfiles de jugadores.
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    idrol INTEGER NULL,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (idrol) REFERENCES rol(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE jugador (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    alias VARCHAR(100) NOT NULL UNIQUE,
    user_id INTEGER NOT NULL UNIQUE,
    foto VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablas de Laravel para sesiones, caché y trabajos.
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INTEGER NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INTEGER NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX cache_expiration_index ON cache(expiration);

CREATE TABLE cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX cache_locks_expiration_index ON cache_locks(expiration);

CREATE TABLE jobs (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts INTEGER NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX jobs_queue_index ON jobs(queue);

CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE failed_jobs (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection VARCHAR(255) NOT NULL,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX failed_jobs_connection_queue_failed_at_index ON failed_jobs(connection, queue, failed_at);

-- Datos de ejemplo
-- Ambas cuentas usan la contraseña: password.
-- Las contraseñas se guardan cifradas.
INSERT INTO rol (id, nombre) VALUES
    (1, 'Administrador'),
    (2, 'Jugador');

INSERT INTO permiso (id, nombre, clave) VALUES
    (1, 'Administrar usuarios', 'administrar_usuario'),
    (2, 'Administrar permisos', 'administrar_permiso'),
    (3, 'Administrar roles', 'administrar_rol'),
    (4, 'Ver panel de administración', 'home_admin'),
    (5, 'Ver perfil de jugador', 'home_jugador');

INSERT INTO rolxpermiso (idrol, idpermiso) VALUES
    (1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (2, 5);

INSERT INTO users (id, name, email, password, idrol) VALUES
    (1, 'Administrador', 'admin@subasart.test', '$2y$12$V6vIad8Dy6y1qVrKU7NvPed0MaivMpAORMzXqAQjzN/hiAEKIMCOm', 1),
    (2, 'Jugador de ejemplo', 'jugador@subasart.test', '$2y$12$V6vIad8Dy6y1qVrKU7NvPed0MaivMpAORMzXqAQjzN/hiAEKIMCOm', 2);

INSERT INTO jugador (alias, user_id) VALUES ('JugadorDemo', 2);
