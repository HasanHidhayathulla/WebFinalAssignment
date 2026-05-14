<?php       

    if (!isset($_SESSION['user_id'])) {
        header('Location: login_user_manage\login.php');
        exit;
    }
    ?>