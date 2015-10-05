<?php
require_once('StaffCommonCode.php');
global $link;

$conid=$_GET['conid'];
if ($conid=="") {$conid=$_POST['conid'];}
if ($conid=="") {$conid=$_SESSION['conid'];}

// LOCALIZATIONS
$_SESSION['return_to_page']="PopulateLiaisonTasks.php";
$title="Populate Liaison Tasks";
$description="<P>Create appropriate Liaison tasks for each Liaison and Presenter appropriate in the Compensation table.</P>\n";
$additionalinfo="<P>Useful links:\n<UL>\n";
if (!may_I("SuperLiaison") AND !may_I("Treasurer")) {
  $additionalinfo.="  <LI><A HREF=PresenterCompensation.php>Compensation Table</A></LI>\n";
}
$additionalinfo.="  <LI><A HREF=genreport.php?reportname=myliaisonresponsibilities>My Liaison Responsibilities</A></LI>\n";
$additionalinfo.="  <LI><A HREF=genreport.php?reportname=mytasklistdisplay>My Task List</A></LI>\n";
$additionalinfo.="  <LI><A HREF=genreport.php?reportname=conflictassignednocompensation>Missing from Compensation table</A></LI>\n";
$additionalinfo.="</UL></P>\n";

/* Get the form information */
if ((isset ($_POST["presenterbadgeid"])) and (is_numeric($_POST["presenterbadgeid"]))) {
  $update_badgeid=$_POST["presenterbadgeid"];
}

if ((isset ($_POST["comliactivityid"])) and (is_numeric ($_POST["comliactivityid"]))) {
  $update_activityid=$_POST["comliactivityid"];
}

if (isset ($update_badgeid)) {
  $element_array=array('conid','badgeid','comliactivityid','completedate');
  $value_array=array($_SESSION['conid'],
		     $update_badgeid,
		     $update_activityid,
		     date('Y-m-d'));
  $message.=submit_table_element($link,$title,"ComLiTasksStatus",$element_array,$value_array);
  $message.="<P>Update: $update_badgeid and $update_activityid</P>\n";
}

/* Submission format
activityid, conid, badgeid, timestamp, activity, activitynotes, activitystart, targettime, donestate, donetime
Autoset: activityid, timestamp, donestate, donetime
conid comes from $conid
badgeid comes from $presenter_array['Liaison Badgeid']
activity, activitynotes, activitystart, and targetttime comes from $tasks
Activity, or activitynotes should start with $preseenter_array['Presenter']
*/

/* Get the common tasks */
$query = <<<EOD
SELECT
    comliactivityid as "Generic Activity ID",
    comliactivity as "Generic Activity List",
    comliactivitynotes as "Generic Activity Notes",
    comliactivitystart as "Activity Start Time",
    comlitargettime as "Activity Completion Time"
  FROM
      CommonLiaisonTasks
  WHERE
    conid=$conid
EOD;

// Retrieve query
list($taskscount,$tasks_headers,$tasks_array)=queryreport($query,$link,$title,$description,0);

/*Map presenters to their liaison*/
$query = <<<EOD
SELECT
    compamount as Liaison,
    badgename as Presenter,
    badgeid as PresenterBadgeid,
    compdescription as "Liaison Badgeid"
  FROM
      Compensation
    JOIN CompensationTypes USING (comptypeid)
    JOIN CongoDump USING (badgeid)
  WHERE
    conid=$conid and
    comptypename in ("Liaison")
  ORDER BY
    compamount
EOD;

// Retrieve query
list($presentercount,$presenter_headers,$presenter_array)=queryreport($query,$link,$title,$description,0);

$query = <<<EOD
SELECT 
    badgeid,
    comliactivityid,
    completedate
  FROM
      ComLiTasksStatus
  WHERE
    conid=$conid;
EOD;

// Retrieve query
list($completioncount,$completion_headers,$completion_array)=queryreport($query,$link,$title,$description,0);

for ($i=1; $i<=$completioncount; $i++) {
  $isdone[$completion_array[$i]['badgeid']][$completion_array[$i]['comliactivityid']]=$completion_array[$i]['completedate'];
}

for ($i=1; $i<=$presentercount; $i++) {
  for ($j=1; $j<=$taskscount; $j++) {
    if (isset ($isdone[$presenter_array[$i]['PresenterBadgeid']][$tasks_array[$j]['Generic Activity ID']])) {
      $donestate[$i][$j]=$isdone[$presenter_array[$i]['PresenterBadgeid']][$tasks_array[$j]['Generic Activity ID']];
    } else {
      $donestate[$i][$j]="<FORM name=\"LiaisonTask$i$j\" method=POST action=\"PopulateLiaisonTasks.php\">\n";
      $donestate[$i][$j].="<INPUT type=\"hidden\" name=\"presenterbadgeid\" value=\"".$presenter_array[$i]["PresenterBadgeid"]."\">\n";
      $donestate[$i][$j].="<INPUT type=\"hidden\" name=\"comliactivityid\" value=\"".$tasks_array[$j]["Generic Activity ID"]."\">\n";
      $donestate[$i][$j].="<INPUT type=\"submit\" name=\"submit\" value=\"No\">\n";
      $donestate[$i][$j].="</FORM>\n";
    }
  }
}

topofpagereport($title,$description,$additionalinfo);
if ($message_error!="") { 
  echo "<P class=\"errmsg\">$message_error</P>";
}
if ($message!="") {
  echo"<P class=\"regmsg\">$message</P>";
}
echo "<P>Tasks to be replicated:</P>\n";
echo renderhtmlreport(1,$taskscount,$tasks_headers,$tasks_array);
echo "<P>Presenter/Liaison mapping</P>\n";
/*echo renderhtmlreport(1,$presentercount,$presenter_headers,$presenter_array); */
$liaison="NONE";
echo "<TABLE>\n";
for ($i=1; $i<=$presentercount; $i++) {
  if ($liaison!=$presenter_array[$i]["Liaison"]) {
    $liaison=$presenter_array[$i]["Liaison"];
    echo "</TABLE>\n";
    echo "<P>Liaison: $liaison</P>\n";
    echo "<TABLE border=1>\n";
    echo "  <TR><TH>$liaison</TH>\n";
    for ($j=1; $j<=$taskscount; $j++) {
      echo "    <TH>";
      echo $tasks_array[$j]["Generic Activity List"];
      echo "</TH>\n";
    }
    echo "  </TR>\n";
  }
  echo "  <TR><TH>";
  echo $presenter_array[$i]["Presenter"];
  echo "</TH>";
  for ($j=1; $j<=$taskscount; $j++) {
    echo "    <TD>";
    echo $donestate[$i][$j];
    echo "</TD>\n";
  }
}
echo "</TABLE>\n";
correct_footer();
?>