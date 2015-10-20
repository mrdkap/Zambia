<?php
require_once('StaffCommonCode.php');
global $link;

$conid=$_GET['conid'];
if ($conid=="") {$conid=$_POST['conid'];}
if ($conid=="") {$conid=$_SESSION['conid'];}

unset($doit);

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
$additionalinfo.="<P>There still needs to be an editor for the Generic Liaison Tasks, to be linked in the list above.</P>\n";

/* Get the form information */
if (isset ($_POST["doit"])) {
  $message.="Done:<br>";
  $doit=1;
}

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
    GROUP_CONCAT("<LI>",badgename,"</LI>\n" SEPARATOR "") as Presenters,
    GROUP_CONCAT(badgeid SEPARATOR ", ") as "Presenters Badgeid",
    compdescription as "Liaison Badgeid"
  FROM
      Compensation
    JOIN CompensationTypes USING (comptypeid)
    JOIN CongoDump USING (badgeid)
  WHERE
    conid=$conid and
    comptypename in ("Liaison")
  GROUP BY
    compdescription
  ORDER BY
    compamount
EOD;

// Retrieve query
list($liaisoncount,$liaison_headers,$liaison_array)=queryreport($query,$link,$title,$description,0);

/* Submission format
activityid, conid, badgeid, timestamp, activity, activitynotes, activitystart, targettime, donestate, donetime
Autoset: activityid, timestamp, donestate, donetime
conid comes from $conid
badgeid comes from $presenter_array['Liaison Badgeid']
activity, activitynotes, activitystart, and targetttime comes from $tasks
activitynotes postpended with $liaison_array['Presenters']
*/

$element_array = array('conid','badgeid','activity','activitynotes','activitystart','targettime');

for ($i=1; $i<=$liaisoncount; $i++) {
  for ($j=1; $j<=$taskscount; $j++) {
    $value_array=array($_SESSION['conid'],
		       $liaison_array[$i]['Liaison Badgeid'],
		       $tasks_array[$j]['Generic Activity List'],
		       $tasks_array[$j]['Generic Activity Notes'] . "<UL>\n" . $liaison_array[$i]['Presenters'] . "\n</UL>\n",
		       $tasks_array[$j]['Activity Start Time'],
		       $tasks_array[$j]['Activity Completion Time']);
    if (isset($doit)) {
      $message.=$liasion_array[$i]['Liaison'] . " - " . $tasks_array[$j]['Generic Activity List'] . " - ";
      $message.=submit_table_element($link,$title,"TaskList",$element_array, $value_array);
    }
  }
}
													  

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

echo "<P>Tasks to be replicated: ";
echo "<FORM name=\"populateliaisiontasksabove\" method=POST action=\"PopulateLiaisonTasks.php\">\n";
echo "<BUTTON type=\"submit\" name=\"doit\" value=\"doit\">Do It</BUTTON>\n";
echo "</FORM></P>\n";
echo renderhtmlreport(1,$taskscount,$tasks_headers,$tasks_array);
echo "<FORM name=\"populateliaisiontasksbelow\" method=POST action=\"PopulateLiaisonTasks.php\">\n";
echo "<BUTTON type=\"submit\" name=\"doit\" value=\"doit\">Do It</BUTTON>\n";
echo "</FORM>\n";

correct_footer();
?>