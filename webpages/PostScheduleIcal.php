<?php 
header("Content-Type: text/Calendar");
require_once ("PostingCommonCode.php");
require_once ("CommonIcal.php");
global $link;

// Fixed, or setup variables
$DBHostname=DBHOSTNAME;

$dtstamp=date('Ymd').'T'.date('His');
if (isset($_GET['pubsname'])) {
  $pubsname=$_GET['pubsname'];
} elseif (isset($_POST['pubsname'])) {
  $pubsname=$_POST['pubsname'];
} else {
  $pubsname="Not Attending This Con";
}

if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Establish the schedual array
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
      ParticipantOnSession
    JOIN Sessions USING (sessionid,conid)
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN Tracks USING (trackid)
    JOIN Participants USING (badgeid)
    JOIN ConInfo USING (conid)
  WHERE
    pubsname='$pubsname' AND
    conid=$conid
  ORDER BY
    starttime
EOD;

list($schdrows,$schdheader_array,$schdarray)=queryreport($query,$link,"PostScheduleIcal",$query,0);

// Establish the participant array
$query= <<<EOD
SELECT
    sessionid,
    badgename,
    pubsname,
    moderator,
    volunteer,
    introducer,
    aidedecamp
  FROM
      ParticipantOnSession
    JOIN CongoDump USING (badgeid)
    JOIN Participants USING (badgeid)
  WHERE
    sessionid IN (
      SELECT
          sessionid 
        FROM
            ParticipantOnSession
          JOIN Participants USING (badgeid)
        WHERE 
          pubsname='$pubsname' AND
          conid=$conid) AND
    conid=$conid
  ORDER BY
    sessionid,
    moderator DESC
EOD;

list($partrows,$partheader_array,$partarray)=queryreport($query,$link,"PostScheduleIcal",$query,0);

$filename=str_replace(" ","_",$pubsname);
header("Content-Disposition: inline; filename=$filename-calendar.ics");
echo add_ical_header();

// This should loop for every element in the produced array
for ($i=1; $i<=$schdrows; $i++) {
  /*
    UID should be DTSTAMP, followed by DTSTART, followed by DTEND followed by SEQUENCE followed by $DBHostname
    DTSTAMP should be generated from whenever this file is clicked on
    DTSTAMP:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
    LAST-MODIFIED should be DTSTAMP
    CREATED should be DTSTAMP
    SEQUENCE should be the loop counter
    PRIORITY is set to 5, arbitrarily
    CATEGORY should be "conname Event Calendar"
    SUMMARY should be title -- trackname, possibly add sessionid?
    LOCATION should be roomname
    DTSTART should be garnered from the constartdate + starttime
    DTSTART:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
    DTEND is chosen over DURATION because it's easier to just do constartdate + starttime + duration
    DTEND:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
    DESCRIPTION should include the progguiddesc, and all the presneter information ... this needs to be tweaked
    ORGANIZER should be set to the conname and the MAILTO: set to the programemail
    TRANSP is set to OPAQUE
    CLASS is set to PUBLIC
  */  
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
    if ($partarray[$j]["pubsname"]!=$partarray[$j]["badgename"]) {
      echo " (".$partarray[$j]["badgename"].")";
    }
    if ($partarray[$j]["moderator"]) {
      echo " - moderator";
    }
    if ($partarray[$j]["volunteer"]) {
      echo " - volunteer";
    }
    if ($partarray[$j]["introducer"]) {
      echo " - introducer";
    }
    if ($partarray[$j]["aidedecamp"]) {
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