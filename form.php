<?php
session_start();
require_once 'config.php';

$message = '';
$error = '';

// Get message from URL parameter
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

// Get error from session or URL
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

// Get books and members lists for dropdowns
$books = getBooks($pdo);
$members = getMembers($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrow Transaction Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        .nav-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        .nav-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book Borrow Transaction Form</h1>

        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="create.php">
            <div class="form-group">
                <label for="borrow_id">Borrow ID:</label>
                <input type="text" id="borrow_id" name="borrow_id" placeholder="e.g., BR001" required>
                <div class="info">Format: BR followed by 3 digits (BR001-BR999)</div>
            </div>

            <div class="form-group">
                <label for="book_id">Book ID:</label>
                <select id="book_id" name="book_id" required>
                    <option value="">-- Select a Book --</option>
                    <?php foreach ($books as $book): ?>
                        <option value="<?php echo $book['book_id']; ?>">
                            <?php echo $book['book_id'] . ' - ' . $book['book_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="info">Format: B001-B999</div>
            </div>

            <div class="form-group">
                <label for="member_id">Member ID:</label>
                <select id="member_id" name="member_id" required>
                    <option value="">-- Select a Member --</option>
                    <?php foreach ($members as $member): ?>
                        <option value="<?php echo $member['member_id']; ?>">
                            <?php echo $member['member_id'] . ' - ' . $member['member_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="info">Format: M001-M999</div>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="">-- Select Status --</option>
                    <option value="borrowed">Borrowed</option>
                    <option value="available">Available</option>
                </select>
            </div>

            <button type="submit">Create Transaction</button>
        </form>

        <a href="list.php" class="nav-link">View All Transactions</a>
    </div>
</body>
</html>
