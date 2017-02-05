<?php
require_once('PostingCommonCode.php');
global $link;
$title="Sessions Grid";
$pagetitle=$title;

// Deal with what is passed in.
if (!empty($_SERVER['QUERY_STRING'])) {
  $passon="?".$_SERVER['QUERY_STRING'];
  $passon_p=$passon."&print_p=y";
} else {
  $passon_p="?print_p=y";
}

if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,conurl,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$conurl=$conname_array[1]['conurl'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$logo=$conname_array[1]['conlogo'];

if (isset($_GET['volunteer'])) {
  $pubstatus_check="'Volunteer'";
  $pubstatus_check="'Volunteer','Reg Staff','Sales Staff'";
  $schedtype="VolsSched.php";
  $Grid_Spacer=3600;
} elseif (isset($_GET['registration'])) {
  $pubstatus_check="'Reg Staff'";
  $Grid_Spacer=3600;
} elseif (isset($_GET['sales'])) {
  $pubstatus_check="'Sales Staff'";
  $Grid_Spacer=3600;
} elseif (isset($_GET['vfull'])) {
  $pubstatus_check="'Volunteer','Reg Staff','Sales Staff'";
  $Grid_Spacer=3600;
} else {
  $pubstatus_check="'Public'";
  $schedtype="PubsSched.php";
}

// LOCALIZATIONS
$_SESSION['return_to_page']="Postgrid-wide.php";
$title="Sessions Grid for $conname";
$pagetitle=$title;
$description="<P>Grid of all sessions. (Program details subject to change prior to the event.)</P>\n";
$additionalinfo="<P>Click on ";
if ($schedtype=="VolsSched.php") {
  $additionalinfo.="the job title to visit the job\n";
  $additionalinfo.="<A HREF=\"VolsSched.php?format=desc&conid=$conid\">description</A>\n";
  $additionalinfo.="<A HREF=\"VolsSched.php?format=desc&conid=$conid&short=Y\">(short)</A>,\n";
  $additionalinfo.="or the time to visit that section of the\n";
  $additionalinfo.="<A HREF=\"VolsSched.php?format=sched&conid=$conid\">timeslots</A>\n";
  $additionalinfo.="<A HREF=\"VolsSched.php?format=sched&conid=$conid&short=Y\">(short)</A>.\n";
} else {
  $additionalinfo.="the title to visit the session's\n";
  $additionalinfo.="the <A HREF=\"PubsSched.php?format=desc&conid=$conid\">description</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=desc&conid=$conid&short=Y\">(short)</A>,\n";
  $additionalinfo.="the presenter to visit their\n";
  $additionalinfo.="<A HREF=\"PubsBios.php?conid=$conid\">bios</A>\n";
  $additionalinfo.="<A HREF=\"PubsBios.php?short=Y&conid=$conid\">(short)</A>,\n";
  $additionalinfo.="the time to visit that section of the\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=sched&conid=$conid\">schedule</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=sched&conid=$conid&short=Y\">(short)</A>,\n";
  $additionalinfo.="or the track name to visit that session's\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=tracks&conid=$conid\">track</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>,\n";
  $additionalinfo.="or look at the\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=trtime&conid=$conid\">tracks by time</A>\n";
  $additionalinfo.="<A HREF=\"PubsSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>.\n";
}
$additionalinfo.="(<A HREF=\"Postgrid.php$passon\">Switch indices</A>)</P>\n";
$additionalinfo.="<P>If you wish to have a copy printed, please download the\n";
$additionalinfo.="<A HREF=Postgrid.php$passon_p>Rooms x Times</A> or\n";
$additionalinfo.="<A HREF=Postgrid-wide.php$passon_p>Times x Rooms</A> version.</P>\n";

/* This query returns the room names for an array, to be used as
 headers, and keys for other arrays.*/
$query = <<<EOD
SELECT
    roomname,
    roomid
  FROM
      Rooms
  WHERE
    roomid in (SELECT
                   DISTINCT roomid
                 FROM
	             Schedule
                   JOIN Sessions USING (sessionid,conid)
                   JOIN PubStatuses USING (pubstatusid)
                 WHERE
                   pubstatusname in ($pubstatus_check) AND
	           conid=$conid)
  ORDER BY
    display_order
EOD;

// Retrieve query
list($rooms,$unneeded_array_a,$header_array)=queryreport($query,$link,$title,$description,0);

// If it is the vols, don't bother with the bios link.
if ($schedtype=="VolsSched.php") {
  $pubsnamelink='pubsname';
} else {
  $pubsnamelink='"<A HREF=\"PubsBios.php?conid='.$conid.'#",pubsname,"\">",pubsname,"</A>"';
}

/* This set of queries finds the appropriate presenters for a session element,
 based on sessionid, and produces links for them. */
$query = <<<EOD
SELECT
    sessionid,
    GROUP_CONCAT(concat($pubsnamelink,if((moderator in ('1','Yes')),'(m)','')) SEPARATOR ", ") as allpubsnames
  FROM
      Sessions
    JOIN ParticipantOnSession USING (sessionid,conid)
    JOIN Participants USING (badgeid)
  WHERE 
    conid=$conid AND
    volunteer not in ("1","Yes") AND
    introducer not in ("1","Yes") AND
    aidedecamp not in ("1","Yes")
  GROUP BY
    sessionid
  ORDER BY
    sessionid;
EOD;

// Retrieve query
list($presenters,$unneeded_array_b,$presenters_tmp_array)=queryreport($query,$link,$title,$description,0);
for ($i=1; $i<=$presenters; $i++) {
  $presenters_array[$presenters_tmp_array[$i]['sessionid']]=$presenters_tmp_array[$i]['allpubsnames'];
 } 

/* The extra day is because things ran to (or past) midnight
   on the last day of the con.*/
$grid_start_sec=0;
$grid_end_sec=($connumdays + 1)*86400;

/* lasttime0, lasttime1, lasttime2, and setting the skipcount for the
   grid_array allows us to have a blank (or two) between things,
   before breaking the grid.  Currently set to 2 blanks (seen at the
   bottom of each grid as a check).  To set it to two, use lasttime2,
   to set it to one, use lasttime1 and to set it to current time (the
   original behaviour) set it to lasttime0 in the below if:

   if (!empty($grid_array[$lasttimeN]['skipcount']))
*/
$lasttime1="-1";
$lasttime2="-1";
$grid_array["-1"]['skipcount']=1;

/* This complex query set is generated by stepping along by the time
 interval, and, in each interval, setting up the title, sessionid,
 duration, and background color of each class/grid element. */
/* Probably should use queryreport to standardize gets.*/
$header_time=array("Room Name");
$header_count=1;
$newtableline=1;
$breakon[$newtableline]=1;
for ($time=$grid_start_sec; $time<=$grid_end_sec; $time = $time + $Grid_Spacer) {
  $lasttime0=$time;
  $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME(constartdate,SEC_TO_TIME('$time')),'%a&nbsp;%l:%i&nbsp;%p') as 'blocktime'
EOD;

  // loop across the set of rooms.
  for ($i=1; $i<=$rooms; $i++) {
    $x=$header_array[$i]["roomid"];
    $y=$header_array[$i]["roomname"];
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(starttime))),title,\"\") SEPARATOR '') as \"%s title\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(starttime))),sessionid,\"\") SEPARATOR '') as \"%s sessionid\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(starttime))),trackname,\"\") SEPARATOR '') as \"%s track\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(starttime))),",$x);
    $query.="CASE WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')";
    $query.="     WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')";
    $query.="     ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min') END";
    $query.=sprintf(",\"\") SEPARATOR '') as \"%s duration\"",$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(starttime))),IF(estatten,estatten,\"\"),\"\") SEPARATOR '') as \"%s total\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,TY.htmlcellcolor,\"\") SEPARATOR '') as \"%s htmlcellcolor\"",$x,$y);
  }
  $query.=<<<EOD
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN Types TY USING (typeid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
  WHERE
    pubstatusname in ($pubstatus_check) AND
    TIME_TO_SEC(starttime) <= $time AND
    (TIME_TO_SEC(starttime) + TIME_TO_SEC(duration)) >= ($time + $Grid_Spacer) AND
    conid=$conid
EOD;

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

  /* It seems to me that the below to elements can and should be combined,
   somehow.  Otherwise we are walking the same set of loops more times than
   we need. */
  /* Still in the time-stepped loop, we create the elements of the array to
   be called forth, below, in terms of colour, border, and skipvalue. */
  $grid_array[$time]=mysql_fetch_array($result,MYSQL_BOTH);
  $skiprow=0;
  $refskiprow=0;
  for ($i=1; $i<=$rooms; $i++) {
    $j=$header_array[$i]['roomname'];
    if ($grid_array[$time]["$j htmlcellcolor"]!="") {
      $skiprow++;
      if ($grid_array[$time]["$j sessionid"]!="") {
	$grid_array[$time]["$j cellclass"]="border1011d";
	$refskiprow++;
      } else {
	$grid_array[$time]["$j cellclass"]="border1010d";
      }
    } else {
      $grid_array[$time]["$j cellclass"]="border1111";
    }
  }
  if ($skiprow == 0) {
    $grid_array[$time]['skipcount']=1;
    if (!empty($grid_array[$lasttime2]['skipcount'])) {
      $grid_array[$time]['blocktime'] = "Skip";
      if ($breakon[$newtableline] != $header_count) {$breakon[++$newtableline] = $header_count;}
    }
  } else {
    if ($refskiprow != 0) {
      $k=$grid_array[$time]['blocktime'];
      $fk=str_replace("&nbsp;"," ",$k);
      $grid_array[$time]['blocktime']=sprintf("<A HREF=\"%s?format=sched&conid=%s#%s\">%s</A>",$schedtype,$conid,$fk,$k);
    }
    array_push($header_time,$grid_array[$time]['blocktime']);
    $header_count++;
  }
  $lasttime2=$lasttime1;
  $lasttime1=$time;
 }

/* Assembling the body by creating the element_array, of all the
 information in each row, distinguished by $element_row. $breakon allows
 for one tabel per set of skips.  The extra ifs keep the parens out of
 the otherwise empty blocks.  We switch on htmlcellcolor, because, by
 design, that is the only thing written in a continuation block. */
/* This should also make generating the iCal that much easier, when
 that code is added */
$element_row=1;
for ($i=1; $i<=$rooms; $i++) { $header_rooms[$i]=$header_array[$i]['roomname']; }
array_unshift($header_rooms,"Class Time");
for ($j=1; $j<=$rooms; $j++) {
  $element_array[$element_row]["Room Name"] = sprintf("<TD class=\"border1111\">%s</TD>\n",$header_array[$j]['roomname']);
  for ($i = $grid_start_sec; $i < $grid_end_sec; $i = ($i + $Grid_Spacer)) {
      $header_roomname=$header_array[$j]['roomname'];
      $element_col=$grid_array[$i]['blocktime'];
      $bgcolor=$grid_array[$i]["$header_roomname htmlcellcolor"]; //cell background color
      $cellclass=$grid_array[$i]["$header_roomname cellclass"]; //cell edge state
      if ($cellclass == "") {$cellclass="border1111";}
      $sessionid=$grid_array[$i]["$header_roomname sessionid"]; //sessionid
      $title=$grid_array[$i]["$header_roomname title"]; //title
      $track=$grid_array[$i]["$header_roomname track"]; //track
      $duration=$grid_array[$i]["$header_roomname duration"]; // duration
      $total=$grid_array[$i]["$header_roomname total"]; //total
      $presenters=$presenters_array[$sessionid]; //presenters
      if ($bgcolor!="") {
	$element_array[$element_row][$element_col] = sprintf("<TD BGCOLOR=\"%s\" CLASS=\"%s\">",$bgcolor,$cellclass);
	if ($title!="") {
	  $element_array[$element_row][$element_col].= sprintf("<A HREF=\"%s?format=desc&conid=%s#%s\">%s</A>",$schedtype,$conid,$title,$title);
	}
	if (($track!="") and ($schedtype!="VolsSched.php")) {
	  $element_array[$element_row][$element_col].= sprintf(" - <A HREF=\"%s?format=tracks&conid=%s#%s\">%s</A>",$schedtype,$conid,$track,$track);
	}
	if ($duration!="") {
	  $element_array[$element_row][$element_col].= sprintf(" - %s",$duration);
	}
	if ($total!="") {
	  $element_array[$element_row][$element_col].= sprintf("<br>(Count: %s)",$total);
	}
	if ($presenters!="") {
	  $element_array[$element_row][$element_col].= sprintf("<br>\n%s",$presenters);
	}
      } else { $element_array[$element_row][$element_col].= "<TD class=\"border1111\">&nbsp;"; } 
      $element_array[$element_row][$element_col].= "</TD>\n";
    }
    $element_row++;
  }

// Page Rendering
/* Check for the csv variable, to see if we should be dropping a table,
 instead of displaying one.  If so, feed a continuous table, otherwise
 split up the tables on "skip" spaces, to make them flow more naturally.
 Include the $additionalinfo regularly, so one doesn't have to scroll
 all the way back to the top, and it gives a nice visual break. */
if ($_GET["csv"]=="y") {
  topofpagecsv("grid.csv");
  echo rendercsvreport(1,$element_row,$header_time,$element_array);
 } elseif ($_GET["print_p"]=="y") {
  require_once('../../tcpdf/config/lang/eng.php');
  require_once('../../tcpdf/tcpdf.php');
  $pdf = new TCPDF('l', 'mm', 'letter', true, 'UTF-8', false);
  $pdf->SetCreator('Zambia');
  $pdf->SetAuthor('Programming Team');
  $pdf->SetTitle('Grid');
  $pdf->SetSubject('Programming Grid');
  $pdf->SetKeywords('Zambia, Presenters, Volunteers, Programming, Grid');
  $pdf->SetHeaderData($logo, 70, $conname, $conurl);
  $pdf->setHeaderFont(Array("helvetica", '', 10));
  $pdf->setFooterFont(Array("helvetica", '', 8));
  $pdf->SetDefaultMonospacedFont("courier");
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->setLanguageArray($l);
  $pdf->setFontSubsetting(true);
  $pdf->SetFont('helvetica', '', 6, '', true);
  for ($i=1; $i<$newtableline; $i++) {
    for ($j=$breakon[$i]; $j<=$breakon[$i+1]; $j = ($j + 16)) {
      if ($breakon[$i+1]-$j >= 16) {
	$k = 16;
      } else {
	$k = $breakon[$i+1] - $j;
      }
      if ($k > 0) {
	$gridstring=rendergridreport(1,$element_row,array_merge(array_slice($header_time,0,1), array_slice($header_time,$j,$k)),$element_array);
	$pdf->AddPage();
	$pdf->writeHTML($gridstring, true, false, true, false, '');
      }
    }
  }
  $pdf->Output($conname.'-grid-wide.pdf', 'I');
 } else {
  topofpagereport($pagetitle,$description,$additionalinfo,$message,$message_error);
  for ($i=1; $i<$newtableline; $i++) {
    for ($j=$breakon[$i]; $j<=$breakon[$i+1]; $j = ($j + 11)) {
      if ($breakon[$i+1]-$j >= 11) {
	$k = 11;
      } else {
	$k = $breakon[$i+1] - $j;
      }
      if ($k > 0) {
	echo rendergridreport(1,$element_row,array_merge(array_slice($header_time,0,1), array_slice($header_time,$j,$k)),$element_array);
	echo $additionalinfo;
      }
    }
  }
  correct_footer();
 }
