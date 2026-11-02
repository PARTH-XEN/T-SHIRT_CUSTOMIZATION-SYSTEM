<?php require_once 'includes/header.php'; ?>

<h2 style="text-align: center; margin-bottom: 30px;">All Products</h2>
<div class="grid grid-3">
    <?php
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
    foreach ($products as $p):
    ?>
    <div class="card">
        <img src="<?php echo escape($p['image']); ?>" alt="<?php echo escape($p['name']); ?>" onerror="this.src='https://via.placeholder.com/300x400?text=T-Shirt'">
        <h3 style="margin-top: 15px;"><?php echo escape($p['name']); ?></h3>
        <p style="color: #777; margin-top: 5px;">Type: <?php echo escape($p['type']); ?></p>
        <p style="color: var(--accent-color); font-weight: bold; margin: 10px 0; font-size: 1.2rem;">₹<?php echo number_format($p['price'], 2); ?></p>
        <a href="customize.php?product_id=<?php echo $p['id']; ?>" class="btn">Customize This</a>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
