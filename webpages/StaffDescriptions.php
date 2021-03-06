<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;
$ConStart=$_SESSION['constartdate']; // make it a variable so it can be substituted
$ConNumDays=$_SESSION['connumdays']; // make it a variable so it can be substituted

$conid=$_GET['conid'];
if ($conid=="") {$conid=$_SESSION['conid'];}

if (isset($_GET['feedback'])) {
  $feedbackp='?feedback=y';
} else {
  $feedbackp='';
}

// LOCALIZATIONS
$_SESSION['return_to_page']="StaffDescriptions.php$feedbackp";
$title="Session Descriptions";
$description="<P>Descriptions for all sessions.</P>\n";
$additionalinfo="<P>Click on the time to visit the session's <A HREF=\"StaffSchedule.php$feedbackp\">timeslot</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php$feedbackp\">bio</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"StaffTracks.php$feedbackp\">track</A>, or visit the <A HREF=\"grid.php?standard=y&unpublished=y\">grid</A>.</P>\n";
if ((strtotime($ConStart)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>Click on the (iCal) tag to download the iCal calendar for the particular activity you want added to your calendar.</P>\n";
 }
if (strtotime($ConStart) < time()) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
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
$pubstatus_string=implode(",",$pubstatus_array);

/* This query grabs everything necessary for the descriptions to be printed. */
$query = <<<EOD
SELECT
    if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffBios.php$feedbackp#',pubsname,'\">',pubsname,'</A>',if((moderator in ('1','Yes')),'(m)','')) SEPARATOR ', ')) AS 'Participants',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffSchedule.php$feedbackp#',DATE_FORMAT(ADDTIME('$ConStart',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStart',starttime),'%a %l:%i %p'),'</A>') SEPARATOR ', ') AS 'Start Time',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffTracks.php$feedbackp#',trackname,'\">',trackname,'</A>')) as 'Track',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR ', ') AS Roomname,
    estatten AS Attended,
    Sessionid,
    if((questiontypeid IS NULL),"",questiontypeid) AS questiontypeid,
    concat('<A NAME=\"',sessionid,'\"></A>',title_good_web) as Title,
    subtitle_good_web AS Subtitle,
    concat('<A HREF=StaffPrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=StaffFeedback.php?sessionid=',sessionid,'>(Feedback)</A>') AS Feedback,
    concat(desc_good_web,'</P>') AS 'Web Description',
    concat(desc_good_book,'</P>') AS 'Book Description'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    LEFT JOIN TypeHasQuestionType USING (typeid,conid)
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
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
      WHERE
          conid=$conid AND
	  descriptiontypeid=3 AND
	  biostateid=3 AND
	  biodestid=2 AND
	  descriptionlang='en-us') DGB USING (sessionid)
  WHERE
    conid=$conid AND
    pubstatusname in ($pubstatus_string) AND
    (volunteer=0 OR volunteer="0" OR volunteer IS NULL) AND
    (introducer=0 OR introducer="0" OR introducer IS NULL) AND
    (aidedecamp=0 OR aidedecamp="0" OR aidedecamp IS NULL)
  GROUP BY
    sessionid
  ORDER BY
    title
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

if (isset($_GET['feedback'])) {
  $feedback_array=getFeedbackData("");
 }

/* Printing body.  Uses the page-init then creates the Descriptions. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
//echo "<P>Query: $query</P>\n";
//print_r($feedback_array);
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
  if ((strtotime($ConStart)+(60*60*24*$ConNumDays)) > time()) {
    echo sprintf("&mdash; %s",$element_array[$i]['iCal']);
  }
  if (strtotime($ConStart) < time()) {
    if ($element_array[$i]['Attended']) {
      echo sprintf("&mdash; About %s Attended",$element_array[$i]['Attended']);
    }
    echo sprintf("&mdash; %s",$element_array[$i]['Feedback']);
  }
  if ($_SESSION['role']=="Participant") {
    echo sprintf("</DT>\n<DD><P>%s",$element_array[$i]['Web Description']);
  } else {
    echo sprintf("  </DT>\n  <DD><P>Web: %s</P>\n",$element_array[$i]['Web Description']);
    echo sprintf("  </DD>\n  <DD><P>Book: %s</P>\n",$element_array[$i]['Book Description']);
    $feedback_file=sprintf("../Local/Feedback/%s.jpg",$element_array[$i]["Sessionid"]);
    if ((file_exists($feedback_file)) and (isset($_GET['feedback']))) {
      echo "  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
      echo sprintf ("<img src=\"%s\">\n<br>\n",$feedback_file);
    }
    if (isset($feedback_array['graph'][$element_array[$i]["Sessionid"]."-".$conid])) {
      echo "  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
      $graphstring=generateSvgString($element_array[$i]["Sessionid"],$conid);
      echo $graphstring;
    }
    if ($feedback_array[$element_array[$i]["Sessionid"]."-".$conid]) {
      echo "  </DD>\n    <DD>Written feedback from surveys:\n<br>\n";
      echo sprintf("%s<br>\n",$feedback_array[$element_array[$i]["Sessionid"]."-".$conid]);
    }
  }
  if ($element_array[$i]['Participants']) {
    echo sprintf("</DD>\n<DD><i>%s</i>",$element_array[$i]['Participants']);
  }
  echo "</DD></P>\n";
 }
echo "</DL>\n";
correct_footer();
?>
