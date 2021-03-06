<?php
require_once('StaffCommonCode.php');
$conid=$_SESSION['conid'];

$title="Task List Update";
$description="Return to <A HREF=\"genreport.php?reportname=alltasklistdisplay\">all events</A>,\n";
$description.="<A HREF=\"genreport.php?reportname=tasklistdisplay\">this event's</A>,\n";
$description.="the <A HREF=\"gantt_relative.php\">Gantt Chart</A>, or\n";
$description.="<A HREF=\"genreport.php?reportname=mytasklistdisplay\">my</A> task list report</P>\n";

// Submit the task, if there was one, when this was called
if (isset($_POST["activitynotes"])) {
  if ($_POST["activityid"] == "-1") {
    $element_array=array('conid','activity','activitynotes','badgeid','activitystart','targettime','donestate');
    $value_array=array($_SESSION['conid'],$_POST['activity'],$_POST['activitynotes'],$_POST['assignedid'],$_POST['activitystart'],$_POST['targettime'],"N");
    //    $value_array=array($_POST['activity'],$_POST['activitynotes'],"123",$_POST['activitystart'],$_POST['targettime'],"N");
    $message.=submit_table_element($link, $title, "TaskList", $element_array, $value_array);
  } else {
    if ($_POST['donestate']=="Y") {
      if (isset($_POST['donetime'])) {
	$donetime=",donetime='".$_POST['donetime']."'";
      } else {
	$donetime=",donetime=CURRENT_DATE";
      }
    } else { $donetime=""; }
    $pairedvalue_array=array("conid='".$_SESSION['conid']."'","activitynotes='".$_POST['activitynotes']."'","badgeid='".$_POST['assignedid']."'","activitystart='".$_POST['activitystart']."'","targettime='".$_POST['targettime']."'","donestate='".$_POST['donestate']."'".$donetime);
    $match_field="activityid";
    $match_value=$_POST['activityid'];
    $message.=update_table_element($link, $title, "TaskList", $pairedvalue_array, $match_field, $match_value);
  }
 }

// Carry over the task list element, from the form before, if they exist
if (isset($_POST["activityid"])) {
  $activityid=$_POST["activityid"];
 }
 elseif (isset($_GET["activityid"])) {
  $activityid=$_GET["activityid"];
 }
 else {
   $activityid=0;
 }

$query=<<<EOD
SELECT
    activityid,
    concat(activity," (",conname,")") AS Activity
  FROM
      TaskList
    JOIN ConInfo USING (conid)
  ORDER BY
     activityid

EOD;

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>

<FORM name="tasklistselect" method=POST action="TaskListUpdate.php">
  <DIV><LABEL for="activityid">Select Task</LABEL>
    <SELECT name="activityid">
      <?php populate_select_from_query($query,$activityid,"Select task",false); ?>
    </SELECT>
  </DIV>
<BUTTON class="SubmitButton" type="submit" name="submit" >Submit</BUTTON>
</FORM>

<?php
// Stop page here if and individual has not yet been selected
if ($activityid==0) {
  correct_footer();
  exit();
 }

/* Get all the Permission Roles */
$query = <<<EOD
SELECT
    permrolename,
    notes
  FROM
      PermissionRoles
  WHERE
    permroleid > 1
EOD;

list($permrole_rows,$permrole_header_array,$permrole_array)=queryreport($query,$link,"Broken Query",$query,0);

// Empty Title Switch to begin with.
$TitleSwitch="";

/* Attempt to establish default graph based on permissions */
for ($i=1; $i<=$permrole_rows; $i++) {
  if (may_I($permrole_array[$i]['permrolename'])) {
    $permrolecheck_array[]="'".$permrole_array[$i]['permrolename']."'";
   }
 }
$permrolecheck_string=implode(",",$permrolecheck_array);

$Pquery=<<<EOD
SELECT
    badgeid,
  CONCAT(pubsname, " (", GROUP_CONCAT(permrolename SEPARATOR ", "), ")") AS Participant
  FROM
      Participants
    JOIN UserHasPermissionRole UHPR USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    UHPR.conid=$conid AND
    permrolename in ($permrolecheck_string)
  GROUP BY
    pubsname
EOD;

if ($activityid==-1) {
  // Update note through form below

?>
<hr>
<FORM name="tasklistform" method=POST action="TaskListUpdate.php">
  <DIV class="titledtextarea">
    <INPUT type="hidden" name="activityid" value="<?php echo $activityid; ?>">
    <LABEL for="assignedid">Person assigned:</LABEL>
    <SELECT name="assignedid">
      <?php populate_select_from_query($Pquery,$_SESSION['badgeid'],"",false); ?>
    </SELECT>
    <LABEL for"activity">Task:</LABEL>
    <INPUT type="text" size="25" name="activity" id="activity">
    <LABEL for="activitynotes">Note:</LABEL>
    <TEXTAREA name="activitynotes" rows=6 cols=72><?php echo $activitynotes ?></TEXTAREA>
    <LABEL for="activitystart">Targeted Start Time: (eg: 2038-10-12)</LABEL>
    <INPUT type="text" size=10 name="activitystart" id="activitystart" value="<?php echo date("Y-m-d") ?>">
    <LABEL for="targettime">Targeted Completion Time: (eg: 2038-12-12)</LABEL>
    <INPUT type="text" size=10 name="targettime" id="tartettime" value="<?php echo date("Y-m-d", mktime(0,0,0,date("m")+1,date("d"),date("Y"))) ?>">
  </DIV>
  <BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php } else {

  // Get the selected note information

  $query= <<<EOD
SELECT
    concat(activity," (",conname,")") AS Activity,
    activitynotes,
    badgeid,
    activitystart,
    targettime,
    donestate,
    donetime,
    depends
  FROM
      TaskList
  LEFT JOIN (SELECT
	H.activityid,
	H.conid,
	GROUP_CONCAT("<A HREF=TaskListUpdate.php?activityid=",T.activityid,">",activity,"</A>" SEPARATOR ", ") AS depends
      FROM
	  HasDependencies H
	JOIN TaskList T ON (H.hasdependency=T.activityid and H.conid=T.conid)
      GROUP BY
        H.activityid) HD USING (activityid,conid)
    JOIN ConInfo USING (conid)
  WHERE
    activityid='$activityid'

EOD;

  list($rows,$header_array,$task_array)=queryreport($query,$link,$title,$description,0);

  $activity=$task_array[1]['Activity'];
  $activitynotes=$task_array[1]['activitynotes'];
  $assignedid=$task_array[1]['badgeid'];
  $activitystart=$task_array[1]['activitystart'];
  $targettime=$task_array[1]['targettime'];
  $donestate=$task_array[1]['donestate'];
  $donetime=$task_array[1]['donetime'];
  if (!empty($task_array[1]['depends'])) {
    $depends=$task_array[1]['depends'];
  } else {
    $depends="None Listed";
  }

  // Update note through form below
?>
<hr>
<FORM name="tasklistform" method=POST action="TaskListUpdate.php">
  <INPUT type="hidden" name="activityid" value="<?php echo $activityid; ?>">
  <DIV class="titledtextarea">
    <LABEL for="assigned">Person assigned:</LABEL>
    <SELECT name="assignedid">
      <?php populate_select_from_query($Pquery,$assignedid,"Outside your assignment list.",true); ?>
    </SELECT>
    <LABEL for"activity">Task:</LABEL><?php echo $activity ?>
    <LABEL for="activitynotes">Note:</LABEL>
    <TEXTAREA name="activitynotes" rows=6 cols=72><?php echo $activitynotes ?></TEXTAREA>
    [<A HREF="TaskDependency.php?activityid=$activityid">update</A>]
    <B>Dependencies:</B> <?php echo $depends ?><br>
    <LABEL for="activitystart">Targeted Start Time: (eg: 2038-10-12)</LABEL>
    <INPUT type="text" size=10 name="activitystart" id="activitystart" value="<?php echo htmlspecialchars($activitystart) ?>">
    <LABEL for="targettime">Targeted Completion Time: (eg: 2038-12-12)</LABEL>
    <INPUT type="text" size=10 name="targettime" id="targettime" value="<?php echo htmlspecialchars($targettime) ?>">
    <?php if ($donestate=="Y") { ?>
    <LABEL for="finished">Finished at:</LABEL><?php echo $donetime; ?>
    <INPUT type="hidden" name="donetime" value="<?php echo $donetime ?>">
    <INPUT type="hidden" name="donestate" value="<?php echo $donestate ?>">
    <?php } else { ?>
    <LABEL for="finished">Is it done?</LABEL>
    <INPUT type="radio" name="donestate" id="donestate" value="Y" <?php if ($donestate=="Y") {echo "checked";} ?>> Yes, it is finished.<br>
    <INPUT type="radio" name="donestate" id="donestate" value="P" <?php if ($donestate=="P") {echo "checked";} ?>> It is partially done.<br>
    <INPUT type="radio" name="donestate" id="donestate" value="N" <?php if ($donestate=="N") {echo "checked";} ?>> It has not yet been begun.<br>
    <?php } ?>
  </DIV>
  <BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php
 }
correct_footer();
?>
