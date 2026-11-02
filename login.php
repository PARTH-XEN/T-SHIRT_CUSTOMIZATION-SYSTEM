<?php
require_once 'includes/header.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            if ($user['role'] === 'admin') {
                redirect('admin/index.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<h2 style="text-align: center; margin-bottom: 20px;">Login</h2>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<div class="card" style="max-width: 400px; margin: 0 auto;">
<form method="POST" action="login.php">
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-accent" style="width: 100%;">Login</button>
    <p style="margin-top: 15px;">Don't have an account? <a href="register.php">Register</a></p>
</form>
</div>

<?php require_once 'includes/footer.php'; ?>
