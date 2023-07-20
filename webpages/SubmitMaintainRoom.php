<?php
function check_room_sched_conflicts($deleteScheduleIds,$addToScheduleArray) {
  /* $addToScheduleArray is an array of $sessionid => $startmin
     sessions to add to schedule with starttime measured in minutes from start of con
     $deleteScheduleIds is an array of $scheduleid => 1

     Perform following checks on the participants in the new schedule entries (taking into account deleted entries):
     1. Are any participants double booked?
     2. Are any participants scheduled outside available times?
     3. Are any participants scheduled for more sessions than limits (daily and total)?

     Process

     Get the data of entries added -- Get the list of participants affected
     If there are no additions, then there are only deletions and these can't cause conflicts.
     Get their existing schedule
     Remove the deleted items from the existing schedule data to be compare
     Check 1
     Retrieve availability info
     Check 2
     Retrieve session limit info
     Check 3

     $addToScheduleArray2 [sessionid]
       [startmin]
       [durationmin]
       [participants] array of []=>badgeid */
  global $title, $link, $message;
  $conid=$_SESSION['conid']; // make it a variable so it can be substituted

  // If there are no additions, then there are only deletions and these can't cause conflicts.
  if (!$addToScheduleArray) return (true);

  // This should become an implode
  $sessionidlist="";
  foreach ($addToScheduleArray as $sessionid => $starttime) {
    $sessionidlist.=$sessionid.",";
  }
  $sessionidlist=substr($sessionidlist,0,-1); // remove trailing comma

  $query= <<<EOD
SELECT
    sessionid,
    hour(duration)*60+minute(duration) as durationmin,
    title
  FROM
      Sessions
  WHERE
    conid=$conid AND
    sessionid in ($sessionidlist)
EOD;
  if (!$result=mysqli_query($link,$query)) {
    $message=$query."<BR>\nError querying database.<BR>\n";
    RenderError($title,$message);
    exit();
  }
  while (list($sessionid,$durationmin,$title)=mysqli_fetch_array($result,MYSQLI_NUM)) {
    $addToScheduleArray2[$sessionid]['startmin']=$addToScheduleArray[$sessionid];
    $addToScheduleArray2[$sessionid]['endmin']=$addToScheduleArray[$sessionid]+$durationmin;
    $addToScheduleArray2[$sessionid]['title']=$title;
  }

  $query= <<<EOD
SELECT
    sessionid,
    badgeid
  FROM
      Sessions
    JOIN ParticipantOnSession USING (sessionid,conid)
  WHERE
    conid=$conid AND
    sessionid in ($sessionidlist)
EOD;

  if (!$result=mysqli_query($link,$query)) {
    $message=$query."<BR>\nError querying database.<BR>\n";
    RenderError($title,$message);
    exit();
  }
  while (list($sessionid, $badgeid)=mysqli_fetch_array($result,MYSQLI_NUM)) {
    $addToScheduleArray2[$sessionid]['participants'][]=$badgeid;
    $addToScheduleParticipants[$badgeid]=1;
  }

  // If none of the sessions added to the schedule had any participants, then there can be no participant conflicts.
  if (!$addToScheduleParticipants) return(true);

  // This should become an implode
  $badgeidlist="";
  foreach ($addToScheduleParticipants as $badgeid => $x) {
    $badgeidlist.="'$badgeid',";
  }
  $badgeidlist=substr($badgeidlist,0,-1); // remove trailing comma

  $query= <<<EOD
SELECT
    badgeid,
    pubsname
  FROM
      Participants
  WHERE
    badgeid in ($badgeidlist)
EOD;

  if (!$result=mysqli_query($link,$query)) {
    $message=$query."<BR>\nError querying database.<BR>\n";
    RenderError($title,$message);
    exit();
  }
  while ($x=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
    $addToScheduleParticipants[$x['badgeid']]=$x['pubsname'];
  }

  // starttime and duration in minutes from start of con -- simpler time comparison
  // Get participant availabilities
  $query= <<<EOD
SELECT
    badgeid,
    HOUR(starttime)*60+MINUTE(starttime) AS startmin,
    HOUR(endtime)*60+MINUTE(endtime) AS endmin
  FROM
      ParticipantAvailabilityTimes
  WHERE
    badgeid IN ($badgeidlist) AND
    conid=$conid
  ORDER BY
    badgeid,
    startmin
EOD;

  if (!$result=mysqli_query($link,$query)) {
    $message=$query."<BR>\nError querying database.<BR>\n";
    RenderError($title,$message);
    exit();
  }
  $oldbadgeid="";
  while (list($badgeid,$startmin,$endmin)=mysqli_fetch_array($result,MYSQLI_NUM)) {
    if ($oldbadgeid!=$badgeid) {
      $oldbadgeid=$badgeid;
      $i=1;
      $participantAvailabilityTimes[$badgeid][$i]['startmin']=$startmin;
      $participantAvailabilityTimes[$badgeid][$i]['endmin']=$endmin;
    } else {
      if ($startmin<$participantAvailabilityTimes[$badgeid][$i]['endmin']) {
	$participantAvailabilityTimes[$badgeid][$i]['endmin']=max($endmin,$participantAvailabilityTimes[$badgeid][$i]['endmin']);
      } else {
	$i++;
	$participantAvailabilityTimes[$badgeid][$i]['startmin']=$startmin;
	$participantAvailabilityTimes[$badgeid][$i]['endmin']=$endmin;
      }
    }
  }

  $query= <<<EOD
SELECT
    scheduleid,
    sessionid,
    hour(starttime)*60+minute(starttime) as startmin,
    hour(starttime)*60+minute(starttime)+hour(duration)*60+minute(duration) as endmin,
    badgeid,
    title,
    roomname
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN ParticipantOnSession USING (sessionid,conid)
  WHERE
    conid=$conid AND
    badgeid in ($badgeidlist)
EOD;
  if (!$result=mysqli_query($link,$query)) {
    $message=$query."<BR>\nError querying database.<BR>\n";
    RenderError($title,$message);
    exit();
  }
  while ($x=mysqli_fetch_array($result,MYSQLI_ASSOC)) {

    // skip the scheduleids that will be deleted anyway
    if ($deleteScheduleIds[$x['scheduleid']]==1) continue;

    $refScheduleArray[]=$x;
  }

  // If net of deletes there are no sessions for relevant participants
  // then there can be no conflicts
  if (!$refScheduleArray) return (true);
  $message="";
  foreach ($addToScheduleArray2 as $sessionid => $addSession) {
    $conflictThisAddition=false;
    // check #1 two place at once conflict
    foreach ($refScheduleArray as $refSession) {
      if ($addSession['startmin']>=$refSession['endmin'] or
	  $refSession['startmin']>=$addSession['endmin']) continue;
      $participants=$addSession['participants'];
      if ($participants) {
	foreach ($participants as $badgeid) {
	  if ($badgeid!=$refSession['badgeid']) continue;
	  if (!$conflictThisAddition) { // Need header for this session
	    $message.="<P>Session $sessionid: {$addSession['title']}</P>\n<UL>";
	  }
	  $conflictThisAddition=true;
	  $message.="<LI>".htmlspecialchars($addToScheduleParticipants[$badgeid],ENT_NOQUOTES)." ($badgeid) ";
	  $message.="has conflict with ".htmlspecialchars($refSession['title'],ENT_NOQUOTES)." ({$refSession['sessionid']}) in ";
	  $message.="{$refSession['roomname']}.</LI>\n";
	  // conflict!
	}
      }
    }
    /* check #2 not available conflict
       Don't report conflict if there are no availabilities at all for the participant
       echo "Participant Availability Times:<BR>\n";
       print_r($participantAvailabilityTimes);
       echo "<BR>\n";
       echo "addSession:<BR>\n";
       print_r($addSession);
       echo "<BR>\n"; */
    if ($addSession['participants']) {
      $addParts=$addSession['participants'];
      foreach ($addParts as $addBadgeid) {
	$availability_match=false;
	$partAvailTimeSet=$participantAvailabilityTimes[$addBadgeid];
	if ($partAvailTimeSet) {
	  foreach ($partAvailTimeSet as $partAvailTime) {
	    if ($partAvailTime['startmin']>$addSession['startmin']) continue;
	    if ($partAvailTime['endmin']<$addSession['endmin']) continue;
	    $availability_match=true;
	    break;
	  }
	} else {
	  // Don't report conflict if there are no availabilities at all for the participant
	  $availability_match=true;
	}
	if (!$availability_match) {
	  if (!$conflictThisAddition) { // Need header for this session
	    $message.="<P>Session $sessionid: {$addSession['title']}</P>\n<UL>";
	  }
	  $conflictThisAddition=true;
	  $message.="<LI>".htmlspecialchars($addToScheduleParticipants[$addBadgeid],ENT_NOQUOTES)." ($addBadgeid) ";
	  $message.="is not available.</LI>\n";
	}
      }
    }
    if ($conflictThisAddition) {
      $message.="</UL>";
    }
  }
  return (($message)?false:true); // empty message == no conflicts.
}

/* This is hardcoded to follow the workflow of editme -> vetted -> scheduled -> assigned
   We need to find a way to make it more configurable and flexible */
function SubmitMaintainRoom($ignore_conflicts) {
  global $link,$message;

  $newroomslots=$_SESSION['newroomslots']; // make it a variable so it can be substituted
  $conid=$_SESSION['conid']; // make it a variable so it can be substituted

  $numrows=$_POST["numrows"];
  $selroomid=$_POST["selroom"];
  get_name_and_email($name, $email); // populates them from session data or db as necessary
  $name=mysql_real_escape_string($name,$link);
  $email=mysql_real_escape_string($email,$link);
  $badgeid=mysql_real_escape_string($_SESSION['badgeid'],$link);

  for ($i=1; $i<=$numrows; $i++) { //***** need to update render as well to start at 1********
    if($_POST["del$i"]!=1) continue;
    $deleteScheduleIds[$_POST["row$i"]]=$_POST["rowsession$i"];
  }
  $incompleteRows=0;
  $completeRows=0;
  for ($i=1;$i<=$newroomslots;$i++) {
    if ($_POST["sess$i"]=="unset") continue;
    if ($_SESSION['connumdays']==1) {
      $day=1;
    } else {
      $day=$_POST["day$i"];
    }
    if ($day==0 or $_POST["hour$i"]==-1 or $_POST["min$i"]==-1) {
      $incompleteRows++;
      continue;
    }
    // starttimes in minutes from start of con
    $addToScheduleArray[$_POST["sess$i"]]=($day-1)*1440+$_POST["ampm$i"]*720+$_POST["hour$i"]*60+$_POST["min$i"];
    $completeRows++;
  }
  if (!$ignore_conflicts) {
    if (!check_room_sched_conflicts($deleteScheduleIds,$addToScheduleArray)) {
      echo "<P class=\"errmsg\">Database not updated.  There were conflicts</P>\n";
      echo $message;
      return false;
    }
  }
  if ($deleteScheduleIds!="") {

    // This should become an implode
    $delSchedIdList="";
    foreach ($deleteScheduleIds as $delid=>$delsessionid) {
      $delSchedIdList.="$delid,";
    }
    $delSchedIdList=substr($delSchedIdList,0,-1); // remove trailing comma

    //  Set status of deleted entries back to vetted.
    $vs=get_idlist_from_db("SessionStatuses",'statusid','statusname',"'vetted'");
    if ($_POST["nostatchange"] != "True") {
      $query = <<<EOD
UPDATE
      Sessions AS S,
      Schedule AS SC
  SET
    S.statusid=$vs
  WHERE
    S.sessionid=SC.sessionid AND
    S.conid=$conid AND
    S.conid=SC.conid AND
    SC.scheduleid IN ($delSchedIdList)
EOD;

      if (!mysqli_query($link,$query)) {
	$message=$query."<BR>Error updating database.<BR>";
	RenderError($title,$message);
	exit();
      }
    }
    $query="DELETE FROM Schedule WHERE conid=$conid AND scheduleid in ($delSchedIdList)";
    if (!mysqli_query($link,$query)) {
      $message=$query."<BR>Error updating database.<BR>";
      RenderError($title,$message);
      exit();
    }
    $rows=mysqli_affected_rows($link);
    echo "<P class=\"regmsg\">$rows session".($rows>1?"s":"")." removed from schedule.\n";
    // Was: (sessionid, badgeid, name, email_address, timestamp, sessioneditcode, statusid, editdescription)
    // And: $query.="($delsessionid,$conid,\"$badgeid\",\"$name\",\"$email\",null,5,$vs,null),";
    $query = <<<EOD
INSERT INTO SessionEditHistory
	  (sessionid, conid, badgeid, name, email_address, sessioneditcode, statusid)
        Values
EOD;
    foreach ($deleteScheduleIds as $delid=>$delsessionid) {
      $query.="($delsessionid,$conid,\"$badgeid\",\"$name\",\"$email\",5,$vs),";
    }
    $query=substr($query,0,-1); // remove trailing comma
    if (!mysqli_query($link,$query)) {
      $message=$query."<BR>Error updating database.<BR>";
      RenderError($title,$message);
      exit();
    }
  }
  if (!$addToScheduleArray) return (true); // nothing to add
  foreach ($addToScheduleArray as $sessionid => $startmin) {
    $hour=floor($startmin/60); // convert to hours since start of con
    $min=$startmin%60;
    $time=sprintf("%03d:%02d:00",$hour,$min);
    $query="INSERT INTO Schedule SET conid=$conid, sessionid=$sessionid, roomid=$selroomid, starttime=\"$time\"";
    if (!mysqli_query($link,$query)) {
      $message=$query."<BR>Error updating database.<BR>";
      RenderError($title,$message);
      exit();
    }
    // Set status of scheduled entries to Scheduled.
    $vs=get_idlist_from_db("SessionStatuses",'statusid','statusname',"'scheduled'");
    if ($_POST["nostatchange"] != "True") {
      $query="UPDATE Sessions SET statusid=$vs WHERE sessionid=$sessionid AND conid=$conid";
      if (!mysqli_query($link,$query)) {
	$message=$query."<BR>Error updating database.<BR>";
	RenderError($title,$message);
	exit();
      }
    }

// Record history of new entries to schedule
// WAS: (sessionid, badgeid, name, email_address, timestamp, sessioneditcode, statusid, editdescription)
// AND: $query.="($sessionid,$conid,\"$badgeid\",\"$name\",\"$email\",null,4,$vs,\"".time_description($time)." in $selroomid\")";
    $query = <<<EOD
INSERT INTO SessionEditHistory
      (sessionid, conid, badgeid, name, email_address, sessioneditcode, statusid)
        Values
EOD;
    $query.="($sessionid,$conid,\"$badgeid\",\"$name\",\"$email\",4,$vs)";
    if (!mysqli_query($link,$query)) {
      $message=$query."<BR>Error updating database.<BR>";
      RenderError($title,$message);
      exit();
    }
  }
  if ($completeRows) {
    echo "<P class=\"regmsg\">$completeRows new schedule entr".($completeRows>1?"ies":"y")." written to database.\n";
  }
  if ($incompleteRows) {
    echo "<P class=\"errmsg\">$incompleteRows row".($incompleteRows>1?"s":"")." not entered due to incomplete data.\n";
  }
  return (true);
}
?>
