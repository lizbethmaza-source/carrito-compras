CREATE DATABASE IF NOT EXISTS tienda_web;
USE tienda_web;

-- 1. Tabla de Productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255)
);

-- 2. Tabla de Pedidos (Ventas)
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) DEFAULT 'Pagado'
);

-- 3. Tabla de Detalle del Pedido
CREATE TABLE detalle_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
);

-- Insertar productos de prueba
INSERT INTO productos (nombre, precio, imagen) VALUES 
('Laptop HP Student', 650.00, 'https://via.placeholder.com/150'),
('Mouse Inalámbrico', 15.50, 'https://via.placeholder.com/150'),
('Teclado Mecánico RGB', 45.00, 'https://via.placeholder.com/150');