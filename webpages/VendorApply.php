<?php
require_once('VendorCommonCode.php');
require_once('Vendor_FNC.php');
global $link, $message, $message_error;

// LOCALIZATIONS
$title="Vendor Application";
$description="<P>Apply (or update your application) for the ". $_SESSION['connamelong'] . "</P>";
$additionalinfo="<P>Vendor FAQ: ";
$additionalinfo.="<A HREF=\"http://fetishflea.com/index.php?page=vending-community-tables\">";
$additionalinfo.="http://fetishflea.com/index.php?page=vending-community-tables</A></P>\n";
$_SESSION['return_to_page']='VendorApply.php';
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
  // Collaps the two choices into one
  if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}
  if ($_POST["partide"]!=0) {$_POST["partid"]=$_POST["partide"];}

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

list($vstatusrows,$vstatusheader_array,$vstatus_array)=queryreport($queryVendorStatus, $link, $title, $description, 0);

if ($vstatusrows==1) {
  $vstatusid=$vstatus_array[1]['vendorstatustypeid'];
  $vstatusname=$vstatus_array[1]['vendorstatustypename'];
}

// Price total
$total=0;

// Vendor Space Name
$queryVendorSpace=<<<EOD
SELECT
    basevendorspacename,
    vendorspacecount,
    vendorspacecount*vendorspaceprice AS vendorspacecost
  FROM
      VendorHasSpace
    JOIN VendorSpace USING (vendorspaceid)
    JOIN BaseVendorSpace USING (basevendorspaceid)
  WHERE
    conid=$conid AND
    badgeid=$badgeid
EOD;

list($vspacerows,$vspaceheader_array,$vspace_array)=queryreport($queryVendorSpace, $link, $title, $description, 0);

$vspace="";
for ($i=1; $i<=$vspacerows; $i++) {
  $vspace.=$vspace_array[$i]['vendorspacecount'] . " " . $vspace_array[$i]['basevendorspacename'] . ", ";
  $total+=$vspace_array[$i]['vendorspacecost'];
}

// remove the trailing comma and space
$vspace=rtrim($vspace, ', ');

$queryVendorLoc=<<<EOD
SELECT
    if(booth!="",
       concat(baselocroomname, " ", locationkey, booth),
       if(baselocsubroomname!="",
          concat(baselocroomname, " ", locationkey, baselocsubroomname),
          concat(locationkey, baselocroomname))) AS actualvendorloc
  FROM
      VendorHasLoc
    JOIN Location USING (locationid)
    JOIN BaseLocSubRoom USING (baselocsubroomid)
    JOIN BaseLocRoom USING (baselocroomid)
  WHERE
    conid=$conid AND
    badgeid=$badgeid
EOD;

list($vlocrows,$vlocheader_array,$vloc_array)=queryreport($queryVendorLoc, $link, $title, $description, 0);

$vloc="";
for ($i=1; $i<=$vlocrows; $i++) {
  $vloc.=$vloc_array[$i]['actualvendorloc'] . ", ";
}

// remove the trailing comma and space
$vloc=rtrim($vloc, ', ');

// Get Features
$queryFeature=<<<EOD
SELECT
    vendorfeaturecount*vendorfeatureprice AS vendorfeaturecost
  FROM
      VendorHasFeature
    JOIN VendorFeature USING (vendorfeatureid)
    JOIN BaseVendorFeature USING (basevendorfeatureid)
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

list($featurerows,$featureheader_array,$feature_array)=queryreport($queryFeature,$link,$title,$description,0);

for ($i=1; $i<=$featurerows; $i++) {
  $total+=$feature_array[$i]['vendorfeaturecost'];
}

// Get PrintAds
$queryPrintAd=<<<EOD
SELECT
    printadcount*printadprice AS printadcost
  FROM
      VendorHasPrintAd
    JOIN PrintAd USING (printadid)
    JOIN BasePrintAd USING (baseprintadid)
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

list($printadrows,$printadheader_array,$printad_array)=queryreport($queryPrintAd,$link,$title,$description,0);

for ($i=1; $i<=$printadrows; $i++) {
  $total+=$printad_array[$i]['printadcost'];
}

// Get Digital Ads
$queryDigitalAd=<<<EOD
SELECT
    digitaladcount*digitaladprice AS digitaladcost
  FROM
      VendorHasDigitalAd
    JOIN DigitalAd USING (digitaladid)
    JOIN BaseDigitalAd USING (basedigitaladid)
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

list($digitaladrows,$digitaladheader_array,$digitalad_array)=queryreport($queryDigitalAd,$link,$title,$description,0);

for ($i=1; $i<=$digitaladrows; $i++) {
  $total+=$digitalad_array[$i]['digitaladcost'];
}

// Get Sponsor Levels
$querySponsorLevel=<<<EOD
SELECT
    sponsorlevelcount*sponsorlevelprice AS sponsorlevelcost
  FROM
      VendorHasSponsorLevel
    JOIN SponsorLevel USING (sponsorlevelid)
    JOIN BaseSponsorLevel USING (basesponsorlevelid)
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

list($sponsorlevelrows,$sponsorlevelheader_array,$sponsorlevel_array)=queryreport($querySponsorLevel,$link,$title,$description,0);

for ($i=1; $i<=$sponsorlevelrows; $i++) {
  $total+=$sponsorlevel_array[$i]['sponsorlevelcost'];
}

// Get Payment Adjustment
$queryPayAdj=<<<EOD
SELECT
    vendorpayadj
  FROM
      VendorAnnualInfo
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

list($payadjrows,$payadjheader_array,$payadj_array)=queryreport($queryPayAdj,$link,$title,$description,0);

for ($i=1; $i<=$payadjrows; $i++) {
  $total+=$payadj_array[$i]['vendorpayadj'];
}

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Again possible for the SuperVendor to set someone up.
if (may_I('SuperVendor')) {
  //Choose the individual from the database
  select_participant($badgeid, 'VENDOR', "VendorApply.php");
  echo "\n<hr>\n";
  echo "<P>Update for: $badgeid</P>\n";
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

// booth type
if (!empty($vspace)) {
  echo "Your space will be: $vspace<br />\n";
} else {
  echo "Your space has not yet been decided.<br />\n";
}

// booth location
if (!empty($vloc)) {
  echo "Your location is: $vloc<br />\n";
} else {
  echo "Your location has not yet been decided.<br />\n";
}

// Pretty sure we want this, can be done off of vstatusname probably
if ($vstatusname=="Invoiced") {
  echo "Your current total is: $" . $total . ".<br>\n";
  echo "Please <A HREF=\"VendorWelcome.php\">Pay Here</A>.</P>\n";
} elseif (($vstatusname=="Paid") or ($vstatusname=="Accepted")) {
  echo "Thank you for paying.  We are looking forward to seeing you.</P>\n";
} else {
  echo "Should you be accepted for the event, payment will be expected promptly.</P>\n";
}

$verbiage=get_verbiage("VendorApply");
if ($verbiage != "") {
  echo "<FORM name=\"vendorform\" action=\"renderVendorWelcome.php\" method=POST>\n";
  echo eval('?>' . $verbiage);
  echo "  <input type=\"hidden\" name=\"badgeid\" value=\"$badgeid\" id=\"badgeid\">\n";
  echo "  <P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Updated Application\">Update</BUTTON></P>\n";
  echo "</FORM>\n";
} else {
  echo "<P>The application process is not yet set up for this year, please stay tuned.</P>\n";
}

correct_footer();
?>
