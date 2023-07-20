<?php
global $link, $participant, $message, $message_error, $congoinfo;
require_once('PartCommonCode.php');

// Localizations
$conid=$_SESSION['conid']; // make it a variable so it can be substituted
$badgeid=$_SESSION['badgeid']; // make it a variable so it can be substituted
$title="Show Search Session Results";

$trackid=0;
$typeid=0;
$statusid=0;
$sessionid="";

// Passed in Variables
if ((isset($_POST["track"])) and (is_numeric($_POST["track"]))) {
  $trackid=$_POST["track"];
} elseif ((isset($_GET["track"])) and (is_numeric($_GET["track"]))) {
  $trackid=$_GET["track"];
}

if ((isset($_POST["type"])) and (is_numeric($_POST["type"]))) {
  $typeid=$_POST["type"];
} elseif ((isset($_GET["type"])) and (is_numeric($_GET["type"]))) {
  $typeid=$_GET["type"];
}

if ((isset($_POST["status"])) and (is_numeric($_POST["status"]))) {
  $statusid=$_POST["status"];
} elseif ((isset($_GET["status"])) and (is_numeric($_GET["status"]))) {
  $statusid=$_GET["status"];
}

if ((isset($_POST["sessionid"])) and (is_numeric($_POST["sessionid"]))) {
  $sessionid=$_POST["sessionid"];
} elseif ((isset($_GET["sessionid"])) and (is_numeric($_GET["sessionid"]))) {
  $sessionid=$_GET["sessionid"];
}

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
    sessionid,
    trackname,
    concat(title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS title ,
    CASE
      WHEN (minute(duration)=0) AND (starttime) THEN CONCAT(DATE_FORMAT(duration,'%l&nbsp;hr&nbsp;starting&nbsp;'), DATE_FORMAT(ADDTIME(constartdate, starttime), '%a&nbsp;%l:%i&nbsp;%p'))
      WHEN (minute(duration)=0) THEN DATE_FORMAT(duration,'%l&nbsp;hr')
      WHEN (hour(duration)=0) AND (starttime) THEN CONCAT(DATE_FORMAT(duration, '%i&nbsp;min&nbsp;starting&nbsp;'), DATE_FORMAT(ADDTIME(constartdate, starttime), '%a&nbsp;%l:%i&nbsp;%p'))
      WHEN (hour(duration)=0) THEN DATE_FORMAT(duration, '%i&nbsplmin')
      WHEN (starttime) THEN CONCAT(DATE_FORMAT(duration,'%l&nbsp;hr,&nbsp;%i&nbsp;min&nbsp;starting&nbsp;'), DATE_FORMAT(ADDTIME(constartdate, starttime), '%a&nbsp;%l:%i&nbsp;%p'))
      ELSE DATE_FORMAT(duration,'%l&nbsp;hr,&nbsp;%i&nbsp;min')
    END AS duration,
    desc_good_web,
    persppartinfo,
    badgeid
  FROM
      Sessions
    JOIN Tracks USING (trackid)
    JOIN SessionStatuses USING (statusid)
    JOIN ConInfo USING (conid)
    LEFT JOIN Schedule USING (sessionid,conid)
    LEFT JOIN (SELECT
          badgeid,
          sessionid,
          conid
        FROM
            ParticipantSessionInterest
        WHERE
          badgeid='$badgeid') as PSI USING (sessionid,conid)
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
if ($typeid!=0) {
  $query.="          AND typeid=$typeid\n";
}
if ($statusid!=0) {
  $query.="          AND statusid=$statusid\n";
}
if ($sessionid!="") {
  $query.="          AND sessionid=$sessionid\n";
}
$query.=")\n  ORDER BY\n    trackid,starttime\n";
if (!$result=mysqli_query($link,$query)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
}

require ('RenderMySessions1.php');
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo "<FORM method=POST action=\"SearchMySessionsScheduled.php\">\n";
$search=RenderSearchSession($trackid,$statusid,$typeid,$sessionid);
echo $search;
echo "</FORM>\n";
echo "<HR>\n";
RenderMySessions1($result);
correct_footer();
exit();
?>
