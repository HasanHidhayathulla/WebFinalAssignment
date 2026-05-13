<?php       
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login_User_manage/login.php');
        exit;
    }
    ?>