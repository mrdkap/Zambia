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
$title="Space assignment for $vendorname";
$description="<P>Assign $vendorname their space.</P>\n";
$additionalinfo="<P>This is in two stages.  The first, for billing\n";
$additionalinfo.="purposes establishes what (general) space they have\n";
$additionalinfo.="(single, double, triple, end-cap, etc.  The second\n";
$additionalinfo.="is their specific space assignment.  Both might need\n";
$additionalinfo.="to be done at once, depending on the process.</P>\n";
$conid=$_SESSION['conid'];

/* When this file is retuned to, with the various location information
   bits set, put them into the database, so the vendor can be billed,
   or mapped.
*/
if (($_POST['update']=='Yes') and ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor")))) {
}


// Change this to location information
// Fetch the Vendor Location after possible update so any updates will be incorporated.
if (!empty($vendorid)) {
  $queryVspace=<<<EOD
SELECT
    basevendorspacename,
    concat("No space picked") AS Location
  FROM
      VendorHasSpace
    JOIN VendorSpace USING (vendorspaceid)
    JOIN BaseVendorSpace USING (basevendorspaceid)
  WHERE
    badgeid=$vendorid
EOD;

  list($vspacerows,$vspaceheader_array,$vspace_array)=queryreport($queryVspace,$link,$title,$description,0);
  if ($vspacerows == 0) {
    $vspace_array[1]['basevendorspacename']="This vendor has yet to be assigned a space type.";
    $vspace_array[1]['Location']="No space picked.";
  }
}

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

  // Again possible for the SuperVendor to set someone up.
  if (may_I('SuperVendor')) {
    //Choose the individual from the database
    select_participant($vendorid, 'VENDORCURRENT', "VendorAdminState.php");
  }

  // If there is no vendor selected, exit here.  This might want to have the vendor pulldowns instead.
  if (empty($vendorid)) {
    correct_footer();
    exit();
  }

  $querySpaceType=<<<EOD
SELECT
    basevendorspacename AS "Booth Wanted",
    vendorprefspacerank as Rank,
    vendornotes as Notes
  FROM
      VendorPrefSpace
    JOIN VendorSpace USING (vendorspaceid)
    JOIN BaseVendorSpace USING (basevendorspaceid)
    JOIN VendorAnnualInfo USING (badgeid,conid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
  ORDER BY
    vendorprefspacerank
EOD;

// Get the information
list($spacetrows,$spacetheader_array,$spacet_array)=queryreport($querySpaceType,$link,$title,$description,0);

  // First form -- space type
  echo "<br /><hr>\n";
  echo "<P>Something here to do ... something to assign the space.</P>\n";
  echo "<P>We probably need the list of requested spaces, the number of booths requested, so the notes, if they are a sponsor (so get premium spaces), and the features (so we can see if they asked for anything special).</P>\n";
  echo renderhtmlreport(1,$spacetrows,$spacetheader_array,$spacet_array);
  echo "<FORM name=\"updatevendorspacetype\" class=\"bb\" method=POST action=\"VendorAssignSpace.php\">\n";
  // Close the form
  echo "</FORM>";

  // Second form -- actual location
  echo "<br /><hr>\n";
  echo "<P>If you need to create the spaces you are assigning (or create more spaces), go <A HREF=\"VendorSetupLocation.php\">here</A>.</P>\n";
  echo "<FORM name=\"updatevendorspaceloc\" class=\"bb\" method=POST action=\"VendorAssignSpace.php\">\n";
  echo "<P>Something here to do ... something to assign the actual location.<P>\n";
  // Close the form
  echo "</FORM>";
}

// Close the page
correct_footer();
?>
