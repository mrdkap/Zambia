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
$_SESSION['return_to_page']="FAQ.php";
$title="FAQ for $conname";
$description="";

// FAQ information (possibly including mapping instructions)
$faqinfo="";
if (file_exists("../Local/$conid/FAQ")) {
  $faqinfo.="\n<UL>\n";
  $faqinfo.=file_get_contents("../Local/$conid/FAQ");
  $faqinfo.="\n</UL>\n";
}

/* Printing body.  Uses the page-init then creates the FAQ page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo $faqinfo;
correct_footer();
?>