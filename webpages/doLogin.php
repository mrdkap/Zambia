<?php
$logging_in=true;

// Set the conid
if ((!empty($_POST['newconid'])) and (is_numeric($_POST['newconid']))) {
  define("CON_KEY",$_POST['newconid']);
}

require_once ('CommonCode.php');

$title="Submit Password";
$badgeid = $_POST['badgeid'];
$password = stripslashes($_POST['passwd']);

// echo "Trying to connect to database.\n";
if (prepare_db()===false) {
  $message_error="Unable to connect to database.<BR>No further execution possible.";
  RenderError($title,$message_error);
  exit();
};
// echo "Connected to database.\n";

//Badgid test
$result=mysql_query("Select password from Participants where badgeid='".$badgeid."'",$link);
if (!$result) {
  $message="Incorrect BadgeID or Password - please be aware that BadgeID and Password are case sensitive and try again.";
  require ('login.php');
  exit();
}

// Password check
$dbobject=mysql_fetch_object($result);
$dbpassword=$dbobject->password;
//echo $badgeid."<BR>".$dbpassword."<BR>".$password."<BR>".md5($password);
//exit(0);
if (md5($password)!=$dbpassword) {
  $message="Incorrect BadgeID or Password - please be aware that BadgeID and Password are case sensitive and try again.";
  require ('login.php');
  exit(0);
}

// Get and set information on individual
$result=mysql_query("Select badgename from CongoDump where badgeid='".$badgeid."'",$link);
if ($result) {
  $dbobject=mysql_fetch_object($result);
  $badgename=$dbobject->badgename;
  $_SESSION['badgename']=$badgename;
} else {
  $_SESSION['badgename']="";
}
$result=mysql_query("Select pubsname from Participants where badgeid='".$badgeid."'",$link);
$pubsname="";
if ($result) {
  $dbobject=mysql_fetch_object($result);
  $pubsname=$dbobject->pubsname;
}
if (!($pubsname=="")) {
  $_SESSION['badgename']=$pubsname;
}
$_SESSION['badgeid']=$badgeid;
$_SESSION['password']=$dbpassword;
set_permission_set($badgeid);
//error_log("Zambia: Completed set_permission_set.\n");

$message2="";

// Switch on which page is shown
if (retrieve_participant_from_db($badgeid)==0) {
  if(may_I('Staff')) {
    require ('StaffPage.php');
  } elseif ((may_I('Vendor')) or ((may_I('public_login')) and ($_POST['target']=="vendor"))) {
    require ('renderVendorWelcome.php');
  } elseif (may_I('Participant')) {
    require ('renderWelcome.php');
  } elseif (may_I('public_login')) {
    require ('BrainstormWelcome.php');
  } else {
    $message_error.="There is a problem with your userid's permission configuration:\n";
    $message_error.="It doesn't have permission to access any welcome page for ";
    $message_error.=$_SESSION['conname'].".\nPlease pick a <A HREF=\"http://".$_SESSION['conurl']."\">";
    $message_error.="different year</A> or contact a member of the ".$_SESSION['conname']." staff.";
    RenderError($title,$message_error);
  }
  exit();
}

// Fail to get db information somewhere ...
$message_error=$message2."<BR>Error retrieving data from DB.  No further execution possible.";
RenderError($title,$message_error);
exit();
?>
