<?php
require_once('StaffCommonCode.php');
require_once('SubmitAdminParticipants.php');
global $link;
$conid=$_SESSION['conid'];

// LOCALIZATIONS
$title="Establish the Class Sponsors";
$description="<P>Map a sponsor to a class.</P>\n";

// Passed in variables
$sessionid="";
$sponsorid="";
if ((!empty($_POST["sestype"])) and (is_numeric($_POST["sestype"]))) {$sessionid=$_POST["sestype"];}
if ((!empty($_POST["sesnum"])) and (is_numeric($_POST["sesnum"]))) {$sessionid=$_POST["sesnum"];}
if ((!empty($_POST["sesname"])) and (is_numeric($_POST["sesname"]))) {$sessionid=$_POST["sesname"];}
if ((!empty($_POST["sponsorchoice"])) and (is_numeric($_POST["sponsorchoice"]))) {$sponsorid=$_POST["sponsorchoice"];}

// Test and submit new sponsor pairing to database
if ($_POST["update"]=="please") {
  if ((empty($sessionid)) or (empty($sponsorid))) {
    $message_error="Please select a valid Schedule Element and Sponsor\n";
  } else {
    $description="<P>Map a sponsor to another class.</P>\n";
    $element_array = array('sessionid','conid','badgeid');
    $value_array = array($sessionid,$conid,$sponsorid);
    $message.=submit_table_element($link, $title, "SessionHasSponsor", $element_array, $value_array);
  }
}

// Test and remove deleted sponsor pairing from database
if (($_POST["dodelete"]=="please") and
    (!empty($_POST["delsessionid"])) and
    (is_numeric($_POST["delsessionid"])) and
    (!empty($_POST["delsponsorid"])) and
    (is_numeric($_POST["delsponsorid"]))) {
  $match_string="sessionid=".$_POST["delsessionid"]." AND conid=".$_SESSION["conid"]." AND badgeid=".$_POST["delsponsorid"];
  $message.=delete_table_element($link, $title, "SessionHasSponsor", $match_string);
}

// Complicated form for sponsor removal
$deleteform ="'<FORM name=\"deletesponsor\" method=\"POST\" action=\"ClassSponsors.php\">\n";
$deleteform.="  <INPUT type=\"hidden\" name=\"dodelete\" value=\"please\">\n";
$deleteform.="  <INPUT type=\"hidden\" name=\"delsessionid\" value=\"',sessionid,'\">\n";
$deleteform.="  <INPUT type=\"hidden\" name=\"delsponsorid\" value=\"',badgeid,'\">\n";
$deleteform.="  <INPUT type=\"submit\" name=\"submit\" value=\"Delete\">\n";
$deleteform.="</FORM>\n'";

// Test and toggle sponsor state in database
if (($_POST["dotoggle"]=="please") and
    (!empty($_POST["togglesessionid"])) and
    (is_numeric($_POST["togglesessionid"])) and
    (!empty($_POST["togglesponsorid"])) and
    (is_numeric($_POST["togglesponsorid"])) and
    (($_POST["newsponsorstate"] == "p") or ($_POST["newsponsorstate"] == "a"))) {
  $set_array=array("sponsorstatus='".$_POST["newsponsorstate"]."'");
  $match_string="sessionid=".$_POST["togglesessionid"]." AND conid=".$_SESSION["conid"]." AND badgeid=".$_POST["togglesponsorid"];
  $message.=update_table_element_extended_match($link, $title, "SessionHasSponsor", $set_array, $match_string);
}

// Complicated form for sponsor state toggle
$toggleform ="'<FORM name=\"togglesponsor\" method=\"POST\" action=\"ClassSponsors.php\">\n";
$toggleform.="  <INPUT type=\"hidden\" name=\"dotoggle\" value=\"please\">\n";
$toggleform.="  <INPUT type=\"hidden\" name=\"newsponsorstate\" value=\"',if(sponsorstatus=\"p\",\"a\",\"p\"),'\">\n";
$toggleform.="  <INPUT type=\"hidden\" name=\"togglesessionid\" value=\"',sessionid,'\">\n";
$toggleform.="  <INPUT type=\"hidden\" name=\"togglesponsorid\" value=\"',badgeid,'\">\n";
$toggleform.="  <INPUT type=\"submit\" name=\"submit\" value=\"',if(sponsorstatus=\"p\",\"Pending\",\"Accepted\"),'\">\n";
$toggleform.="</FORM>\n'";

// Get the list of classes and who they are sponsored by.
$query=<<<EOD
SELECT
    concat("<A HREF=StaffAssignParticipants.php?selsess=", sessionid, ">", sessionid, "</A> - <A HREF=EditSession.php?id=", sessionid, ">", title, if(secondtitle,concat(": ",secondtitle),""), "</A>") AS Class,
    Participants,
    DATE_FORMAT(ADDTIME(constartdate,starttime),'%a %l:%i %p') AS "Starting Time",
    pubsname as "Sponsored by",
    concat($deleteform) as "Remove Sponsor",
    concat($toggleform) as "Change State"
  FROM
      SessionHasSponsor
    JOIN Sessions USING (sessionid,conid)
    JOIN Schedule USING (sessionid,conid)
    JOIN Participants USING (badgeid)
    JOIN ConInfo USING (conid)
    LEFT JOIN (SELECT
        sessionid,
        conid,
	       GROUP_CONCAT("<A HREF=mailto:",email,">",badgename,"</A>",if(moderator in ('1','Yes'),'(m)','') ORDER BY moderator DESC SEPARATOR ', ') AS Participants
      FROM
          ParticipantOnSession
        JOIN CongoDump USING (badgeid)
      WHERE
        introducer not in ('1','Yes') AND
        volunteer not in ('1','Yes') AND
        aidedecamp not in ('1','Yes')
      GROUP BY
        conid,
        sessionid) POS USING (sessionid,conid)
  WHERE
    conid=$conid
  ORDER BY
    starttime
EOD;
list($sponsorrows,$sponsorheader_array,$sponsor_array)=queryreport($query,$link,$title,$description,0);

// Session query by trackname then title then sessionid
$query0=<<<EOD
SELECT
    sessionid,
    concat(trackname,' - ',sessionid,' - ',title) as sname
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    LEFT JOIN SessionHasSponsor USING (sessionid,conid)
  WHERE
    conid=$conid AND
    pubstatusname in ("Public") AND
    badgeid IS NULL
  ORDER BY
    trackname,
    title,
    sessionid
EOD;

// Session query by sessionid then title then trackname
$query1=<<<EOD
SELECT
    sessionid,
    concat(sessionid,' - ',title,' - ',trackname) as sname
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    LEFT JOIN SessionHasSponsor USING (sessionid,conid)
  WHERE
    conid=$conid AND
    pubstatusname in ("Public") AND
    badgeid IS NULL
  ORDER BY
    sessionid,
    title,
    trackname
EOD;

// Session query by title then trackname then sessionid
$query2=<<<EOD
SELECT
    sessionid,
    concat(title,' - ',trackname,' - ',sessionid) as sname
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    LEFT JOIN SessionHasSponsor USING (sessionid,conid)
  WHERE
    conid=$conid AND
    pubstatusname in ("Public") AND
    badgeid IS NULL
  ORDER BY
    title,
    trackname,
    sessionid
EOD;

// Sponsor query, search for all badgeids for those who are sponsors, and present them by name
$sponsorquery=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(pubsname," -- ",badgename) as sponsor
  FROM
      UserHasConRole
    JOIN ConRoles USING (conroleid)
    JOIN CongoDump USING (badgeid)
    JOIN Participants USING (badgeid)
  WHERE
    conid=$conid AND
    conrolename in ("Sponsor")
EOD;

// Begin page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Form to update the class sponsors
echo "<FORM name=\"selclasssponsorform\" method=POST action=\"ClassSponsors.php\">\n";
echo "  <INPUT type=\"hidden\" name=\"update\" value=\"please\">\n";

// Select the classes
echo "  <P>Select the Schedule Element:</P>\n";
echo "  <DIV>\n";
echo "    <LABEL for=\"sestype\">Select by type:</LABEL>\n";
echo "    <SELECT name=\"sestype\">\n";
echo populate_select_from_query_inline($query0, 0, "Select Schedule Element", true);
echo "    </SELECT>\n";
echo "  </DIV>\n";
echo "  <DIV>\n";
echo "    <LABEL for=\"sesnum\">Select by number:</LABEL>\n";
echo "    <SELECT name=\"sesnum\">\n";
echo populate_select_from_query_inline($query1, 0, "Select Schedule Element", true);
echo "    </SELECT>\n";
echo "  </DIV>\n";
echo "  <DIV>\n";
echo "    <LABEL for=\"sesname\">Select by name:</LABEL>\n";
echo "    <SELECT name=\"sesname\">\n";
echo populate_select_from_query_inline($query2, 0, "Select Schedule Element", true);
echo "    </SELECT>\n";
echo "  </DIV>\n";

// Select the sponsor
echo "  <DIV>\n";
echo "    <LABEL for=\"sponorchoice\">Select the Sponsor:</LABEL>\n";
echo "    <SELECT name=\"sponsorchoice\">\n";
echo populate_select_from_query_inline($sponsorquery, 0, "Select Sponsor", true);
echo "    </SELECT>\n";
echo "  </DIV>\n";

// Submit and close the form
echo "  <DIV class=\"SubmitDiv\">\n";
echo "    <BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON>\n";
echo "  </DIV>\n";
echo "</FORM>\n";

// Show the current state of sponsorship
echo renderhtmlreport(1,$sponsorrows,$sponsorheader_array,$sponsor_array);

// End page
correct_footer();
?>
