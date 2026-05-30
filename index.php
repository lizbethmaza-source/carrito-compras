<?php include 'carrito.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Mi Tienda Virtual</title>
    
    <style>
        :root {
            --primary: #0f172a;
            --accent: #2563eb;
            --accent-hover: #1d4ed8;
            --background: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --border: #e2e8f0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: var(--background); color: var(--text); padding: 40px 20px; }
        
        .container { max-width: 1100px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 2.5rem; color: var(--primary); }
        .header p { color: #64748b; margin-top: 5px; }

        .layout { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        
        /* Grid de tarjetas de productos */
        .productos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
        .producto-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; }
        .producto-card h3 { font-size: 1.1rem; margin-bottom: 10px; color: var(--primary); }
        .producto-card .precio { font-size: 1.4rem; font-weight: bold; color: var(--accent); margin-bottom: 15px; }
        
        .btn-agregar { background: var(--accent); color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%; transition: background 0.2s; }
        .btn-agregar:hover { background: var(--accent-hover); }

        /* Estilos de la sección del carrito */
        .carrito-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: fit-content; }
        .carrito-card h2 { font-size: 1.3rem; margin-bottom: 15px; border-bottom: 2px solid var(--background); padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        td, th { padding: 10px 0; border-bottom: 1px solid var(--border); text-align: left; font-size: 0.95rem; }
        th { color: #64748b; font-size: 0.85rem; text-transform: uppercase; }
        
        .total-box { display: flex; justify-content: space-between; align-items: center; margin: 20px 0; }
        .total-box span { font-size: 1.1rem; color: #64748b; }
        .total-box strong { font-size: 1.6rem; color: var(--primary); }

        @media (max-width: 768px) { .layout { grid-template-columns: 1fr; } }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://www.paypal.com/sdk/js?client-id=test&currency=USD"></script>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Mi Tienda Virtual</h1>
        <p>Selecciona tus productos y paga de forma segura con PayPal</p>
    </div>

    <div class="layout">
        <div class="productos-grid">
            <?php
            $resultado = $conexion->query("SELECT * FROM productos");
            if ($resultado) {
                while ($prod = $resultado->fetch_assoc()) {
                ?>
                    <div class="producto-card">
                        <h3><?php echo $prod['nombre']; ?></h3>
                        <div>
                            <div class="precio">$<?php echo number_format($prod['precio'], 2); ?></div>
                            <button class="btn-agregar" 
                                    data-id="<?php echo $prod['id']; ?>" 
                                    data-nombre="<?php echo $prod['nombre']; ?>" 
                                    data-precio="<?php echo $prod['precio']; ?>">
                                Agregar al carrito
                            </button>
                        </div>
                    </div>
                <?php 
                }
            } else {
                echo "<p>No se encontraron productos. Verifica tu Base de Datos.</p>";
            }
            ?>
        </div>

        <div class="carrito-card">
            <h2>Tu Pedido</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: center;">Cant.</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_general = 0;
                    if (!empty($_SESSION['carrito'])): 
                        foreach ($_SESSION['carrito'] as $id => $item): 
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total_general += $subtotal;
                    ?>
                        <tr>
                            <td><strong><?php echo $item['nombre']; ?></strong></td>
                            <td style="text-align: center;"><?php echo $item['cantidad']; ?></td>
                            <td style="text-align: right;">$<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                    <?php 
                        endforeach; 
                    else:
                    ?>
                        <tr><td colspan="3" style="text-align: center; color: #64748b; padding: 20px 0;">El carrito está vacío</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="total-box">
                <span>Total:</span>
                <strong>$<?php echo number_format($total_general, 2); ?></strong>
            </div>

            <?php if ($total_general > 0): ?>
                <div id="paypal-button-container"></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// 1. Script AJAX para agregar al carrito sin recargar toda la página
$('.btn-agregar').click(function() {
    let id = $(this).data('id');
    let nombre = $(this).data('nombre');
    let precio = $(this).data('precio');

    $.ajax({
        url: 'carrito.php',
        type: 'POST',
        data: { accion: 'agregar', id: id, nombre: nombre, precio: precio },
        success: function() {
            location.reload(); // Recarga rápida para actualizar la tabla del carrito
        }
    });
});

// 2. Script que levanta la pasarela de PayPal
if (document.getElementById('paypal-button-container')) {
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: { value: '<?php echo $total_general; ?>' }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Mandamos el total de vuelta a PHP para registrar la compra
                $.ajax({
                    url: 'carrito.php',
                    type: 'POST',
                    data: { accion: 'pagar', total: '<?php echo $total_general; ?>' },
                    success: function() {
                        alert('¡Pago aprobado por PayPal! Tu pedido se ha guardado en la base de datos.');
                        location.reload(); 
                    }
                });
            });
        }
    }).render('#paypal-button-container'); 
}
</script>

</body>
</html>