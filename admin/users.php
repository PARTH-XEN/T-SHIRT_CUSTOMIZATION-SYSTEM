<?php
require_once 'header.php';

// Admin cannot delete itself
if (isset($_GET['delete']) && $_GET['delete'] != $_SESSION['user_id']) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    redirect('users.php');
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<h2>Manage Users</h2>

<div style="overflow-x: auto; margin-top: 20px;">
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registered</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo escape($u['name']); ?></td>
            <td><?php echo escape($u['email']); ?></td>
            <td><?php echo escape(ucfirst($u['role'])); ?></td>
            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
            <td>
                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <a href="users.php?delete=<?php echo $u['id']; ?>" class="btn" style="background-color: #d9534f; color: white; padding: 5px 10px;" onclick="return confirm('Delete user and all related data?')">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once 'footer.php'; ?>
