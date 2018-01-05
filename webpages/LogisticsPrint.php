<?php
require_once('StaffCommonCode.php');
global $link;
$conid=$_SESSION['conid'];

// LOCALIZATIONS
$_SESSION['return_to_page']="LogisticsPrint.php";
$title="Logistics";
$description="<P>Logistics information for each of the rooms.</P>\n";
$additionalinfo="<P>List <A HREF=\"LogisticsPrint.php?order=room\">by room </A>\n";
$additionalinfo.="(<A HREF=\"LogisticsPrint.php?order=room&print_p=y\">print</A>)\n";
$additionalinfo.="(<A HREF=\"LogisticsPrint.php?order=room&csv=y\">CSV</A>)\n";
$additionalinfo.="or <A HREF=\"LogisticsPrint.php?order=time\">by time</A>\n";
$additionalinfo.="(<A HREF=\"LogisticsPrint.php?order=time&print_p=y\">print</A>)\n";
$additionalinfo.="(<A HREF=\"LogisticsPrint.php?order=time&csv=y\">CSV</A>).</P>\n";

/* This query returns the room names, start time, sessionid, title,
 services, features, and any other tech notes for printing */
$query = <<<EOD
SELECT
    concat('<A HREF="MaintainRoomSched.php?selroom=',roomid,'">',roomname,'</A>') AS Room,
    DATE_FORMAT(ADDTIME(constartdate,starttime),'%a&nbsp;%l:%i&nbsp;%p') as 'Start Time',
    concat('<A HREF=StaffAssignParticipants.php?selsess=',sessionid,'>',sessionid,'</A>') AS Sessionid,
    concat('<a href=EditSession.php?id=',sessionid,'>',title,'</a>') Title,
    roomsetname as 'Room Set',
  if((servicelist!=''),concat("<UL><LI>",servicelist,"</LI></UL>"),'') as 'Services',
    if((featurelist!=''),featurelist,'') as 'Features',
    if((servicenotes!=''),servicenotes,'') as 'Hotel and Tech Notes'
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN Divisions USING (divisionid)
    JOIN ConInfo USING (conid)
    LEFT JOIN  (SELECT
           sessionid,
           GROUP_CONCAT(DISTINCT servicename SEPARATOR '</LI><LI>') as 'servicelist'
        FROM
            Sessions
	  JOIN SessionHasService USING (sessionid,conid)
          JOIN Services USING (serviceid,conid)
        WHERE
	  conid=$conid
        GROUP BY
          sessionid
        ORDER BY
          servicename) X USING (sessionid)
    LEFT JOIN (SELECT
           sessionid,
           GROUP_CONCAT(DISTINCT featurename SEPARATOR ', ') as 'featurelist'
        FROM
            Sessions
	  JOIN SessionHasFeature USING (sessionid,conid)
          JOIN Features USING (featureid,conid)
        WHERE
          conid=$conid
        GROUP BY
          sessionid) Y USING (sessionid)
    LEFT JOIN (SELECT
	  sessionid,
	  roomsetname
        FROM
	    Sessions
	  JOIN RoomSets USING (roomsetid)
        WHERE
	  conid=$conid) Z USING (sessionid)
  WHERE
    divisionname in ('Programming', 'Events') AND
    conid=$conid
 ORDER BY

EOD;

if ($_GET["order"]=="time") {
  $query.="    starttime,\n    roomname";
 } else {
  $query.="    roomname,\n    starttime";
 }

// Retrieve query
list($rows,$header_array,$roomset_array)=queryreport($query,$link,$title,$description,0);

$checkfield="";
$newtableline=0;
for ($i=1; $i<=$rows; $i++) {
  if ($_GET["order"]=="time") {
    if ($roomset_array[$i]['Start Time'] != $checkfield) {
      $checkfield=$roomset_array[$i]['Start Time'];
      $breakon[$newtableline++]=$i;
    }
  } else {
    if ($roomset_array[$i]['Room'] != $checkfield) {
      $checkfield=$roomset_array[$i]['Room'];
      $breakon[$newtableline++]=$i;
    }
  }
 }
$breakon[$newtableline]=$i;

// Page Rendering
/* Check for the csv variable, to see if we should be dropping a table,
 instead of displaying one.  If so, feed a continuous table, otherwise
 split up the tables on "skip" spaces, to make them flow more naturally.
 Include the $additionalinfo regularly, so one doesn't have to scroll
 all the way back to the top, and it gives a nice visual break. */
if ($_GET["csv"]=="y") {
  topofpagecsv("Logistics_grid.csv");
  echo rendercsvreport(1,$rows,$header_array,$roomset_array);
 } elseif ($_GET["print_p"]=="y") {
  require_once('../../tcpdf/config/lang/eng.php');
  require_once('../../tcpdf/tcpdf.php');
  $pdf = new TCPDF('l', 'mm', 'letter', true, 'UTF-8', false);
  $pdf->SetCreator('Zambia');
  $pdf->SetAuthor('Programming Team');
  $pdf->SetTitle('Logistics Grid');
  $pdf->SetSubject('Logistics Grid');
  $pdf->SetKeywords('Zambia, Rooms, Logistics, Services, Features, Tech Notes, Grid');
  $pdf->SetHeaderData($_SESSION['conlogo'], 70, $_SESSION['conname'], $_SESSION['conurl']);
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
  $pdf->SetFont('helvetica', '', 12, '', true);
  for ($i=0; $i<$newtableline; $i++) {
    $htmlstring=renderhtmlreport($breakon[$i],$breakon[$i+1]-1,$header_array,$roomset_array);
    $pdf->AddPage();
    $pdf->writeHTML($htmlstring, true, false, true, false, '');
  }
  $pdf->Output($_SESSION['conname'].'-grid.pdf', 'I');
 } else {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  for ($i=0; $i<$newtableline; $i++) {
    echo renderhtmlreport($breakon[$i],$breakon[$i+1]-1,$header_array,$roomset_array);
  }
  correct_footer();
 }
