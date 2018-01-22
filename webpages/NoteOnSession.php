<?php
$title="Notes On Session";
require_once('StaffCommonCode.php');
global $link, $message, $message_error;

$conid=$_GET['conid'];

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

if (isset($_GET["id"])) { // Sets the "id" from the GET string
  $id=$_GET["id"];
 } elseif (isset($_POST["id"])) { // Sets the "id" from the POST string
  $id=$_POST["id"];
 }

if ((is_numeric($id)) and ($id>0)) { // If the "id" is numerica and greater than one, test it
  $status=retrieve_session_from_db($id,$conid);
  if ($status==-3) {
    $message_error.="Error retrieving record from database.";
    $error=true;
    $id="";
   }
  if ($status==-2) {
    $message_error.="Session record with id=".$id." not found (or error with Session primary key).";
    $error=true;
    $id="";
   }
 }

// If the "id" still is not set, or reset to "", add the "Select" to the top of the form, so it can be chosen.
if ((!isset($id)) or ($id=="")) {

  // Set the description to a select version
  $description="<P>Please, select the session you wish to add notes on.</P>";
  // Limit it to just the appropriate set of schedule elements presented
  if (may_I("Programming")) {$pubstatus_array[]="'Prog Staff'"; $pubstatus_array[]="'Public'";}
  if (may_I("Liaison")) {$pubstatus_array[]="'Public'";}
  if (may_I("General")) {$pubstatus_array[]="'Volunteer'";}
  if (may_I("Event")) {$pubstatus_array[]="'Event Staff'";}
  if (may_I("Registration")) {$pubstatus_array[]="'Reg Staff'";}
  if (may_I("Watch")) {$pubstatus_array[]="'Watch Staff'";}
  if (may_I("Vendor")) {$pubstatus_array[]="'Vendor Staff'";}
  if (may_I("Sales")) {$pubstatus_array[]="'Sales Staff'";}
  if (may_I("Fasttrack")) {$pubstatus_array[]="'Fast Track'";}
  if (may_I("Logistics")) {$pubstatus_array[]="'Logistics'"; $pubstatus_array[]="'Public'";}

  if (isset($pubstatus_array)) {
    $pubstatus_string=implode(",",$pubstatus_array);
  } else {
    $pubstatus_string="'Public'";
  }

  $query=<<<EOD
SELECT
    sessionid,
    concat(trackname,' - ',sessionid,' - ',title) as sname
  FROM
      Sessions
    JOIN Tracks USING (trackid)
    JOIN SessionStatuses USING (statusid)
    JOIN PubStatuses USING (pubstatusid)
  WHERE
    conid=$conid AND
    statusname='Brainstorm' AND
    pubstatusname in ($pubstatus_string)
  ORDER BY
    trackname,
    sessionid,
    title
EOD;


  topofpagereport($title,$description,$additionalinfo,$message,$message_error);

  echo "<FORM name=\"idform\" method=POST action=\"NoteOnSession.php\">\n";
  echo "<DIV><LABEL for=\"id\">Select Session</LABEL>\n";
  echo "<SELECT name=\"id\">\n";
  populate_select_from_query($query, $sessionid, "Select Session", false);
  echo "</SELECT></DIV>\n";
  echo "<P>&nbsp;\n";
  echo "<DIV class=\"SubmitDiv\">";
  if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
  }
  echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Session</BUTTON></DIV>\n";
  echo "</FORM>\n";
  correct_footer();
  exit();
}

// Submit the note, if there was one, when this was called
if (isset($_POST["note"])) {
  $element_array = array('sessionid','conid','badgeid','sessnote');
  $value_array = array($id,$conid,$_SESSION['badgeid'],$_POST["note"]);
  $message.=submit_table_element($link,$title,"NotesOnSessions",$element_array, $value_array);
 }

$description="<P><B>This is outdated.  Don't use this until NotesOnSessions have been fully migrated to VotesOnSessions.</B></P>\n";
$description.="<P>The system knows who you are, so please, don't put your name in the \"Note\" field.  Simply put the number in the Note field, and then hit update.  Once that's done, please hit the \"Return To Report\" link, below.</P>";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if (isset($_SESSION['return_to_page'])) {
  echo "<A HREF=\"".$_SESSION['return_to_page']."#$id\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
}
// Add note through form below
?>

<HR>
<BR>
<FORM name="sessnoteform" method=POST action="NoteOnSession.php">
<INPUT type="hidden" name="id" value="<?php echo $id; ?>">
<DIV class="titledtextarea">
  <LABEL for="note">Note:</LABEL>
  <TEXTAREA name="note" rows=6 cols=72></TEXTAREA>
</DIV>
<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php
// Show previous notes added, for references, and end page
$query = <<<EOD
SELECT
    conid as "Con",
    descriptiontext as 'Title',
    notesforpart as 'Presenter',
    concat(pubsname,": ",sessnote) as 'Note'
  FROM
      NotesOnSessions
    JOIN Participants USING (badgeid)
    JOIN Descriptions USING (sessionid,conid)
    JOIN Sessions USING (sessionid,conid)
  WHERE
    sessionid=$id AND
    descriptiontypeid=1 AND
    biostateid=3 AND
    biodestid=1 AND
    descriptionlang='en-us'
  ORDER BY
    timestamp
EOD;

list($rows,$header_array,$notes_array)=queryreport($query,$link,"Note on Session","","");
echo renderhtmlreport(1,$rows,$header_array,$notes_array);
correct_footer();
?>
