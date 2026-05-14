<?php
include_once '../db.php'; // Include database connection file


$fine_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($fine_id)) {
    header('Location: fines.php');
    exit;
}

try {
    // Verify the record exists
    $stmt = $pdo->prepare("SELECT fine_id FROM fine WHERE fine_id = ?");
    $stmt->execute([$fine_id]);
    
    if (!$stmt->fetch()) {
        echo "<script>alert('Fine record not found!');</script>";
        header('Location: fines.php');
        exit;
    }
    
    // Delete the fine record
    $delete_stmt = $pdo->prepare("DELETE FROM fine WHERE fine_id = ?");
    $delete_stmt->execute([$fine_id]);
    
    echo "<script>alert('Fine deleted successfully!');</script>";
    header('Location: fines.php');
    exit;
    
} catch (PDOException $e) {
    echo "<script>alert('Database error: " . htmlspecialchars($e->getMessage()) . "');</script>";
    header('Location: fines.php');
    exit;
}
?>
