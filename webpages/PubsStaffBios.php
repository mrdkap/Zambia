<?php
require_once('PostingCommonCode.php');
global $link;

/* takes a variable, and searches across all the posible variations to
   see if said variable exists in the bios-set.  This checks for the
   dests of staffweb, staffbook, web, then book, across the states of
   good, edited, then raw.
 */
function getBioDestEdit($biotype,$biolang,$bioinfo) {
  if (!empty($bioinfo[$biotype.'_'.$biolang.'_good_staffweb_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_good_staffweb_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_good_staffbook_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_good_staffbook_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_good_web_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_good_web_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_good_book_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_good_book_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_edited_staffweb_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_staffweb_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_edited_staffbook_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_staffbook_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_raw_staffweb_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_raw_staffweb_bio'];
  } elseif (!empty($bioinfo[$biotype.'_'.$biolang.'_raw_staffbook_bio'])) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_raw_staffbook_bio'];
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
   This is staffweb, staffbook, web then book. */
function getPictureDestEdit($checkbadge) {
  $picture="";
  $picturestaffweb="../Local/Participant_Images_staffweb/$checkbadge";
  $picturestaffbook="../Local/Participant_Images_staffbook/$checkbadge";
  $pictureweb="../Local/Participant_Images_web/$checkbadge";
  $picturebook="../Local/Participant_Images_book/$checkbadge";
  if (file_exists($picturestaffweb)) {
    $picture=sprintf("<img width=300 src=\"%s\">",$picturestaffweb);
  } elseif (file_exists($picturestaffbook)) {
    $picture=sprintf("<img width=300 src=\"%s\">",$picturestaffbook);
  } elseif (file_exists($picturweb)) {
    $picture=sprintf("<img width=300 src=\"%s\">",$pictureweb);
  } elseif (file_exists($picturebook)) {
    $picture=sprintf("<img width=300 src=\"%s\">",$picturebook);
  }
  return ($picture);
}

// Pass in variables
$conid=$_GET['conid'];
if ($conid=="") {$conid=$_SESSION['conid'];}

$short="F";
if (isset($_GET['short'])) {
  if ($_GET['short'] == "Y") {
    $short="T";
  } elseif ($_GET['short'] == "N") {
    $short="F";
  }
}

/* Just setting this, in other fooBios this is passed in. Since we never want
   to do this without pictures in public, in long form, it is just hard-coded. */
$pic_p="T";

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

// LOCALIZATIONS
$_SESSION['return_to_page']="PubsStaffBios.php?conid=$conid";
$title="Biographical Information";
$description="<P>Biographical Information for all Con Staff.</P>\n";
$additionalinfo="<P>See also the ";
if ($short=="T") {
  $additionalinfo.="<A HREF=\"PubsStaffBios.php?conid=$conid\">full</A> version of this\n";
} else {
  $additionalinfo.="<A HREF=\"PubsStaffBios.php?short=Y&conid=$conid\">short</A> version of this\n";
}
$additionalinfo.="or the <A HREF=\"ConStaffBios.php?conid=$conid\">Roles and Descriptions</A> ";

/* This query grabs everything necessary for the con roles. */

$query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=\"ConStaffBios.php?conid=$conid#",conrolename,"\">",conrolenotes,"</A>") AS 'Title',
    pubsname AS 'Participants',
    badgeid
  FROM
      UserHasConRole
    JOIN Participants USING (badgeid)
    JOIN ConRoles USING (conroleid)
    JOIN ConInfo USING (conid)
    JOIN HasReports USING (conroleid,conid)
    LEFT JOIN (SELECT
	badgeid,
	biotext as name_edited_web
      FROM
	  Bios
        JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
        biotypename in ('name') and
        biostatename in ('edited') and
        biodestname in ('web') and
	biolang='en-us') NEWEB USING (badgeid)
  WHERE
    conid=$conid
  ORDER BY
    name_edited_web
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
	  $name=$header;
	}

	$name="<A NAME=\"$name\"></A>$name";

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
	$biostring.=sprintf("<P><B>%s</B>",$name);
	if ($bio != "") {
	  $biostring.=$bio;
	}
	$biostring.="</P>\n";
	if ($uri != "") {
	  $biostring.=sprintf("<P>%s</P>\n",$uri);
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

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

/* Produce the report. */
$printstring=renderschedreport($format,$header_break,$single_line_p,$elements,$element_array);
echo $printstring;

correct_footer();
?>
