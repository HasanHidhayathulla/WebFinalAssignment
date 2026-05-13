<?php
/**
 * Shared Navigation Header
 * Include this file at the top of each module's main page to provide consistent navigation
 * 
 * Usage: <?php include_once '../navigation.php'; ?>
 */

// Determine current page/module from the REQUEST_URI
$current_page = basename($_SERVER['PHP_SELF']);
$current_module = basename(dirname($_SERVER['PHP_SELF']));

// Map modules to display names
$module_names = [
    'book_category' => 'Book Categories',
    'book_management' => 'Book Management',
    'borrow_management' => 'Borrow Management',
    'member_management' => 'Member Management',
    'fines' => 'Fines Management',
    'login_User_manage' => 'Staff Management'
];

$current_module_name = $module_names[$current_module] ?? 'Library Management';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_name = isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : '';
?>

<style>
    .nav-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        flex-wrap: wrap;
        gap: 15px;
    }

    .nav-header-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 1;
    }

    .nav-breadcrumb {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .nav-breadcrumb a {
        color: white;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .nav-breadcrumb a:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .nav-separator {
        opacity: 0.6;
    }

    .nav-current {
        font-weight: bold;
        background-color: rgba(255, 255, 255, 0.2);
        padding: 5px 10px;
        border-radius: 4px;
    }

    .nav-header-right {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .user-info {
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .nav-button {
        padding: 8px 15px;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    .nav-button:hover {
        background-color: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        text-decoration: none;
    }

    .nav-button-primary {
        background-color: #4CAF50;
        border-color: #4CAF50;
    }

    .nav-button-primary:hover {
        background-color: #45a049;
        border-color: #45a049;
    }

    .nav-button-danger {
        background-color: #f44336;
        border-color: #f44336;
    }

    .nav-button-danger:hover {
        background-color: #da190b;
        border-color: #da190b;
    }

    @media (max-width: 768px) {
        .nav-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .nav-header-left,
        .nav-header-right {
            width: 100%;
        }

        .nav-header-right {
            justify-content: flex-start;
        }

        .nav-buttons {
            width: 100%;
        }

        .nav-button {
            flex: 1;
            text-align: center;
            min-width: 120px;
        }
    }
</style>

<!-- Navigation Header -->
<div class="nav-header">
    <div class="nav-header-left">
        <div class="nav-breadcrumb">
            <a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/../index.php">📚 Dashboard</a>
            <span class="nav-separator">›</span>
            <span class="nav-current"><?php echo $current_module_name; ?></span>
        </div>
    </div>
    <div class="nav-header-right">
        <?php if ($is_logged_in): ?>
            <div class="user-info">
                👤 <?php echo $user_name; ?>
            </div>
        <?php endif; ?>
        <div class="nav-buttons">
            <a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/../index.php" class="nav-button">← Back to Dashboard</a>
            <?php if ($is_logged_in): ?>
                <a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/../login_User_manage/logout.php" class="nav-button nav-button-danger">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</div>
