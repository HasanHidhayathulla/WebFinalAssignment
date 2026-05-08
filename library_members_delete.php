<?php
// Handle member deletion
session_start();

include 'library_db_config.php';

$error = '';
$message = '';

// Only process DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_member_id'])) {
    $member_id = trim($_POST['delete_member_id']);
    
    // Validate Member ID format
    if (!preg_match('/^M\d{3}$/', $member_id)) {
        $error = "Invalid Member ID format";
    } else {
        // Delete the member
        $sql = "DELETE FROM member WHERE member_id = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param('s', $member_id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $message = "Member deleted successfully!";
                } else {
                    $error = "Member not found";
                }
            } else {
                $error = "Error deleting member: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Redirect back to main page with message
if ($message) {
    $_SESSION['success'] = $message;
    header('Location: library_members.php?msg=success');
    exit;
} elseif ($error) {
    $_SESSION['error'] = $error;
    header('Location: library_members.php?msg=error');
    exit;
} else {
    header('Location: library_members.php');
    exit;
}
?>
