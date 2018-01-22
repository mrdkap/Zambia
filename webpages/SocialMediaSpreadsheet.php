<?php
require_once('StaffCommonCode.php');
global $link;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted
$conname=$_SESSION['conname']; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="SocialMediaSpreadsheet.php";
$title="Social Media Spreadsheet Starting Point";
$description="<P>A collection of Social Media information for use.</P>";
$additionalinfo.="<P>A <A HREF=\"SocialMediaSpreadsheet.php?csv=Y\"";
$additionalinfo.=" target=\"_blank\">CSV</A> of all tables.</P>\n";
$additionalinfo.="<P>Still need to add requested sponsorships,";
$additionalinfo.=" print ads, and digital ads, no idea how to";
$additionalinfo.=" tell yet if they have been billed, so don't";
$additionalinfo.=" know if that's relevant to social media at";
$additionalinfo.=" all.</P>\n";

$namewhere[1]='"Presenters"';
$csvname[1]=$conname . "_Social_Media_Presenters.csv";
$wherestring_array[1]=<<<EOD
SELECT
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
     aidedecamp not in ('1','Yes')
EOD;

$namewhere[2]='"Vendors"';
$csvname[2]=$conname . "_Social_Media_Vendors.csv";
$wherestring_array[2]=<<<EOD
SELECT
    DISTINCT(badgeid)
  FROM
      VendorStatus
    JOIN VendorStatusTypes USING (vendorstatustypeid)
  WHERE
    vendorstatustypename in ('Accepted') AND
    badgeid not in (SELECT
        badgeid
      FROM
          VendorPrefSpace
        JOIN VendorSpace USING (vendorspaceid)
        JOIN BaseVendorSpace USING (basevendorspaceid)
      WHERE
         basevendorspacename like "%Community Table%" AND
         conid=$conid) AND
    conid=$conid
EOD;

$namewhere[3]='"Community Tables"';
$csvname[3]=$conname . "_Social_Media_Community_Tables.csv";
$wherestring_array[3]=<<<EOD
SELECT
    DISTINCT(badgeid)
  FROM
      VendorStatus
    JOIN VendorStatusTypes USING (vendorstatustypeid)
  WHERE
    vendorstatustypename in ('Accepted') AND
    badgeid in (SELECT
        badgeid
      FROM
          VendorPrefSpace
        JOIN VendorSpace USING (vendorspaceid)
        JOIN BaseVendorSpace USING (basevendorspaceid)
      WHERE
         basevendorspacename like "%Community Table%" AND
         conid=$conid) AND
    conid=$conid
EOD;

$namewhere[4]='"Sponsors"';
$csvname[4]=$conname . "_Social_Media_Sponsors.csv";
$wherestring_array[4]=<<<EOD
SELECT
    DISTINCT(badgeid)
  FROM
      UserHasConRole
    JOIN ConRoles USING (conroleid)
  WHERE
    conrolename in ('Sponsor') AND
    conid=$conid
EOD;

for ($i=1; $i<=count($wherestring_array); $i++) {

  $namestring=$namewhere[$i];
  $wherestring=$wherestring_array[$i];

  $participant_query = <<<EOD
SELECT
    concat('<A HREF=StaffEditCreateParticipant.php?action=edit&partid=',badgeid,'>',pubsname,'</A>') AS $namestring,
    uri_good_web AS 'URL Web Block',
    uri_good_book AS 'URL Book Block',
    facebook_good_web AS "Facebook",
    fetlife_good_web AS "FetLife",
    twitter_good_web AS "Twitter",
    email,
    altcontact,
    concat("") AS "Notes"
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
    LEFT JOIN (SELECT 
        badgeid,
        biotext AS fetlife_good_web
      FROM
          Bios
        JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
        biolang in ('en-us') AND
        biotypename in ('fetlife') AND 
        biostatename in ('good') AND
        biodestname in ('web')) FEGW USING (badgeid)
    LEFT JOIN (SELECT 
        badgeid,
        biotext AS facebook_good_web
      FROM
          Bios
        JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
        biolang in ('en-us') AND
        biotypename in ('facebook') AND 
        biostatename in ('good') AND
        biodestname in ('web')) FAGW USING (badgeid)
    LEFT JOIN (SELECT 
        badgeid,
        biotext AS twitter_good_web
      FROM
          Bios
        JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
        biolang in ('en-us') AND
        biotypename in ('twitter') AND 
        biostatename in ('good') AND
        biodestname in ('web')) TWGW USING (badgeid)
  WHERE
    badgeid in ($wherestring)
  ORDER BY
    pubsname
EOD;

  // Retrieve query
  list($participant_rows,$participant_header_array,$participant_array)=queryreport($participant_query,$link,$title,$description,0);

  // If a category is empty:
  if ($participant_rows == 0) {
    $participant_header_array[0]="No $namestring at this time.";
  }

  $printstring.="<P><A HREF=\"SocialMediaSpreadsheet.php?csv=$i\" target=\"_blank\">CSV</A> of the below table.</P>\n";
  $printstring.=renderhtmlreport(1,$participant_rows,$participant_header_array,$participant_array);
  $csvprint[$i]=rendercsvreport(1,$participant_rows,$participant_header_array,$participant_array);
  $printstring.="<P><A HREF=\"SocialMediaSpreadsheet.php?csv=$i\" target=\"_blank\">CSV</A> of the above table.</P>\n";
}

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

  $i++;
  $csvname[$i]=$conname . "_Social_Media_Vendors.csv";
  $printstring.="<P><A HREF=\"SocialMediaSpreadsheet.php?csv=$i\" target=\"_blank\">CSV</A> of the below table.</P>\n";
  $printstring.=renderhtmlreport(1,$vendor_rows,$vendor_header_array,$vendor_array);
  $csvprint[$i].=rendercsvreport(1,$vendor_rows,$vendor_header_array,$vendor_array);
  $printstring.="<P><A HREF=\"SocialMediaSpreadsheet.php?csv=$i\" target=\"_blank\">CSV</A> of the above table.</P>\n";

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

  $i++;
  $csvname[$i]=$conname . "_Social_Media_Community_Tables.csv";
  $printstring.="<P><A HREF=\"SocialMediaSpreadsheet.php?csv=$i\" target=\"_blank\">CSV</A> of the below table.</P>\n";
  $printstring.=renderhtmlreport(1,$community_rows,$community_header_array,$community_array);
  $csvprint[$i].=rendercsvreport(1,$community_rows,$community_header_array,$community_array);
  $printstring.="<P><A HREF=\"SocialMediaSpreadsheet.php?csv=$i\" target=\"_blank\">CSV</A> of the above table.</P>\n";
}

// Walk the possible CSV reports
$csv_p=0;
for ($j=1; $j<=$i; $j++) {
  if ($_GET['csv']==$j) {
    $csv_p++;
    topofpagecsv($csvname[$j]);
    echo $csvprint[$j];
  }
  $csvall.=$csvprint[$j];
}

// If all the CVS give that, else if there are no CSV reports, write out the collected tables
if ($_GET['csv']=="Y") {
  topofpagecsv($conname . "_Social_Media.csv");
  echo $csvall;
} elseif ($csv_p == 0) {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo $printstring;
  correct_footer();
}
?>
