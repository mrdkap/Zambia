<?php
require_once('PostingCommonCode.php');
global $link;
$conid=$_GET['conid'];

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStartDatim=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

// LOCALIZATIONS
$_SESSION['return_to_page']="Vendors.php";
$title="Vendor List for $conname";
$description="<P>List of all Vendors.</P>\n";

$vendormap="";
if (file_exists("../Local/$conid/Vendor_Map.svg")) {
  $vendormap.=file_get_contents("../Local/$conid/Vendor_Map.svg");
}

$vendorpdfmap="";
if (file_exists("../Local/$conid/Vendor_Map.pdf")) {
  $vendorpdfmap="<A HREF=\"../Local/$conid/Vendor_Map.pdf\">Click for the Map</A>\n";
}

$vendorlist="";
if (file_exists("../Local/$conid/Vendor_List")) {
  $vendorlist.=file_get_contents("../Local/$conid/Vendor_List");
}

/* This complex query grabs the name, and class information.
 Most, if not all of the formatting is done within the query, as opposed to in
 the post-processing. The vendor bio information is grabbed seperately. */
$query = <<<EOD
SELECT
    DISTINCT concat('<A NAME=\"',pubsname,'\"></A>',pubsname) AS 'Participants',
    if((secondtitle!=''),concat('<A NAME=\"', sessionid, '\">', secondtitle, '</A>'),"") AS 'Location',
    pubsname,
    badgeid
  FROM
      Participants
    JOIN UserHasPermissionRole UHPR USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
    JOIN Interested I USING (badgeid,conid)
    JOIN InterestedTypes USING (interestedtypeid)
    LEFT JOIN ParticipantOnSession USING (badgeid,conid)
    LEFT JOIN Sessions USING (sessionid,conid)
  WHERE
    interestedtypename in ('Yes') AND
    permrolename in ('Vendor') AND
    conid=$conid
  ORDER BY
  pubsname
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the vendor bio page. */
topofpagereport($title,$description,$additionalinfo);
if ($vendormap != "") {
  echo "<H3><A NAME=\"VendorMapStart\"></A><B>Map</B><br>(jump to the <A HREF=\"#VendorStart\">Vendors</A>";
  echo " or the <A HREF=\"#CommunityStart\">Community Tables</A>)</H3>\n";
  echo $vendormap;
}
echo $vendorpdfmap;
echo $vendorlist;

// Heavy_handed hack!
if (($conid == "42") or ($conid == "43") or ($conid == "44")) {
  // Connect to Vendor Database
  if (vendor_prepare_db()===false) {
    $message_error="Unable to connect to database.<BR>No further execution possible.";
    RenderError($title,$message_error);
    exit();
  }

  // Vendors
  $query = <<<EOD
SELECT
    concat("<A NAME=\"",
      vendor_business_name,
      "\"",
      (if(vendor_website IS NULL,"",concat(" HREF=\"",vendor_website,"\""))),
      ">",
      vendor_business_name,
      "</A>") AS Title,
    if (vendor_space_position IS NULL,"",vendor_space_position) AS Room,
    concat(if (vendor_description IS NULL,"",vendor_description),
      if(vendor_website IS NULL,"",concat("<br>\n<A HREF=\"",vendor_website,"\">",vendor_website,"</A>"))) AS Description
  FROM
      default_vendors_$conid
  WHERE
    vendor_status in ('Approved')
  ORDER BY
    vendor_business_name
EOD;
  list($elements,$header_array,$element_array)=queryreport($query,$vlink,$title,$description,0);

  //If there is multiple rooms, have to split them out, if it is empty, on to the next.
  for($i=1; $i<=$elements; $i++) {
    if (!empty($element_array[$i]['Room'])) {
      $room_array=explode(", ",$element_array[$i]['Room']);
      for ($j=0; $j<count($room_array); $j++) {
	$room_array[$j]="<A NAME=\"".$room_array[$j]."\" HREF=\"#vendor".$room_array[$j]."\">".$room_array[$j]."</A>";
      }
      $element_array[$i]['Room']=implode(", ",$room_array);
    }
  }

  echo "<H3><A NAME=\"VendorStart\"></A><B>Vendors</B><br>(jump to the <A HREF=\"#CommunityStart\">Community Tables</A>";
  if ($vendormap != "") {
    echo " or the <A HREF=\"#VendorMapStart\">Map</A>";
  }
  echo ")</H3>\n";
  $printstring=renderschedreport("desc","","F",$elements,$element_array);
  echo $printstring;

  // Community Tables

  // Fix the inconsistent where string
  $wherestring="status in ('Approved')";
  if ($conid == "44") {$wherestring="vendor_status in ('Approved')";}

  $query = <<<EOD
SELECT
    concat("<A NAME=\"",
      name,
      "\"",
      (if(website IS NULL,"",concat(" HREF=\"",website,"\""))),
      ">",
      name,
      "</A>") AS Title,
    if(vendor_location IS NULL,"",vendor_location) AS Room,
    if(website IS NULL,"",concat("<A HREF=\"",website,"\">",website,"</A>")) AS Description
  FROM
      default_community_tables_$conid
  WHERE
    $wherestring
  ORDER BY
    name
EOD;
  list($elements,$header_array,$element_array)=queryreport($query,$vlink,$title,$description,0);

  //If there is multiple rooms, have to split them out.
  for($i=1; $i<=$elements; $i++) {
    $room_array=explode(", ",$element_array[$i]['Room']);
    for ($j=0; $j<count($room_array); $j++) {
      $room_array[$j]="<A NAME=\"".$room_array[$j]."\" HREF=\"#vendor".$room_array[$j]."\">".$room_array[$j]."</A>";
    }
    $element_array[$i]['Room']=implode(", ",$room_array);
  }

  echo "<H3><A NAME=\"CommunityStart\"></A><B>Community Tables</B><br>(jump to the <A HREF=\"#VendorStart\">Vendors</A>";
  if ($vendormap != "") {
    echo " or the <A HREF=\"#VendorMapStart\">Map</A>";
  }
  echo ")</H3>\n";
  $printstring=renderschedreport("desc","","F",$elements,$element_array);
  echo $printstring;

} else {

$printparticipant="";
for ($i=1; $i<=$elements; $i++) {
  if ($element_array[$i]['Participants'] != $printparticipant) {
    if ($printparticipant != "") {
      echo "    </TD>\n  </TR>\n</TABLE>\n";
      echo "<br>\n";
    }
    $printparticipant=$element_array[$i]['Participants'];
    $bioinfo=getBioData($element_array[$i]['badgeid']);
    /* Presenting the Web, URI and Picture pieces, in whatever
       languages we have, grouping by language, then type.
       Currently we are using raw as the state, due to lack
       of time.  At some point we should move to good. */
    $namecount=0;
    $tablecount=0;
    $biostate='raw'; // for ($l=0; $l<count($bioinfo['biostate_array']); $l++) {
    $biodest='web'; // for ($m=0; $m<count($bioinfo['biodest_array']); $m++) {
    for ($k=0; $k<count($bioinfo['biolang_array']); $k++) {
      $bioout=array();
      for ($j=0; $j<count($bioinfo['biotype_array']); $j++) {

	// Setup for keyname, to collapse all four variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$j];
	$biolang=$bioinfo['biolang_array'][$k];
	// $biostate=$bioinfo['biostate_array'][$l];
	// $biodest=$bioinfo['biodest_array'][$m];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Set up the useful pieces.
	if (isset($bioinfo[$keyname])) {$bioout[$biotype]=$bioinfo[$keyname];}
      }

      // Still in the language switch, but have set the $bioout array.
      if (isset($bioout['picture']) AND ($bioout['picture'] != "")) {
	if ($tablecount == 0) {
	  echo "<TABLE>\n  <TR>\n    <TD valign=top width=310>";
	  $tablecount++;
	} else {
	  echo "    </TD>\n  </TR>\n  <TR>\n    <TD valign=top width=310>";
	}
	echo sprintf("<img width=300 src=\"%s\"</TD>\n<TD>",$bioout['picture']);
      } else {
	if ($tablecount == 0) {
	  echo "<TABLE>\n  <TR>\n    <TD>";
	  $tablecount++;
	}
      }
/*    if (isset($bioout['location']) AND ($bioout['location'] != "")) {
        echo sprintf("<B>%s</B> - %s<br>\n",$printparticipant,$bioout['location']); */
      if ($element_array[$i]['Location'] != "") {
	echo sprintf("<B>%s</B> - %s<br>\n",$printparticipant,$element_array[$i]['Location']);
	$namecount++;
      }
      if (isset($bioout['web']) AND ($bioout['web'] != "")) {
	if ($namecount==0) {
	  $namecount++;
	  echo sprintf("<B>%s:</B><br>%s<br>\n",$printparticipant,$bioout['web']);
	} else {
	  echo sprintf("%s<br>\n",$bioout['web']);
	}
      }
      if (isset($bioout['uri']) AND ($bioout['uri'] != "")) {
	if ($namecount==0) {
	  $namecount++;
	  echo sprintf("<B>%s:</B><br>%s<br>\n",$printparticipant,$bioout['uri']);
	} else {
	  echo sprintf("%s<br>\n",$bioout['uri']);
	}
      }
    }
    // If there were no bios
    if ($namecount==0) { echo sprintf("<P><B>%s</B>",$printparticipant);}
  }
}

}
correct_footer();
?>
