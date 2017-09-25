<?php
/* create_vendor and edit_vendor functions.  Need more doc. */
function create_vendor ($participant_arr) {
  global $link,$message,$message_error;

  //Temporarily setting newbadgeid, because, that is commented out.
  $newbadgeid=1680;


  // Get the various length limits
  $limit_array=getLimitArray();

  // Get a set of bioinfo, not for the info, but for the arrays.
  $bioinfo=getBioData($_SESSION['badgeid']);

  // Test constraints.

  // Bios test moved into the add bios loop.

  // Too short/long name.
  $error_status=false;
  if (isset($limit_array['min']['web']['name'])) {
    $namemin=$limit_array['min']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) < $namemin) OR
	(strlen($participant_arr['badgename']) < $namemin)) {
      $message_error="All name fields are required and minimum length is $namemin characters.  <BR>\n";
      return array ($message,$message_error);
    }
  }
  if (isset($limit_array['max']['web']['name'])) {
    $namemax=$limit_array['max']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) > $namemax) OR
	(strlen($participant_arr['badgename']) > $namemax)) {
      $message_error="All name fields are required and maximum length is $namemax characters.  <BR>\n";
      return array ($message,$message_error);
    }
  }

  // Invalid email address.
  if (!is_email($participant_arr['email'])) {
    $message_error="Email address: ".$participant_arr['email']." is not valid.  <BR>\n";
    return array ($message,$message_error);
  }

  // Already existing email address.
  $query = "SELECT email FROM CongoDump where email like \"%".$participant_arr['email']."%\"";
  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error="Unable to reach database.<BR>\n$query<BR>\n";
    RenderError($title,$message_error);
    exit();
  }
  if (mysql_num_rows($result) > 0) {
    $message_error="There is already an entry with this email address in the system.<br />\n";
    $message_error.="Please <A HREF\"doLogin.php?newconid=$conid&badgeid=".$participant_arr['email']."\">log in</A>\n";
    $message_error.="instead of trying to re-create yourself.  If you have forgotton your password ";
    $message_error.="you will be prompted to have a new one sent.\n";
    RenderError($title,$message_error);
    exit();
  }

  // Wrong conid
  if ($participant_arr['conid'] != $_SESSION['conid']) {
    $message_error="You seem to have tried to submit this by hand, somehow, please ask for instructions.";
    RenderError($title,$message_error);
  }

  // Get next possible badgeid.
  // WAS: "SELECT MAX(badgeid) FROM Participants WHERE badgeid>='1'";
  $query = "SELECT badgeid FROM Participants ORDER BY ABS(badgeid) DESC LIMIT 1";
  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error="Unrecoverable error updating database.  Database not updated.<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  if (mysql_num_rows($result)!=1) {
    $message_error="Database query returned unexpected number of rows(1 expected).  Database not updated.<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  $maxbadgeid=mysql_result($result,0);
  //error_log("Zambia: SubmitEditCreateParticipant.php: maxbadgeid: $maxbadgeid");
  sscanf($maxbadgeid,"%d",$x);
  $newbadgeid=sprintf("%d",$x+1); // convert to num; add 1; convert back to string

  // Create Participants entry.
  $element_array = array('badgeid', 'password', 'bestway', 'pubsname');
  $value_array=array($newbadgeid,
                     "1111101",
                     "Email",
		     htmlspecialchars_decode($participant_arr['badgename']));
  $message.=submit_table_element($link, $title, "Participants", $element_array, $value_array);

  // Add vendor status - should fail if vendor status entry already exists.
  $element_array = array('conid', 'badgeid', 'vendorstatustypeid');
  $value_array=array($_SESSION['conid'],$newbadgeid,"2");
  $message.=submit_table_element($link, $title, "VendorStatus", $element_array, $value_array);


  // Add Bios.
  // We are only updating the raw bios here, so only a 3-depth
  // search happens on biolang, biotypename, and biodest.

  $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
      for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

	// Setup for keyname, to collapse all three variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$i];
	$biolang=$bioinfo['biolang_array'][$j];
	// $biostate=$bioinfo['biostate_array'][$k];
	$biodest=$bioinfo['biodest_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Length-check the values.
	$biotext=stripslashes(htmlspecialchars_decode($participant_arr[$keyname]));
	if ((isset($limit_array['max'][$biodest][$biotype])) and (strlen($biotext)>$limit_array['max'][$biodest][$biotype])) {
	  $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	  $message.=" too long (".strlen($biotext)." characters), the limit is ".$limit_array['max'][$biodest][$biotype]." characters.";
	} elseif ((isset($limit_array['min'][$biodest][$biotype])) and (strlen($biotext)<$limit_array['min'][$biodest][$biotype])) {
	  $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	  $messaage.=" too short (".strlen($biotext)." characters), the limit is ".$limit_array['min'][$biodest][$biotype]." characters.";
	} else {
	  update_bio_element($link,$title,$biotext,$newbadgeid,$biotype,$biolang,$biostate,$biodest);
	}
      }
    }
  }

  // Create CongoDump entry.
  $element_array = array('badgeid', 'firstname', 'lastname', 'badgename', 'phone', 'email', 'postaddress1', 'postaddress2', 'postcity', 'poststate', 'postzip', 'postcountry', 'regtype');
  $value_array=array($newbadgeid,
		     htmlspecialchars_decode($participant_arr['firstname']),
		     htmlspecialchars_decode($participant_arr['lastname']),
		     htmlspecialchars_decode($participant_arr['badgename']),
		     htmlspecialchars_decode($participant_arr['phone']),
		     htmlspecialchars_decode($participant_arr['email']),
		     htmlspecialchars_decode($participant_arr['postaddress1']),
		     htmlspecialchars_decode($participant_arr['postaddress2']),
		     htmlspecialchars_decode($participant_arr['postcity']),
		     htmlspecialchars_decode($participant_arr['poststate']),
		     htmlspecialchars_decode($participant_arr['postzip']),
		     htmlspecialchars_decode($participant_arr['postcountry']),
		     htmlspecialchars_decode($participant_arr['regtype']));
  $message.=submit_table_element($link, $title, "CongoDump", $element_array, $value_array);

  // Assign permissions by getting the right Permission Role
  $query="SELECT permroleid FROM PermissionRoles WHERE permrolename='Vendor'";
  list($permissionrows,$permissionheader_array,$permission_array)=queryreport($query,$link,$title,$description,0);
  if ($permissionrows != 1) {
    $message_error.="Somehow there are more or less Permission Rows maping to Vendor.";
    $message_error.="Please check your database for inconsistencies, or suggest a change.\n";
    RenderError($title,$message_error);
  }
  $vendorpermrole=$permission_array[1]['permroleid'];

  $element_array = array('badgeid', 'permroleid', 'conid');
  $value_array=array($newbadgeid, $vendorpermrole, $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "UserHasPermissionRole", $element_array, $value_array);

  // Set the Vendor Self Carry to the appropriate value.
  $vendorselfcarry="N";
  if ($participant_arr['vendorselfcarry'] == "Yes") {
    $vendorselfcarry="Y";
  }

  // Create Annual information entry with Vendor When Applied, Self Carry and Notes (w/ times at FFF)
  $element_array = array('conid','badgeid','vendorwhenapplied','vendorselfcarry','vendornotes');
  $value_array=array($_SESSION['conid'],
		     $newbadgeid,
		     htmlspecialchars_decode($participant_arr['vendorwhenapplied']),
		     $vendorselfcarry,
		     htmlspecialchars_decode($participant_arr['vendornotes'] .
					     "\nNumber of times vending at the FFF: " .
					     $participant_arr['times_at_fff']));
  $message.=submit_table_element($link, $title, "VendorAnnualInfo", $element_array, $value_array);

  // Create VendorIs (vendortypeid) entries
  foreach ($participant_arr['wasvendortypeid'] as $key => $value) {
    if (($participant_arr['wasvendortypeid'][$key]=="not") and
	($participant_arr['vendortypeid'][$key]=="checked")) {
      $element_array = array('badgeid', 'vendortypeid');
      $value_array = array($newbadgeid, $key);
      $message.=submit_table_element($link, $title, "VendorIs", $element_array, $value_array);
    }
    if (($participant_arr['wasvendortypeid'][$key]=="indeed") and
	($participant_arr['vendortypeid'][$key]!="checked")) {
      $match_string="badgeid=".$newbadgeid." AND vendortypeid=".$key;
      $message.=delete_table_element($link, $title, "VendorIs",$match_string);
    }
  }

  // Create this year's Vendor Features walking the keys/values
  foreach ($participant_arr['wasvendorfeatureid'] as $key => $value) {

    // Test to see if the inputs are actually useful and numeric
    if (is_numeric($participant_arr['vendorfeatureid'][$key])) {

      // If not previously set, and is now set.
      if(($value=="0") and
	 ($participant_arr['vendorfeatureid'][$key]!="0")) {
	$element_array = array('badgeid', 'vendorfeatureid','vendorfeaturecount');
	$value_array = array($newbadgeid,
			     $key,
			     $participant_arr['vendorfeatureid'][$key]);
	$message.=submit_table_element($link, $title, "VendorHasFeature", $element_array, $value_array);
      }

      // If set previously and is now set to zero.
      elseif (($value!="0") and
	      ($participant_arr['vendorfeatureid'][$key]=="0")) {
	$match_string="badgeid=".$newbadgeid." AND vendorfeatureid=".$key;
	$message.=delete_table_element($link, $title, "VendorHasFeature",$match_string);
      }

      // If previously set to something else.
      elseif ($participant_arr['wasvendorfeatureid'][$key] !=
	      $participant_arr['vendorfeatureid'][$key]) {
	$set_array=array("vendorfeaturecount=".$participant_arr['vendorfeatureid'][$key]);
        $match_string="badgeid=".$newbadgeid." AND vendorfeatureid=".$key;
	$message.=update_table_element_extended_match($link, $title, "VendorHasFeature", $set_array, $match_string);
      }
    }
  }

  // Create this year's Vendor Space preferences
  if ((!empty($participant_arr['vendorspacerank_1'])) and
      (is_numeric($participant_arr['vendorspacerank_1']))) {
    $element_array = array('badgeid','vendorspaceid','vendorprefspacerank');
    $value_array=array($newbadgeid,$participant_arr['vendorspacerank_1'],"1st");
    $message.=submit_table_element($link, $title, "VendorPrefSpace", $element_array, $value_array);
  }

  if ((!empty($participant_arr['vendorspacerank_2'])) and
      (is_numeric($participant_arr['vendorspacerank_2']))) {
    $element_array = array('badgeid','vendorspaceid','vendorprefspacerank');
    $value_array=array($newbadgeid,$participant_arr['vendorspacerank_2'],"2nd");
    $message.=submit_table_element($link, $title, "VendorPrefSpace", $element_array, $value_array);
  }

  if ((!empty($participant_arr['vendorspacerank_3'])) and
      (is_numeric($participant_arr['vendorspacerank_3']))) {
    $element_array = array('badgeid','vendorspaceid','vendorprefspacerank');
    $value_array=array($newbadgeid,$participant_arr['vendorspacerank_3'],"3rd");
    $message.=submit_table_element($link, $title, "VendorPrefSpace", $element_array, $value_array);
  }


  // Add Sponsor Level - initial
  if ((!empty($participant_arr['sponsorlevelid'])) and
      (is_numeric($participant_arr['sponsorlevelid']))) {
    $element_array = array('badgeid','sponsorlevelid','sponsorlevelcount');
    $value_array=array($newbadgeid,$participant_arr['sponsorlevelid'],"1");
    $message.=submit_table_element($link, $title, "VendorHasSponsorLevel", $element_array, $value_array);
  }

  // Add Print Ad - initial
  if ((!empty($participant_arr['printadid'])) and
      (is_numeric($participant_arr['printadid']))) {
    $element_array = array('badgeid','printadid','printadcount');
    $value_array=array($newbadgeid,$participant_arr['printadid'],"1");
    $message.=submit_table_element($link, $title, "VendorHasPrintAd", $element_array, $value_array);
  }

  // Add Digital Ad - initial
  if ((!empty($participant_arr['digitaladid'])) and
      (is_numeric($participant_arr['digitaladid']))) {
    $element_array = array('badgeid','digitaladid','digitaladcount');
    $value_array=array($newbadgeid,$participant_arr['digitaladid'],"1");
    $message.=submit_table_element($link, $title, "VendorHasDigitalAd", $element_array, $value_array);
  }

  // Submit a note about what was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($newbadgeid,
                     $_SESSION['badgeid'],
                     "Created new Vendor entry: $message",
		     $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "NotesOnVendors", $element_array, $value_array);

  // Make $message additive (.=) to get all the information
  $message="Database updated successfully with ".$participant_arr["badgename"].".<BR>";
  // return array ($message,$message_error);
}

function edit_vendor ($participant_arr) {
  global $link,$message,$message_error;

  // Get the various length limits
  $limit_array=getLimitArray();

  // Get a set of bioinfo, and compare below.
  $bioinfo=getBioData($participant_arr['partid']);

  // Test constraints.

  // Too short/long name.
  if (isset($limit_array['min']['web']['name'])) {
    $namemin=$limit_array['min']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) < $namemin) OR
	(strlen($participant_arr['badgename']) < $namemin)) {
      $message_error="All name fields are required and minimum length is $namemin characters.  <BR>\n";
    }
  }
  if (isset($limit_array['max']['web']['name'])) {
    $namemax=$limit_array['max']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) > $namemax) OR
	(strlen($participant_arr['badgename']) > $namemax)) {
      $message_error="All name fields are required and maximum length is $namemax characters.  <BR>\n";
    }
  }

  // Invalid email.
  if (!is_email($participant_arr['email'])) {
    $message_error="Email address: ".$participant_arr['email']." is not valid.  <BR>\n";
  }

  // Update Participants entry.
  $pairedvalue_array=array("bestway='".mysql_real_escape_string($participant_arr['bestway'])."'",
			   "altcontact='".mysql_real_escape_string($participant_arr['altcontact'])."'",
			   "prognotes='".mysql_real_escape_string(stripslashes($participant_arr['prognotes']))."'",
			   "pubsname='".mysql_real_escape_string(stripslashes($participant_arr['badgename']))."'");
  $message.=update_table_element($link, $title, "Participants", $pairedvalue_array, "badgeid", $participant_arr['partid']);

  // Update CongoDump entry.
  $pairedvalue_array=array("firstname='".mysql_real_escape_string(stripslashes($participant_arr['firstname']))."'",
			   "lastname='".mysql_real_escape_string(stripslashes($participant_arr['lastname']))."'",
			   "badgename='".mysql_real_escape_string(stripslashes($participant_arr['badgename']))."'",
			   "phone='".mysql_real_escape_string($participant_arr['phone'])."'",
			   "email='".mysql_real_escape_string($participant_arr['email'])."'",
			   "postaddress1='".mysql_real_escape_string(stripslashes($participant_arr['postaddress1']))."'",
			   "postaddress2='".mysql_real_escape_string(stripslashes($participant_arr['postaddress2']))."'",
			   "postcity='".mysql_real_escape_string(stripslashes($participant_arr['postcity']))."'",
			   "poststate='".mysql_real_escape_string($participant_arr['poststate'])."'",
			   "postzip='".mysql_real_escape_string($participant_arr['postzip'])."'",
			   "regtype='".mysql_real_escape_string(stripslashes($participant_arr['regtype']))."'");
  $message.=update_table_element($link, $title, "CongoDump", $pairedvalue_array, "badgeid", $participant_arr['partid']);

  // Update Interested entry.
  if (isset($participant_arr['interested']) AND ($participant_arr['interested']!='') AND ($participant_arr['interested']!=0)) {
    $query ="UPDATE Interested SET ";
    $query.="interestedtypeid=".$participant_arr['interested']." ";
    $query.="WHERE badgeid=\"".$participant_arr['partid']."\" AND conid=".$_SESSION['conid'];
    if (!mysql_query($query,$link)) {
      $message.=$query."<BR>Error updating Interested table.  Database not update.";
    }
    ereg("Rows matched: ([0-9]*)", mysql_info($link), $r_matched);
    if ($r_matched[1]==0) {
      $element_array=array('conid','badgeid','interestedtypeid');
      $value_array=array($_SESSION['conid'], $participant_arr['partid'], mysql_real_escape_string(stripslashes($participant_arr['interested'])));
      $message.=submit_table_element($link,"Admin Participants","Interested", $element_array, $value_array);
    } elseif ($r_matched[1]>1) {
      $message.="There might be something wrong with the table, there are multiple interested elements for this year.";
    }
  }

  // Update/add Bios.
  /* We are only updating the raw bios here, so only a 3-depth
   search happens on biolang, biotypename and biodest. */
  $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
      for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

	// Setup for keyname, to collapse all three variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$i];
	$biolang=$bioinfo['biolang_array'][$j];
	// $biostate=$bioinfo['biostate_array'][$k];
	$biodest=$bioinfo['biodest_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Clean up the posted string
	$teststring=stripslashes(htmlspecialchars_decode($participant_arr[$keyname]));
	$biostring=stripslashes(htmlspecialchars_decode($bioinfo[$keyname]));

	if ($teststring != $biostring) {
	  if ((isset($limit_array['max'][$biodest][$biotype])) and (strlen($teststring)>$limit_array['max'][$biodest][$biotype])) {
	    $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	    $message.=" too long (".strlen($teststring)." characters), the limit is ".$limit_array['max'][$biodest][$biotype]." characters.";
	  } elseif ((isset($limit_array['min'][$biodest][$biotype])) and (strlen($teststring)<$limit_array['min'][$biodest][$biotype])) {
	    $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	    $message.=" too short (".strlen($teststring)." characters), the limit is ".$limit_array['min'][$biodest][$biotype]." characters.";
	  } else {
	    update_bio_element($link,$title,$teststring,$participant_arr['partid'],$biotype,$biolang,$biostate,$biodest);
	  }
	}
      }
    }
  }

  // Submit a note about what was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($participant_arr['partid'],
                     $_SESSION['badgeid'],
                     mysql_real_escape_string(htmlspecialchars_decode($participant_arr['note'])),
		     $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "NotesOnParticipants", $element_array, $value_array);

  // Update permissions
  foreach ($participant_arr['waspermroleid'] as $key => $value) {
    if (($participant_arr['waspermroleid'][$key]=="not") and
	($participant_arr['permroleid'][$key]=="checked")) {
      $element_array = array('badgeid', 'permroleid', 'conid');
      $value_array = array($participant_arr['partid'], $key, $_SESSION['conid']);
      $message.=submit_table_element($link, $title, "UserHasPermissionRole", $element_array, $value_array);
    }
    if (($participant_arr['waspermroleid'][$key]=="indeed") and
	($participant_arr['permroleid'][$key]!="checked")) {
      $match_string="badgeid=".$participant_arr['partid']." AND permroleid=".$key." AND conid=".$_SESSION['conid'];
      $message.=delete_table_element($link, $title, "UserHasPermissionRole",$match_string);
    }
  }

  // Update con roles
  if (isset($participant_arr['wasconroleid'])) {
    foreach ($participant_arr['wasconroleid'] as $key => $value) {
      if (($participant_arr['wasconroleid'][$key]=="not") and
	  ($participant_arr['conroleid'][$key]=="checked")) {
	$element_array = array('badgeid', 'conroleid', 'conid');
	$value_array = array($participant_arr['partid'], $key, $_SESSION['conid']);
	$message.=submit_table_element($link, $title, "UserHasConRole", $element_array, $value_array);
      }
      if (($participant_arr['wasconroleid'][$key]=="indeed") and
	  ($participant_arr['conroleid'][$key]!="checked")) {
	$match_string="badgeid=".$participant_arr['partid']." AND conroleid=".$key." AND conid=".$_SESSION['conid'];
	$message.=delete_table_element($link, $title, "UserHasConRole",$match_string);
      }
    }
  }

  // Make $message additive (.=) to get all the information
  $message="Database updated successfully.<BR>";
}
