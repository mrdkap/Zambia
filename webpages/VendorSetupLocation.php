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
$additionalinfo.="<P>There is currently no way to update, just to add.</P>\n";
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
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
} elseif ((!empty($_POST['history'])) AND ($_POST['history'] == "Y")) {
  $hist="T";
  $additionalinfo.="<P>To see this without the clutter of the previous con information, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php>click here</A>.</P>\n";
} else {
  $additionalinfo.="<P>To see this with previous con information included, ";
  $additionalinfo.="<A HREF=VendorSetupLocation.php?history=Y>click here</A>.</P>\n";
}

// If the view is limited, then show just this years,
// otherwise previous years and this years in the history section.
$wherestring="";
if ($limitedview == "T") {
  $hist="T";
  $wherestring="WHERE conid=$conid";
}


// New Element
if ($_POST['submit'] == "Update") {
  $element_array = array('baselocbuildingid', 'baselocfloorid', 'baselocroomid',
			 'baselocsubroomid', 'conid', 'divisionid', 'trackid',
			 'locationkey', 'locationmap', 'locationheight',
			 'locationdimensions', 'locationarea', 'locationnotes',
			 'display_order');
  $value_array = array($_POST['baselocbuildingid'],
		       $_POST['baselocfloorid'],
		       $_POST['baselocroomid'],
		       $_POST['baselocsubroomid'],
		       $_SESSION['conid'],
		       $_POST['divisionid'],
		       $_POST['trackid'],
		       $_POST['locationkey'],
		       $_POST['locationmap'],
		       $_POST['locationheight'],
		       $_POST['locationdimensions'],
		       $_POST['locationarea'],
		       $_POST['locationnotes'],
		       $_POST['display_order']);
  $message.=submit_table_element($link, $title, "Location", $element_array, $value_array);

  // Somehow, do time
  /*
  $element_array = array('locationid', 'locationstart', 'locationend');
  $value_array = array(???,$_POST['locationstart'],$_POST['locationend']);
  $message.=submit_table_element($link, $title, "VendorLocHasTime", $element_array, $value_array);
  */
}

// Update Element
/* Do this somehow
   $message.=update_table_element($link, $title, "Location", $pairedvalue_array, $typeid, $$typeid);
   $message.=update_table_element($link, $title, "VendorLocHasTime", $pairedvalue_array, $typeid, $$typeid);
*/

// The below is done after the post updates, so all updates are included in the fetch.
// Locations already set for this event.
$queryLocations=<<<EOD
SELECT
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
    locationnotes as Notes,
    L.display_order as "Display Order"
  FROM
      Location L
    JOIN BaseLocBuilding USING (baselocbuildingid)
    JOIN BaseLocFloor USING (baselocfloorid)
    JOIN BaseLocRoom USING (baselocroomid)
    JOIN BaseLocSubRoom USING (baselocsubroomid)
    JOIN Divisions USING (divisionid)
    JOIN Tracks USING (trackid)
    JOIN ConInfo USING (conid)
    JOIN (SELECT
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
EOD;

// Get the query
list($rows,$header_array,$report_array)=queryreport($queryLocations,$link,$title,$description,0);

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
if ($hist=="T") {
  $workstring="<P>All event's Vendor Locations.</P>\n";
} else {
  $workstring="<P>This event's Vendor Locations.</P>\n";
}

// The already set Vendor Locations
$workstring.=renderhtmlreport(1,$rows,$header_array,$report_array);

// Only show the change tables if permissions match
if ($limitedview == "F") {
  $workstring.="<FORM name=\"locform\" action=\"VendorSetupLocation.php\" method=POST>\n";

  // continue to include history if requested.
  if ($hist=="T") {
    $workstring.="  <INPUT type=\"hidden\" name\"history\" id=\"history\" value=\"Y\">\n";
  }

  $workstring.="  <TABLE>\n";
  $workstring.="    <TR>\n";

  // Building
  $workstring.="      <TD colspan=2>\n";
  $workstring.="        <SPAN><LABEL for=\"baselocbuildingid\">Building: </LABEL><SELECT name=\"baselocbuildingid\">\n";
  $workstring.=populate_select_from_query_inline($queryBuilding, 0, "SELECT", true);
  $workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";

  // Floor
  $workstring.="      <TD>\n";
  $workstring.="        <SPAN><LABEL for=\"baselocfloorid\">Floor: </LABEL><SELECT name=\"baselocfloorid\">\n";
  $workstring.=populate_select_from_query_inline($queryFloor, 0, "SELECT", true);
  $workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";

  $workstring.="    </TR>\n    <TR>\n";
  // Room
  $workstring.="      <TD colspan=2>\n";
  $workstring.="        <SPAN><LABEL for=\"baselocroomid\">Room: </LABEL><SELECT name=\"baselocroomid\">\n";
  $workstring.=populate_select_from_query_inline($queryRoom, 0, "SELECT", true);
  $workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";

  // Subroom
  $workstring.="      <TD>\n";
  $workstring.="        <SPAN><LABEL for=\"baselocsubroomid\">Subroom: </LABEL><SELECT name=\"baselocsubroomid\">\n";
  $workstring.=populate_select_from_query_inline($querySubRoom, 0, "SELECT", true);
  $workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";

  // Conid should be set above.

  $workstring.="    </TR>\n    <TR>\n";
  // Division
  $workstring.="      <TD>\n";
  $workstring.="        <SPAN><LABEL for=\"divisionid\">Division: </LABEL><SELECT name=\"divisionid\">\n";
  $workstring.=populate_select_from_query_inline($queryDivision, 0, "SELECT", true);
  $workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";

  // Track
  $workstring.="      <TD colspan=2>\n";
  $workstring.="        <SPAN><LABEL for=\"trackid\">Track: </LABEL><SELECT name=\"trackid\">\n";
  $workstring.=populate_select_from_query_inline($queryTracks, 0, "SELECT", true);
  $workstring.="        </SELECT>&nbsp;&nbsp;</SPAN>\n    </TD>\n";

  $workstring.="    </TR>\n  <TR>\n";
  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"locationkey\">Key:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"locationkey\" id=\"locationkey\" value=\"\">\n";
  $workstring.="      </TD>\n";
  $workstring.="      <TD colspan=2>\n";
  $workstring.="        <LABEL for=\"locationmap\">Map:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"locationmap\" id=\"locationmap\" size=42 value=\"\">\n";
  $workstring.="      </TD>\n";

  $workstring.="    </TR>\n  <TR>\n";

  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"locationheight\">Height:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"locationheight\" id=\"locationheight\" value=\"\">\n";
  $workstring.="      </TD>\n";
  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"locationdimensions\">Dimensions:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"locationdimensions\" id=\"locationdimensions\" value=\"\">\n";
  $workstring.="      </TD>\n";
  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"locationarea\">Area:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"locationarea\" id=\"locationarea\" value=\"\">\n";
  $workstring.="      </TD>\n";

  $workstring.="    </TR>\n  <TR>\n";

  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"locationstart\">Start Time:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"locationstart\" id=\"locationstart\" size=6 value=\"00:00:00\">\n";
  $workstring.="      </TD>\n";
  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"locationend\">End Time:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"locationend\" id=\"locationend\" size=6 value=\"00:00:00\">\n";
  $workstring.="      </TD>\n";
  $workstring.="      <TD>\n";
  $workstring.="        <LABEL for=\"display_order\">Display Order: </LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"display_order\" id=\"display_order\" value=\"\">\n";
  $workstring.="      </TD>\n";

  $workstring.="    </TR>\n  <TR>\n";

  $workstring.="      <TD colspan=3>\n";
  $workstring.="        <LABEL for=\"locationnotes\">Notes:</LABEL>\n";
  $workstring.="        <input type=\"text\" name=\"locationnotes\" id=\"locationnotes\" size=63 value=\"\">\n";
  $workstring.="      </TD>\n";
  $workstring.="    </TR>\n  </TABLE>\n";
  $workstring.="<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Update\">Update</BUTTON></P>\n</FORM>\n";
}

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

echo $workstring;

correct_footer();
?>
