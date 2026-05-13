

<?php
/**
 * Validate Borrow ID format (BR### - BR followed by 3 digits)
 * @param string $borrow_id
 * @return bool True if valid, false otherwise
 */
function validateBorrowID($borrow_id) {
    return preg_match('/^BR\d{3}$/', $borrow_id) === 1;
}

/**
 * Validate Book ID format (B### - B followed by 3 digits)
 * @param string $book_id
 * @return bool True if valid, false otherwise
 */
function validateBookID($book_id) {
    return preg_match('/^B\d{3}$/', $book_id) === 1;
}

/**
 * Validate Member ID format (M### - M followed by 3 digits)
 * @param string $member_id
 * @return bool True if valid, false otherwise
 */
function validateMemberID($member_id) {
    return preg_match('/^M\d{3}$/', $member_id) === 1;
}

/**
 * Get all books from the database
 * @param PDO $pdo
 * @return array Array of books with book_id and book_name
 */
function getBooks($pdo) {
    try {
        $sql = "SELECT book_id, book_name FROM book ORDER BY book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get all members from the database
 * @param PDO $pdo
 * @return array Array of members with member_id and member_name
 */
function getMembers($pdo) {
    try {
        $sql = "SELECT member_id, CONCAT(first_name, ' ', last_name) as member_name FROM member ORDER BY member_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }

}

?>