<?php
require_once('PostingCommonCode.php');
global $link;
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStartDatim=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

// LOCALIZATIONS
$_SESSION['return_to_page']="CuratedComments.php";
$title="Comments and Feedback from $conname";
$description="<P>Some of the feedback and comments people have offered up about $conname:</P>";

// Feedback that have been selected to be put up.
$verbiage=get_verbiage("Curated_Comments");
if ($verbiage != "") {
  ob_start();
  eval ('?>' . $verbiage);
  $feedbackfile.=ob_get_clean();
}

/* Printing body.  Uses the page-init then creates the comments/feedback page. */
if ($included!="YES") {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo $feedbackfile;
  correct_footer();
}
?>
