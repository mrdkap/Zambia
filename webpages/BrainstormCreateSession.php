<?php
require_once ('BrainstormCommonCode.php');
require_once ('RenderEditCreateSession.php');
global $email, $name, $badgeid, $message_error, $message2;
$_SESSION['return_to_page']="BrainstormCreateSession.php";
if (!get_name_and_email($name, $email)) {
  error_log("get_name_and_email failed in CreateSession.  ");
}
// error_log("badgeid: $badgeid; name: $name; email: $email"); // for debugging only
$message_error="";
$message_warn="";
set_session_defaults();
$id=get_next_session_id();
if (!$id) { exit(); }
$session["sessionid"]=$id;
$session["newsessionid"]=$id;
$action="brainstorm";
RenderEditCreateSession($action,$session,$message_warn,$message_error);
exit();
?>
