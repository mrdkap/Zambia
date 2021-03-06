<?php
require_once('PostingCommonCode.php');
global $link;

// Deal with what is passed in.
if (!empty($_SERVER['QUERY_STRING'])) {
  $passon="?".$_SERVER['QUERY_STRING'];
}

if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

$roomname="concat('<A NAME=\"',roomname,'\"></A>',roomname)";
$trackname="trackname";
$orderby="roomname";
if (isset($_GET['volunteer'])) {
  $pubstatus_check="'Volunteer'";
} elseif (isset($_GET['registration'])) {
  $pubstatus_check="'Reg Staff'";
} elseif (isset($_GET['sales'])) {
  $pubstatus_check="'Sales Staff'";
} elseif (isset($_GET['vfull'])) {
  $pubstatus_check="'Volunteer','Reg Staff','Sales Staff'";
} else {
  $pubstatus_check="'Public'";
  $roomname="roomname";
  $trackname="concat('<A NAME=\"',trackname,'\"></A>',trackname,if((DATE_ADD('$ConStart',INTERVAL $connumdays DAY)>NOW()),concat(' <A HREF=TrackScheduleIcal.php?trackid=',trackid,'><I>(iCal)</I></A>'),''))";
  $orderby="trackname";
}

// Check if feedback is allowed
$query = <<<EOD
SELECT
    phasestate
  FROM
      PhaseTypes
    JOIN Phase USING (phasetypeid)
  WHERE
    phasetypename like '%Feedback Available%' AND
    conid=$conid
EOD;
    
// Retrieve query
list($phasestatrows,$phaseheader_array,$phase_array)=queryreport($query,$link,$title,$description,0);

// LOCALIZATIONS
$_SESSION['return_to_page']="Tracks.html";
$title="Event Tracks Schedule for $conname";
$description="<P>Track Schedules for all sessions.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.php$passon\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"Bios.php$passon\">bio</A>, the time to visit the session's\n";
$additionalinfo.="<A HREF=\"Schedule.php$passon\">timeslot</A>, or visit the <A HREF=\"Postgrid.php$passon\">grid</A>.</P>\n";
if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
  $additionalinfo.="<P>Click on the <I>(iCal)</i> next to the track name to have an iCal Calendar sent to your machine for\n";
  $additionalinfo.="automatic inclusion, and the (iCal) next to the particular activity for one of that activity.</P>";
 }
if ((strtotime($ConStart) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the schedule to be printed. */
$query = <<<EOD
SELECT
  if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"Bios.php$passon#',pubsname,'\">',pubsname,'</A>',if((moderator in ('1','Yes')),'(m)','')) SEPARATOR ', ')) as 'Participants',
    concat('<A HREF=\"Schedule.php$passon#',DATE_FORMAT(ADDTIME('$ConStart',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStart',starttime),'%a %l:%i %p'),'</A>') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT $roomname SEPARATOR ', ') as Roomname,
    Sessionid,
    GROUP_CONCAT(DISTINCT $trackname SEPARATOR ', ') as 'Track',
    concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=Feedback.php?sessionid=',sessionid,'>(Feedback)</A>') AS Feedback,
    concat('<A HREF=\"Descriptions.php$passon#',sessionid,'\">',title_good_web,'</A>') as Title,
    subtitle_good_web AS Subtitle,
    concat('<P>',desc_good_web,'</P>') as Description
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN (SELECT
        sessionid,
	descriptiontext as title_good_web
      FROM
          Descriptions
      WHERE
          conid=$conid AND
	  descriptiontypeid=1 AND
	  biostateid=3 AND
	  biodestid=1 AND
	  descriptionlang='en-us') TGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
      WHERE
          conid=$conid AND
	  descriptiontypeid=2 AND
	  biostateid=3 AND
	  biodestid=1 AND
	  descriptionlang='en-us') SGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
      WHERE
          conid=$conid AND
	  descriptiontypeid=3 AND
	  biostateid=3 AND
	  biodestid=1 AND
	  descriptionlang='en-us') DGW USING (sessionid)
  WHERE
    conid=$conid AND
    pubstatusname in ($pubstatus_check) AND
    (volunteer=0 OR volunteer="0" OR volunteer IS NULL) AND
    (introducer=0 OR introducer="0" OR introducer IS NULL) AND
    (aidedecamp=0 OR aidedecamp="0" OR aidedecamp IS NULL)
  GROUP BY
    sessionid
  ORDER BY
    $orderby,
    starttime,
    R.display_order
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the Schedule. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo "<DL>\n";
$printtrack="";
for ($i=1; $i<=$elements; $i++) {
  if ($roomname == "roomname") {
    if ($element_array[$i]['Track'] != $printtrack) {
      $printtrack=$element_array[$i]['Track'];
      echo sprintf("</DL><P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$printtrack);
    }
  } else {
    if ($element_array[$i]['Roomname'] != $printtrack) {
      $printtrack=$element_array[$i]['Roomname'];
      echo sprintf("</DL><P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$printtrack);
    }
  }
  echo sprintf("<P><DT><B>%s</B> &mdash; %s &mdash; <i>%s</i>",
	       $element_array[$i]['Title'],$element_array[$i]['Start Time'],$element_array[$i]['Duration']);
  if ($element_array[$i]['Roomname']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Roomname']);
  }
  if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
    echo sprintf("&mdash; %s",$element_array[$i]['iCal']);
  }
  if ((strtotime($ConStart) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
    echo sprintf("&mdash; %s",$element_array[$i]['Feedback']);
  }
  echo sprintf("</DT>\n<DD>%s",$element_array[$i]['Description']);
  if ($element_array[$i]['Participants']) {
    echo sprintf("<i>%s</i>",$element_array[$i]['Participants']);
  }
  echo "</DD></P>\n";
 }
echo "</DL>\n";
correct_footer();
?>