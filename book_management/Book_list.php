<?php
session_start();
require_once '../borrow_management/db.php';

$error = '';
$success = '';

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

try {
    // Fetch all books with category information
    $sql = "SELECT b.book_id, b.book_name, b.category_id, bc.category_Name
            FROM book b
            JOIN bookcategory bc ON b.category_id = bc.category_id
            ORDER BY b.book_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $books = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
    $books = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Registry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .add-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .add-btn:hover {
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            display: inline-block;
        }
        .edit-btn {
            background-color: #2196F3;
            color: white;
        }
        .edit-btn:hover {
            background-color: #0b7dda;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        .delete-btn:hover {
            background-color: #da190b;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .record-count {
            color: #666;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book Registry</h1>
        
        <div class="header-info">
            <span class="record-count">Total Books: <?php echo count($books); ?></span>
            <a href="Book_form.php" class="add-btn">+ Register New Book</a>
        </div>
        
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (count($books) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Book Name</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                            <td><?php echo htmlspecialchars($book['book_name']); ?></td>
                            <td><?php echo htmlspecialchars($book['category_Name']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="Book_form.php?book_id=<?php echo urlencode($book['book_id']); ?>" class="edit-btn">Edit</a>
                                    <a href="delete_book.php?book_id=<?php echo urlencode($book['book_id']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>No books registered yet. <a href="Book_form.php">Register a new book</a></p>
            </div>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="../borrow_management/Book_index.php">Back to Main</a>
        </div>
    </div>
</body>
</html>
