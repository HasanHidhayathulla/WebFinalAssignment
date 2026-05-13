<?php
session_start();
include_once '../db.php'; // Include database connection file
// 1. Logic to ADD a Fine
if (isset($_POST['add_fine'])) {
    $fine_id = $_POST['fine_id'];
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $amount = $_POST['fine_amount'];
    $date = date("Y-m-d H:i:s"); // Gets the current system date and time 

    // Check if Fine ID follows the format (Example: F001) 
    // Validation: Fine amount must be between 2 and 500 [cite: 58]
    if ($amount < 2 || $amount > 500) {
        echo "<script>alert('Error: Fine must be between 2 LKR and 500 LKR');</script>";
    } else {
        // Validate that member_id exists
        try {
            $checkMember = $pdo->prepare("SELECT member_id FROM member WHERE member_id = ?");
            $checkMember->execute([$member_id]);
            if ($checkMember->rowCount() == 0) {
                echo "<script>alert('Error: Member ID does not exist');</script>";
            } else {
                // Validate that book_id exists
                $checkBook = $pdo->prepare("SELECT book_id FROM book WHERE book_id = ?");
                $checkBook->execute([$book_id]);
                if ($checkBook->rowCount() == 0) {
                    echo "<script>alert('Error: Book ID does not exist');</script>";
                } else {
                    $sql = "INSERT INTO fine (fine_id, member_id, book_id, fine_amount, fine_date_modified) 
                            VALUES (:fine_id, :member_id, :book_id, :amount, :date)";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':fine_id' => $fine_id,
                        ':member_id' => $member_id,
                        ':book_id' => $book_id,
                        ':amount' => $amount,
                        ':date' => $date
                    ]);
                    echo "<script>alert('Fine assigned successfully!');</script>";
                }
            }
        } catch (PDOException $e) {
            echo "<script>alert('Error: " . htmlspecialchars($e->getMessage()) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fines Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        form { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        form label { display: block; margin-top: 10px; font-weight: bold; }
        input, select { padding: 8px; width: 100%; max-width: 300px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #667eea; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f0f0f0; }
        a { color: #667eea; text-decoration: none; margin-right: 10px; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php include_once '../navigation.php'; ?>
    <div class="container">
<h2>Assign New Fine</h2>
<form method="POST" action="">
    Fine ID: <input type="text" name="fine_id" placeholder="e.g. F001" required><br><br>
    Member ID: <input type="text" name="member_id" required><br><br>
    Book ID: <input type="text" name="book_id" required><br><br>
    Fine Amount (LKR): <input type="number" name="fine_amount" required><br><br>
    <button type="submit" name="add_fine">Assign Fine</button>
</form>

<h2>Fine Records</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Fine ID</th>
        <th>Member ID</th>
        <th>Member Name</th>
        <th>Book Name</th>
        <th>Amount (LKR)</th>
        <th>Date Modified</th>
        <th>Action</th>
    </tr>

    <?php
    // Query to join tables so we can show Member Name and Book Name 
    $query = "SELECT fine.*, member.first_name, book.book_name 
              FROM fine 
              JOIN member ON fine.member_id = member.member_id 
              JOIN book ON fine.book_id = book.book_id";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            echo "<tr>
                    <td>{$row['fine_id']}</td>
                    <td>{$row['member_id']}</td>
                    <td>{$row['first_name']}</td>
                    <td>{$row['book_name']}</td>
                    <td>{$row['fine_amount']}</td>
                    <td>{$row['fine_date_modified']}</td>
                    <td>
                        <a href='edit_fine.php?id={$row['fine_id']}'>Edit</a> | 
                        <a href='delete_fine.php?id={$row['fine_id']}'>Delete</a>
                    </td>
                  </tr>";
        }
    } catch (PDOException $e) {
        echo "<tr><td colspan='7'>Error loading records: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
    ?>
</table>
    </div>
</body>
</html>