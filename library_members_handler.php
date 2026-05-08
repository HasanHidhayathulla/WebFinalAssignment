<?php
// Handle form submissions for creating and updating members
session_start();

include 'library_db_config.php';
include 'validation.php';

$message = '';
$error = '';
$edit_member_id = null;

// Handle POST request (Create or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $email = $_POST['email'] ?? '';
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
        $conn,
        $is_edit ? 'update' : 'create',
        $original_member_id
    );
    
    if ($validation['valid']) {
        if ($is_edit) {
            // Update existing member
            $sql = "UPDATE member SET 
                      first_name = ?,
                      last_name = ?,
                      birthday = ?,
                      email = ?
                      WHERE member_id = ?";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param('sssss', $first_name, $last_name, $birthday, $email, $original_member_id);
                
                if ($stmt->execute()) {
                    $message = "Member updated successfully!";
                } else {
                    $error = "Error updating member: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            // Create new member
            $sql = "INSERT INTO member (member_id, first_name, last_name, birthday, email) 
                      VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param('sssss', $member_id, $first_name, $last_name, $birthday, $email);
                
                if ($stmt->execute()) {
                    $message = "Member created successfully!";
                } else {
                    $error = "Error creating member: " . $stmt->error;
                }
                $stmt->close();
            }
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
    header('Location: library_members.php?msg=success');
    exit;
} elseif ($error) {
    $_SESSION['error'] = $error;
    header('Location: library_members.php?msg=error');
    exit;
}
?>
