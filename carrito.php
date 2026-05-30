<?php
session_start();

// Conexión a la base de datos local
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tienda_web";
$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

// Acción 1: Agregar producto al carrito
if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id]['cantidad'] += 1;
    } else {
        $_SESSION['carrito'][$id] = array(
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => 1
        );
    }
    echo json_encode(['status' => 'success']);
    exit;
}

// Acción 2: Guardar la compra en la Base de Datos cuando PayPal apruebe el pago
if (isset($_POST['accion']) && $_POST['accion'] == 'pagar') {
    $total = $_POST['total'];

    if (empty($_SESSION['carrito'])) {
        echo json_encode(['status' => 'error']);
        exit;
    }

    // Insertar el registro principal del pedido
    $query_pedido = "INSERT INTO pedidos (total) VALUES ('$total')";
    if ($conexion->query($query_pedido)) {
        $pedido_id = $conexion->insert_id;

        // Insertar los productos que estaban dentro del carrito
        foreach ($_SESSION['carrito'] as $producto_id => $item) {
            $cantidad = $item['cantidad'];
            $precio_u = $item['precio'];
            
            $query_detalle = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) 
                              VALUES ('$pedido_id', '$producto_id', '$cantidad', '$precio_u')";
            $conexion->query($query_detalle);
        }

        unset($_SESSION['carrito']); // Vaciamos el carrito de la sesión
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}
?>