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
echo $vendormap;
echo $vendorlist;
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
correct_footer();

