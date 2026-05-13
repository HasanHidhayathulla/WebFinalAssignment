<?php
// Handle member deletion
session_start();

require_once 'db.php';

$error = '';
$message = '';

// Only process DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_member_id'])) {
    $member_id = trim($_POST['delete_member_id']);
    
    // Validate Member ID format
    if (!preg_match('/^M\d{3}$/', $member_id)) {
        $error = "Invalid Member ID format";
    } else {
        try {
            // Delete the member
            $sql = 'DELETE FROM member WHERE member_id = :member_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':member_id' => $member_id]);
            
            if ($stmt->rowCount() > 0) {
                $message = "Member deleted successfully!";
            } else {
                $error = "Member not found";
            }
        } catch (PDOException $e) {
            $error = "Error deleting member: " . $e->getMessage();
        }
    }
}

// Redirect back to main page with message
if ($message) {
    $_SESSION['success'] = $message;
    header('Location: members.php?msg=success');
    exit;
} elseif ($error) {
    $_SESSION['error'] = $error;
    header('Location: members.php?msg=error');
    exit;
} else {
    header('Location: members.php');
    exit;
}
?>
