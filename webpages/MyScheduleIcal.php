<?php
header("Content-Type: text/Calendar");
require_once ("CommonCode.php");
require_once ("CommonIcal.php");
global $link;

// Fixed, or setup variables
$DBHostname=DBHOSTNAME; // make it a variable so it can be substituted
$conid=$_SESSION['conid']; // make it a variable so it can be substituted
$dtstamp=date('Ymd').'T'.date('His');
if (isset($_GET['badgeid'])) {
  $badgeid=$_GET['badgeid'];
} elseif (isset($_POST['badgeid'])) {
  $badgeid=$_POST['badgeid'];
} else {
  $badgeid=$_SESSION['badgeid'];
}

// First query, to establish the schedarray, from which we need the session information.
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
    JOIN Tracks USING (trackid)
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN ConInfo USING (conid)
  WHERE
    badgeid="$badgeid" and
    conid=$conid
  ORDER BY
    starttime
EOD;

list($schdrows,$schdheader,$schdarray)=queryreport($query,$link,$title,$description,0);

// Then the partarray for the participant on session information.
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
    JOIN CongoDump CD USING(badgeid)
    JOIN Participants P USING(badgeid)
  WHERE
    sessionid in (SELECT
                      sessionid
                    FROM
                        ParticipantOnSession
                    WHERE
		      badgeid='$badgeid' AND
		      conid=$conid) AND
    conid=$conid
  ORDER BY
    sessionid,
    moderator DESC
EOD;

list($partrows,$partheader,$partarray)=queryreport($query,$link,$title,$description,0);

// Finally, the pubsname of the participant.
$query=<<<EOD
SELECT
    pubsname
  FROM
      Participants
  WHERE
    badgeid='$badgeid'
EOD;

list($partnamerow,$partnameheader,$partname)=queryreport($query,$link,$title,$description,0);

$filename=str_replace(" ","_",$partname[1]["pubsname"]);

// Send the correct header type
header("Content-Disposition: inline; filename=$filename-calendar.ics");

// Standard intro stuff at the beginning of the file
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
    if (in_array($partarray[$j]["moderator"],array("0","1","YES"))) {
      echo " - moderator";
    }
    if (in_array($partarray[$j]["volunteer"],array("0","1","YES"))) {
      echo " - volunteer";
    }
    if (in_array($partarray[$j]["introducer"],array("0","1","YES"))) {
      echo " - introducer";
    }
    if (in_array($partarray[$j]["aidedecamp"],array("0","1","YES"))) {
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