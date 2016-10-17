<?php
require_once('StaffCommonCode.php');
global $link;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="SocialMediaSpreadsheet.php";
$title="Social Media Spreadsheet Starting Point";

$participant_query = <<<EOD
SELECT
    concat('<A HREF=StaffEditCreateParticipant.php?action=edit&partid=',badgeid,'>',pubsname,'</A>') AS Name,
    uri_good_web AS 'URL Web Block',
    uri_good_book AS 'URL Book Block',
    email,
    altcontact
  FROM
      Participants
    LEFT JOIN (SELECT 
                              badgeid,
                              biotext AS uri_good_web
                             FROM
                                 Bios
                               JOIN BioTypes USING (biotypeid)
                               JOIN BioStates USING (biostateid)
                               JOIN BioDests USING (biodestid)
                             WHERE
                                biolang in ('en-us') AND
                                biotypename in ('uri') AND 
                                biostatename in ('good') AND
                                biodestname in ('web')) UGW USING (badgeid)
    LEFT JOIN (SELECT 
                              badgeid,
                              biotext AS uri_good_book
                             FROM
                                 Bios
                               JOIN BioTypes USING (biotypeid)
                               JOIN BioStates USING (biostateid)
                               JOIN BioDests USING (biodestid)
                             WHERE
                                biolang in ('en-us') AND
                                biotypename in ('uri') AND 
                                biostatename in ('good') AND
                                biodestname in ('book')) UGB USING (badgeid)
    JOIN CongoDump USING (badgeid)
    JOIN (SELECT
	      DISTINCT(badgeid)
            FROM
	        ParticipantOnSession
                JOIN Schedule USING (sessionid,conid)
                JOIN Sessions USING (sessionid,conid)
                JOIN Types USING (typeid)
            WHERE
              conid=$conid AND
              typename in ('Panel', 'Class', 'Presentation', 'Author Reading', 'Lounge', 'SIG/BOF/Mng', 'Social', 'Performance') AND
              volunteer not in ('1','Yes') AND
              introducer not in ('1','Yes') AND
              aidedecamp not in ('1','Yes')) as X using (badgeid)
  ORDER BY
    pubsname
EOD;

// Retrieve query
list($participant_rows,$participant_header_array,$participant_array)=queryreport($participant_query,$link,$title,$description,0);

$printstring=renderhtmlreport(1,$participant_rows,$participant_header_array,$participant_array);

// Connect to Vendor Database
if (vendor_prepare_db()===false) {
  $message_error="Unable to connect to database.<BR>No further execution possible.";
  RenderError($title,$message_error);
  exit();
}

//Check to see if the table exists
$pTableExist = mysql_query("show tables like 'default_vendors_".$conid."'");
if ($rTableExist = mysql_fetch_array($pTableExist)) {
  // Fix for inconsistencies in the database
  $vstatus="vendor_status";
  if ($conid == "45") {
    $vstatus="status";
  }

  // Vendors
  $vendor_query = <<<EOD
SELECT
    concat("<A NAME=\"",
      vendor_business_name,
      "\"",
      (if(vendor_website IS NULL,"",concat(" HREF=\"",vendor_website,"\""))),
      ">",
      vendor_business_name,
      "</A>") AS Vendor,
    vendor_sponsorship_package AS Sponsor,
    print_advertising,
    digital_advertising,
    vendor_contact_email AS "Contact Email",
    concat("<A HREF=\"",vendor_website,"\">",vendor_website,"</A>") AS Website
  FROM
      default_vendors_$conid
  WHERE
    $vstatus in ('Approved', 'Accepted')
  ORDER BY
    vendor_business_name
EOD;
  list($vendor_rows,$vendor_header_array,$vendor_array)=queryreport($vendor_query,$vlink,$title,$description,0);

  $printstring.=renderhtmlreport(1,$vendor_rows,$vendor_header_array,$vendor_array);

  // Add the description once it starts to exist
  $desc="NULL";
  if (($conid == "45") or ($conid == "46") or ($conid == "47") or ($conid == "48")) { $desc="vendor_description"; }

  // Fix for inconsistencies in the database
  $website="website";
  if ($conid == "45") { $website="vendor_website"; }

  $status="status";
  if (($conid == "44") or ($conid == "46") or ($conid == "47") or ($conid == "48")) { $status="vendor_status"; }

  $wherestring="WHERE $status in ('Approved','Accepted')";
  if ($conid == "45") { $wherestring="WHERE vendor_location is NOT NULL"; }

  $community_query = <<<EOD
SELECT
    concat("<A NAME=\"",
      name,
      "\"",
      (if($website IS NULL,"",concat(" HREF=\"",$website,"\""))),
      ">",
      name,
      "</A>") AS "Community Table",
    email,
    contact_email AS "Contact Email",
    concat("<A HREF=\"",website,"\">",website,"</A>") AS Website
  FROM
      default_community_tables_$conid
  $wherestring
  ORDER BY
    name
EOD;

  list($community_rows,$community_header_array,$community_array)=queryreport($community_query,$vlink,$title,$description,0);

  $printstring.=renderhtmlreport(1,$community_rows,$community_header_array,$community_array);
}

topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo $printstring;
correct_footer();
?>
