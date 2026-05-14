<?php
require_once __DIR__ . '/../db.php';
session_start();
include '../sessioncheck.php';


$message = '';
$error = '';
$category_id = '';
$category_name = '';
$edit_mode = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = trim($_POST['category_id'] ?? '');
    $category_name = trim($_POST['category_name'] ?? '');
    $old_category_id = trim($_POST['old_category_id'] ?? '');
    $edit_mode = $old_category_id !== '';

    if ($category_id === '' || $category_name === '') {
        $error = 'Category ID and Category Name are required.';
    } elseif (!preg_match('/^C\d{3}$/', $category_id)) {
        $error = 'Category ID must use the format C001, C002, etc.';
    }

    if ($error === '') {
        $date_modified = date('Y-m-d h:i:sa');

        if ($old_category_id !== '') {
            $sql = 'SELECT category_id FROM bookcategory WHERE category_id = :category_id AND category_id != :old_category_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['category_id' => $category_id, 'old_category_id' => $old_category_id]);
            if ($stmt->fetch()) {
                $error = 'Another category already uses that Category ID.';
            } else {
                $sql = 'UPDATE bookcategory SET category_id = :category_id, category_Name = :category_name, date_modified = :date_modified WHERE category_id = :old_category_id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'category_id' => $category_id,
                    'category_name' => $category_name,
                    'date_modified' => $date_modified,
                    'old_category_id' => $old_category_id,
                ]);
                $message = 'Category updated successfully.';
                $category_id = '';
                $category_name = '';
            }
        } else {
            $sql = 'SELECT category_id FROM bookcategory WHERE category_id = :category_id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['category_id' => $category_id]);
            if ($stmt->fetch()) {
                $error = 'A category with this Category ID already exists.';
            } else {
                $sql = 'INSERT INTO bookcategory (category_id, category_Name, date_modified) VALUES (:category_id, :category_name, :date_modified)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'category_id' => $category_id,
                    'category_name' => $category_name,
                    'date_modified' => $date_modified,
                ]);
                $message = 'Category created successfully.';
                $category_id = '';
                $category_name = '';
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $edit_category_id = trim($_GET['edit']);
    $sql = 'SELECT category_id, category_Name FROM bookcategory WHERE category_id = :category_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['category_id' => $edit_category_id]);
    $category = $stmt->fetch();

    if ($category) {
        $category_id = $category['category_id'];
        $category_name = $category['category_Name'];
        $edit_mode = true;
    } else {
        $error = 'Category not found for editing.';
    }
}

if (isset($_GET['delete'])) {
    $delete_category_id = trim($_GET['delete']);
    $sql = 'DELETE FROM bookcategory WHERE category_id = :category_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['category_id' => $delete_category_id]);
    header('Location: ' . basename(__FILE__) . '?message=' . urlencode('Category deleted successfully.'));
    exit;
}

if (isset($_GET['message']) && $message === '') {
    $message = trim($_GET['message']);
}

$sql = 'SELECT category_id, category_Name, date_modified FROM bookcategory ORDER BY category_id';
$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Category Registration</title>
    <link rel="stylesheet" href="../style.css"> 
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .message { color: green; margin-bottom: 15px; }
        .error { color: red; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        form label { display: block; margin: 8px 0 4px; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .actions a { margin-right: 10px; }
        .button { margin-top: 10px; padding: 10px 18px; }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../navigation.php'; ?>
    <div class="container">
        <h1>Book Category Registration</h1>

        <?php if ($message !== ''): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label for="category_id">Category ID</label>
            <input type="text" id="category_id" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>" placeholder="C001">

            <label for="category_name">Category Name</label>
            <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category_name); ?>" placeholder="Sci-fi">

            <?php if ($edit_mode): ?>
                <input type="hidden" name="old_category_id" value="<?php echo htmlspecialchars($category_id); ?>">
            <?php endif; ?>

            <button type="submit" class="button"><?php echo $edit_mode ? 'Update Category' : 'Create Category'; ?></button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Date Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categories) === 0): ?>
                    <tr><td colspan="4">No categories found.</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_Name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_modified']); ?></td>
                            <td class="actions">
                                <a href="<?php echo basename(__FILE__); ?>?edit=<?php echo urlencode($row['category_id']); ?>">Edit</a>
                                <a href="<?php echo basename(__FILE__); ?>?delete=<?php echo urlencode($row['category_id']); ?>" onclick="return confirm('Delete this category?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>