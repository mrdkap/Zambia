<?php
    require_once('CommonCode.php');
    global $badgeid;
    $badgeid=$_SESSION['badgeid'];
    $_SESSION['role']="Vendor";
if (!(may_I("vendor_apply")) AND !(may_I("Vendor"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };
?>
