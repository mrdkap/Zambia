<?php
require_once('PostingCommonCode.php');
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');
global $link;

// Pass in variables
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}
if ($conid=="") {$conid=$_SESSION['conid'];}

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
  }
}

// Short or long format
$single_line_p="F";
if (isset($_GET['short'])) {
  if ($_GET['short'] == "Y") {
    $single_line_p="T";
  } elseif ($_GET['short'] == "N") {
    $single_line_p="F";
  }
}

// Check to see if we are printing this to PDF
$print_p="F";
if ($_GET['print_p'] == "T") {$print_p="T";}
if ($_GET['print_p'] == "Y") {$print_p="T";}

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

// Check if feedback is allowed
$feedback_p=false;
$query="SELECT phasestate FROM PhaseTypes JOIN Phase USING (phasetypeid) WHERE phasetypename like '%Feedback Available%' AND conid=$conid";
list($phasestatrows,$phaseheader_array,$phase_array)=queryreport($query,$link,$title,$description,0);
if($phase_array[1]['phasestate'] == '0') {$feedback_p=true;}

// Defaults
$_SESSION['return_to_page']="PubsSched.php?format=$format&conid=$conid";
$track='concat("<A HREF=\"PubsSched.php?format=tracks&conid='.$conid.'#",trackname,"\"><i>",trackname,"</i></A>") AS Track';
$sestitle='concat("<A HREF=\"PubsSched.php?format=desc&conid='.$conid.'#",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"\">",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"</A>") AS Title';
$pubsname='if ((pubsname is NULL), " ", GROUP_CONCAT(DISTINCT "<A HREF=\"PubsBios.php?conid='.$conid.'#",pubsname,"\">",pubsname,"</A>",if(moderator in ("1","Yes"),"(m)","") SEPARATOR ", ")) AS "Participants"';
$starttime='GROUP_CONCAT(DISTINCT "<A HREF=\"PubsSched.php?format=sched&conid='.$conid.'#",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),"\"><i>",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),"</i></A>" SEPARATOR ", ") AS "Start Time"';
$room='GROUP_CONCAT(DISTINCT "<A HREF=\"PubsSched.php?format=rooms&conid='.$conid.'#",roomname,"\"><i>",roomname,"</i></A>" SEPARATOR ", ") AS Room';

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
  $description="<P>Track Schedules for all public sessions sorted by starting time.</P>\n";
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
  $sestitle='concat("<A NAME=\"",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"\"></A>",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS Title';
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
if ($single_line_p=="T") {
  $additionalinfo.="<A HREF=\"PubsSched.php?format=$format&conid=$conid\">full</A> or\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=$format&conid=$conid&short=Y&print_p=Y\">print</A> this,\n";
} else {
  $additionalinfo.="<A HREF=\"PubsSched.php?format=$format&conid=$conid&short=Y\">short</A> or\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=$format&conid=$conid&print_p=Y\">print</A> this,\n";
}
$additionalinfo.="the <A HREF=\"PubsBios.php?conid=$conid\">bios</A>\n";
$additionalinfo.="<A HREF=\"PubsBios.php?short=Y&conid=$conid\">(short)</A>,\n";
if ($format != "desc") {
  $additionalinfo.="the <A HREF=\"PubsSched.php?format=desc&conid=$conid\">description</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=desc&conid=$conid&short=Y\">(short)</A>,\n";
}
if ($format != "sched") {
  $additionalinfo.="the <A HREF=\"PubsSched.php?format=sched&conid=$conid\">timeslots</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=sched&conid=$conid&short=Y\">(short)</A>,\n";
}
if ($format != "tracks") {
  $additionalinfo.="the <A HREF=\"PubsSched.php?format=tracks&conid=$conid\">tracks</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>,\n";
}
if ($format != "trtime") {
  $additionalinfo.="the <A HREF=\"PubsSched.php?format=trtime&conid=$conid\">tracks by time</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>,\n";
}
if ($format != "rooms") {
  $additionalinfo.="the <A HREF=\"PubsSched.php?format=rooms&conid=$conid\">rooms</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
}
$additionalinfo.="or the <A HREF=\"Postgrid.php?conid=$conid\">grid</A>.</P>\n";
if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
  $additionalinfo.="<P>Click on the ";
  if (($format == "tracks") or ($format == "trtime")) {
    $additionalinfo.="<I>(iCal)</i> next to the track name to have an iCal Calendar\n";
    $additionalinfo.=" sent to your machine for automatic inclusion, and the\n";
  }
  $additionalinfo.="(iCal) tag to download the iCal calendar for the particular\n";
  $additionalinfo.="activity you want added to your calendar.</P>\n";
 }
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
    if(DATE_ADD(constartdate,INTERVAL connumdays DAY) > NOW(),
      concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>'),
      '') AS iCal,
    if((constartdate < NOW() AND phasestate = "0"),
      concat('<A HREF=Feedback.php?conid=$conid&sessionid=',sessionid,'>(Feedback)</A>'),
      '') AS Feedback,
    if(desc_good_web IS NULL,"",desc_good_web) AS 'Description'
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
    conid=$conid AND
    phasetypename like "%Feedback Available%" AND
    pubstatusname in ('Public') AND
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

/* Produce the report. */
$printstring=renderschedreport($format,$header_break,$single_line_p,$print_p,$elements,$element_array);

// Display, with the option of printing.
if ($print_p == "F") {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo $printstring;
  correct_footer();
} else {
  $printstring->Output('Schedule'.$format.'All.pdf', 'I');
}
?>
