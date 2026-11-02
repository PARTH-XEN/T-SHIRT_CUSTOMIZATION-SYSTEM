<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
require 'db.php';

// Sanitize inputs
$shirt_color = $conn->real_escape_string($_POST['shirt_color'] ?? '#3498db');
$sleeve_color = $conn->real_escape_string($_POST['sleeve_color'] ?? '#2980b9');
$custom_text = $conn->real_escape_string($_POST['custom_text'] ?? '');
$text_color = $conn->real_escape_string($_POST['text_color'] ?? '#ffffff');
$font_size = (int)($_POST['font_size'] ?? 20);
$font_family = $conn->real_escape_string($_POST['font_family'] ?? 'Arial, sans-serif');
$text_rotation = (int)($_POST['text_rotation'] ?? 0);
$user_id = $_SESSION['user_id'];

// Basic price calculation logic
$base_price = 15.00;
$text_length = strlen(trim($custom_text));
$printing_cost = $text_length > 0 ? 5.00 : 0; // Flat fee for printing
$total_price = $base_price + $printing_cost;

// Insert order into DB
$sql = "INSERT INTO orders (user_id, shirt_color, sleeve_color, custom_text, text_color, font_size, font_family, text_rotation, total_price) 
        VALUES ($user_id, '$shirt_color', '$sleeve_color', '$custom_text', '$text_color', $font_size, '$font_family', $text_rotation, $total_price)";

if ($conn->query($sql)) {
    $order_id = $conn->insert_id;
} else {
    die("Database insertion failed: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary - Custom T-Shirt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container container-order">
    <div class="order-summary">
        <h1>Order Confirmed!</h1>
        <p>Thank you for your purchase. Here are your order details:</p>
        
        <div class="order-details">
            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
            <p><strong>Body Color:</strong> <span style="display:inline-block; width:20px; height:20px; background-color: <?php echo htmlspecialchars($shirt_color); ?>; vertical-align: middle; border: 1px solid #ccc; border-radius: 4px;"></span> <?php echo htmlspecialchars($shirt_color); ?></p>
            <p><strong>Sleeve Color:</strong> <span style="display:inline-block; width:20px; height:20px; background-color: <?php echo htmlspecialchars($sleeve_color); ?>; vertical-align: middle; border: 1px solid #ccc; border-radius: 4px;"></span> <?php echo htmlspecialchars($sleeve_color); ?></p>
            <p><strong>Custom Text:</strong> "<?php echo htmlspecialchars($custom_text); ?>"</p>
            <p><strong>Text Style:</strong> <span style="display:inline-block; width:20px; height:20px; background-color: <?php echo htmlspecialchars($text_color); ?>; vertical-align: middle; border: 1px solid #ccc; border-radius: 4px;"></span> <?php echo htmlspecialchars($text_color); ?>, <?php echo $font_size; ?>px, <?php echo htmlspecialchars(explode(',', $font_family)[0]); ?>, <?php echo $text_rotation; ?>&deg;</p>
            <hr style="margin: 1rem 0; border: none; border-top: 1px solid #e2e8f0;">
            <p><strong>Base Price:</strong> ₹<?php echo number_format($base_price, 2); ?></p>
            <p><strong>Custom Printing:</strong> ₹<?php echo number_format($printing_cost, 2); ?></p>
            <p style="font-size: 1.3rem; margin-top: 0.5rem; color: #2c3e50;"><strong>Total Amount:</strong> ₹<?php echo number_format($total_price, 2); ?></p>
        </div>
        
        <div style="margin-top: 2rem;">
            <div class="tshirt-wrapper" style="margin: 0 auto; transform: scale(0.6); transform-origin: top center;" id="tshirt-display">
                <style>
                    #tshirt-display { 
                        --tshirt-color: <?php echo htmlspecialchars($shirt_color); ?>; 
                        --sleeve-color: <?php echo htmlspecialchars($sleeve_color); ?>;
                    }
                    #tshirt-display .tshirt-neck { background: transparent; }
                </style>
                <div class="tshirt-neck"></div>
                <div class="tshirt-sleeve-left"></div>
                <div class="tshirt-sleeve-right"></div>
                <div class="tshirt-body"></div>
                <div class="tshirt-text-layer" id="tshirt-text" style="color: <?php echo htmlspecialchars($text_color); ?>; font-size: <?php echo $font_size; ?>px; font-family: <?php echo htmlspecialchars($font_family); ?>; transform: rotate(<?php echo $text_rotation; ?>deg);">
                    <?php echo htmlspecialchars($custom_text); ?>
                </div>
            </div>
            
        </div>
        
        <br>
        <a href="orders.php" class="back-link" style="margin-right: 2rem;">&larr; Go to Orders</a>
        <a href="index.php" class="back-link">Design Another T-Shirt &rarr;</a>
    </div>
</div>

</body>
</html>
