<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav { background: var(--primary-color); padding: 15px 20px; display: flex; align-items: center;}
        .admin-nav a { color: var(--bg-color); margin-right: 20px; text-decoration: none; font-weight: bold; }
        .admin-nav a:hover { color: var(--accent-color); }
        .admin-brand { margin-right: auto; color: #fff; font-size: 1.2rem; font-weight: bold;}
    </style>
</head>
<body>
    <div class="admin-nav">
        <div class="admin-brand">Admin Panel</div>
        <a href="index.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="../index.php">View Web Site</a>
    </div>
    <main class="container" style="max-width: 1400px; margin-top: 20px;">
