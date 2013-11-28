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

$groupby="progguiddesc";
$roomname="concat('<A HREF=\"Tracks.php$passon#',roomname,'\">',roomname,'</A>')";
$trackname="trackname";
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
  $groupby="sessionid";
  $roomname="roomname";
  $trackname="concat('<A HREF=\"Tracks.php$passon#',trackname,'\">',trackname,'</A>')";
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
$_SESSION['return_to_page']="Descriptions.php";
$title="Session Descriptions for $conname";
$description="<P>Descriptions for all sessions.</P>\n";
$additionalinfo="<P>Click on the time to visit the session's <A HREF=\"Schedule.php$passon\">timeslot</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"Bios.php$passon\">bio</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"Tracks.php$passon\">track</A>, or visit the <A HREF=\"Postgrid.php$passon\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$connumdays)) > time()) {
  $additionalinfo.="<P>Click on the (iCal) tag to download the iCal calendar for the particular activity you want added to your calendar.</P>\n";
 }
if ((strtotime($ConStartDatim) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the descriptions to be printed. */
$query = <<<EOD
SELECT
    if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"Bios.php$passon#',pubsname,'\">',pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"Schedule.php$passon#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') SEPARATOR ', ') as 'Start Time',
    GROUP_CONCAT(DISTINCT $trackname SEPARATOR ', ') as 'Track',
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
    GROUP_CONCAT(DISTINCT concat('<A NAME=\"',sessionid,'\"></A>',title) SEPARATOR ', ') as Title,
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
    JOIN $ReportDB.Rooms USING (roomid)
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
  GROUP BY
    $groupby
  ORDER BY
    S.title
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the Descriptions. */
topofpagereport($title,$description,$additionalinfo);
echo "<DL>\n";
for ($i=1; $i<=$elements; $i++) {
  echo sprintf("<P><DT><B>%s</B>",$element_array[$i]['Title']);
  if ($element_array[$i]['Subtitle'] !='') {
    echo sprintf(": %s",$element_array[$i]['Subtitle']);
  }
  if ($element_array[$i]['Track']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Track']);
  }
  if ($element_array[$i]['Start Time']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Start Time']);
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