<?php
require_once('StaffCommonCode.php');
global $link;
$conid=$_SESSION['conid'];
$badgeid=$_SESSION['badgeid'];
$title="Votes On Session";
$description="<P>Select the presenter's classes you wish to vote on, or ";
$description.="<A HREF=\"genreport.php?reportname=brainstormsubmiswvotes\">see all the votes</A>.</P>";

if (isset($_GET["suggestor"])) { // Sets the "suggestor" from the GET string
  $suggestor=$_GET["suggestor"];
} elseif (isset($_POST["suggestor"])) { // Sets the "suggestor" from the POST string
  $suggestor=$_POST["suggestor"];
}

// Make the passed radio buttons a little more readable.
$ordinal[1]="first";
$ordinal[2]="second";
$ordinal[3]="third";
$ordinal[4]="forth";
$ordinal[5]="fifth";

// Remove the previous votes
if ((isset($_POST['removelist'])) AND ($_POST['removelist'] != "")) {
  $match_string="sessnoteid in (".$_POST['removelist'].")";
  delete_table_element($link, $title, "NotesOnSessions",$match_string);
}

// Add new notes
for ($i=1; $i<=5; $i++) {
  if ((isset($_POST[$ordinal[$i]])) AND ($_POST[$ordinal[$i]] != "")) {
    $element_array = array('sessionid', 'conid', 'badgeid', 'sessnote');
    $value_array = array($_POST[$ordinal[$i]],
			 $_SESSION['conid'],
			 $_SESSION['badgeid'],
			 $i);
    submit_table_element($link, $title, "NotesOnSessions", $element_array, $value_array);
  }
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
    suggestor,
    pubsname
  FROM
      Sessions
    JOIN Participants ON (suggestor=badgeid)
    JOIN SessionStatuses USING (statusid)
    JOIN PubStatuses USING (pubstatusid)
  WHERE
    statusname="Brainstorm" AND
    conid=$conid AND
    pubstatusname in ($pubstatus_string)
  GROUP BY
    suggestor
  ORDER BY
    pubsname
EOD;


topofpagereport($title,$description,$additionalinfo,$message,$message_error);

echo "<FORM name=\"suggestorform\" method=POST action=\"VoteOnSession.php\">\n";
echo "<DIV><LABEL for=\"suggestor\">Select Presenter</LABEL>\n";
echo "<SELECT name=\"suggestor\">\n";
populate_select_from_query($query, $suggestor, "Select Presenter", false);
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
if (isset($_SESSION['return_to_page'])) {
  echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
}
echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Presenter</BUTTON></DIV>\n";
echo "</FORM>\n";

if (empty($suggestor)) {
  correct_footer();
  exit();
}

// This gets all the information on the suggestor's proposals.
$query=<<<EOD
SELECT
    concat('<A NAME=',sessionid,' HREF=StaffAssignParticipants.php?selsess=',sessionid,'>', sessionid,'</A>') AS 'Session<BR>id',
    concat('<A HREF=EditSession.php?id=',sessionid,'>',title_good_web,if(subtitle_good_web IS NOT NULL, concat(": ",subtitle_good_web),''),'</A>') AS Title,
    concat(P.pubsname,' - ',notesforpart) AS 'Proposer',
    Trackname,
    desc_good_web AS Description,
    notesforprog AS 'Notes for Programming',
    if(sessnote IS NOT NULL,GROUP_CONCAT(B.pubsname,':&nbsp;',sessnote),"No vote yet") AS "Votes",
    SUM(6-sessnote) AS "Total"
  FROM
      Sessions S
    JOIN SessionStatuses USING (statusid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN Participants P ON (suggestor=P.badgeid)
    LEFT JOIN NotesOnSessions NOS USING (sessionid,conid)
    LEFT JOIN Participants B ON (B.badgeid=NOS.badgeid)
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
  WHERE
    statusname='Brainstorm' AND
    suggestor=$suggestor AND
    conid=$conid AND
    pubstatusname in ($pubstatus_string)
  GROUP BY
    sessionid
EOD;

list($session_rows,$session_header_array,$session_array)=queryreport($query,$link,$title,$query,0);

// Gets the simple list of classes that are going to be voted on.
$query=<<<EOD
SELECT
    sessionid,
    descriptiontext as Title
  FROM
      Sessions
    JOIN SessionStatuses USING (statusid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN Descriptions USING (sessionid,conid)
    JOIN DescriptionTypes USING (descriptiontypeid)
    JOIN BioStates USING (biostateid)
    JOIN BioDests USING (biodestid)
  WHERE
    statusname='Brainstorm' AND
    conid=$conid AND
    suggestor=$suggestor AND
    descriptiontypename='title' AND
    biostatename='good' AND
    biodestname='web' AND
    descriptionlang='en-us' AND
    pubstatusname in ($pubstatus_string)
EOD;

list($votable_rows,$votable_header_array,$votable_array)=queryreport($query,$link,$title,$query,0);

//Set the session string so we can get the appropriate votes.
$session_string="";
for ($i=1; $i<=$votable_rows; $i++) {
  $session_string.=$votable_array[$i]['sessionid'].",";
}
$session_string=substr($session_string,0,-1); //remove final trailing comma

$query=<<<EOD
SELECT
    sessionid,
    sessnote,
    sessnoteid
  FROM
      NotesOnSessions
  WHERE
    conid=$conid AND
    badgeid=$badgeid AND
    sessionid in ($session_string)
EOD;

list($votes_rows,$votes_header_array,$tmp_votes_array)=queryreport($query,$link,$title,$query,0);

for ($i=1; $i<=$votes_rows; $i++) {
  $votes_array[$tmp_votes_array[$i]['sessionid']]=$tmp_votes_array[$i]['sessnote'];
}

$removelist="";
for ($i=1; $i<=$votes_rows; $i++) {
  $removelist.=$tmp_votes_array[$i]['sessnoteid'].",";
}
$removelist=substr($removelist,0,-1); //remove final trailing comma

echo "<HR>\n";

echo "<P>Please vote below.  Remember, 1st is your first choice, 2nd is your second and so on down to 5th.  The descriptions et al are below the voting section.</P>";

echo "<FORM name=\"voteform\" method=POST action=\"VoteOnSession.php\">\n";
echo "<INPUT type=\"hidden\" name=\"suggestor\" value=\"$suggestor\">\n";
echo "<INPUT type=\"hidden\" name=\"removelist\" value=\"$removelist\">\n";
echo "<TABLE border=1>\n";
echo "  <TR><TH>Title</TH><TH>1st</TH><TH>2nd</TH><TH>3rd</TH><TH>4th</TH><TH>5th</TH></TR>\n";
for ($i=1; $i<=$votable_rows; $i++) {
  echo "  <TR>\n";
  echo "    <TD>" . $votable_array[$i]['Title'] . "</TD>\n";
  for ($j=1; $j<=5; $j++) {
    echo "    <TD align=\"center\"><INPUT type=\"radio\" name=\"".$ordinal[$j]."\" id=\"".$ordinal[$j]."\" value=\"" . $votable_array[$i]['sessionid'] . "\"";
    if ((isset($votes_array[$votable_array[$i]['sessionid']])) AND ($votes_array[$votable_array[$i]['sessionid']] == $j)) {
      echo " CHECKED";
    }
    echo "></TD>\n";
  }
  echo "  </TR>\n";
}
echo "</TABLE>\n";
echo "<BR>\n";
echo "<BR>\n";
echo "<BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\" >Update</BUTTON>\n";
echo "<BR>\n";
echo "<BR>\n";
echo "</FORM>\n";

echo "<P>Here are the classes, their descriptions, and the votes to date:</P>\n";
echo renderhtmlreport(1,$session_rows,$session_header_array,$session_array);

correct_footer();
?>
