<?php
    $logging_in=true;
    $_SESSION['permission_set'][0] = "none";
    $_SESSION['role']="Posting";
    if ((empty($conid)) and (is_numeric($_GET['conid']))) {
      $conid=$_GET['conid'];
    }
    if ((empty($conid)) and (is_numeric($_SESSION['conid']))) {
      $conid=$_SESSION['conid'];
    }
    if ((empty($_SESSION['conid'])) and (is_numeric($conid))) {
      $_SESSION['conid']=$conid;
    }
    require_once('CommonCode.php');
    $_SESSION['permission_set'][0] = "none";
    $_SESSION['role']="Posting";
    if ((empty($conid)) and (is_numeric($_GET['conid']))) {
      $conid=$_GET['conid'];
    }
    if ((empty($conid)) and (is_numeric($_SESSION['conid']))) {
      $conid=$_SESSION['conid'];
    }
    if ((empty($_SESSION['conid'])) and (is_numeric($conid))) {
      $_SESSION['conid']=$conid;
    }
?>
