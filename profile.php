<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch orders
$stmt_orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY date DESC");
$stmt_orders->execute([$user_id]);
$orders = $stmt_orders->fetchAll();
?>

<h2 style="margin-bottom: 20px;">User Profile</h2>
<div class="card" style="margin-bottom: 20px; text-align: left;">
    <h3>Welcome, <?php echo escape($user['name']); ?>!</h3>
    <p style="margin-top: 10px;"><strong>Email:</strong> <?php echo escape($user['email']); ?></p>
    <p><strong>Role:</strong> <?php echo escape(ucfirst($user['role'])); ?></p>
</div>

<h3>Your Orders</h3>
<?php if (count($orders) > 0): ?>
    <div style="overflow-x: auto; margin-top: 15px;">
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo date('M d, Y', strtotime($order['date'])); ?></td>
                <td>₹<?php echo number_format($order['total'], 2); ?></td>
                <td><?php echo escape(ucfirst($order['status'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php else: ?>
    <p style="margin-top: 15px;">You have no orders yet.</p>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
