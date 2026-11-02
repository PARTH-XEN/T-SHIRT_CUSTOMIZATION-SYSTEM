<?php require_once 'includes/header.php'; ?>

<div style="text-align: center; padding: 50px 0;">
    <h1 style="font-size: 3rem; margin-bottom: 20px;">Design Your Own Custom T-Shirts</h1>
    <p style="margin: 20px 0; font-size: 1.2rem; color: #666;">Premium quality, infinite possibilities. Drag, drop, and wear.</p>
    <a href="customize.php" class="btn btn-accent" style="font-size: 1.2rem; padding: 15px 30px; margin-top: 20px;">Start Designing</a>
</div>

<h2 style="text-align: center; margin: 40px 0 20px;">Featured Base Products</h2>
<div class="grid grid-3">
    <?php
    $stmt = $pdo->query("SELECT * FROM products LIMIT 3");
    $products = $stmt->fetchAll();
    foreach ($products as $p):
    ?>
    <div class="card">
        <img src="<?php echo escape($p['image']); ?>" alt="<?php echo escape($p['name']); ?>" onerror="this.src='https://via.placeholder.com/300x400?text=T-Shirt'">
        <h3 style="margin-top: 15px;"><?php echo escape($p['name']); ?></h3>
        <p style="color: var(--accent-color); font-weight: bold; margin: 10px 0; font-size: 1.2rem;">₹<?php echo number_format($p['price'], 2); ?></p>
        <a href="customize.php?product_id=<?php echo $p['id']; ?>" class="btn">Customize This</a>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
