<?php
require_once('CommonCode.php');
$_SESSION['role'] = "Participant";
$badgeid=$_SESSION['badgeid'];
if (!((may_I("Participant"))  or (may_I('Panelist')) or (may_I('Aide')) or
      (may_I('Host')) or (may_I('Demo')) or (may_I('Teacher')) or (may_I('Presenter')) or
      (may_I('Author')) or (may_I('Organizer')) or (may_I('Performer')))) {
  $message.="\nPlease log in to access this page.";
  require ('login.php');
  exit();
};
?>
