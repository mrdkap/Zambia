<?php
require_once('PostingCommonCode.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// Deal with what is passed in.
if (!empty($_SERVER['QUERY_STRING'])) {
  $passon="?".$_SERVER['QUERY_STRING'];
}

$conid=$_GET['conid'];

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from $ReportDB.ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStartDatim=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

$trackname="trackname";
$roomname="concat('<A HREF=\"Tracks.php$passon#',roomname,'\">',roomname,'</A>')";
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
  $trackname="concat('<A HREF=\"Tracks.php$passon#',trackname,'\">',trackname,'</A>')";
  $roomname="roomname";
}

// Check if feedback is allowed
$query = <<<EOD
SELECT
    phasestate
  FROM
      $ReportDB.PhaseTypes
    JOIN $ReportDB.Phase USING (phasetypeid)
  WHERE
    phasetypename like '%Feedback Available%' AND
    conid=$conid
EOD;
    
// Retrieve query
list($phasestatrows,$phaseheader_array,$phase_array)=queryreport($query,$link,$title,$description,0);

// LOCALIZATIONS
$_SESSION['return_to_page']="Schedule.html";
$title="Event Schedule for $conname";
$description="<P>Schedule for all sessions.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.php$passon\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"Bios.php$passon\">bio</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"Tracks.php$passon\">track</A>, or visit the <A HREF=\"Postgrid.php$passon\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$connumdays)) > time()) {
  $additionalinfo.="<P>Click on the (iCal) tag to download the iCal calendar for the particular activity you want added to your calendar.</P>\n";
 }
if ((strtotime($ConStartDatim) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the schedule to be printed. */
$query = "SELECT ";
if (strtoupper(DOUBLE_SCHEDULE)=="TRUE") {
  $query.="  if ((pubsname is NULL), ' ', concat('<A HREF=\"Bios.php$passon#',pubsname,'\">',pubsname,'</A>',if((moderator=1),'(m)',''))) as 'Participants',";
} else {
  $query.="  if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"Bios.php$passon#',pubsname,'\">',pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',";
}
$query.= <<<EOD
    concat('<A NAME=\"',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\"></A>',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p')) as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
EOD;
if (strtoupper(DOUBLE_SCHEDULE)=="TRUE") {
  $query.="    $roomname as Roomname,\n";
  $query.="    $trackname as 'Track',";
} else {
  $query.="    GROUP_CONCAT(DISTINCT $roomname SEPARATOR ', ') as Roomname,\n";
  $query.="    GROUP_CONCAT(DISTINCT $trackname SEPARATOR ', ') as 'Track',";
}
$query.= <<<EOD
    Sessionid,
    concat('<A HREF=\"Descriptions.php$passon#',sessionid,'\">',title,'</A>') as Title,
    secondtitle AS Subtitle,
    concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=Feedback.php?sessionid=',sessionid,'>(Feedback)</A>') AS Feedback,
    concat('<P>',progguiddesc,'</P>') as Description
  FROM
EOD;
if ($conid==CON_KEY) {
  $query.=" Sessions S JOIN Schedule USING (sessionid) LEFT JOIN ParticipantOnSession USING (sessionid)";
} else {
  $query.=" $ReportDB.Sessions S JOIN $ReportDB.Schedule USING (sessionid,conid) LEFT JOIN $ReportDB.ParticipantOnSession USING (sessionid,conid)";
}
$query.= <<<EOD
    JOIN $ReportDB.Rooms R USING (roomid)
    JOIN $ReportDB.Tracks USING (trackid)
    LEFT JOIN $ReportDB.Participants USING (badgeid)
    JOIN $ReportDB.PubStatuses USING (pubstatusid)
  WHERE
EOD;
if ($conid==CON_KEY) {
  $query.="";
} else {
  $query.=" conid=$conid AND  ";
}
$query.= <<<EOD
    pubstatusname in ($pubstatus_check) AND
    (volunteer=0 OR volunteer IS NULL) AND
    (introducer=0 OR introducer IS NULL) AND
    (aidedecamp=0 OR aidedecamp IS NULL)
EOD;
if (strtoupper(DOUBLE_SCHEDULE)=="TRUE") {
  $query.="";
} else {
  $query.="  GROUP BY sessionid ";
}
$query.= <<<EOD
  ORDER BY
    starttime,
    R.display_order
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the Schedule. */
topofpagereport($title,$description,$additionalinfo);
echo "<DL>\n";
$printtime="";
for ($i=1; $i<=$elements; $i++) {
  if ($element_array[$i]['Start Time'] != $printtime) {
    $printtime=$element_array[$i]['Start Time'];
    echo sprintf("</DL><P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$printtime);
  }
  echo sprintf("<P><DT><B>%s</B>",$element_array[$i]['Title']);
  if ($element_array[$i]['Subtitle'] !='') {
    echo sprintf(": %s",$element_array[$i]['Subtitle']);
  }
  if ($element_array[$i]['Track']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Track']);
  }
  if ($element_array[$i]['Duration']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Duration']);
  }
  if ($element_array[$i]['Roomname']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Roomname']);
  }
  if ((strtotime($ConStartDatim)+(60*60*24*$connumdays)) > time()) {
    echo sprintf("&mdash; %s",$element_array[$i]['iCal']);
  }
  if ((strtotime($ConStartDatim) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
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