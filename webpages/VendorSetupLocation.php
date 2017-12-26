<?php
require_once('StaffCommonCode.php');
global $link, $message, $message_error;

// LOCALIZATIONS
$title="Create/Update Vendor Locations";
$description="<P>Create and update this event's instances of the possible Vendor Locations.</P>\n";
$additionalinfo="<P>Each section has it's own update button.\n";
$additionalinfo.="Please do not try to update more than one section at once, ";
$additionalinfo.="it will not (necessarily) be heeded properly.</P>\n";
$additionalinfo.="<P>Time is still broken, and currently a by-hand job.</P>\n";
$additionalinfo.="<P>This page is limited to a few select people who can change it, ";
$additionalinfo.="basically the Con Chair and the Super Vendor folks.</P>\n";
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// Specific Location
$locationid="";
if ((!empty($_GET['locationid'])) AND (is_numeric($_GET['locationid']))) {
  $locationid=$_GET['locationid'];
} elseif ((!empty($_POST['locationid'])) AND (is_numeric($_POST['locationid']))) {
  $locationid=$_POST['locationid'];
}

// Show history
$hist="F";
if ((!empty($_GET['history'])) AND (is_numeric($_GET['history']))) {
  $hist=$_GET['history'];
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=Y>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this without the clutter of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
} elseif ((!empty($_POST['history'])) AND (is_numeric($_POST['history']))) {
  $hist=$_GET['history'];
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=Y>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this without the clutter of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
} elseif ((!empty($_GET['history'])) AND ($_GET['history'] == "Y")) {
  $hist="Y";
  $additionalinfo.="<P>To see this without the clutter of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this with a perticular previous con information included: ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-1) . ">" . ($conid-1) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-2) . ">" . ($conid-2) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-3) . ">" . ($conid-3) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-4) . ">" . ($conid-4) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-5) . ">" . ($conid-5) . "</A>.</P>\n";
} elseif ((!empty($_POST['history'])) AND ($_POST['history'] == "Y")) {
  $hist="Y";
  $additionalinfo.="<P>To see this without the clutter of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this with a perticular previous con information included: ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-1) . ">" . ($conid-1) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-2) . ">" . ($conid-2) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-3) . ">" . ($conid-3) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-4) . ">" . ($conid-4) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-5) . ">" . ($conid-5) . "</A>.</P>\n";
} else {
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=Y>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this with a perticular previous con information included: ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-1) . ">" . ($conid-1) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-2) . ">" . ($conid-2) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-3) . ">" . ($conid-3) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-4) . ">" . ($conid-4) . "</A>, \n";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=" . ($conid-5) . ">" . ($conid-5) . "</A>.</P>\n";
}

// If the view is limited, then show just this years,
// otherwise previous years and this years in the history section.
$wherestring="";
if ($hist == "") {
  $wherestring="WHERE conid=$conid";
} elseif (is_numeric($hist)) {
  $wherestring="WHERE conid=$conid or conid=$hist";
}

// Update Element
if (($_POST['submit'] == "Update") AND ($locationid != "")) {
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
  //$message.=submit_table_element($link, $title, "Location", $element_array, $value_array);

  // Somehow, do time
  /*
  // if update:
  $message.=update_table_element($link, $title, "VendorLocHasTime", $pairedvalue_array, $typeid, $$typeid);

  // if new:
  $element_array = array('locationid', 'locationstart', 'locationend');
  $value_array = array($locationid,$_POST['locationstart'],$_POST['locationend']);
  $message.=submit_table_element($link, $title, "VendorLocHasTime", $element_array, $value_array);
  */
}

// The below is done after the post updates, so all updates are included in the fetch.
// Locations already set for this event.
$queryLocations=<<<EOD
SELECT
    concat("<A HREF=VendorSetupLocation.php?locationid=",locationid,">",L.display_order,"</A>") as "Display Order",
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
  WHERE
    locationid=$locationid
EOD;

  // Get the query
  list($hlocrows,$hlocheader_array,$hloc_array)=queryreport($queryHasLocation,$link,$title,$description,0);
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

// if history is requested, display it
if ($hist=="Y") {
  $workstring="<P>All event's Vendor Locations.</P>\n";
} elseif (is_numeric($hist)) {
  $workstring.="<P>Two events' Vendor Locations.</P>\n";
} else {
  $workstring="<P>This event's Vendor Locations.</P>\n";
}

// The already set Vendor Locations
$workstring.=renderhtmlreport(1,$rows,$header_array,$report_array);

// Only show the change tables if permissions match
if ($locationid != "") {
  $workstring.="<P><strong>Update this entry</strong></P>\n";
}
$workstring.="<FORM name=\"locform\" action=\"VendorSetupLocation.php\" method=POST>\n";
$workstring.="  <INPUT type=\"hidden\" name=\"locationid\" id=\"locationid\" value=\"$locationid\">\n";

// continue to include history if requested.
if ($hist=="T") {
  $workstring.="  <INPUT type=\"hidden\" name=\"history\" id=\"history\" value=\"Y\">\n";
}

$workstring.="  <TABLE>\n";
$workstring.="    <TR>\n";

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
$workstring.="        <LABEL for=\"locationstart\">Start Time:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationstart\" id=\"locationstart\" size=6 value=\"00:00:00\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationstart\" id=\"waslocationstart\" value=\"" . $hloc_array[1]['locationstart'] . "\">\n";
$workstring.="      </TD>\n";
$workstring.="      <TD>\n";
$workstring.="        <LABEL for=\"locationend\">End Time:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationend\" id=\"locationend\" size=6 value=\"00:00:00\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationend\" id=\"waslocationend\" value=\"" . $hloc_array[1]['locationend'] . "\">\n";
$workstring.="      </TD>\n";
$workstring.="      <TD>\n";
$workstring.="        <LABEL for=\"display_order\">Display Order: </LABEL>\n";
$workstring.="        <input type=\"text\" name=\"display_order\" id=\"display_order\" value=\"" . $hloc_array[1]['display_order'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"wasdisplay_order\" id=\"wasdisplay_order\" value=\"" . $hloc_array[1]['display_order'] . "\">\n";
$workstring.="      </TD>\n";

$workstring.="    </TR>\n  <TR>\n";

$workstring.="      <TD colspan=3>\n";
$workstring.="        <LABEL for=\"locationnotes\">Notes:</LABEL>\n";
$workstring.="        <input type=\"text\" name=\"locationnotes\" id=\"locationnotes\" size=63 value=\"" . $hloc_array[1]['locationnotes'] . "\">\n";
$workstring.="        <input type=\"hidden\" name=\"waslocationnotes\" id=\"waslocationnotes\" value=\"" . $hloc_array[1]['locationnotes'] . "\">\n";
$workstring.="      </TD>\n";
$workstring.="    </TR>\n  </TABLE>\n";
$workstring.="<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Update\">Update</BUTTON></P>\n</FORM>\n";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor"))) {
  echo $workstring;
} else {
  echo "<P>We're sorry you do not have permission to view this page at this time.</P>\n";
}

correct_footer();
?>
