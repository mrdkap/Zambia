<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;

/* takes a variable, and searches across all the posible variations
   to see if said variable exists in the bios-set
 */
function getBioDestEdit($biotype,$biolang,$bioinfo) {
  if (!empty($bioinfo[$biotype.'_'.$biolang.'_good_badge_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_good_badge_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_edited_badge_bio'])) {
    $bioout="***EDIT PLEASE*** ".$bioinfo[$biotype.'_'.$biolang.'_edited_badge_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_raw_badge_bio'])) {
    $bioout="***EDIT PLEASE*** ".$bioinfo[$biotype.'_'.$biolang.'_raw_badge_bio'];
  } else {
    $bioout="";
  }
  return ($bioout);
}

/* Take the badgeid and searches for the picture.
   This is book then web with edit note. */
function getPictureDestEdit($checkbadge) {
  $picture="";
  $picturebadge="../Local/Participant_Images_badge/$checkbadge";
  if (file_exists($picturebadge)) {
    $picture=sprintf("<A HREF=\"%s\"><img width=300 src=\"%s\"></A>",$picturebadge,$picturebadge);
  }
  return ($picture);
}

// Pass in variables
$conid=$_GET['conid'];
if ($conid=="") {$conid=$_SESSION['conid'];}

// Short or long format
$short="F";
if (isset($_GET['short'])) {
  if ($_GET['short'] == "Y") {
    $short="T";
  } elseif ($_GET['short'] == "N") {
    $short="F";
  }
}

// With or without their picture
$pic_p="T";
if (isset($_GET['pic_p'])) {
  if ($_GET['pic_p'] == "N") {
    $pic_p="F";
    $short="F";
  }
}

// This is for pulling purposes, we don't want to pdf this.
$print_p="F";

// LOCALIZATIONS
$_SESSION['return_to_page']="BadgeBios.php";
$title="Badge Information";
$description="<P>Badge Information for all con staff with Roles with pictures.</P>\n";
$additionalinfo="<P>See also this ";
if ($short=="T") {
  $additionalinfo.="<A HREF=\"BadgeBios.php\">full</A> or\n";
  $additionalinfo.="<A HREF=\"BadgeBios.php?pic_p=N\">full without images</A>,\n";
} elseif ($pic_p=="F") {
  $additionalinfo.="<A HREF=\"BadgeBios.php\">full</A> or\n";
  $additionalinfo.="<A HREF=\"BadgeBios.php?short=Y\">short</A>,\n";
} else {
  $additionalinfo.="<A HREF=\"BadgeBios.php?pic_p=N\">without images</A> or\n";
  $additionalinfo.="<A HREF=\"BadgeBios.php?short=Y\">short</A>.</P>\n";
}

/* This query grabs everything necessary for the jobs to be printed. */

$query = <<<EOD
SELECT
    DISTINCT conrolenotes AS 'Title',
    pubsname AS 'Participants',
    badgeid
  FROM
      UserHasConRole
    JOIN Participants USING (badgeid)
    JOIN ConRoles USING (conroleid)
    JOIN ConInfo USING (conid)
  WHERE
    conid=$conid AND
    badgeid!=110 AND
  conrolenotes not in ("Pony Paddock", "Rope Lounge", "Whip Lounge", "Captive Moments Photography Exhibit", "NELA's Erotic/Fetish Photo Exhibit", "Hypnosis Lounge", "Bootblacks", "Straight Jacket Lounge", "Chill Lounge", "Convention Sponsor", "Brainstorm Coordinator", "General Staff", "Programming Volunteers", "Evening Events Volunteers", "Valet Staff", "Hotel Staff", "Private Safety Staff", "Medical Organization")
  ORDER BY
    pubsname
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// Establish the bios element
if ($short == "T") {
  $header="";
  for ($i=1; $i<=$elements; $i++) {
    if ($element_array[$i]['Participants'] != $header) {
      $header=$element_array[$i]['Participants'];
      $biostring=sprintf("<P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$header);
    }
    $element_array[$i]['Bio']=$biostring;
  }
} else {
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
	  $name="***EDIT PLEASE*** ".$header;
	}

	//If there is a bio
	$bio=getBioDestEdit('bio',$biolang,$bioinfo);

	//If there is a URI line
	$uri=getBioDestEdit('uri',$biolang,$bioinfo);

	//If there is a pronoun line
	$pronoun=getBioDestEdit('pronoun',$biolang,$bioinfo);

	if ($picture != "") {
	  if ($tablecount == 0) {
	    $biostring.="<TABLE>\n  <TR>\n    <TD valign=\"top\" width=310>";
	    $tablecount++;
	  } else {
	    $biostring.="    </TD>\n  </TR>\n  <TR>\n    <TD width=310>";
	  }
	  $biostring.=sprintf("%s</TD>\n    <TD>",$picture);
	}
	$biostring.=sprintf("<DL>\n  <DT><B>%s</B>",$name);
	if ($bio != "") {
	  $biostring.=$bio;
	}
	$biostring.="</DT>\n";
	if ($uri != "") {
	  $biostring.=sprintf("  <DT>%s</DT>\n",$uri);
	}
	if ($pronoun != "") {
	  $biostring.=sprintf("  <DT>Preferred pronoun: %s</DT>\n",$pronoun);
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

// Variables for the format
$format="bios";
$header_break="Participants";
$single_line_p="T";

/* Produce the report. */
$printstring=renderschedreport($format,$header_break,$single_line_p,$print_p,$elements,$element_array);

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo $printstring;
correct_footer();
?>
