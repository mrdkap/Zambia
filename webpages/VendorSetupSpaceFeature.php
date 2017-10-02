<?php
require_once('StaffCommonCode.php');
global $link, $message, $message_error;

// LOCALIZATIONS
$title="Create/Update Vendor Spaces and Amenities";
$description="<P>Create and update this years instances of the Vendor Spaces and Amenities.</P>\n";
$additionalinfo="<P>Each section has it's own update button.\n";
$additionalinfo.="Please do not try to update more than one section at once, ";
$additionalinfo.="it will not (necessarily) be heeded properly.</P>\n";
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// Limit the ability to change things
if ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor"))) {
  $additionalinfo.="<P>This page is limited to a few select people who can change it, ";
  $additionalinfo.="basically the Con Chair and the Super Vendor folks.</P>\n";
  $limitedview="F";
} else {
  $additionalinfo.="<P>You can view the settings but you do not have sufficient ";
  $additionalinfo.="permissions to change them.  If you think this is in error, talk ";
  $additionalinfo.="to someone in charge.</P>\n";
  $limitedview="T";
}

// Show history
$hist="F";
if ((!empty($_GET['history'])) AND ($_GET['history'] == "Y")) {
  $hist="T";
  $additionalinfo.="<P>To see this without the clutter of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupSpaceFeature.php>click here</A>.</P>\n";
} elseif ((!empty($_POST['history'])) AND ($_POST['history'] == "Y")) {
  $hist="T";
  $additionalinfo.="<P>To see this without the clutter of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupSpaceFeature.php>click here</A>.</P>\n";
} else {
  $additionalinfo.="<P>To see this with previous con information included, ";
  $additionalinfo.="<A HREF=VendorSetupSpaceFeature.php?history=Y>click here</A>.</P>\n";
}

// If the view is limited, then show previous years, and this years,
// otherwise just previous years in the history section.
$wherestring="WHERE conid!=$conid";
if ($limitedview == "T") {
  $hist="T";
  $wherestring="";
}

$option_array=array();
$option_array[1]="price";
$option_array[2]="max";
$option_array[3]="notes";
$option_array[4]="url";
$option_array[5]="display_order";

$typename=array();
$typename[1]="VendorFeature";
$typename[2]="VendorSpace";

$type=array();
for ($i=1; $i<=count($typename); $i++) {
  $type[$i]=strtolower($typename[$i]);
}

$workstring="";

// Iterate across the types
for ($l=1; $l<=count($type); $l++) {

  // Setup reocurring long concat strings
  $baseloopname="base" . $type[$l] . "name AS Name";
  $baseloopdesc="base" . $type[$l] . "desc AS Description";

  $loopfrom=$typename[$l];
  $baseloopfrom="Base" . $loopfrom;

  $typeid=$type[$l] . "id";
  $basetypeid="base" . $typeid;
  $typeid_option=$type[$l]."id_option";

  $looprows=$type[$l] . "rows";
  $loopheader=$type[$l] . "header_array";
  $loopa=$type[$l] . "_array";
  $blooprows="b" . $looprows;
  $bloopheader="b" . $loopheader;
  $bloopa="b" . $loopa;
  $hlooprows="h" . $looprows;
  $hloopheader="h" . $loopheader;
  $hloopa="h" . $loopa;

  // Need the set of options available to be able to create the rest block
  $optioncheck=array();
  $queryOptionLoop="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$loopfrom'";
  list($optionlooprow,$optionloopheader_array,$optionloop_array)=queryreport($queryOptionLoop,$link,$title,$description,0);
  for ($i=1; $i<=$optionlooprow; $i++) {
    $optioncheck[]=$optionloop_array[$i]['COLUMN_NAME'];
  }

  $baselooprest="";
  $looprest="";
  $histlooprest="";
  $firstrun=0;
  for ($j=1; $j<=count($option_array); $j++) {
    if ($option_array[$j]!="display_order") {
      $optionname=$type[$l] . $option_array[$j];
    } else {
      $optionname=$option_array[$j];
    }
    if (in_array ($optionname, $optioncheck)) {
      if ($firstrun > 0) {
	$baselooprest.=",\n";
	$looprest.=",\n";
	$histlooprest.=",\n";
      }
      $baselooprest.="    concat(\"<input type=\\\"text\\\" ";
      $baselooprest.="name=\\\"base" . $type[$l] . "[\",$basetypeid,\"][" . $option_array[$j] . "]\\\" ";
      $baselooprest.="id=\\\"base" . $type[$l] . "[\",$basetypeid,\"][" . $option_array[$j] . "]\\\" ";
      $baselooprest.="value=\\\"\\\">\") AS " . $option_array[$j];
      $looprest.="    concat(\"<input type=\\\"text\\\" ";
      $looprest.="name=\\\"" . $type[$l] . "[\",$typeid,\"][" . $option_array[$j] . "]\\\" ";
      $looprest.="id=\\\"" . $type[$l] . "[\",$typeid,\"][" . $option_array[$j] . "]\\\" ";
      $looprest.="value=\\\"\",if($optionname != '',$optionname,''),\"\\\"> <input type=\\\"hidden\\\" ";
      $looprest.="name=\\\"was" . $type[$l] . "[\",$typeid,\"][" . $option_array[$j] . "]\\\" ";
      $looprest.="id=\\\"was" . $type[$l] . "[\",$typeid,\"][" . $option_array[$j] . "]\\\" ";
      $looprest.="value=\\\"\",if($optionname != '',$optionname,''),\"\\\">\") AS " . $option_array[$j];
      $histlooprest.=$optionname . " AS " . $option_array[$j];
      $firstrun++;
    }
  }

  // Update States

  // Need the set of baseids to be able to walk the possibilities.
  $queryIDLoop="SELECT $basetypeid AS id FROM $baseloopfrom";
  list($idlooprow,$idloopheader_array,$idloop_array)=queryreport($queryIDLoop,$link,$title,$description,0);

  // New Element
  if ($_POST['submit'] == "Base " . $typename[$l]) {
    for ($i=1; $i<=$idlooprow; $i++) {
      $k=0;
      $element_array = array('base' . $type[$l] . 'id','conid');
      $value_array = array($idloop_array[$i]['id'], $_SESSION['conid']);
      for ($j=1; $j<=count($option_array); $j++) {
	if ($option_array[$j]!="display_order") {
	  $optionname=$type[$l] . $option_array[$j];
	} else {
	  $optionname=$option_array[$j];
	}
	if (!empty($_POST['base' . $type[$l]][$idloop_array[$i]['id']][$option_array[$j]])) {
	  $element_array[]=$optionname;
	  $value_array[]=$_POST['base' . $type[$l]][$idloop_array[$i]['id']][$option_array[$j]];
	  $k++;
	}
      }
      if ($k != 0) {
	$message.=submit_table_element($link, $title, $typename[$l], $element_array, $value_array);
      }
    }
  }

  // Update Element
  if ($_POST['submit'] == $typename[$l]) {
    foreach ($_POST[$type[$l]] as $$typeid => $typeid_option) {
      $k=0;
      $pairedvalue_array=array();
      for ($j=1; $j<=count($option_array); $j++) {
	if ($option_array[$j]!="display_order") {
	  $optionname=$type[$l] . $option_array[$j];
	} else {
	  $optionname=$option_array[$j];
	}
	$wasoption=$_POST['was' . $type[$l]][$$typeid][$option_array[$j]];
	$isoption=$typeid_option[$option_array[$j]];
	if (((!empty($wasoption)) or (!empty($isoption))) and ($wasoption != $isoption)) {
	  $pairedvalue_array[]="$optionname='".mysql_real_escape_string($isoption)."'";
	  $k++;
	}
      }
      if ($k != 0) {
	$message.=update_table_element($link, $title, $typename[$l], $pairedvalue_array, $typeid, $$typeid);
      }
    }
  }

  // The below is done after the post updates, so all updates are included in the fetch.

  // Fetch (current) state of missing types
  $queryBaseLoop=<<<EOD
SELECT
    $baseloopname,
    $baseloopdesc,
    $baselooprest
  FROM
      $baseloopfrom
  WHERE
    $basetypeid NOT IN (SELECT $basetypeid from $loopfrom where conid=$conid)

EOD;

  list($$blooprows,$$bloopheader,$$bloopa)=queryreport($queryBaseLoop,$link,$title,$description,0);
  if ($$blooprows==0) {
    $$bloopheader=array("All possible " . $typename[$l] . "s set up for this year.");
  }

  // Fetch (current) state of offered types
  $queryLoop=<<<EOD
SELECT
    $baseloopname,
    $baseloopdesc,
    $looprest
  FROM
      $loopfrom
    JOIN $baseloopfrom USING ($basetypeid)
  WHERE
    conid=$conid

EOD;

  list($$looprows,$$loopheader,$$loopa)=queryreport($queryLoop,$link,$title,$description,0);
  if ($$looprows==0) {
    $$loopheader=array("No " . $typename[$l] . "s set up yet for this year.");
  }

  // Fetch historical state of types for comparison
  $queryHistLoop=<<<EOD
SELECT
    conname AS Event,
    $baseloopname,
    $baseloopdesc,
    $histlooprest
  FROM
      $loopfrom
    JOIN $baseloopfrom USING ($basetypeid)
    JOIN ConInfo USING (conid)
  $wherestring
  ORDER BY
    conid

EOD;

  list($$hlooprows,$$hloopheader,$$hloopa)=queryreport($queryHistLoop,$link,$title,$description,0);
  if ($$hlooprows==0) {
    $$hloopheader=array("No previous " . $typename[$l] . " data available.");
  }

  // Collect output

  // if history is requested, display it
  if ($hist=="T") {
    $workstring.="<P>Previous con's " . $typename[$l] . "s</P>\n";
    $workstring.=renderhtmlreport(1,$$hlooprows,$$hloopheader,$$hloopa);
  }

  // Only show the change tables if permissions match
  if ($limitedview == "F") {
    // sets the price, notes, url, and display_order for each unselected type
    $workstring.="<FORM name=\"base " . $type[$l] . "form\" action=\"VendorSetupSpaceFeature.php\" method=POST>\n";
    $workstring.="<P>Set this con's " . $typename[$l] . "s</P>\n";

    // continue to include history if requested.
    if ($hist=="T") {
      $workstring.="<input type=\"hidden\" name\"history\" id=\"history\" value=\"Y\">\n";
    }

    // show table
    $workstring.=renderhtmlreport(1,$$blooprows,$$bloopheader,$$bloopa);
    $workstring.="<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Base " . $typename[$l] . "\">Update</BUTTON></P>\n";
    $workstring.="</FORM>\n";

    // shows the current price, max, notes, and display_order for selected Sponsor Levels
    $workstring.="<FORM name=\"" . $type[$l] . "form\" action=\"VendorSetupSpaceFeature.php\" method=POST>\n";
    $workstring.="<P>Update this con's " . $typename[$l] . "s</P>\n";

    // continue to include history if requested.
    if ($hist=="T") {
      $workstring.="<input type=\"hidden\" name\"history\" id=\"history\" value=\"Y\">\n";
    }

    // show table
    //$workstring.="<P>looprows = $looprows , value " . $$looprows . " :: loopheader = $loopheader , value " . print_r($$loopheader,true) . " :: loopa = $loopa , value " . print_r($$loopa,true) . "</P>\n";
    //$workstring.="<P>Loop Query = $queryLoop</P>\n";
    $workstring.=renderhtmlreport(1,$$looprows,$$loopheader,$$loopa);
    $workstring.="<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"" . $typename[$l] . "\">Update</BUTTON></P>\n";
    $workstring.="</FORM>\n";
  }
}

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

echo $workstring;

correct_footer();

/* Commented out the below, since I think the above suits, to match PubsSetupAds.php

// Need the set of basevendorfeatureids and basevendorspaceids to be able to walk the possibilities.
$queryBaseVendorFeature="SELECT basevendorfeatureid FROM BaseVendorFeature";
list($bvfeaturerows,$bvfeatureheader_array,$bvfeature_array)=queryreport($queryBaseVendorFeature,$link,$title,$description,0);
$queryBaseVendorSpace="SELECT basevendorspaceid FROM BaseVendorSpace";
list($bvspacerows,$bvspaceheader_array,$bvspace_array)=queryreport($queryBaseVendorSpace,$link,$title,$description,0);

$option_array=array();
$option_array[1]="price";
$option_array[2]="max";
$option_array[3]="notes";
$option_array[4]="display_order";

// Update state here.

// New Vendor Feature
if ($_POST['submit'] == "Base Vendor Feature") {
  for ($i=1; $i<=$bvfeaturerows; $i++) {
    $k=0;
    $element_array = array('basevendorfeatureid','conid');
    $value_array = array($i, $_SESSION['conid']);
    for ($j=1; $j<=count($option_array); $j++) {
      if ($option_array[$j]!="display_order") {
	$option="vendorfeature".$option_array[$j];
      } else {
	$option=$option_array[$j];
      }
      if (!empty($_POST['basevendorfeature'][$i][$option])) {
	$element_array[]=$option;
	$value_array[]=$_POST['basevendorfeature'][$i][$option];
	$k++;
      }
    }
    if ($k != 0) {
      $message.=submit_table_element($link, $title, "VendorFeature", $element_array, $value_array);
    }
  }
}

// Update Vendor Feature
if ($_POST['submit'] == "Vendor Feature") {
  foreach ($_POST['vendorfeature'] as $vendorfeatureid => $vendorfeatureid_option) {
    $k=0;
    $pairedvalue_array=array();
    for ($j=1; $j<=count($option_array); $j++) {
      if ($option_array[$j]!="display_order") {
	$option="vendorfeature".$option_array[$j];
      } else {
	$option=$option_array[$j];
      }
      $wasoption=$_POST['wasvendorfeature'][$vendorfeatureid][$option];
      $isoption=$vendorfeatureid_option[$option];
      if (((!empty($wasoption)) or (!empty($isoption))) and ($wasoption != $isoption)) {
	$pairedvalue_array[]="$option='".mysql_real_escape_string($isoption)."'";
	$k++;
      }
    }
    if ($k != 0) {
      $message.=update_table_element($link, $title, "VendorFeature", $pairedvalue_array, "vendorfeatureid", $vendorfeatureid);
    }
  }
}

// New Vendor Space
if ($_POST['submit'] == "Base Vendor Space") {
  for ($i=1; $i<=$bvspacerows; $i++) {
    $k=0;
    $element_array = array('basevendorspaceid','conid');
    $value_array = array($i, $_SESSION['conid']);
    for ($j=1; $j<=count($option_array); $j++) {
      if ($option_array[$j]!="display_order") {
	$option="vendorspace".$option_array[$j];
      } else {
	$option=$option_array[$j];
      }
      if (!empty($_POST['basevendorspace'][$i][$option])) {
	$element_array[]=$option;
	$value_array[]=$_POST['basevendorspace'][$i][$option];
	$k++;
      }
    }
    if ($k != 0) {
      $message.=submit_table_element($link, $title, "VendorSpace", $element_array, $value_array);
    }
  }
}

// Update Vendor Space
if ($_POST['submit'] == "Vendor Space") {
  foreach ($_POST['vendorspace'] as $vendorspaceid => $vendorspaceid_option) {
    $k=0;
    $pairedvalue_array=array();
    for ($j=1; $j<=count($option_array); $j++) {
      if ($option_array[$j]!="display_order") {
	$option="vendorspace".$option_array[$j];
      } else {
	$option=$option_array[$j];
      }
      $wasoption=$_POST['wasvendorspace'][$vendorspaceid][$option];
      $isoption=$vendorspaceid_option[$option];
      if (((!empty($wasoption)) or (!empty($isoption))) and ($wasoption != $isoption)) {
	$pairedvalue_array[]="$option='".mysql_real_escape_string($isoption)."'";
	$k++;
      }
    }
    if ($k != 0) {
      $message.=update_table_element($link, $title, "VendorSpace", $pairedvalue_array, "vendorspaceid", $vendorspaceid);
    }
  }
}

// The below is done after the post updates, so all updates are included in the fetch.

// Fetch (current) state of missing features
$queryBaseFeature=<<<EOD
SELECT
    basevendorfeaturename AS Name,
    basevendorfeaturedesc AS Description,
    concat("<input type=\"text\"
      name=\"basevendorfeature[",basevendorfeatureid,"][vendorfeatureprice]\"
      id=\"basevendorfeature[",basevendorfeatureid,"][vendorfeatureprice]\"
      value=\"\">\n") AS Price,
    concat("<input type=\"text\"
      name=\"basevendorfeature[",basevendorfeatureid,"][vendorfeaturemax]\"
      id=\"basevendorfeature[",basevendorfeatureid,"][vendorfeaturemax]\"
      value=\"\">\n") AS "Max # Available",
    concat("<input type=\"text\"
      name=\"basevendorfeature[",basevendorfeatureid,"][vendorfeaturenotes]\"
      id=\"basevendorfeature[",basevendorfeatureid,"][vendorfeaturenotes]\"
      value=\"\">\n") AS Notes,
    concat("<input type=\"text\"
      name=\"basevendorfeature[",basevendorfeatureid,"][display_order]\"
      id=\"basevendorfeature[",basevendorfeatureid,"][display_order]\"
      value=\"\">\n") AS "Display Order"
  FROM
      BaseVendorFeature
  WHERE
    basevendorfeatureid NOT IN (SELECT basevendorfeatureid from VendorFeature where conid=$conid) 
    
EOD;

list($bfeaturerows,$bfeatureheader_array,$bfeature_array)=queryreport($queryBaseFeature,$link,$title,$description,0);
if ($bfeaturerows==0) {
  $bfeatureheader_array[0]="All possible amenities set up for this year.";
}

// Fetch (current) state of offered features
$queryFeature=<<<EOD
SELECT
    basevendorfeaturename AS Name,
    basevendorfeaturedesc AS Description,
    concat("<input type=\"text\"
      name=\"vendorfeature[",vendorfeatureid,"][vendorfeatureprice]\"
      id=\"vendorfeature[",vendorfeatureid,"][vendorfeatureprice]\"
      value=\"",vendorfeatureprice,"\">\n",
      "    <input type=\"hidden\"
      name=\"wasvendorfeature[",vendorfeatureid,"][vendorfeatureprice]\"
      id=\"wasvendorfeature[",vendorfeatureid,"][vendorfeatureprice]\"
      value=\"",vendorfeatureprice,"\">\n") AS Price,
    concat("<input type=\"text\"
      name=\"vendorfeature[",vendorfeatureid,"][vendorfeaturemax]\"
      id=\"vendorfeature[",vendorfeatureid,"][vendorfeaturemax]\"
      value=\"",vendorfeaturemax,"\">\n",
      "    <input type=\"hidden\"
      name=\"wasvendorfeature[",vendorfeatureid,"][vendorfeaturemax]\"
      id=\"wasvendorfeature[",vendorfeatureid,"][vendorfeaturemax]\"
      value=\"",vendorfeaturemax,"\">\n") AS "Max # Available",
    concat("<input type=\"text\"
      name=\"vendorfeature[",vendorfeatureid,"][vendorfeaturenotes]\"
      id=\"vendorfeature[",vendorfeatureid,"][vendorfeaturenotes]\"
      value=\"",vendorfeaturenotes,"\">\n",
      "    <input type=\"hidden\"
      name=\"wasvendorfeature[",vendorfeatureid,"][vendorfeaturenotes]\"
      id=\"wasvendorfeature[",vendorfeatureid,"][vendorfeaturenotes]\"
      value=\"",vendorfeaturenotes,"\">\n") AS Notes,
    concat("<input type=\"text\"
      name=\"vendorfeature[",vendorfeatureid,"][display_order]\"
      id=\"vendorfeature[",vendorfeatureid,"][display_order]\"
      value=\"",display_order,"\">\n",
      "    <input type=\"hidden\"
      name=\"wasvendorfeature[",vendorfeatureid,"][display_order]\"
      id=\"wasvendorfeature[",vendorfeatureid,"][display_order]\"
      value=\"",display_order,"\">\n") AS "Display Order"
  FROM
      VendorFeature
    JOIN BaseVendorFeature USING (basevendorfeatureid)
  WHERE
    conid=$conid
EOD;

list($featurerows,$featureheader_array,$feature_array)=queryreport($queryFeature,$link,$title,$description,0);
if ($featurerows==0) {
  $featureheader_array[0]="No amenities set up yet for this year.";
}

// Fetch (current) state of missing spaces
$queryBaseSpace=<<<EOD
SELECT
    basevendorspacename AS Name,
    basevendorspacedesc AS Description,
    concat("<input type=\"text\"
      name=\"basevendorspace[",basevendorspaceid,"][vendorspaceprice]\"
      id=\"basevendorspace[",basevendorspaceid,"][vendorspaceprice]\"
      value=\"\">\n") AS Price,
    concat("<input type=\"text\"
      name=\"basevendorspace[",basevendorspaceid,"][vendorspacenotes]\"
      id=\"basevendorspace[",basevendorspaceid,"][vendorspacenotes]\"
      value=\"\">\n") AS Notes,
    concat("<input type=\"text\"
      name=\"basevendorspace[",basevendorspaceid,"][display_order]\"
      id=\"basevendorspace[",basevendorspaceid,"][display_order]\"
      value=\"\">\n") AS "Display Order"
  FROM
      BaseVendorSpace
  WHERE
    basevendorspaceid NOT IN (SELECT basevendorspaceid from VendorSpace where conid=$conid) 
    
EOD;

list($bspacerows,$bspaceheader_array,$bspace_array)=queryreport($queryBaseSpace,$link,$title,$description,0);
if ($bspacerows==0) {
  $bspaceheader_array[0]="All possible spaces set up for this year.";
}

// Fetch (current) state of offered spaces
$querySpace=<<<EOD
SELECT
    basevendorspacename AS Name,
    basevendorspacedesc AS Description,
    concat("<input type=\"text\"
      name=\"vendorspace[",vendorspaceid,"][vendorspaceprice]\"
      id=\"vendorspace[",vendorspaceid,"][vendorspaceprice]\"
      value=\"",vendorspaceprice,"\">\n",
      "    <input type=\"hidden\"
      name=\"wasvendorspace[",vendorspaceid,"][vendorspaceprice]\"
      id=\"wasvendorspace[",vendorspaceid,"][vendorspaceprice]\"
      value=\"",vendorspaceprice,"\">\n") AS Price,
    concat("<input type=\"text\"
      name=\"vendorspace[",vendorspaceid,"][vendorspacenotes]\"
      id=\"vendorspace[",vendorspaceid,"][vendorspacenotes]\"
      value=\"",vendorspacenotes,"\">\n",
      "    <input type=\"hidden\"
      name=\"wasvendorspace[",vendorspaceid,"][vendorspacenotes]\"
      id=\"wasvendorspace[",vendorspaceid,"][vendorspacenotes]\"
      value=\"",vendorspacenotes,"\">\n") AS Notes,
    concat("<input type=\"text\"
      name=\"vendorspace[",vendorspaceid,"][display_order]\"
      id=\"vendorspace[",vendorspaceid,"][display_order]\"
      value=\"",display_order,"\">\n",
      "    <input type=\"hidden\"
      name=\"wasvendorspace[",vendorspaceid,"][display_order]\"
      id=\"wasvendorspace[",vendorspaceid,"][display_order]\"
      value=\"",display_order,"\">\n") AS "Display Order"
  FROM
      VendorSpace
    JOIN BaseVendorSpace USING (basevendorspaceid)
  WHERE
    conid=$conid
EOD;

list($spacerows,$spaceheader_array,$space_array)=queryreport($querySpace,$link,$title,$description,0);
if ($spacerows==0) {
  $spaceheader_array[0]="No spaces set up yet for this year.";
}

// Fetch historical state of features for comparison
$queryHistFeature=<<<EOD
SELECT
    conname AS Event,
    basevendorfeaturename AS Name,
    basevendorfeaturedesc AS Description,
    vendorfeatureprice AS Price,
    vendorfeaturemax AS "Max # Avail",
    vendorfeaturenotes AS Notes,
    display_order AS "Display Order"
  FROM
      VendorFeature
    JOIN BaseVendorFeature USING (basevendorfeatureid)
    JOIN ConInfo USING (conid)
  $wherestring
  ORDER BY
    conid
    
EOD;

list($hfeaturerows,$hfeatureheader_array,$hfeature_array)=queryreport($queryHistFeature,$link,$title,$description,0);
if ($hfeaturerows==0) {
  $hfeatureheader_array[0]="No previous amenity data available.";
}

// Fetch historical state of spaces for comparison
$queryHistSpace=<<<EOD
SELECT
    conname AS Event,
    basevendorspacename AS Name,
    basevendorspacedesc AS Description,
    vendorspaceprice AS Price,
    vendorspacenotes AS Notes,
    display_order AS "Display Order"
  FROM
      VendorSpace
    JOIN BaseVendorSpace USING (basevendorspaceid)
    JOIN ConInfo USING (conid)
  $wherestring
  ORDER BY
    conid
    
EOD;

list($hspacerows,$hspaceheader_array,$hspace_array)=queryreport($queryHistSpace,$link,$title,$description,0);
if ($hspacerows==0) {
  $hspaceheader_array[0]="No previous space data available.";
}

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Vendor Feature

// if history is requested, display it
if ($hist=="T") {
  echo "<P>Previous con's Vendor Amenities</P>\n";
  echo renderhtmlreport(1,$hfeaturerows,$hfeatureheader_array,$hfeature_array);
}

// Only show the change tables if permissions match
if ($limitedview == "F") {
  // sets the price, max, notes, and display_order for unselected VendorFeatures
  echo "<FORM name=\"basevendorfeatureform\" action=\"VendorSetupSpaceFeature.php\" method=POST>\n";
  echo "<P>Set this con's Vendor Amenities</P>\n";

  // continue to include history if requested.
  if ($hist=="T") {
    echo "<input type=\"hidden\" name\"history\" id=\"history\" value=\"Y\">\n";
  }

  // show table
  echo renderhtmlreport(1,$bfeaturerows,$bfeatureheader_array,$bfeature_array);
  echo "<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Base Vendor Feature\">Update</BUTTON></P>\n";
  echo "</FORM>\n";

  // shows the current price, max, notes, and display_order for selected VendorFeatures
  echo "<FORM name=\"vendorfeatureform\" action=\"VendorSetupSpaceFeature.php\" method=POST>\n";
  echo "<P>Update this con's Vendor Amenities</P>\n";

  // continue to include history if requested.
  if ($hist=="T") {
    echo "<input type=\"hidden\" name\"history\" id=\"history\" value=\"Y\">\n";
  }

  // show table
  echo renderhtmlreport(1,$featurerows,$featureheader_array,$feature_array);
  echo "<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Vendor Feature\">Update</BUTTON></P>\n";
  echo "</FORM>\n";
}  

// Vendor space

// if history is requested, display it
if ($hist=="T") {
  echo "<P>Previous con's Vendor Spaces</P>\n";
  echo renderhtmlreport(1,$hspacerows,$hspaceheader_array,$hspace_array);
}

// Only show the change tables if permissions match
if ($limitedview == "F") {
  // sets the price, notes, and display_order for unselected VendorSpaces
  echo "<FORM name=\"basevendorspaceform\" action=\"VendorSetupSpaceFeature.php\" method=POST>\n";
  echo "<P>Set this con's Vendor Space options</P>\n";

  //a continue to include history if requested.
  if ($hist=="T") {
    echo "<input type=\"hidden\" name\"history\" id=\"history\" value=\"Y\">\n";
  }

  // show table
  echo renderhtmlreport(1,$bspacerows,$bspaceheader_array,$bspace_array);
  echo "<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Base Vendor Space\">Update</BUTTON></P>\n";
  echo "</FORM>\n";

  // shows the current price, notes, and display_order for selected VendorSpaces
  echo "<FORM name=\"vendorspaceform\" action=\"VendorSetupSpaceFeature.php\" method=POST>\n";
  echo "<P>Update this con's Vendor Space options</P>\n";

  // continue to include history if requested.
  if ($hist=="T") {
    echo "<input type=\"hidden\" name\"history\" id=\"history\" value=\"Y\">\n";
  }

  // show table
  echo renderhtmlreport(1,$spacerows,$spaceheader_array,$space_array);
  echo "<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Vendor Space\">Update</BUTTON></P>\n";
  echo "</FORM>\n";
}

correct_footer();
*/
?>
