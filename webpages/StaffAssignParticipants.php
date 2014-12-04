<?php
require_once('StaffCommonCode.php');
require_once('StaffAssignParticipants_FNC.php');
$conid=$_SESSION['conid'];
$title="Staff - Assign Participants";
$description="<P>Assign a participant to a Session Element.  Click on the element name to modify the element.</P>\n";

topofpagereport($title,$description,$additionalinfo);

if (isset($_POST["numrows"])) {
  SubmitAssignParticipants();
}

if (isset($_POST["selsess"])) { // room was selected by a form
  $selsessionid=$_POST["selsess"];
} elseif (isset($_GET["selsess"])) { // room was select by external page such as a report
  $selsessionid=$_GET["selsess"];
} else {
  $selsessionid=0; // room was not yet selected.
  unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
}

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
    pubstatusname in ($pubstatus_string) AND
    conid=$conid
  ORDER BY
    trackname,
    sessionid,
    title
EOD;

echo "<FORM name=\"selsesform\" method=POST action=\"StaffAssignParticipants.php\">\n";
echo "<DIV><LABEL for=\"selsess\">Select Session</LABEL>\n";
echo "<SELECT name=\"selsess\">\n";
populate_select_from_query($query,$selsessionid, "Select Session", false);
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\">";
if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
    }
echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Session</BUTTON></DIV>\n";
echo "</FORM>\n";
echo "<HR>&nbsp;<BR>\n";
if ($selsessionid==0) {
    correct_footer();
    exit();
    }

$query = <<<EOD
SELECT 
    concat(title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS title,
    desc_good_web,
    desc_good_book,
    persppartinfo,
    notesforpart,
    notesforprog,
    pubsname,
    statusid
  FROM
      Sessions
    JOIN Participants ON (suggestor=badgeid)
    JOIN (SELECT
        sessionid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='title' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') TGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='subtitle' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') SGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='description' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') DGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='description' AND
	  biostatename='good' AND
	  biodestname='book' AND
	  descriptionlang='en-us') DGB USING (sessionid)
  WHERE
    sessionid=$selsessionid AND
    conid=$conid
EOD;
if (!$result=mysql_query($query,$link)) {
    $message_error=$query."Error querying database. Unable to continue.";
    RenderError($title,$message_error);
    exit();
    }
if (mysql_num_rows($result)==0) {
    $message_error="Zero rows returned, this is either a removed class, or a not-yet-created one.  Please select another session above.";
    RenderError($title,$message_error);
    exit();
    }
if (mysql_num_rows($result)!=1) {
    $message_error=$query."returned unexpected number of rows (1 expected).";
    RenderError($title,$message_error);
    exit();
    }
echo "<H2>$selsessionid - <A HREF=\"EditSession.php?id=".$selsessionid."\">".htmlspecialchars(mysql_result($result,0,"title"))."</A></H2>";    
echo "<P>Web Program Text\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"desc_good_web"));
echo "\n";
echo "<P>Program Book Text\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"desc_good_book"));
echo "\n";
echo "<P>Prospective Participant Info\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"persppartinfo"));
echo "\n";
echo "<P>Notes for Participant\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"notesforpart"));
echo "\n";
echo "<P>Suggestor\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"pubsname"));
echo "\n";
echo "<P>Notes for Program Staff\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"notesforprog"));
echo "\n";
echo "<HR>\n";
$statusid=mysql_result($result,0,"statusid");

$query = <<<EOD
SELECT
    POS.badgeid AS posbadgeid,
    PSI.badgeid AS psibadgeid,
    moderator,
    volunteer,
    introducer,
    aidedecamp,
    R.badgeid,
    pubsname,
    rank,
    willmoderate,
    obadgeid,
    ibadgeid,
    psits,
    R.conid,
    comments
  FROM
      Participants
      JOIN (SELECT
                distinct badgeid,
	        sessionid,
	        conid
              FROM
                  (SELECT
                       badgeid,
		       sessionid,
		       conid
                     FROM
                         ParticipantOnSession
                     WHERE
                       sessionid=$selsessionid AND
                       conid=$conid
                   UNION
                   SELECT
                       badgeid,
		       sessionid,
		       conid
                     FROM
                         ParticipantSessionInterest
                     WHERE
                       sessionid=$selsessionid AND
                       conid=$conid) AS R2) AS R USING (badgeid)
    LEFT JOIN ParticipantSessionInterest PSI ON (R.badgeid=PSI.badgeid AND R.sessionid=PSI.sessionid AND R.conid=PSI.conid)
    LEFT JOIN ParticipantOnSession POS ON (R.badgeid=POS.badgeid AND R.sessionid=POS.sessionid and R.conid=POS.conid)
  WHERE
    R.conid=$conid AND
    (R.sessionid=$selsessionid OR
     R.sessionid is null)
  ORDER BY
    psits,
    pubsname
EOD;
if (!$result=mysql_query($query,$link)) {
    $message_error=$query."<BR>Error querying database. Unable to continue.<BR>";
    RenderError($title,$message_error);
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

$additional_permission_array=array('SuperProgramming', 'SuperLiaison', 'Liaison');

$volintaid_p=0;
foreach ($additional_permission_array as $perm) {
  if (may_I($perm)) {
    $permrolecheck_array[]="'Participant'";
    $volintaid_p++;
  }
}

$permrolecheck_string=implode(",",$permrolecheck_array);

$participant_query = <<<EOD
SELECT
    DISTINCT(badgeid),
    concat(pubsname, ' - ', badgeid) AS Pubsname,
    lastname
  FROM
      Participants
    JOIN CongoDump USING(badgeid)
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
    JOIN Interested USING (badgeid,conid)
    JOIN InterestedTypes USING (interestedtypeid)
  WHERE
    interestedtypename in ('Yes') AND
    conid=$conid AND
    permrolename in ($permrolecheck_string) AND
    badgeid not in (SELECT
                          badgeid
                        FROM
                            ParticipantSessionInterest
                        WHERE
                          sessionid=$selsessionid AND
		          conid=$conid)
  ORDER BY
    pubsname
EOD;

$i=0;
$modid=0;
$volid=0;
$intid=0;
while ($bigarray[$i] = mysql_fetch_array($result, MYSQL_ASSOC)) {
  if (($bigarray[$i]["moderator"]=="1") or ($bigarray[$i]["moderator"]=="Yes")) {
    $modid=$bigarray[$i]["badgeid"];
  }
  if (($bigarray[$i]["volunteer"]=="1") or ($bigarray[$i]["volunteer"]=="Yes")) {
    $volid=$bigarray[$i]["badgeid"];
  }
  if (($bigarray[$i]["introducer"]=="1") or ($bigarray[$i]["introducer"]=="Yes")) {
    $intid=$bigarray[$i]["badgeid"];
  }
  $i++;
}
$numrows=$i; 
echo "<FORM name=\"selsesform\" method=POST action=\"StaffAssignParticipants.php\">\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Update</BUTTON></DIV>\n";
echo "<INPUT type=\"radio\" name=\"moderator\" id=\"moderator\" value=\"0\"".(($modid==0)?"checked":"").">";
echo "<LABEL for=\"moderator\">No Moderator Selected</LABEL><br>";
echo "<INPUT type=\"radio\" name=\"volunteer\" id=\"volunteer\" value=\"0\"".(($volid==0)?"checked":"").">";
echo "<LABEL for=\"volunteer\">No Volunteer Assigned</LABEL><br>";
echo "<INPUT type=\"radio\" name=\"introducer\" id=\"introducer\" value=\"0\"".(($intid==0)?"checked":"").">";
echo "<LABEL for=\"introducer\">No Introducer Assigned</LABEL>";
echo "<TABLE>\n";
for ($i=0;$i<$numrows;$i++) {
    echo "   <TR>\n";
    echo "      <TD class=\"vatop\">\n";
    echo "        <INPUT type=\"checkbox\" name=\"asgn".$bigarray[$i]["badgeid"]."\" ";
    echo (($bigarray[$i]["posbadgeid"])?"checked":"")." value=\"1\">\n";
    echo "        <LABEL for=\"asgn\">Assigned</LABEL></TD>\n";
    echo "        <INPUT type=\"hidden\" name=\"row$i\" value=\"".$bigarray[$i]["badgeid"]."\">\n";
    echo "        <INPUT type=\"hidden\" name=\"wasasgn".$bigarray[$i]["badgeid"]."\" value=\"";
    echo ((isset($bigarray[$i]["posbadgeid"]))?1:0)."\">\n";
    echo "      </TD>\n";
    echo "      <TD class=\"vatop\">".$bigarray[$i]["badgeid"]."</TD>\n";
    echo "      <TD class=\"vatop\">".$bigarray[$i]["pubsname"]." (";
    echo $bigarray[$i]["psits"].")</TD>\n";
    echo "      <TD class=\"vatop\">Rank: ".$bigarray[$i]["rank"]."</TD>\n";
    echo "      <TD class=\"vatop\">".(($bigarray[$i]["willmoderate"]==1)?"Volunteered to moderate.":"")."</TD>\n";
    echo "      </TR>\n";
    echo "   <TR>\n";
    echo "      <TD class=\"vatop\" vcenter>";
    echo "        <INPUT type=\"radio\" name=\"moderator\" id=\"moderator\" value=\"".$bigarray[$i]["badgeid"]."\" ";
    echo (($bigarray[$i]["moderator"])?"checked":"").">\n";
    echo "        <LABEL for=\"moderator\">Moderator<br></LABEL>";
    if ($volintaid_p > 0) {
      echo "        <INPUT type=\"radio\" name=\"volunteer\" id=\"volunteer\" value=\"".$bigarray[$i]["badgeid"]."\" ";
      echo (($bigarray[$i]["volunteer"])?"checked":"").">\n";
      echo "        <LABEL for=\"volunteer\">Volunteer<br></LABEL>";
      echo "        <INPUT type=\"radio\" name=\"introducer\" id=\"introducer\" value=\"".$bigarray[$i]["badgeid"]."\" ";
      echo (($bigarray[$i]["introducer"])?"checked":"").">\n";
      echo "        <LABEL for=\"introducer\">Introducer<br></LABEL>";
      echo "        <INPUT type=\"checkbox\" name=\"aidedecamp".$bigarray[$i]["badgeid"]."\" ";
      echo "id=\"aidedecamp".$bigarray[$i]["badgeid"]."\" ".(($bigarray[$i]["aidedecamp"])?"checked":"")." value=\"1\">\n";
      echo "        <LABEL for=\"aidedecamp\">Assisting<br></LABEL>";
      echo "        <INPUT type=\"hidden\" name=\"wasaidedecamp".$bigarray[$i]["badgeid"]."\" value=\"";
      echo (($bigarray[$i]["aidedecamp"])?1:0)."\">\n";
    }
    echo "        <INPUT type=\"checkbox\" name=\"unlist".$bigarray[$i]["badgeid"]."\" ";
    echo "id=\"unlist".$bigarray[$i]["badgeid"]."\" value=\"1\">\n";
    echo "        <LABEL for=\"unlist\">Not Interested</LABEL></TD>";
    echo "      <TD colspan=4 class=\"border1111 lrpad\">".htmlspecialchars($bigarray[$i]["comments"]);
    echo "</TD>\n";
    echo "      </TR>\n";
    echo "   <TR><TD colspan=6>&nbsp;</TD></TR>\n";
    }
echo "</TABLE>";
echo "<INPUT type=\"hidden\" name=\"selsess\" value=\"$selsessionid\">\n";
echo "<INPUT type=\"hidden\" name=\"numrows\" value=\"$numrows\">\n";
echo "<INPUT type=\"hidden\" name=\"wasmodid\" value=\"$modid\">\n";
echo "<INPUT type=\"hidden\" name=\"wasvolid\" value=\"$volid\">\n";
echo "<INPUT type=\"hidden\" name=\"wasintid\" value=\"$intid\">\n";
echo "<INPUT type=\"hidden\" name=\"statusid\" value=\"$statusid\">\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Update</BUTTON></DIV>\n";
echo "<HR>\n";
echo "<DIV><LABEL for=\"asgnpart\">Assign participant not indicated as interested or invited.</LABEL><BR>\n";
echo "<SELECT name=\"asgnpart\">\n";
populate_select_from_query($participant_query, 0,"Assign Participant",true);
echo "</SELECT></DIV>\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Add</BUTTON></DIV>\n";

echo "</FORM>\n";
correct_footer();
?>
