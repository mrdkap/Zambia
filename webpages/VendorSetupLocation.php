<?php
require_once('StaffCommonCode.php');
global $link, $message, $message_error;

// LOCALIZATIONS
$title="Create/Update Vendor Locations";
$description="<P>Create and update this event's instances of the possible Vendor Locations.</P>\n";
$additionalinfo="<P>Each section has it's own update button.\n";
$additionalinfo.="Please do not try to update more than one section at once, ";
$additionalinfo.="it will not (necessarily) be heeded properly.</P>\n";
$additionalinfo.="<P>This page is limited to a few select people who can change it, ";
$additionalinfo.="basically the Con Chair and the Super Vendor folks.</P>\n";
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// Specific Location
$locationid="";
if ((!empty($_GET['locationid'])) AND (is_numeric($_GET['locationid']))) {
  $locationid=$_GET['locationid'];
  $additionalinfo.="<P>You are updating a location. ";
  $additionalinfo.="<A HREF=\"VendorSetupLocation.php\">Add</A> a new one.</P>\n";
} elseif ((!empty($_POST['locationid'])) AND (is_numeric($_POST['locationid']))) {
  $locationid=$_POST['locationid'];
  $additionalinfo.="<P>You are updating a location. ";
  $additionalinfo.="<A HREF=\"VendorSetupLocation.php\">Add</A> a new one.</P>\n";
}

// Show history
$hist="";
if ((!empty($_GET['history'])) AND (is_numeric($_GET['history']))) {
  $hist=$_GET['history'];
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=Y>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this without the clutter of any of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
} elseif ((!empty($_POST['history'])) AND (is_numeric($_POST['history']))) {
  $hist=$_POST['history'];
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=Y>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this without the clutter of any of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
} elseif ((!empty($_GET['history'])) AND ($_GET['history'] == "Y")) {
  $hist="Y";
  $additionalinfo.="<P>To see this without the clutter of any of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
} elseif ((!empty($_POST['history'])) AND ($_POST['history'] == "Y")) {
  $hist="Y";
  $additionalinfo.="<P>To see this without the clutter of any of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
} else {
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=Y>click here</A>.</P>\n";
}

// To select a particular con's info for display
$queryOtherCons=<<<EOD
SELECT
    conid,
    conname
  FROM
      ConInfo
  WHERE
    conid!=$conid AND
    conid!=0
  ORDER BY
    conid+0
EOD;
$additionalinfo.="<FORM name=\"conshow\" action=\"VendorSetupLocation.php\" method=GET>\n";
$additionalinfo.="  <SPAN><LABEL for=\"history\">To see this with a perticular previous con information included: </LABEL><SELECT name=\"history\">\n";
$additionalinfo.=populate_select_from_query_inline($queryOtherCons, $hist, "SELECT", true);
$additionalinfo.="  </SELECT></SPAN>\n";
$additionalinfo.="  <BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Select\">Select</BUTTON>\n</FORM>\n";

// Hint for top posting
$additionalinfo.="<P>To bring the form to add an element to the top set toppost to \"T\"</P>\n<hr>\n";


// If the view is limited, then show just this years, otherwise
// appropriate previous years and this years in the history section.
$wherestring="";
if ($hist == "") {
  $wherestring="WHERE conid=$conid";
} elseif (is_numeric($hist)) {
  $wherestring="WHERE conid=$conid or conid=$hist";
}

// Update Element
if (($_POST['submit'] == "Update") AND ($locationid != "")) {
  // If it is a removal, remove it, otherwise update it.
  if ($_POST['remove']=="remove") {
    $match_string="locationid=$locationid";
    $message.=delete_table_element($link, $title, "Location", $match_string);
    $locationid="";
  } else {
    $element_array = array('baselocbuildingid', 'baselocfloorid', 'baselocroomid',
			   'baselocsubroomid', 'conid', 'divisionid', 'trackid',
			   'locationkey', 'locationmap', 'locationheight',
			   'locationdimensions', 'locationarea', 'locationnotes',
			   'display_order');
    foreach ($element_array as $i) {
      if ($_POST[$i] != $_POST["was".$i]) {
	$pairedvalue_array[]="$i=\"" . mysql_real_escape_string(stripslashes($_POST[$i])) . '"';
      }
    }
    $message.=update_table_element($link, $title, "Location", $pairedvalue_array, "locationid", $locationid);
  }
  // else check to see/submit if it is a new element
} elseif ($_POST['submit'] == "Update") {
  $element_array = array('baselocbuildingid', 'baselocfloorid', 'baselocroomid',
			 'baselocsubroomid', 'conid', 'divisionid', 'trackid',
			 'locationkey', 'locationmap', 'locationheight',
			 'locationdimensions', 'locationarea', 'locationnotes',
			 'display_order');
  $value_array = array(mysql_real_escape_string(stripslashes($_POST['baselocbuildingid'])),
		       mysql_real_escape_string(stripslashes($_POST['baselocfloorid'])),
		       mysql_real_escape_string(stripslashes($_POST['baselocroomid'])),
		       mysql_real_escape_string(stripslashes($_POST['baselocsubroomid'])),
		       mysql_real_escape_string(stripslashes($_SESSION['conid'])),
		       mysql_real_escape_string(stripslashes($_POST['divisionid'])),
		       mysql_real_escape_string(stripslashes($_POST['trackid'])),
		       mysql_real_escape_string(stripslashes($_POST['locationkey'])),
		       mysql_real_escape_string(stripslashes($_POST['locationmap'])),
		       mysql_real_escape_string(stripslashes($_POST['locationheight'])),
		       mysql_real_escape_string(stripslashes($_POST['locationdimensions'])),
		       mysql_real_escape_string(stripslashes($_POST['locationarea'])),
		       mysql_real_escape_string(stripslashes($_POST['locationnotes'])),
		       mysql_real_escape_string(stripslashes($_POST['display_order'])));
  $message.=submit_table_element($link, $title, "Location", $element_array, $value_array);
}

// Remove/update/add time:
if (($_POST['submit'] == "Time Update") AND ($locationid != "")) {
  $queryTimeLoc=<<<EOD
SELECT
    locationtimeid,
    substr(locationstart,1,5) as "Start",
    substr(locationend,1,5) as "End"
  FROM
      VendorLocHasTime
  WHERE
    locationid=$locationid
EOD;

  // Get the query
  list($tlocrows,$tlocheader_array,$tloc_array)=queryreport($queryTimeLoc,$link,$title,$description,0);

  for ($i=1; $i<=$tlocrows; $i++) {
    $tlocid=$tloc_array[$i]['locationtimeid'];
    // if remove / else update if either are changed
    if ($_POST['remove'][$tlocid] == "remove") {
      $match_string="locationtimeid=$tlocid";
      $message.=delete_table_element($link, $title, "VendorLocHasTime", $match_string);
    } else {
      $pairedvalue_array=array();
      $delta=0;
      // change in location start
      if ($_POST['locationstart'][$tlocid] != $_POST['waslocationstart'][$tlocid]) {
	$pairedvalue_array[]='locationstart="' . $_POST['locationstart'][$tlocid] . ':00"';
	$delta++;
      }
      // change in location end
      if ($_POST['locationend'][$tlocid] != $_POST['waslocationend'][$tlocid]) {
	$pairedvalue_array[]='locationend="' . $_POST['locationend'][$tlocid] . ':00"';
	$delta++;
      }
      // if there was a change
      if ($delta > 0) {
	$message.=update_table_element($link, $title, "VendorLocHasTime", $pairedvalue_array, "locationtimeid", $tlocid);
      }
    }
  }

  if ((!empty($_POST['start'])) AND
      (!empty($_POST['end'])) AND
      (preg_match('/^[0-9][0-9]:[0-5][0-9]$/',$_POST['start'])) AND
      (preg_match('/^[0-9][0-9]:[0-5][0-9]$/',$_POST['end']))) {
    echo "locationid = $locationid";
    $element_array = array('locationid', 'locationstart', 'locationend');
    $value_array = array($_POST['locationid'],$_POST['start'],$_POST['end']);
    $message.=submit_table_element($link, $title, "VendorLocHasTime", $element_array, $value_array);
  }
}

// The below is done after the post updates, so all updates are included in the fetch.
// Locations already set for this event.
$queryLocations=<<<EOD
SELECT
    concat(if(conid=$conid,concat("<A HREF=VendorSetupLocation.php?locationid=",locationid,">"),""),L.display_order,if(conid=$conid,"</A>","")) as "Display Order",
    conname AS Event,
    baselocbuildingname as Building,
    baselocfloorname as Floor,
    baselocroomname as Room,
    if(baselocsubroomid != 0,baselocsubroomname,"") as "Room Section",
    divisionname as Division,
    if(trackid != 0,trackname,"") as Track,
    locationkey as "Key",
    locationmap as Map,
    locationheight as Height,
    locationdimensions as Dimensions,
    locationarea as Area,
    Time,
    locationnotes as Notes
  FROM
      Location L
    JOIN BaseLocBuilding USING (baselocbuildingid)
    JOIN BaseLocFloor USING (baselocfloorid)
    JOIN BaseLocRoom USING (baselocroomid)
    JOIN BaseLocSubRoom USING (baselocsubroomid)
    JOIN Divisions USING (divisionid)
    JOIN Tracks USING (trackid)
    JOIN ConInfo USING (conid)
    LEFT JOIN (SELECT
        locationid,
	GROUP_CONCAT("Start: ",
          DATE_FORMAT(ADDTIME(constartdate,locationstart), '%a %l:%i %p'),
          " End: ",
          DATE_FORMAT(ADDTIME(constartdate,locationend), '%a %l:%i %p') SEPARATOR "<br />") as "Time"
      FROM
          VendorLocHasTime
        JOIN Location USING (locationid)
	JOIN ConInfo USING (conid)
      GROUP BY
        locationid) VLHT USING (locationid)
  $wherestring
  ORDER BY
    conid,
    L.display_order
EOD;

// Get the query
list($rows,$header_array,$report_array)=queryreport($queryLocations,$link,$title,$description,0);

if ($locationid != "") {
  $queryHasLocation=<<<EOD
SELECT
    locationid,
    L.display_order,
    baselocbuildingid,
    baselocfloorid,
    baselocroomid,
    baselocsubroomid,
    divisionid,
    trackid,
    locationkey,
    locationmap,
    locationheight,
    locationdimensions,
    locationarea,
    locationnotes
  FROM
      Location L
  WHERE
    locationid=$locationid
EOD;

  // Get the query
  list($hlocrows,$hlocheader_array,$hloc_array)=queryreport($queryHasLocation,$link,$title,$description,0);

  $queryTimeLoc=<<<EOD
SELECT
    locationtimeid,
    substr(locationstart,1,5) as "Start",
    substr(locationend,1,5) as "End"
  FROM
      VendorLocHasTime
  WHERE
    locationid=$locationid
EOD;

  // Get the query
  list($tlocrows,$tlocheader_array,$tloc_array)=queryreport($queryTimeLoc,$link,$title,$description,0);
}

// Some Vendor Defaults
if ($hlocrows == 0) {
  $hloc_array[1]['baselocsubroomid']=0;
  $hloc_array[1]['divisionid']=8;
  $hloc_array[1]['trackid']=0;
}

// All base buildings
$queryBuilding=<<<EOD
SELECT
    baselocbuildingid,
    baselocbuildingname
  FROM
      BaseLocBuilding
EOD;

// All base floors
$queryFloor=<<<EOD
SELECT
    baselocfloorid,
    baselocfloorname
  FROM
      BaseLocFloor
EOD;

// All base rooms
$queryRoom=<<<EOD
SELECT
    baselocroomid,
    baselocroomname
  FROM
      BaseLocRoom
  ORDER BY
    baselocroomname
EOD;

// All base sub-room divisions
$querySubRoom=<<<EOD
SELECT
    baselocsubroomid,
    baselocsubroomname
  FROM
      BaseLocSubRoom
EOD;

// All Con Divisions
$queryDivision=<<<EOD
SELECT
    divisionid,
    divisionname
  FROM
      Divisions
  ORDER BY
    display_order
EOD;

// All Con Tracks
$queryTracks=<<<EOD
SELECT
    trackid,
    trackname
  FROM
      Tracks
  ORDER BY
    display_order
EOD;

$workstring="";
$tablestring="";
// if history is requested, display it
if ($hist=="Y") {
  $tablestring.="<P>All event's Vendor Locations.</P>\n";
} elseif (is_numeric($hist)) {
  $tablestring.="<P>Two events' Vendor Locations.</P>\n";
} else {
  $tablestring.="<P>This event's Vendor Locations.</P>\n";
}

// The already set Vendor Locations
$tablestring.=renderhtmlreport(1,$rows,$header_array,$report_array);

// Only show the change tables if permissions match
if ($locationid != "") {
  $workstring.="<P><strong>Update the times for this entry:</strong></P>\n";
  $workstring.="<P>Times are in the form of HH:MM offset from 00:00 (the start of the con).</P>\n";
  $workstring.="<FORM name=\"timeform\" action=\"VendorSetupLocation.php\" method=POST>\n";
  $workstring.="  <INPUT type=\"hidden\" name=\"locationid\" id=\"locationid\" value=\"$locationid\">\n";

  // continue to include history if requested.
  if ($hist != "") {
    $workstring.="  <INPUT type=\"hidden\" name=\"history\" id=\"history\" value=\"$hist\">\n";
  }

  // put the add at the top if requested
  if (($_POST['toppost'] == "T") or ($_GET['toppost'] == "T")) {
    $workstring.="  <INPUT type=\"hidden\" name=\"toppost\" id=\"toppost\" value=\"T\">\n";
  }

  $workstring.="  <TABLE>\n";
  for ($i=1; $i<=$tlocrows; $i++) {
    $workstring.="    <TR>\n";
    $workstring.="      <TD>\n";
    $slabel="locationstart[" . $tloc_array[$i]['locationtimeid'] . "]";
    $elabel="locationend[" . $tloc_array[$i]['locationtimeid'] . "]";
    $rlabel="remove[" . $tloc_array[$i]['locationtimeid'] . "]";
    $workstring.="        <LABEL for=\"$slabel\">Start Time:</LABEL>\n";
    $workstring.="        <input type=\"text\" name=\"$slabel\" id=\"$slabel\" size=4 value=\"" . $tloc_array[$i]['Start'] . "\">\n";
    $workstring.="        <input type=\"hidden\" name=\"was$slabel\" id=\"was$slabel\" value=\"" . $tloc_array[$i]['Start'] . "\">\n";
    $workstring.="      </TD>\n";
    $workstring.="      <TD>\n";
    $workstring.="        <LABEL for=\"$elabel\">End Time:</LABEL>\n";
    $workstring.="        <input type=\"text\" name=\"$elabel\" id=\"$elabel\" size=4 value=\"" . $tloc_array[$i]['End'] . "\">\n";
    $workstring.="        <input type=\"hidden\" name=\"was$elabel\" id=\"was$elabel\" value=\"" . $tloc_array[$i]['End'] . "\">\n";
    $workstring.="      </TD>\n";
    $workstring.="      <TD>\n";
    $workstring.="        <LABEL for\"$rlabel\">Remove:</LABEL>\n";
    $workstring.="        <INPUT type=\"checkbox\" name=\"$rlabel\" id=\"$rlabel\" value=\"remove\">\n";
    $workstring.="      </TD>\n";
    $workstring.="    </TR>\n";
  }
  $workstring.="    <TR>\n";
  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"start\">Start Time:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"start\" id=\"start\" size=4 value=\"\">\n";
  $workstring.="      </TD>\n";
  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"end\">End Time:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"end\" id=\"end\" size=4 value=\"\">\n";
  $workstring.="      </TD>\n";
  $workstring.="    </TR>\n";

  $workstring.="  </TABLE>\n";
  $workstring.="  <P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Time Update\">Update Time</BUTTON></P>\n</FORM>\n<hr>\n";
  $workstring.="<P><strong>Update this entry:</strong></P>\n";
}

$workstring.="<FORM name=\"locform\" action=\"VendorSetupLocation.php\" method=POST>\n";
$workstring.="  <INPUT type=\"hidden\" name=\"locationid\" id=\"locationid\" value=\"$locationid\">\n";

// continue to include history if requested.
if ($hist != "") {
  $workstring.="  <INPUT type=\"hidden\" name=\"history\" id=\"history\" value=\"$hist\">\n";
}

// put the add at the top if requested
if (($_POST['toppost'] == "T") or ($_GET['toppost'] == "T")) {
  $workstring.="  <INPUT type=\"hidden\" name=\"toppost\" id=\"toppost\" value=\"T\">\n";
}

$workstring.="  <TABLE>\n";
$workstring.="    <TR>\n";

// Removal
if ($locationid != "") {
  $workstring.="      <TD colspan=3>\n";
  $workstring.="  <LABEL for\"remove\">Remove:</LABEL>\n";
  $workstring.="  <INPUT type=\"checkbox\" name=\"remove\" id=\"remove\" value=\"remove\">\n";
  $workstring.="      </TD></TR>\n";
  $workstring.="    <TR>\n";
}

// Building
$workstring.="      <TD colspan=2>\n";
$workstring.="        <SPAN><LABEL for=\"baselocbuildingid\">Building: </LABEL><SELECT name=\"baselocbuildingid\">\n";
$workstring.=populate_select_from_query_inline($queryBuilding, $hloc_array[1]['baselocbuildingid'], "SELECT", true);
$workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";
$workstring.="        <INPUT type=\"hidden\" name=\"wasbaselocbuildingid\" id=\"wasbaselocbuildingid\" value=\"" . $hloc_array[1]['baselocbuildingid'] . "\">\n";

// Floor
$workstring.="      <TD>\n";
$workstring.="        <SPAN><LABEL for=\"baselocfloorid\">Floor: </LABEL><SELECT name=\"baselocfloorid\">\n";
$workstring.=populate_select_from_query_inline($queryFloor, $hloc_array[1]['baselocfloorid'], "SELECT", true);
$workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";
$workstring.="        <INPUT type=\"hidden\" name=\"wasbaselocfloorid\" id=\"wasbaselocfloorid\" value=\"" . $hloc_array[1]['baselocfloorid'] . "\">\n";

$workstring.="    </TR>\n    <TR>\n";
// Room
$workstring.="      <TD colspan=2>\n";
$workstring.="        <SPAN><LABEL for=\"baselocroomid\">Room: </LABEL><SELECT name=\"baselocroomid\">\n";
$workstring.=populate_select_from_query_inline($queryRoom, $hloc_array[1]['baselocroomid'], "SELECT", true);
$workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";
$workstring.="        <INPUT type=\"hidden\" name=\"wasbaselocroomid\" id=\"wasbaselocroomid\" value=\"" . $hloc_array[1]['baselocroomid'] . "\">\n";

// Subroom
$workstring.="      <TD>\n";
$workstring.="        <SPAN><LABEL for=\"baselocsubroomid\">Subroom: </LABEL><SELECT name=\"baselocsubroomid\">\n";
$workstring.=populate_select_from_query_inline($querySubRoom, $hloc_array[1]['baselocsubroomid'], "SELECT", true);
$workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";
$workstring.="        <INPUT type=\"hidden\" name=\"wasbaselocsubroomid\" id=\"wasbaselocsubroomid\" value=\"" . $hloc_array[1]['baselocsubroomid'] . "\">\n";

// Conid should be set above.

$workstring.="    </TR>\n    <TR>\n";
// Division
$workstring.="      <TD>\n";
$workstring.="        <SPAN><LABEL for=\"divisionid\">Division: </LABEL><SELECT name=\"divisionid\">\n";
$workstring.=populate_select_from_query_inline($queryDivision, $hloc_array[1]['divisionid'], "SELECT", true);
$workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";
$workstring.="        <INPUT type=\"hidden\" name=\"wasdivisionid\" id=\"wasdivisionid\" value=\"" . $hloc_array[1]['divisionid'] . "\">\n";

// Track
$workstring.="      <TD colspan=2>\n";
$workstring.="        <SPAN><LABEL for=\"trackid\">Track: </LABEL><SELECT name=\"trackid\">\n";
$workstring.=populate_select_from_query_inline($queryTracks, $hloc_array[1]['trackid'], "SELECT", true);
$workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";
$workstring.="        <INPUT type=\"hidden\" name=\"wastrackid\" id=\"wastrackid\" value=\"" . $hloc_array[1]['trackid'] . "\">\n";

$workstring.="    </TR>\n  <TR>\n";
$workstring.="      <TD>\n";
$workstring.="        <LABEL for=\"locationkey\">Key:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationkey\" id=\"locationkey\" value=\"" . $hloc_array[1]['locationkey'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationkey\" id=\"waslocationkey\" value=\"" . $hloc_array[1]['locationkey'] . "\">\n";
$workstring.="      </TD>\n";
$workstring.="      <TD colspan=2>\n";
$workstring.="        <LABEL for=\"locationmap\">Map:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationmap\" id=\"locationmap\" size=42 value=\"" . $hloc_array[1]['locationmap'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationmap\" id=\"waslocationmap\" value=\"" . $hloc_array[1]['locationmap'] . "\">\n";
$workstring.="      </TD>\n";

$workstring.="    </TR>\n  <TR>\n";

$workstring.="      <TD>\n";
$workstring.="        <LABEL for=\"locationheight\">Height:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationheight\" id=\"locationheight\" value=\"" . $hloc_array[1]['locationheight'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationheight\" id=\"waslocationheight\" value=\"" . $hloc_array[1]['locationheight'] . "\">\n";
$workstring.="      </TD>\n";
$workstring.="      <TD>\n";
$workstring.="        <LABEL for=\"locationdimensions\">Dimensions:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationdimensions\" id=\"locationdimensions\" value=\"" . $hloc_array[1]['locationdimensions'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationdimensions\" id=\"waslocationdimensions\" value=\"" . $hloc_array[1]['locationdimensions'] . "\">\n";
$workstring.="      </TD>\n";
$workstring.="      <TD>\n";
$workstring.="        <LABEL for=\"locationarea\">Area:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationarea\" id=\"locationarea\" value=\"" . $hloc_array[1]['locationarea'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationarea\" id=\"waslocationarea\" value=\"" . $hloc_array[1]['locationarea'] . "\">\n";
$workstring.="      </TD>\n";

$workstring.="    </TR>\n  <TR>\n";

$workstring.="      <TD>\n";
$workstring.="        <LABEL for=\"display_order\">Display Order: </LABEL>\n";
$workstring.="        <input type=\"text\" name=\"display_order\" id=\"display_order\" value=\"" . $hloc_array[1]['display_order'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"wasdisplay_order\" id=\"wasdisplay_order\" value=\"" . $hloc_array[1]['display_order'] . "\">\n";
$workstring.="      </TD>\n";
$workstring.="      <TD colspan=2>\n";
$workstring.="        <LABEL for=\"locationnotes\">Notes:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationnotes\" id=\"locationnotes\" size=63 value=\"" . $hloc_array[1]['locationnotes'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationnotes\" id=\"waslocationnotes\" value=\"" . $hloc_array[1]['locationnotes'] . "\">\n";
$workstring.="      </TD>\n";
$workstring.="    </TR>\n  </TABLE>\n";
$workstring.="  <P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Update\">Update Entry</BUTTON></P>\n</FORM>\n";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor"))) {
  if (($_POST['toppost'] == "T") or ($_GET['toppost'] == "T")) {
    echo $workstring;
    echo "<hr>\n";
    echo $tablestring;
  } else {
    echo $tablestring;
    echo "<hr>\n";
    echo $workstring;
  }
} else {
  echo "<P>We're sorry you do not have permission to view this page at this time.</P>\n";
}

correct_footer();
?>
