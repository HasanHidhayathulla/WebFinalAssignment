<?php
session_start();
require_once '../db.php';
include '../sessioncheck.php';

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
    // Fetch all borrow transactions with book and member information
    $sql = "SELECT bb.borrow_id, bb.book_id, b.book_name, bb.member_id, 
                   CONCAT(m.first_name, ' ', m.last_name) as member_name, 
                   bb.borrow_status, bb.borrower_date_modified
            FROM bookborrower bb
            JOIN book b ON bb.book_id = b.book_id
            JOIN member m ON bb.member_id = m.member_id
            ORDER BY bb.borrow_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $transactions = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
    $transactions = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrow Transactions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #008CBA;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #007399;
        }
        .btn-warning {
            background-color: #ff9800;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-warning:hover {
            background-color: #e68900;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-danger:hover {
            background-color: #da190b;
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
        thead {
            background-color: #4CAF50;
            color: white;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        tbody tr:hover {
            background-color: #f5f5f5;
        }
        .actions {
            display: flex;
            gap: 8px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-borrowed {
            background-color: #ff9800;
            color: white;
        }
        .status-available {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <?php include_once '../navigation.php'; ?>
    <div class="container">
        <h1>Book Borrow Transactions</h1>

        <div class="header-actions">
            <a href="borrow_form.php" class="btn btn-primary">+ Create New Transaction</a>
            <a href="../index.php" class="btn btn-secondary">Back to Home</a>
        </div>

        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($transactions)): ?>
            <div class="no-data">
                <p>No borrow transactions found. <a href="borrow_form.php">Create one now</a></p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Borrow ID</th>
                        <th>Book ID</th>
                        <th>Book Name</th>
                        <th>Member ID</th>
                        <th>Member Name</th>
                        <th>Status</th>
                        <th>Date Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['borrow_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['book_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['book_name']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['member_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['member_name']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($transaction['borrow_status']); ?>">
                                    <?php echo ucfirst($transaction['borrow_status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($transaction['borrower_date_modified']); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="update_borrow.php?borrow_id=<?php echo urlencode($transaction['borrow_id']); ?>" class="btn btn-warning">Edit</a>
                                    <a href="delete_borrow.php?borrow_id=<?php echo urlencode($transaction['borrow_id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this transaction?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
