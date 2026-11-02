<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="navbar-brand">
        <strong>T-Shirt Studio</strong>
    </div>
    <div class="navbar-links">
        <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
        <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Customization</a>
        <a href="orders.php" class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">Orders</a>
    </div>
    <div class="navbar-user">
        Welcome, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</nav>
