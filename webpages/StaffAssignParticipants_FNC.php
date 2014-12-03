<?php 
/* This should be folded either into StaffAssignParticipants, or
   CommonCode if it is ever referenced otherwise.  Also the
   StaffAssignParticipantsVariable set should probably go away. */
function SubmitAssignParticipants() {
  global $link, $title;
  $conid=$_SESSION['conid'];

  //    print_r($_POST);
  $asgnpart=$_POST["asgnpart"];
  $numrows=$_POST["numrows"];
  $moderator=$_POST["moderator"];
  $volunteer=$_POST["volunteer"];
  $introducer=$_POST["introducer"];
  $wasmodid=$_POST["wasmodid"];
  $wasvolid=$_POST["wasvolid"];
  $wasintid=$_POST["wasintid"];
  $selsessionid=$_POST["selsess"];
  for ($i=0; $i<$numrows; $i++) {
    $badgeid=$_POST["row$i"];
    $ismod=($moderator==$badgeid);
    $isvol=($volunteer==$badgeid);
    $isint=($introducer==$badgeid);
    $isunlist=($_POST["unlist$badgeid"]==1);
    $isaid=$_POST["aidedecamp$badgeid"];
    $isasgn=(isset($_POST["asgn$badgeid"]) or $ismod or $isvol or $isint or $isaid);
    $wasasgn=($_POST["wasasgn$badgeid"]==1);
    $wasmod=($wasmodid==$badgeid);
    $wasvol=($wasvolid==$badgeid);
    $wasint=($wasintid==$badgeid);
    $wasaid=$_POST["wasaidedecamp$badgeid"];
    //echo "i: $i | isasgn: $isasgn | wasasgn: $wasasgn | ismod: $ismod | wasmod: $wasmod | isaid $isaid | wasaid $wasaid <BR>\n";        
    if (!$wasasgn and $isasgn) {
      $element_array = array('badgeid','sessionid','conid','obadgeid','moderator','volunteer','introducer','aidedecamp');
      $value_array = array($badgeid, $selsessionid, $conid, $_SESSION['badgeid'],
			   ($ismod?"1":"0"), ($isvol?"1":"0"), ($isint?"1":"0"), ($isaid?"1":"0"));
      $message.=submit_table_element($link, $title, "ParticipantOnSession", $element_array, $value_array);
    }
    if (($ismod and !$wasmod) or (!$ismod and $wasmod) or
	($isvol and !$wasvol) or (!$isvol and $wasvol) or
	($isint and !$wasint) or (!$isint and $wasint) or
	($isaid and !$wasaid) or (!$isaid and $wasaid)) {
      $pairedvalue_array=array("moderator='".($ismod?1:0)."'",
			       "volunteer='".($isvol?1:0)."'",
			       "introducer='".($isint?1:0)."'",
			       "aidedecamp='".($isaid?1:0)."'");
      $match_string="badgeid='".$badgeid."' AND sessionid='".$selsessionid."' and conid='".$conid."'";
      $message.=update_table_element_extended_match ($link,$title,"ParticipantOnSession",$pairedvalue_array, $match_string);
    }
    if ($isunlist) {
      $match_string="badgeid=\"$badgeid\" and sessionid=$selsessionid and conid=$conid";
      $message.=delete_table_element($link, $title, "ParticipantSessionInterest", $match_string);
    }
    if (!$isasgn and $wasasgn) {
      $match_string="badgeid=\"$badgeid\" and sessionid=$selsessionid and conid=$conid";
      $message.=delete_table_element($link, $title, "ParticipantOnSession", $match_string);
    }
  }

  // If there was someone hand-assigned at the bottom.
  if ($asgnpart!=0) {
    $element_array = array('badgeid', 'sessionid', 'conid','ibadgeid');
    $value_array = array($asgnpart, $selsessionid, $conid, $_SESSION['badgeid']);
    $message.=submit_table_element($link, $title, "ParticipantSessionInterest", $element_array, $value_array);
    $element_array = array('badgeid','sessionid','conid','moderator','volunteer','introducer','aidedecamp','obadgeid');
    $value_array = array($asgnpart, $selsessionid, $conid, "0", "0", "0", "0", $_SESSION['badgeid']);
    $message.=submit_table_element($link, $title, "ParticipantOnSession", $element_array, $value_array);
    // statusid 7 is Assigned 2 is Vetted.  If Vetted move to Assigned.  Otherwise leave alone.
    // This should probably be done more dynamically.
    if ($_POST['statusid']=='2') {
      $pairedvalue_array=array("statusid='7'");
      $match_string="sessionid='".$selsessionid."' AND conid='".$conid."'";
      $message.=update_table_element_extended_match ($link,$title,"Sessions",$pairedvalue_array, $match_string);
    }
  }
}
?>    
