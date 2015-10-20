<?php
require_once('StaffCommonCode.php');
global $link;
$conid=$_SESSION['conid'];

$title="Volunteer Check In";
$description="<P align=\"center\">Check in the Volunteer in question.</P>";
$additionalinfo = "<P align=\"center\"><A HREF=\"genreport.php?reportname=progvolexpected\">Programming</A> or <A HREF=\"genreport.php?reportname=genvolexpected\">General</A> Volunteer expected to be on.</A><br>";
$additionalinfo.= "<A HREF=\"VolunteerCheckOut.php\">Check Out</A> instead.</P>";

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

/* Need to add two things.  Interest=yes, and somehow check to see if
they aren't already logged in, without limititing it to those who have
already stood shifts. */

$query=<<<EOD
SELECT
    DISTINCT badgeid,
    pubsname 
  FROM
      Participants
    JOIN UserHasPermissionRole UHPR USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    conid=$conid AND
    permrolename in ($permrolecheck_string)
  ORDER BY
    pubsname
EOD;

if (isset($_POST['badgeid'])) {
  $badgeid=$_POST['badgeid'];
 } elseif (isset($_GET['badgeid'])) {
  $badgeid=$_GET['badgeid'];
 } else {
  $_SESSION['return_to_page']="VolunteerCheckIn.php";
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo "<FORM name=\"whichvol\" method=POST action=\"VolunteerCheckIn.php\">";
  echo "  <DIV style=\"text-align:center\">\n    <LABEL for=\"badgeid\">Volunteer: </LABEL>\n";
  echo "    <SELECT name=\"badgeid\">\n";
  populate_select_from_query($query,$_SESSION['badgeid'],"",false);
  echo "    </SELECT>\n  </DIV>\n";
  echo "  <DIV style=\"text-align:center\">\n";
  echo "    <BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButtion\">Choose</BUTTON>\n";
  echo "  </DIV>\n</FORM>\n";
  correct_footer();
  exit();
 }

if (isset($_POST['voltimein'])) {
  $element_array = array('conid', 'badgeid', 'voltimein', 'volinbadgeid');
  $value_array=array($_SESSION['conid'],$badgeid,$_POST['voltimein'],$_SESSION['badgeid']);
  $message.=submit_table_element($link, $title, "TimeCard", $element_array, $value_array);
  if (isset($_POST['onepageprog'])) {
    header("Location: genreport.php?reportname=progvolexpected"); // Redirect back to the progvolexpected report
  } elseif (isset($_POST['onepagegen'])) {
    header("Location: genreport.php?reportname=genvolexpected"); // Redirect back to the progvolexpected report
  } elseif (isset($_SESSION['return_to_page'])) {
    header("Location: ".$_SESSION['return_to_page']); // Redirect back to what send you here
  } else {
    header("Location: VolunteerCheckIn.php"); // Redirect back to here, with a blank slate
  }
  /* topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo "<P>Database: TimeCard</P>\n";
  correct_footer(); */
  exit();
 }

$query=<<<EOD
SELECT
    pubsname 
  FROM
      Participants
  WHERE
    badgeid='$badgeid'
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

$pubsname=$element_array[1]['pubsname'];

topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>
<FORM name="volcheckin" method=POST action="VolunteerCheckIn.php">
  <INPUT type="hidden" name="badgeid" value="<?php echo $badgeid; ?>">
  <INPUT type="hidden" name="voltimein" value="<?php echo date('Y-m-d H:i:s'); ?>">
  <DIV style="text-align:center">
    <BUTTON class="SubmitButtion" type="submit" name="submit">Check in <?php echo "$pubsname"; ?> now.</BUTTON>
  </DIV>
</FORM>
<hr>
<FORM name="volfixcheckin" method=POST action="VolunteerCheckIn.php">
  <INPUT type="hidden" name="badgeid" value="<?php echo $badgeid; ?>">
  <DIV style="text-align:center">
    <LABEL for="voltimein">Actual start time for <?php echo "$pubsname: " ?></LABEL>
    <INPUT type="text" size="20" name="voltimein" value="<?php echo date('Y-m-d H:i:s'); ?>"
  </DIV>
  <DIV style="text-align:center">
    <BUTTON class="SubmitButton" type="submit" name="submit">Check in <?php echo "$pubsname"; ?> as of the above date.</BUTTON>
  </DIV>
</FORM>
<?php
correct_footer();
?>