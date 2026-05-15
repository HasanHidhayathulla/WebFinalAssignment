<?php
session_start();
require_once '../db.php';

$borrow_id = isset($_GET['borrow_id']) ? trim($_GET['borrow_id']) : '';

if (empty($borrow_id)) {
    $_SESSION['error'] = 'Invalid Borrow ID!';
    header('Location: borrowlist.php');
    exit;
}

try {
    // Verify the record exists
    $sql = "SELECT borrow_id FROM bookborrower WHERE borrow_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$borrow_id]);
    
    if (!$stmt->fetch()) {
        $_SESSION['error'] = 'Transaction not found!';
        header('Location: borrowlist.php');
        exit;
    }
    
    // Delete the transaction
    $sql = "DELETE FROM bookborrower WHERE borrow_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$borrow_id]);
    
    $_SESSION['success'] = 'Transaction deleted successfully!';
    header('Location: borrowlist.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: borrowlist.php');
    exit;
}
?>
