<?php
global $participant,$message_error,$message2,$congoinfo;
require_once('PartCommonCode.php');

// Localizations
$conid=$_SESSION['conid']; // make it a variable so it can be substituted
$badgeid=$_SESSION['badgeid']; // make it a variable so it can be substituted
$title="Show Search Session Results";

// Passed in Variables
$trackid=$_POST["track"];
$titlesearch=stripslashes($_POST["title"]);

/* If the individual doing the searches is a Programming Volunteer,
   they get to see all the scheduled classes, otherwise, just the
   panels set to be searchable.  Also, added the JOIN to Schedule, so
   only classes already scheduled comes up. */

if (may_I('Programming')) {
  $invitedguest_p="";
  $schedule_p="JOIN Schedule USING (sessionid,conid)";
} else {
  $invitedguest_p="invitedguest=0 AND";
  $schedule_p="";
}

// List of sessions that match search criteria 
// Does not includes sessions in which participant is interested if they do match match search
// Use "My Panel Interests" page to just see everything in which you are interested
$query = <<<EOD
SELECT
    concat(conname," - ",sessionid) AS Sessionid,
    trackname,
    title,
    CASE
      WHEN (minute(duration)=0) THEN date_format(duration,'%l hr')
      WHEN (hour(duration)=0) THEN date_format(duration, '%i min')
      ELSE date_format(duration,'%l hr, %i min')
    END AS duration,
    pocketprogtext,
    progguiddesc,
    persppartinfo,
    badgeid
  FROM
      Sessions
    JOIN Tracks USING (trackid)
    JOIN SessionStatuses USING (statusid)
    LEFT JOIN (SELECT
          badgeid,
          sessionid,
          conid
        FROM
            ParticipantSessionInterest
        WHERE
          badgeid='$badgeid') as PSI USING (sessionid,conid)
  WHERE
    conid=$conid AND
    may_be_scheduled=1 AND
    sessionid in (SELECT
          sessionid
        FROM
            Sessions
          JOIN Tracks T USING (trackid)
          JOIN Types Y USING (typeid)
          $schedule_p
        WHERE
          $invitedguest_p
          conid=$conid AND
          T.selfselect=1 AND
          Y.selfselect=1
EOD;
if ($trackid!=0) {
  $query.="          AND trackid=$trackid\n";
}
if ($titlesearch!="") {
  $x=mysql_real_escape_string($titlesearch,$link);
  $query.="          AND title LIKE \"%$x%\"\n";
}
$query.=")\n";
if (!$result=mysql_query($query,$link)) {
  $message=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
}
participant_header($title);
//echo $query."<BR>\n";
require ('RenderMySessions1.php');    
RenderMySessions1($result);
participant_footer();
exit();
?>
