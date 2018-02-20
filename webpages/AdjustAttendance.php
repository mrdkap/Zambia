<?php
require_once ('StaffCommonCode.php');
global $link, $message, $message_error;

// LOCALISMS
$title="Adjust Attendence Counts";
$description="<P>Add/adjust attendence count for session.</P>\n";
$additionalinfo="<P><A HREF=\"" . $_SESSION['return_to_page'] . "\">Return to Report</A></P>\n";
$conid=$_SESSION['conid'];

//Defaults presumed
$sessionid="";
$estatten="";
$printstring="";
$rtp="";

// Reset conid
if ((!empty($_POST["conid"])) and (is_numeric($_POST["conid"]))) {
  $conid=$_POST["conid"];
} elseif ((!empty($_GET["conid"])) and (is_numeric($_GET["conid"]))) {
  $conid=$_GET["conid"];
}

// Check to see if sessionid is passed in
if ((!empty($_POST["sessionid"])) and (is_numeric($_POST["sessionid"]))) {
  $sessionid=$_POST["sessionid"];
} elseif ((!empty($_GET["sessionid"])) and (is_numeric($_GET["sessionid"]))) {
  $sessionid=$_GET["sessionid"];
}

// If there is a sessionid
if (!empty($sessionid)) {
  if ($rtp!="") {$rtp.="&";}
  $rtp.="sessionid=$sessionid";

  // Check to see if an estatten number was passed in
  if ((!empty($_POST["estatten"])) and (is_numeric($_POST["estatten"]))) {
    $estatten=$_POST["estatten"];
  } elseif ((!empty($_GET["estatten"])) and (is_numeric($_GET["estatten"]))) {
    $estatten=$_GET["estatten"];
  }

  // If there was an estatten number passed in, update it
  if (!empty($estatten)) {
    $set_array=array("estatten=$estatten");
    $match_string="conid=$conid AND sessionid=$sessionid";
    $message.=update_table_element_extended_match($link, $title, "Sessions", $set_array, $match_string);
  }

  // Get the selected session's information for precis processing
  $query=retrieve_select_from_db("","","",$sessionid,$conid);

  // Retrieve query
  list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

  // Set up the estatten elements as a form input
  for ($i=1; $i<=$elements; $i++) {
    $element_array[$i]['estatten']="<INPUT type=\"text\" size=\"3\" name=\"estatten\" value=\"" . $element_array[$i]['estatten'] . "\">";
  }

  // Set up the string to be printed
  $printstring=renderprecisreport(1,$elements,$header_array,$element_array);
}

// If there is a conid, and it doesn't match the current conid
if ($conid!=$_SESSION['conid']) {
  if ($rtp!="") {$rtp.="&";}
  $rtp.="conid=$conid";
}

// If there is anything to pass through
if ($rtp!="") {$rtp="?$rtp";}

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if (!empty($printstring)) {
  echo "<FORM method=POST action=\"AdjustAttendance.php\">\n";
  echo "  <CENTER><input type=\"submit\" value=\"Update\"></CENTER>\n";
  echo "  <input type=\"hidden\" name=\"sessionid\" value=\"$sessionid\">\n";
  echo $printstring;
  echo "</FORM>\n";
} else {
  echo "<P>Please select a session before attempting to set the Estimated Attendance.</P>\n";
}

correct_footer();
?>
