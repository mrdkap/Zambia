<?php
    require_once('CommonCode.php');
    $_SESSION['role'] = "Staff";
    $badgeid=$_SESSION['badgeid'];
    if (!(may_I("Staff"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };
?>
