<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (isAdmin()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        redirect('index.php');
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - T-Shirt Customizer</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: var(--bg-color);">
    <div class="card" style="width: 100%; max-width: 400px; text-align: left;">
        <h2 style="text-align: center; margin-bottom: 20px;">Admin Access</h2>
        <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-accent" style="width: 100%; font-size: 1.1rem; padding: 12px; margin-top: 10px;">Login to Dashboard</button>
            <p style="text-align: center; margin-top: 15px;"><a href="../index.php" style="color: var(--accent-color); text-decoration: none;">&larr; Back to Store</a></p>
        </form>
    </div>
</body>
</html>
