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
  if ((isset($bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'])) and
      ($bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'] != "")) {
    $bioout=$bioinfo[$biotype.'_'.$biolang.'_edited_book_bio'];
  } elseif ((isset($bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'])) and
	    ($bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'] != "")) {
    $bioout="***EDIT PLEASE*** ".$bioinfo[$biotype.'_'.$biolang.'_edited_web_bio'];
  } elseif ((isset($bioinfo[$biotype.'_'.$biolang.'_raw_book_bio']))  and
	    ($bioinfo[$biotype.'_'.$biolang.'_raw_book_bio'] != "")) {
    $bioout="***EDIT PLEASE*** ".$bioinfo[$biotype.'_'.$biolang.'_raw_book_bio'];
  } elseif ((isset($bioinfo[$biotype.'_'.$biolang.'_raw_web_bio']))  and
	    ($bioinfo[$biotype.'_'.$biolang.'_raw_web_bio'] != "")) {
    $bioout="***EDIT PLEASE*** ".$bioinfo[$biotype.'_'.$biolang.'_raw_web_bio'];
  } else {
    $bioout="";
  }

  return ($bioout);
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

$pic_p="T";
if (isset($_GET['pic_p'])) {
  if ($_GET['pic_p'] == "N") {
    $pic_p="F";
    $short="F";
  }
}

// LOCALIZATIONS
$_SESSION['return_to_page']="BookBios.php";
$title="Biographical Information";
$description="<P>Biographical Information for all Presenters.</P>\n";
$additionalinfo="<P>See also this ";
if ($short=="T") {
  $additionalinfo.="<A HREF=\"BookStaffBios.php\">full</A> or\n";
  $additionalinfo.="<A HREF=\"BookStaffBios.php?pic_p=N\">full without images</A>,\n";
} elseif ($pic_p=="F") {
  $additionalinfo.="<A HREF=\"BookStaffBios.php\">full</A> or\n";
  $additionalinfo.="<A HREF=\"BookStaffBios.php?short=Y\">short</A>,\n";
} else {
  $additionalinfo.="<A HREF=\"BookStaffBios.php?pic_p=N\">without images</A> or\n";
  $additionalinfo.="<A HREF=\"BookStaffBios.php?short=Y\">short</A>,\n";
}
$additionalinfo.="the <A HREF=\"BookBios.php\">Bios</A>\n";
$additionalinfo.="<A HREF=\"BookBios.php?pic_p=N\">(without images)</A>\n";
$additionalinfo.="<A HREF=\"BookBios.php?short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"BookSched.php?format=desc\">description</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=desc&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"BookSched.php?format=sched\">timeslots</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=sched&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"BookSched.php?format=tracks\">tracks</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=tracks&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"BookSched.php?format=trtime\">tracks by time</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=trtime&short=Y\">(short)</A>,\n";
$additionalinfo.="or the <A HREF=\"grid.php?standard=y\">grid</A>.</P>\n";

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
    JOIN HasReports USING (conroleid,conid)
    LEFT JOIN (SELECT
	badgeid,
	biotext as name_edited_book
      FROM
	  Bios
        JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
        biotypename in ('name') and
        biostatename in ('edited') and
        biodestname in ('book') and
	biolang='en-us') NEB USING (badgeid)
  WHERE
    conid=$conid
  ORDER BY
    name_edited_book
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
	$picturetmp=getBioDestEdit('picture',$biolang,$bioinfo);
	if (($picturetmp != "") and ($pic_p == "T")) {
	  $edit_p=strpos($picturetmp,"***EDIT PLEASE***");
	  if ($edit_p === false) {
	    $picture=sprintf("<img src=\"%s\">",$picturetmp);
	  } else {
	    $picture=sprintf("Picture for editing at: http://%s/webpages/%s",
			     $_SESSION['conurl'], substr($picturetmp, 18));
	  }
	} else {
	  $picture="";
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