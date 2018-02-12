<?php
require_once ('StaffCommonCode.php');
global $link,$message,$message_error;

// Get the various length limits
$limit_array=getLimitArray();

if (!empty($_POST['badgeids'])) {
  $sessionids=$_POST['badgeids'];
} elseif (!empty($_GET['badgeids'])) {
  $sessionids=$_GET['badgeids'];
}

if ((!empty($_POST['badgeid'])) and (is_numeric($_POST['badgeid']))) {
  $sessionid=$_POST['badgeid'];
} elseif ((!empty($_GET['badgeid'])) and (is_numeric($_GET['badgeid']))) {
  $sessionid=$_GET['badgeid'];
}

if ((!empty($_POST['qno'])) and (is_numeric($_POST['qno']))) {
  $qno=$_POST['qno'];
} elseif ((!empty($_GET['qno'])) and (is_numeric($_GET['qno']))) {
  $qno=$_GET['qno'];
}

if ((!empty($_POST['conid'])) and (is_numeric($_POST['conid']))) {
  $conid=$_POST['conid'];
} elseif ((!empty($_GET['conid'])) and (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
} else {
  $conid=$_SESSION['conid'];
}

$title="Edit Class Descriptions";
$additionalinfo ="<P><A HREF=\"StaffManageBios.php";
if ((isset($qno)) or (isset($sessionids)) or (isset($_POST['unlock']))) {
  $additionalinfo.="?";
  if (isset($qno)) {
    $additionalinfo.="qno=$qno";
    if ((isset($sessionids)) or (isset($_POST['unlock']))) {
      $additionalinfo.="&";
    }
  }
  if (isset($sessionids)) {
    $additionalinfo.="badgeids=$sessionids";
    if (isset($_POST['unlock'])) {
      $additionalinfo.="&";
    }
  }
  if (isset($_POST['unlock'])) {$additionalinfo.="unlock=".$_POST['unlock'];}
}
$additionalinfo.="\">Return</A> to the selected list.</P>\n";
$additionalinfo.="<P>Please edit the descriptions below.  We don't currently edit the";
$additionalinfo.=" raw descriptions here.";
$additionalinfo.="  If you really need to, please click on the title, and edit it there.</P>\n";
$additionalinfo.="<P>If the raw and edited in concordance, you may promote it to good.</P>\n";

if (!isset($sessionid)) {
  $message_error.="Required argument 'badgeid' missing from URL.<BR>\n";
  RenderError($title,$message_error);
  exit ();
 }

// Get the bio data.
$descinfo=getDescData($sessionid,$conid);

// Get the Participant name, and the name of the locker
$query = <<<EOD
SELECT
    pubsname as lockedby,
    if(title!="",title,sessionid) name
  FROM
      Sessions
    JOIN Descriptions USING (sessionid,conid)
    LEFT JOIN Participants on descriptionlockedby = badgeid
  WHERE
    conid=$conid AND
    sessionid=$sessionid
EOD;

if (($result=mysql_query($query,$link))===false) {
  $message_error.=$query."<BR>\nError retrieving lock and name data from database.\n";
  RenderError($title,$message_error);
  exit();
 }
$participant_info_array=mysql_fetch_assoc($result);

/* If there is an update/save to the edited state passed, check for
 what was changed, and update (just) that in the database. */
if (isset($_POST['update'])) {
  for ($i=0; $i<count($descinfo['desctype_array']); $i++) {
    for ($j=0; $j<count($descinfo['desclang_array']); $j++) {
      for ($k=0; $k<count($descinfo['biostate_array']); $k++) {
	for ($l=0; $l<count($descinfo['biodest_array']); $l++) {

	  // Setup for short names and keyname, collapsing all four variables into one passed name.
	  $desctype=$descinfo['desctype_array'][$i];
	  $desclang=$descinfo['desclang_array'][$j];
	  $biostate=$descinfo['biostate_array'][$k];
	  $biodest=$descinfo['biodest_array'][$l];
	  $keyname=$desctype."_".$desclang."_".$biostate."_".$biodest."_bio";

	  // Skip the "staff" and "badge" categories
	  if ($biodest=="staffbook") { continue; }
	  if ($biodest=="staffweb") { continue; }
	  if ($biodest=="badge") { continue; }

	  // Only the "edited" state is updated.
	  if ($biostate!='edited') {continue;}

	  // Clean up the posted string.
	  $teststring=stripslashes(htmlspecialchars_decode($_POST[$keyname]));
	  $descstring=stripslashes(htmlspecialchars_decode($descinfo[$keyname]));

	  // Check for differences, if they exist, update the database.
	  if ($teststring != $descstring) {
	    if ((isset($limit_array['max'][$biodest][$desctype])) and (strlen($teststring)>$limit_array['max'][$biodest][$desctype])) {
	      $message.=ucfirst($biostate)." ".ucfirst($desctype)." ".ucfirst($biodest)." (".$desclang.") Biography";
	      $message.=" too long (".strlen($teststring)." characters), the limit is ".$limit_array['max'][$biodest][$desctype]." characters.";
	    } elseif ((isset($limit_array['min'][$biodest][$desctype])) and (strlen($teststring)<$limit_array['min'][$biodest][$desctype])) {
	      $message.=ucfirst($biostate)." ".ucfirst($desctype)." ".ucfirst($biodest)." (".$desclang.") Biography";
	      $message.=" too short (".strlen($teststring)." characters), the limit is ".$limit_array['min'][$biodest][$desctype]." characters.";
	    } else {
	      if (isset($_POST[$keyname])) {
		update_desc_element($link,$title,$teststring,$sessionid,$conid,$desctype,$desclang,$biostate,$biodest);
	      }
	    }
	    $descinfo[$keyname]=$teststring;
	  }
	}
      }
    }
  }
}

// Copy edited to good
if ((isset($_GET['desctype'])) and (isset($_GET['desclang'])) and (isset($_GET['biodest']))) {
  $desctype=$_GET['desctype'];
  $desclang=$_GET['desclang'];
  $biodest=$_GET['biodest'];
  $goodstring=$descinfo[$desctype."_".$desclang."_edited_".$biodest."_bio"];
  $checkgoodstring=$descinfo[$desctype."_".$desclang."_good_".$biodest."_bio"];
  if ($goodstring!=$checkgoodstring) {
    update_desc_element($link,$title,$goodstring,$sessionid,$conid,$desctype,$desclang,"good",$biodest);
    $descinfo[$desctype."_".$desclang."_good_".$biodest."_bio"]=$goodstring;
  }
}

/* Lock the editing of the participant.
 Returns 0 if succeeded, -2 if lock failed, -1 if db error. */
$lockresult=lock_participant($badgeid);

if ($lockresult==-2) {
  $message_error.="<P>This description is currently being edited by ".htmlspecialchars($participant_info_array['lockedby'])."</P>\n";
 }

$description ="<H2 class=\"head\"><A HREF=\"EditSession.php?id=$sessionid&conid=$conid\">";
$description.=htmlspecialchars($participant_info_array['name'])."</A></H2>\n";

$query = <<<EOD
SELECT
    badgeid
  FROM
      UserHasConRole
    JOIN HasReports USING (conroleid,conid)
  WHERE
    conid=$conid AND
    badgeid=$badgeid
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// See if they are on the appropriate level of staff
$isstaff="T";
if ($element_array[1]['badgeid']!=$badgeid) {$isstaff="F";}

$query = <<<EOD
SELECT
    badgeid
  FROM
      ParticipantOnSession
  WHERE
    conid=$conid AND
    badgeid=$badgeid AND
    volunteer not in ('1', 'Yes') AND
    introducer not in ('1', 'Yes') AND
    aidedecamp not in ('1', 'Yes')
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// See if they are a presenter
$ispresenter="T";
if ($element_array[1]['badgeid']!=$badgeid) {$ispresenter="F";}

$query = <<<EOD
SELECT
    badgeid
  FROM
      VendorStatus
    JOIN VendorStatusTypes USING (vendorstatustypeid)
  WHERE
    conid=$conid AND
    badgeid=$badgeid AND
    vendorstatustypename not like "%Denied%"
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// See if they are a vendor
$isvendor="T";
if ($element_array[1]['badgeid']!=$badgeid) {$isvendor="F";}

// Begin the presenations
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

//Build the self-referential form.
echo "<FORM name=\"desceditform\" method=POST action=\"StaffEditDescs.php\">\n";
echo "<INPUT type=hidden name=\"qno\" value=\"$qno\">\n";
echo "<INPUT type=hidden name=\"conid\" value=\"$conid\">\n";
echo "<INPUT type=hidden name=\"badgeid\" value=\"$sessionid\">\n";
echo "<INPUT type=hidden name=\"badgeids\" value=\"$sessionids\">\n";
echo "<INPUT type=hidden name=\"update\" value=\"Yes\">\n";
echo "<INPUT type=hidden name=\"unlock\" value=\"$sessionid\">\n";

// Top submit button.
echo "<DIV class=\"submit\" id=\"submit\">\n  <BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save Whole Page</BUTTON>\n</DIV>\n";

/* Four-deep array to cover all the variables.  The biostate is now last,
   even though it is normally third, so that the compare one box to the next
   can happen more cleanly.
 */
for ($i=0; $i<count($descinfo['desctype_array']); $i++) {
  for ($j=0; $j<count($descinfo['desclang_array']); $j++) {
    for ($l=0; $l<count($descinfo['biodest_array']); $l++) {
      for ($k=0; $k<count($descinfo['biostate_array']); $k++) {

	// Setup for short names and keyname, collapsing all three variables into one passed name.
	$desctype=$descinfo['desctype_array'][$i];
	$desclang=$descinfo['desclang_array'][$j];
	$biostate=$descinfo['biostate_array'][$k];
	$biodest=$descinfo['biodest_array'][$l];
	$keyname=$desctype."_".$desclang."_".$biostate."_".$biodest."_bio";

	// Skip the "staff" and "badge" categories
	if ($biodest=="staffbook") { continue; }
	if ($biodest=="staffweb") { continue; }
	if ($biodest=="badge") { continue; }

	// Modify the titles for legability, and switch on the readonly for raw.
	$readonly="";
	if ($biostate=='raw') {
	  $readonly="readonly";
	}

	if ($biostate=='good') {
	  if (!empty($descinfo[$keyname])) {
	    echo "<H3>Good entry exists for ".ucfirst($desctype)." ".ucfirst($biodest)." ($desclang) Biography</H3>\n";
	  }
	  continue;
	  }

	// Set up the LABEL.
	echo "<LABEL for=\"$keyname\">".ucfirst($biostate)." ".ucfirst($desctype)." ".ucfirst($biodest)." (".$desclang.") Biography";
	$limit_string="";
	if (isset($limit_array['max'][$biodest][$desctype])) {
	  $limit_string.=" maximum ".$limit_array['max'][$biodest][$desctype];
	}
	if (isset($limit_array['min'][$biodest][$desctype])) {
	  $limit_string.=" minimum ".$limit_array['min'][$biodest][$desctype];
	}
	if ($limit_string !="") {
	  echo " (Limit".$limit_string." characters)";
	}
	echo ":</LABEL><BR>\n";

	// Set up the input box.
	echo "<TEXTAREA $readonly name=\"$keyname\" rows=8 cols=72>".$descinfo[$keyname]."</TEXTAREA><BR><BR>\n";

	// Only on edited should there be a submit button, or promotion button
        if ($biostate=="edited") {
	  echo "<DIV class=\"submit\" id=\"submit\">\n  <BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save Whole Page</BUTTON>\n";
	  if (($descinfo[$desctype."_".$desclang."_raw_".$biodest."_bio"] == $descinfo[$desctype."_".$desclang."_edited_".$biodest."_bio"]) and
	      ($descinfo[$desctype."_".$desclang."_raw_".$biodest."_bio"] != $descinfo[$desctype."_".$desclang."_good_".$biodest."_bio"])) {
	    echo " <A HREF=\"StaffEditDescs.php?qno=$qno&conid=$conid&badgeid=$sessionid&badgeids=$sessionids&desctype=$desctype&desclang=$desclang&biodest=$biodest\">";
	    echo " Promote ".ucfirst($desctype)." ".ucfirst($biodest)." ($desclang) to good.";
	    echo "</A>\n";
	  }
	  echo "</DIV>\n";
	}
      }
    }
  }
}
//echo "<br>\n<DIV class=\"submit\" id=\"submit\">\n  <BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save Whole Page</BUTTON>\n</DIV>\n";
echo "</FORM>\n";
echo "<BR>\n<BR>\n";
correct_footer();
?>

