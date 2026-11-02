<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';

$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Custom T-Shirt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container orders-container">
    <h1 style="color: #2c3e50; font-size: 2rem; margin-bottom: 2rem; width: 100%;">My Orders</h1>
    
    <?php if ($orders && $orders->num_rows > 0): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Custom Text</th>
                    <th>Shirt / Sleeve</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></td>
                    <td>"<?php echo htmlspecialchars($order['custom_text']); ?>" <br><small style="color:gray;">Font: <?php echo htmlspecialchars(explode(',', $order['font_family'])[0]); ?> | Rot: <?php echo $order['text_rotation']; ?>&deg;</small></td>
                    <td>
                        <span style="display:inline-block; width:20px; height:20px; background:<?php echo htmlspecialchars($order['shirt_color']); ?>; border:1px solid #ccc; border-radius: 3px;" title="Body Color"></span> / 
                        <span style="display:inline-block; width:20px; height:20px; background:<?php echo htmlspecialchars($order['sleeve_color']); ?>; border:1px solid #ccc; border-radius: 3px;" title="Sleeve Color"></span>
                    </td>
                    <td style="font-weight: bold; color: #3498db;">₹<?php echo number_format($order['total_price'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #718096; width: 100%;">You haven't placed any orders yet. <a href="index.php" style="color: #3498db; font-weight: bold; text-decoration: none;">Create one now!</a></p>
    <?php endif; ?>
</div>

</body>
</html>
