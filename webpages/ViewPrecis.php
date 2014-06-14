<?php
require_once ('StaffCommonCode.php');
global $link;

$title=$_SESSION['conname'] . " - Precis";
$description="<P>If you have any questions, please contact: ";
$description.="<A HREF=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</A></P>\n";

// Showlinks mapping
$showlinks=$_GET["showlinks"];
$_SESSION['return_to_page']="ViewPrecis.php?showlinks=$showlinks";
if ($showlinks=="1") {
  $showlinks=true;
} elseif ($showlinks="0") {
  $showlinks=false;
}

// statusidlist produces a comma-seperated list of ids that match the listed typenames
$statusidlist=get_idlist_from_db("SessionStatuses","statusid","statusname","'Brainstorm','Edit Me','Vetted','Scheduled'");
$typeidlist="";
$trackidlist="";
$sessionid="";

// Get the selected information for precis processing
if (retrieve_select_from_db($trackdlist,$statusidlist,$typeidlist,$sessionid)==0) {
  topofpagereport($title,$description,$additionalinfo);
  RenderPrecis($result,$showlinks);
  correct_footer();
  exit();
}

// Or fail.
$message_error="Error retrieving from database. ".$message2;
RenderError($title,$message_error);
?> 
