SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS db_biblioteca;
USE db_biblioteca;

CREATE TABLE libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(30) UNIQUE NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    anio_publicacion INT NOT NULL,
    ejemplares_disponibles INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO libros (isbn, titulo, autor, anio_publicacion, ejemplares_disponibles) VALUES
    ('978-84-376-0494-7', 'Cien años de soledad', 'Gabriel García Márquez', 1967, 5),
    ('978-84-206-5100-6', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 1605, 3);
