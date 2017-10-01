<?php
require_once ('StaffCommonCode.php');
global $link, $message, $message_error;

// Set the vendorid
$vendorid="";
if ((!empty($_GET['vendorid'])) and (is_numeric($_GET['vendorid']))) {
  $vendorid=$_GET['vendorid'];
} elseif ((!empty($_POST['vendorid'])) and (is_numeric($_POST['vendorid']))) {
  $vendorid=$_POST['vendorid'];
}

if ($vendorid !="") {
  $congoinfo=getCongoData($vendorid);
  $vendorname=$congoinfo['badgename'];
  if (empty($congoinfo['badgeid'])) {
    $message_error.=" since there is no vendor associated with that badgeid.";
    RenderError($title,$message_error);
    exit();
  }
} else {
  $vendorname="No Vendor Selected";
}

// LOCALISMS
$title="Change Vendor Status for $vendorname";
$description="<P>Select the appropriate status for $vendorname.</P>\n";
$additionalinfo="";
$conid=$_SESSION['conid'];

// If there is no vendor selected, exit here.  This might want to have the vendor pulldowns instead.
if (empty($vendorid)) {
  $message_error="Please only use this page with a selected vendor.";
  RenderError($title,$message_error);
  exit();
}

/* Fetch the states. */
$queryVstat=<<<EOD
SELECT
    vendorstatustypeid as "ID",
    vendorstatustypename as "Vendor Status",
    vendorstatustypedesc as "Vendor Description"
  FROM
    VendorStatusTypes
  ORDER BY
    display_order
EOD;

list($vstatrows,$vstatheader_array,$vstat_array)=queryreport($queryVstat,$link,$title,$description,0);


/* When this file is retuned to, with the status set, put the status
   into the database, so the vendor can be in the appropriate phase.
*/
if (($_POST['update']=='Yes') and ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor")))) {
  if ((!empty($_POST['vendorstatustypeid'])) and (is_numeric($_POST['vendorstatustypeid']))) {
    $set_array=array("vendorstatustypeid=".$_POST['vendorstatustypeid']);
    $match_string="badgeid=".$vendorid." AND conid=".$conid;
    $message.=update_table_element_extended_match($link, $title, "VendorStatus", $set_array, $match_string);

    // Submit a note about what was done.
    $element_array = array('badgeid', 'rbadgeid', 'note','conid');
    $value_array=array($vendorid,
		       $_SESSION['badgeid'],
		       "Changed vendor $vendorid state to " . $_POST['vendorstatustypeid'] . ".",
		       $_SESSION['conid']);
    $message.=submit_table_element($link, $title, "NotesOnVendors", $element_array, $value_array);
  }
}

// Fetch the Vendor Status after possible update so any updates will be incorporated.
$queryVstatType=<<<EOD
SELECT
    vendorstatustypename
  FROM
      VendorStatus
    JOIN VendorStatusTypes USING (vendorstatustypeid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
EOD;

list($vstattyperows,$vstattypeheader_array,$vstattype_array)=queryreport($queryVstatType,$link,$title,$description,0);
if ($vstattyperows == 0) {
  $message_error.="This vendor has yet to apply.";
  RenderError($title,$message_error);
  exit();
} elseif ($vstattyperows != 1) {
  $message_error.="There are multiple returns for your query: $queryVstatType";
  RenderError($title,$message_error);
  exit();
}

$vstattype=$vstattype_array[1]['vendorstatustypename'];
  
// Who can modify what:
if ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor"))) {
  $additionalinfo.="<P>Update the state from $vstattype to the relevent appropriate state now.";
} else {
  $additionalinfo="<P>You don't have permission to change any elements at this time.</P>\n";
}

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Vendor Statuses set only by ConChair, SuperVendor, or the Janitor
if ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor"))) {

  echo "<FORM name=\"updatevendorstat\" class=\"bb\" method=POST action=\"VendorAdminState.php\">\n";
  echo "  <input type=\"hidden\" name=\"update\" value=\"Yes\">\n";
  echo "  <input type=\"hidden\" name=\"vendorid\" value=\"$vendorid\">\n";
  echo "  <SPAN><LABEL for=\"vendorstatustypeid\">Vendor Status:<br></LABEL>\n";
  // $label, $element_list, $key, $value, $button_array
  echo populate_radio_block_from_array("vendorstatustypeid",$vstattype,"Vendor Status","ID",$vstat_array);
  echo "  </SPAN>\n  <BR>\n";
  echo "  <A HREF=\"" . $_SESSION['return_to_page'] . "\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>\n";
  echo "  <BUTTON class=\"ib\" type=submit value=\"Update\">Update</BUTTON>\n";

  // Close the form
  echo "</FORM>";
}

$keystring="<br>\n<HR>\n<P>Key:</P>\n";
$keystring.=renderhtmlreport(1,$vstatrows,$vstatheader_array,$vstat_array);
echo $keystring;

// Close the page
correct_footer();
?>