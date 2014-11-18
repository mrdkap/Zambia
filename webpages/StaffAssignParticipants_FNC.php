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
    if ($isunlist) {
      $query="DELETE FROM ParticipantSessionInterest where badgeid=\"$badgeid\" ";
      $query.="and sessionid=$selsessionid and conid=$conid";
    } elseif (!$isasgn and $wasasgn) {
      $query="DELETE FROM ParticipantOnSession where badgeid=\"$badgeid\" ";
      $query.="and sessionid=$selsessionid and conid=$conid";
    } elseif (!$wasasgn and $isasgn) {
      $query="INSERT INTO ParticipantOnSession (badgeid, sessionid, conid,";
      $query.=" moderator, volunteer, introducer, aidedecamp) VALUES ";
      $query.="(\"$badgeid\", $selsessionid, $conid, \"".($ismod?1:0)."\", \"";
      $query.=($isvol?1:0)."\", \"".($isint?1:0)."\", \"".($isaid?1:0)."\")";
    } elseif (($ismod and !$wasmod) or (!$ismod and $wasmod) or
	      ($isvol and !$wasvol) or (!$isvol and $wasvol) or
	      ($isint and !$wasint) or (!$isint and $wasint) or
	      ($isaid and !$wasaid) or (!$isaid and $wasaid)) {
      $query="UPDATE ParticipantOnSession set moderator=\"".($ismod?1:0);
      $query.="\", volunteer=\"".($isvol?1:0);
      $query.="\", introducer=\"".($isint?1:0);
      $query.="\", aidedecamp=\"".($isaid?1:0);
      $query.="\" WHERE badgeid=\"$badgeid\" and sessionid=\"$selsessionid\" and conid=$conid";
    } else {
      continue;
    }
    // echo "<P>Query: $query</P>\n";
    if (!mysql_query($query,$link)) {
      $message=$query."<BR>Error updating database.<BR>";
      RenderError($title,$message);
      exit();
    }
  }
  if ($asgnpart!=0) {
    $element_array = array('badgeid', 'sessionid', 'conid');
    $value_array = array($asgnpart, $selsessionid, $conid);
    $message.=submit_table_element($link, $title, "ParticipantSessionInterest", $element_array, $value_array);
    $element_array = array('badgeid','sessionid','conid','moderator','volunteer','introducer','aidedecamp');
    $value_array = array($asgnpart, $selsessionid, $conid, "0", "0", "0", "0");
    $message.=submit_table_element($link, $title, "ParticipantOnSession", $element_array, $value_array);
    // statusid 7 is Assigned.  This should probably be done more dynamically.
    $pairedvalue_array=array("statusid='7'");
    $match_string="sessionid='".$selsessionid."' AND conid='".$conid."'";
    $message.=update_table_element_extended_match ($link,$title,"Sessions",$pairedvalue_array, $match_string);
  }
}
?>    
