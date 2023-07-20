<?php
require_once('StaffCommonCode.php');
global $link;

$mybadgeid=$_SESSION['badgeid']; // make it a simple variable so it can be substituted
$conid=$_SESSION['conid']; // make it a simple variable so it can be substituted

// Get the various length limits for substitution
$limit_array=getLimitArray();

// LOCALIZATIONS
$showreport=0;
$reportid=$_GET["reportid"];
$reportname=$_GET["reportname"];
$title="General Report Generator";
$additionalinfo="";

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

// Check for addtion
if (isset($_POST['addto'])) {
  add_flow_report($_POST['addto'],$_POST['addphase'],"Personal","",$title,$description);
 }

// Switch on which way this is called
if (!$reportname) {
  $_SESSION['return_to_page']="genreport.php?reportid=$reportid";
  $description="<P>If you are seeing this, something failed trying to get report: $reportid.</P>\n";
 } else {
  $_SESSION['return_to_page']="genreport.php?reportname=$reportname";
  $description="<P>If you are seeing this, something failed trying to get report: $reportname.</P>\n";
  $showreport++;
 }
if ($reportid) {$showreport++;}

// No reportid, load the all-reports page
if ($showreport==0) {
  $_SESSION['return_to_page']="genreport.php";
  $title="List of all reports";
  $description="<P>Here is a list of all the reports that are available to be generated.</P>\n";
  $query = <<<EOD
SELECT
    concat("<A HREF=genreport.php?reportid=",reportid,">",reporttitle,"</A> (<A HREF=genreport.php?reportid=",reportid,"&csv=y>csv</A>)") AS Title,
    reportdescription AS Description
  FROM
      Reports
  ORDER BY
    reportname
EOD;

  // Retrieve query
  list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

  // Page Rendering
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo renderhtmlreport(1,$rows,$header_array,$report_array);
  correct_footer();
 } else {

  $query = <<<EOD
SELECT
    reportname,
    reportid,
    reporttitle,
    reportdescription,
    reportadditionalinfo,
    reportrestrictions,
    reportquery
  FROM
      Reports
  WHERE
EOD;

  if (!$reportid) {
    $query.="\n    reportname = '$reportname'";
  } else {
    $query.="\n    reportid in ($reportid)";
  }

  // Retrieve query
  list($returned_reports,$reportnumber_array,$report_array)=queryreport($query,$link,$title,$description,0);

  // Get the full personal flow for previous and next
  $query = <<<EOD
SELECT
    reportid,
    pflownote
  FROM
      PersonalFlow
      LEFT JOIN Phase USING (phasetypeid)
  WHERE
    badgeid='$mybadgeid' AND
    (phasetypeid is null OR
     (phasestate = TRUE AND
      conid=$conid))
  ORDER BY
    pfloworder
EOD;

  // Retrieve query
  if (!$result=mysqli_query($link,$query)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }

  while ($row=mysqli_fetch_assoc($result)) {
    $pflow_array[]=$row['reportid'];
    $pflow_notes[]=$row['pflownote'];
  }

  // Begin the per-report cycle.
  for ($i=1; $i<=$returned_reports; $i++) {
    $basereportid=$report_array[$i]['reportid'];

    /* Check for permissions - if there is an array of permissions set on the report
       walk through those permissions, to see if the person may see the report.  If
       not, set the query to a null value and return that instead. 
     */
    $empty_permissions_p=1;
    $allowed_p=0;
    if (!empty($report_array[$i]['reportrestrictions'])) {
      $empty_permissions_p=0;
      $permissions_array=explode(',',$report_array[$i]['reportrestrictions']);
      foreach ($permissions_array as $permission_check) {
	if (may_I($permission_check)) {
	  $allowed_p++;
	}
      }
    }
    if (($empty_permissions_p==0) AND ($allowed_p==0)) {
      $report_array[$i]['reportquery']="SELECT '' AS 'You do not have permission to view this table.  If you think this is an error, please contact a Zambia Administrator.'";
    }

    // Start with a blank $personal, walk the array, set the previous and next for this reportid
    $personal="Personal Flow: ";
    for ($j=0; $j<count($pflow_array); $j++) {
      if ($pflow_array[$j]==$basereportid) {
	if ($j > 0) {
	  $personal.="<A HREF=genreport.php?reportid=".$pflow_array[$j-1].">Prev</A> ";
	}
	if ($j < count($pflow_array)-1) {
	  $personal.="<A HREF=genreport.php?reportid=".$pflow_array[$j+1].">Next</A>";
	}
	if ($pflow_notes[$j]!="") {
	  $personal.=" (Note: ".$pflow_notes[$j].")";
	}
      }
    }
    if ($personal=="Personal Flow: ") {$personal="";}

    // Get the Groups that this report is part of
    $query = <<<EOD
SELECT
    GF.gflowname
  FROM
      Reports R,
      GroupFlow GF
  WHERE
    R.reportid=GF.reportid AND
    R.reportid=$basereportid
EOD;

    // Retrieve query
    list($grouplistrows,$grouplistheader_array,$grouplistreport_array)=queryreport($query,$link,$title,$description,0);

    $groups="";

    // Iterate accross the Groups getting the previous and next
    for ($j=1; $j<=$grouplistrows; $j++) {
      $cgroup=$grouplistreport_array[$j]['gflowname'];
      $query = <<<EOD
SELECT
    DISTINCT reportid
  FROM
      GroupFlow
      LEFT JOIN Phase USING (phasetypeid)
  WHERE
    gflowname='$cgroup' AND
      (phasetypeid is null OR (phasestate = TRUE and conid=$conid))
  ORDER BY
    gfloworder
EOD;

      // Retrieve query
      list($gflowrows,$gflowheader_array,$gflow_array)=queryreport($query,$link,$title,$description,0);

      // Start with a blank $cgroups, walk the array, set the previous and next
      $cgroups=" $cgroup Flow: ";
      for ($k=1; $k<=$gflowrows; $k++) {
	if ($gflow_array[$k]['reportid']==$basereportid) {
	  if ($k > 1) {
	    $cgroups.="<A HREF=genreport.php?reportid=".$gflow_array[$k-1]['reportid'].">Prev</A> ";
	  }
	  if ($k < $gflowrows) {
	    $cgroups.="<A HREF=genreport.php?reportid=".$gflow_array[$k+1]['reportid'].">Next</A>";
	  }
	}
      }
      if ($cgroups==" $cgroup Flow: ") {$cgroups="";}
      $groups.=$cgroups;
    }

    // Fix references in the string so variables can be substituted in.
    $report_array[$i]['reportquery']=eval("return<<<EOF\n".$report_array[$i]['reportquery']."\nEOF;\n");

    // Retrieve secondary query
    list($rows,$header_array,$class_array)=queryreport($report_array[$i]['reportquery'],$link,$report_array[$i]['reporttitle'],$report_array[$i]['reportdescription'],$reportid);
    $report_array[$i]['reportadditionalinfo'].="<P>Generat a <A HREF=\"genreport.php?reportid=".$report_array[$i]['reportid']."&csv=y\" target=_blank>csv</A> file or\n";
    $report_array[$i]['reportadditionalinfo'].="<A HREF=\"genreport.php?reportid=".$report_array[$i]['reportid']."&print_p=y\" target=_blank>print</A> the file";
    if (may_I("Maint")) {
      $report_array[$i]['reportadditionalinfo'].="\nor <A HREF=\"EditReport.php?selreport=".$report_array[$i]['reportid']."\">edit</A> this report";
    }
    $report_array[$i]['reportadditionalinfo'].=".</P>\n";
    $report_array[$i]['reportadditionalinfo'].="<P><FORM name=\"addto\" method=POST action=\"genreport.php?reportid=".$report_array[$i]['reportid']."\">";
    $report_array[$i]['reportadditionalinfo'].="<INPUT type=\"hidden\" name=\"addto\" value=\"".$report_array[$i]['reportid']."\">";
    $report_array[$i]['reportadditionalinfo'].=" <INPUT type=submit value=\"Add\">";
    $report_array[$i]['reportadditionalinfo'].=" this report to your Personal Flow. (If you wish, put in the phase number: ";
    $report_array[$i]['reportadditionalinfo'].="<LABEL for=\"addphase\" ID=\"addphase\"></LABEL>";
    $report_array[$i]['reportadditionalinfo'].="<INPUT type=\"text\" name=\"addphase\" size=\"1\">.)";
    $report_array[$i]['reportadditionalinfo'].="</FORM>\n";
    $report_array[$i]['reportadditionalinfo'].="<P>".$personal.$groups."</P>";
    if ($returned_reports > 1) {$report_array[$i]['reportadditionalinfo'].="<P>Report $i of $returned_reports</P>\n";}

    // Page Rendering
    if ($_GET["csv"]=="y") {
      topofpagecsv($report_array[$i]['reportname'].".csv");
      echo rendercsvreport(1,$rows,$header_array,$class_array);
    } elseif ($_GET["print_p"]=="y") {
        require_once('../../tcpdf/config/lang/eng.php');
        require_once('../../tcpdf/tcpdf.php');
	$logo=$_SESSION['conlogo'];
	$pdf = new TCPDF('p', 'mm', 'letter', true, 'UTF-8', false);
	$pdf->SetCreator('Zambia');
	$pdf->SetAuthor('Programming Team');
	$pdf->SetTitle($report_array[$i]['reporttitle']);
	$pdf->SetSubject($report_array[$i]['reporttitle']);
	$pdf->SetKeywords('Zambia, Report, '.$report_array[$i]['reportname']);
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
	$htmlstring=renderhtmlreport(1,$rows,$header_array,$class_array);
	$pdf->AddPage();
	$pdf->writeHTML($htmlstring, true, false, true, false, '');
	$pdf->Output($_SESSION['conname'].'-'.$report_array[$i]['reportname'].'-grid.pdf', 'I');
    } else {
      topofpagereport($report_array[$i]['reporttitle'],$report_array[$i]['reportdescription'],$report_array[$i]['reportadditionalinfo'],$message,$message_error);
      echo renderhtmlreport(1,$rows,$header_array,$class_array);
      if ($i==$returned_reports) {
	correct_footer();
      }
    }
  }
 }
?>
