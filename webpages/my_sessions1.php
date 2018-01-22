<?php
global $link,$participant,$message,$message_error,$congoinfo,$partAvail,$availability;
// initialize db, check login, set $badgeid from session
require_once('PartCommonCode.php');

// Localizations
$title="Search Panels";
$description="<P>On the following page, you can select panels for participation.  You must SAVE your changes before leaving the page or your selections will not be recorded.</P>\n";
$additionalinfo="<P>Clicking Search without making any selections will display all panels.</P>\n";

if (!may_I('search_panels')) {
  $message_error.="You do not currently have permission to view this page.<BR>\n";
  RenderError($title,$message_error);
  exit();
}
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>
<FORM method=POST action="SearchMySessionsScheduled.php">
<?php $search=RenderSearchSession(0,0,0,""); echo $search ?>
</FORM>
<?php correct_footer() ?>