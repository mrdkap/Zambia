<?php
require_once('PartCommonCode.php');
require_once('RenderEditCreateSession.php');
global $name,$email,$badgeid,$message_error,$message2;
$_SESSION['return_to_page']="MyProposals.php";
if (!get_name_and_email($name, $email)) {
  error_log("get_name_and_email failed in CreateSession.  ");
}
//error_log("Did create session get name: $name and email: $email");
$message_error="";
$message_warn="";
set_session_defaults();
$id=get_next_session_id();
if (empty($id)) { exit(); }
$id=get_next_session_id();
if (empty($id)) { exit(); }
$session["sessionid"]=$id;
$session["newsessionid"]=$id;
$action="propose";
RenderEditCreateSession($action,$session,$message_warn,$message_error);
exit();
?>
