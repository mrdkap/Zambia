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

// Update only if the right permissions.
if (may_I("Maint") or may_I("ConChair") or may_I("SuperVendor")) {

  // Has Space (before update)
  $querySpaceHasInfo=<<<EOD
SELECT
    vendorspacecount,
    vendorspaceid
  FROM
      VendorHasSpace
    JOIN VendorSpace USING (vendorspaceid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
EOD;

  // Get the information
  list($spaceirows,$spaceiheader_array,$spacei_array)=queryreport($querySpaceHasInfo,$link,$title,$description,0);

  // Make sure the post values are all (only) numbers, and the submit is Assign Space
  if (($_POST['submit']=='Assign Space') AND is_numeric($_POST['vendorid']) AND is_numeric($_POST['spaceid']) AND is_numeric($_POST['spacecount'])) {
    // Check the incoming data against what has already been set.
    $worked_p=0;
    for ($i=1; $i<=$spaceirows; $i++) {
      if ($_POST['spaceid'] == $spacei_array[$i]['vendorspaceid']) {

	// the post matched an already assigned vendorspaceid
	$worked_p++;

	// Set the match string for badgeid and vendorspaceid
	$match_string="badgeid=".$_POST['vendorid']." AND vendorspaceid=".$spacei_array[$i]['vendorspaceid'];

	// Either delete the entry, or update the entry, depending on the spacecount
	if ($_POST['spacecount'] == 0) { // delete the entry
	  $message.=delete_table_element($link, $title, "VendorHasSpace", $match_string);
	} elseif ($_POST['spacecount'] != $spacei_array[$i]['vendorspacecount']) { // update the count
	  $set_array=array("vendorspacecount=".$_POST['spacecount']);
	  $message.=update_table_element_extended_match($link, $title, "VendorHasSpace", $set_array, $match_string);
	}
      }
    }

    // Not already in the databasse
    if ($worked_p == 0) {
      $element_array=array('badgeid','vendorspaceid','vendorspacecount');
      $value_array=array($_POST['vendorid'],$_POST['spaceid'],$_POST['spacecount']);
      $message.=submit_table_element($link, $title, "VendorHasSpace", $element_array, $value_array);
    }
  }

  // Make sure the post values are all (only) numbers, and the submit is Assign Location
  if (($_POST['submit']=='Assign Location') AND is_numeric($_POST['locationid']) AND is_numeric($_POST['vendorid'])) {

    // Has Location (before update)
    $queryLocHas=<<<EOD
SELECT
    locationid,
    booth,
    badgeid
  FROM
      VendorHasLoc
    JOIN Location USING (locationid)
  WHERE
    conid=$conid
EOD;

    // Get the information
    list($lochasrows,$lochasheader_array,$lochas_array)=queryreport($queryLocHas,$link,$title,$description,0);
    $worked_p=0;
    // Walk the list of already assigned locations
    for ($i=1; $i<=$lochasrows; $i++) {
      if (($_POST['locationid'] == $lochas_array[$i]['locationid']) AND
	  ($_POST['booth'] == $lochas_array[$i]['booth'])) {

	// This has a match
	$worked_p++;

	// Check for already assigned to someone else, already assined to this someone, or removal
	if ($_POST['vendorid'] != $lochas_array[$i]['badgeid']) {
	  $message_error.="This area is already assigned.  Please check your assignments and try again.\n";
	} elseif ($_POST['remove']=="remove") {
	  $match_string="badgeid=".$_POST['vendorid']." AND locationid=".$_POST['locationid']." AND booth='".$_POST['booth']."'";
	  $message.=delete_table_element($link, $title, "VendorHasLoc", $match_string);
	} else {
	  $message.="This area is already assigned to this vendor.\n";
	}
      }
    }

    // Not already in the database
    if ($worked_p == 0) {
      $element_array=array('badgeid','locationid','booth');
      $value_array=array($_POST['vendorid'],$_POST['locationid'],$_POST['booth']);
      $message.=submit_table_element($link, $title, "VendorHasLoc", $element_array, $value_array);
    }
  }
}

// Has Space (after update)
$querySpaceHasInfo=<<<EOD
SELECT
    vendorspacecount,
    vendorspaceid
  FROM
      VendorHasSpace
    JOIN VendorSpace USING (vendorspaceid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
EOD;

// Get the information
list($spaceirows,$spaceiheader_array,$spacei_array)=queryreport($querySpaceHasInfo,$link,$title,$description,0);

// Has Location (after update)
$queryLocHas=<<<EOD
SELECT
    locationid,
    booth
  FROM
      VendorHasLoc
    JOIN Location USING (locationid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
EOD;

// Get the information
list($lochasrows,$lochasheader_array,$lochas_array)=queryreport($queryLocHas,$link,$title,$description,0);

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Vendor Statuses set only by ConChair, SuperVendor, or the Janitor
if ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor"))) {

  // Again possible for the SuperVendor to set someone up.
  if (may_I('SuperVendor')) {
    //Choose the individual from the database
    select_participant($vendorid, 'VENDORCURRENT', "VendorAssignSpace.php");
  }

  // If there is no vendor selected, exit here.  This might want to have the vendor pulldowns instead.
  if (empty($vendorid)) {
    correct_footer();
    exit();
  }

  // Wanted Space
  $querySpaceType=<<<EOD
SELECT
    basevendorspacename AS "Booth Wanted",
    vendorprefspacerank as Rank
  FROM
      VendorPrefSpace
    JOIN VendorSpace USING (vendorspaceid)
    JOIN BaseVendorSpace USING (basevendorspaceid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
  ORDER BY
    vendorprefspacerank
EOD;

  // Get the information
  list($spacetrows,$spacetheader_array,$spacet_array)=queryreport($querySpaceType,$link,$title,$description,0);

  // Notes
  $queryVendorNotes=<<<EOD
SELECT
    vendornotes as Notes
  FROM
      VendorAnnualInfo
  WHERE
    conid=$conid AND
    badgeid=$vendorid
EOD;

  // Get the information
  list($notesrows,$notesheader_array,$notes_array)=queryreport($queryVendorNotes,$link,$title,$description,0);

  // Pay Adjustments
  $queryVendorPayAdj=<<<EOD
SELECT
    concat("<A HREF=VendorPayAdj.php?vendorid=$vendorid>",vendorpayadj,"</A>") AS Amount,
    vendorpayadjdesc AS "Payment Adjustment Reason"
  FROM
      VendorAnnualInfo
  WHERE
    conid=$conid AND
    badgeid=$vendorid
EOD;

  // Get the information
  list($payadjrows,$payadjheader_array,$payadj_array)=queryreport($queryVendorPayAdj,$link,$title,$description,0);

  // Features
  $queryFeatureList=<<<EOD
SELECT
    basevendorfeaturename AS "Amenity Wanted",
    vendorfeaturecount as "How Many"
  FROM
      VendorHasFeature
    JOIN VendorFeature USING (vendorfeatureid)
    JOIN BaseVendorFeature USING (basevendorfeatureid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
  ORDER BY
    display_order
EOD;

  // Get the information
  list($featurerows,$featureheader_array,$feature_array)=queryreport($queryFeatureList,$link,$title,$description,0);

  // Sponsor
  $querySponsorLevel=<<<EOD
SELECT
    basesponsorlevelname AS "Sponsor Level",
    sponsorlevelcount as "How Many"
  FROM
      VendorHasSponsorLevel
    JOIN SponsorLevel USING (sponsorlevelid)
    JOIN BaseSponsorLevel USING (basesponsorlevelid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
  ORDER BY
    display_order
EOD;

  // Get the information
  list($sponsorrows,$sponsorheader_array,$sponsor_array)=queryreport($querySponsorLevel,$link,$title,$description,0);

  // Has Space (similar to above, but for different purposes
  $querySpaceHas=<<<EOD
SELECT
    basevendorspacename AS "Currently Assigned Space",
    vendorspacecount AS "How many of this type"
  FROM
      VendorHasSpace
    JOIN VendorSpace USING (vendorspaceid)
    JOIN BaseVendorSpace USING (basevendorspaceid)
  WHERE
    conid=$conid AND
    badgeid=$vendorid
EOD;

  // Get the information
  list($spacerows,$spaceheader_array,$space_array)=queryreport($querySpaceHas,$link,$title,$description,0);

  // Space choice
  $querySpace=<<<EOD
SELECT
    vendorspaceid,
    concat(basevendorspacename, " ", if((vendorspacenotes IS NOT NULL) AND (vendorspacenotes != ""),vendorspacenotes,""))
  FROM
      VendorSpace
    JOIN BaseVendorSpace USING (basevendorspaceid)
  WHERE
    conid=$conid
  ORDER BY
    display_order
EOD;

  // Location types
  $queryLocation=<<<EOD
SELECT
    locationid,
    concat(baselocbuildingname, " ",
	   baselocfloorname, " ",
	   baselocroomname, " ",
	   if(baselocsubroomid != 0,concat(baselocsubroomname, " "),""),
	   if(locationheight != "",concat("Height: ", locationheight, " "),""),
	   if(locationdimensions != "",concat("Dimensions: ", locationdimensions, " "),""),
	   if(locationarea != "",concat("Area: ", locationarea, " "),""),
	   if(locationnotes != "",concat("Notes: ", locationnotes, " "),"")) AS LocName
  FROM
      Location
    JOIN BaseLocBuilding USING (baselocbuildingid)
    JOIN BaseLocFloor USING (baselocfloorid)
    JOIN BaseLocRoom USING (baselocroomid)
    JOIN Divisions USING (divisionid)
    LEFT JOIN BaseLocSubRoom USING (baselocsubroomid)
  WHERE
    conid=$conid AND
    divisionname in ("Vendor")
EOD;

  // Get the information
  list($loctmprows,$loctmpheader_array,$loctmp_array)=queryreport($queryLocation,$link,$title,$description,0);

  // Pretty up the existing locations.
  for ($i=1; $i<=$loctmprows; $i++) {
    $locmap_array[$loctmp_array[$i]['locationid']]=$loctmp_array[$i]['LocName'];
  }

  $locshowheader_array=array("Location","Booth");
  for ($i=1; $i<=$lochasrows; $i++) {
    $locshow_array[$i]['Location']=$locmap_array[$lochas_array[$i]['locationid']];
    $locshow_array[$i]['Booth']=$lochas_array[$i]['booth'];
  }

  // First form -- space type
  echo "<br /><hr>\n";
  echo "<P>Listed below is the space requested, any notes from the vendor,\n";
  echo "any pay adjustments to take into consideration, the amenities\n";
  echo "requested, if they are a sponsor, and to what level, and finally\n";
  echo "any already actually assigned spaces.</P>\n";
  echo "To unassign a space, set the count to 0 (Zero)</P>\n";

  // Requested space
  if ($spacetrows == 0) {
    echo "<P><strong>No space specifically requested.</strong></P>\n";
  } else {
    echo renderhtmlreport(1,$spacetrows,$spacetheader_array,$spacet_array);
  }

  // Notes
  if ($notesrows != 0) {
    echo renderhtmlreport(1,$notesrows,$notesheader_array,$notes_array);
  }

  // Payment Adjustment
  if ($payadjrows != 0) {
    echo renderhtmlreport(1,$payadjrows,$payadjheader_array,$payadj_array);
  }

  // Requested features only if such exists
  if ($featurerows != 0) {
    echo renderhtmlreport(1,$featurerows,$featureheader_array,$feature_array);
  }

  // Sponsor level only if such exists
  if ($sponsorrows != 0) {
    echo renderhtmlreport(1,$sponsorrows,$sponsorheader_array,$sponsor_array);
  }

  // Assigned spaces
  if ($spacerows != 0) {
    echo renderhtmlreport(1,$spacerows,$spaceheader_array,$space_array);
  }

  echo "<FORM name=\"updatevendorspacetype\" class=\"bb\" method=POST action=\"VendorAssignSpace.php\">\n";
  echo "  <INPUT type=\"hidden\" name=\"vendorid\" value=\"$vendorid\">\n";
  echo "  <SPAN><LABEL for=\"spaceid\">Space: </LABEL>\n";
  echo "    <SELECT name=\"spaceid\">\n";
  echo populate_select_from_query_inline($querySpace, $spacei_array[1]['vendorspaceid'], "SELECT", false);
  echo "    </SELECT>&nbsp;&nbsp;</SPAN>\n";
  echo "  <LABEL for\"spacecount\">Count:</LABEL>\n";
  echo "  <INPUT type=\"text\" name=\"spacecount\" id=\"spacecount\" value=\"" . $spacei_array[1]['vendorspacecount'] . "\">\n";

  // Close the form
  echo "  <P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Assign Space\">Update</BUTTON></P>\n</FORM>\n";

  // Second form -- actual location.  Only if there is a space type selected.
  if ($spaceirows != 0) {
    echo "<br /><hr>\n";
    echo "<P>If you need to create the spaces you are assigning (or create more spaces),\n";
    echo "go <A HREF=\"VendorSetupLocation.php\">here</A>.</P>\n";
    echo "<P>To remove an assignment, click the \"Remove:\" checkbox at the end of the row.</P>\n";
    echo "<P>Booths should just be a number in most cases.  If the room was broken into\n";
    echo "sub-rooms (rows or clusters or the like) please set them in the\n";
    echo "<A HREF=\"VendorSetupLocation.php\">Location Setup</A> form.  The \"Key\" set\n";
    echo "or sub-room name in that form will make the labeling go properly.</P>\n";

    // Assigned Locations
    if ($lochasrows != 0) {
      echo renderhtmlreport(1,$lochasrows,$locshowheader_array,$locshow_array);
    }

    // Form to assign a location
    echo "<FORM name=\"updatevendorspaceloc\" class=\"bb\" method=POST action=\"VendorAssignSpace.php\">\n";
    echo "  <INPUT type=\"hidden\" name=\"vendorid\" value=\"$vendorid\">\n";
    echo "  <SPAN><LABEL for=\"locationid\">Space: </LABEL>\n";
    echo "    <SELECT name=\"locationid\">\n";
    echo populate_select_from_query_inline($queryLocation, $lochas_array[1]['locationid'], "SELECT", true);
    echo "    </SELECT>\n  </SPAN>\n";
    echo "  <LABEL for\"booth\">Booth:</LABEL>\n";
    echo "  <INPUT type=\"text\" name=\"booth\" id=\"booth\" value=\"" . $lochas_array[1]['booth'] . "\">\n";
    echo "  <LABEL for\"remove\">Remove:</LABEL>\n";
    echo "  <INPUT type=\"checkbox\" name=\"remove\" id=\"remove\" value=\"remove\">\n";

    // Close the form
    echo "  <P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Assign Location\">Update</BUTTON></P>\n</FORM>\n";
  }
}

// Close the page
correct_footer();
?>
