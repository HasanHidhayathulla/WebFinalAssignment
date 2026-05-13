<?php
session_start();
require_once '../db.php';
require_once '../borrow_management/functions.php'; // Include validation functions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $book_id = isset($_POST['book_id']) ? trim($_POST['book_id']) : '';
    $book_name = isset($_POST['book_name']) ? trim($_POST['book_name']) : '';
    $category_id = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
    
    $error = '';
    
    // Regex Validation for Book ID (B001 format)
    if (!validateBookID($book_id)) {
        $error = 'Invalid Book ID format. Must be B followed by 3 digits (e.g., B001)';
    } elseif (empty($book_name)) {
        $error = 'Book name is required';
    } elseif (empty($category_id)) {
        $error = 'Please select a category';
    }
    
    if ($error) {
        $_SESSION['error'] = $error;
        header('Location: Book_form.php');
        exit;
    }
    
    try {
        // Check if book_id already exists
        $check_sql = "SELECT book_id FROM book WHERE book_id = ?";
        $stmt = $pdo->prepare($check_sql);
        $stmt->execute([$book_id]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Book ID already exists. Please use a different ID.';
            header('Location: Book_form.php');
            exit;
        }
        
        // Insert new book
        $sql = "INSERT INTO book (book_id, book_name, category_id) 
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $params = [$book_id, $book_name, $category_id];
        $stmt->execute($params);
        
        $_SESSION['success'] = 'Book registered successfully!';
        header('Location: Book_list.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: Book_form.php');
        exit;
    }
}

// If not POST request, redirect to form
header('Location: Book_form.php');
exit;
?>
