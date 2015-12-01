<?php
require_once ('StaffCommonCode.php');
global $link,$message,$message_error;

// Get the various length limits
$limit_array=getLimitArray();

if (isset($_POST['badgeids'])) {
  $badgeids=$_POST['badgeids'];
 } elseif (isset($_GET['badgeids'])) {
   $badgeids=$_GET['badgeids'];
 }

if (isset($_POST['badgeid'])) {
  $badgeid=$_POST['badgeid'];
 } elseif (isset($_GET['badgeid'])) {
   $badgeid=$_GET['badgeid'];
 }

if (isset($_POST['qno'])) {
  $qno=$_POST['qno'];
} elseif (isset($_GET['qno'])) {
  $qno=$_GET['qno'];
}

$title="Edit Participant Biography";
$additionalinfo ="<P><A HREF=\"StaffManageBios.php";
if ((isset($qno)) or (isset($badgeids)) or (isset($_POST['unlock']))) {
  $additionalinfo.="?";
  if (isset($qno)) {
    $additionalinfo.="qno=$qno";
    if ((isset($badgeids)) or (isset($_POST['unlock']))) {
      $additionalinfo.="&";
    }
  }
  if (isset($badgeids)) {
    $additionalinfo.="badgeids=$badgeids";
    if (isset($_POST['unlock'])) {
      $additionalinfo.="&";
    }
  }
  if (isset($_POST['unlock'])) {$additionalinfo.="unlock=".$_POST['unlock'];}
}
$additionalinfo.="\">Return</A> to the selected list.</P>\n";
$additionalinfo.="<P>Please edit the bios below.  We don't currently edit the raw bios here.";
$additionalinfo.="  If you really need to, please click on their name, and edit it there.</P>\n";
$additionalinfo.="<P>If the raw and edited in concordance, you may promote it to good.</P>\n";

if (!isset($badgeid)) {
  $message_error.="Required argument 'badgeid' missing from URL.<BR>\n";
  RenderError($title,$message_error);
  exit ();
 }

// Get the bio data.
$bioinfo=getBioData($badgeid);

// Get the Participant name, and the name of the locker
$query = <<<EOD
SELECT
    LB.pubsname as lockedby,
    if(P.pubsname!="",P.pubsname,concat(firstname," ",lastname)) name
  FROM
      Participants P
    JOIN Bios B USING (badgeid)
    JOIN CongoDump USING (badgeid)
    LEFT JOIN Participants LB on B.biolockedby = LB.badgeid
  WHERE
    P.badgeid='$badgeid'
EOD;

if (($result=mysql_query($query,$link))===false) {
  $message_error.=$query."<BR>\nError retrieving lock and name data from database.\n";
  RenderError($title,$message_error);
  exit();
 }
$participant_info_array=mysql_fetch_assoc($result);

/* If there is an update/save to the edited state passed, check for
 what was changed, and update (just) that in the database. */
$biostate="edited";
if (isset($_POST['update'])) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
      // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) { // only edited
      for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

	// Setup for short names and keyname, collapsing all four variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$i];
	$biolang=$bioinfo['biolang_array'][$j];
	// $biostate=$bioinfo['biostate_array'][$k]; hard set above
	$biodest=$bioinfo['biodest_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	if ($biostate!='edited') {continue;}

	// Clean up the posted string.
	$teststring=stripslashes(htmlspecialchars_decode($_POST[$keyname]));
	$biostring=stripslashes(htmlspecialchars_decode($bioinfo[$keyname]));

	// Check for differences, if they exist, update the database.
	if ($teststring != $biostring) {
	  if ((isset($limit_array['max'][$biodest][$biotype])) and (strlen($teststring)>$limit_array['max'][$biodest][$biotype])) {
	    $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	    $message.=" too long (".strlen($teststring)." characters), the limit is ".$limit_array['max'][$biodest][$biotype]." characters.";
	  } elseif ((isset($limit_array['min'][$biodest][$biotype])) and (strlen($teststring)<$limit_array['min'][$biodest][$biotype])) {
	    $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	    $message.=" too short (".strlen($teststring)." characters), the limit is ".$limit_array['min'][$biodest][$biotype]." characters.";
	  } else {
	    update_bio_element($link,$title,$teststring,$badgeid,$biotype,$biolang,$biostate,$biodest);
	  }
	  $bioinfo[$keyname]=$teststring;
	}
      }
    }
  }
}

// Copy edited to good
if ((isset($_GET['biotype'])) and (isset($_GET['biolang'])) and (isset($_GET['biodest']))) {
  $biotype=$_GET['biotype'];
  $biolang=$_GET['biolang'];
  $biodest=$_GET['biodest'];
  $biostate="edited";
  $goodstring=$bioinfo[$biotype."_".$biolang."_".$biostate."_".$biodest."_bio"];
  update_bio_element($link,$title,$goodstring,$badgeid,$biotype,$biolang,"good",$biodest);
  $bioinfo[$biotype."_".$biolang."_good_".$biodest."_bio"]=$goodstring;
}

/* Lock the editing of the participant.
 Returns 0 if succeeded, -2 if lock failed, -1 if db error. */
$lockresult=lock_participant($badgeid);

if ($lockresult==-2) {
  $message_error.="<P>This biography is currently being edited by ".htmlspecialchars($participant_info_array['lockedby'])."</P>\n";
 }

$description ="<H2 class=\"head\"><A HREF=\"StaffEditCreateParticipant.php?action=edit&partid=$badgeid\">";
$description.=htmlspecialchars($participant_info_array['name'])."</A></H2>\n";

// Begin the presenations
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

//Build the self-referential form.
echo "<FORM name=\"bioeditform\" method=POST action=\"StaffEditBios.php\">\n";
echo "<INPUT type=hidden name=\"qno\" value=\"$qno\">\n";
echo "<INPUT type=hidden name=\"badgeid\" value=\"$badgeid\">\n";
echo "<INPUT type=hidden name=\"badgeids\" value=\"$badgeids\">\n";
echo "<INPUT type=hidden name=\"update\" value=\"Yes\">\n";
echo "<INPUT type=hidden name=\"unlock\" value=\"$badgeid\">\n";

// Top submit button.
echo "<DIV class=\"submit\" id=\"submit\">\n  <BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save Whole Page</BUTTON>\n</DIV>\n";

/* Four-deep array to cover all the variables.  The biostate is now last,
   even though it is normally third, so that the compare one box to the next
   can happen more cleanly.
 */
for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
  for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
    for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {
      for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {

	// Setup for short names and keyname, collapsing all three variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$i];
	$biolang=$bioinfo['biolang_array'][$j];
	$biostate=$bioinfo['biostate_array'][$k];
	$biodest=$bioinfo['biodest_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Modify the titles for legability, and switch on the readonly for raw.
	$readonly="";
	if ($biostate=='raw') {
	  $readonly="readonly";
	}

	// For now, skip the "good" category
	if ($biostate=='good') {
	  if (!empty($bioinfo[$keyname])) {
	    echo "<H3>Good entry exists for ".ucfirst($biotype)." ".ucfirst($biodest)." ($biolang) Biography</H3>\n";
	  }
	  continue;
	}

	// Set up the LABEL.
	echo "<LABEL for=\"$keyname\">".ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	$limit_string="";
	if (isset($limit_array['max'][$biodest][$biotype])) {
	  $limit_string.=" maximum ".$limit_array['max'][$biodest][$biotype];
	}
	if (isset($limit_array['min'][$biodest][$biotype])) {
	  $limit_string.=" minimum ".$limit_array['min'][$biodest][$biotype];
	}
	if ($limit_string !="") {
	  echo " (Limit".$limit_string." characters)";
	}
	echo ":</LABEL><BR>\n";

	// Set up the input box.
	echo "<TEXTAREA $readonly name=\"$keyname\" rows=8 cols=72>".$bioinfo[$keyname]."</TEXTAREA><BR><BR>\n";
      }
      // Every other change submit button.
      echo "<DIV class=\"submit\" id=\"submit\">\n  <BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save Whole Page</BUTTON>\n";
      if (($bioinfo[$biotype."_".$biolang."_raw_".$biodest."_bio"] == $bioinfo[$biotype."_".$biolang."_edited_".$biodest."_bio"]) and
	  ($bioinfo[$biotype."_".$biolang."_raw_".$biodest."_bio"] != $bioinfo[$biotype."_".$biolang."_good_".$biodest."_bio"])) {
	echo " <A HREF=\"StaffEditBios.php?qno=$qno&badgeid=$badgeid&badgeids=$badgeids&biotype=$biotype&biolang=$biolang&biodest=$biodest\">";
	echo " Promote ".ucfirst($biotype)." ".ucfirst($biodest)." ($biolang) to good.";
        echo "</A>\n";
      }
      echo "</DIV>\n";
    }
  }
}
//echo "<br>\n<DIV class=\"submit\" id=\"submit\">\n  <BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save Whole Page</BUTTON>\n</DIV>\n";
echo "</FORM>\n";
echo "<BR>\n<BR>\n";
correct_footer();
?>

