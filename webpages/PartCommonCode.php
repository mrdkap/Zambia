<?php
require_once('CommonCode.php');
$_SESSION['role'] = "Participant";
$badgeid=$_SESSION['badgeid'];
if (!(may_I("Participant"))) {
  $message.="\nPlease log in to access this page.";
  require ('login.php');
  exit();
};
?>
