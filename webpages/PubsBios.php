<?php
require_once('PostingCommonCode.php');
global $link;

/* takes a variable, and searches across all the posible variations
   to see if said variable exists in the bios-set
 */
function getBioDestEdit($biotype,$biolang,$bioinfo) {
  if ((isset($bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'])) and
      ($bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'] != "")) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'];
  } elseif ((isset($bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'])) and
	    ($bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'] != "")) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'];
  } elseif ((isset($bioinfo[$biotype.'_'.$biolang.'_raw_web_bio']))  and
	    ($bioinfo[$biotype.'_'.$biolang.'_raw_web_bio'] != "")) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_raw_web_bio'];
  } elseif ((isset($bioinfo[$biotype.'_'.$biolang.'_raw_book_bio']))  and
	    ($bioinfo[$biotype.'_'.$biolang.'_raw_book_bio'] != "")) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_raw_book_bio'];
  } else {
    $bioout="";
  }

  return ($bioout);
}

// Pass in variables
$conid=$_GET['conid'];
if ($conid=="") {$conid=$_SESSION['conid'];}

$short="F";
if (isset($_GET['short'])) {
  if ($_GET['short'] == "Y") {
    $short="T";
  } elseif ($_GET['short'] == "N") {
    $short="F";
  }
}

/* Just setting this, in other fooBios this is passed in. Since we never want
   to do this without pictures in public, in long form, it is just hard-coded. */
$pic_p="T";

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
$_SESSION['return_to_page']="PubsBios.php&conid=$conid";
$title="Biographical Information";
$description="<P>Biographical Information for all Presenters.</P>\n";
$additionalinfo="<P>See also this ";
if ($short=="T") {
  $additionalinfo.="<A HREF=\"PubsBios.php?conid=$conid\">full</A> or\n";
} else {
  $additionalinfo.="<A HREF=\"PubsBios.php?short=Y&conid=$conid\">short</A>,\n";
}
$additionalinfo.="the <A HREF=\"PubsSched.php?format=desc&conid=$conid\">description</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=desc&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=sched&conid=$conid\">timeslots</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=sched&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=tracks&conid=$conid\">tracks</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=trtime&conid=$conid\">tracks by time</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=rooms&conid=$conid\">rooms</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="or the <A HREF=\"Postgrid.php?conid=$conid\">grid</A>.</P>\n";

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
    concat("<A HREF=\"PubsSched.php?format=tracks&conid=$conid#",
      trackname,
      "\"><i>",
      trackname,
      "</i></A>") AS Track,
    concat("<A HREF=\"PubsSched.php?format=desc&conid=$conid#",
      title_good_web,
      if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),
      "\">",
      title_good_web,
      if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),
      "</A>") AS Title,
    pubsname AS 'Participants',
    badgeid,
    concat("<A HREF=\"PubsSched.php?format=sched&conid=$conid#",
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
    concat("<A HREF=\"PubsSched.php?format=rooms&conid=$conid#",
      roomname,
      "\"><i>",
      roomname,
      "</i></A>") AS Room,
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
    pubstatusname in ('Public') AND
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

      // For each language in our language array
      for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
	$biolang=$bioinfo['biolang_array'][$j];

	// If there is a picture
	$picturetmp=getBioDestEdit('picture',$biolang,$bioinfo);
	if (($picturetmp != "") and ($pic_p == "T")) {
	  $edit_p=strpos($picturetmp,"***EDIT PLEASE***");
	  if ($edit_p === false) {
	    $picture=sprintf("<img width=300 src=\"%s\">",$picturetmp);
	  } else {
	    $picture=sprintf("Picture for editing at: http://%s/webpages/%s",
			     $_SESSION['conurl'], substr($picturetmp, 18));
	  }
	} else {
	  $picture="";
	}

	// Set their name
	$name=getBioDestEdit('name',$biolang,$bioinfo);
	if ($name == "") {
	  $name=$header;
	}

	$name="<A NAME=\"$name\"></A>$name";

	//If there is a bio
	$bio=getBioDestEdit('bio',$biolang,$bioinfo);

	//If there is a URI line
	$uri=getBioDestEdit('uri',$biolang,$bioinfo);

	if ($picture != "") {
	  if ($tablecount == 0) {
	    $biostring.="<TABLE>\n  <TR>\n    <TD valign=\"top\" width=310>";
	    $tablecount++;
	  } else {
	    $biostring.="    </TD>\n  </TR>\n  <TR>\n    <TD width=310>";
	  }
	  $biostring.=sprintf("%s</TD>\n    <TD>",$picture);
	}
	$biostring.=sprintf("<P><B>%s</B>",$name);
	if ($bio != "") {
	  $biostring.=$bio;
	}
	$biostring.="</P>\n";
	if ($uri != "") {
	  $biostring.=sprintf("<P>%s</P>\n",$uri);
	}
      } // End of Language Switch
      if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
	$biostring.=sprintf(" <A HREF=\"PostScheduleIcal.php?pubsname=%s\">(Fan iCal)</A></P>\n<P>",$element_array[$i]['pubsname']);
      }
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
topofpagereport($title,$description,$additionalinfo);

/* Produce the report. */
$printstring=renderschedreport($format,$header_break,$single_line_p,$elements,$element_array);
echo $printstring;

correct_footer();
?>
