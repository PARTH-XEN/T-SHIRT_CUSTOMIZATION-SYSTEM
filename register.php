<?php
require_once 'includes/header.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = 'Email is already registered.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = 'Registration successful! <a href="login.php">Login here</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>

<h2 style="text-align: center; margin-bottom: 20px;">Register</h2>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

<div class="card" style="max-width: 400px; margin: 0 auto;">
<form method="POST" action="register.php">
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-accent" style="width: 100%;">Register</button>
    <p style="margin-top: 15px;">Already have an account? <a href="login.php">Login</a></p>
</form>
</div>

<?php require_once 'includes/footer.php'; ?>
