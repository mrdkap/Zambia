<?php
require_once('VendorCommonCode.php');
require_once('Vendor_FNC.php');
require_once('Vendor_FNC2.php');
global $participant,$message,$message_error,$message2;

// LOCALIZATIONS
$title="Vendor View";
$description="";
$additionalinfo="";
$message_error.=$message2;
$conid=$_SESSION['conid'];
$badgeid=$_SESSION['badgeid'];

// If new vendor application submitted, do it.
if (($_POST['vendorstatustypename'] == "Applied") and ($_POST['submit'] == "New Vendor")) {
  $message.=create_vendor($_POST);
  // Hope to have two passes here eventually, with the same input, so both are serviced.
  // $message.=edit_vendor_apply($_POST);
  // $message.=edit_vendor_update($_POST);

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
  $set_array=array("vendoracknowledgement=\"".htmlspecialchars_decode($_POST['vendoracknowledgement'])."\"");
  $match_string="badgeid=".$badgeid." AND conid=".$conid;
  $message.=update_table_element_extended_match($link, $title, "VendorAnnualInfo", $set_array, $match_string);
}


// Default status name/id
$vstatusid=0;
$vstatusname="Waiting To Apply";

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

// Temporary so I can test things
/*
$queryVendorStatus=<<<EOD
SELECT
    vendorstatustypeid,
    vendorstatustypename
  FROM
      VendorStatusTypes
  WHERE
    vendorstatustypeid=12
EOD;
*/

list($vstatusrows,$vstatusheader_array,$vstatus_array)=queryreport($queryVendorStatus, $link, $title, $description, 0);

if ($vstatusrows==1) {
  $vstatusid=$vstatus_array[1]['vendorstatustypeid'];
  $vstatusname=$vstatus_array[1]['vendorstatustypename'];
}

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if (may_I('Vendor')) {
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
  echo "<!-- <form action=\"https://nelaonline.org/fetish-fair-fleamarket/summer-fleamarket/summer-vendors/vendor-application\" method=\"post\" accept-charset=\"utf-8\" class=\"crud_form\" id=\"\" enctype=\"multipart/form-data\"> -->\n";
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
