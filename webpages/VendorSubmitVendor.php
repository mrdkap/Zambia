<?php
require_once('VendorCommonCode.php');
require_once('Vendor_FNC.php');
global $link, $message, $message_error;

// LOCALIZATIONS
$title="Update Vendor Business Information";
$description="<P>Password update not necessary for submission.</P>\n";
$additionalinfo="<P>Please make sure all your information is valid, there are no double checks.\n";
$additionalinfo.="If they are not valid they are not going to resolve properly the chance that we might\n";
$additionalinfo.="see you at this or future events decreases exponentially.</P>\n";
$_SESSION['return_to_page']='VendorWelcome.php';
$conid=$_SESSION['conid'];

// This might be varied, below.
$badgeid=$_SESSION['badgeid'];

if ($badgeid == "100") {
  // Not signing up as badgeid 100 (brainstorm) because, people are clickity.
  $description="<P>Returning Vendor Application/Update Form.</P>";
  $additionalinfo ="<P>If you are a returning vendor, please\n";
  $additionalinfo.="<A HREF=\"login.php?newconid=$conid\">log in</A>\n";
  $additionalinfo.="with your email address and password.</P>\n";
  $additionalinfo.="<P>If you wish to apply to be a new vendor, please\n";
  $additionalinfo.="<A HREF=\"VendorWelcome.php\">Return</A> and fill\n";
  $additionalinfo.="out the form there.</P>\n";

  topofpagereport($title,$description,$additionalinfo,$message,$message_error);

  correct_footer();
  exit ();
}

// This may allow the SuperVendor to apply for someone.
if (may_I('SuperVendor')) {
  // Collaps the three choices into one
  if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
  if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
  if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}

  if (isset($_POST["partid"])) {
    $badgeid=$_POST["partid"];
  } elseif (isset($_GET["partid"])) {
    $badgeid=$_GET["partid"];
  }
}

// Begin the display
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Again possible for the SuperVendor to set someone up.
if (may_I('SuperVendor')) {
  //Choose the individual from the database
  select_participant($badgeid, '', "VendorApply.php");
  echo "\n<hr>\n";
  echo "<P>Update for: ($badgeid) $pubsname</P>\n";
}

// Pull the VendorUpdate verbiage, that should be all that is needed.
$verbiage=get_verbiage("VendorUpdate");
if ($verbiage != "") {
  echo "<FORM name=\"vendorform\" action=\"renderVendorWelcome.php\" method=POST>\n";
  echo eval('?>' . $verbiage);
  echo "  <P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Updated Information\">Update</BUTTON></P>\n";
  echo "</FORM>\n";
} else {
  echo "<P>The Business Update process is not yet set up for this year, please stay tuned.</P>\n";
}

correct_footer();

?>
