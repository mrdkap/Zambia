<?php
require_once('StaffCommonCode.php');
require ('RenderEditCreateSession.php');
global $link, $name, $email, $message, $message_error;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

get_name_and_email($name,$email);
$error=false;
$title="Edit Session";
$description="<P>Please, select the session you wish to edit.</P>";

if (isset($_GET["id"])) { // Sets the "id" from the GET string
  $id=$_GET["id"];
 } elseif (isset($_POST["id"])) { // Sets the "id" from the POST string
  $id=$_POST["id"];
 }

if ((is_numeric($id)) and ($id>0)) { // If the "id" is numeric and greater than one, test it
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
    may_be_scheduled=1 AND
    conid=$conid AND
    pubstatusname in ($pubstatus_string)
  ORDER BY
    trackname,
    sessionid,
    title
EOD;

  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo "<FORM name=\"idform\" method=POST action=\"EditSession.php\">\n";
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

// Set up for Rendering
$action="edit";

// Actually do the rendering work
RenderEditCreateSession($action,$session,$message,$message_error);
exit();
?>
