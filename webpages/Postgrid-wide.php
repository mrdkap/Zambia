<?php
require_once('PostingCommonCode.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

$title="Sessions Grid";
$pagetitle=$title;

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// Deal with what is passed in.
if (!empty($_SERVER['QUERY_STRING'])) {
  $passon="?".$_SERVER['QUERY_STRING'];
  $passon_p=$passon."&print_p=y";
} else {
  $passon_p="?print_p=y";
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

if (isset($_GET['volunteer'])) {
  $pubstatus_check="'Volunteer'";
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
}

// LOCALIZATIONS
$_SESSION['return_to_page']="Postgrid-wide.php";
$title="Sessions Grid for $conname";
$pagetitle=$title;
$description="<P>Grid of all sessions.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.php$passon\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"Bios.php$passon\">bio</A>, the time to visit that section of\n";
$additionalinfo.="the <A HREF=\"Schedule.php$passon\">schedule</A>, or the track name to see all the classes\n";
$additionalinfo.="by <A HREF=\"Tracks.php$passon\">track</A>. (<A HREF=\"Postgrid.php$passon\">Switch indices</A>)</P>\n";
$additionalinfo.="<P>If you wish to have a copy printed, please download the <A HREF=Postgrid.php$passon_p>Rooms\n";
$additionalinfo.="x Times</A> or <A HREF=Postgrid-wide.php$passon_p>Times x Rooms</A> version.</P>\n";

/* This query returns the room names for an array, to be used as
 headers, and keys for other arrays.*/
$query = <<<EOD
SELECT
    roomname,
    roomid
  FROM
      $ReportDB.Rooms
  WHERE
    roomid in (SELECT
                   DISTINCT roomid
                 FROM
EOD;
if ($conid==CON_KEY) {
  $query.=" Schedule JOIN Sessions USING (sessionid) ";
} else {
  $query.=" $ReportDB.Schedule JOIN $ReportDB.Sessions USING (sessionid,conid) ";
}
$query.= <<<EOD
                   JOIN $ReportDB.PubStatuses USING (pubstatusid)
                 WHERE
                   pubstatusname in ($pubstatus_check)
EOD;
if ($conid==CON_KEY) {
  $query.=") ORDER BY display_order";
} else {
  $query.=" AND conid=$conid) ORDER BY display_order";
}

// Retrieve query
list($rooms,$unneeded_array_a,$header_array)=queryreport($query,$link,$title,$description,0);

/* This set of queries finds the appropriate presenters for a session element,
 based on sessionid, and produces links for them. */

$query = <<<EOD
SELECT
    sessionid,
    GROUP_CONCAT(concat("<A HREF=\"Bios.php$passon#",pubsname,"\">",pubsname,"</A>",if((moderator=1),'(m)','')) SEPARATOR ", ") as allpubsnames
  FROM
EOD;
if ($conid==CON_KEY) {
  $query.=" Sessions JOIN ParticipantOnSession USING (sessionid) ";
} else {
  $query.=" $ReportDB.Sessions JOIN $ReportDB.ParticipantOnSession USING (sessionid,conid) ";
}
$query.= <<<EOD
    JOIN $ReportDB.Participants USING (badgeid)
  WHERE 
EOD;
if ($conid==CON_KEY) {
  $query.="";
} else {
  $query.=" conid=$conid AND  ";
}
$query.= <<<EOD
    volunteer=0 AND
    introducer=0 AND
    aidedecamp=0
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

$grid_start_sec=0;
$grid_end_sec=$connumdays*86400;
/* This complex query set is generated by stepping along by the time interval,
 and, in each interval, setting up the title, sessionid, duration, and background
 color of each class/grid element. */
/* Probably should use queryreport to standardize gets.*/
$header_time=array("Room Name");
$header_count=1;
$newtableline=1;
$breakon[$newtableline]=1;
for ($time=$grid_start_sec; $time<=$grid_end_sec; $time = $time + $Grid_Spacer) {
  $query="SELECT DATE_FORMAT(ADDTIME('$ConStartDatim',SEC_TO_TIME('$time')),'%a&nbsp;%l:%i&nbsp;%p') as 'blocktime'";
  for ($i=1; $i<=$rooms; $i++) {
    $x=$header_array[$i]["roomid"];
    $y=$header_array[$i]["roomname"];
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.title,\"\") SEPARATOR '') as \"%s title\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.sessionid,\"\") SEPARATOR '') as \"%s sessionid\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.duration,\"\") SEPARATOR '') as \"%s duration\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),IF(S.estatten,S.estatten,\"\"),\"\") SEPARATOR '') as \"%s total\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,T.htmlcellcolor,\"\") SEPARATOR '') as \"%s htmlcellcolor\"",$x,$y);
  }
  if ($conid==CON_KEY) {
    $query.=" FROM Schedule SCH JOIN Sessions S USING (sessionid)";
  } else {
    $query.=" FROM $ReportDB.Schedule SCH JOIN $ReportDB.Sessions S USING (sessionid,conid)";
  }
  $query.=" JOIN $ReportDB.Rooms R USING (roomid) JOIN $ReportDB.Types T USING (typeid) JOIN $ReportDB.PubStatuses PS USING (pubstatusid)";
  $query.=" WHERE PS.pubstatusname in ($pubstatus_check) AND TIME_TO_SEC(SCH.starttime) <= $time";
  $query.=" AND (TIME_TO_SEC(SCH.starttime) + TIME_TO_SEC(S.duration)) >= ($time + $Grid_Spacer)";
  if ($conid==CON_KEY) {
    $query.=";";
  } else {
    $query.=" AND conid=$conid;";
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
	$grid_array[$time]["$j cellclass"]="border1101d";
	$refskiprow++;
      } else {
	$grid_array[$time]["$j cellclass"]="border0101d";
      }
    } else {
      $grid_array[$time]["$j cellclass"]="border1111";
    }
  }
  if ($skiprow == 0) {
    $grid_array[$time]['blocktime'] = "Skip";
    if ($breakon[$newtableline] != $header_count) {$breakon[++$newtableline] = $header_count;}
  } else {
    if ($refskiprow != 0) {
      $k=$grid_array[$time]['blocktime'];
      $fk=str_replace("&nbsp;"," ",$k);
      $grid_array[$time]['blocktime']=sprintf("<A HREF=\"Schedule.php%s#%s\">%s</A>",$passon,$fk,$k);
    }
    array_push($header_time,$grid_array[$time]['blocktime']);
    $header_count++;
  }
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
      $duration=substr($grid_array[$i]["$header_roomname duration"],0,-3); // duration; drop ":00" representing seconds off the end
      if (substr($duration,0,1)=="0") {$duration = substr($duration,1,999);} // drop leading "0"
      $total=$grid_array[$i]["$header_roomname total"]; //total
      $presenters=$presenters_array[$sessionid]; //presenters
      if ($bgcolor!="") {
	$element_array[$element_row][$element_col] = sprintf("<TD BGCOLOR=\"%s\" CLASS=\"%s\">",$bgcolor,$cellclass);
	if ($title!="") {
	  $element_array[$element_row][$element_col].= sprintf("<A HREF=\"Descriptions.php%s#%s\">%s</A>",$passon,$sessionid,$title);
	}
	if ($duration!="") {
	  $element_array[$element_row][$element_col].= sprintf(" (%s)",$duration);
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
  $pdf->SetHeaderData($logo, 70, $conname, CON_URL);
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
  topofpagereport($pagetitle,$description,$additionalinfo);
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
  posting_footer();
 }
