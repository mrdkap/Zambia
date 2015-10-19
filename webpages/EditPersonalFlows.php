<?php
require_once('StaffCommonCode.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}

// LOCALIZATIONS
$_SESSION['return_to_page']="EditPersonalFlows.php";
$title="Edit Personal Flow Reports";
$description="<P>Edit the order of your personal flow, generally and for each phase.</P>\n";
$additionalinfo="<P><A HREF=genreport.php?reportname=personalflow>Return</A> to your Personal Flow.</P>";
$mybadgeid=$_SESSION['badgeid'];

if (isset($_POST['addto'])) {
  add_flow_report($_POST['addto'],$_POST['addphase'],"$ReportDB.Personal","",$title,$description);
 }

if (isset($_POST['unrank'])) {
  remove_flow_report($_POST['unrank'],"$ReportDB.Personal",$title,$description);
 }

if (isset($_POST['upfrom'])) {
  deltarank_flow_report($_POST['upfrom'],"$ReportDB.Personal","Up",$title,$description);
 }

if (isset($_POST['downfrom'])) {
  deltarank_flow_report($_POST['downfrom'],"$ReportDB.Personal","Down",$title,$description);
 }

if (isset($_POST['newnote'])) {
  $note_array=array("pflownote='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST['newnote'])))."'");
  update_table_element ($link, $title, "$ReportDB.PersonalFlow", $note_array, "pflowid", $_POST['noteid']);
}

// Forms inserted into the query
$uprank_query ="concat('<FORM name=\"uprank\" method=POST action=\"EditPersonalFlows.php\">";
$uprank_query.="<INPUT type=\"hidden\" name=\"upfrom\" value=\"',pflowid,'\">";
$uprank_query.="<INPUT type=submit value=\"Move Up\">";
$uprank_query.="</FORM>') as Earlier,";
$downrank_query ="concat('<FORM name=\"downrank\" method=POST action=\"EditPersonalFlows.php\">";
$downrank_query.="<INPUT type=\"hidden\" name=\"downfrom\" value=\"',pflowid,'\">";
$downrank_query.="<INPUT type=submit value=\"Move down\">";
$downrank_query.="</FORM>') as Later,";
$addto_query ="concat('<FORM name=\"addto\" method=POST action=\"EditPersonalFlows.php\">";
$addto_query.="<INPUT type=\"hidden\" name=\"addto\" value=\"',reportid,'\">";
$addto_query.="<LABEL for=\"addphase\" ID=\"addphase\"></LABEL>";
$addto_query.="<INPUT type=\"text\" name=\"addphase\" size=\"1\">";
$addto_query.=" <INPUT type=submit value=\"Add\">";
$addto_query.="</FORM>') as 'Add To<BR>Phaseid #',";
$remove_query ="concat('<FORM name=\"unrank\" method=POST action=\"EditPersonalFlows.php\">";
$remove_query.="<INPUT type=\"hidden\" name=\"unrank\" value=\"',pflowid,'\">";
$remove_query.="<INPUT type=submit value=\"Remove\">";
$remove_query.="</FORM>') as Remove,";
$note_query ="concat('<FORM name=\"notemod\" method=POST action=\"EditPersonalFlows.php\">";
$note_query.="<INPUT type=\"hidden\" name=\"noteid\" value=\"',pflowid,'\">";
$note_query.="<INPUT type=\"text\" name=\"newnote\" value=\"',if ((pflownote is NULL),'',pflownote),'\">";
$note_query.="<INPUT type=submit value=\"Update Note\">";
$note_query.="</FORM>') as 'My Notes'";

// First table, list of phases and their phasetypeids
$conid=$_SESSION['conid'];
$query = <<<EOD
SELECT
    phasetypeid,
    concat(phasetypename,if ((phasestate=TRUE),' (c)',' ')) AS Phases
  FROM
    $ReportDB.PhaseTypes
  JOIN $ReportDB.Phase USING (phasetypeid)
  WHERE
    conid=$conid
  ORDER BY
    phasetypeid
EOD;

// Retrieve query
list($phaserows,$phaseheader_array,$phasereport_array)=queryreport($query,$link,$title,$description,0);

// Add the "all" entry, just in case.
$phaserows++;
$phasereport_array[$phaserows]['Phases']="ALL";

$query = <<<EOD
SELECT
  concat("<A HREF=genreport.php?reportid=",reportid,">",reporttitle,"</A> (<A HREF=genreport.php?reportid=",reportid,"&csv=y>csv</A>)") AS Title,
    $uprank_query
    $downrank_query
    $addto_query
    $remove_query
    pfloworder AS "Order #",
  if((phasetypeid IS NULL),'ALL',concat("(",phasetypeid,") ",phasetypename)) AS Phase,
    $note_query
  FROM
      $ReportDB.PersonalFlow
    JOIN $ReportDB.Reports USING (reportid)
    LEFT JOIN $ReportDB.PhaseTypes USING (phasetypeid)
    LEFT JOIN $ReportDB.Phase USING (phasetypeid)
  WHERE
    badgeid=$mybadgeid AND
    (conid is NULL or conid=$conid)
  ORDER BY
    phasetypename,pfloworder
EOD;

// Retrieve query
list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

// Page Rendering
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo renderhtmlreport(1,$phaserows,$phaseheader_array,$phasereport_array);
echo renderhtmlreport(1,$rows,$header_array,$report_array);
correct_footer();
?>
