<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;
$mybadgeid=$_SESSION['badgeid']; // make it a simple variable so it can be substituted

// Test for conid being passed in and make it a simple variable so it can be substituted
$conid=$_GET['conid'];

if (($conid == "") or (!is_numeric($conid))) {
  $conid=$_SESSION['conid'];
}

// Get the GOH list
$query = <<<EOD
SELECT
    badgeid
  FROM
      UserHasConRole
    JOIN ConRoles USING (conroleid)
  WHERE
    conid=$conid AND
    conrolename like '%GOH%'
EOD;

list($gohrows,$gohheader_array,$gohbadge_array)=queryreport($query,$link,$title,$description,0);
$GohBadgeList="('";
for ($i=1; $i<=$gohrows; $i++) {
  $GohBadgeList.=$gohbadge_array[$i]['badgeid']."', '";
}
$GohBadgeList.="')";


// LOCALIZATIONS
/* unpub controls the "Do Not Print" and "Staff Only" inclusion into
   the grid it needs to be set first, because otherwise we are
   checking on a negative.  Default exclude "Do Not Print" and "Staff
   Only" filled is the switch between semi-filled (color only) and
   filled (name in each block).  Default semi-filled nocolor allows
   for the lack of background color of the cells. Default color
   beginonly gives the short version of the grid, not broken by time
   demarcation, showing start times only, with no day-breaks
   progselect limits to the programming items/rooms only volselect
   limits to the volunteer items/rooms only regselect limits to the
   registration items/rooms only saleselect limits to the sales
   items/rooms only vendselect limits to the vendor items/rooms only
   watchselect limits to the watch items/rooms only logselect limits
   to the logistic items/rooms only eventselect limits to the events
   items/rooms only fasttrack limits to the Fast Track items/rooms
   only (the above several should exclude each other) goh limits to
   the goh involved programs only
*/
$unpub="n";
$unpub=$_GET['unpublished'];
$standard=$_GET['standard'];
$staffonly=$_GET['staffonly'];
$filled=$_GET['timefilled'];
$nocolor=$_GET['nocolor'];
$beginonly=$_GET['starttime'];
$progselect=$_GET['programming'];
$volselect=$_GET['volunteer'];
$regselect=$_GET['registration'];
$saleselect=$_GET['sales'];
$vendselect=$_GET['vending'];
$watchselect=$_GET['watch'];
$logselect=$_GET['logistics'];
$eventselect=$_GET['events'];
$fasttrackselect=$_GET['fasttrack'];
$trackselect=$_GET['track'];
$loungeselect=$_GET['lounge'];
$goh=$_GET['goh'];
$default=$_GET['default'];

/* Attempt to establish a default grid based on permissions, and set
   the proper return_to_page location. */
if (!empty($_SERVER['QUERY_STRING'])) {
  $_SESSION['return_to_page']="grid.php?".$_SERVER['QUERY_STRING'];
} else {
  $_SESSION['return_to_page']="grid.php";
  if (may_I('Programming')) {$progselect="y";}
  if (may_I('Liaison')) {$progselect="y";}
  if (may_I('General')) {$volselect="y";}
  if (may_I('Registration')) {$regselect="y";}
  if (may_I('Sales')) {$salesselect="y";}
  if (may_I('Vending')) {$vendselect="y";}
  if (may_I('Watch')) {$watchselect="y";}
  if (may_I('Logistics')) {$logselect="y";}
  if (may_I('Events')) {$eventselect="y";}
  if (may_I('Fasttrack')) {$fasttrackselect="y";}
  if (may_I('Lounge')) {$loungeselect="y";}
}

/* If Participant, fix several of the variables, so there is only one
   grid displayed. */
if ($_SESSION['role']=="Participant") {
  $unpub="n";
  $staffonly="n";
  $progselect="n";
  $volselect="n";
  $regselect="n";
  $saleselect="n";
  $vendselect="n";
  $watchselect="n";
  $logselect="n";
  $eventselect="n";
  $fastrtactselect="n";
  $loungeselect="n";
  $filled="n";
  $beginonly="n";
  $goh="n";
  $nocolor="n";
 }

// Title/header hacking so everything is switched, and easily readable.
// Defaults
$allprint="excludes";
$tallprint="";
$typeprint="";
$beginonlyprint="regular ";
$semifill=" (only)";
$tsemifill="Time Semi-filled ";
$gohprint="";
$tcolorprint="Color ";
$colorprint=", keyed by color";
$typeortrackprint=", colored by type";
$typeortrack="Types T USING (typeid) JOIN Tracks TN USING (trackid)";

// Mods
if ($unpub=="y") {
  $allprint="includes";
  $tallprint="Unabridged ";
 }
if ($staffonly=="y") {
  $unpub="y";
  $allprint="is only";
  $tallprint="Staff Only ";
 }
if ($progselect=="y") {
  $typeprint.="Programming ";
 }
if ($volselect=="y") {
  $typeprint.="Volunteer ";
 }
if ($regselect=="y") {
  $typeprint.="Registration ";
 }
if ($saleselect=="y") {
  $typeprint.="Sales ";
 }
if ($vendselect=="y") {
  $typeprint.="Vending ";
 }
if ($watchselect=="y") {
  $typeprint.="Watch ";
 }
if ($logselect=="y") {
  $typeprint.="Logistics/Tech ";
 }
if ($eventselect=="y") {
  $typeprint.="Events ";
 }
if ($fasttrackselect=="y") {
  $typeprint.="Fast Track ";
 }
if ($loungeselect=="y") {
  $typeprint.="Lounges ";
 }
if ($trackselect=="y") {
  $typeortrackprint=", colored by track";
  $typeortrack="Tracks T USING (trackid) JOIN Types TN USING (typeid)";
}
if ($filled=="y") {
  $semifill="";
  $tsemifill="Time Filled ";
 }
if ($beginonly=="y") {
  $filled="y";
  $beginonlyprint="";
  $semifill="";
  $tsemifill="";
 }
if ($goh=="y") {
  $gohprint="GoH ";
 }
if ($nocolor=="y") {
  $tcolorprint="";
  $colorprint="";
  $typeortrackprint="";
 }
if ($typeprint=="") {
  $typeprint="Complete ";
}

// Back to the more standard piece.
$title=$tallprint.$gohprint.$typeprint.$tsemifill.$tcolorprint."Grid";
$description="<P>Display ".$gohprint.$typeprint."schedule with rooms on horizontal axis and ".$beginonlyprint."time on vertical".$colorprint.$semifill.". This $allprint items marked \"Do Not Print\" or \"Staff Only\".</P>\n";
$additionalinfo="<P>Click on the session id to edit the session's participants,\n";
$additionalinfo.="the session title to edit the session's\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid\">description</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="the presenter to visit their\n";
$additionalinfo.="<A HREF=\"StaffBios.php?conid=$conid\">bio</A>\n";
$additionalinfo.="<A HREF=\"StaffBios.php?short=Y&conid=$conid\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffBios.php?pic_p=N&conid=$conid\">(without images)</A>,\n";
$additionalinfo.="the time to visit that section of the\n";
$additionalinfo.="the <A HREF=\"StaffSched.php?format=sched&conid=$conid\">schedule</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=sched&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=sched&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="the track name to see all the classes by\n";
$additionalinfo.="the <A HREF=\"StaffSched.php?format=tracks&conid=$conid\">track</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="or <A HREF=\"StaffSched.php?format=trtime&conid=$conid\">track by time</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="the room name to edit the\n";
$additionalinfo.="the <A HREF=\"StaffSched.php?format=rooms&conid=$conid\">room's schedule</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=rooms&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="or <A HREF=\"manualGRIDS.php\">pick</A> another grid.</P>\n";

/* This query returns the room names for an array.  The "unpub" still
   restricts it to the rooms this grid is about, and the "staffonly"
   is often (but not always) redundant.

   The pubstatus_check is somewhat complicated, but it switches on the
   appropriate group set, and allows for mini-convention information.
   Probably the most useful staff-only view is for logistics, for
   their overlay of standard classes is probably otherwise
   complicated. */

if ($progselect=="y") {
  if ($staffonly=="y") {
    $pubstatus_array[]="'Prog Staff'";
  } else {
    $pubstatus_array[]="'Prog Staff','Public'";
  }
}
if ($logselect=="y") {
  if ($staffonly=="y") {
    $pubstatus_array[]="'Logistics'";
  } else {
    $pubstatus_array[]="'Logistics','Public'";
  }
}
if ($volselect=="y") {$pubstatus_array[]="'Volunteer'";}
if ($regselect=="y") {$pubstatus_array[]="'Reg Staff'";}
if ($saleselect=="y") {$pubstatus_array[]="'Sales Staff'";}
if ($vendselect=="y") {$pubstatus_array[]="'Vendor Staff'";}
if ($watchselect=="y") {$pubstatus_array[]="'Watch Staff'";}
if ($eventselect=="y") {$pubstatus_array[]="'Event Staff'";}
if ($fasttrackselect=="y") {$pubstatus_array[]="'Fast Track'";}
if ($loungeselect=="y") {$pubstatus_array[]="'Lounge Staff'";}
if (isset($pubstatus_array)) {
  $pubstatus_string=implode(",",$pubstatus_array);
  $pubstatus_check=" pubstatusname IN ($pubstatus_string)";
 } else {
  $pubstatus_check=" pubstatusname IN ('Public')";
 }
if ($standard=="y") {
  if ($staffonly=="y") {
    $pubstatus_check=" pubstatusname NOT IN ('Public')";
  } elseif ($unpub=="y") {
    $pubstatus_check=" pubstatusid > 0";
  }
}

$query ="SELECT roomname, roomid";
$query.=" FROM Rooms";
$query.=" WHERE";
$query.=" roomid in (SELECT DISTINCT roomid FROM Schedule JOIN Sessions USING (sessionid,conid) JOIN PubStatuses USING (pubstatusid)";
if ($goh=="y") {$query.=" JOIN ParticipantOnSession USING (sessionid,conid) WHERE conid=$conid AND badgeid in $GohBadgeList AND";} else {$query.=" WHERE conid=$conid AND";}
$query.=$pubstatus_check;
$query.=") ORDER BY display_order";

// Retrieve query
list($rooms,$unneeded_array_a,$header_array)=queryreport($query,$link,$title,$description,0);

// Set up the header cells
// Need to add the iCal link in here once it works
$header_cells="<TR><TH class=\"border2222\">&nbsp;&nbsp;Class&nbsp;&nbsp;Time&nbsp;&nbsp;</TH>";
for ($i=1; $i<=$rooms; $i++) {
  $header_cells.="<TH class=\"border2222\">";
  $header_cells.=sprintf("<A HREF=\"MaintainRoomSched.php?selroom=%s&conid=%s\"><B>%s</B></A>",$header_array[$i]["roomid"],$conid,$header_array[$i]["roomname"]);
  $header_cells.="</TH>";
 }
$header_cells.="</TR>";

/* This set of queries finds the appropriate presenters for a class,
   based on sessionid, and produces links for them.  This links the
   presenters, and shows the volunteers, introducers, and aidedecamp
   folk, marked appropriately. */
$query = <<<EOD
SELECT
      sessionid,
      GROUP_CONCAT(IF((volunteer not in ('1','yes') AND introducer not in ('1','yes') AND aidedecamp not in ('1','yes')),concat("<A HREF=\"StaffBios.php?conid=$conid#",pubsname,"\">",pubsname,"</A>",if((moderator in ('1','yes')),'(m), ',', ')),"") SEPARATOR "") as presentpubsnames,
      GROUP_CONCAT(IF((volunteer in ('1','yes')),concat(pubsname,"(v), "),"") SEPARATOR "") as volpubsnames,
      GROUP_CONCAT(IF((introducer in ('1','yes')),concat(pubsname,"(i), "),"") SEPARATOR "") as intpubsnames,
      GROUP_CONCAT(IF((aidedecamp in ('1','yes')),concat(pubsname,"(a), "),"") SEPARATOR "") as aidpubsnames
    FROM
      Sessions
    JOIN ParticipantOnSession USING (sessionid,conid)
    JOIN Participants USING (badgeid)
    WHERE
      conid=$conid
    GROUP BY
      sessionid
    ORDER BY
      sessionid;
EOD;

// Retrieve query
list($presenters,$unneeded_array_b,$presenters_tmp_array)=queryreport($query,$link,$title,$description,0);

for ($i=1; $i<=$presenters; $i++) {
  $presenters_array[$presenters_tmp_array[$i]['sessionid']]=$presenters_tmp_array[$i]['presentpubsnames'].$presenters_tmp_array[$i]['volpubsnames'].$presenters_tmp_array[$i]['intpubsnames'].$presenters_tmp_array[$i]['aidpubsnames'];
 }

/* Set the sizeing of the grid, from the congridspacer element
   in the ConInfo table. */
$query="SELECT congridspacer FROM ConInfo WHERE conid=$conid";
list($congridrows,$congridheader_array,$congrid_array)=queryreport($query,$link,$title,$description,0);
$grid_spacer=$congrid_array[1]['congridspacer'];

/* These queries finds the first and last second that is actually
   scheduled so we don't waste grid-space. */
$query="SELECT TIME_TO_SEC(starttime) as 'beginschedule' FROM Schedule WHERE conid=$conid ORDER BY starttime ASC LIMIT 0,1";
list($earliest,$unneeded_array_c,$grid_start_sec_array)=queryreport($query,$link,$title,$description,0);
$grid_start_sec=$grid_start_sec_array[1]['beginschedule'];

$query = <<<EOD
SELECT
    (TIME_TO_SEC(starttime) + TIME_TO_SEC(duration)) as 'endschedule'
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN PubStatuses USING (pubstatusid)
  WHERE
    conid=$conid AND
    $pubstatus_check
  ORDER BY
    endschedule DESC
  LIMIT
    0,1
EOD;

list($latest,$unneeded_array_d,$grid_end_sec_array)=queryreport($query,$link,$title,$description,0);
$grid_end_sec=$grid_end_sec_array[1]['endschedule'];

/* This sets the unpub to all the classes in the chosen rooms, if it
   isn't staffonly, and fixes the staffonly for the standard, which is
   the only one not fixed above. */
if (($unpub=="y") AND ($staffonly!="y")) {
  $pubstatus_check=" pubstatusid > 0";
 }
if (($standard=="y") AND ($staffonly=="y")) {
  $pubstatus_check=" pubstatusname NOT IN ('Public')";
 }

/* This complex set of queries fills in the header_cells and then puts
   the times, associated with each room along the row seperated out by
   the determinants above, by stepping along either in time intervals
   or as a whole, again, chosen above. */
if ($beginonly=="y") {$grid_end_sec=$grid_start_sec;}
$printrowscount=0;
for ($time=$grid_start_sec; $time<=$grid_end_sec; $time = $time + $grid_spacer) {
  $printrowscount++;
  $printrows_array[$printrowscount]=$time;
  if ($beginonly=="y") {
    $query="SELECT DATE_FORMAT(ADDTIME(constartdate,SCH.starttime),'%a %l:%i %p') as 'blocktime'";
  } else {
    $query="SELECT DATE_FORMAT(ADDTIME(constartdate,SEC_TO_TIME('$time')),'%a %l:%i %p') as 'blocktime'";
  }
  if ($filled=="y") {
    $filled_cull="roomid=%s";
  } else {
    $filled_cull="(roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime)))";
  }
  for ($i=1; $i<=$rooms; $i++) {
    $header_roomid=$header_array[$i]["roomid"];
    $header_roomname=$header_array[$i]["roomname"];
    $query.=sprintf(",GROUP_CONCAT(IF($filled_cull,concat(title_good_web,if((subtitle_good_web IS NULL),\"\",concat(\": \",subtitle_good_web))),\"\") SEPARATOR '') as \"%s title\"",$header_roomid,$header_roomname);
    $query.=sprintf(",GROUP_CONCAT(IF($filled_cull,S.sessionid,\"\") SEPARATOR '') as \"%s sessionid\"",$header_roomid,$header_roomname);
    $query.=sprintf(",GROUP_CONCAT(IF($filled_cull,trackname,\"\") SEPARATOR '') as \"%s track\"",$header_roomid,$header_roomname);
    $query.=sprintf(",GROUP_CONCAT(IF($filled_cull,",$header_roomid);
    $query.="CASE WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')";
    $query.="     WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')";
    $query.="     ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min') END";
    $query.=sprintf(",\"\") SEPARATOR '') as \"%s duration\"",$header_roomname);
    $query.=sprintf(",GROUP_CONCAT(IF($filled_cull,IF(S.estatten,S.estatten,\"\"),\"\") SEPARATOR '') as \"%s total\"",$header_roomid,$header_roomname);
    $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,T.htmlcellcolor,\"\") SEPARATOR '') as \"%s htmlcellcolor\"",$header_roomid,$header_roomname);
  }
  $query.=<<<EOD
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN $typeortrack
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
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
	  descriptiontypename='title' AND
	  biostatename='good' AND
	  biodestname='web' AND
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
	  descriptiontypename='subtitle' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') SGW USING (sessionid,conid)
  WHERE
    conid=$conid AND
EOD;
  if ($goh=="y") {$query.=" S.sessionid in (SELECT DISTINCT sessionid from ParticipantOnSession WHERE badgeid IN $GohBadgeList and conid=$conid) AND";}
  $query.=$pubstatus_check." AND";
  if ($beginonly=="y") {
    $query.=" SCH.sessionid = S.sessionid GROUP BY SCH.starttime ORDER BY SCH.starttime";
  } else {
    $query.=" TIME_TO_SEC(SCH.starttime) <= $time";
    $query.=" AND (TIME_TO_SEC(SCH.starttime) + TIME_TO_SEC(S.duration)) >= ($time + congridspacer);";
  }

  if (($result=mysql_query($query,$link))===false) {
    $message="Error retrieving data from database.<BR>";
    $message.=$query;
    $message.="<BR>";
    $message.= mysql_error();
    RenderError($title,$message);
    exit ();
  }
  if (0==($rows=mysql_num_rows($result))) {
    $message="<P>This report retrieved no results matching the criteria.</P>\n";
    RenderError($title,$message);
    exit();
  }

  if ($beginonly=="y") {
    for ($i=1; $i<=$rows; $i++) {
      $printrows_array[$i]=$i;
      $grid_array[$i]=mysql_fetch_array($result,MYSQL_BOTH);
      $k=$grid_array[$i]['blocktime'];
      $grid_array[$i]['blocktime']=sprintf("<A HREF=\"StaffSched.php?format=sched&conid=$conid#%s\"><B>%s</B></A>",$k,$k);
    }
  } else {
    $grid_array[$time]=mysql_fetch_array($result,MYSQL_BOTH);
    $skiprow=0;
    $refskiprow=0;
    for ($i=1; $i<=$rooms; $i++) {
      $j=$header_array[$i]['roomname'];
      if ($grid_array[$time]["$j htmlcellcolor"]!="") {
	$skiprow++;
	if ($grid_array[$time]["$j sessionid"]!="") {
	  $grid_array[$time]["$j cellclass"]="border1101d";
	  $refskiprow++;
	} else {
	  $grid_array[$time]["$j cellclass"]="border0101d";
	}
      } else {
	$grid_array[$time]["$j cellclass"]="border1111";
      }
    }
    if ($skiprow == 0) {$grid_array[$time]['blocktime'] = "Skip";}
    if ($refskiprow != 0) {
      $k=$grid_array[$time]['blocktime'];
      $grid_array[$time]['blocktime']=sprintf("<A HREF=\"StaffSched.php?format=sched&conid=$conid#%s\"><B>%s</B></A>",$k,$k);
    }
  }
 }

/* Printing body.  Uses the page-init from above adds informational
   line then creates the grid.  skipinit kills the rogue extrat /TABLE
   and skipaccum allows for only one new tabel per set of skips.  The
   extra ifs keep the parens out of the otherwise empty blocks.  We
   switch on htmlcellcolor, because, by design, that is the only thing
   written in a continuation block. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
$skipinit=0;
$skipaccum=1;
if (empty($printrows_array)) {
  echo "<P>Sorry, no grid-elements match your selection at this time.</P>";
  correct_footer();
  exit();
}

foreach ($printrows_array as $i) {
  if ($skipaccum == 1) {
    if ($skipinit != 0) {echo "</TABLE>\n";} else {$skipinit++;}
    echo "<TABLE class=\"border1111\">";
    //            echo "<TABLE BORDER=1>";
    echo $header_cells;
  }
  if ($grid_array[$i]['blocktime'] == "Skip") {
    $skipaccum++;
  } else {
    echo "<TR><TH class=\"border1111\">";
    echo $grid_array[$i]['blocktime'];
    echo "</TH>";
    for ($j=1; $j<=$rooms; $j++) {
      $header_roomname=$header_array[$j]['roomname'];
      $bgcolor=$grid_array[$i]["$header_roomname htmlcellcolor"]; //cell background color
      $cellclass=$grid_array[$i]["$header_roomname cellclass"]; //cell edge state
      if ($cellclass == "") {$cellclass="border1111";}
      $sessionid=$grid_array[$i]["$header_roomname sessionid"]; //sessionid
      $title=$grid_array[$i]["$header_roomname title"]; //title
      $track=$grid_array[$i]["$header_roomname track"]; //track
      $duration=$grid_array[$i]["$header_roomname duration"]; //duration
      $total=$grid_array[$i]["$header_roomname total"]; //total
      $presenters = substr($presenters_array[$sessionid],0,-2); //presenters, with the final ", " cut off.
      if ($bgcolor!="") {
	if ($nocolor=="y") {
	  echo sprintf("<TD CLASS=\"%s\">",$cellclass);
	} else {
	  echo sprintf("<TD BGCOLOR=\"%s\" CLASS=\"%s\">",$bgcolor,$cellclass);
	}
	if (($sessionid!="") AND ($_SESSION['role']!='Participant')) {
	  echo sprintf("(<A HREF=\"StaffAssignParticipants.php?selsess=%s&conid=%s\">%s</A>) ",$sessionid,$conid,$sessionid);
	}
	if ($title!="") {
	  if ($_SESSION['role']=="Participant") {
	    echo sprintf("<A HREF=\"StaffSched.php?format=desc&conid=%s#%s\">%s</A>",$conid,$sessionid,$title);
	  } else {
	    echo sprintf("<A HREF=\"EditSession.php?id=%s&conid=%s\">%s</A>",$sessionid,$conid,$title);
	  }
	}
	if ($track!="") {
	  echo sprintf(" - <A HREF=\"StaffSched.php?format=tracks&conid=%s#%s\">%s</A>",$conid,$track,$track);
	}
	if ($duration!="") {
	  echo sprintf(" (%s)",$duration);
	}
	if ($total!="") {
	  echo sprintf("<br>(Count: %s)",$total);
	}
	if ($presenters!="") {
	  echo sprintf("<br>\n%s",$presenters);
	}
      }
      else
	{ echo "<TD class=\"border1111\">&nbsp;"; }
      echo "</TD>";
    }
    echo "</TR>\n";
    $skipaccum=0;
  }
}
echo "</TABLE>";

// Color key
$fromtable="Types";
$field="typename";
if ($trackselect == "y") {
  $fromtable="Tracks";
  $field="trackname";
}
if ($nocolor == "y") {
  correct_footer();
} else {
  $query = <<<EOD
SELECT
    concat("</TD><TD BGCOLOR=\"",htmlcellcolor,"\">",$field) AS "</TH><TH>Key"
  FROM
      $fromtable
  ORDER BY
    display_order
EOD;

  list($keyrows,$keyheader_array,$key_array)=queryreport($query,$link,$title,$description,0);
  echo renderhtmlreport(1,$keyrows,$keyheader_array,$key_array);
  correct_footer();
}
?>
