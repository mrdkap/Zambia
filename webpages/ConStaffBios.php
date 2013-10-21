<?php
require_once('PostingCommonCode.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// LOCALIZATIONS
$_SESSION['return_to_page']="ConStaffBios.php";
$title="Organizational Chart";
$description="<P>List of all organizers, their roles, who reports to them, and who they report to.</P>\n";
$additionalinfo="";
$conid=$_SESSION['conid'];

$query= <<<EOD
SELECT
    concat('  <DT><A NAME="',CRA.conrolename,'"><B>',CRA.conrolenotes,'</B></A> (<A HREF="#',CRA.conrolename,'.desc">Job Description</A>)</DT>\n') AS Role,
    group_concat(DISTINCT pubsname SEPARATOR '/') AS Who,
    concat('Reports To: <A HREF="#',CRC.conrolename,'">',CRC.conrolenotes,'</A><br>\n') AS 'ReportsTo',
    group_concat(DISTINCT '    <LI><A HREF="#',CRB.conrolename,'">',CRB.conrolenotes,'</A></LI>' SEPARATOR '\n') AS Reports,
  concat('  <DT><A NAME="',CRA.conrolename,'.desc" HREF="#',CRA.conrolename,'">',CRA.conrolenotes,'</A></DT>\n   <DD>',CRA.conroledescription,'</DD>\n') AS Description
  FROM
      $ReportDB.ConRoles CRA
    LEFT JOIN $ReportDB.UserHasConRole UHCR ON (UHCR.conroleid=CRA.conroleid AND UHCR.conid=$conid)
    LEFT JOIN $ReportDB.Participants USING (badgeid)
    LEFT JOIN $ReportDB.HasReports HRA ON (HRA.conroleid=CRA.conroleid AND HRA.conid=$conid)
    LEFT JOIN $ReportDB.ConRoles CRB ON (CRB.conroleid=HRA.hasreport)
    LEFT JOIN $ReportDB.HasReports HRB ON (HRB.hasreport=CRA.conroleid AND HRB.conid=$conid)
    LEFT JOIN $ReportDB.ConRoles CRC ON (CRC.conroleid=HRB.conroleid)
  WHERE
    HRA.conid=$conid
  GROUP BY
    CRA.conroleid
EOD;

// Get the various values from the databases
// Retrive query
list($rows,$header_array,$roles_array)=queryreport($query,$link,$title,$description,0);

// Build the string:
$descstring="<HR>\n<H2>Job Descriptions</H2>\n<DL>\n";
$webstring= "<DL>\n";
for ($i=1; $i<=$rows; $i++) {
  $webstring.=$roles_array[$i]['Role'];
  $webstring.="  <DD>".$roles_array[$i]['Who']."<br>\n";
  $webstring.=$roles_array[$i]['ReportsTo'];
  $webstring.="  Direct Reports:\n  <UL>\n";
  $webstring.=$roles_array[$i]['Reports'];
  $webstring.="\n  </UL>\n</DD>\n";
  $descstring.=$roles_array[$i]['Description'];
  }
$webstring.="</DL>\n$descstring\n</DL>\n";

// Start page

if ($included!="YES") {
  topofpagereport($title,$description,$additionalinfo);
  if (file_exists("../Local/Verbiage/ConStaffBios_1")) {
    echo file_get_contents("../Local/Verbiage/ConStaffBios_1");
  } else {
     echo $webstring;
  }
  correct_footer(); 
}
?>