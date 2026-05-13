<?php
session_start();
require_once 'db.php';

$book_id = isset($_GET['book_id']) ? trim($_GET['book_id']) : '';

if (empty($book_id)) {
    $_SESSION['error'] = 'Invalid Book ID!';
    header('Location: Book_list.php');
    exit;
}

try {
    // Verify the record exists
    $sql = "SELECT book_id FROM book WHERE book_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$book_id]);
    
    if (!$stmt->fetch()) {
        $_SESSION['error'] = 'Book not found!';
        header('Location: Book_list.php');
        exit;
    }
    
    // Delete the book
    $sql = "DELETE FROM book WHERE book_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$book_id]);
    
    $_SESSION['success'] = 'Book deleted successfully!';
    header('Location: Book_list.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: Book_list.php');
    exit;
}
?>
