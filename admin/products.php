<?php
require_once 'header.php';

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $tmp_name = $_FILES['image']['tmp_name'];
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $destination = $upload_dir . $filename;
        if (move_uploaded_file($tmp_name, $destination)) {
            $image_path = 'assets/images/' . $filename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, type, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $type, $price, $image_path]);
    redirect('products.php');
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    redirect('products.php');
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<h2>Manage Products</h2>

<div class="card" style="margin: 20px 0; max-width: 600px; text-align: left;">
    <h3>Add New Product</h3>
    <form method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="type" required>
                <option value="Men">Men</option>
                <option value="Women">Women</option>
                <option value="Oversized">Oversized</option>
            </select>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" name="price" required>
        </div>
        <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" accept="image/*" required>
        </div>
        <button type="submit" name="add_product" class="btn btn-accent">Add Product</button>
    </form>
</div>

<h3>Existing Products</h3>
<div style="overflow-x: auto;">
    <table style="margin-top: 15px;">
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Type</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?php echo $p['id']; ?></td>
            <td><img src="../<?php echo escape($p['image']); ?>" width="50" style="border-radius: 4px;"></td>
            <td><?php echo escape($p['name']); ?></td>
            <td><?php echo escape($p['type']); ?></td>
            <td>₹<?php echo number_format($p['price'], 2); ?></td>
            <td>
                <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn" style="background-color: #d9534f; color: white;" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once 'footer.php'; ?>
