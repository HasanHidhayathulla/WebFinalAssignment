<?php
session_start();
require_once '../db.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');

    if (!preg_match('/^U\d{3}$/', $user_id)) {
        $error = 'User ID must be in the U001 format.';
    } elseif ($first_name === '' || $last_name === '' || $username === '' || $email === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) <= 8) {
        $error = 'Password must be longer than 8 characters.';
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM `user` WHERE user_id = :user_id OR username = :username OR email = :email');
        $stmt->execute(['user_id' => $user_id, 'username' => $username, 'email' => $email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $stmt = $pdo->prepare('SELECT user_id, username, email FROM `user` WHERE user_id = :user_id OR username = :username OR email = :email');
            $stmt->execute(['user_id' => $user_id, 'username' => $username, 'email' => $email]);
            $conflicts = $stmt->fetchAll();
            $messages = [];
            foreach ($conflicts as $conflict) {
                if ($conflict['user_id'] === $user_id) {
                    $messages[] = 'This User ID is already in use.';
                }
                if ($conflict['username'] === $username) {
                    $messages[] = 'This username is already in use.';
                }
                if ($conflict['email'] === $email) {
                    $messages[] = 'This email is already in use.';
                }
            }
            $error = implode(' ', array_unique($messages));
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO `user` (user_id, email, first_name, last_name, username, password) VALUES (:user_id, :email, :first_name, :last_name, :username, :password)');
            $stmt->execute([
                'user_id' => $user_id,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'username' => $username,
                'password' => $hashedPassword,
            ]);
            $success = 'Registration successful. You can now <a href="login.php">login</a>.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Staff Registration</h1>
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="post" action="register.php">
        <label>User ID</label>
        <input type="text" name="user_id" value="<?php echo htmlspecialchars($_POST['user_id'] ?? ''); ?>" placeholder="U001" required>

        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

        <button type="submit">Register</button>
    </form>
    <p>Already registered? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
