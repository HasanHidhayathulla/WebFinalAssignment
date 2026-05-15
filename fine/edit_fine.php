<?php
include_once '../db.php'; // Include database connection file

$fine_id = isset($_GET['id']) ? trim($_GET['id']) : '';
$error = '';
$success = '';

if (empty($fine_id)) {
    header('Location: fines.php');
    exit;
}

try {
    // Get fine details
    $stmt = $pdo->prepare("SELECT fine.*, member.first_name, member.last_name, book.book_name 
                          FROM fine 
                          JOIN member ON fine.member_id = member.member_id 
                          JOIN book ON fine.book_id = book.book_id 
                          WHERE fine.fine_id = ?");
    $stmt->execute([$fine_id]);
    $fine = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fine) {
        $error = "Fine record not found!";
        header('Location: fines.php');
        exit;
    }

    // Fetch all members for dropdown
    $members = [];
    try {
        $members_stmt = $pdo->prepare("SELECT member_id, first_name, last_name FROM member ORDER BY first_name ASC");
        $members_stmt->execute();
        $members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching members: " . htmlspecialchars($e->getMessage());
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_member_id = $_POST['member_id'] ?? '';
        $new_amount = $_POST['fine_amount'] ?? '';
        
        if (empty($new_member_id)) {
            $error = "Member is required.";
        } elseif (empty($new_amount)) {
            $error = "Fine amount is required.";
        } elseif ($new_amount < 2 || $new_amount > 500) {
            $error = "Fine must be between 2 LKR and 500 LKR.";
        } else {
            $date = date("Y-m-d H:i:s");
            $update_stmt = $pdo->prepare("UPDATE fine SET member_id = ?, fine_amount = ?, fine_date_modified = ? WHERE fine_id = ?");
            $update_stmt->execute([$new_member_id, $new_amount, $date, $fine_id]);
            
            echo "<script>alert('Fine updated successfully!');</script>";
            header('Location: fines.php');
            exit;
        }
    }
} catch (PDOException $e) {
    $error = "Database error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Fine</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; padding: 10px; background-color: #ffcccc; border: 1px solid red; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], select { padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        a { margin-left: 10px; color: #0066cc; }
    </style>
</head>
<body>
    <h1>Edit Fine</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($fine): ?>
        <div class="form-group">
            <p><strong>Fine ID:</strong> <?php echo htmlspecialchars($fine['fine_id']); ?></p>
            <p><strong>Book:</strong> <?php echo htmlspecialchars($fine['book_name']); ?></p>
            <p><strong>Current Amount:</strong> <?php echo htmlspecialchars($fine['fine_amount']); ?> LKR</p>
        </div>

        <form method="POST" action="edit_fine.php?id=<?php echo urlencode($fine_id); ?>">
            <div class="form-group">
                <label for="member_id">Assign Member:</label>
                <select id="member_id" name="member_id" required>
                    <option value="">-- Select Member --</option>
                    <?php foreach ($members as $member): ?>
                        <option value="<?php echo htmlspecialchars($member['member_id']); ?>" <?php echo ($member['member_id'] === $fine['member_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($member['member_id'] . ' - ' . $member['first_name'] . ' ' . $member['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fine_amount">New Fine Amount (LKR):</label>
                <input type="number" id="fine_amount" name="fine_amount" min="2" max="500" value="<?php echo htmlspecialchars($fine['fine_amount']); ?>" required>
                <small>Must be between 2 and 500 LKR</small>
            </div>
            <button type="submit">Update Fine</button>
            <button class="small-button" href="fines.php">Cancel</button>
        </form>
    <?php endif; ?>
</body>
</html>
