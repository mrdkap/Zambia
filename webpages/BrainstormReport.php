<?php
require_once('BrainstormCommonCode.php');
global $link;
$conid=$_SESSION['conid'];

// Variables, passed in
if (isset($_POST["track"])) {
  $trackid=$_POST["track"];
} else {
  $trackid=0;
}
if (isset($_POST["status"])) {
  $statusid=$_POST["status"];
} else {
  $statusid=0;
}
$titlesearch=stripslashes($_POST["title"]);

// These are all set the same, across all the possibilities, except for searches.
$typeidlist=get_idlist_from_db("Types","typeid","typename","'Panel','Class','Presentation','Author Reading','Lounge','SIG/BOF/MnG','Social','EVENT','Performance'");
$trackidlist="";
$sessionid="";

// Search left out of choices to avoid tail-biting.
$statuschoice['all']="ANY";
$statuschoice['unseen']="New (Unseen)";
$statuschoice['reviewed']="Reviewed";
$statuschoice['likely']="Likely to Occur";
$statuschoice['scheduled']="Scheduled";


// Switch on which status elements are desired, and adjust the title and description to match.
if (($_GET['status']=="scheduled") or ($_POST['status']=="scheduled")) {
  //"'Assigned','Scheduled'";
  $statusidlist=get_idlist_from_db("SessionStatuses","statusid","statusname","'Assigned','Scheduled'");
  $title="Scheduled Suggestions";
  $description="<P>These ideas are highly likely to make it into the final schedule. Things are looking good for them.</P>\n";
  $additionalinfo ="<P>Please remember events out of our control and last minute emergencies cause this to change!";
  $additionalinfo.=" No promises, but we are doing our best to have this happen.</P>\n";
} elseif (($_GET['status']=="likely") OR ($_POST['status']=="likely")) {
  //"'Vetted','Assigned','Scheduled'";
  $statusidlist=get_idlist_from_db("SessionStatuses","statusid","statusname","'Assigned','Vetted','Scheduled'");
  $title="Likely to Occur Suggestions";
  $description="<P>These ideas have made the first cut.</P>\n";
  $additionalinfo ="<P>We like these ideas and would like to see them happen. Now to just find all the right people... </P>\n";
} elseif (($_GET['status']=="reviewed") OR ($_POST['status']=="reviewed")) {
  //"'Edit Me','Vetted','Assigned','Scheduled'";
  $statusidlist=get_idlist_from_db("SessionStatuses","statusid","statusname","'Assigned','Edit Me','Vetted','Scheduled'");
  $title="Reviewed Suggestions";
  $description="<P>We've seen these. They have varying degrees of merit.</P>\n";
  $additionalinfo ="<P>We have or will sort through these suggestions: combining duplicates; splitting big ones into pieces;";
  $additionalinfo.=" checking general feasability; finding needed people to present; looking for an appropiate time and location;";
  $additionalinfo.=" rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</P>\n";
  $additionalinfo.="<P>Note that ideas that we like and are pursuing further will stay on this list.  That is to make it easier";
  $additionalinfo.=" to find the idea you suggested.</P>\n";
} elseif (($_GET['status']=="unseen") OR ($_POST['status']=="unseen")) {
  //"'Brainstorm'";
  $statusidlist=get_idlist_from_db("SessionStatuses","statusid","statusname","'Brainstorm'");
  $title="New (Unseen) Suggestions";
  $description="<P>If an idea is on this page, there is a good chance we have not yet seen it.</P>\n";
  $additionalinfo="<P>So, please wear your Peril Sensitive Sunglasses while reading. We do.</P>\n";
} elseif (($_GET['status']=="all") OR ($_POST['status']=="all")) {
  //"'Edit Me','Brainstorm','Vetted','Assigned','Scheduled'";
  $statusidlist=get_idlist_from_db("SessionStatuses","statusid","statusname","'Brainstorm','Edit Me','Vetted','Assigned','Scheduled'");
  $title="All Suggestions";
  $description="<P>This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</P>\n";
  $additionalinfo ="<P>We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces;";
  $additionalinfo.=" checking general feasability; finding needed people to present; looking for an appropiate time and location;";
  $additionalinfo.=" rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</P>\n";
} else { // Same as status == all.
  //"'Edit Me','Brainstorm','Vetted','Assigned','Scheduled'";
  $statusidlist=get_idlist_from_db("SessionStatuses","statusid","statusname","'Brainstorm','Edit Me','Vetted','Assigned','Scheduled'");
  $title="All Suggestions";
  $description="<P>This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</P>\n";
  $additionalinfo ="<P>We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces;";
  $additionalinfo.=" checking general feasability; finding needed people to present; looking for an appropiate time and location;";
  $additionalinfo.=" rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</P>\n";
}

$additionalinfo.="<P>If you want to help, email us at: ";
$additionalinfo.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A></P>\n";
$additionalinfo.="<P>This list is sorted by Track and then Title.</P>\n";

if ((isset($_POST['status'])) OR ($_GET['status']=='search')) {
  $search=RenderSearchSession($trackid,$statusid,0,"");

  $additionalinfo.="<BR>\n";
  $additionalinfo.="<FORM name=\"brainstormsearchsession\" method=POST action=\"BrainstormReport.php\">\n";
  $additionalinfo.=$search;
  $additionalinfo.="</FORM>\n";

  // Add the switch on track
  if (($trackid==0) or (!is_numeric($trackid))) {
    $trackidlist="";
  } else {
    $trackidlist=$trackid;
  }

  // Stop here if there wasn't a previous search using the "issearch" variable.
  if (!isset($_POST["issearch"])) {
    // Header, followed by search
    topofpagereport($title,$description,$additionalinfo,$message,$message_error);
    correct_footer();
    exit();
  }
}

if (!empty($_SERVER['QUERY_STRING'])) {
  $_SESSION['return_to_page']="BrainstormReport.php?".$_SERVER['QUERY_STRING'];
} else {
  $_SESSION['return_to_page']="BrainstormReport.php?status=all";
}

// Get the selected information for precis processing
$query=retrieve_select_from_db($trackidlist,$statusidlist,$typeidlist,$sessionidlist,$_SESSION['conid']);

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

/* Produce the report. */
$printstring=renderprecisreport(1,$elements,$header_array,$element_array);
echo $printstring;

correct_footer();
?>
