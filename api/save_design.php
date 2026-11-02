<?php
require_once '../config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['design_data']) && isset($input['preview'])) {
    $user_id = $_SESSION['user_id'];
    $design_data = $input['design_data'];
    $preview = $input['preview'];

    try {
        $stmt = $pdo->prepare("INSERT INTO designs (user_id, design_data, preview_image) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $design_data, $preview])) {
            echo json_encode(['success' => true, 'design_id' => $pdo->lastInsertId()]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database execution failed']);
        }
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data payload']);
}
