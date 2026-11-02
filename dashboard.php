<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// Get total orders for the user
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id");
$order_count = 0;
if ($result) {
    $row = $result->fetch_assoc();
    $order_count = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Custom T-Shirt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container dashboard-container">
    <h1 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 1rem;">Welcome to your Dashboard!</h1>
    <p style="font-size: 1.2rem; margin-bottom: 3rem; color: #4a5568;">You have <strong style="color: #3498db;"><?php echo $order_count; ?></strong> saved orders.</p>
    
    <div style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;">
        <a href="index.php" class="btn-submit" style="text-decoration: none; padding: 1.5rem 3rem; font-size: 1.2rem; display: inline-block; width: auto;">Customization Panel</a>
        <a href="orders.php" class="btn-submit" style="background: #2ecc71; text-decoration: none; padding: 1.5rem 3rem; font-size: 1.2rem; display: inline-block; width: auto;">View My Orders</a>
    </div>
</div>

</body>
</html>
