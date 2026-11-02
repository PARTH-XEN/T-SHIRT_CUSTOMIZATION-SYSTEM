<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $product_id = (int)$_POST['product_id'];
    $design_id = (int)$_POST['design_id'];
    $size = $_POST['size'];
    $color = $_POST['color'];

    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add item
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'design_id' => $design_id,
        'size' => $size,
        'color' => $color,
        'quantity' => 1
    ];
    
    redirect('cart.php');
}

// Handle Remove from Cart
if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); 
    }
    redirect('cart.php');
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
?>

<h2 style="margin-bottom: 20px;">Shopping Cart</h2>

<?php if (empty($cart)): ?>
    <div class="alert alert-success" style="background-color: #f8f9fa; color: #333; border-color: #ddd;">
        Your cart is empty. <a href="products.php" style="color: var(--accent-color); font-weight: bold;">Browse products</a>.
    </div>
<?php else: ?>
    <div style="overflow-x: auto;">
        <table>
            <tr>
                <th>Product</th>
                <th>Design Details</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cart as $index => $item): 
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch();
                $price = $product['price'];
                $total += $price * $item['quantity'];

                $stmt_d = $pdo->prepare("SELECT preview_image FROM designs WHERE id = ?");
                $stmt_d->execute([$item['design_id']]);
                $design = $stmt_d->fetch();
            ?>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img src="<?php echo escape($product['image']); ?>" width="50" style="border-radius: 4px;" onerror="this.src='https://via.placeholder.com/50?text=T'">
                        <span><?php echo escape($product['name']); ?></span>
                    </div>
                </td>
                <td>
                    Size: <?php echo escape($item['size']); ?> <br>
                    Color: <?php echo escape($item['color']); ?> <br>
                    <?php if($design): ?>
                        <a href="<?php echo escape($design['preview_image']); ?>" target="_blank" style="font-size: 0.85rem; color: var(--accent-color);">View Design</a>
                    <?php endif; ?>
                </td>
                <td>₹<?php echo number_format($price, 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>
                    <a href="cart.php?remove=<?php echo $index; ?>" class="btn" style="background-color: #dc3545; color: white; padding: 5px 10px;">Remove</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card" style="margin-top: 20px; text-align: right; max-width: 400px; margin-left: auto;">
        <h3>Cart Total: <span style="color: var(--accent-color); font-size: 1.8rem;">₹<?php echo number_format($total, 2); ?></span></h3>
        <a href="checkout.php" class="btn btn-accent" style="display: inline-block; margin-top: 15px; width: 100%; font-size: 1.2rem; padding: 12px;">Proceed to Checkout</a>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
