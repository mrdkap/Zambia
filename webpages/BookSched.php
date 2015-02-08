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

$single_line_p="F";
if (isset($_GET['short'])) {
  if ($_GET['short'] == "Y") {
    $single_line_p="T";
  } elseif ($_GET['short'] == "N") {
    $single_line_p="F";
  }
}

// Default
$pubsname="if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT pubsname,if(moderator in ('1','Yes'),'(m)','') SEPARATOR ', ')) AS 'Participants'";

if ($format == "tracks") {
  $title="Track List by Name";
  $description="<P>Track Schedules for all public sessions orderd by their name.</P>\n";
  // Temporarily overwriting the (m) for Tracks
  //$pubsname="if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT pubsname SEPARATOR ', ')) AS 'Participants'";
  $orderby="trackname,title_good_web,starttime,R.roomname";
  $header_break="Track";
}
if ($format == "trtime") {
  $title="Track List by Time";
  $description="<P>Track Schedules for all public sessions orderd by when they are.</P>\n";
  $orderby="trackname,starttime,title_good_web,R.roomname";
  $header_break="Track";
}
if ($format == "rooms") {
  $title="Room List";
  $description="<P>Room Schedules for all public sessions.</P>\n";
  $orderby="R.roomname,starttime,title_good_web,trackname";
  $header_break="Room";
}
if ($format == "desc") {
  $title="Descriptions";
  $description="<P>Descriptions for all public sessions.</P>\n";
  $orderby="title_good_web";
  $header_break="";
}
if ($format == "sched") {
  $title="Schedule";
  $description="<P>Schedule for all public sessions.</P>\n";
  $orderby="starttime,R.display_order,title_good_web";
  $header_break="Start Time";
}

// LOCALIZATIONS
$_SESSION['return_to_page']="BookSched.php?format=$format";
$additionalinfo="<P>See also this ";
if ($single_line_p=="T") {
  $additionalinfo.="<A HREF=\"BookSched.php?format=$format\">full</A>,\n";
} else {
  $additionalinfo.="<A HREF=\"BookSched.php?format=$format&short=Y\">short</A>,\n";
}
$additionalinfo.="the <A HREF=\"BookBios.php\">bios</A>\n";
$additionalinfo.="<A HREF=\"BookBios.php?pic_p=N\">(without images)</A>\n";
$additionalinfo.="<A HREF=\"BookBios.php?short=Y\">(short)</A>,\n";
if ($format != "desc") {
  $additionalinfo.="the <A HREF=\"BookSched.php?format=desc\">description</A>\n";
  $additionalinfo.="<A HREF=\"BookSched.php?format=desc&short=Y\">(short)</A>,\n";
}
if ($format != "sched") {
  $additionalinfo.="the <A HREF=\"BookSched.php?format=sched\">timeslots</A>\n";
  $additionalinfo.="<A HREF=\"BookSched.php?format=sched&short=Y\">(short)</A>,\n";
}
if ($format != "tracks") {
  $additionalinfo.="the <A HREF=\"BookSched.php?format=tracks\">tracks</A>\n";
  $additionalinfo.="<A HREF=\"BookSched.php?format=tracks&short=Y\">(short)</A>,\n";
}
if ($format != "trtime") {
  $additionalinfo.="the <A HREF=\"BookSched.php?format=trtime\">tracks by time</A>\n";
  $additionalinfo.="<A HREF=\"BookSched.php?format=trtime&short=Y\">(short)</A>,\n";
}
if ($format != "rooms") {
  $additionalinfo.="the <A HREF=\"BookSched.php?format=rooms\">rooms</A>\n";
  $additionalinfo.="<A HREF=\"BookSched.php?format=rooms&short=Y\">(short)</A>,\n";
}
$additionalinfo.="or the <A HREF=\"grid.php?standard=y\">grid</A>.</P>\n";

/* This query grabs everything necessary for the schedule to be printed. */
$query = <<<EOD
SELECT
    trackname AS Track,
    concat(title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS Title,
    $pubsname,
    GROUP_CONCAT(DISTINCT DATE_FORMAT(ADDTIME(constartdate,starttime),'%a %l:%i %p') SEPARATOR ', ') AS 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR ', ') AS Room,
    if(desc_good_book IS NULL,
      concat("***EDIT PLEASE***",if(desc_good_web IS NULL,"",desc_good_web)),
      desc_good_book) AS 'Description'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
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

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo);

/* Produce the report. */
$printstring=renderschedreport($format,$header_break,$single_line_p,$elements,$element_array);
echo $printstring;

correct_footer();
?>
