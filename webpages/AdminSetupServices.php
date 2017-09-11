<?php
require_once ('StaffCommonCode.php');
global $link;
$title="Setup Services et al";
$description="<P>Select the various elements that should be migrated to this con-instance.</P>\n";
$additionalinfo="";
$conid=$_SESSION['conid'];

// Who can modify what:
if ((may_I("SuperProgramming")) or (may_I("SuperLogistics"))) {
  $additionalinfo.="<P>Check or uncheck the Services and Features you desire to have ";
  $additionalinfo.="available or unavailable for request in the schedule elements.</P>\n";
} else {
  $additionalinfo="<P>You don't have permission to change any elements at this time.</P>\n";
}

// Fetch the base list of services.
$query="SELECT baseserviceid, baseservicename FROM BaseServices";
list($baseservicerows,$baseserviceheader_array,$baseservice_array)=queryreport($query,$link,$title,$description,0);
if (in_array("This report retrieved no results matching the criteria.",array_keys($baseservice_array))) {
  $baseservice_array=array();
} else {
  for ($i=1; $i<=$baseservicerows; $i++) {
    $baseservice[$baseservice_array[$i]['baseserviceid']]=$baseservice_array[$i]['baseservicename'];
  }
}

// Fetch the base list of features.
$query="SELECT basefeatureid, basefeaturename FROM BaseFeatures";
list($basefeaturerows,$basefeatureheader_array,$basefeature_array)=queryreport($query,$link,$title,$description,0);
if (in_array("This report retrieved no results matching the criteria.",array_keys($basefeature_array))) {
  $basefeature_array=array();
} else {
  for ($i=1; $i<=$basefeaturerows; $i++) {
    $basefeature[$basefeature_array[$i]['basefeatureid']]=$basefeature_array[$i]['basefeaturename'];
  }
}

/* When this file is retuned to, with the values set, put the values
   into the database, so they can be selected for the
   schedule-elements.
*/
if ($_POST['update']=='Yes') {

  // Update services
  foreach ($_POST['wasserviceid'] as $key => $value) {
    if (($_POST['wasserviceid'][$key]=="not") and
	($_POST['serviceid'][$key]=="checked")) {
      $element_array = array('servicename', 'conid', 'display_order');
      $value_array = array($baseservice[$key],$_SESSION['conid'],$key);
      $message.=submit_table_element($link, $title, "Services", $element_array, $value_array);
    }
    if (($_POST['wasserviceid'][$key]=="indeed") and
	($_POST['serviceid'][$key]!="checked")) {
      $match_string="display_order=".$key." AND conid=".$_SESSION['conid'];
      $message.=delete_table_element($link, $title, "Services", $match_string);
    }
  }

  // Update features
  foreach ($_POST['wasfeatureid'] as $key => $value) {
    if (($_POST['wasfeatureid'][$key]=="not") and
	($_POST['featureid'][$key]=="checked")) {
      $element_array = array('featurename', 'conid', 'display_order');
      $value_array = array($basefeature[$key],$_SESSION['conid'],$key);
      $message.=submit_table_element($link, $title, "Features", $element_array, $value_array);
    }
    if (($_POST['wasfeatureid'][$key]=="indeed") and
	($_POST['featureid'][$key]!="checked")) {
      $match_string="display_order=".$key." AND conid=".$_SESSION['conid'];
      $message.=delete_table_element($link, $title, "Features", $match_string);
    }
  }

  /*
  foreach (somehow VendorFeatures) {
    // insert feature/display order into VendorFeatures, with
  appropriate conid and price
  }
  foreach (somehow VendorSpaces) {
    // insert space/display order into VendorSpaces, with appropriate
  conid and price
  }
  */
}

/* Fetch the services already offered, this should be done after the
   possible update so any updates will be incorporated.
   This is a bit chancy, depending on someone not screwing up the
   display_order.
*/
$query="SELECT display_order FROM Services where conid=$conid";
list($servicerows,$serviceheader_array,$service_array)=queryreport($query,$link,$title,$description,0);
if (in_array("This report retrieved no results matching the criteria.",array_keys($service_array))) {
  $service_array=array();
} else {
  for ($i=1; $i<=$servicerows; $i++) {
    $servicename_array[$i]=$service_array[$i]['display_order'];
  }
  $servicename_list=implode(",",$servicename_array);
}

/* Fetch the features already offered, this should be done after the
   possible update so any updates will be incorporated.
   This is a bit chancy, depending on someone not screwing up the
   display_order.
*/
$query="SELECT display_order FROM Features where conid=$conid";
list($featurerows,$featureheader_array,$feature_array)=queryreport($query,$link,$title,$description,0);
if (in_array("This report retrieved no results matching the criteria.",array_keys($feature_array))) {
  $feature_array=array();
} else {
  for ($i=1; $i<=$featurerows; $i++) {
    $featurename_array[$i]=$feature_array[$i]['display_order'];
  }
  $featurename_list=implode(",",$featurename_array);
}

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

/* Start form.  Any button will update all of the lists.  Since they
   only change if there is something different selected, hence the
   hidden -> update only has to be once.
*/
echo "<FORM name=\"updatelists\" class=\"bb\" method=POST action=\"AdminSetupServices.php\">\n";
echo "  <INPUT type=\"hidden\" name=\"update\" value=\"Yes\">\n";

// Service set, only by SuperProgramming or SuperLogistics
if ((may_I("SuperProgramming")) or (may_I("SuperLogistics"))) {
  echo "  <HR>\n";
  echo "  <SPAN><LABEL for=\"serviceid\">Which service are available:</LABEL>\n";
  echo populate_checkbox_block_from_array("serviceid",$servicename_list,"baseserviceid","baseservicename",$baseservice_array);
  echo "  </SPAN>\n  <BR>\n";
  echo "  <BUTTON class=\"ib\" type=submit value=\"Update\">Update</BUTTON>\n";
}

// Feature set, only by SuperProgramming or SuperLogistics
if ((may_I("SuperProgramming")) or (may_I("SuperLogistics"))) {
  echo "  <HR>\n";
  echo "  <SPAN><LABEL for=\"featureid\">Which features are available:</LABEL>\n";
  echo populate_checkbox_block_from_array("featureid",$featurename_list,"basefeatureid","basefeaturename",$basefeature_array);
  echo "  </SPAN>\n  <BR>\n";
  echo "  <BUTTON class=\"ib\" type=submit value=\"Update\">Update</BUTTON>\n";
}

// Close the form
echo "</FORM>";

// Close the page
correct_footer();
?>