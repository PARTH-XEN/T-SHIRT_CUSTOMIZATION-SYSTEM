<?php
require 'config.php';
try {
    $pdo->exec("UPDATE products SET image = REPLACE(image, '.png', '.svg')");
    echo "Database products updated locally.\n";
} catch (Exception $e) {
    echo "Failed to update DB: " . $e->getMessage() . "\n";
}
