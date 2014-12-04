<?php
global $participant,$message_error,$message2,$congoinfo,$session_interests,$session_interest_index, $title;
$title="Select Interested Sessions";
require ('PartCommonCode.php'); //define database functions
require ('PartPanelInterests_FNC.php');
require ('PartPanelInterests_Render.php');

$maxrow=$_POST["maxrow"];
$delcount=0;
$dellist="";
for ($i=0;$i<=$maxrow;$i++) {
  if (($_POST["checked".$i]==1)&&(!isset($_POST["int".$i]))) {
    $dellist.=(($delcount==0)?"":",").$_POST["sessionid".$i];
    $delcount++;
  }
}
if ($delcount>0) {
  $match_string="badgeid=\"$badgeid\" and sessionid in ($dellist) and conid=".$_SESSION['conid'];
  $message.=delete_table_element($link, $title, "ParticipantSessionInterest", $match_string);
}
$inscount=0;
for ($i=0;$i<=$maxrow;$i++) {
  if (($_POST["checked".$i]==0)&&(isset($_POST["int".$i]))) {
    $element_array = array('badgeid', 'sessionid', 'conid','ibadgeid');
    $value_array = array($badgeid, $_POST['sessionid'.$i], $_SESSION['conid'], $_SESSION['badgeid']);
    $message.=submit_table_element($link, $title, "ParticipantSessionInterest", $element_array, $value_array);
    $inscount++;
  }
}

$error=false;
if (($delcount==0)&&($inscount==0)) {
  $message.="No changes to database requested.";
}
if ($delcount>0) {
  $message.=$delcount." session(s) removed from interest list.<BR>";
}
if ($inscount>0) {
  $message.=$inscount." session(s) added to interest list.";
}
$messageSave=$message;
$message="";
// Get the participant's interest data -- use global $session_interests
    $session_interest_count=get_session_interests_from_db($badgeid); // Returns count; Will render its own errors
// Get title, etc. of such data -- use global $session_interests
    get_si_session_info_from_db($session_interest_count); // Will render its own errors
    $message=$messageSave . $message;
    $message_error="";
    render_session_interests($badgid,$session_interest_count,$message,$message_error); // includes footer
?>        
