-- Biblioteca en Jugadores. Importar en la base de datos existente de Jugadores (MySQL).
-- No elimina ni reemplaza usuarios, roles o registros existentes.
-- Mantiene los acentos y la ñ al importar este archivo.
SET NAMES utf8mb4;

-- Autores que pueden tener una o varias obras.
CREATE TABLE IF NOT EXISTS autor (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    foto VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categorías para organizar las obras.
CREATE TABLE IF NOT EXISTS categoria (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cada obra pertenece a un autor y una categoría; no se borran mientras tengan obras.
CREATE TABLE IF NOT EXISTS obra (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    idautor INTEGER NOT NULL,
    idcategoria INTEGER NOT NULL,
    foto VARCHAR(255) NULL,
    FOREIGN KEY (idautor) REFERENCES autor(id) ON DELETE RESTRICT,
    FOREIGN KEY (idcategoria) REFERENCES categoria(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Partidas con su clave, estado y fecha de creación.
CREATE TABLE IF NOT EXISTS partida (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(10) NOT NULL,
    status INTEGER NOT NULL,
    fecha_creacion DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cada inscripción une una partida con una cuenta de Jugadores, sin repetir la pareja.
-- Al borrar la partida o la cuenta, también se elimina su inscripción.
-- Ajuste al tipo real de users.id: admite la instalación existente y jugadores.sql.
SET @tipo_usuario = (SELECT COLUMN_TYPE FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'id');
SET @crear_participantes = CONCAT('CREATE TABLE IF NOT EXISTS partidaxusuario (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    idpartida INTEGER NOT NULL,
    idusuario ', @tipo_usuario, ' NOT NULL,
    UNIQUE (idpartida, idusuario),
    FOREIGN KEY (idpartida) REFERENCES partida(id) ON DELETE CASCADE,
    FOREIGN KEY (idusuario) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
PREPARE crear_participantes FROM @crear_participantes;
EXECUTE crear_participantes;
DEALLOCATE PREPARE crear_participantes;

-- Permisos de los nuevos catálogos.
INSERT INTO permiso (nombre, clave)
SELECT 'Administrar autores', 'administrar_autor'
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE clave = 'administrar_autor');

INSERT INTO permiso (nombre, clave)
SELECT 'Administrar categorías', 'administrar_categoria'
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE clave = 'administrar_categoria');

-- Corrige el nombre si una importación anterior interpretó UTF-8 como Latin-1.
UPDATE permiso SET nombre = 'Administrar categorías'
WHERE clave = 'administrar_categoria'
    AND HEX(nombre) = '41646D696E6973747261722063617465676F72C383C2AD6173';

INSERT INTO permiso (nombre, clave)
SELECT 'Administrar obras', 'administrar_obra'
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE clave = 'administrar_obra');

INSERT INTO permiso (nombre, clave)
SELECT 'Administrar partidas', 'administrar_partida'
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE clave = 'administrar_partida');

INSERT INTO permiso (nombre, clave)
SELECT 'Administrar participantes', 'administrar_partidaxusuario'
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE clave = 'administrar_partidaxusuario');

-- Da los nuevos permisos al administrador sin duplicar los que ya tiene.
INSERT INTO rolxpermiso (idrol, idpermiso)
SELECT r.id, p.id FROM rol r CROSS JOIN permiso p
WHERE r.nombre = 'Administrador'
    AND p.clave IN ('administrar_autor', 'administrar_categoria', 'administrar_obra', 'administrar_partida', 'administrar_partidaxusuario')
    AND NOT EXISTS (SELECT 1 FROM rolxpermiso rp WHERE rp.idrol = r.id AND rp.idpermiso = p.id);

-- Datos recuperados de Biblioteca.
-- Al repetir la importación se conservan los registros existentes.
INSERT INTO autor (id, nombre, foto)
SELECT 1, 'jesus sansores', 'fotos/autores/biblioteca-1.jpg'
WHERE NOT EXISTS (SELECT 1 FROM autor WHERE id = 1);

INSERT INTO categoria (id, nombre)
SELECT 1, 'mascotas'
WHERE NOT EXISTS (SELECT 1 FROM categoria WHERE id = 1);

INSERT INTO categoria (id, nombre)
SELECT 2, 'alimentos'
WHERE NOT EXISTS (SELECT 1 FROM categoria WHERE id = 2);

INSERT INTO obra (id, nombre, idautor, idcategoria, foto)
SELECT 1, 'obra cenicienta', 1, 1, 'fotos/obras/biblioteca-1.jpg'
WHERE NOT EXISTS (SELECT 1 FROM obra WHERE id = 1);

INSERT INTO obra (id, nombre, idautor, idcategoria, foto)
SELECT 4, 'obra el principito', 1, 2, 'fotos/obras/biblioteca-4.png'
WHERE NOT EXISTS (SELECT 1 FROM obra WHERE id = 4);

INSERT INTO partida (id, clave, status, fecha_creacion)
SELECT 1, 'test', 1, '2026-10-02 05:18:26'
WHERE NOT EXISTS (SELECT 1 FROM partida WHERE id = 1);

