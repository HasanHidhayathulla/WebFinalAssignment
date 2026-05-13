<?php
// Database connection (make sure you created the 'library_system' database)
$conn = mysqli_connect("localhost", "root", "", "library_system");

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
        $sql = "INSERT INTO fine (fine_id, member_id, book_id, fine_amount, fine_date_modified) 
                VALUES ('$fine_id', '$member_id', '$book_id', '$amount', '$date')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Fine assigned successfully!');</script>";
        }
    }
}
?>

<h2>Assign New Fine</h2>
<form method="POST" action="">
    Fine ID: <input type="text" name="fine_id" placeholder="e.g. F001" required><br><br>
    Member ID: <input type="text" name="member_id" required><br><br>
    Book ID: <input type="text" name="book_id" required><br><br>
    Fine Amount (LKR): <input type="number" name="fine_amount" required><br><br>
    <button type="submit" name="add_fine">Assign Fine</button>
</form>

<hr>

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
    
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
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
    ?>
</table>