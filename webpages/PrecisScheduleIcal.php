<?php 
require_once ("PostingCommonCode.php");
require_once ("CommonIcal.php");
global $link;

// Fixed, or setup variables
$DBHostname=DBHOSTNAME;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

$dtstamp=date('Ymd').'T'.date('His');
$title="Precis iCal generation page";
$description="<P>Please select from the below list.</P>";
$additionalinfo="";
$trackid="";

// Header query, to list the Sessions
$query= <<<EOD
SELECT
    DISTINCT concat("<A HREF=PrecisScheduleIcal.php?sessionid=",sessionid,">",title,"</A>") AS "Precis"
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN PubStatuses USING (pubstatusid)
  WHERE
    pubstatusname in ('Public') AND
    conid=$conid
  ORDER BY
    title
EOD;

list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

if (isset($_GET['sessionid'])) {
  $sessionid=$_GET['sessionid'];
 } elseif (isset($_POST['sessionid'])) {
  $sessionid=$_POST['sessionid'];
 } else {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo renderhtmlreport(1,$rows,$header_array,$report_array);
  correct_footer();
  exit();
 }

// First query, to establish the schedarray for the schedule elements to put into the calendar
$query= <<<EOD
SELECT
    sessionid,
    trackname,
    title,
    roomname,
    progguiddesc,
    DATE_FORMAT(ADDTIME(constartdate, starttime),'%Y%m%dT%H%i%s') AS dtstart,
    DATE_FORMAT(ADDTIME(ADDTIME(constartdate, starttime), duration), '%Y%m%dT%H%i%s') AS dtend
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN Tracks USING (trackid)
    JOIN ConInfo USING (conid)
  WHERE
    sessionid="$sessionid" AND
    conid=$conid
  ORDER BY
    starttime
EOD;

list($schdrows,$schdheader_array,$schdarray)=queryreport($query,$link,$title,$description,0);

if ($schdrows==0) {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo renderhtmlreport(1,$rows,$header_array,$report_array);
  correct_footer();
  exit();
 }

$filename=str_replace(" ","_",$schdarray[1]["title"]);

// Second query establishes the people in a particular schedule element.
$query= <<<EOD
SELECT
    sessionid,
    pubsname,
    moderator,
    volunteer,
    introducer,
    aidedecamp
  FROM
      ParticipantOnSession
    JOIN Participants USING (badgeid)
  WHERE
    sessionid='$sessionid' AND
    conid=$conid
  ORDER BY
    sessionid,
    moderator DESC
EOD;

list($partrows,$partheader_array,$partarray)=queryreport($query,$link,$title,$description,0);

header("Content-Type: text/Calendar");
header("Content-Disposition: inline; filename=$filename-calendar.ics");
echo add_ical_header();

// This should loop for every element in the produced array
for ($i=1; $i<=$schdrows; $i++) {
  
  // UID should be DTSTAMP, followed by DTSTART, followed by DTEND followed by SEQUENCE followed by $DBHostname
  // DTSTAMP should be generated from whenever this file is clicked on
  // DTSTAMP:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
  // LAST-MODIFIED should be DTSTAMP
  // CREATED should be DTSTAMP
  // SEQUENCE should be the loop counter
  // PRIORITY is set to 5, arbitrarily
  // CATEGORY should be "conname Event Calendar"
  // SUMMARY should be title -- trackname, possibly add sessionid?
  // LOCATION should be roomname
  // DTSTART should be garnered from the constartdate + starttime
  // DTSTART:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
  // DTEND is chosen over DURATION because it's easier to just do constartdate + starttime + duration
  // DTEND:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
  // DESCRIPTION should include the progguiddesc, and all the presneter information ... this needs to be tweaked
  // ORGANIZER should be set to the conname and the MAILTO: set to the programemail
  // TRANSP is set to OPAQUE
  // CLASS is set to PUBLIC

  echo "BEGIN:VEVENT\n";
  echo "UID:$dtstamp-".$schdarray[$i]["dtstart"]."-".$schdarray[$i]["dtend"]."-$i-$DBHostname\n";
  echo "DTSTAMP:$dtstamp\n";
  echo "LAST-MODIFIED:$dtstamp\n";
  echo "CREATED:$dtstamp\n";
  echo "SEQUENCE:$i\n";
  echo "PRIORITY:5\n";
  echo "CATEGORY:".$_SESSION['conname']." Event Calendar\n";
  echo "SUMMARY:".$schdarray[$i]["title"]." -- ".$schdarray[$i]["trackname"]."\n";
  echo "LOCATION:".$schdarray[$i]["roomname"]."\n";
  echo "DTSTART;TZID=America/New_York:".$schdarray[$i]["dtstart"]."\n"; 
  echo "DTEND;TZID=America/New_York:".$schdarray[$i]["dtend"]."\n"; 
  echo "DESCRIPTION:".$schdarray[$i]["progguiddesc"]."\\n\\n ";
  for ($j=1; $j<=$partrows; $j++) {
    if ($partarray[$j]["sessionid"]!=$schdarray[$i]["sessionid"]) {
      continue;
    }
    echo $partarray[$j]["pubsname"];
    if (in_array($partarray[$j]["moderator"],array("1","Yes"))) {
      echo " - moderator";
    }
    if (in_array($partarray[$j]["volunteer"],array("1","Yes"))) {
      echo " - volunteer";
    }
    if (in_array($partarray[$j]["introducer"],array("1","Yes"))) {
      echo " - introducer";
    }
    if (in_array($partarray[$j]["aidedecamp"],array("1","Yes"))) {
      echo " - assistant";
    }
    echo "\\n\\n ";
  }
  echo "\n";
  echo "ORGANIZER;CN=".$_SESSION['conname'].":MAILTO:".$_SESSION['programemail']."\n";
  echo "TRANSP:OPAQUE\n";
  echo "CLASS:PUBLIC\n";
  echo "END:VEVENT\n";
 }
// At the end of the file
echo "END:VCALENDAR\n";
?>