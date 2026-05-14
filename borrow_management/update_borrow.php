<?php
session_start();
require_once '../db.php';
require_once "functions.php";
include '../sessioncheck.php';



$borrow_id = isset($_GET['borrow_id']) ? trim($_GET['borrow_id']) : '';
$borrow_transaction = null;
$error = '';

if (empty($borrow_id)) {
    header('Location: borrowlist.php');
    exit;
}

try {
    // Fetch the borrow transaction to edit
    $sql = "SELECT * FROM bookborrower WHERE borrow_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$borrow_id]);
    $borrow_transaction = $stmt->fetch();
    
    if (!$borrow_transaction) {
        $_SESSION['error'] = 'Transaction not found!';
        header('Location: borrowlist.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: borrowlist.php');
    exit;
}

// Get books and members for dropdowns
$books = getBooks($pdo);
$members = getMembers($pdo);

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_book_id = isset($_POST['book_id']) ? trim($_POST['book_id']) : '';
    $new_member_id = isset($_POST['member_id']) ? trim($_POST['member_id']) : '';
    $new_status = isset($_POST['status']) ? trim($_POST['status']) : '';
    
    $error = '';
    
    // Validate new values
    if (!validateBookID($new_book_id)) {
        $error = 'Invalid Book ID format. Must be B followed by 3 digits (e.g., B001)';
    } elseif (!validateMemberID($new_member_id)) {
        $error = 'Invalid Member ID format. Must be M followed by 3 digits (e.g., M001)';
    } elseif (empty($new_status)) {
        $error = 'Please select a status';
    }
    
    if ($error) {
        // Error will be displayed in the form
    } else {
        try {
            // Update the transaction
            $date_modified = date('Y-m-d H:i:s');
            $sql = "UPDATE bookborrower 
                    SET book_id = ?, member_id = ?, borrow_status = ?, borrower_date_modified = ?
                    WHERE borrow_id = ?";
            $stmt = $pdo->prepare($sql);
            $params = [$new_book_id, $new_member_id, $new_status, $date_modified, $borrow_id];
            $stmt->execute($params);
            
            $_SESSION['success'] = 'Transaction updated successfully!';
            header('Location: borrowlist.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    // Update the borrow transaction for display if there was an error
    $borrow_transaction['book_id'] = $new_book_id;
    $borrow_transaction['member_id'] = $new_member_id;
    $borrow_transaction['borrow_status'] = $new_status;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Borrow Transaction</title>
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
        input[readonly] {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        button, a {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        button {
            background-color: #4CAF50;
            color: white;
        }
        button:hover {
            background-color: #45a049;
        }
        .cancel-btn {
            background-color: #008CBA;
            color: white;
        }
        .cancel-btn:hover {
            background-color: #007399;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .borrow-id {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }
        .info {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Borrow Transaction</h1>

        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="borrow_id">Borrow ID (Read-only):</label>
                <input type="text" id="borrow_id" name="borrow_id" value="<?php echo htmlspecialchars($borrow_transaction['borrow_id']); ?>" readonly class="borrow-id">
            </div>

            <div class="form-group">
                <label for="book_id">Book ID:</label>
                <select id="book_id" name="book_id" required>
                    <option value="">-- Select a Book --</option>
                    <?php foreach ($books as $book): ?>
                        <option value="<?php echo $book['book_id']; ?>" 
                                <?php echo ($book['book_id'] === $borrow_transaction['book_id']) ? 'selected' : ''; ?>>
                            <?php echo $book['book_id'] . ' - ' . $book['book_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="info">Current: <?php echo htmlspecialchars($borrow_transaction['book_id']); ?></div>
            </div>

            <div class="form-group">
                <label for="member_id">Member ID:</label>
                <select id="member_id" name="member_id" required>
                    <option value="">-- Select a Member --</option>
                    <?php foreach ($members as $member): ?>
                        <option value="<?php echo $member['member_id']; ?>" 
                                <?php echo ($member['member_id'] === $borrow_transaction['member_id']) ? 'selected' : ''; ?>>
                            <?php echo $member['member_id'] . ' - ' . $member['member_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="info">Current: <?php echo htmlspecialchars($borrow_transaction['member_id']); ?></div>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="">-- Select Status --</option>
                    <option value="borrowed" <?php echo ($borrow_transaction['borrow_status'] === 'borrowed') ? 'selected' : ''; ?>>Borrowed</option>
                    <option value="available" <?php echo ($borrow_transaction['borrow_status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                </select>
                <div class="info">Current: <?php echo ucfirst($borrow_transaction['borrow_status']); ?></div>
            </div>

            <div class="button-group">
                <button type="submit">Update Transaction</button>
                <a href="borrowlist.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
