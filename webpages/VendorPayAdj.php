<?php
require_once ('StaffCommonCode.php');
global $link, $message, $message_error;

// Set the vendorid
$vendorid="";

// See if it is set by the pulldowns
if (may_I('SuperVendor')) {
  // Collaps the two choices into one
  if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}
  if ($_POST["partide"]!=0) {$_POST["partid"]=$_POST["partide"];}

  if (isset($_POST["partid"])) {
    $vendorid=$_POST["partid"];
  } elseif (isset($_GET["partid"])) {
    $vendorid=$_GET["partid"];
  }
}

// Supercede the pulldowns if it is explicitly passed.
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
$title="Pay adjustments for $vendorname";
$description="<P>Add a pay adjustment for $vendorname.</P>\n";
$additionalinfo="<P>Negative pay adjustment means we are giving\n";
$additionalinfo.="them money, or less that they have to pay.\n";
$additionalinfo.="Positive means more money that they owe us.</P>\n";
$additionalinfo.="<P>Please make sure the reason is entered.</P>\n";
$conid=$_SESSION['conid'];

/* When this file is retuned to, with the payment adjustment, update
   the VendorAnnualInfo table appropriately.  Require _something_ in
   the payment adjustment reason column.
*/

// Update only if the right permissions.  Check the validity of the submissions.
if (($_POST['submit']=="Update") and (may_I("Maint") or may_I("ConChair") or may_I("SuperVendor"))) {
  if ((!empty($_POST['payadj'])) and (is_numeric($_POST['payadj']))) {
    if ((empty($_POST['payreason'])) and ($_POST['payadj']!="0.00")) {
      $message_error.="Please supply the reason for the payment adjustment.";
    } else {
      // Set the match_string to the vendor and the conid
      $match_string="badgeid=".$_POST['vendorid']." AND conid=$conid";
      $set_array=array('vendorpayadj="'.$_POST['payadj'].'"',
		       'vendorpayadjdesc="'.mysql_real_escape_string(stripslashes($_POST['payreason'])).'"');
      $message.=update_table_element_extended_match($link, $title, "VendorAnnualInfo", $set_array, $match_string);
    }
  } else {
    $message_error.="Please supply the amount of the payment adjustment as numbers only.\n";
  }
}

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Vendor Statuses set only by ConChair, SuperVendor, or the Janitor
if ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor"))) {

  // Again possible for the SuperVendor to set someone up.
  if (may_I('SuperVendor')) {
    //Choose the individual from the database
    select_participant($vendorid, 'VENDORCURRENT', "VendorPayAdj.php");
  }

  echo "<P><A HREF=\"".$_SESSION['return_to_page']."\">Return to report</A></P>\n";

  // If there is no vendor selected, exit here.  This might want to have the vendor pulldowns instead.
  if (empty($vendorid)) {
    correct_footer();
    exit();
  }

$queryPayAdj=<<<EOD
SELECT
    vendorpayadj,
    vendorpayadjdesc
  FROM
      VendorAnnualInfo
  WHERE
    conid=$conid AND
    badgeid=$vendorid
EOD;

// Get the information
list($payadjrows,$payadjheader_array,$payadj_array)=queryreport($queryPayAdj,$link,$title,$description,0);

// If there are more than one entries in the VendorAnnualInfo table
// for this vendor for this year, there are problems.
if ($payadjrows > 1) {
  $message_error="Too many rows returned: $payadjrows\n";
  RenderError($title,$message_error);
}

  echo "<br /><hr>\n";
  echo "<FORM name=\"updatepayadj\" class=\"bb\" method=POST action=\"VendorPayAdj.php\">\n";
  echo "  <INPUT type=\"hidden\" name=\"vendorid\" value=\"$vendorid\">\n";
  echo "  <LABEL for=\"payadj\">Payment Adjustment: </LABEL>\n";
  echo "  <INPUT type=\"text\" name=\"payadj\" id=\"payadj\" size=\"8\" value=\"" . $payadj_array[1]['vendorpayadj'] . "\">\n";
  echo "  <LABEL for\"payreason\">Reason:</LABEL>\n";
  echo "  <INPUT type=\"text\" name=\"payreason\" id=\"payreason\" size=\"50\" value=\"" . $payadj_array[1]['vendorpayadjdesc'] . "\">\n";
  echo "  <P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Update\">Update</BUTTON></P>\n</FORM>\n";
} else {
  echo "<P>We're sorry, you do not have sufficient permissions to see this page.</P>\n";
}

// Close the page
correct_footer();
?>
