<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
$message = $_GET['message'] ?? '';
$stmt = $pdo->query('SELECT user_id, first_name, last_name, username, email, password FROM `user` ORDER BY user_id');
$staff = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css"> <!-- Updated to use the style.css inside WebFinalAssignment folder -->
</head>
<body>
<div class="container">
    <header class="topbar">
        <h1>Admin Panel</h1>
        <div>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
            <a class="button" href="logout.php">Logout</a>
        </div>
    </header>

    <?php if ($message): ?>
        <div class="success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="actions">
        <a class="button" href="register.php">Add New Staff</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Password (hash)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($staff as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td class="mono"><?php echo htmlspecialchars($row['password']); ?></td>
                <td>
                    <a class="small-button" href="update_staff.php?user_id=<?php echo urlencode($row['user_id']); ?>">Update</a>
                    <a class="small-button delete" href="delete_staff.php?user_id=<?php echo urlencode($row['user_id']); ?>" onclick="return confirm('Delete this staff record?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
