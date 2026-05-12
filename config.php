<?php
// Database Configuration
$host = 'localhost';
$db = 'library_test';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Validation Functions with Regex
function validateBorrowID($borrow_id) {
    return preg_match('/^BR[0-9]{3}$/', $borrow_id);
}

function validateBookID($book_id) {
    return preg_match('/^B[0-9]{3}$/', $book_id);
}

function validateMemberID($member_id) {
    return preg_match('/^M[0-9]{3}$/', $member_id);
}

// Function to get all books
function getBooks($pdo) {
    $sql = "SELECT book_id, book_name FROM book ORDER BY book_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get all members
function getMembers($pdo) {
    $sql = "SELECT member_id, CONCAT(first_name, ' ', last_name) as member_name FROM member ORDER BY member_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get book name by ID
function getBookName($pdo, $book_id) {
    $sql = "SELECT book_name FROM book WHERE book_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$book_id]);
    $row = $stmt->fetch();
    return $row ? $row['book_name'] : 'Unknown';
}

// Function to get member name by ID
function getMemberName($pdo, $member_id) {
    $sql = "SELECT CONCAT(first_name, ' ', last_name) as member_name FROM member WHERE member_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id]);
    $row = $stmt->fetch();
    return $row ? $row['member_name'] : 'Unknown';
}
?>
