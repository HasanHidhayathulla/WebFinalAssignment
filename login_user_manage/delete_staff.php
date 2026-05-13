<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';
$user_id = $_GET['user_id'] ?? '';
if ($user_id) {
    $stmt = $pdo->prepare('DELETE FROM `user` WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
}
header('Location: admin.php?message=' . urlencode('Staff record deleted successfully.'));
exit;
