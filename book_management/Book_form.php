<?php
session_start();
require_once 'Book_functions.php';

require_once '../db.php';

$message = '';
$error = '';
$edit_mode = false;
$book = null;

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

// Check if we're editing
if (isset($_GET['book_id'])) {
    $book_id = trim($_GET['book_id']);
    
    try {
        $sql = "SELECT * FROM book WHERE book_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();
        
        if (!$book) {
            $_SESSION['error'] = 'Book not found!';
            header('Location: Book_list.php');
            exit;
        }
        $edit_mode = true;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: Book_list.php');
        exit;
    }
}

// Get categories for dropdown
$categories = getCategories($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit Book' : 'Register New Book'; ?></title>
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
        .form-note {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $edit_mode ? 'Edit Book' : 'Register New Book'; ?></h1>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo $edit_mode ? 'update_Book.php' : 'create_book.php'; ?>">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="book_id">Book ID:</label>
                <input 
                    type="text" 
                    id="book_id" 
                    name="book_id" 
                    <?php echo $edit_mode ? 'readonly' : 'required'; ?>
                    placeholder="e.g., B001"
                    value="<?php echo $edit_mode ? htmlspecialchars($book['book_id']) : (isset($_POST['book_id']) ? htmlspecialchars($_POST['book_id']) : ''); ?>"
                >
                <div class="form-note">Format: B followed by 3 digits (e.g., B001, B002)</div>
            </div>
            
            <div class="form-group">
                <label for="book_name">Book Name:</label>
                <input 
                    type="text" 
                    id="book_name" 
                    name="book_name" 
                    required
                    placeholder="Enter book name"
                    value="<?php echo $edit_mode ? htmlspecialchars($book['book_name']) : (isset($_POST['book_name']) ? htmlspecialchars($_POST['book_name']) : ''); ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option 
                            value="<?php echo htmlspecialchars($category['category_id']); ?>"
                            <?php echo ($edit_mode && $book['category_id'] === $category['category_id']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($category['category_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit"><?php echo $edit_mode ? 'update_Book Book' : 'Register Book'; ?></button>
        </form>
        
        <div class="back-link">
            <a href="Book_list.php">Back to Book List</a>
        </div>
    </div>
</body>
</html>
