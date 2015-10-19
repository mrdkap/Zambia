<?php
require_once ('StaffCommonCode.php');
global $link;

$title=$_SESSION['conname'] . " - Precis";
$description="<P>If you have any questions, please contact: ";
$description.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A></P>\n";

$_SESSION['return_to_page']="ViewPrecis.php";

//Defaults presumed
$trackidlist="";
// statusidlist produces a comma-seperated list of ids that match the listed typenames
$statusidlist=get_idlist_from_db("SessionStatuses","statusid","statusname","'Brainstorm','Edit Me','Vetted','Assigned','Scheduled'");
$typeidlist="";
$sessionidlist="";
$trackid=0;
$typeid=0;
$statusid=0;
$sessionid="";
$conid=$_SESSION['conid'];
$rtp="";

if ((isset($_POST["track"])) and (is_numeric($_POST["track"]))) {
  $trackid=$_POST["track"];
} elseif ((isset($_GET["track"])) and (is_numeric($_GET["track"]))) {
  $trackid=$_GET["track"];
}

if ((isset($_POST["status"])) and (is_numeric($_POST["status"]))) {
  $statusid=$_POST["status"];
} elseif ((isset($_GET["status"])) and (is_numeric($_GET["status"]))) {
  $statusid=$_GET["status"];
}

if ((isset($_POST["type"])) and (is_numeric($_POST["type"]))) {
  $typeid=$_POST["type"];
} elseif ((isset($_GET["type"])) and (is_numeric($_GET["type"]))) {
  $typeid=$_GET["type"];
}

if ((isset($_POST["sessionid"])) and (is_numeric($_POST["sessionid"]))) {
  $sessionid=$_POST["sessionid"];
} elseif ((isset($_GET["sessionid"])) and (is_numeric($_GET["sessionid"]))) {
  $sessionid=$_GET["sessionid"];
}

if ((isset($_POST["conid"])) and (is_numeric($_POST["conid"]))) {
  $conid=$_POST["conid"];
  $title=$conid . " - Precis";
} elseif ((isset($_GET["conid"])) and (is_numeric($_GET["conid"]))) {
  $conid=$_GET["conid"];
  $title=$conid . " - Precis";
}

if ($trackid!=0) {
  $trackidlist=$trackid;
  if ($rtp!="") {$rtp.="&";}
  $rtp.="track=$trackid";
}
if ($statusid!=0) {
  $statusidlist=$statusid;
  if ($rtp!="") {$rtp.="&";}
  $rtp.="status=$statusid";
}
if ($typeid!=0) {
  $typeidlist=$typeid;
  if ($rtp!="") {$rtp.="&";}
  $rtp.="type=$typeid";
}
if ($sessionid!="") {
  $sessionidlist=$sessionid;
  if ($rtp!="") {$rtp.="&";}
  $rtp.="sessionid=$sessionid";
}
if ($conid!=$_SESSION['conid']) {
  $conid=$conid;
  if ($rtp!="") {$rtp.="&";}
  $rtp.="conid=$conid";
}

if ($rtp!="") {$_SESSION['return_to_page'].="?$rtp";}

// Get the selected information for precis processing
$query=retrieve_select_from_db($trackidlist,$statusidlist,$typeidlist,$sessionidlist,$conid);

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// put up the form to choose a subset of the precis
echo "<FORM method=POST action=\"ViewPrecis.php\">\n";
if ($conid!=$_SESSION['conid']) {
  echo "<INPUT type=hidden name=conid value=$conid>\n";
}
$search=RenderSearchSession($trackid,$statusid,$typeid,$sessionid);
echo $search;
echo "</FORM>\n";

/* Produce the report. */
$printstring=renderprecisreport(1,$elements,$header_array,$element_array);
echo $printstring;

correct_footer();
?>
