<?php
session_start();
require_once '../db.php';
require_once 'Book_functions.php';

include 'sessioncheck.php';

?>

    <?php include_once '../navigation.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background-color: white;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 600px;
            margin: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
        }
        .features {
            background-color: #f5f5f5;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }
        .features h3 {
            margin-top: 0;
            color: #333;
        }
        .features ul {
            list-style-position: inside;
            color: #666;
            line-height: 1.8;
        }
        .features li {
            margin-bottom: 8px;
        }
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        a, button {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        .btn-secondary {
            background-color: #2196F3;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #0b7dda;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }
        .btn-back {
            background-color: #666;
            color: white;
        }
        .btn-back:hover {
            background-color: #555;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .feature-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        
        
    </style>
</head>
<body>
    <?php include_once '../navigation.php'; ?>
    <div class="container">
        
        <h1>📚 Book Management System</h1>
        <p class="subtitle">Register, Update, and Manage Your Book Inventory</p>
        
        
        <div class="button-group">
            <a href="Book_list.php" class="btn-primary">View All Books</a>
            <a href="Book_form.php" class="btn-secondary">+ Register New Book</a>
        </div>
        
     
    </div>
</body>
</html>
