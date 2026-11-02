<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) {
    redirect('cart.php');
}

$total = 0;
foreach ($cart as $item) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$item['product_id']]);
    $price = $stmt->fetchColumn();
    $total += $price * $item['quantity'];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    if (empty($name) || empty($address) || empty($phone)) {
        $error = 'All fields are required.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, address, phone, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $name, $address, $phone, $total]);
            $order_id = $pdo->lastInsertId();

            $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, design_id, color, size, quantity, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($cart as $item) {
                $stmt_p = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $stmt_p->execute([$item['product_id']]);
                $price = $stmt_p->fetchColumn();

                $stmt_item->execute([
                    $order_id,
                    $item['product_id'],
                    $item['design_id'],
                    $item['color'],
                    $item['size'],
                    $item['quantity'],
                    $price
                ]);
            }

            $pdo->commit();
            
            $_SESSION['cart'] = [];
            
            echo "<div class='alert alert-success' style='text-align: center; margin-top: 50px; background-color: var(--card-bg); border: 1px solid var(--border-color);'>
                    <h2 style='color: #28a745;'>Order Placed Successfully!</h2>
                    <p style='margin: 15px 0;'>Your order ID is #{$order_id}.</p>
                    <a href='profile.php' class='btn btn-accent'>View Your Orders</a>
                  </div>";
            require_once 'includes/footer.php';
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to place order: ' . $e->getMessage();
        }
    }
}
?>

<h2 style="text-align: center; margin-bottom: 20px;">Checkout</h2>

<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<div class="grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
    
    <div class="card" style="text-align: left;">
        <h3>Shipping Information</h3>
        <form method="POST" action="checkout.php" style="margin-top: 15px;">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo escape($_SESSION['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Complete Address</label>
                <textarea name="address" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" required>
            </div>
            <button type="submit" class="btn btn-accent" style="width: 100%; font-size: 1.1rem; padding: 12px; margin-top: 10px;">Place Order</button>
        </form>
    </div>

    <div class="card" style="text-align: left;">
        <h3>Order Summary</h3>
        <ul style="list-style: none; padding: 0; margin-top: 15px;">
            <?php foreach ($cart as $item): 
                $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch();
            ?>
            <li style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px;">
                <span><?php echo escape($product['name']); ?> <br><small style="color: #777;">(<?php echo escape($item['size']); ?>, <?php echo escape($item['color']); ?>) x <?php echo $item['quantity']; ?></small></span>
                <span style="font-weight: bold; margin-top: auto;">₹<?php echo number_format($product['price'] * $item['quantity'], 2); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <span style="font-size: 1.2rem; font-weight: bold;">Grand Total:</span>
            <span style="font-size: 1.5rem; color: var(--accent-color); font-weight: bold;">₹<?php echo number_format($total, 2); ?></span>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
