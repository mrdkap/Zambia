<?php
require_once('StaffCommonCode.php');
global $link;
$title="KonOpas Data Generator";
$description="<P>Generated Data for:</P>\n";

// Pass in variables
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}
if ($conid=="") {$conid=$_SESSION['conid'];}

// Set the conname from the conid
$conquery="SELECT conname,connumdays,conurl,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($conquery,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$conurl=$conname_array[1]['conurl'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

// Set up the cardinal to ordinal mapping
$ordinal[0]="";
$ordinal[1]="2nd";
$ordinal[2]="3rd";
$ordinal[3]="4th";
$ordinal[4]="5th";

// Program (session element) information

$programquery = <<<EOD
SELECT
    concat('"id": "',sessionid,'"') AS id,
    concat('"title": "',replace(title_good_web,'"',"''"),if((subtitle_good_web IS NULL),"",concat(": ",replace(subtitle_good_web,'"',"''"))),'"') AS title,
    concat('"tags": [ "',trackname,'", "',typename,'"',if(title_good_web like "%MASTER CLASS%",', "Master Class"',""), ' ]') AS tags,
    DATE_FORMAT(ADDTIME(constartdate,starttime),'"date": "%Y-%m-%d", "time":  "%H:%i"') AS time,
    concat('"mins": "',TIME_TO_SEC(duration) div 60,'"') AS mins,
    concat('"loc": [ "',roomname,'" ]') AS loc,
    concat('"people": [ ',if ((pubsname is NULL), " ", GROUP_CONCAT(DISTINCT '{ "id": "',badgeid,'", "name": "',pubsname,'" }' SEPARATOR ", ")),' ]') AS people,
    concat('"desc": "',if(title_good_web like "%MASTER CLASS%",'<A HREF=https://t.co/hNZv8BjRy2 target=_blank>Sign Up</A><br />',""),if(desc_good_web IS NULL,"",replace(desc_good_web,'"',"''")),'"') AS 'desc'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    JOIN Types USING (typeid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
    JOIN Phase USING (conid)
    JOIN PhaseTypes USING (phasetypeid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('title') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') TGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('subtitle') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
          descriptionlang='en-us') SGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') DGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('book') AND
          descriptionlang='en-us') DGB USING (sessionid,conid)
  WHERE
    conid=$conid AND
    phasetypename like "%Feedback Available%" AND
    pubstatusname in ('Public') AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  GROUP BY
    sessionid
EOD;

// Retrieve query
list($elementrows,$header_array,$element_array)=queryreport($programquery,$link,$title,$description,0);

$program.="var program = [\n";

for ($i=1; $i<=$elementrows; $i++) {
  $program.="    {\n";
  $program.="        " . $element_array[$i]['id'] . ",\n";
  $program.="        " . $element_array[$i]['title'] . ",\n";
  $program.="        " . $element_array[$i]['tags'] . ",\n";
  $program.="        " . $element_array[$i]['time'] . ",\n";
  $program.="        " . $element_array[$i]['mins'] . ",\n";
  $program.="        " . $element_array[$i]['loc'] . ",\n";
  $program.="        " . $element_array[$i]['people'] . ",\n";
  $program.="        " . str_replace("\n"," ",$element_array[$i]['desc']) . "\n";
  $program.="    },\n";
}

// Remove the trailing comma
$program=rtrim(trim($program), ',');

$program.="\n];";

// Bios Information

$bioquery = <<<EOD
SELECT
    concat('"id": "',badgeid,'"') AS id,
    concat('"name": [ "',name_good_web,'" ]') AS name,
    concat('"tags": []') AS tags,
    concat('"prog": [ ',GROUP_CONCAT(DISTINCT '"',sessionid,'"' SEPARATOR ", "),' ]') AS prog,
    badgeid,
    if(facebook_good_web IS NULL,"",replace(facebook_good_web,'"','')) AS facebook,
    if(twitter_good_web IS NULL,"",replace(twitter_good_web,'"','')) AS twitter,
    if(fetlife_good_web IS NULL,"",replace(fetlife_good_web,'"','')) AS fetlife,
    if(uri_good_web IS NULL,"",replace(uri_good_web,'"','')) AS uri,
    concat('"bio": "',name_good_web,if(bio_good_web IS NULL,"",replace(bio_good_web,'"',"''")),if(pronoun_good_web IS NULL,"",concat(" Preferred Pronoun: ",pronoun_good_web)),'" ') AS bio
  FROM
      ParticipantOnSession
    JOIN Sessions USING (sessionid,conid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN SessionStatuses USING (statusid)
    JOIN ConInfo USING (conid)
    JOIN (SELECT
        badgeid,
	biotext as name_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('name') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  biolang='en-us') NGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as bio_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('bio') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
          biolang='en-us') BGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as uri_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('uri') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  biolang='en-us') UGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as facebook_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('facebook') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  biolang='en-us') FBGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as twitter_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('twitter') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  biolang='en-us') TWGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as fetlife_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('fetlife') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  biolang='en-us') FLGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as pronoun_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('pronoun') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
          biolang='en-us') PGW USING (badgeid)
  WHERE
    conid=$conid AND
    pubstatusname in ('Public') AND
    statusname in ('Scheduled', 'Assigned') AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  GROUP BY
    badgeid
  ORDER BY
    name_good_web
EOD;

// Retrieve query
list($biorows,$bioheader_array,$bio_array)=queryreport($bioquery,$link,$title,$description,0);

$bios.="var people = [\n";

for ($i=1; $i<=$biorows; $i++) {
  $bios.="    {\n";
  $bios.="        " . $bio_array[$i]['id'] . ",\n";
  $bios.="        " . $bio_array[$i]['name'] . ",\n";
  $bios.="        " . $bio_array[$i]['tags'] . ",\n";
  $bios.="        " . $bio_array[$i]['prog'] . ",\n";
  $tmp_links="";
  if (file_exists("../Local/Participant_Images_web/" . $bio_array[$i]['badgeid'])) {
    $tmp_links.="\n" . '            "img": "http://' . $conurl . '/Local/Participant_Images_web/' . $bio_array[$i]['badgeid'] . '"';
  }
  if (!empty($bio_array[$i]['facebook'])) {
    $fb_array=preg_split("/[\s,]+/",$bio_array[$i]['facebook']);
    $ordcount=0;
    foreach ($fb_array as $fb_element) {
      if (!empty($tmp_links)) {$tmp_links.=',';}
      $tmp_links.="\n" . '            "fb' . $ordinal[$ordcount] . '" : "' . $fb_element . '"';
      $ordcount++;
    }
  }
  if (!empty($bio_array[$i]['twitter'])) {
    $tw_array=preg_split("/[\s,]+/",$bio_array[$i]['twitter']);
    $ordcount=0;
    foreach ($tw_array as $tw_element) {
      if (!empty($tmp_links)) {$tmp_links.=',';}
      $tmp_links.="\n" . '            "twitter' . $ordinal[$ordcount] . '" : "' . $tw_element . '"';
      $ordcount++;
    }
  }
  if (!empty($bio_array[$i]['fetlife'])) {
    $fl_array=preg_split("/[\s,]+/",$bio_array[$i]['fetlife']);
    $ordcount=0;
    foreach ($fl_array as $fl_element) {
      if (!empty($tmp_links)) {$tmp_links.=',';}
      $tmp_links.="\n" . '            "fl' . $ordinal[$ordcount] . '" : "' . $fl_element . '"';
      $ordcount++;
    }
  }
  if (!empty($bio_array[$i]['uri'])) {
    if (!empty($tmp_links)) {$tmp_links.=',';}
    $tmp_links.="\n" . '            "url" : "' . $bio_array[$i]['uri'] . '"';
  }
  if (!empty($tmp_links)) {
    $tmp_links='"links": { ' . $tmp_links . "\n" . '        }';
    $bios.="        " . str_replace("\n"," ",$tmp_links) . ",\n";
  }
  $bios.="        " . str_replace("\n"," ",$bio_array[$i]['bio']) . "\n";
  $bios.="    },\n";
}

// Remove the trailing comma
$bios=rtrim(trim($bios), ',');

$bios.="\n];";

// Staff Job Descriptions

$staffdescquery = <<<EOD
SELECT
    concat('"id": "',CRA.conroleid,'"') AS id,
    concat('"title": "',CRA.conrolenotes,'"') AS title,
    concat('"tags": [ "" ], "date": "", "time": "", "mins": "", "loc": [ "" ]') AS empty,
    concat('"people": [ ',if ((name_good_staffweb is NULL), " ", GROUP_CONCAT(DISTINCT '{ "id": "',badgeid,'", "name": "',name_good_staffweb,'" }' SEPARATOR ", ")),' ]') AS people,
    concat('"desc": "', CRA.conroledescription, '"') AS 'desc'
  FROM
      ConRoles CRA
    JOIN HasReports USING (conroleid)
    LEFT JOIN UserHasConRole UHCR USING (conroleid, conid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as name_good_staffweb
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('name') AND
	  biostatename in ('good') AND
	  biodestname in ('staffweb') AND
	  biolang='en-us') NGS USING (badgeid)
  WHERE
    conid=$conid
  GROUP BY
    conroleid
  ORDER BY
    display_order,
    conrolename
EOD;

// Retrieve query
list($elementrows,$header_array,$element_array)=queryreport($staffdescquery,$link,$title,$description,0);

$staffdesc.="var program = [\n";

for ($i=1; $i<=$elementrows; $i++) {
  $staffdesc.="    {\n";
  $staffdesc.="        " . $element_array[$i]['id'] . ",\n";
  $staffdesc.="        " . $element_array[$i]['title'] . ",\n";
  $staffdesc.="        " . $element_array[$i]['empty'] . ",\n";
  $staffdesc.="        " . $element_array[$i]['people'] . ",\n";
  $staffdesc.="        " . str_replace("\n"," ",$element_array[$i]['desc']) . "\n";
  $staffdesc.="    },\n";
}

// Remove the trailing comma
$staffdesc=rtrim(trim($staffdesc), ',');

$staffdesc.="\n];";

// Staff Bios Information

$staffbioquery = <<<EOD
SELECT
    concat('"id": "',badgeid,'"') AS id,
    concat('"name": [ "',name_good_staffweb,'" ]') AS name,
    concat('"tags": []') AS tags,
    concat('"prog": [ ',GROUP_CONCAT(DISTINCT '"',conroleid,'"' SEPARATOR ", "),' ]') AS prog,
    badgeid,
    if(facebook_good_staffweb IS NULL,"",replace(facebook_good_staffweb,'"','')) AS facebook,
    if(twitter_good_staffweb IS NULL,"",replace(twitter_good_staffweb,'"','')) AS twitter,
    if(fetlife_good_staffweb IS NULL,"",replace(fetlife_good_staffweb,'"','')) AS fetlife,
    if(uri_good_staffweb IS NULL,"",replace(uri_good_staffweb,'"','')) AS uri,
    concat('"bio": "',if(bio_good_staffweb IS NULL,"",replace(bio_good_staffweb,'"',"''")),if(pronoun_good_staffweb IS NULL,"",concat(" Preferred Pronoun: ",pronoun_good_staffweb)),'" ') AS bio
  FROM
      UserHasConRole
    JOIN HasReports USING (conroleid, conid)
    JOIN (SELECT
        badgeid,
	biotext as name_good_staffweb
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('name') AND
	  biostatename in ('good') AND
	  biodestname in ('staffweb') AND
	  biolang='en-us') NGS USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as bio_good_staffweb
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('bio') AND
	  biostatename in ('good') AND
	  biodestname in ('staffweb') AND
          biolang='en-us') BGS USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as uri_good_staffweb
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('uri') AND
	  biostatename in ('good') AND
	  biodestname in ('staffweb') AND
	  biolang='en-us') UGS USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as facebook_good_staffweb
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('facebook') AND
	  biostatename in ('good') AND
	  biodestname in ('staffweb') AND
	  biolang='en-us') FBGS USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as twitter_good_staffweb
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('twitter') AND
	  biostatename in ('good') AND
	  biodestname in ('staffweb') AND
	  biolang='en-us') TWGS USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as fetlife_good_staffweb
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('fetlife') AND
	  biostatename in ('good') AND
	  biodestname in ('staffweb') AND
	  biolang='en-us') FLGS USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as pronoun_good_staffweb
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('pronoun') AND
	  biostatename in ('good') AND
	  biodestname in ('staffweb') AND
          biolang='en-us') PGS USING (badgeid)
  WHERE
    conid=$conid
  GROUP BY
    badgeid
  ORDER BY
    name_good_staffweb
EOD;

// Retrieve query
list($staffbiorows,$staffbioheader_array,$staffbio_array)=queryreport($staffbioquery,$link,$title,$description,0);

$staffbios.="var people = [\n";

for ($i=1; $i<=$staffbiorows; $i++) {
  $staffbios.="    {\n";
  $staffbios.="        " . $staffbio_array[$i]['id'] . ",\n";
  $staffbios.="        " . $staffbio_array[$i]['name'] . ",\n";
  $staffbios.="        " . $staffbio_array[$i]['tags'] . ",\n";
  $staffbios.="        " . $staffbio_array[$i]['prog'] . ",\n";
  $tmp_links="";
  if (file_exists("../Local/Participant_Images_staffweb/" . $staffbio_array[$i]['badgeid'])) {
    $tmp_links.="\n" . '            "img": "http://' . $conurl . '/Local/Participant_Images_staffweb/' . $staffbio_array[$i]['badgeid'] . '"';
  }
  if (!empty($staffbio_array[$i]['facebook'])) {
    $fb_array=preg_split("/[\s,]+/",$staffbio_array[$i]['facebook']);
    $ordcount=0;
    foreach ($fb_array as $fb_element) {
      if (!empty($tmp_links)) {$tmp_links.=',';}
      $tmp_links.="\n" . '            "fb' . $ordinal[$ordcount] . '" : "' . $fb_element . '"';
      $ordcount++;
    }
  }
  if (!empty($staffbio_array[$i]['twitter'])) {
    $tw_array=preg_split("/[\s,]+/",$staffbio_array[$i]['twitter']);
    $ordcount=0;
    foreach ($tw_array as $tw_element) {
      if (!empty($tmp_links)) {$tmp_links.=',';}
      $tmp_links.="\n" . '            "twitter' . $ordinal[$ordcount] . '" : "' . $tw_element . '"';
      $ordcount++;
    }
  }
  if (!empty($staffbio_array[$i]['fetlife'])) {
    $fl_array=preg_split("/[\s,]+/",$staffbio_array[$i]['fetlife']);
    $ordcount=0;
    foreach ($fl_array as $fl_element) {
      if (!empty($tmp_links)) {$tmp_links.=',';}
      $tmp_links.="\n" . '            "fl' . $ordinal[$ordcount] . '" : "' . $fl_element . '"';
      $ordcount++;
    }
  }
  if (!empty($staffbio_array[$i]['uri'])) {
    if (!empty($tmp_links)) {$tmp_links.=',';}
    $tmp_links.="\n" . '            "url" : "' . $staffbio_array[$i]['uri'] . '"';
  }
  if (!empty($tmp_links)) {
    $tmp_links='"links": { ' . $tmp_links . "\n" . '        }';
    $staffbios.="        " . str_replace("\n"," ",$tmp_links) . ",\n";
  }
  $staffbios.="        " . str_replace("\n"," ",$staffbio_array[$i]['bio']) . "\n";
  $staffbios.="    },\n";
}

// Remove the trailing comma
$staffbios=rtrim(trim($staffbios), ',');

$staffbios.="\n];";

// Vendor information

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

  // Vendor query should contain an image that is the logo?

  $vendorquery=<<<EOD
SELECT
    concat('"id": "',id,'"') AS ID,
    concat('"name": [ "',vendor_business_name,if(vendor_location IS NULL,"",if((vendor_location > 100),concat(' (Room: ',vendor_location,')'),concat(' (Booth: ',vendor_location,')'))), '" ]') AS name,
    concat('"tags": []') AS tags,
    concat('"prog": []') AS prog,
    concat('"links": { ',if(vendor_website IS NULL,"",concat('\n            "url" : "',replace(vendor_website,'"',''),'"')),'\n        }') AS links,
    concat('"bio": "',if(vendor_description IS NULL,"",replace(vendor_description,'"',"''")),'" ') AS bio
  FROM
      default_vendors_$conid
  WHERE
    $vstatus in ('Approved')
  ORDER BY
    vendor_business_name
EOD;

  // Retrieve query
  list($vendorrows,$vendorheader_array,$vendor_array)=queryreport($vendorquery,$vlink,$title,$description,0);

  $vendors.="var vendor = [\n";

  for ($i=1; $i<=$vendorrows; $i++) {
    $vendors.="    {\n";
    $vendors.="        " . $vendor_array[$i]['ID'] . ",\n";
    $vendors.="        " . $vendor_array[$i]['name'] . ",\n";
    $vendors.="        " . $vendor_array[$i]['tags'] . ",\n";
    $vendors.="        " . $vendor_array[$i]['prog'] . ",\n";
    $vendors.="        " . str_replace("\n"," ",$vendor_array[$i]['links']) . ",\n";
    $vendors.="        " . str_replace("\n"," ",$vendor_array[$i]['bio']) . "\n";
    $vendors.="    },\n";
  }

  // Remove the trailing comma
  $vendors=rtrim(trim($vendors), ',');

  $vendors.="\n];";

  // Continuing with Community Tables, presuming that if the Vendor table exists,
  // so does the Community Table table.  (Yes, that's an awkward construction.)

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

  $comtblquery=<<<EOD
SELECT
    concat('"id": "',id,'"') AS ID,
    concat('"name": [ "',name,if(vendor_location IS NULL,"",if((vendor_location > 100),concat(' (Room: ',vendor_location,')'),concat(' (Booth: ',vendor_location,')'))), '" ]') AS Name,
    concat('"tags": []') AS tags,
    concat('"prog": []') AS prog,
    concat('"links": { ',if($website IS NULL,"",concat('\n            "url" : "',replace($website,'"',''),'"')),'\n        }') AS links,
    concat('"bio": "',if($desc IS NULL,"",replace($desc,'"',"''")),'" ') AS bio
  FROM
      default_community_tables_$conid
  $wherestring
  ORDER BY
    name
EOD;

  // Retrieve query
  list($comtblrows,$comtblheader_array,$comtbl_array)=queryreport($comtblquery,$vlink,$title,$description,0);

  $comtbls.="var community = [\n";

  for ($i=1; $i<=$comtblrows; $i++) {
    $comtbls.="    {\n";
    $comtbls.="        " . $comtbl_array[$i]['ID'] . ",\n";
    $comtbls.="        " . $comtbl_array[$i]['Name'] . ",\n";
    $comtbls.="        " . $comtbl_array[$i]['tags'] . ",\n";
    $comtbls.="        " . $comtbl_array[$i]['prog'] . ",\n";
    $comtbls.="        " . str_replace("\n"," ",$comtbl_array[$i]['links']) . ",\n";
    $comtbls.="        " . str_replace("\n"," ",$comtbl_array[$i]['bio']) . "\n";
    $comtbls.="    },\n";
  }

  // Remove the trailing comma
  $comtbls=rtrim(trim($comtbls), ',');

  $comtbls.="\n];";

} else { // Is in Zambia
// Vendors Information

  $vendorquery = <<<EOD
SELECT
    concat('"id": "',badgeid,'"') AS id,
    concat('"name": [ "',
           if(dba_good_web IS NULL,name_good_web,dba_good_web),
	   if(actualvendorloc!="",concat (" - ", actualvendorloc),""),
	   '" ]') AS name,
    concat('"tags": []') AS tags,
    concat('"prog": []') AS prog,
    badgeid,
    if(facebook_good_web IS NULL,"",replace(facebook_good_web,'"','')) AS facebook,
    if(twitter_good_web IS NULL,"",replace(twitter_good_web,'"','')) AS twitter,
    if(fetlife_good_web IS NULL,"",replace(fetlife_good_web,'"','')) AS fetlife,
    if(uri_good_web IS NULL,"",replace(uri_good_web,'"','')) AS uri,
    concat('"bio": "',if(bio_good_web IS NULL,"",replace(bio_good_web,'"',"''")),'" ') AS bio
  FROM
      Participants
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
    JOIN VendorStatus USING (badgeid,conid)
    JOIN VendorStatusTypes USING (vendorstatustypeid)
    JOIN (SELECT
        badgeid,
        locationid,
	conid,
	GROUP_CONCAT(if(locationkey IS NULL,
       	  "",
	  concat(locationkey,
	    if(booth!="",
	      booth,
	      if(baselocsubroomname!="",
		baselocsubroomname,
		baselocroomname)))) SEPARATOR ", ") AS actualvendorloc
      FROM
          VendorAnnualInfo
	LEFT JOIN (SELECT
	    badgeid,
	    locationkey,
	    locationid,
	    booth,
	    baselocsubroomname,
	    baselocroomname,
            conid
          FROM
              VendorHasLoc
            JOIN Location USING (locationid)
            JOIN BaseLocSubRoom USING (baselocsubroomid)
            JOIN BaseLocRoom USING (baselocroomid)
          WHERE
	    conid=$conid) SUBAVL USING (badgeid,conid)
      WHERE
        conid=$conid
      GROUP BY
	badgeid) AVL USING (badgeid,conid)
    JOIN (SELECT
        badgeid,
	biotext as name_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('name') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') NGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as bio_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('bio') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
        biolang='en-us') BGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as uri_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('uri') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') UGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as facebook_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('facebook') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') FBGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as twitter_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('twitter') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') TWGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as fetlife_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('fetlife') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') FLGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as pronoun_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('pronoun') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
        biolang='en-us') PGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as dba_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('dba') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
        biolang='en-us') DGW USING (badgeid)
    LEFT JOIN (SELECT
	badgeid,
	conid,
	basevendorspacename
      FROM
          VendorPrefSpace
        JOIN VendorSpace USING (vendorspaceid)
        JOIN BaseVendorSpace USING (basevendorspaceid)) BVS USING (badgeid,conid)
  WHERE
    conid=$conid AND
    vendorstatustypename in ('Accepted') AND
    permrolename in ('Vendor') AND
    (basevendorspacename NOT like "%Community Table%" OR basevendorspacename IS NULL)
  GROUP BY
    badgeid
  ORDER BY
    name_good_web
EOD;

  // Retrieve query
  list($vendorrows,$vendorheader_array,$vendor_array)=queryreport($vendorquery,$link,$title,$description,0);

  $vendors.="var vendor = [\n";

  for ($i=1; $i<=$vendorrows; $i++) {
    $vendors.="    {\n";
    $vendors.="        " . $vendor_array[$i]['id'] . ",\n";
    $vendors.="        " . $vendor_array[$i]['name'] . ",\n";
    $vendors.="        " . $vendor_array[$i]['tags'] . ",\n";
    $vendors.="        " . $vendor_array[$i]['prog'] . ",\n";
    $tmp_links="";
    if (file_exists("../Local/Participant_Images_web/" . $vendor_array[$i]['badgeid'])) {
      $tmp_links.="\n" . '            "img": "http://' . $conurl . '/Local/Participant_Images_web/' . $vendor_array[$i]['badgeid'] . '"';
    }
    if (!empty($vendor_array[$i]['facebook'])) {
      $fb_array=preg_split("/[\s,]+/",$vendor_array[$i]['facebook']);
      $ordcount=0;
      foreach ($fb_array as $fb_element) {
	if (!empty($tmp_links)) {$tmp_links.=',';}
	$tmp_links.="\n" . '            "fb' . $ordinal[$ordcount] . '" : "' . $fb_element . '"';
	$ordcount++;
      }
    }
    if (!empty($vendor_array[$i]['twitter'])) {
      $tw_array=preg_split("/[\s,]+/",$vendor_array[$i]['twitter']);
      $ordcount=0;
      foreach ($tw_array as $tw_element) {
	if (!empty($tmp_links)) {$tmp_links.=',';}
	$tmp_links.="\n" . '            "twitter' . $ordinal[$ordcount] . '" : "' . $tw_element . '"';
	$ordcount++;
      }
    }
    if (!empty($vendor_array[$i]['fetlife'])) {
      $fl_array=preg_split("/[\s,]+/",$vendor_array[$i]['fetlife']);
      $ordcount=0;
      foreach ($fl_array as $fl_element) {
	if (!empty($tmp_links)) {$tmp_links.=',';}
	$tmp_links.="\n" . '            "fl' . $ordinal[$ordcount] . '" : "' . $fl_element . '"';
	$ordcount++;
      }
    }
    if (!empty($vendor_array[$i]['uri'])) {
      if (!empty($tmp_links)) {$tmp_links.=',';}
      $tmp_links.="\n" . '            "url" : "' . $vendor_array[$i]['uri'] . '"';
    }
    if (!empty($tmp_links)) {
      $tmp_links='"links": { ' . $tmp_links . "\n" . '        }';
      $vendors.="        " . str_replace("\n"," ",$tmp_links) . ",\n";
    }
    $vendors.="        " . str_replace("\n"," ",$vendor_array[$i]['bio']) . "\n";
    $vendors.="    },\n";
  }

  // Remove the trailing comma
  $vendors=rtrim(trim($vendors), ',');

  $vendors.="\n];";

  // Community Tables
  $comtblquery=<<<EOD
SELECT
    concat('"id": "',badgeid,'"') AS id,
    concat('"name": [ "',name_good_web,
	   if(actualvendorloc!="",concat (" - ", actualvendorloc),""),
	   '" ]') AS name,
    concat('"tags": []') AS tags,
    concat('"prog": []') AS prog,
    badgeid,
    if(facebook_good_web IS NULL,"",replace(facebook_good_web,'"','')) AS facebook,
    if(twitter_good_web IS NULL,"",replace(twitter_good_web,'"','')) AS twitter,
    if(fetlife_good_web IS NULL,"",replace(fetlife_good_web,'"','')) AS fetlife,
    if(uri_good_web IS NULL,"",replace(uri_good_web,'"','')) AS uri,
    concat('"bio": "',if(bio_good_web IS NULL,"",replace(bio_good_web,'"',"''")),'" ') AS bio
  FROM
      Participants
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
    JOIN VendorStatus USING (badgeid,conid)
    JOIN VendorStatusTypes USING (vendorstatustypeid)
    JOIN (SELECT
        badgeid,
        locationid,
	conid,
	GROUP_CONCAT(if(locationkey IS NULL,
       	  "",
	  concat(locationkey,
	    if(booth!="",
	      booth,
	      if(baselocsubroomname!="",
		baselocsubroomname,
		baselocroomname)))) SEPARATOR ", ") AS actualvendorloc
      FROM
          VendorAnnualInfo
	LEFT JOIN (SELECT
	    badgeid,
	    locationkey,
	    locationid,
	    booth,
	    baselocsubroomname,
	    baselocroomname,
            conid
          FROM
              VendorHasLoc
            JOIN Location USING (locationid)
            JOIN BaseLocSubRoom USING (baselocsubroomid)
            JOIN BaseLocRoom USING (baselocroomid)
          WHERE
	    conid=$conid) SUBAVL USING (badgeid,conid)
      WHERE
        conid=$conid
      GROUP BY
	badgeid) AVL USING (badgeid,conid)
    JOIN (SELECT
        badgeid,
	biotext as name_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('name') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') NGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as bio_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('bio') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
        biolang='en-us') BGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as uri_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('uri') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') UGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as facebook_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('facebook') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') FBGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as twitter_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('twitter') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') TWGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as fetlife_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('fetlife') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
	biolang='en-us') FLGW USING (badgeid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as pronoun_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	biotypename in ('pronoun') AND
	biostatename in ('good') AND
	biodestname in ('web') AND
        biolang='en-us') PGW USING (badgeid)
    LEFT JOIN (SELECT
	badgeid,
	conid,
	basevendorspacename
      FROM
          VendorHasSpace
        JOIN VendorSpace USING (vendorspaceid)
        JOIN BaseVendorSpace USING (basevendorspaceid)) BVS USING (badgeid,conid)
  WHERE
    conid=$conid AND
    vendorstatustypename in ('Accepted') AND
    permrolename in ('Vendor') AND
    basevendorspacename like "%Community Table%"
  GROUP BY
    badgeid
  ORDER BY
    name_good_web
EOD;
  // Retrieve query
  list($comtblrows,$comtblheader_array,$comtbl_array)=queryreport($comtblquery,$link,$title,$description,0);

  $comtbls.="var community = [\n";

  for ($i=1; $i<=$comtblrows; $i++) {
    $comtbls.="    {\n";
    $comtbls.="        " . $comtbl_array[$i]['id'] . ",\n";
    $comtbls.="        " . $comtbl_array[$i]['name'] . ",\n";
    $comtbls.="        " . $comtbl_array[$i]['tags'] . ",\n";
    $comtbls.="        " . $comtbl_array[$i]['prog'] . ",\n";
    $tmp_links="";
    if (file_exists("../Local/Participant_Images_web/" . $comtbl_array[$i]['badgeid'])) {
      $tmp_links.="\n" . '            "img": "http://' . $conurl . '/Local/Participant_Images_web/' . $comtbl_array[$i]['badgeid'] . '"';
    }
    if (!empty($comtbl_array[$i]['facebook'])) {
      $fb_array=preg_split("/[\s,]+/",$comtbl_array[$i]['facebook']);
      $ordcount=0;
      foreach ($fb_array as $fb_element) {
	if (!empty($tmp_links)) {$tmp_links.=',';}
	$tmp_links.="\n" . '            "fb' . $ordinal[$ordcount] . '" : "' . $fb_element . '"';
	$ordcount++;
      }
    }
    if (!empty($comtbl_array[$i]['twitter'])) {
      $tw_array=preg_split("/[\s,]+/",$comtbl_array[$i]['twitter']);
      $ordcount=0;
      foreach ($tw_array as $tw_element) {
	if (!empty($tmp_links)) {$tmp_links.=',';}
	$tmp_links.="\n" . '            "twitter' . $ordinal[$ordcount] . '" : "' . $tw_element . '"';
	$ordcount++;
      }
    }
    if (!empty($comtbl_array[$i]['fetlife'])) {
      $fl_array=preg_split("/[\s,]+/",$comtbl_array[$i]['fetlife']);
      $ordcount=0;
      foreach ($fl_array as $fl_element) {
	if (!empty($tmp_links)) {$tmp_links.=',';}
	$tmp_links.="\n" . '            "fl' . $ordinal[$ordcount] . '" : "' . $fl_element . '"';
	$ordcount++;
      }
    }
    if (!empty($comtbl_array[$i]['uri'])) {
      if (!empty($tmp_links)) {$tmp_links.=',';}
      $tmp_links.="\n" . '            "url" : "' . $comtbl_array[$i]['uri'] . '"';
    }
    if (!empty($tmp_links)) {
      $tmp_links='"links": { ' . $tmp_links . "\n" . '        }';
      $comtbls.="        " . str_replace("\n"," ",$tmp_links) . ",\n";
    }
    $comtbls.="        " . str_replace("\n"," ",$comtbl_array[$i]['bio']) . "\n";
    $comtbls.="    },\n";
  }

  // Remove the trailing comma
  $comtbls=rtrim(trim($comtbls), ',');

  $comtbls.="\n];";

}

// OrgChart information
$orgchart=<<<EOD
var orgchartData = {
    "cols": [
	{"id":"","label":"Name","pattern":"","type":"string"},
	{"id":"","label":"Manager","pattern":"","type":"string"},
	{"id":"","label":"ToolTip","pattern":"","type":"string"}
      ],
    "rows": [
EOD;

$orgquery=<<<EOD
SELECT
    concat('{"c":[{"v":"',CRA.conrolenotes,'","f":"',CRA.conrolenotes,'<br/> ',
	   group_concat(DISTINCT "<A HREF=\'KonOpasStaff.php?conid=$conid#part/",badgeid,"\'>",pubsname,"</A>" SEPARATOR '/'),
           '"},{"v":"',if((CRC.conrolenotes IS NOT NULL),CRC.conrolenotes,""),'"},{"v":"',CRA.conroledescription,'"}]}') AS reportmap
  FROM
      ConRoles CRA
    LEFT JOIN UserHasConRole UHCR ON (UHCR.conroleid=CRA.conroleid AND UHCR.conid=$conid)
    LEFT JOIN Participants USING (badgeid)
    LEFT JOIN HasReports HRA ON (HRA.conroleid=CRA.conroleid AND HRA.conid=$conid)
    LEFT JOIN ConRoles CRB ON (CRB.conroleid=HRA.hasreport)
    LEFT JOIN HasReports HRB ON (HRB.hasreport=CRA.conroleid AND HRB.conid=$conid)
    LEFT JOIN ConRoles CRC ON (CRC.conroleid=HRB.conroleid)
  WHERE
    HRA.conid=$conid
  GROUP BY
    CRA.conroleid
EOD;

//Build the array
$orgchart_array=array();
list($orgrows,$orgheader_array,$org_array)=queryreport($orgquery,$link,$title,$description,0);
for ($i=1; $i<=$orgrows; $i++) {
  if (isset($org_array[$i]['reportmap'])) {
    $orgchart_array[]=$org_array[$i]['reportmap'];
  }
}

// Map the array to a string
$orgchart.=implode(",\n\t",$orgchart_array);

$orgchart.="
    ]
}
";

// Timeline information - start and stop time first, then build the respective grids

// Establish the start and stop of the various grids.
// Start time at zero hour.
$grid_start_sec=0;

// End time is a day past the number of days, just in case things go past midnight.
$grid_end_sec=($connumdays + 1)*86400;

// This query pulls all the distinct start and end times.  There is probably a computationally
// less intense way to do this, but ... this works, and it's not that hard.
$gridtimequery = <<<EOD
SELECT
  DISTINCT concat("('",TIME_TO_SEC(starttime), "','", TIME_TO_SEC(ADDTIME(starttime,duration))+congridspacer+congridspacer,"')") AS timing,
  TIME_TO_SEC(starttime) AS "Start",
  TIME_TO_SEC(ADDTIME(starttime,duration))+congridspacer+congridspacer AS "End"
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
  WHERE
    conid=$conid AND
    pubstatusname in ('Public')
  ORDER BY
 starttime
EOD;

// Retrieve the time signatures
list($gridtimerows,$gridtimeheader_array,$gridtime_array)=queryreport($gridtimequery,$link,$title,$description,0);

// Find all the times that there should be grid elements.

// In or out of a grid state
$in_p=0;

// The count of the grids
$graph_count=0;

// Array to hold how many elements wide a graph is
$graph_slots=array();

// Array to hold the day-names
$graph_day=array();

// This walks all the possible grid break points, and checks to see if they are durring any
// class element (with a windage of two slots after) and then notes where the graphs should be.
for ($time=$grid_start_sec; $time<=$grid_end_sec; $time = $time + $Grid_Spacer) {
  $timeslot[$time]=0;
  for ($gridloop=1; $gridloop<=$gridtimerows; $gridloop++) {
    $starttime=$gridtime_array[$gridloop]['Start'];
    $stoptime=$gridtime_array[$gridloop]['End'];
    if ($time >= $starttime && $time <= $stoptime) {
      $timeslot[$time]++;
    }
  }
  if (($timeslot[$time] > 0) && ($in_p == 0)) {
    $graph_start[$graph_count]=$time;
    $in_p=1;
    $dateinsec = strtotime($ConStart);
    $newdate=$dateinsec+$time;
    $graph_day[$graph_count]=date("l", $newdate);
  } elseif (($timeslot[$time] == 0) && ($in_p == 1)) {
    $graph_end[$graph_count]=$time-$Grid_Spacer;
    $graph_slots[$graph_count]=($graph_end[$graph_count]-$graph_start[$graph_count])/$Grid_Spacer;
    $in_p=0;
    $graph_count++;
  }
}

// Empty the workstring, tlname, and tlheight ... just in case.
$workstring="";
$tlname=array();
$tlheight=array();

// This walks each of the appropriate graphs.
for ($graphrow=0; $graphrow<$graph_count; $graphrow++) {
  $graphstarttime=$graph_start[$graphrow];
  $graphendtime=$graph_end[$graphrow];

// Get the roomname, the title and presenters, the descripton, the starttime, the endtime.
  $timelinequery = <<<EOD
SELECT
    roomname AS "Where",
    replace(title_good_web,'"',"''") AS "What",
    htmlcellcolor,
    if (name_good_web IS NULL," ",GROUP_CONCAT(name_good_web,if((moderator in ('1','Yes')),'(m)','') SEPARATOR ", ")) AS "Who",
    DATE_FORMAT(ADDTIME(constartdate, starttime),"Date(%Y, %m, %d, %k, %i, %s)") AS "Start Time",
    DATE_FORMAT(ADDTIME(ADDTIME(constartdate, starttime),duration),"Date(%Y, %m, %d, %k, %i, %s)") AS "End Time"
  FROM
      Sessions
    JOIN Schedule USING (sessionid, conid)
    JOIN Rooms USING (roomid)
    JOIN Types USING (typeid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
    JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('title') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') TGW USING (sessionid,conid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as name_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('name') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  biolang='en-us') NGW USING (badgeid)
  WHERE
    conid=$conid AND
    TIME_TO_SEC(starttime) >= $graphstarttime AND
    TIME_TO_SEC(starttime) <= $graphendtime AND
    pubstatusname in ('Public') AND
    volunteer not in ("1","Yes") AND
    introducer not in ("1","Yes") AND
    aidedecamp not in ("1","Yes")
  GROUP BY
    sessionid
EOD;

  // Retrieve query
  list($timelinerows,$timelineheader_array,$timeline_array)=queryreport($timelinequery,$link,$title,$description,0);

  // Walk the rows and build the array
  $timelinedata_array=array();
  $roomcountelements_array=array();
  for ($timeloop=1; $timeloop<=$timelinerows; $timeloop++) {
    $roomcountelements_array[$timeline_array[$timeloop]['Where']]++;
    $timelinedata_array[$timeloop]="\t{\"c\":[{\"v\":\"".$timeline_array[$timeloop]['Where']."\"},";
    $timelinedata_array[$timeloop].="{\"v\":\"".$timeline_array[$timeloop]['What']."\"},";
    $timelinedata_array[$timeloop].="{\"v\":\"".$timeline_array[$timeloop]['Who']."\"},";
    $timelinedata_array[$timeloop].="{\"v\":\"".$timeline_array[$timeloop]['htmlcellcolor']."\"},";
    $timelinedata_array[$timeloop].="{\"v\":\"".$timeline_array[$timeloop]['Start Time']."\"},";
    $timelinedata_array[$timeloop].="{\"v\":\"".$timeline_array[$timeloop]['End Time']."\"}]}";
  }

  // The number of rooms for the height of the graph
  $tlheight[$graphrow]=count($roomcountelements_array);

  // Build the string ... header first
  $timelinename="timelineData" . $graphrow;
  $tlname[$graphrow]=$timelinename;
  $timelinestring="var $timelinename = {\n";
  $timelinestring.=<<<EOD
    "cols" :[
	{"type":"string","id":"Room"},
	{"type":"string","id":"Name"},
	{"type":"string","role":"tooltip","p":{"role":"tooltip"}},
	{"type":"string","role":"style"},
	{"type":"date","id":"Start"},
	{"type":"date","id":"End"}
    ],
    "rows":[

EOD;

  // ... then the chewy array center
  $timelinestring.=implode(",\n",$timelinedata_array);

  // Followed by the close string
  $timelinestring.="\n    ]\n};\n";

  // Accumulation of the timeline strings
  $workstring.=$timelinestring;

} // for ($graphrow=1; $graphrow<$graph_count; $graphrow++)

// Adding the chartnum, tlname, tldwidth, and tldheight variables to the timeline.js file
$workstring.="var chartnum = ". ($graphrow - 1) .";\n";
$workstring.="var tlname = [" . implode(", ",$tlname) . "];\n";;
$workstring.="var tlwidth = [" . implode(", ",$graph_slots) . "];\n";
$workstring.="var tlheight = [" . implode(", ",$tlheight) . "];\n";
$workstring.="var dayname = [\"" . implode('", "',$graph_day) . "\"];\n";

// Adding the variables that the presentation script needs to the timeline.php file
$phpworkstring="<?php\n";
$phpworkstring.="\$graph_count=$graph_count;\n";
$phpworkstring.="\$graph_day = array ('" . implode("', '",$graph_day) . "');\n";
$phpworkstring.="?>\n";

// Write out the files
$kpfile="../Local/$conid/program.js";
$recordfile = fopen($kpfile,"w") or RenderError($title,"Unable to open record file: $kpfile.");
fwrite ($recordfile, $program);
fclose($recordfile);
$message.="$kpfile created<br>\n";

$kbfile="../Local/$conid/people.js";
$recordfile = fopen($kbfile,"w") or RenderError($title,"Unable to open record file: $kbfile.");
fwrite ($recordfile, $bios);
fclose($recordfile);
$message.="$kbfile created<br>\n";

$ksdfile="../Local/$conid/staffjobs.js";
$recordfile = fopen($ksdfile,"w") or RenderError($title,"Unable to open record file: $ksdfile.");
fwrite ($recordfile, $staffdesc);
fclose($recordfile);
$message.="$ksdfile created<br>\n";

$ksfile="../Local/$conid/staff.js";
$recordfile = fopen($ksfile,"w") or RenderError($title,"Unable to open record file: $ksfile.");
fwrite ($recordfile, $staffbios);
fclose($recordfile);
$message.="$ksfile created<br>\n";

$kvfile="../Local/$conid/vendor.js";
$recordfile = fopen($kvfile,"w") or RenderError($title,"Unable to open record file: $kvfile.");
fwrite ($recordfile, $vendors);
fclose($recordfile);
$message.="$kvfile created<br>\n";

$kcfile="../Local/$conid/community.js";
$recordfile = fopen($kcfile,"w") or RenderError($title,"Unable to open record file: $kcfile.");
fwrite ($recordfile, $comtbls);
fclose($recordfile);
$message.="$kcfile created<br>\n";

$kocfile="../Local/$conid/orgchart.js";
$recordfile = fopen($kocfile,"w") or RenderError($title,"Unable to open record file: $kocfile.");
fwrite ($recordfile, $orgchart);
fclose($recordfile);
$message.="$kocfile created<br>\n";

$kgfile="../Local/$conid/timeline.js";
$recordfile = fopen($kgfile,"w") or RenderError($title,"Unable to open record file: $kgfile.");
fwrite ($recordfile, $workstring);
fclose($recordfile);
$message.="$kgfile created<br>\n";

$kgpfile="../Local/$conid/timeline.php";
$recordfile = fopen($kgpfile,"w") or RenderError($title,"Unable to open record file: $kgpfile.");
fwrite ($recordfile, $phpworkstring);
fclose($recordfile);
$message.="$kgpfile created<br>\n";

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

correct_footer();
?>
