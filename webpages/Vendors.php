<?php
require_once('PostingCommonCode.php');
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');
global $link;

/* takes a variable, and searches across all the posible variations to
   see if said variable exists in the bios-set.  This checks for the
   dest web then book across the state good, then edited, then raw.
 */
function getBioDestEdit($biotype,$biolang,$bioinfo) {
  if (!empty($bioinfo[$biotype.'_'.$biolang.'_good_web_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_good_web_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_good_book_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_good_book_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_raw_web_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_raw_web_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_raw_book_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_raw_book_bio'];
  } else {
    $bioout="";
  }
  return ($bioout);
}

/* Take the badgeid and searches for the picture.
   This is web then book. */
function getPictureDestEdit($checkbadge) {
  $picture="";
  $pictureweb="../Local/Participant_Images_web/$checkbadge";
  $picturebook="../Local/Participant_Images_book/$checkbadge";
  if (file_exists($pictureweb)) {
    $picture=sprintf("<img width=300 src=\"%s\">",$pictureweb);
  } elseif (file_exists($picturebook)) {
    $picture=sprintf("<img width=300 src=\"%s\">",$picturebook);
  }
  return ($picture);
}

// Pass in variables
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Format is desc for the renderschedreport.
$format="desc";

// No header breakpoint for now.
$header_break="";

// No short/single line for now.
$single_line_p="F";

// No printing of this for now.
$print_p="F";

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

// LOCALIZATIONS
$_SESSION['return_to_page']="Vendors.php?conid=$conid";
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
    DISTINCT pubsname AS 'Participants',
    if((secondtitle!=''),concat('<A NAME=\"', sessionid, '\">', secondtitle, '</A>'),"") AS 'Location',
    pubsname,
    badgeid
  FROM
      Participants
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
    JOIN VendorStatus USING (badgeid,conid)
    JOIN VendorStatusTypes USING (vendorstatustypeid)
    LEFT JOIN ParticipantOnSession USING (badgeid,conid)
    LEFT JOIN Sessions USING (sessionid,conid)
  WHERE
    vendorstatustypename in ('Accepted') AND
    permrolename in ('Vendor') AND
    conid=$conid
  ORDER BY
  pubsname
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the vendor bio page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
if ($vendormap != "") {
  echo "<H3><A NAME=\"VendorMapStart\"></A><B>Map</B><br>(jump to the <A HREF=\"#VendorStart\">Vendors</A>";
  echo " or the <A HREF=\"#CommunityStart\">Community Tables</A>)</H3>\n";
  echo $vendormap;
}
echo $vendorpdfmap;
echo $vendorlist;


// Connect to Vendor Database
if (vendor_prepare_db()===false) {
  $message_error="Unable to connect to database.<BR>No further execution possible.";
  RenderError($title,$message_error);
  exit();
}

//Check to see if the table exists - 42, 43, 44, 45, 46, 47, 48, 49
$pTableExist = mysql_query("show tables like 'default_vendors_".$conid."'");
if ($rTableExist = mysql_fetch_array($pTableExist)) {

  // Fix for inconsistencies in the database
  $vstatus="vendor_status";
  if ($conid == "45") {
    $vstatus="status";
  }

  // Vendors
  $query = <<<EOD
SELECT
    concat("<A NAME=\"",
      vendor_business_name,
      "\"",
      (if(vendor_website IS NULL,"",concat(" HREF=\"",vendor_website,"\" target=\"_blank\""))),
      ">",
      vendor_business_name,
      "</A>") AS Title,
    if (vendor_location IS NULL,"",vendor_location) AS Room,
    concat(if (vendor_description IS NULL,"",vendor_description),
      if(vendor_website IS NULL,"",concat("<br>\n<A HREF=\"",vendor_website,"\" target=\"_blank\">",vendor_website,"</A>"))) AS Description
  FROM
      default_vendors_$conid
  WHERE
      $vstatus in ('Approved')
  ORDER BY
    vendor_business_name
EOD;
  list($elements,$header_array,$element_array)=queryreport($query,$vlink,$title,$description,0);
  $vendorquery=$query;

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
  // echo "<P>Query=$vendorquery</P>";
  if ($vendormap != "") {
    echo " or the <A HREF=\"#VendorMapStart\">Map</A>";
  }
  echo ")</H3>\n";
  $printstring=renderschedreport($format,$header_break,$single_line_p,$print_p,$elements,$element_array);
  echo $printstring;

  // Community Tables - 42, 43, 44, 45, 46, 47, 48, 49

  // Add the description once it starts to exist
  $desc="NULL";
  if (($conid == "45") or ($conid == "46") or ($conid == "47") or ($conid == "48") or ($conid == "49")) { $desc="vendor_description"; }

  // Fix for inconsistencies in the database
  $website="website";
  if ($conid == "45") { $website="vendor_website"; }

  $status="status";
  if (($conid == "44") or ($conid == "46") or ($conid == "47") or ($conid == "48") or ($conid == "49")) { $status="vendor_status"; }

  $wherestring="WHERE $status in ('Approved')";
  if ($conid == "45") { $wherestring="WHERE vendor_location is NOT NULL"; }

  $query = <<<EOD
SELECT
    concat("<A NAME=\"",
      name,
      "\"",
      (if($website IS NULL,"",concat(" HREF=\"",$website,"\""))),
      ">",
      name,
      "</A>") AS Title,
    if(vendor_location IS NULL,"",vendor_location) AS Room,
    concat(if($desc IS NULL,"",$desc),
           if(($desc IS NULL or $website IS NULL),"","<br>\n"),
           if($website IS NULL,"",concat("<A HREF=\"",$website,"\">",$website,"</A>"))) AS Description
  FROM
      default_community_tables_$conid
  $wherestring
  ORDER BY
    name
EOD;

  list($elements,$header_array,$element_array)=queryreport($query,$vlink,$title,$description,0);
  $communityquery=$query;
  //If there is multiple rooms, have to split them out.
  for($i=1; $i<=$elements; $i++) {
    $room_array=explode(", ",$element_array[$i]['Room']);
    for ($j=0; $j<count($room_array); $j++) {
      $room_array[$j]="<A NAME=\"".$room_array[$j]."\" HREF=\"#vendor".$room_array[$j]."\">".$room_array[$j]."</A>";
    }
    $element_array[$i]['Room']=implode(", ",$room_array);
  }

  echo "<H3><A NAME=\"CommunityStart\"></A><B>Community Tables</B><br>(jump to the <A HREF=\"#VendorStart\">Vendors</A>";
  // echo "<P>Query=$communityquery</P>";
  if ($vendormap != "") {
    echo " or the <A HREF=\"#VendorMapStart\">Map</A>";
  }
  echo ")</H3>\n";
  $printstring=renderschedreport($format,$header_break,$single_line_p,$print_p,$elements,$element_array);
  echo $printstring;

} else { // Pulled from Zambia.

  // Establish the bios element
  if ($short == "T") {
    $header="";
    for ($i=1; $i<=$elements; $i++) {
      if ($element_array[$i]['Participants'] != $header) {
	$header=$element_array[$i]['Participants'];
	$biostring=sprintf("<P>&nbsp;</P>\n<HR><H3>%s</H3>\n",$header);
      }
      $element_array[$i]['Bio']=$biostring;
    }
  } else { // Not Short
    $header="";
    for ($i=1; $i<=$elements; $i++) {
      if ($element_array[$i]['Participants'] != $header) {
	$tablecount=0;
	$header=$element_array[$i]['Participants'];
	$biostring="<P>&nbsp;</P>\n";

	// Get the bio information
	$bioinfo=getBioData($element_array[$i]['badgeid']);

	// For each language in our language array
	for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
	  $biolang=$bioinfo['biolang_array'][$j];

	  // If there is a picture
	  $picture="";
	  if ($pic_p == "T") {
	    $picture=getPictureDestEdit($element_array[$i]['badgeid']);
	  }

	  // Set their name
	  $name=getBioDestEdit('name',$biolang,$bioinfo);
	  if ($name == "") {
	    $name=$header;
	  }

	  $name="<A NAME=\"$name\"></A>$name";

	  // If there is a bio
	  $bio=getBioDestEdit('bio',$biolang,$bioinfo);

	  // If there is a URI line
	  $uri=getBioDestEdit('uri',$biolang,$bioinfo);

	  // If there is a twitter line
	  $twitter=getBioDestEdit('twitter',$biolang,$bioinfo);

	  // If there is a facebook line
	  $facebook=getBioDestEdit('facebook',$biolang,$bioinfo);

	  // If there is a fetlife line
	  $fetlife=getBioDestEdit('fetlife',$biolang,$bioinfo);

	  // If there is a DBA line
	  $dba=getBioDestEdit('dba',$biolang,$bioinfo);

	  if ($picture != "") {
	    if ($tablecount == 0) {
	      $biostring.="<TABLE>\n  <TR>\n    <TD valign=\"top\" width=310>";
	      $tablecount++;
	    } else {
	      $biostring.="    </TD>\n  </TR>\n  <TR>\n    <TD width=310>";
	    }
	    $biostring.=sprintf("%s</TD>\n    <TD>",$picture);
	  }

	  $biostring.=sprintf("<P><B>%s",$name);

	  if ($dba != "") {
	    $biostring.=" (DBA: $dba)";
	  }

	  // If there is a location
	  if ($element_array[$i]['Location'] != "") {
	    $biostring.=" - " . $element_array[$i]['Location'];
	  }

          $biostring.="</B><br />\n";

	  if ($bio != "") {
	    $biostring.=$bio;
	  }

	  $biostring.="</P>\n";

	  if ($twitter != "") {
	    $biostring.=sprintf("<P>Twitter: <A HREF=\"https://twitter.com/%s\" target=\"_blank\">@%s</A></P>\n",$twitter,$twitter);
	  }

	  if ($facebook != "") {
	    $biostring.=sprintf("<P>Facebook: <A HREF=\"https://facebook.com/%s\" target=\"_blank\">@%s</A></P>\n",$facebook,$facebook);
	  }

	  if ($fetlife != "") {
	    $biostring.=sprintf("<P>FetLife: <A HREF=\"https://fetlife.com/%s\" target=\"_blank\">%s</A></P>\n",$fetlife,$fetlife);
	  }

	  if ($uri != "") {
	    $biostring.=sprintf("<P>%s</P>\n",$uri);
	  }

	} // End of Language Switch

	$element_array[$i]['Bio']=$biostring;
	$element_array[$i]['istable']=$tablecount;
      } else {  // if it is the same in the 'Participants' field, just copy the result in.
	$element_array[$i]['Bio']=$biostring;
	$element_array[$i]['istable']=$tablecount;
      }
    }
  }
  $format="bios";
  $header_break="Participants";
  $single_line_p="T";

  /* Produce the report. */
  $printstring=renderschedreport($format,$header_break,$single_line_p,$print_p,$elements,$element_array);

  // Display, with the option of printing.
  if ($print_p == "F") {
    echo $printstring;
    correct_footer();
  } else {
    $printstring->Output('Schedule'.$format.'All.pdf', 'I');
  }
}

correct_footer();
?>
