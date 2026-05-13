<?php 
include 'sessioncheck.php';
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px 0;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-custom .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
            font-size: 1.5rem;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 50px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .feature-card-header {
            padding: 25px;
            color: white;
            font-weight: bold;
            font-size: 1.3rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 120px;
        }

        .feature-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .feature-card-body p {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .feature-card-footer {
            display: flex;
            gap: 10px;
            padding: 0 20px 20px 20px;
        }

        .feature-card-footer a {
            flex: 1;
            padding: 10px 15px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid;
        }

        /* Color schemes for each feature */
        .card-blue .feature-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-blue .feature-card-footer a {
            color: #667eea;
            border-color: #667eea;
        }
        .card-blue .feature-card-footer a:hover {
            background-color: #667eea;
            color: white;
        }

        .card-green .feature-card-header {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
        }
        .card-green .feature-card-footer a {
            color: #56ab2f;
            border-color: #56ab2f;
        }
        .card-green .feature-card-footer a:hover {
            background-color: #56ab2f;
            color: white;
        }

        .card-orange .feature-card-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .card-orange .feature-card-footer a {
            color: #f5576c;
            border-color: #f5576c;
        }
        .card-orange .feature-card-footer a:hover {
            background-color: #f5576c;
            color: white;
        }

        .card-teal .feature-card-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .card-teal .feature-card-footer a {
            color: #4facfe;
            border-color: #4facfe;
        }
        .card-teal .feature-card-footer a:hover {
            background-color: #4facfe;
            color: white;
        }

        .card-red .feature-card-header {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        .card-red .feature-card-footer a {
            color: #fa709a;
            border-color: #fa709a;
        }
        .card-red .feature-card-footer a:hover {
            background-color: #fa709a;
            color: white;
        }

        .card-purple .feature-card-header {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        .card-purple .feature-card-footer a {
            color: #d63031;
            border-color: #d63031;
        }
        .card-purple .feature-card-footer a:hover {
            background-color: #d63031;
            color: white;
        }

        .admin-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 30px;
        }

        .admin-section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .admin-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-admin {
            padding: 12px 30px;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-admin-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .btn-admin-login:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-admin-panel {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
            color: white;
            border: none;
        }

        .btn-admin-panel:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(86, 171, 47, 0.4);
            color: white;
            text-decoration: none;
        }

        .footer {
            text-align: center;
            color: white;
            padding: 30px;
            margin-top: 50px;
        }

        .footer p {
            margin: 0;
            font-size: 0.95rem;
        }

        .emoji {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8rem;
            }

            .feature-grid {
                gap: 20px;
            }

            .admin-buttons {
                flex-direction: column;
            }

            .btn-admin {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
<nav class="navbar navbar-light navbar-custom">
  <div class="container-fluid">
    <span class="navbar-brand">📚 Library Management System</span>
    <button class="btn btn-outline-danger ms-auto" 
            onclick="location.href='login_User_manage/logout.php'">
            Logout
    </button>
  </div>
</nav>


    <!-- Header -->
    <div class="header">
        <h1>Welcome to Library Management</h1>
        <p>Manage books, members, borrowing, fines, and staff all in one place</p>
    </div>

    <!-- Main Dashboard -->
    <div class="dashboard-container">
        <!-- Features Grid -->
        <div class="feature-grid">
            <!-- Staff Management -->
            <div class="feature-card card-purple">
                <div class="feature-card-header">
                    <div>
                        <div class="emoji">👨‍💼</div>
                        <span>Staff Management</span>
                    </div>
                </div>
                <div class="feature-card-body">
                    <p>Manage library staff accounts, admin panel access, and user credentials (login required).</p>
                </div>
                <div class="feature-card-footer">
                    <a href="login_User_manage/admin.php">Admin panel</a>
                </div>
            </div>
            
            <!-- Book Management -->
            <div class="feature-card card-green">
                <div class="feature-card-header">
                    <div>
                        <div class="emoji">📖</div>
                        <span>Book Management</span>
                    </div>
                </div>
                <div class="feature-card-body">
                    <p>Add, view, update, and remove books from your library inventory with category assignment.</p>
                </div>
                <div class="feature-card-footer">
                    <a href="book_management/Book_index.php">Manage Books</a>
                </div>
            </div>

            <!-- Book Category Management -->
            <div class="feature-card card-blue">
                <div class="feature-card-header">
                    <div>
                        <div class="emoji">🏷️</div>
                        <span>Book Categories</span>
                    </div>
                </div>
                <div class="feature-card-body">
                    <p>Create, view, update, and delete book categories to organize your library's collection effectively.</p>
                </div>
                <div class="feature-card-footer">
                    <a href="book_category/book_category.php">Manage Categories</a>
                </div>
            </div>


            <!-- Member Management -->
            <div class="feature-card card-teal">
                <div class="feature-card-header">
                    <div>
                        <div class="emoji">👥</div>
                        <span>Member Management</span>
                    </div>
                </div>
                <div class="feature-card-body">
                    <p>Register new members, view member details, update information, and manage member records.</p>
                </div>
                <div class="feature-card-footer">
                    <a href="member_management/members.php">Manage Members</a>
                </div>
            </div>

            <!-- Borrow Management -->
            <div class="feature-card card-orange">
                <div class="feature-card-header">
                    <div>
                        <div class="emoji">📤</div>
                        <span>Borrow Management</span>
                    </div>
                </div>
                <div class="feature-card-body">
                    <p>Track book borrowing transactions, manage borrow records, and monitor return status.</p>
                </div>
                <div class="feature-card-footer">
                    <a href="borrow_management/borrow.php">Manage Borrowing</a>
                </div>
            </div>

            <!-- Fines Management -->
            <div class="feature-card card-red">
                <div class="feature-card-header">
                    <div>
                        <div class="emoji">💰</div>
                        <span>Fines Management</span>
                    </div>
                </div>
                <div class="feature-card-body">
                    <p>Assign fines to members for late returns, manage fine records, and track payments.</p>
                </div>
                <div class="feature-card-footer">
                    <a href="fine/fines.php">Manage Fines</a>
                </div>
            </div>

            
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
