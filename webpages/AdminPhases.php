<?php
require_once ('StaffCommonCode.php');
global $link;
$title="Setup or change Phases";
$description="<P>Select the appropriate phases for this time.</P>\n";
$additionalinfo="";
$conid=$_SESSION['conid'];

// Who can modify what:
if ((may_I("Maint")) or (may_I("ConChair"))) {
  $additionalinfo.="<P>Check or uncheck the current phases relevent now.";
} else {
  $additionalinfo="<P>You don't have permission to change any elements at this time.</P>\n";
}

/* Check to see if the phases actually exist for this con instance */
$add_phases=0;
$phase_element_array = array('conid','phasetypeid','phasestate');
$query="SELECT phasetypeid, phasestate FROM Phase WHERE conid=$conid";
list($phaserows,$phaseheader_array,$phase_array)=queryreport($query,$link,$title,$description,0);
if (in_array("This report retrieved no results matching the criteria.",array_keys($phase_array))) {
  $add_phases++;
  $message.="No phases.<br>\n";
} else {
  for ($i=1; $i<=$phaserows; $i++) {
    $phase[$phase_array[$i]['phasetypeid']]=$phase_array[$i]['phasestate'];
    //$message.="Phase for $i:<br>\n";
  }
}


/* Fetch the phases. */
$query="SELECT phasetypeid, phasetypename, phasetypedescription FROM PhaseTypes";
list($phasetyperows,$phasetypeheader_array,$phasetype_array)=queryreport($query,$link,$title,$description,0);
if (in_array("This report retrieved no results matching the criteria.",array_keys($phasetype_array))) {
  $phasetype_array=array();
} else {
  $keyheader_array=array("Phase Name","Phase Description");
  for ($i=1; $i<=$phasetyperows; $i++) {
    $pti=$phasetype_array[$i]['phasetypeid'];
    $phasetype[$pti]=$phasetype_array[$i]['phasetypename'];
    $key_array[$i]['Phase Name']=$phasetype_array[$i]['phasetypename'];
    $key_array[$i]['Phase Description']=$phasetype_array[$i]['phasetypedescription'];
    if (!isset($phase[$pti])) {
      $value_array = array($conid,$pti,"");
      $message.=submit_table_element($link, $title, "Phase", $phase_element_array, $value_array);
      $phase[$pti]="";
    }
  }
}

/* When this file is retuned to, with the values set, put the values
   into the database, so they can become the active set of phases.
   probably should be done up higher, but ... can update the array
   here as well.
*/
if (($_POST['update']=='Yes') and ((may_I("Maint")) or (may_I("ConChair")))) {

  // Update phases
  foreach ($_POST['wasphasetypeid'] as $key => $value) {
    if (($_POST['wasphasetypeid'][$key]=="not") and
	($_POST['phasetypeid'][$key]=="checked")) {
      $set_array=array("phasestate='0'");
      $match_string="phasetypeid=".$key." AND conid=".$conid;
      $message.=update_table_element_extended_match($link, $title, "Phase", $set_array, $match_string);
    }
    if (($_POST['wasphasetypeid'][$key]=="indeed") and
	($_POST['phasetypeid'][$key]!="checked")) {
      $set_array=array("phasestate=''");
      $match_string="phasetypeid=".$key." AND conid=".$conid;
      $message.=update_table_element_extended_match($link, $title, "Phase", $set_array, $match_string);
    }
  }
}

/* Fetch the services already offered, this should be done after the
   possible update so any updates will be incorporated.
   This is a bit chancy, depending on someone not screwing up the
   display_order.
*/
$query="SELECT phasetypeid FROM Phase where conid=$conid and phasestate in ('0')";
list($workrows,$workheader_array,$work_array)=queryreport($query,$link,$title,$description,0);
if (in_array("This report retrieved no results matching the criteria.",array_keys($work_array))) {
  $work_array=array();
} else {
  for ($i=1; $i<=$workrows; $i++) {
    $workname_array[$i]=$work_array[$i]['phasetypeid'];
  }
  $workname_list=implode(",",$workname_array);
}

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Phases set only by ConChair or the Janitor
if ((may_I("Maint")) or (may_I("ConChair"))) {

  /* Start form.  Any button will update all of the lists.  Since they
     only change if there is something different selected, hence the
     hidden -> update only has to be once.
  */
  echo "<FORM name=\"updatelists\" class=\"bb\" method=POST action=\"AdminPhases.php\">\n";
  echo "  <INPUT type=\"hidden\" name=\"update\" value=\"Yes\">\n";

  echo "  <SPAN><LABEL for=\"phasetypeid\">Which Phases are currently set:<br></LABEL>\n";
  // $label, $element_list, $key, $value, $boxarray
  populate_checkbox_block_from_array("phasetypeid",$workname_list,"phasetypeid","phasetypename",$phasetype_array);
  echo "  </SPAN>\n  <BR>\n";
  echo "  <BUTTON class=\"ib\" type=submit value=\"Update\">Update</BUTTON>\n";

  // Close the form
  echo "</FORM>";
}

$keystring="<br>\n<HR>\n<P>Key:</P>\n";
$keystring.=renderhtmlreport(1,count($key_array),$keyheader_array,$key_array);
echo $keystring;

// Close the page
correct_footer();
?>