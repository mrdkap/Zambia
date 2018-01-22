<?php
require_once('PartCommonCode.php');
require_once('RenderEditCreateSession.php');
global $link, $name, $email, $badgeid, $message, $message_error;
$_SESSION['return_to_page']="MyMigrations.php";
if (!get_name_and_email($name, $email)) {
  error_log("get_name_and_email failed in CreateSession.  ");
}
//error_log("Did create session get name: $name and email: $email");
// check for sessionid
$id="";
if (!empty($_POST['sessionid'])) {
  $id=$_POST['sessionid'];
} elseif (!empty($_GET['sessionid'])) {
  $id=$_GET['sessionid'];
}

// check for conid
$conid="";
if (!empty($_POST['conid'])) {
  $conid=$_POST['conid'];
} elseif (!empty($_GET['conid'])) {
  $conid=$_GET['conid'];
}

// get the session information
if ((is_numeric($id)) and ($id>0) and (is_numeric($conid)) and ($conid>0)) {
  $status=retrieve_session_from_db($id,$conid);
  if ($status==-3) {
    $message_error.="Error retrieving record from database.";
    RenderError($title,$message_error);
    exit;
   }
  if ($status==-2) {
    $message_error.="Session record with id=".$id." not found (or error with Session primary key).";
    RenderError($title,$message_error);
    exit;
   }
 }


// check for suggestor
if ($conid==$_SESSION['conid']) {
  $message_error.="This has already been migrated/proposed for this year.  You have reached this page in error.  Please pick another class to migrate.";
  RenderError($title,$message_error);
  exit;
} elseif ($session["suggestor"]==$_SESSION['badgeid']) {
  $message.="Migration Begun, please edit the below as necessary and hit the save button.";
} elseif (may_I("ConChair")) {
  $message.="Status: ".$status." Division: ".$session["divisionid"]." Track: ".$session["trackid"]." Type: ".$session["typeid"]." Status: ".$session["statusid"]." Suggestor: ".$session["suggestor"];
} else {
  $message_error.="This ($id from $conid) was not a class suggested by you (".$_SESSION['badgeid']."), and therefore should not be migrated by you.";
  RenderError($title,$message_error);
  exit;
}

$newid=get_next_session_id();
if (!$newid) { exit(); }
$session["sessionid"]=$id;
$session["newsessionid"]=$newid;
$action="propose";
RenderEditCreateSession($action,$session,$message,$message_error);
exit();
?>
