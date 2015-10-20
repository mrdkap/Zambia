<?php
require_once ('StaffCommonCode.php');
global $link;
$title="Migrate Participants";
$conid=$_SESSION['conid'];

// This is not called from anywhere, it might be depreciated.

// Assign the fromconid if it was passed in
$fromconid='';
if (isset($_POST['fromconid']) AND ($_POST['fromconid'] != '')) {$fromconid=$_POST['fromconid'];}
elseif (isset($_GET['fromconid']) AND ($_GET['fromdconid'] != '')) {$typename=$_GET['fromconid'];}

// If the "from" con is not picked, show the choose con interface and exit
if ($fromconid=='') {
  //Choose the con from the database
  $description ="<P>Choose the appropriate convention from which to migrate the participants.</P>\n";
  $query = "SELECT conid, conname from ConInfo";
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo "<FORM name=\"fromconid\" method=POST action=\"MigrateParticipants.php\">\n";
  echo "<DIV><LABEL for=\"fromconid\">Select Convention to migrate from:</LABEL>\n";
  echo "<SELECT name=\"fromconid\">\n";
  populate_select_from_query($query, $fromconid, " ", false);
  echo "</SELECT></DIV>\n";
  echo "<DIV><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
  echo "</FORM>\n";
  correct_footer();
  exit();
}

/* Check and update as necessary the Invited state, and the
   permissions state for the following badgeids.

   Need to build lookup for the "Invited" value from the
   InterestedTypes table.  Currently hard-set to 3=Invited.

   Need to also add the UserHasPermissionsSet check/update.
*/

$invited=3;

$workbadgeids_string="";
if (isset($_POST['updateinterest']) AND ($_POST['updateinterest'] != '')) {
  $workbadgeids_array=$_POST[updateinterest];
  $workbadgeids_string=implode(",",$workbadgeids_array);
  $element_array = array('conid', 'badgeid', 'interestedtypeid');
  foreach ($workbadgeids_array as $workbadgeid) {
    $query = "SELECT interestedtypeid FROM Interested WHERE conid=$conid AND badgeid=$workbadgeid";
    if (($result=mysql_query($query,$link))===false) {
      $message.="<P>Error retrieving data from database.</P>\n<P>";
      $message.=$query;
      RenderError($title,$message);
      exit ();
    }
    if (0==($rows=mysql_num_rows($result))) {
      $value_array = array($conid,$workbadgeid,$invited);
      print_r($value_array);
      $message.=submit_table_element($link, $title, "Interested", $element_array, $value_array);
    } else {
      $message.="<P>$workbadgeid has an interested state already.</P>";
    }
  }
}

$message.="Badgeids to check: $workbadgeids_string";

/* Get the checkbox, name, and notes for each user
   Need to generalize this to the perms of the individual, somehow.
   If they are in "SuperFoo" give them "SuperFoo and Foo"

   The checkbox should be checked (and report their badgeid) if they
   are already of the right UserHasPermissionRole, and interest is in
   the state of "Yes" or "Invited".
 */

//
// Hack to figure out previous database, until the notes are migrated properly
$NoteDB="nelaonli_FFF" . $fromconid . "Z";

$query = <<<EOD
SELECT
    if ((X.Interested is NULL),CONCAT("<INPUT type=\"checkbox\" name=\"updateinterest[]\" value=\"",badgeid,"\">"),X.Interested) AS 'Migrate',
																   CONCAT("<A HREF=\"StaffEditCreateParticipant.php?action=edit&partid=", badgeid, "\">", badgename, " - ", firstname, " ", lastname, "</A>") AS 'Name',
    GROUP_CONCAT(if ((note="Participant entry create"),
                     '',
                     if ((note="Participant entry edit"),
                         '',
                         if ((note="Reset password on request. -AND/OR- Updated attendance state to:"),
                             '',
                             note)
			 )
		     ) SEPARATOR " ") AS 'Note'
  FROM
      UserHasPermissionRole UHPR
    JOIN PermissionRoles USING (permroleid)
    JOIN CongoDump USING (badgeid)
    LEFT JOIN $NoteDB.NotesOnParticipants USING (badgeid)
    JOIN Interested USING (badgeid,conid)
    JOIN InterestedTypes USING (interestedtypeid)
    LEFT JOIN (
      SELECT
          badgeid,
          interestedtypename AS 'Interested'
        FROM
            Interested
          JOIN InterestedTypes USING (interestedtypeid)
        WHERE
          conid=$conid) X USING (badgeid)
  WHERE
    onid=$fromconid AND
    interestedtypename="Yes" AND
    (permrolename='Programming' OR
     permrolename='SuperProgramming')
  GROUP BY
    badgeid
EOD;

// Retrieve query
list($rows,$header_array,$participant_array)=queryreport($query,$link,$title,$description,0);

/* From here, it is just the page information.
   The begin page starts it.
   Selection of another con to migrate from.
   Verbiage that presents the "from"
*/
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo "<FORM name=\"fromconid\" method=POST action=\"MigrateParticipants.php\">\n";
echo "<DIV><LABEL for=\"fromconid\">Select another Convention to migrate from:</LABEL>\n";
echo "<SELECT name=\"fromconid\">\n";
$query = "SELECT conid, conname from ConInfo";
populate_select_from_query($query, $fromconid, " ", false);
echo "</SELECT></DIV>\n";
echo "<DIV><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
echo "</FORM>\n<HR>\n";
echo "<FORM name=\"migrateparticipant\" method=POST action=\"MigrateParticipants.php\">\n";
echo "<INPUT type=hidden name=update value=please>\n";
echo "<INPUT type=hidden name=fromconid value=$fromconid>\n";
echo "<P>Select the check-boxes of those you want to set to \"Invited\" for this year's con,";
echo " and make sure their permissions are set properly for their participation in your";
echo " department.  If they already have a \"Yes\" by their name, they are either already";
echo " \"Invited\" or a \"Yes\" for this year's con.</P>";
echo "<INPUT type=submit name=submit class=SubmitButton value=Update>\n";
echo renderhtmlreport(1,$rows,$header_array,$participant_array);
echo "<INPUT type=submit name=submit class=SubmitButton value=Update>\n";
echo "</FORM>\n";
correct_footer();
exit();
?>