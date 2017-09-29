<?php
require_once('VendorCommonCode.php');
global $link, $message, $message_error;

// LOCALIZATIONS
$title="Vendor Application";
$description="<P>Apply (or update your application) for the ". $_SESSION['connamelong'] . "</P>";
$additionalinfo="";
$_SESSION['return_to_page']='VendorApply.php';
$badgeid=$_SESSION['badgeid'];
$conid=$_SESSION['conid'];

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

// Again possible for the SuperVendor to set someone up.
if (may_I('SuperVendor')) {
  //Choose the individual from the database
  select_participant($badgeid, '', "VendorApply.php");
  echo "\n<hr>\n";
  echo "<P>Update for: ($badgeid) $pubsname</P>\n";
}

?>

<P>Welcome!  The below is where you will apply to be a vendor at this
   event, or update your requirements.  During different phases of this
   process, you might or might not be able to change what you have bid.
  If there is something you need to change, but cannot change here,
  please, use the email us at <A HREF="mailto: <?php echo $_SESSION['vendoremail'] ?>">
  <?php echo $_SESSION['vendoremail'] ?></A> post-haste, to see if your adjustments
  can be made.</P>

<P>Your current status is: 

<?php
echo $vstatusname."<br />\n";

// Somehow, booth size
if (!empty($vspaceactual)) {
  echo "Your space will be: $vspaceactual<br />\n";
} else {
  echo "Your space has not yet been decided.<br />\n";
}

// Not sure we want this, but probably can be done off of vstatusname
if ($session['total']!=0) {
  echo "Your current total is: $".$session['total'].".<br>\n";
}

// Somehow, location
if (!empty($vlocation)) {
  echo "Your location is: $vlocation<br />\n";
} else {
  echo "Your location has not yet been decided.<br />\n";
}

// Pretty sure we want this, can be done off of vstatusname probably
if ($session['statusname']=="Vendor Approved") {
  echo "Please <A HREF=\"VendorInvoice.php\">Pay Here</A>.</P>\n";
} elseif ($session['statusname']=="Vendor Paid") {
  echo "Thank you for paying.  We are looking forward to seeing you.</P>\n";
} else {
  echo "Should you be accepted for the event, payment will be expected promptly.</P>\n";
}

$verbiage=get_verbiage("VendorApply");
if ($verbiage != "") {
  echo "<FORM name=\"vendorform\" action=\"renderVendorWelcome.php\" method=POST>\n";
  echo eval('?>' . $verbiage);
  echo "  <P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Updated Application\">Update</BUTTON></P>\n";
  echo "</FORM>\n";
} else {
  echo "<P>The application process is not yet set up for this year, please stay tuned.</P>\n";
}

correct_footer();
?>
