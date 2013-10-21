<?php
global $link,$participant,$message_error,$message2,$congoinfo;
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
unlock_participant(''); // unlocks any records locked by current user

// LOCALIZATIONS
$conid=$_SESSION['conid']; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// To make navigation easier
$jumpstring="<P>Jump to: <A HREF=\"#Flow\">Session Flow</A> :: <A HREF=\"#ToO\">Table of Orgaization</A> :: <A HREF=\"#Jobs\">Job Descriptions</A></P>\n";

// Header variables
$title="Staff Overview";
$description=$jumpstring;
$additionalinfo ="<P>Please note the tabs above.   One of them will take you to your ";
$additionalinfo.="participant view.  Another will allow you to manage Sessions.  Note that ";
$additionalinfo.="\"Sessions\" is the generic term we are using for all Events, Films, Panels, ";
$additionalinfo.="Anime, Video, etc. and \"Precis\" is used for the description of same.</P>\n";
$additionalinfo.="<P>There is always the somewhat-spotty <A HREF=\"../Documentation\">documentation</A></P>\n";

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

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
    HRB.conid=$conid OR
    CRA.conroleid=1
  GROUP BY
    CRA.conroleid
EOD;

// Retrive query
list($rows,$header_array,$roles_array)=queryreport($query,$link,$title,$description,0);

// Build the string:
$descstring="<A NAME=\"Jobs\"></A><H2>Job Descriptions</H2>\n<DL>\n";
$todstring= "<A NAME=\"ToO\"></A><H2>Table of Organization</H2>\n<DL>\n";
for ($i=1; $i<=$rows; $i++) {
  $todstring.=$roles_array[$i]['Role'];
  $todstring.="  <DD>".$roles_array[$i]['Who']."<br>\n";
  $todstring.=$roles_array[$i]['ReportsTo'];
  if (isset($roles_array[$i]['Reports'])) {
    $todstring.="  Direct Reports:\n  <UL>\n";
    $todstring.=$roles_array[$i]['Reports'];
    $todstring.="\n  </UL>\n</DD>\n";
  } else {$todstring.="</DD>\n";}
  $descstring.=$roles_array[$i]['Description'];
  }
$todstring.="</DL>\n";
$descstring.="</DL>\n";

$query= <<<EOD
SELECT
    concat("  <LI>",statusname," - ",statusdescription,"</LI>") AS Description
  FROM
      $ReportDB.SessionStatuses
  ORDER BY
    display_order
EOD;

// Retrive query
list($rows,$header_array,$statusdesc_array)=queryreport($query,$link,$title,$description,0);

// Build the string:
$flowstring="<A NAME=\"Flow\"></A><H2>Session Flow</H2>\n";
$flowstring.="<P>The general flow of sessions over time is:\n<UL>\n";
for ($i=1; $i<=$rows; $i++) {
  $flowstring.=$statusdesc_array[$i]['Description']."\n";
}
$flowstring.="</UL></P>\n";

topofpagereport($title,$description,$additionalinfo);
if (file_exists("../Local/Verbiage/StaffPage_1")) {
  echo file_get_contents("../Local/Verbiage/StaffPage_1");
} else {
  echo "<HR>\n";
  echo $flowstring;
  echo $jumpstring;
  echo "<HR>\n";
  echo $todstring;
  echo $jumpstring;
  echo "<HR>\n";
  echo $descstring;
  echo $jumpstring;
}
staff_footer(); 
?>
