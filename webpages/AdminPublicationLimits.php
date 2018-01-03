<?php
require_once('StaffCommonCode.php');
global $link, $message, $message_error;

/*
function get_enum_values( $table, $field )
{
    $type = $this->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $enum = explode("','", $matches[1]);
    return $enum;
}
*/

// LOCALIZATIONS
$title="Create/Update Vendor Locations";
$description="<P>Create and update this event's instances of the possible Vendor Locations.</P>\n";
$additionalinfo="<P>Each section has it's own update button.\n";
$additionalinfo.="Please do not try to update more than one section at once, ";
$additionalinfo.="it will not (necessarily) be heeded properly.</P>\n";
$additionalinfo.="<P>This page is limited to a few select people who can change it, ";
$additionalinfo.="basically the Con Chair and the Super Vendor folks.</P>\n";
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// Get the publimname array and the publimtype array
$publimname_array=get_enum_values('PublicationLimits','publimname');
$publimtype_array=get_enum_values('PublicationLimits','publimtype');

// Get the BioDests
$queryBioDests="SELECT biodestname FROM BioDests order by display_order";
// Get the query
list($biodestrows,$biodestheader_array,$biodest_array)=queryreport($queryBioDests,$link,$title,$description,0);
for ($i=1; $i<=$biodestrows; $i++) {
  $publimdest_array[]=$biodest_array[$i]['biodestname'];
}

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

foreach ($publimname_array as $publimname) {
  echo "<TABLE border=2>\n  <TR><TH>$publimname</TH></TR>\n  <TR>\n";
  foreach ($publimdest_array as $publimdest) {
    echo "    <TD><TABLE border=1>\n      <TR><TH colspan=2>$publimdest</TH></TR>\n      <TR>\n";
    foreach ($publimtype_array as $publimtype) {
      echo "        <TD>$publimtype = &nbsp;&nbsp;</TD>";
    }
    echo "</TR></TABLE></TD></TR>\n";
  }
  echo "</TABLE>\n";
}

correct_footer();
?>


