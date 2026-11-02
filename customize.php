<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 1;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='alert alert-error' style='margin-top:20px; text-align:center;'>Product not found. Please go back to <a href='products.php'>Products</a>.</div>";
    require_once 'includes/footer.php';
    exit;
}

$front_img = escape($product['image']);
$back_img = str_replace(array('.png', '.svg', '.jpg', '.jpeg'), array('-back.png', '-back.svg', '-back.jpg', '-back.jpeg'), $front_img); 
?>

<script>
    const PRODUCT_ID = <?php echo $product['id']; ?>;
    const PRODUCT_PRICE = <?php echo $product['price']; ?>;
    const FRONT_IMG = '<?php echo $front_img; ?>';
    const BACK_IMG = '<?php echo $back_img; ?>'; 
    let defaultShirtColor = '#ffffff'; // Default color
</script>

<div class="grid" style="grid-template-columns: 2fr 1fr; margin-top: 20px; gap: 30px;">
    
    <!-- Canvas Editor Area -->
    <div id="editor-container" style="position: relative; perspective: 1000px; display: flex; justify-content: center; align-items: center; background: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 20px; min-height: 500px; overflow: hidden;">
        <canvas id="tshirt-canvas" width="400" height="500" style="border: 1px dashed #ccc; background-color: #f9f9f9; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.1s; transform-style: preserve-3d; border-radius: 4px;"></canvas>
    </div>

    <!-- Controls Area -->
    <div class="card" style="text-align: left;">
        <h3 style="margin-bottom: 10px;">Customize <?php echo escape($product['name']); ?></h3>
        <p style="font-size: 1.5rem; color: var(--accent-color); font-weight: bold; margin-bottom: 20px;">₹<?php echo number_format($product['price'], 2); ?></p>
        
        <div class="form-group">
            <label style="font-weight: bold;">View Side</label>
            <div style="display: flex; gap: 10px;">
                <button id="btn-front" class="btn btn-accent" style="flex:1;">Front</button>
                <button id="btn-back" class="btn" style="flex:1; background-color: #ddd; color: #333;">Back</button>
            </div>
        </div>

        <div class="form-group">
            <label style="font-weight: bold;">1. Shirt Color</label>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;" id="color-palette">
                <!-- Preset colors -->
                <div class="color-swatch" style="background-color: #ffffff; border: 2px solid #333;" data-color="#ffffff"></div>
                <div class="color-swatch" style="background-color: #000000;" data-color="#000000"></div>
                <div class="color-swatch" style="background-color: #e74c3c;" data-color="#e74c3c"></div>
                <div class="color-swatch" style="background-color: #3498db;" data-color="#3498db"></div>
                <div class="color-swatch" style="background-color: #2ecc71;" data-color="#2ecc71"></div>
                <div class="color-swatch" style="background-color: #f1c40f;" data-color="#f1c40f"></div>
                <!-- Custom Hex Picker -->
                <input type="color" id="custom-shirt-color" title="Choose custom color" style="width: 30px; height: 30px; padding: 0; border: none; border-radius: 50%; cursor: pointer; overflow: hidden;">
            </div>
            <style>
                .color-swatch { width: 30px; height: 30px; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 2px solid transparent; transition: transform 0.2s; }
                .color-swatch:hover { transform: scale(1.1); }
                .color-swatch.active { border-color: var(--accent-color) !important; transform: scale(1.1); }
            </style>
        </div>

        <div class="form-group">
            <label style="font-weight: bold;">2. Add Graphic (Image/Logo)</label>
            <input type="file" id="upload-img" accept="image/*">
        </div>

        <div class="form-group">
            <label style="font-weight: bold;">3. Add Custom Text</label>
            <input type="text" id="custom-text" placeholder="Enter text here...">
            <div style="display: flex; gap: 10px; margin-top: 10px; align-items: center;">
                <input type="color" id="text-color" value="#000000" style="width: 50px; height: 40px; padding: 0;">
                <input type="number" id="text-size" value="30" min="10" max="100" style="flex:1;">
                <button id="btn-add-text" class="btn">Add</button>
            </div>
            <p style="font-size: 0.85rem; color: #777; margin-top: 5px;">* Drag elements to move. Press Delete/Backspace to remove selected element.</p>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--border-color);">

        <button id="btn-save-design" class="btn" style="width: 100%; margin-bottom: 15px; background-color: #28a745; color: white; display: flex; justify-content: center; align-items: center; gap: 10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Design
        </button>
        
        <form method="POST" action="cart.php" id="cart-form">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="design_id" id="design-id-input" value="">
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <select name="size" required style="flex:1;">
                    <option value="">Select Size</option>
                    <option value="S">Small (S)</option>
                    <option value="M">Medium (M)</option>
                    <option value="L">Large (L)</option>
                    <option value="XL">Extra Large (XL)</option>
                </select>
                <input type="hidden" name="color" id="selected-color-input" value="#ffffff">
            </div>
            <button type="submit" id="btn-add-cart" class="btn btn-accent" style="width: 100%; font-size: 1.1rem; padding: 12px;" disabled>Add to Cart</button>
            <p id="save-warning" style="font-size: 0.85rem; color: red; text-align: center; margin-top: 5px;">* Please save your design first</p>
        </form>
    </div>
</div>

<script src="assets/js/editor.js?v=<?php echo time(); ?>"></script>

<?php require_once 'includes/footer.php'; ?>
