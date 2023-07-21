<?php
require_once('StaffCommonCode.php');
global $link;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// LOCALIZATIONS
$gflowname=$_GET["gflowname"];
$_SESSION['return_to_page']="genindex.php";
$title="General index Generator";
$description="<P>If you are seeing this, something failed trying to get index: $gflowname.</P>\n";
$additionalinfo="";

// No reportid, load the all-reports page
if (!$gflowname) {
  $title="List of all indicies";
  $description="<P>Here is a list of all the indicies that are available to be generated.</P>\n";
  $additionalinfo="<P>If a Div/Area Head would like any of their reports tweaked, email to ".$_SESSION['programemail']." and let us know.</P>\n";
  $query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genindex.php?gflowname=",gflowname,">",gflowname," Reports</A>") AS Indicies,
    gflowname
  FROM
      GroupFlow
  ORDER BY
    gflowname
EOD;
  // Retrieve query
  list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

  // Hand-add "All Reports", "Search", "My Flow", "Edit", and "Grids" entry for now.
  $header_array[2]='Tools';
  $report_array[1]['Tools']="<A HREF=genreport.php>All Reports</A>";
  $report_array[2]['Tools']="<A HREF=genreport.php?reportname=personalflow>My Flow</A>";
  $report_array[3]['Tools']="<A HREF=grid.php>Default Grid</A>";
  $report_array[4]['Tools']="<A HREF=manualGRIDS.php>All Grids</A>";
  $report_array[5]['Tools']="<A HREF=ScheduledAndCheckInTimesGraph.php>Sched/Check In</A>";
  $report_array[6]['Tools']="<A HREF=ScheduledAndAvailabilityTimesGraph.php>Sched/Avail</A>";
  $report_array[7]['Tools']="<A HREF=searchreport.php>Search</A>";
  $report_array[8]['Tools']="<A HREF=EditGroupFlows.php>Edit</A>";

  // Page Rendering
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo renderhtmlreport(1,$rows,$header_array,$report_array);
  correct_footer();
 } else {

  $title="$gflowname Reports";
  $description="<P>Here is a list of all the $gflowname reports that are available to be generated during this phase.</P>\n";
  $query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genreport.php?reportid=",R.reportid,">",R.reportid," - ",R.reporttitle,"</A> (<A HREF=genreport.php?reportid=",R.reportid,"&csv=y>csv</A>)") AS Title,
    R.reportdescription AS Description
  FROM
      GroupFlow GF,
      Reports R,
      Phase P
  WHERE
    GF.reportid=R.reportid AND
    GF.gflowname='$gflowname' AND
    (GF.phasetypeid is NULL OR (GF.phasetypeid = P.phasetypeid AND P.phasestate = TRUE AND conid=$conid))
  ORDER BY
    GF.gfloworder
EOD;

  // Retrieve query
  list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

  // Page Rendering
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo renderhtmlreport(1,$rows,$header_array,$report_array);
  correct_footer();
 }
?>
