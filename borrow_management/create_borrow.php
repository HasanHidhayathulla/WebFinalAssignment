<?php
session_start();
require_once '../db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $borrow_id = isset($_POST['borrow_id']) ? trim($_POST['borrow_id']) : '';
    $book_id = isset($_POST['book_id']) ? trim($_POST['book_id']) : '';
    $member_id = isset($_POST['member_id']) ? trim($_POST['member_id']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    
    $error = '';
    
    // Triple Regex Validation
    if (!validateBorrowID($borrow_id)) {
        $error = 'Invalid Borrow ID format. Must be BR followed by 3 digits (e.g., BR001)';
    } elseif (!validateBookID($book_id)) {
        $error = 'Invalid Book ID format. Must be B followed by 3 digits (e.g., B001)';
    } elseif (!validateMemberID($member_id)) {
        $error = 'Invalid Member ID format. Must be M followed by 3 digits (e.g., M001)';
    } elseif (empty($status)) {
        $error = 'Please select a status';
    }
    
    if ($error) {
        $_SESSION['error'] = $error;
        header('Location: borrow_form.php');
        exit;
    }
    
    // Get current timestamp
    $date_modified = date('Y-m-d H:i:s');
    
    try {
        // Check if borrow_id already exists
        $check_sql = "SELECT borrow_id FROM bookborrower WHERE borrow_id = ?";
        $stmt = $pdo->prepare($check_sql);
        $stmt->execute([$borrow_id]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Borrow ID already exists. Please use a different ID.';
            header('Location: borrow_form.php');
            exit;
        }
        
        // Insert new borrow transaction
        $sql = "INSERT INTO bookborrower (borrow_id, book_id, member_id, borrow_status, borrower_date_modified) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $params = [$borrow_id, $book_id, $member_id, $status, $date_modified];
        $stmt->execute($params);
        
        // Redirect with success message
        header('Location: borrow_form.php?message=Transaction%20created%20successfully!');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        header('Location: borrow_form.php');
        exit;
    }
} else {
    header('Location: borrow_form.php');
    exit;
}
?>
