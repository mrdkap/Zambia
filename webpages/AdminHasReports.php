<?php
require_once ('StaffCommonCode.php');
global $link;
$title="Setup or change who reports to whom";
$description="<P>Select the appropriate reports for this con.</P>\n";
$additionalinfo="";
$conid=$_SESSION['conid'];

// Who can modify what:
if ((may_I("Maint")) or (may_I("ConChair"))) {
  $additionalinfo.="<P>Check or uncheck the reports relevent for this con.";
} else {
  $additionalinfo="<P>You don't have permission to change any elements at this time.</P>\n";
}

/* Fetch the phases. */
$query="SELECT conroleid, conrolenotes, conroledescription FROM ConRoles";
list($conrolerows,$conroleheader_array,$conrole_array)=queryreport($query,$link,$title,$description,0);
if (in_array("This report retrieved no results matching the criteria.",array_keys($conrole_array))) {
  $conrole_array=array();
} else {
  $keyheader_array=array("Role Name","Role Description");
  for ($i=1; $i<=$conrolerows; $i++) {
    $cri=$conrole_array[$i]['conroleid'];
    $conrole[$cri]=$conrole_array[$i]['conrolenotes'];
    $key_array[$i]['Role Name']=$conrole_array[$i]['conrolenotes'];
    $key_array[$i]['Role Description']=$conrole_array[$i]['conroledescription'];
  }
}

$role=0;
if ((isset($_GET['role'])) && (is_numeric($_GET['role']))) {
  $role=$_GET['role'];
} elseif ((isset($_POST['role'])) && (is_numeric($_POST['role']))) {
  $role=$_POST['role'];
}

/* When this file is retuned to, with the roles associated, put the
   values into the database, so they can become the active set of
   reports.  Probably should be done up higher, but ... can update the
   array here as well.
*/
if (($_POST['update']=='Yes') and ((may_I("Maint")) or (may_I("ConChair")))) {

  // Update HasReports
  foreach ($_POST['washasreport'] as $key => $value) {
    if (($_POST['washasreport'][$key]=="not") &&
        ($_POST['hasreport'][$key]=="checked")) {
      $element_array=array("conid","conroleid","hasreport");
      $value_array=array($conid,$role,$key);
      $message.=submit_table_element($link, $title, "HasReports", $element_array, $value_array);
    }
    if (($_POST['washasreport'][$key]=="indeed") &&
	($_POST['hasreport'][$key]!="checked")) {
      $match_string="conroleid=".$role." AND hasreport=".$key." AND conid=".$conid;
      $message.=delete_table_element($link, $title, "HasReports", $match_string);
    }
  }
}

if (($role!=0) and ((may_I("Maint")) or (may_I("ConChair")))) {
  $query="SELECT hasreport FROM HasReports where conid=$conid and conroleid=$role";
  list($workrows,$workheader_array,$work_array)=queryreport($query,$link,$title,$description,0);
  if (in_array("This report retrieved no results matching the criteria.",array_keys($work_array))) {
    $work_array=array();
  } else {
    for ($i=1; $i<=$workrows; $i++) {
      $workname_array[$i]=$work_array[$i]['hasreport'];
    }
    $workname_list=implode(",",$workname_array);
  }
}

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

/* Start form.  Any button will update all of the lists.  Since they
   only change if there is something different selected, hence the
   hidden -> update only has to be once.
*/
echo "<FORM name=\"selroles\" class=\"bb\" method=POST action=\"AdminHasReports.php\">\n";

/* Give the possible roles to select from. */
$query="SELECT conroleid, conrolenotes AS role FROM ConRoles";
echo "<DIV><LABEL for=\"role\">Select Role </LABEL>\n";
echo "<SELECT name=\"role\">\n";
echo populate_select_from_query_inline($query, $role, "Role:", true);
echo "</SELECT></DIV>\n";

// Submit button
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";

// Close the form
echo "</FORM>\n";

// If there isn't a role selected, end here.
if ($role==0) {
  // Display the key
  $keystring="<br>\n<HR>\n<P>Key:</P>\n";
  $keystring.=renderhtmlreport(1,count($key_array),$keyheader_array,$key_array);
  echo $keystring;

  // Close the page
  correct_footer();
  exit;
}
  
// Reports set only by ConChair or the Janitor
if ((may_I("Maint")) or (may_I("ConChair"))) {

  // Begin the page that has the selection process on it.
  echo "\n<HR>\n";

  // Start the form
  echo "<FORM name=\"selreports\" class=\"bb\" method=POST action=\"AdminHasReports.php\">\n";

  // This will give us the clue that we need to update something, and the role we update it for.
  echo "  <INPUT type=\"hidden\" name=\"update\" value=\"Yes\">\n";
  echo "  <INPUT type=\"hidden\" name=\"role\" value=\"$role\">\n";
  echo "  <SPAN><LABEL for=\"hasreportid\">Who reports to ".$conrole[$role].":<br></LABEL>\n";
  // $label, $element_list, $key, $value, $boxarray
  echo populate_checkbox_block_from_array("hasreport",$workname_list,"conroleid","conrolenotes",$conrole_array);
  echo "  </SPAN>\n  <BR>\n";
  echo "  <BUTTON class=\"ib\" type=submit value=\"Update\">Update</BUTTON>\n";

  // Close the form
  echo "</FORM>";
}

// Display the key
$keystring="<br>\n<HR>\n<P>Key:</P>\n";
$keystring.=renderhtmlreport(1,count($key_array),$keyheader_array,$key_array);
echo $keystring;

// Close the page
correct_footer();
?>