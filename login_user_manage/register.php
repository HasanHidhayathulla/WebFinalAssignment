<?php
session_start();

require_once '../db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($first_name === '' || $last_name === '' || $username === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Minimum 8 characters required for the password.';
    } else {
        // Check for duplicate username or email
        $stmt = $pdo->prepare('SELECT user_id, username, email FROM `user` WHERE username = :username OR email = :email');
        $stmt->execute(['username' => $username, 'email' => $email]);
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
            try {
                // Generate new user_id (U + 3 digits)
                $stmt = $pdo->query('SELECT MAX(CAST(SUBSTRING(user_id, 2) AS UNSIGNED)) as max_id FROM `user`');
                $row = $stmt->fetch();
                $next_id = intval($row['max_id'] ?? 0) + 1;
                $new_user_id = 'U' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

                // Insert new staff record
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO `user` (user_id, email, first_name, last_name, username, password) VALUES (:user_id, :email, :first_name, :last_name, :username, :password)');
                $stmt->execute([
                    ':user_id' => $new_user_id,
                    ':email' => $email,
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':username' => $username,
                    ':password' => $hashed_password
                ]);
                
                if (!$_SESSION)
                header('Location: login.php?message=' . urlencode('New staff member ' . htmlspecialchars($username) . ' created successfully!'));
                exit;
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register New Staff</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container">
    <header class="topbar">
        <h1>Register New Staff</h1>
        <div>
            
            <a class="button" href="admin.php">Back to Admin</a>
        </div>
    </header>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="register.php">
        <label>First Name:</label>
        <input type="text" name="first_name" required><br><br>

        <label>Last Name:</label>
        <input type="text" name="last_name" required><br><br>

        <label>Username:</label>
        <input type="text" name="username" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Password (minimum 8 characters):</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Create Staff Account</button>
    </form>
    <a  href="login.php">Already have an account? Login here</a>
</div>
</body>
</html>
