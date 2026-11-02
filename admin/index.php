<?php
require_once 'header.php';

$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$orders_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue = $pdo->query("SELECT SUM(total) FROM orders")->fetchColumn();
$products_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>

<h2>Admin Dashboard</h2>
<div class="grid grid-3" style="margin-top: 20px;">
    <div class="card">
        <h3>Total Users</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: var(--accent-color);"><?php echo $users_count; ?></p>
    </div>
    <div class="card">
        <h3>Total Orders</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: var(--accent-color);"><?php echo $orders_count; ?></p>
    </div>
    <div class="card">
        <h3>Total Revenue</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: var(--accent-color);">₹<?php echo number_format($revenue ?? 0, 2); ?></p>
    </div>
    <div class="card">
        <h3>Products</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: var(--accent-color);"><?php echo $products_count; ?></p>
    </div>
</div>

<?php require_once 'footer.php'; ?>
