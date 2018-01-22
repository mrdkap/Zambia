<?php
$action=$_POST["action"]; // "create" or "edit" or "brainstorm" or "propose"
if ($action=="brainstorm") {
  require_once ('BrainstormCommonCode.php');
} elseif ($action=="propose") {
  require_once ('PartCommonCode.php');
} else {
  require_once ('StaffCommonCode.php');
}
require_once ('RenderEditCreateSession.php');
//session_start();
global $link, $name, $email, $message, $message_error;

//store in arguments and SESSION variables!
get_nameemail_from_post($name, $email);

// return true if OK
$email_status=validate_name_email($name,$email);; 


// store in global $session array
get_session_from_post();

//return true if OK
$status=validate_session(); 
if ($status==false || $email_status==false) {
  $message_error.="<BR>The data you entered was incorrect.  Database not updated.";
  //error_log($message_error);
  RenderEditCreateSession($action,$session,$message,$message_error);
  exit();
}
if ($action=="edit") {
  $status=update_session();
  if (!$status) {
    $message_error.="<BR>Unknown error updating record.  Database not updated successfully.";
    RenderEditCreateSession($action,$session,$message,$message_error);
    exit();
  } else {
    if (!record_session_history($session['sessionid'], $badgeid, $name, $email, 3, $session['status'])) {
      // 3 is code for unknown edit
      error_log("Error recording session history. ".$message_error);
    }
    $session_started=true;
    if (isset($_SESSION['return_to_page'])) {
      header("Location: ".$_SESSION['return_to_page']); /* Redirect browser */
    } else {
      header("Location: genreport.php?reportname=ViewAllSessions"); /* Redirect browser */
    }
    exit();
  }
}
// action = create/brainstorm/propose
$id=insert_session();
if (empty($id)) {
  $message_error.="<BR>".$query."\nUnknown error creating record.  Database not updated successfully.";
  RenderEditCreateSession($action,$session,$message,$message_error);
  exit();
}
if ($id!=$session["sessionid"]) {
  $message_error.="Due to problem with database or concurrent editing, the session ";
  $message_error.="created was actually id: ".$id.".";
} else {
  $message_error.="";
}
$message.="Session record created.  Database updated successfully.  Session ID# = $id";
// 1 is brainstorm; 2 is staff ; 6 is propose
$editcode=3;
if ($action=='brainstorm') {
  $editcode=1;
} elseif ($action=='propose') {
  $editcode=6;
} else {
  $editcode=2;
}

record_session_history($id, $badgeid, $name, $email, $editcode, $session['status']);
set_session_defaults();
$id=get_next_session_id();
if (empty($id)) {exit(); }
$session["sessionid"]=$id;
$session["newsessionid"]=$id;
RenderEditCreateSession($action,$session,$message,$message_error);
exit();
?>
