<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;

/* takes a variable, and searches across all the posible variations
   to see if said variable exists in the bios-set
 */
function getBioDestEdit($biotype,$biolang,$biodest,$bioinfo) {
  // Good checking
  if (!empty($bioinfo[$biotype.'_'.$biolang.'_good_'.$biodest.'_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_good_'.$biodest.'_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_edited_'.$biodest.'_bio'])) {
    $bioout="***EDITED hasn't promoted*** ".$bioinfo[$biotype.'_'.$biolang.'_edited_'.$biodest.'_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_raw_'.$biodest.'_bio'])) {
    $bioout="***RAW alone exists*** ".$bioinfo[$biotype.'_'.$biolang.'_raw_'.$biodest.'_bio'];
  } else {
    $bioout="";
  }
  return ($bioout);
}

/* Take the badgeid and searches for the picture.
   This presents the web, book, and badge picture if they exist. */
function getPictureDestEdit($checkbadge,$bioinfo) {
  $picture="";
  for ($k=0; $k<count($bioinfo['biodest_array']); $k++) {
    $biodest=$bioinfo['biodest_array'][$k];
    $picturestring="../Local/Participant_Images_$biodest/$checkbadge";
    if (file_exists($picturestring)) {
      $picture.=sprintf("%s:<br>\n<img width=300 src=\"%s\">",ucfirst($biodest),$picturestring);
    }
  }
  return ($picture);
}

// Pass in variables
$conid=$_GET['conid'];
if ($conid=="") {$conid=$_SESSION['conid'];}

$_SESSION['return_to_page']="StaffBios.php?conid=$conid";

$short="F";
if (isset($_GET['short'])) {
  if ($_GET['short'] == "Y") {
    $short="T";
    $_SESSION['return_to_page']="StaffBios.php?conid=$conid&short=\"Y\"";
  } elseif ($_GET['short'] == "N") {
    $short="F";
  }
}

$pic_p="T";
if (isset($_GET['pic_p'])) {
  if ($_GET['pic_p'] == "N") {
    $pic_p="F";
    $short="F";
    $_SESSION['return_to_page']="StaffBios.php?conid=$conid&pic_p=\"N\"";
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
$pubstatus_check=implode(",",$pubstatus_array);

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

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
$title="Biographical Information";
$description="<P>Biographical Information for all Presenters.</P>\n";
$additionalinfo="<P>See also this ";
if ($short=="T") {
  $additionalinfo.="<A HREF=\"StaffBios.php?conid=$conid\">full</A> or\n";
  $additionalinfo.="<A HREF=\"StaffBios.php?pic_p=N&conid=$conid\">full without images</A>,\n";
} elseif ($pic_p=="F") {
  $additionalinfo.="<A HREF=\"StaffBios.php?conid=$conid\">full</A> or\n";
  $additionalinfo.="<A HREF=\"StaffBios.php?short=Y&conid=$conid\">short</A>,\n";
} else {
  $additionalinfo.="<A HREF=\"StaffBios.php?pic_p=N&conid=$conid\">without images</A> or\n";
  $additionalinfo.="<A HREF=\"StaffBios.php?short=Y&conid=$conid\">short</A>,\n";
}
$additionalinfo.="the <A HREF=\"StaffSched.php?format=desc&conid=$conid\">description</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="the <A HREF=\"StaffSched.php?format=sched&conid=$conid\">timeslots</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=sched&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=sched&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="the <A HREF=\"StaffSched.php?format=tracks&conid=$conid\">tracks</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="the <A HREF=\"StaffSched.php?format=trtime&conid=$conid\">tracks by time</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="the <A HREF=\"StaffSched.php?format=rooms&conid=$conid\">rooms</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=rooms&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="or the <A HREF=\"grid.php?standard=y\">grid</A>.</P>\n";

// iCal
if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
  $additionalinfo.="<P>To get an iCal calendar of all the classes of this Presenter, click\n";
  $additionalinfo.="on the (Fan iCal) after their Bio entry, or the (iCal) after the\n";
  $additionalinfo.="particular activity, to create a calendar for just that activity.</P>\n";
 }

// Feedback
if ((strtotime($ConStart) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular\n";
  $additionalinfo.="scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the schedule to be printed.
   It is not very different from the fooSched query, but different enough. */

$query = <<<EOD
SELECT
    concat("<A HREF=\"StaffSched.php?format=tracks&conid=$conid#",
      trackname,
      "\"><i>",
      trackname,
      "</i></A>") AS Track,
    concat("<A HREF=\"StaffSched.php?format=desc&conid=$conid#",
      title_good_web,
      if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),
      "\">",
      title_good_web,
      if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),
      "</A>",
      if(moderator in ('1','Yes'),' (m)',''),
      "<SUP><A HREF=\"EditSession.php?id=",
      sessionid,
      "\">(edit)</A></SUP>") AS Title,
    pubsname AS 'Participants',
    badgeid,
    concat("<A HREF=\"StaffSched.php?format=sched&conid=$conid#",
      DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),
      "\"><i>",
      DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),
      "</i></A>") AS "Start Time",
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    concat("<A HREF=\"StaffSched.php?format=rooms&conid=$conid#",
      roomname,
      "\"><i>",
      roomname,
      "</i></A>") AS Room,
    if(estatten IS NULL,"",estatten) AS Estatten,
    Sessionid,
    if(DATE_ADD(constartdate,INTERVAL connumdays DAY) > NOW(),
      concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>'),
      '') AS iCal,
    if((constartdate < NOW() AND phasestate = "0"),
      concat('<A HREF=Feedback.php?conid=$conid&sessionid=',sessionid,'>(Feedback)</A>'),
      '') AS Feedback,
    if(desc_good_web IS NULL,
      if(desc_good_book IS NULL,"",desc_good_book),
      desc_good_web) AS 'Description'
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
    pubstatusname in ($pubstatus_check) AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  ORDER BY
    pubsname,
    starttime
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// Establish the bios element
if ($short == "T") {
  $header="";
  for ($i=1; $i<=$elements; $i++) {
    if ($element_array[$i]['Participants'] != $header) {
      $header=$element_array[$i]['Participants'];
      $biostring=sprintf("<P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$header);
    }
    $element_array[$i]['Bio']=$biostring;
  }
} else {
  $header="";
  for ($i=1; $i<=$elements; $i++) {
    if ($element_array[$i]['Participants'] != $header) {
      $tablecount=0;
      $header=$element_array[$i]['Participants'];
      $biostring="<P>&nbsp;</P>\n";

      // Get the bio information
      $bioinfo=getBioData($element_array[$i]['badgeid']);

      // We are only using "edited" as our biostate
      $biostate='edited';

      // For each language in our language array
      for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
	$biolang=$bioinfo['biolang_array'][$j];

	// If there is a picture
	$picture="";
	if ($pic_p == "T") {
	  $picture=getPictureDestEdit($element_array[$i]['badgeid'],$bioinfo);
	}

	if ($picture != "") {
	  if ($tablecount == 0) {
	    $biostring.="<TABLE>\n  <TR>\n    <TD valign=\"top\" width=310>";
	    $tablecount++;
	  } else {
	    $biostring.="    </TD>\n  </TR>\n  <TR>\n    <TD width=310>";
	  }
	  $biostring.=sprintf("%s</TD>\n    <TD>",$picture);
	}

	// For each biodest in our biodest array
	for ($k=0; $k<count($bioinfo['biodest_array']); $k++) {
	  $accum="";
	  $biodest=$bioinfo['biodest_array'][$k];

	  // Shows the destination
	  $accumtitle=sprintf("%s:<br>\n",ucfirst($biodest));

	  // Set their name
	  $name=getBioDestEdit('name',$biolang,$biodest,$bioinfo);
	  if ($name != "") {
	    $name="<A NAME=\"$name\"></A>$name";
	    $accum.=sprintf("<P><B>%s</B>",$name);
	  }

	  // Sets the bio info
	  $bio=getBioDestEdit('bio',$biolang,$biodest,$bioinfo);
	  if (($name == "") and ($bio != "")) {
	    $accum.=sprintf("***EDIT PLEASE*** <P><B><A NAME=\"%s\"</A>%s</B>%s</P>\n",$header,$header);
	  } elseif ($name != "") {
	    $accum.=sprintf("%s</P>\n",$bio);
	  }

	  // Sets the URI info
          $uri=getBioDestEdit('uri',$biolang,$biodest,$bioinfo);
	  if ($uri != "") {
	    $accum.=sprintf("<P>%s</P>\n",$uri);
	  }

	  // Sets the pronoun info
          $pronoun=getBioDestEdit('pronoun',$biolang,$biodest,$bioinfo);
	  if ($pronoun != "") {
	    $accum.=sprintf("<P>Preferred pronoun: %s</P>\n",$pronoun);
	  }

	  // Add only if there is anything in the biodest
	  if ($accum != "") {
	    $biostring.=$accumtitle . $accum;
	  }
	} // End of biodest switch
      } // End of language switch
      if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
	$biostring.=sprintf(" <A HREF=\"PostScheduleIcal.php?pubsname=%s\">(Fan iCal)</A></P>\n<P>",$header);
      }
      $biostring.="<br><A HREF=\"StaffEditBios.php?qno=1&badgeid=".$element_array[$i]['badgeid']."&badgeids=".$element_array[$i]['badgeid']."\">(edit bio)</A><br>";
      $element_array[$i]['Bio']=$biostring;
      $element_array[$i]['istable']=$tablecount;
    } else {  // if it is the same in the 'Participants' field, just copy the result in.
      $element_array[$i]['Bio']=$biostring;
      $element_array[$i]['istable']=$tablecount;
    }
  }
}

// Variables for the format
$format="bios";
$header_break="Participants";
$single_line_p="T";

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

/* Produce the report. */
$printstring=renderschedreport($format,$header_break,$single_line_p,$elements,$element_array);
echo $printstring;

correct_footer();
?>
