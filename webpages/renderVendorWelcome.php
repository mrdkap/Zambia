<?php
require_once('VendorCommonCode.php');
require_once('Vendor_FNC.php');
global $link, $participant, $message, $message_error;

// LOCALIZATIONS
$title="Vendor View";
$description="";
$additionalinfo="<P>Vendor FAQ: ";
$additionalinfo.="<A HREF=\"http://fetishflea.com/index.php?page=vending-community-tables\">";
$additionalinfo.="http://fetishflea.com/index.php?page=vending-community-tables</A></P>\n";
$conid=$_SESSION['conid'];
$badgeid=$_SESSION['badgeid'];

// If new vendor application submitted, do it.
if (($_POST['vendorstatustypename'] == "Applied") and ($_POST['submit'] == "New Vendor")) {
  $message.=create_vendor($_POST);
  // $message.="In Applied, New Vendor";
  // Hope to have two passes here eventually, with the same input, so both are serviced.
  // Needs testing before I do.
  // $message.=edit_vendor_update($_POST);
  // $message.=edit_vendor_apply($_POST);

  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo "<P>Thank you for applying to the " . $_SESSION['conname'] . ".  Please feel free\n";
  echo "to <A HREF=\"doLogin.php?newconid=$conid&badgeid=".$_POST['email']."\">log\n";
  echo "in</A> at this time.  All updates to your state, and your next steps will be found there.</P>\n";
  echo "<P>If you have any issues please get in touch with the Vendor Coordinator <some address></P>\n";
  correct_footer();
  exit();
}

// If an updated vendor application submitted, do it.
if (($_POST['vendorstatustypename'] == "Updated") and ($_POST['submit'] == "Updated Application")) {
  $message.=edit_vendor_apply($_POST);
  $message.="Updated Applicaton";
}

// If an application for a vendor is submitted, by them or by SuperVendor, do it.
if (($_POST['vendorstatustypename'] == "Applied") and ($_POST['submit'] == "Updated Application")) {
  $message.=edit_vendor_apply($_POST);
  $message.="Updated Applicaton";
}

// If an updated vendor business information submitted, do it.
if (($_POST['vendorstatustypename'] == "Updated") and ($_POST['submit'] == "Updated Information")) {
  $message.=edit_vendor_update($_POST);
  $message.="Updated Information";
}

// If a contract is being signed, apply the name.
if (($_POST['vendorcontract'] == "Signed") and ($_POST['submit'] == "Vendor Contract")) {
  $query="SELECT badgeid from VendorAnnualInfo where badgeid=$badgeid and conid=$conid";
  list($isannualrows,$isannualheader_array,$isannual_array)=queryreport($query,$link,$title,$description,0);
  if ($isannualrows != 1) {
    $message_error.="You somehow have not actually managed to sign up for this year.\n";
    $message_error.="Please, go back to that step, and try again.\n";
    RenderError($title,$message_error);
    exit();
  }
  // Get the Vendors's name from CongoDump
  $queryVendorName=<<<EOD
SELECT
    concat(firstname, " ", lastname) AS name
  FROM
    CongoDump
  WHERE
    badgeid=$badgeid
EOD;

  list($vnamerows,$vnameheader_array,$vname_array)=queryreport($queryVendorName,$link,$title,$description,0);

  // Simplify, so it can be substituted below.
  $vname=$vname_array[1]['name'];

  if ($vname == htmlspecialchars_decode($_POST['vendoracknowledgement'])) {
    $set_array=array("vendoracknowledgement=\"".htmlspecialchars_decode($_POST['vendoracknowledgement'])."\"");
    $match_string="badgeid=".$badgeid." AND conid=".$conid;
    $message.=update_table_element_extended_match($link, $title, "VendorAnnualInfo", $set_array, $match_string);

    // Update the value in the table
    $set_array=array("vendorstatustypeid=3");
    $match_string="badgeid=".$_SESSION['badgeid']." AND conid=".$conid;
    $verbose.=update_table_element_extended_match($link, $title, "VendorStatus", $set_array, $match_string);

    // Update a note that it was done.
    $element_array = array('badgeid', 'rbadgeid', 'note','conid');
    $value_array=array($_SESSION['badgeid'],
		       $_SESSION['badgeid'],
		       "Promoted self (" . $_SESSION['badgename'] . ") from Applied.",
		       $_SESSION['conid']);
    $verbose.=submit_table_element($link, $title, "NotesOnVendors", $element_array, $value_array);
  }
}

// If the last updated vendor application before the invoice is submitted, do it, and update the state.
if (($_POST['vendorstatustypename'] == "Updated") and ($_POST['submit'] == "Pre-Invoice")) {
  $message.=edit_vendor_apply($_POST);
  $message.="In Updated, Pre-Invoice<br \>\n";

  // Promote Vendor Status to "Invoiced"
  $queryVendorStatusType="SELECT vendorstatustypeid FROM VendorStatusTypes WHERE vendorstatustypename='Invoiced'";
  list($vendorstatustyperows,$vendorstatustypeheader_array,$vendorstatustype_array)=queryreport($queryVendorStatusType,$link,$title,$description,0);
  if ($vendorstatustyperows != 1) {
    $message_error.="Somehow there are more or less Vendor Status Types maping to Invoiced.";
    $message_error.="Please check your database for inconsistencies, or suggest a change.\n";
    RenderError($title,$message_error);
    exit();
  }

  // Update the value in the table
  $set_array=array("vendorstatustypeid=".$vendorstatustype_array[1]['vendorstatustypeid']);
  $match_string="badgeid=".$_SESSION['badgeid']." AND conid=".$conid;
  $verbose.=update_table_element_extended_match($link, $title, "VendorStatus", $set_array, $match_string);

  // Update a note that it was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($_SESSION['badgeid'],
		     $_SESSION['badgeid'],
		     "Promoted self (" . $_SESSION['badgename'] . ") to Invoiced.",
		     $_SESSION['conid']);
  $verbose.=submit_table_element($link, $title, "NotesOnVendors", $element_array, $value_array);

}

// If there is an invoice submitted, add the data to the invoice table,
// and go to the next status.
if (($_POST['return'] == "Submit Order") and
    ($_POST['MStatus'] == "success") and
    ($_POST['FinalStatus'] == "success")) {
  $element_array = array('badgeid', 'conid', 'invoiceref','invoiceorderid','invoicedate','invoiceamt','invoicecost','invoicepaid','invoicename','invoicedesc','invoiceemail','invoiceshipemail');
  $value_array=array($_SESSION['badgeid'],
		     $_SESSION['conid'],
		     $_POST['refnumber'],
		     $_POST['orderID'],
		     $_POST['auth_date'],
		     $_POST['amountcharged'],
		     $_POST['cost1'],
		     $_POST['card-amount'],
		     $_POST['item1'],
		     $_POST['description1'],
                     $_POST['email'],
		     $_POST['shipemail']);
  $verbose.=submit_table_element($link, $title, "Invoice", $element_array, $value_array);

  error_log("Zambia: Vendor Invoice all information: " . print_r($_POST,true));

  // Get the current status information
  $queryVendorCurrentStatus=<<<EOD
SELECT
    vendorstatustypeid
  FROM
      VendorStatus
    JOIN VendorStatusTypes USING (vendorstatustypeid)
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

  list($vcstatusrows,$vcstatusheader_array,$vcstatus_array)=queryreport($queryVendorCurrentStatus, $link, $title, $description, 0);

  // promote the current status information to the next status
  $vcstatusid=$vcstatus_array[1]['vendorstatustypeid'];
  $vcstatusid++;

  // Update the database with the new status
  $set_array=array("vendorstatustypeid=".$vcstatusid);
  $match_string="badgeid=".$_SESSION['badgeid']." AND conid=".$conid;
  $verbose.=update_table_element_extended_match($link, $title, "VendorStatus", $set_array, $match_string);

  // Update a note that it was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($_SESSION['badgeid'],
		     $_SESSION['badgeid'],
		     "Paid an invoice (" . $_POST['item1'] . ") and promoted self (" . $_SESSION['badgename'] . ") from " . $vcstatus_array[1]['vendorstatustypeid'] . " to " . $vcstatusid . ".",
		     $_SESSION['conid']);
  $verbose.=submit_table_element($link, $title, "NotesOnVendors", $element_array, $value_array);
}

// Default status name/id
$vstatusid=0;
$vstatusname="Waiting To Apply";

// Get this person's Vendor Status
$queryVendorStatus=<<<EOD
SELECT
    vendorstatustypeid,
    vendorstatustypename
  FROM
      VendorStatus
    JOIN VendorStatusTypes USING (vendorstatustypeid)
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

list($vstatusrows,$vstatusheader_array,$vstatus_array)=queryreport($queryVendorStatus, $link, $title, $description, 0);

// Make sure there is only one row.
if ($vstatusrows==1) {
  $vstatusid=$vstatus_array[1]['vendorstatustypeid'];
  $vstatusname=$vstatus_array[1]['vendorstatustypename'];
} elseif ($vstatusrows > 1) {
  $message_error.="There are more than one vendor status rows for this user.";
  RenderError($title,$message_error);
  exit();
}

// Begin the output.  Most of the below is in Verbiage entries.
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if (may_I('SuperVendor')) { // Force an application for someone
  echo "<P>As someone with SuperVendor privs, you can create a new vendor here.</P>\n";
  $badgeid="100";
  $verbiage=get_verbiage("VendorWelcomeApply");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
    echo "<P>Please stand by, this is still under construction.</P>\n";
    echo "<P>You are the Super Vendor, but this isn't set up yet for this con.</P>\n";
  }
} elseif (may_I('Vendor')) {
  echo "<H3>You are currently at the $vstatusname status.</H3>\n";
  $verbiage=get_verbiage("VendorWelcome_" . $vstatusid);
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
    echo "<P>Here you can create/update your vendor profile, and\n";
    echo "   apply to be a vendor at this event.</P>\n";
    echo "<P>Most events are Juried, so not everyone who applies might get\n";
    echo "   in.</P>\n";
    echo "<P>You have to indicate that you are interested in the event, to\n";
    echo "   be considered by the folks who will decide, to begin with.</P>\n";
    echo "<P>You can do any of the following things at this time:\n";
    echo "<UL>\n";
    echo "  <LI> <A HREF=\"VendorSubmitVendor.php\">Update</A> your contact (vendor) information.</LI>\n";
    if (may_I("vendor_apply")) {
      echo "  <LI> <A HREF=\"VendorApply.php\">Apply</A> to be a vendor at " . $_SESSION['conname'] . "\n";
    }
    echo "</UL></P>\n";
  } // end of local current vendor words
} elseif (may_I('vendor_apply')) { // Applying to vend at this show
  // echo "<FORM name=\"vendorform\" action=\"tmp_form.php\" method=POST>\n";
  $verbiage=get_verbiage("VendorWelcomeApply");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
    echo "<P>Please stand by, this is still under construction.</P>\n";
    echo "<P>If you are a returning vendor, please\n";
    echo "<A HREF=\"login.php?newconid=$conid\">log in</A>\n";
    echo "with your email address and password.</P>\n";
    echo "<P>If you wish to apply to be a vendor, please fill out the following form:</P>\n";
  } // end of local apply to be vendor words
} else { // brainstorm/vendor not permitted
    echo "<P>This page should never be seen.</P>\n";
  $verbiage=get_verbiage("VendorWelcome_100");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else { 
    echo "<P>We are not accepting new vendors at this time for " . $_SESSION['conname'] . ".</P>\n";
    echo "<P>You may still use the \"Search\" tab to view the vendors who might be attending and/or those that have been accepted.</P>\n";
  } // end of local not accepting at this time words
} //end of brainstorm/vendor not permitted

correct_footer(); 
?>
