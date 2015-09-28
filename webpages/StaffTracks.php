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
$_SESSION['return_to_page']="StaffTracks.php$feedbackp";
$title="Event Tracks Schedule";
$description="<P>Track Schedules for all sessions.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"StaffDescriptions.php$feedbackp\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php$feedbackp\">bio</A>, the time to visit the session's\n";
$additionalinfo.="<A HREF=\"StaffSchedule.php$feedbackp\">timeslot</A>, or visit the <A HREF=\"grid.php?standard=y&unpublished=y\">grid</A>.</P>\n";
if ((strtotime($ConStart)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>Click on the <I>(iCal)</i> next to the track name to have an iCal Calendar sent to your machine for\n";
  $additionalinfo.="automatic inclusion, and the (iCal) next to the particular activity for one of that activity.</P>";
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
$pubstatus_string=implode(",",$pubstatus_array);

/* This query grabs everything necessary for the schedule to be printed. */
$query = <<<EOD
SELECT
    if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffBios.php$feedbackp#',pubsname,'\">',pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) AS 'Participants',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffSchedule.php$feedbackp#',DATE_FORMAT(ADDTIME('$ConStart',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStart',starttime),'%a %l:%i %p'),'</A>') SEPARATOR ', ') AS 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR ', ') AS Room,
    estatten AS Attended,
    Sessionid,
    if((questiontypeid IS NULL),"",questiontypeid) AS questiontypeid,
    GROUP_CONCAT(DISTINCT concat('<A NAME=\"',trackname,'\">',trackname,'</A>',if((DATE_ADD('$ConStart',INTERVAL $ConNumDays DAY)>NOW()),concat(' <A HREF=StaffTrackScheduleIcal.php?trackid=',trackid,'><I>(iCal)</I></A>'),''))) AS Track,
    concat('<A HREF=StaffPrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=StaffFeedback.php?sessionid=',sessionid,'>(Feedback)</A>') AS Feedback,
    concat('<A HREF=\"StaffDescriptions.php$feedbackp#',sessionid,'\">',title_good_web,if(subtitle_good_web IS NULL,"",concat(": ",subtitle_good_web)),'</A>') AS Title,
    desc_good_web AS 'Web Description',
    desc_good_book AS 'Book Description'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    LEFT JOIN TypeHasQuestionType USING (typeid,conid)
    JOIN PubStatuses USING (pubstatusid)
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
    pubstatusname in ($pubstatus_string) AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  GROUP BY
    sessionid
  ORDER BY
    trackname,
    starttime,
    R.display_order
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

if (isset($_GET['feedback'])) {
  $feedback_array=getFeedbackData("");
 }

/* Printing body.  Uses the page-init then creates the Schedule. */
topofpagereport($title,$description,$additionalinfo);
echo "<DL>\n";
$printtrack="";
for ($i=1; $i<=$elements; $i++) {
  if ($element_array[$i]['Track'] != $printtrack) {
    $printtrack=$element_array[$i]['Track'];
    echo sprintf("</DL><P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$printtrack);
  }
  echo sprintf("<P><DT><B>%s</B> &mdash; %s &mdash; <i>%s</i>",
	       $element_array[$i]['Title'],$element_array[$i]['Start Time'],$element_array[$i]['Duration']);
  if ($element_array[$i]['Room']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Room']);
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
    echo sprintf("</DT>\n<DD><P>%s</P>",$element_array[$i]['Web Description']);
  } else {
    echo sprintf("  </DT>\n  <DD><P>Web: %s</P>\n",$element_array[$i]['Web Description']);
    echo sprintf("  </DD>\n  <DD><P>Book: %s</P>\n",$element_array[$i]['Book Description']);
    $feedback_file=sprintf("../Local/Feedback/%s.jpg",$element_array[$i]["Sessionid"]);
    if ((file_exists($feedback_file)) and (isset($_GET['feedback']))) {
      echo "  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
      echo sprintf ("<img src=\"%s\">\n<br>\n",$feedback_file);
    }
    if (isset($feedback_array['graph'][$element_array[$i]["Sessionid"]])) {
      echo "  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
      $graphstring=generateSvgString($element_array[$i]["Sessionid"]);
      echo $graphstring;
    }
    if ($feedback_array[$element_array[$i]["Sessionid"]]['comments']) {
      echo "  </DD>\n    <DD>Written feedback from surveys:\n<br>\n";
      echo sprintf("%s<br>\n",$feedback_array[$element_array[$i]["Sessionid"]]);
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
