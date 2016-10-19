<?php
require_once('CommonCode.php');
global $badgeid;
$badgeid=$_SESSION['badgeid'];
$_SESSION['role']="PhotoSub";
if (!(may_I("public_login")) AND !(may_I("PhotoSub"))) {
  $message="You are not authorized to access this page.";
  require ('login.php');
  exit();
}
if (may_I("public_login")) {
  $_SESSION['role']="Posting";
}

?>
