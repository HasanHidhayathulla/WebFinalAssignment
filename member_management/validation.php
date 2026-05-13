<?php
/**
 * Validation functions for Library Member Registration
 */

/**
 * Validate Member ID format (M### - letter M followed by 3 digits)
 * @param string $member_id
 * @return array with 'valid' (bool) and 'error' (string)
 */
function validateMemberId($member_id) {
    $member_id = trim($member_id);
    
    if (empty($member_id)) {
        return ['valid' => false, 'error' => 'Member ID is required'];
    }
    
    // Check format: M followed by exactly 3 digits
    if (!preg_match('/^M\d{3}$/', $member_id)) {
        return ['valid' => false, 'error' => 'Member ID must be in format M### (e.g., M001)'];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Validate email format
 * @param string $email
 * @return array with 'valid' (bool) and 'error' (string)
 */
function validateEmail($email) {
    $email = trim($email);
    
    if (empty($email)) {
        return ['valid' => false, 'error' => 'Email is required'];
    }
    
    // Standard email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'error' => 'Invalid email format (e.g., example@mail.com)'];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Validate name (first or last name)
 * @param string $name
 * @param string $field_name
 * @return array with 'valid' (bool) and 'error' (string)
 */
function validateName($name, $field_name = 'Name') {
    $name = trim($name);
    
    if (empty($name)) {
        return ['valid' => false, 'error' => "$field_name is required"];
    }
    
    // Allow letters, spaces, hyphens, and apostrophes
    if (!preg_match('/^[a-zA-Z\s\-\']+$/', $name)) {
        return ['valid' => false, 'error' => "$field_name can only contain letters, spaces, hyphens, and apostrophes"];
    }
    
    if (strlen($name) > 100) {
        return ['valid' => false, 'error' => "$field_name cannot exceed 100 characters"];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Validate birthday (format: DD/MM/YYYY or YYYY-MM-DD)
 * @param string $birthday
 * @return array with 'valid' (bool) and 'error' (string)
 */
function validateBirthday($birthday) {
    $birthday = trim($birthday);
    
    if (empty($birthday)) {
        return ['valid' => false, 'error' => 'Birthday is required'];
    }
    
    // Try to parse date in DD/MM/YYYY or YYYY-MM-DD format
    $parsed = false;
    $date = null;
    
    // Try YYYY-MM-DD format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthday)) {
        $date = DateTime::createFromFormat('Y-m-d', $birthday);
        $parsed = $date && $date->format('Y-m-d') === $birthday;
    }
    // Try DD/MM/YYYY format
    elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $birthday)) {
        $date = DateTime::createFromFormat('d/m/Y', $birthday);
        $parsed = $date && $date->format('d/m/Y') === $birthday;
    }
    
    if (!$parsed) {
        return ['valid' => false, 'error' => 'Invalid birthday format. Use DD/MM/YYYY or YYYY-MM-DD'];
    }
    
    // Check if birthday is in the past
    $today = new DateTime();
    if ($date >= $today) {
        return ['valid' => false, 'error' => 'Birthday must be in the past'];
    }
    
    return ['valid' => true, 'error' => ''];
}

/**
 * Check if Member ID already exists in database
 * @param PDO $pdo
 * @param string $member_id
 * @param string $exclude_id (optional - for update operations)
 * @return array with 'exists' (bool) and 'error' (string)
 */
function checkMemberIdExists($pdo, $member_id, $exclude_id = null) {
    $member_id = trim($member_id);
    
    $sql = "SELECT member_id FROM member WHERE member_id = :member_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':member_id' => $member_id]);
    $exists = $stmt->rowCount() > 0;
    
    // If exclude_id is provided, allow if it's the same ID (update case)
    if ($exists && $exclude_id && $exclude_id === $member_id) {
        return ['exists' => false, 'error' => ''];
    }
    
    if ($exists) {
        return ['exists' => true, 'error' => 'Member ID already exists'];
    }
    
    return ['exists' => false, 'error' => ''];
}

/**
 * Validate all member fields
 * @param array $data
 * @param PDO $pdo
 * @param string $mode ('create' or 'update')
 * @param string $exclude_member_id (for update - the original member ID)
 * @return array with 'valid' (bool) and 'errors' (array of field errors)
 */
function validateMemberData($data, $pdo, $mode = 'create', $exclude_member_id = null) {
    $errors = [];
    
    // Validate Member ID
    $member_id_validation = validateMemberId($data['member_id'] ?? '');
    if (!$member_id_validation['valid']) {
        $errors['member_id'] = $member_id_validation['error'];
    } else {
        // Check for duplicates only in create mode or if ID changed in update mode
        $check_duplicate = ($mode === 'create') || ($exclude_member_id !== $data['member_id']);
        if ($check_duplicate) {
            $duplicate_check = checkMemberIdExists($pdo, $data['member_id'], $exclude_member_id);
            if ($duplicate_check['exists']) {
                $errors['member_id'] = $duplicate_check['error'];
            }
        }
    }
    
    // Validate First Name
    $first_name_validation = validateName($data['first_name'] ?? '', 'First Name');
    if (!$first_name_validation['valid']) {
        $errors['first_name'] = $first_name_validation['error'];
    }
    
    // Validate Last Name
    $last_name_validation = validateName($data['last_name'] ?? '', 'Last Name');
    if (!$last_name_validation['valid']) {
        $errors['last_name'] = $last_name_validation['error'];
    }
    
    // Validate Email
    $email_validation = validateEmail($data['email'] ?? '');
    if (!$email_validation['valid']) {
        $errors['email'] = $email_validation['error'];
    }
    
    // Validate Birthday
    $birthday_validation = validateBirthday($data['birthday'] ?? '');
    if (!$birthday_validation['valid']) {
        $errors['birthday'] = $birthday_validation['error'];
    }
    
    $is_valid = count($errors) === 0;
    
    return [
        'valid' => $is_valid,
        'errors' => $errors
    ];
}
?>
