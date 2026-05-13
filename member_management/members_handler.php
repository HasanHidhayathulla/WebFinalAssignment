<?php
// Handle form submissions for creating and updating members
session_start();

require_once '../db.php';
include 'validation.php';

include '../sessioncheck.php';


$message = '';
$error = '';
$edit_member_id = null;

// Handle POST request (Create or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = trim($_POST['member_id'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $birthday = trim($_POST['birthday'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $is_edit = !empty($_POST['is_edit']);
    $original_member_id = $_POST['original_member_id'] ?? null;
    
    // Prepare data for validation
    $data = [
        'member_id' => $member_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'birthday' => $birthday,
        'email' => $email
    ];
    
    // Validate all fields
    $validation = validateMemberData(
        $data,
        $pdo,
        $is_edit ? 'update' : 'create',
        $original_member_id
    );
    
    if ($validation['valid']) {
        try {
            if ($is_edit) {
                // Update existing member
                $sql = 'UPDATE member SET first_name = :first_name, last_name = :last_name, birthday = :birthday, email = :email WHERE member_id = :member_id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':birthday' => $birthday,
                    ':email' => $email,
                    ':member_id' => $original_member_id
                ]);
                $message = "Member updated successfully!";
            } else {
                // Create new member
                $sql = 'INSERT INTO member (member_id, first_name, last_name, birthday, email) VALUES (:member_id, :first_name, :last_name, :birthday, :email)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':member_id' => $member_id,
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':birthday' => $birthday,
                    ':email' => $email
                ]);
                $message = "Member created successfully!";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        // Validation errors
        $error = "Please fix the following errors:\n";
        foreach ($validation['errors'] as $field => $error_msg) {
            $error .= "- $error_msg\n";
        }
        // Store validation errors in session for display
        $_SESSION['validation_errors'] = $validation['errors'];
        $_SESSION['form_data'] = $data;
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
}
?>
