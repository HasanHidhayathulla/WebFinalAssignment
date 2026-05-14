<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';
$error = '';
$success = '';
$user_id = $_GET['user_id'] ?? '';

if (!$user_id) {
    header('Location: admin.php');
    exit;
}
$stmt = $pdo->prepare('SELECT user_id, first_name, last_name, username, email FROM `user` WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($first_name === '' || $last_name === '' || $username === '' || $email === '') {
        $error = 'All fields except password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif ($password !== '' && strlen($password) <= 8) {
        $error = 'New password must be longer than 8 characters.';
    } else {
        $stmt = $pdo->prepare('SELECT user_id, username, email FROM `user` WHERE (username = :username OR email = :email) AND user_id != :user_id');
        $stmt->execute(['username' => $username, 'email' => $email, 'user_id' => $user_id]);
        $conflicts = $stmt->fetchAll();
        $messages = [];
        foreach ($conflicts as $conflict) {
            if ($conflict['username'] === $username) {
                $messages[] = 'This username is already in use.';
            }
            if ($conflict['email'] === $email) {
                $messages[] = 'This email is already in use.';
            }
        }

        if ($messages) {
            $error = implode(' ', array_unique($messages));
        } else {
            $params = ['first_name' => $first_name, 'last_name' => $last_name, 'username' => $username, 'email' => $email, 'user_id' => $user_id];
            $sql = 'UPDATE `user` SET first_name = :first_name, last_name = :last_name, username = :username, email = :email';
            if ($password !== '') {
                $sql .= ', password = :password';
                $params['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sql .= ' WHERE user_id = :user_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = 'Staff record updated successfully.';
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['username'] = $username;
            $user['email'] = $email;
            if ($user_id === $_SESSION['user_id']) {
                $_SESSION['username'] = $username;
                $_SESSION['first_name'] = $first_name;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Staff</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container">
    <header class="topbar">
        <h1>Update Staff</h1>
        <div>
            <a class="button" href="admin.php">Back to Admin</a>
        </div>
    </header>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" action="update_staff.php?user_id=<?php echo urlencode($user_id); ?>">
        <label>User ID</label>
        <input type="text" value="<?php echo htmlspecialchars($user['user_id']); ?>" disabled>

        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>New Password <span class="hint">(leave blank to keep current password)</span></label>
        <input type="password" name="password">

        <button type="submit">Save Changes</button>
    </form>
</div>
</body>
</html>
