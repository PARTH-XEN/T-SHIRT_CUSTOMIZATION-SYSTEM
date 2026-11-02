<?php
require_once 'header.php';

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    redirect('orders.php');
}

$orders = $pdo->query("SELECT orders.*, users.email FROM orders JOIN users ON orders.user_id = users.id ORDER BY date DESC")->fetchAll();
?>

<h2>Manage Orders</h2>

<div style="overflow-x: auto; margin-top: 20px;">
    <table>
        <tr>
            <th>ID</th>
            <th>User Email</th>
            <th>Total</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td>#<?php echo $o['id']; ?></td>
            <td><?php echo escape($o['email']); ?></td>
            <td>₹<?php echo number_format($o['total'], 2); ?></td>
            <td><?php echo date('M d, Y H:i', strtotime($o['date'])); ?></td>
            <td>
                <span style="font-weight: bold; color: <?php echo $o['status'] === 'completed' ? 'green' : ($o['status'] === 'cancelled' ? 'red' : 'orange'); ?>">
                    <?php echo escape(ucfirst($o['status'])); ?>
                </span>
            </td>
            <td>
                <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                    <select name="status" style="padding: 5px;">
                        <option value="pending" <?php echo $o['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $o['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="completed" <?php echo $o['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $o['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status" class="btn" style="padding: 5px 10px;">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once 'footer.php'; ?>
