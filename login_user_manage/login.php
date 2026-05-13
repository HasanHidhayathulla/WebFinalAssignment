<?php
session_start();
require_once '../db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']  );
    $password = trim($_POST['password']  );

    if ($username === '' || $password === '') {
        $error = 'Enter both username and password.';
    } else {
        $sql = 'SELECT * FROM `user` WHERE username = :username LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            $passwordVerified = password_verify($password, $user['password']);
            if (!$passwordVerified && hash_equals($user['password'], $password)) {
                $passwordVerified = true;
                $stmt = $pdo->prepare('UPDATE `user` SET password = :password WHERE user_id = :user_id');
                $stmt->execute([
                    'password' => password_hash($password,),
                    'user_id' => $user['user_id'],
                ]);
            }

            if ($passwordVerified) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                header('Location: ../index.php');
                exit;
            }
        }
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container">
    <h1>Staff Login</h1>
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register now</a></p>
</div>
</body>
</html>
