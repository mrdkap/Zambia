<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;

// Pass in variables
$conid=$_GET['conid'];
if ($conid=="") {$conid=$_SESSION['conid'];}

$conid_or_badgeid="conid=$conid";
$badgeid_p="F";
if ((!empty($_GET['badgeid'])) and (is_numeric($_GET['badgeid']))) {
  $checkbadgeid=$_GET['badgeid'];
  $conid_or_badgeid="badgeid=$checkbadgeid";
  $badgeid_p="T";
}

// Format to print in, and modifications to the query and the additionalinfo
$format="desc";
if (isset($_GET['format'])) {
  if ($_GET['format'] == "tracks") {
    $format="tracks";
  } elseif ($_GET['format'] == "trtime") {
    $format="trtime";
  } elseif ($_GET['format'] == "desc") {
    $format="desc";
  } elseif ($_GET['format'] == "rooms") {
    $format="rooms";
  } elseif ($_GET['format'] == "sched") {
    $format="sched";
  } elseif ($_GET['format'] == "feedback") {
    $format="desc";
    $_GET['feedback']="Y";
    $_GET['short']="N";
  }
}

$_SESSION['return_to_page']="StaffSched.php?format=$format&conid=$conid";

// Short or long format
$single_line_p="F";
if (isset($_GET['short'])) {
  if ($_GET['short'] == "Y") {
    $single_line_p="T";
    $_SESSION['return_to_page'].="StaffSched.php?format=$format&conid=$conid&short=\"Y\"";
  } elseif ($_GET['short'] == "N") {
    $single_line_p="F";
  }
}

// Show returned feedback, sets the single_line_p to long, by definition.
$feedback_p="F";
if (isset($_GET['feedback'])) {
  if ($_GET['feedback'] == "Y") {
    $feedback_p="T";
    $single_line_p="F";
    $_SESSION['return_to_page'].="StaffSched.php?format=$format&conid=$conid&feedback=Y";
    if (may_I("Staff")) {
      if (empty($checkbadgeid)) {
        $feedback_array=getFeedbackData("");
      } else {
        $feedback_array=getFeedbackData($checkbadgeid);
      }
    } else {
      $feedback_array=getFeedbackData($_SESSION['badgeid']);
    }
  } elseif ($_GET['feedback'] == "N") {
    $feedback_p='F';
  }
}

// Generate the constraints on what is shown
if (may_I('General')) {$pubstatus_array[]='\'Volunteer\'';}
if (may_I('Programming')) {$pubstatus_array[]='\'Prog Staff\'';}
if (may_I('Participant')) {$pubstatus_array[]='\'Public\'';}
if (may_I('Events')) {$pubstatus_array[]='\'Event Staff\'';}
if (may_I('Registration')) {$pubstatus_array[]='\'Reg Staff\'';}
if (may_I('Watch')) {$pubstatus_array[]='\'Watch Staff\'';}
if (may_I('Vendor')) {$pubstatus_array[]='\'Vendor Staff\'';}
if (may_I('Sales')) {$pubstatus_array[]='\'Sales Staff\'';}
if (may_I('Fasttrack')) {$pubstatus_array[]='\'Fast Track\'';}
if (may_I('Logistics')) {$pubstatus_array[]='\'Logistics\'';}
if (may_I('Vendor')) {$pubstatus_array[]='\'Vendor\'';}
if (may_I('Lounge')) {$pubstatus_array[]='\'Lounge Staff\'';}
if (may_I('Staff')) {$pubstatus_array[]='\'For Feedback\'';}
$pubstatus_check=implode(",",$pubstatus_array);

// Hack to force feedback for the feedback page
if ($_GET['format']=="feedback") {$pubstatus_check="'Public','For Feedback'";}

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

// Check if giving feedback is allowed
$query="SELECT phasestate FROM PhaseTypes JOIN Phase USING (phasetypeid) WHERE phasetypename like '%Feedback Available%' AND conid=$conid";
list($phasestatrows,$phaseheader_array,$phase_array)=queryreport($query,$link,$title,$description,0);

// Defaults
$track='concat("<A HREF=\"StaffSched.php?format=tracks&conid='.$conid.'#",trackname,"\"><i>",trackname,"</i></A>") AS Track';
$sestitle='concat("<A HREF=\"StaffSched.php?format=desc&conid='.$conid.'#",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"\">",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"</A><SUP><A HREF=\"EditSession.php?id=",sessionid,"\">(edit)</A></SUP>") AS Title';
$pubsname='concat(if ((pubsname is NULL), " ", GROUP_CONCAT(DISTINCT "<A HREF=\"StaffBios.php?conid='.$conid.'#",pubsname,"\">",pubsname,"</A>",if(moderator in ("1","Yes"),"(m)","") SEPARATOR ", ")),"<SUP><A HREF=\"StaffAssignParticipants.php?selsess=",sessionid,"\">(edit)</A></SUP>") AS "Participants"';
$starttime='GROUP_CONCAT(DISTINCT "<A HREF=\"StaffSched.php?format=sched&conid='.$conid.'#",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),"\"><i>",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),"</i></A>" SEPARATOR ", ") AS "Start Time"';
$room='GROUP_CONCAT(DISTINCT "<A HREF=\"StaffSched.php?format=rooms&conid='.$conid.'#",roomname,"\"><i>",roomname,"</i></A>" SEPARATOR ", ") AS Room';

// LOCALIZATIONS
if ($format == "tracks") {
  $title="Event Track Schedule for $conname by Name";
  $description="<P>Track Schedules for all public sessions sorted by session name.</P>\n";
  $track='concat("<A NAME=\"",trackname,"\"></A>",trackname,if((DATE_ADD(constartdate,INTERVAL connumdays DAY)>NOW()),concat(" <A HREF=TrackScheduleIcal.php?trackid=",trackid,"><I>(iCal)</I></A>"),"")) AS Track';
  $orderby="trackname,title_good_web,starttime,R.roomname";
  $header_break="Track";
}
if ($format == "trtime") {
  $title="Event Track Schedule for $conname by Time";
  $description="<P>Track Schedules for all public sessions sorted by time.</P>\n";
  $track='concat("<A NAME=\"",trackname,"\"></A>",trackname,if((DATE_ADD(constartdate,INTERVAL connumdays DAY)>NOW()),concat(" <A HREF=TrackScheduleIcal.php?trackid=",trackid,"><I>(iCal)</I></A>"),"")) AS Track';
  $orderby="trackname,starttime,title_good_web,R.roomname";
  $header_break="Track";
}
if ($format == "rooms") {
  $title="Event Room Schedule for $conname";
  $description="<P>Room Schedules for all public sessions.</P>\n";
  $room='concat("<A NAME=\"",roomname,"\"></A>",roomname) AS Room';
  $orderby="R.Roomname,starttime,title_good_web";
  $header_break="Room";
}
if ($format == "desc") {
  $title="Session Descriptions for $conname";
  $description="<P>Descriptions for all public sessions.</P>\n";
  $sestitle='concat("<A NAME=\"",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"\"></A>",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"<SUP><A HREF=\"EditSession.php?id=",sessionid,"\">(edit)</A></SUP>") AS Title';
  $orderby="title_good_web";
  $header_break="";
}
if ($format == "sched") {
  $title="Event Schedule for $conname";
  $description="<P>Schedule for all public sessions.</P>\n";
  $starttime='concat("<A NAME=\"",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),"\"></A>",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p")) AS "Start Time"';
  $orderby="starttime,R.display_order,title_good_web";
  $header_break="Start Time";
}

// Additional info setup.
$additionalinfo="<P>See also this ";
if ($feedback_p=="T") {
  $additionalinfo.="<A HREF=\"StaffSched.php?format=$format&conid=$conid\">w/o feedback</A> or\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=$format&conid=$conid&short=Y\">short</A>,\n";
} elseif ($single_line_p=="T") {
  $additionalinfo.="<A HREF=\"StaffSched.php?format=$format&conid=$conid\">full</A> or\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=$format&conid=$conid&feedback=Y\">w/feedback</A>,\n";
} else {
  $additionalinfo.="<A HREF=\"StaffSched.php?format=$format&conid=$conid&short=Y\">short</A> or\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=$format&conid=$conid&feedback=Y\">w/feedback</A>,\n";
}
$additionalinfo.="the <A HREF=\"StaffBios.php?conid=$conid\">bios</A>\n";
$additionalinfo.="<A HREF=\"StaffBios.php?short=Y&conid=$conid\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffBios.php?pic_p=N&conid=$conid\">(without images)</A>,\n";
if ($format != "desc") {
  $additionalinfo.="the <A HREF=\"StaffSched.php?format=desc&conid=$conid\">description</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid&short=Y\">(short)</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
}
if ($format != "sched") {
  $additionalinfo.="the <A HREF=\"StaffSched.php?format=sched&conid=$conid\">timeslots</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=sched&conid=$conid&short=Y\">(short)</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=sched&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
}
if ($format != "tracks") {
  $additionalinfo.="the <A HREF=\"StaffSched.php?format=tracks&conid=$conid\">tracks</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
}
if ($format != "trtime") {
  $additionalinfo.="the <A HREF=\"StaffSched.php?format=trtime&conid=$conid\">tracks by time</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
}
if ($format != "rooms") {
  $additionalinfo.="the <A HREF=\"StaffSched.php?format=rooms&conid=$conid\">rooms</A>\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
  $additionalinfo.="<A HREF=\"StaffSched.php?format=rooms&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
}
$additionalinfo.="or the <A HREF=\"Postgrid.php?conid=$conid\">grid</A>.</P>\n";

// Add the ical tag if the time is right.
if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
  $additionalinfo.="<P>Click on the ";
  if (($format == "tracks") or ($format == "trtime")) {
    $additionalinfo.="<I>(iCal)</i> next to the track name to have an iCal Calendar\n";
    $additionalinfo.=" sent to your machine for automatic inclusion, and the\n";
  }
  $additionalinfo.="(iCal) tag to download the iCal calendar for the particular\n";
  $additionalinfo.="activity you want added to your calendar.</P>\n";
 }

// Further feedback-only hackery
//if ($_GET['format']=="feedback") {$additionalinfo=print_r($feedback_array,t);}
if ($_GET['format']=="feedback") {$additionalinfo="";}

// Add the feedback tag if the time is right and the phase allows for it.
if ((strtotime($ConStart) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the schedule to be printed. */
$query = <<<EOD
SELECT
    $track,
    $sestitle,
    $pubsname,
    $starttime,
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat("<i>",date_format(duration,'%i'),'min</i>')
      WHEN MINUTE(duration)=0 THEN
        concat("<i>",date_format(duration,'%k'),'hr</i>')
      ELSE
        concat("<i>",date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min</i>')
      END AS Duration,
    $room,
    if(estatten IS NULL,'',estatten) AS Estatten,
    Sessionid,
    conid,
    conname,
    if(DATE_ADD(constartdate,INTERVAL connumdays DAY) > NOW(),
      concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>'),
      '') AS iCal,
    if((constartdate < NOW() AND phasestate = "0"),
      concat('<A HREF=Feedback.php?conid=$conid&sessionid=',sessionid,'>(Feedback)</A>'),
      '') AS Feedback,
    concat(if(desc_good_web IS NULL,"",concat("Web: ", desc_good_web)),
      if((desc_good_web IS NOT NULL) AND (desc_good_book IS NOT NULL),"</P>\n<P>",""),
      if(desc_good_book IS NULL,"",concat("Book: ",desc_good_book))) AS 'Description'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
    JOIN Phase USING (conid)
    JOIN PhaseTypes USING (phasetypeid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('title') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
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
	  descriptiontypename in ('subtitle') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
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
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
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
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('book') AND
          descriptionlang='en-us') DGB USING (sessionid,conid)
  WHERE
    $conid_or_badgeid AND
    phasetypename like "%Feedback Available%" AND
    pubstatusname in ($pubstatus_check) AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  GROUP BY
    sessionid
  ORDER BY
    $orderby
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// Return the whole con feedback
// If asked for by a staff person, feedback is included, and it's not just a person
if (may_I("Staff") and ($feedback_p=="T") and ($badgeid_p=="F")) {
   $elements++;
   $element_array[$elements]['Track']=" ";
   $element_array[$elements]['Title']="Whole Con Feedback";
   $element_array[$elements]['Participants']=" ";
   $element_array[$elements]['Start Time']=" ";
   $element_array[$elements]['Duration']=" ";
   $element_array[$elements]['Room']=" ";
   $element_array[$elements]['Estatten']="";
   $element_array[$elements]['Sessionid']="-1";
   $element_array[$elements]['conid']="$conid";
   $element_array[$elements]['conname']="$conname";
   $element_array[$elements]['iCal']="";
   $element_array[$elements]['Feedback']="";
   $element_array[$elements]['Description']="";
}

// Add the feedback
for ($i=1; $i<=$elements; $i++) {
  if ($_SESSION["conid"] != $element_array[$i]["conid"]) {
    $element_array[$i]['Description']=$element_array[$i]["conname"]."<br>".$element_array[$i]['Description'];
  }
  $feedback_file=sprintf("../Local/%s/Feedback/%s.jpg",$conid,$element_array[$i]["Sessionid"]);
  if ((file_exists($feedback_file)) and ($feedback_p="T")) {
    $element_array[$i]['Description'].="  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
    $element_array[$i]['Description'].=sprintf("<img src=\"%s\">\n<br>\n",$feedback_file);
  }
  if (isset($feedback_array['graph'][$element_array[$i]["Sessionid"]."-".$element_array[$i]["conid"]])) {
    $element_array[$i]['Description'].="  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
    $element_array[$i]['Description'].=generateSvgString($element_array[$i]["Sessionid"],$element_array[$i]["conid"]);
  }
  if ($feedback_array[$element_array[$i]["Sessionid"]."-".$element_array[$i]["conid"]]) {
    $element_array[$i]['Description'].="  </DD>\n    <DD>Written feedback from surveys:\n<br>\n";
    $element_array[$i]['Description'].=sprintf("%s<br>\n",$feedback_array[$element_array[$i]["Sessionid"]."-".$element_array[$i]["conid"]]);
  }
}

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

/* Produce the report. */
$printstring=renderschedreport($format,$header_break,$single_line_p,$elements,$element_array);
echo $printstring;

correct_footer();
?>
