<?php
require_once('StaffCommonCode.php');
$title="Compensation Information";
$description="<P>Here is all the compensation entered for all the people.</P>\n";
$additionalinfo="<P>To <A HREF=\"StaffEditCompensation.php\">change</A> someone's compensation, click on their name.</P>\n";

// Check to see if page can be displayed
if (!may_I("SuperLiaison") AND !may_I("Treasurer")) {
  $message_error ="Alas, you do not have the proper permissions to view this page.";
  $message_error.=" If you think this is in error, please, get in touch with an administrator.";
  RenderError($title,$message_error);
  exit();
}

// See if the grid is requested with notes.
$withnotes=0;
if (isset($_GET['Notes']) AND ($_GET['Notes']=="Y")) {
  $withnotes=1;
  $additionalinfo.="<P><A HREF=\"PresenterCompensation.php\">Without Notes</A> :: \n";
} else {
    $additionalinfo.="<P><A HREF=\"PresenterCompensation.php?Notes=Y\">With Notes</A> :: \n";
}

$additionalinfo.="<A HREF=\"PresenterCompensation.php?Notes=Y&print_p=y\">Print with notes</A> :: \n";
$additionalinfo.="<A HREF=\"PresenterCompensation.php?print_p=y\">Print without notes</A> :: \n";
$additionalinfo.="<A HREF=\"PresenterCompensation.php?Notes=Y&csv=y\">csv with notes</A> :: \n";
$additionalinfo.="<A HREF=\"PresenterCompensation.php?csv=y\">csv without notes</A></P>\n";

// Get the comptypeid and comptypename for the header fields
$query = <<<EOD
SELECT 
    comptypeid,
    comptypename,
    comptypedescription
  FROM
      CompensationTypes
  ORDER BY
    comptypeid;
EOD;

// Retrieve query
list($comptypecount,$unneeded_array_a,$comptype_array)=queryreport($query,$link,$title,$description,0);

// Set up the header cells.
$header_array[0]="Presenter";
$header_array[1]="Attending";
$key_header_array[0]="Key";
$headercount=2;
for ($i=1; $i<=$comptypecount; $i++) {
  $header_array[$headercount++]=sprintf("<A HREF=\"#key%s\">%s</A>",$comptype_array[$i]['comptypeid'],$comptype_array[$i]['comptypename']);
  if ($withnotes) {
    $header_array[$headercount++]=$comptype_array[$i]['comptypename']." Notes";
  }
  $key_array[$i]['Key']=sprintf("<A NAME=\"key%s\"></A>%s: %s",$comptype_array[$i]['comptypeid'],$comptype_array[$i]['comptypename'],$comptype_array[$i]['comptypedescription']);
}

// Second walk through: Fetch the compensation per presenter
for ($i=1; $i<=$comptypecount; $i++) {
  $query = <<<EOD
SELECT 
    pubsname,
    badgeid,
    conid,
    conname,
    compamount,
    compdescription
  FROM
      Compensation
    JOIN Participants USING (badgeid)
    JOIN ConInfo USING (conid)
  WHERE
    comptypeid=$i
  ORDER BY
    pubsname
EOD;
  
  // Retrieve query
  list($tmpcompcount,$unneded_array_b,$tmp_comp_array)=queryreport($query,$link,$title,$description,0);

  // Set up the comp_array and totals
  for ($j=1; $j<=$tmpcompcount; $j++) {
    $comp_array[$tmp_comp_array[$j]['pubsname']][$tmp_comp_array[$j]['conid']][$i]["Value"]=$tmp_comp_array[$j]['compamount'];
    $comp_array[$tmp_comp_array[$j]['pubsname']][$tmp_comp_array[$j]['conid']][$i]["Note"]=$tmp_comp_array[$j]['compdescription'];
    $badge_array[$tmp_comp_array[$j]['pubsname']][$tmp_comp_array[$j]['conid']]=$tmp_comp_array[$j]['badgeid'];
    $total_array[$tmp_comp_array[$j]['conid']][$i]=$total_array[$tmp_comp_array[$j]['conid']][$i]+$tmp_comp_array[$j]['compamount'];
    $conid_array[$tmp_comp_array[$j]['conid']]=$tmp_comp_array[$j]['conid'];
    $conname_array[$tmp_comp_array[$j]['conid']]=$tmp_comp_array[$j]['conname'];
    if ((strpos($comptype_array[$i]['comptypename'],"Expense") !== false) or
	(strpos($comptype_array[$i]['comptypename'],"Cost") !== false) or
	(strpos($comptype_array[$i]['comptypename'],"Honorarium") !== false)) {
      $total_array[$tmp_comp_array[$j]['conid']][0]=$total_array[$tmp_comp_array[$j]['conid']][0]+$tmp_comp_array[$j]['compamount'];
    }
  }
}

// get the conids in reverse order.
rsort($conid_array);

$query = <<<EOD
SELECT 
    interestedtypename,
    conid,
    pubsname
  FROM
      Interested
    JOIN InterestedTypes USING (interestedtypeid)
    JOIN Participants USING (badgeid)
EOD;
  
// Retrieve query
list($interestedcount,$unneded_array_b,$interested_array)=queryreport($query,$link,$title,$description,0);

// Integrate the interested array
for ($i=1; $i<=$interestedcount; $i++) {
  $int_array[$interested_array[$i]['pubsname']][$interested_array[$i]['conid']]=$interested_array[$i]['interestedtypename'];
}

$rows=1;
// Walk the presenters, and make the array of values.
foreach ($conid_array as $conid) {
  $report_array[$rows]['Presenter']="";
  $report_array[$rows]['Attending']="<B>".$conname_array[$conid]."</B>";
  $rows++;
  foreach ($comp_array as $presenter => $comp) {
    if (isset($badge_array[$presenter][$conid])) {
      $report_array[$rows]['Presenter']="<A HREF=\"StaffEditCompensation.php?partid=".$badge_array[$presenter][$conid]."&conid=$conid\">$presenter</A>";
      $report_array[$rows]['Attending']="<A HREF=\"AdminParticipants.php?partid=".$badge_array[$presenter][$conid]."\">";
      if (isset($int_array[$presenter][$conid]) AND ($int_array[$presenter][$conid]!='')) {
	$report_array[$rows]['Attending'].=$int_array[$presenter][$conid];
      } else {
	$report_array[$rows]['Attending'].="??";
      }
      $report_array[$rows]['Attending'].="</A>";
      for ($i=1; $i<=$comptypecount; $i++) {
	$report_array[$rows][sprintf("<A HREF=\"#key%s\">%s</A>",$comptype_array[$i]['comptypeid'],$comptype_array[$i]['comptypename'])]=$comp[$conid][$i]["Value"];
	if ($withnotes) {
	  $report_array[$rows][$comptype_array[$i]['comptypename']." Notes"]=$comp[$conid][$i]["Note"];
	}
      }
      $rows++;
    }
  }
  // Totals line
  $report_array[$rows]['Presenter']="TOTALS ".$conname_array[$conid].":";
  $report_array[$rows]['Attending']="$".$total_array[$conid][0];
  for ($i=1; $i<=$comptypecount; $i++) {
    $report_array[$rows][sprintf("<A HREF=\"#key%s\">%s</A>",$comptype_array[$i]['comptypeid'],$comptype_array[$i]['comptypename'])]=$total_array[$conid][$i];
  }
  $rows++;
}

if ($_GET["csv"]=="y") {
  topofpagecsv($_SESSION['conname']."-Presenter_Compensation.csv");
  echo rendercsvreport(1,$rows,$header_array,$report_array);
 } elseif ($_GET["print_p"]=="y") {
  require_once('../../tcpdf/config/lang/eng.php');
  require_once('../../tcpdf/tcpdf.php');
  $pdf = new TCPDF('l', 'mm', 'letter', true, 'UTF-8', false);
  $pdf->SetCreator('Zambia');
  $pdf->SetAuthor('Programming Team');
  $pdf->SetTitle('Presenter Compensation');
  $pdf->SetSubject('Presenter Compensation');
  $pdf->SetKeywords('Zambia, Presenter Compensation');
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
  $pdf->SetFont('helvetica', '', 10, '', true);
  $htmlstring=renderhtmlreport(1,$rows,$header_array,$report_array);
  $pdf->AddPage();
  $pdf->writeHTML($htmlstring, true, false, true, false, '');
  $htmlstring=renderhtmlreport(1,$comptypecount,$key_header_array,$key_array);
  $pdf->AddPage();
  $pdf->writeHTML($htmlstring, true, false, true, false, '');
  $pdf->Output($_SESSION['conname'].'-Presenter_Compensation.pdf', 'I');
} else {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo renderhtmlreport(1,$rows,$header_array,$report_array);
  echo renderhtmlreport(1,$comptypecount,$key_header_array,$key_array);
  correct_footer();
}

?>