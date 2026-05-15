<?php
session_start();
require_once '../db.php';
include '../sessioncheck.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrow Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            align-items: center;
            justify-content: center;
        }
        .container {
            background-color: white;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            height: 200px;
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
            list-style: none;
            padding: 0;
        }
        .features li {
            padding: 8px 0;
            color: #555;
        }
        .features li:before {
            content: "✓ ";
            color: #4CAF50;
            font-weight: bold;
            margin-right: 8px;
        }
        .actions {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .btn {
            flex: 1;
            min-width: 200px;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        .btn-secondary {
            background-color: #008CBA;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #007399;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 140, 186, 0.4);
        }
        .btn-back {
            background-color: #666;
            color: white;
        }
        .btn-back:hover {
            background-color: #555;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-top: 20px;
            text-align: left;
            border-radius: 4px;
        }
        .info-box strong {
            color: #1976D2;
        }
    </style>
</head>
<body>
    <?php include_once '../navigation.php'; ?>
    <div class="container">
        <h1>📚 Book Borrow Management</h1>
      

        <div class="actions">
            <a href="create_borrow.php" class="btn btn-primary">+ Create Transaction</a>
            <a href="borrowlist.php" class="btn btn-secondary">View Transactions</a>
        </div>


        
    </div>
</body>
</html>
