<?php
require_once('PartCommonCode.php');
require_once('RenderEditCreateSession.php');
global $link, $name, $email, $badgeid, $message, $message_error;
$_SESSION['return_to_page']="MyProposals.php";
if (!get_name_and_email($name, $email)) {
  error_log("get_name_and_email failed in CreateSession.  ");
}
//error_log("Did create session get name: $name and email: $email");
set_session_defaults();
$id=get_next_session_id();
if (empty($id)) { exit(); }
$id=get_next_session_id();
if (empty($id)) { exit(); }
$session["sessionid"]=$id;
$session["newsessionid"]=$id;
$action="propose";
RenderEditCreateSession($action,$session,$message,$message_error);
exit();
?>
