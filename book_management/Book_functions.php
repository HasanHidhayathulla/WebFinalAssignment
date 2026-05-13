<?php
// Function to get all categories
function getCategories($pdo) {
    $sql = "SELECT category_id, category_Name FROM bookcategory ORDER BY category_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get category name by ID
function getCategoryName($pdo, $category_id) {
    $sql = "SELECT category_Name FROM bookcategory WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_id]);
    $row = $stmt->fetch();
    return $row ? $row['category_Name'] : 'Unknown';
}