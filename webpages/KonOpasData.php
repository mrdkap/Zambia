<?php
require_once('PostingCommonCode.php');
global $link;

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

$programquery = <<<EOD
SELECT
    concat('"id": "',sessionid,'"') AS id,
    concat('"title": "',replace(title_good_web,'"',"''"),if((subtitle_good_web IS NULL),"",concat(": ",replace(subtitle_good_web,'"',"''"))),'"') AS title,
  concat('"tags": [ "',trackname,'", "',typename,'" ]') AS tags,
    DATE_FORMAT(ADDTIME(constartdate,starttime),'"date": "%Y-%m-%d", "time":  "%H:%i"') AS time,
    concat('"mins": "',TIME_TO_SEC(duration) div 60,'"') AS mins,
    concat('"loc": [ "',roomname,'" ]') AS loc,
    concat('"people": [ ',if ((pubsname is NULL), " ", GROUP_CONCAT(DISTINCT '{ "id": "',badgeid,'", "name": "',pubsname,'" }' SEPARATOR ", ")),' ]') AS people,
  concat('"desc": "',if(desc_good_web IS NULL,"",replace(desc_good_web,'"',"''")),'"') AS 'desc'
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

for ($i=1; $i<$elementrows; $i++) {
  $program.="    {\n";
  $program.="        " . $element_array[$i]['id'] . ",\n";
  $program.="        " . $element_array[$i]['title'] . ",\n";
  $program.="        " . $element_array[$i]['tags'] . ",\n";
  $program.="        " . $element_array[$i]['time'] . ",\n";
  $program.="        " . $element_array[$i]['mins'] . ",\n";
  $program.="        " . $element_array[$i]['loc'] . ",\n";
  $program.="        " . $element_array[$i]['people'] . ",\n";
  $program.="        " . str_replace("
  $program.="    },\n";
}

// last run through, without the comma at the end.
$program.="    {\n";
$program.="        " . $element_array[$i]['id'] . ",\n";
$program.="        " . $element_array[$i]['title'] . ",\n";
$program.="        " . $element_array[$i]['tags'] . ",\n";
$program.="        " . $element_array[$i]['time'] . ",\n";
$program.="        " . $element_array[$i]['mins'] . ",\n";
$program.="        " . $element_array[$i]['loc'] . ",\n";
$program.="        " . $element_array[$i]['people'] . ",\n";
$program.="        " . str_replace("
$program.="    }\n";

$program.="];";

// concat('"links": { "url" : "',if(uri_good_web IS NULL,"",replace(uri_good_web,'"','')),'" }') AS links,
// concat('"links": {}') AS links,

$bioquery = <<<EOD
SELECT
    concat('"id": "',badgeid,'"') AS id,
    concat('"name": [ "',name_good_web,'" ]') AS name,
    concat('"tags": []') AS tags,
    concat('"prog": [ ',GROUP_CONCAT(DISTINCT '"',sessionid,'"' SEPARATOR ", "),' ]') AS prog,
    concat('"links": { \n            "img": "http://$conurl/Local/Participant_Images_web/',badgeid,'",',if(uri_good_web IS NULL,"",concat('\n            "url" : "',replace(uri_good_web,'"',''),'"')),'\n        }') AS links,
    concat('"bio": "',if(bio_good_web IS NULL,"",replace(bio_good_web,'"',"''")),if(pronoun_good_web IS NULL,"",concat(" Preferred Pronoun: ",pronoun_good_web)),'" ') AS bio
  FROM
      ParticipantOnSession
    JOIN Sessions USING (sessionid,conid)
    JOIN PubStatuses USING (pubstatusid)
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

for ($i=1; $i<$biorows; $i++) {
  $bios.="    {\n";
  $bios.="        " . $bio_array[$i]['id'] . ",\n";
  $bios.="        " . $bio_array[$i]['name'] . ",\n";
  $bios.="        " . $bio_array[$i]['tags'] . ",\n";
  $bios.="        " . $bio_array[$i]['prog'] . ",\n";
  $bios.="        " . str_replace("
  $bios.="        " . str_replace("
  $bios.="    },\n";
}

// last run through, without the comma at the end.
$bios.="    {\n";
$bios.="        " . $bio_array[$i]['id'] . ",\n";
$bios.="        " . $bio_array[$i]['name'] . ",\n";
$bios.="        " . $bio_array[$i]['tags'] . ",\n";
$bios.="        " . $bio_array[$i]['prog'] . ",\n";
$bios.="        " . str_replace("
$bios.="        " . str_replace("
$bios.="    }\n";

$bios.="];";


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

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

correct_footer();
?>