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
      PhaseTypes
    JOIN Phase USING (phasetypeid)
  WHERE
    phasetypename like '%Feedback Available%' AND
    conid=$conid
EOD;
    
// Retrieve query
list($phasestatrows,$phaseheader_array,$phase_array)=queryreport($query,$link,$title,$description,0);

// LOCALIZATIONS
$_SESSION['return_to_page']="Bios.php";
$title="Bios for Presenters at $conname";
$description="<P>List of all Presenters biographical information.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.php$passon\">description</A>,\n";
$additionalinfo.="the time to visit the <A HREF=\"Schedule.php$passon\">timeslot</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"Tracks.php$passon\">track</A>, or visit the <A HREF=\"Postgrid.php$passon\">grid</A>.</P>\n";
if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
  $additionalinfo.="<P>To get an iCal calendar of all the classes of this Presenter, click on the (Fan iCal) after their\n";
  $additionalinfo.="Bio entry, or the (iCal) after the particular activity, to create a calendar for just that activity.</P>\n";
 }
if ((strtotime($ConStart) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This complex query grabs the name, and class information.
 Most, if not all of the formatting is done within the query, as opposed to in
 the post-processing. The bio information is grabbed seperately. */
$query = <<<EOD
SELECT
    concat('<A NAME=\"',pubsname,'\"></A>',pubsname) as 'Participants',
    concat('<A HREF=\"Descriptions.php$passon#',sessionid,'\"><B>',title_good_web,'</B></A>') AS Title,
    subtitle_good_web AS Subtitle,
    if((moderator in ('1','Yes')),' (m)','') AS Moderator,
    $trackname AS Track,
    concat('<A HREF=\"Schedule.php$passon#',DATE_FORMAT(ADDTIME('$ConStart',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStart',starttime),'%a %l:%i %p'),'</A>') AS 'Start Time',
    CASE 
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    $roomname as Roomname,
    concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=Feedback.php?sessionid=',sessionid,'>(Feedback)</A>') AS Feedback,
    pubsname,
    badgeid
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms USING (roomid)
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
  WHERE
    conid=$conid AND
    pubstatusname in ($pubstatus_check) AND
    (volunteer=0 OR volunteer="0" OR volunteer IS NULL) AND
    (introducer=0 OR introducer="0" OR introducer IS NULL) AND
    (aidedecamp=0 OR aidedecamp="0" OR aidedecamp IS NULL)
  ORDER BY
  pubsname,
  starttime
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the bio page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
$printparticipant="";
for ($i=1; $i<=$elements; $i++) {
  if ($element_array[$i]['Participants'] != $printparticipant) {
    if ($printparticipant != "") {
      echo "    </TD>\n  </TR>\n</TABLE>\n";
      echo "<P>&nbsp;</P>\n";
    }
    $printparticipant=$element_array[$i]['Participants'];
    $bioinfo=getBioData($element_array[$i]['badgeid']);
    /* Presenting the Web, URI and Picture pieces, in whatever
       languages we have, grouping by language, then type.
       Currently we are using edited as the state, at some
       point we should move to good. */
    $namecount=0;
    $tablecount=0;
    $biostate='edited'; // for ($l=0; $l<count($bioinfo['biostate_array']); $l++) {
    $biodest='web'; // for ($m=0; $m<count($bioinfo['biodest_array']); $m++) {
    for ($k=0; $k<count($bioinfo['biolang_array']); $k++) {
      $bioout=array();
      for ($j=0; $j<count($bioinfo['biotype_array']); $j++) {

	// Setup for keyname, to collapse all four variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$j];
	$biolang=$bioinfo['biolang_array'][$k];
	// $biostate=$bioinfo['biostate_array'][$l];
	// $biodest=$bioinfo['biodest_array'][$m];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Set up the useful pieces.
	if (isset($bioinfo[$keyname])) {$bioout[$biotype]=$bioinfo[$keyname];}
      }

      // Still in the language switch, but have set the $bioout array.
      if (isset($bioout['picture'])) {
	if ($tablecount == 0) {
	  echo "<TABLE>\n  <TR>\n    <TD valign=top width=310>";
	  $tablecount++;
	} else {
	  echo "    </TD>\n  </TR>\n  <TR>\n    <TD valign=top width=310>";
	}
	echo sprintf("<img width=300 src=\"%s\"></TD>\n<TD>",$bioout['picture']);
      } else {
	if ($tablecount == 0) {
	  echo "<TABLE>\n  <TR>\n    <TD>";
	  $tablecount++;
	}
      }
      if (isset($bioout['web'])) {
	echo sprintf("<P><B>%s</B>%s</P>\n",$printparticipant,$bioout['web']);
	$namecount++;
      }
      if (isset($bioout['uri'])) {
	if ($namecount==0) {
	  echo sprintf("<P><B>%s:</B><br>%s</P>\n",$printparticipant,$bioout['uri']);
	} else {
	  echo sprintf("<P>%s</P>\n",$bioout['uri']);
	}
      }
    }
    // If there were no bios
    if ($namecount==0) { echo sprintf("<P><B>%s</B>",$printparticipant);}
    if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
      echo sprintf(" <A HREF=\"PostScheduleIcal.php?pubsname=%s\">(Fan iCal)</A></P>\n<P>",$element_array[$i]['pubsname']);
    }
  }
  echo sprintf("<DT>%s",$element_array[$i]['Title']);
  if ($element_array[$i]['Subtitle'] !='') {
    echo sprintf(": %s",$element_array[$i]['Subtitle']);
  }
  if ($element_array[$i]['Moderator']) {
    echo sprintf("%s",$element_array[$i]['Moderator']);
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
  if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
    echo sprintf("&mdash; %s",$element_array[$i]['iCal']);
  }
  if ((strtotime($ConStart) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
    echo sprintf("&mdash; %s",$element_array[$i]['Feedback']);
  }
}
echo "    </TD>\n  </TR>\n</TABLE>\n";
echo "<P>&nbsp;</P>\n";

correct_footer();
?>