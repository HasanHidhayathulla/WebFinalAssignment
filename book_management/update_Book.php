<?php
session_start();
require_once '../db.php';
include '../sessioncheck.php';


$book_id = isset($_POST['book_id']) ? trim($_POST['book_id']) : '';
$book_name = isset($_POST['book_name']) ? trim($_POST['book_name']) : '';
$category_id = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';

if (empty($book_id)) {
    $_SESSION['error'] = 'Invalid Book ID!';
    header('Location: Book_list.php');
    exit;
}

$error = '';

// Validate new values
if (empty($book_name)) {
    $error = 'Book name is required';
} elseif (empty($category_id)) {
    $error = 'Please select a category';
}

if ($error) {
    $_SESSION['error'] = $error;
    header('Location: Book_form.php?book_id=' . urlencode($book_id));
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
    
    // Update the book
    $sql = "UPDATE book 
            SET book_name = ?, category_id = ?
            WHERE book_id = ?";
    $stmt = $pdo->prepare($sql);
    $params = [$book_name, $category_id, $book_id];
    $stmt->execute($params);
    
    $_SESSION['success'] = 'Book updated successfully!';
    header('Location: Book_list.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: Book_form.php?book_id=' . urlencode($book_id));
    exit;
}
?>
