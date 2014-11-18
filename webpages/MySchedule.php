<?php
require_once('PartCommonCode.php'); // initialize db; check login;
$conid=$_SESSION['conid'];  // make it a variable so it can be substituted
$badgeid=$_SESSION['badgeid'];  // make it a variable so it can be substituted

$title="My Schedule";
$description="";
$additionalinfo="";
// require_once('renderMySessions2.php');
if (!may_I('my_schedule')) {
  $message_error="You do not currently have permission to view this page.<BR>\n";
  RenderError($title,$message_error);
  exit();
 }

// General presenter information
// Gather the comments offered on this presenter into pcommentarray, if any
$query = <<<EOD
SELECT
    concat(conname,": ",comment) AS Comment
  FROM
      CommentsOnParticipants
    JOIN ConInfo USING (conid)
  WHERE
    badgeid="$badgeid"
EOD;
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$pcommentrows=mysql_num_rows($result);
for ($i=1; $i<=$pcommentrows; $i++) {
  $pcommentarray[$i]=mysql_fetch_assoc($result);
 }

// Get the state of registration into $regmessage
$query = <<<EOD
SELECT
    message
  FROM
      CongoDump
    LEFT JOIN RegTypes USING (regtype)
  WHERE
    badgeid="$badgeid"
EOD;
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$row=mysql_fetch_array($result, MYSQL_NUM);
$regmessage=$row[0];

// Get the number of pannels the participant is introducing
$query = <<<EOD
SELECT
    count(*) AS poscount,
    SUM(if(introducer in ('1','Yes'),1,0)) AS intro_p
  FROM
      ParticipantOnSession
    JOIN Schedule USING (sessionid,conid)
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
list($poscount,$intro_p)=mysql_fetch_array($result, MYSQL_NUM);

// Message about state of registration, (on more than 3 pannels programming will ask for a comp).
if (!$regmessage) {
  if ($poscount>=3) {
    $regmessage="not registered.</span><span>  Programming has requested a comp membership for you";
  }
  else {
    $regmessage="not registered.</span><span>  Panelists on 3 or more panels receive complementary memberships from Programming.  If you are interested in increasing your number of panels to take advantage of this, please contact us and we will work with you to see if it is possible.  If you are expecting a comp from helping another division, that will show up here shortly after registration processes it.  Please contact that division or registration with questions";
  }
 }

// Get all the written feedback on the sessions, and the graph of the questions.
$feedback_array=getFeedbackData($badgeid);

/* For the title and descriptions (these should become not hard-coded):
   biostateid: Only using "good" for now.
   descriptionlang: Only using "en-us" for now. */

// Build the schedule of classes into schd_array
$query = <<<EOD
SELECT
    DISTINCT concat(sessionid,"-",conid) as "Sess-Con",
    sessionid,
    conid,
    conname,
    trackname,
    concat(if(title_good_web IS NOT NULL,title_good_web,title), if(subtitle_good_web IS NOT NULL,concat(": ",subtitle_good_web),""),if(estatten IS NOT NULL,concat(" (estimated attendance: ",estatten,")"),'')) as title,
    if(allrooms IS NOT NULL,allrooms,"") AS allrooms,
    desc_good_web,
    desc_good_book,
    if((questiontypeid IS NULL),"",questiontypeid) AS questiontypeid,
    DATE_FORMAT(ADDTIME(constartdate, starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END
      AS Duration,
    persppartinfo,
    notesforpart,
    partlist,
    concat(if((servicenotes!=''),servicenotes,""),
           if(((servicenotes!='') AND (servicelist!='')),", ",""),
           if((servicelist!=''),servicelist,''),
           if((((servicenotes!='') OR (servicelist!='')) AND (featurelist!='')),", ",""),
           if((featurelist!=''),featurelist,'')) AS Needed
  FROM
      ParticipantOnSession
    JOIN ConInfo USING (conid)
    JOIN Sessions USING (sessionid,conid)
    JOIN Tracks USING (trackid)
    LEFT JOIN (SELECT
           sessionid,
           conid,
	   GROUP_CONCAT(DISTINCT roomname SEPARATOR ", ") as 'allrooms',
	   starttime
         FROM
             Schedule
           JOIN Rooms USING (roomid)
         GROUP BY
           sessionid,conid) RL USING (sessionid,conid)
    LEFT JOIN TypeHasQuestionType USING (typeid,conid)
    LEFT JOIN (SELECT
           sessionid,
	   conid,
           GROUP_CONCAT(DISTINCT servicename SEPARATOR ', ') as 'servicelist'
         FROM
             SessionHasService
	   JOIN Services USING (serviceid,conid)
         GROUP BY
	       sessionid,conid) SL USING (sessionid,conid)
    LEFT JOIN (SELECT
           sessionid,
	   conid,
           GROUP_CONCAT(DISTINCT featurename SEPARATOR ', ') as 'featurelist'
         FROM
             SessionHasFeature
	   JOIN Features USING (featureid,conid)
         GROUP BY
	    sessionid,conid) FL USING (sessionid,conid)
    LEFT JOIN (SELECT
            sessionid,
            conid,
            GROUP_CONCAT("  <TR>\n    <TD>&nbsp;</TD>\n    <TD>",pubsname, if(moderator IN('1','Yes')," <I>mod</I> ",""), if(volunteer IN('1','Yes')," <I>volunteer</I> ",""), if(introducer IN('1','Yes')," <I>introducer</I> ",""), if(aidedecamp IN('1','Yes')," <I>assistant</I> ",""), "</TD>\n    <TD colspan=5>", if(comments,comments,""), "</TD>\n  </TR>\n" SEPARATOR "") AS partlist
          FROM
              ParticipantOnSession
            JOIN Participants USING (badgeid)
            LEFT JOIN ParticipantSessionInterest USING (sessionid,conid,badgeid)
          GROUP BY
	    sessionid,conid) PL USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename='title' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') TGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename='subtitle' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') SGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename='description' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') DGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename='description' AND
	  biostatename='good' AND
	  biodestname='book' AND
	  descriptionlang='en-us') DGB USING (sessionid,conid)
  WHERE
    badgeid="$badgeid"
  ORDER BY
    conid,
    starttime
EOD;
// error_log("Zambia: $query");
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$otherschdcon="";
$currentschdredirect="";
$otherschdredirect="";
$schd_rows=mysql_num_rows($result);
for ($i=1; $i<=$schd_rows; $i++) {
  $schd_array[$i]=mysql_fetch_assoc($result);
  if ($otherschdcon!=$schd_array[$i]["conid"]) {
      $otherschdcon=$schd_array[$i]["conid"];
    if ($schd_array[$i]['conid'] == $_SESSION['conid']) {
      $currentschdredirect.="<br>$otherschdcon: ";
    } else {
      $otherschdredirect.="<br>$otherschdcon: ";
    }
  }
  if ($schd_array[$i]['conid'] == $_SESSION['conid']) {
    $currentschdredirect.=" <A HREF=#".$schd_array[$i]["Sess-Con"].">".htmlspecialchars($schd_array[$i]["title"])."</A>";
  } else {
    $otherschdredirect.=" <A HREF=#".$schd_array[$i]["Sess-Con"].">".htmlspecialchars($schd_array[$i]["title"])."</A>";
  }
  $feedback_file=sprintf("../Local/Feedback/%s.jpg",$schd_array[$i]["sessionid"]);
  if (file_exists($feedback_file)) {
    $schd_array[$i]["feedbackgraph"]="  <TR>\n    <TD>&nbsp;</TD>\n    <TD colspan=6 class=border1000>Feedback graph from surveys:<br>";
    $schd_array[$i]["feedbackgraph"].="<img src=\"$feedback_file\"></TD>\n  </TR>\n";
  }
  if (isset($feedback_array['graph'][$schd_array[$i]["Sess-Con"]])) {
    $schd_array[$i]["autofeedbackgraph"]="  <TR>\n    <TD>&nbsp;</TD>\n    <TD colspan=6 class=border1000>Feedback graph from surveys:<br>";
    $schd_array[$i]["autofeedbackgraph"].=generateSvgString($schd_array[$i]["sessionid"],$schd_array[$i]["conid"]);
    $schd_array[$i]["autofeedbackgraph"].="</TD>\n  </TR>\n";
  }
  if (isset($feedback_array[$schd_array[$i]["Sess-Con"]])) {
    $schd_array[$i]["feedbackwritten"]="  <TR>\n    <TD>&nbsp;</TD>\n    <TD colspan=6 class=border1000>Written feedback from surveys:\n";
    $schd_array[$i]["feedbackwritten"].=$feedback_array[$schd_array[$i]["Sess-Con"]]."</TD>\n  </TR>\n";
  }
}


// Begin the presentation of the information
topofpagereport($title,$description,$additionalinfo);
if (file_exists("../Local/Verbiage/MySchedule_0")) {
  echo file_get_contents("../Local/Verbiage/MySchedule_0");
 } else {
  echo "<P>Below is the list of all the schedule elements for which you are scheduled.  If you need any changes\n";
  echo "to this schedule please contact <A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>.</P>\n";
  echo "<P>In order to put together the entire schedule, we had to schedule some panels outside of\n";
  echo "the times that certain panelists requested.  If this happened to you, we would love to have\n";
  echo "you on the panel, but understand if you cannot make it.  Please let us know if you cannot.</P>\n";
  echo "<P>Several of the panels we are running this year were extremely popular with over 20 potential\n";
  echo "panelists signing up.  Choosing whom to place on those panels was difficult.  There is always a\n";
  echo "possibility that one of the panelists currently scheduled will be unavailable so feel free to\n";
  echo "check with us to see if a space has opened up on a panel on which you'd still like to participate.</P>\n";
  echo "<P>To facilitate communication yet also preserve privacy, we provide you the option of putting your\n";
  echo "contact information in the comments field for each panel (under the\n";
  echo "<A HREF=\"./my_sessions2.php\">\"My Panel Interests\"</A> tab).  That will expose it to other\n";
  echo "panelists who can then email or call you as appropriate to discuss the panel in advance.  If you\n";
  echo "check back in a day or two you may find other panelists' information.</P>\n";
 }
echo "<P>You can also take a look at all that is going on by <A HREF=\"StaffSchedule.php\">timeslot</A>,\n";
echo "<A HREF=\"StaffDescriptions.php\">descriptions</A>, <A HREF=\"StaffTracks.php\">tracks</A>, visit the\n";
echo "<A HREF=\"grid.php?programming=y&unpublished=y\">grid</A>, or people's <A HREF=\"StaffBios.php\">bio</A>.</P>\n";
echo "<P><A HREF=\"MyScheduleIcal.php\">Here</A> is an iCal (Calendar standard) calendar of your schedule.\n";
echo "<A HREF=\"SchedulePrint.php?print_p=T&individual=".$_SESSION['badgeid']."\">Print</A> a PDF of your schedule.\n";
if ($intro_p > 0) {
  echo "<A HREF=\"ClassIntroPrint.php\">Print</A> a PDF of all of your class and panel introductions.\n";
 }
echo "</P>\n";
echo "<P>Your registration status is <SPAN class=\"hilit\">$regmessage.</SPAN></P>\n";
if ($pcommentrows > 0) {
  echo "<P>General <A HREF=#genfeedback>Feedback</A> received about or for you.\n</P>";
 }
if ($currentschdredirect != "") {
  echo "<P>Go directly to this event's classes: $currentschdredirect</P>\n";
}
if ($otherschdredirect != "") {
  echo "<P>Go to all of your other classes: $otherschdredirect</P>\n";
}
echo "<P>Thank you -- <A HREF=\"mailto:".$_SESSION['programemail']."\">Programming</a>\n";
if ($pcommentrows > 0) {
  echo "<hr>\n<P><A NAME=genfeedback></A>Personal Feedback:</A></P>\n";
  echo "<UL>\n";
  for ($i=1; $i<=$pcommentrows; $i++) {
    echo "  <LI>".$pcommentarray[$i]["Comment"]."\n";
  }
  echo "</UL>\n<br>\n";
}
echo "<HR>\n<H3>Classes</H3>\n<HR>\n";
echo "<TABLE>\n";
//echo "  <TR><TD></TD><TD width=\"30%\"></TD><TD width=\"20%\"></TD><TD></TD><TD width=\"6%\"></TD><TD></TD><TD width=\"18%\"></TD></TR>\n";
echo "  <COL><COL width=\"30%\"><COL width=\"20%\"><COL><COL width=\"6%\"><COL><COL width=\"18%\">\n";
for ($i=1; $i<=$schd_rows; $i++) {
  echo "  <TR>\n";
  echo "    <TD class=\"hilit\"><A NAME=".$schd_array[$i]["Sess-Con"]."></A>".$schd_array[$i]["conname"]."</TD>\n";
  echo "    <TD class=\"hilit\">".htmlspecialchars($schd_array[$i]["title"])."</TD>\n";
  echo "    <TD class=\"hilit\">".$schd_array[$i]["allrooms"]."</TD>\n";
  echo "    <TD class=\"hilit\">".$schd_array[$i]["trackname"]."</TD>\n";
  echo "    <TD class=\"hilit\">&nbsp;</TD>\n";
  echo "    <TD class=\"hilit\">".$schd_array[$i]["Start Time"]."</TD>\n";
  echo "    <TD class=\"hilit\">Duration: ".$schd_array[$i]["Duration"]."</TD>\n";
  echo "  </TR>\n";
  echo "  <TR>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD colspan=6 class=\"border0010\">Web: ".htmlspecialchars($schd_array[$i]["desc_good_web"])."</TD>\n";
  echo "  </TR>\n";
  echo "  <TR>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD colspan=6 class=\"border0010\">Book: ".htmlspecialchars($schd_array[$i]["desc_good_book"])."</TD>\n";
  echo "  </TR>\n";
  if ($schd_array[$i]["persppartinfo"] != "") {
    echo "  <TR>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD colspan=6 class=\"border0010\">Requirements: ".htmlspecialchars($schd_array[$i]["persppartinfo"])."</TD>\n";
    echo "  </TR>\n";
  }
  if ($schd_array[$i]["notesforpart"] != "") {
    echo "  <TR>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD colspan=6 class=\"border0010\">Participant notes: ".htmlspecialchars($schd_array[$i]["notesforpart"])."</TD>\n";
    echo "  </TR>\n";
  }
  if ($schd_array[$i]["Needed"] != "") {
    echo "  <TR>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD colspan=6 class=\"border0010\">Support requests: ".htmlspecialchars($schd_array[$i]["Needed"])."</TD>\n";
    echo "  </TR>\n";
  }
  echo "  <TR>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD colspan=6 class=\"border0010\">Propose <A HREF=MyMigrations.php?sessionid=".$schd_array[$i]['sessionid']."&conid=".$schd_array[$i]['conid'].">".$schd_array[$i]['title']."</A> for ".$_SESSION['conname'].".</TD>\n";
  echo "  </TR>\n";
  echo "  <TR>\n";
  echo "    <TD colspan=7 class=\"smallspacer\">&nbsp;</TD></TR>\n";
  echo "  <TR>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD class=\"usrinp\">Panelists' Publication Names (Badge Names)</TD>\n";
  echo "    <TD colspan=5 class=\"usrinp\">Their Comments</TD>\n";
  echo "  </TR>\n";
  echo "  <TR>\n";
  echo "    <TD colspan=7 class=\"smallspacer\">&nbsp;</TD></TR>\n";
  echo "  <TR>\n";
  echo $schd_array[$i]["partlist"];
  echo $schd_array[$i]["feedbackgraph"];
  echo $schd_array[$i]["autofeedbackgraph"];
  echo $schd_array[$i]["feedbackwritten"];
  echo "  <TR>\n    <TD colspan=7 class=\"border0020\">&nbsp;</TD>\n  </TR>\n";
  echo "  <TR>\n    <TD colspan=7 class=\"border0000\">&nbsp;</TD>\n  </TR>\n";
 }
echo "</TABLE>\n";
correct_footer();
?>
