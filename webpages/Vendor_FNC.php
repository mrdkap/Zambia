<?php
/* Function populate_radio_block_number_table_from_array($label, $index_key, $count_key, $max_key, $desc_key, $info_array)
   Reads parameters (see below) and an array to work from and
   produces a table that contains a description and a series
   of numbered radio buttons that can be selected.
   $label: The label base for the form, indexed by the index_key
   $index_key: The row of the info_array that is the index
   $count_key: The row of the info_array that is the count/value already selected
   $max_key: The row of the info_array that is the max count/value able to be selected
   $desc_key: The row of the info_array that is the description of what is being selected
   $button_array: The array of possible items */

// Function to create the radio array:
function populate_radio_block_number_table_from_array($label, $index_key, $count_key, $max_key, $desc_key, $info_array) {
  // Start the table
  $returnstring="<table border=1>\n  <tbody>\n";

  // Walk the button_array string for the relevant information
  for ($i=1; $i<=count($info_array); $i++) {

    // Zero the array, and then present the array from 0-max as the
    // radio button array possibilities, with the key and value the
    // same.
    $button_array=array();
    for ($j=0; $j<=$info_array[$i][$max_key]; $j++) {
      $button_array[$j + 1]['key']=$j;
      $button_array[$j + 1]['value']=$j;
    }

    // Label for the table
    $returnstring.="    <tr><td>" . $info_array[$i][$desc_key] . "<br />\n";

    // Button row for the table
    $returnstring.="      " . populate_radio_block_from_array($label . "[" . $info_array[$i][$index_key] . "]", $info_array[$i][$count_key],"key","value",$button_array) . "</td></tr>\n";
  }
  $returnstring.="  </tbody>\n</table>\n";

  return($returnstring);
}

/* Function edit_vendor_apply ($work_arr)
   Takes an array of values (possibly from $_POST) and updates the
   Annual table (and other appropriate event-based tables) with the
   information for this event.
   The array is looking for:
    . vendorselfcarry: "Yes" or "No" - will they self-carry
    . times_at_fff: string - how many times.  Needs care and attention.
    . vendorwhenapplied: (only when new) date - when applied
    . vendornotes: string - Any pertenant notes
    . wasvendorfeatureid[key,value]: array - previously set vendor
      feature ID numbers and values
    . vendorfeatureid[key,value]: current vendor feature ID numbers
      and values
    . wasvendorspacerank_[123]: previous ranked space requests
    . vendorspacerank_[123]: current ranked space requests
    . wassponsorlevelid[key,value]: array - previously set sponsor
      level ID numbers and values
    . sponsorlevelid[key,value]: current sponsor level ID numbers and
      values
    . wasprintadid[key,value]: array - previously set print ad ID
      numbers and values
    . printadid[key,value]: current print ad ID numbers and values
    . wasdigitaladid[key,value]: array - previously set digital ad ID
      numbers and values
    . digitaladid[key,value]: current digital ad ID numbers and values */
function edit_vendor_apply ($work_arr) {
  global $link,$message,$message_error;
  $conid=$_SESSION['conid'];

  // This might want to be changed, for others to apply for one.
  $badgeid=$work_arr['badgeid'];

  // If no vendor status exists, create one as "Applied"
  $queryVendorStatusType="SELECT vendorstatustypeid FROM VendorStatusTypes WHERE vendorstatustypename='Applied'";
  list($vendorstatustyperows,$vendorstatustypeheader_array,$vendorstatustype_array)=queryreport($queryVendorStatusType,$link,$title,$description,0);
  if ($vendorstatustyperows != 1) {
    $message_error.="Somehow there are more or less Vendor Status Types maping to Applied.";
    $message_error.="Please check your database for inconsistencies, or suggest a change.\n";
    RenderError($title,$message_error);
    exit();
  }
  $vendorstatusapplied=$vendorstatustype_array[1]['vendorstatustypeid'];

  $queryVendorStatus="SELECT vendorstatustypeid FROM VendorStatus WHERE conid=$conid AND badgeid=$badgeid";
  list($vstatrows,$vstatheader_array,$vstat_array)=queryreport($queryVendorStatus,$link,$title,$description,0);

  if ($vstatrows == 0) {
    $element_array = array('conid', 'badgeid', 'vendorstatustypeid');
    $value_array=array($_SESSION['conid'],$badgeid,$vendorstatusapplied);
    $message.=submit_table_element($link, $title, "VendorStatus", $element_array, $value_array);
  }

  // If not set as a Vendor roll in UserHasPermissionRole, set it
  $queryPermissionRole="SELECT permroleid FROM PermissionRoles WHERE permrolename='Vendor'";
  list($permissionrolerows,$permissionrowheader_array,$permissionrole_array)=queryreport($queryPermissionRole,$link,$title,$description,0);
  if ($permissionrolerows != 1) {
    $message_error.="Somehow there are more or less Permission Rows maping to Vendor.";
    $message_error.="Please check your database for inconsistencies, or suggest a change.\n";
    RenderError($title,$message_error);
    exit();
  }
  $permrolevendor=$permissionrole_array[1]['permroleid'];

  $queryPermission="SELECT permroleid FROM UserHasPermissionRole WHERE conid=$conid AND badgeid=$badgeid AND permroleid=$permrolevendor";
  list($vpermrows,$vpermheader_array,$vperm_array)=queryreport($queryPermission,$link,$title,$description,0);

  if ($vpermrows == 0) {
    $element_array = array('badgeid', 'permroleid', 'conid');
    $value_array=array($badgeid, $permrolevendor, $_SESSION['conid']);
    $message.=submit_table_element($link, $title, "UserHasPermissionRole", $element_array, $value_array);
  }

  // Set the Vendor Self Carry to the appropriate value.
  $vendorselfcarry="'N'";
  if ($work_arr['vendorselfcarry'] == "Yes") {
    $vendorselfcarry="'Y'";
  }

  // Add the number of times at the FFF to the vendor notes, if it exists.
  if (!empty($work_arr['times_at_fff'])) {
    $work_arr['vendornotes'].="\nNumber of times vending at the FFF: " . $work_arr['times_at_fff'];
  }

  // If Annual information does not exist create it, otherwise update it.
  // Vendor When Applied (only on creation), Self Carry and Notes (w/ times at FFF)
  $queryVendorAnnual=<<<EOD
SELECT
    vendorselfcarry,
    vendornotes
  FROM
      VendorAnnualInfo
  WHERE
    conid=$conid AND
    badgeid=$badgeid
EOD;
  list($vannualrows,$vannualheader_array,$vannual_array)=queryreport($queryVendorAnnual,$link,$title,$description,0);

  if ($vannualrows == 0) {
    $element_array = array('conid','badgeid','vendorwhenapplied','vendorselfcarry','vendornotes');
    $value_array=array($_SESSION['conid'],
		       $badgeid,
		       htmlspecialchars_decode($work_arr['vendorwhenapplied']),
		       $vendorselfcarry,
		       htmlspecialchars_decode($work_arr['vendornotes']));
    $message.=submit_table_element($link, $title, "VendorAnnualInfo", $element_array, $value_array);
  } else {
    $set_array=array();
    if ($work_arr['wasvendorselfcarry'] != $work_arr['vendorselfcarry']) {
      $set_array[]="vendorselfcarry=$vendorselfcarry";
    }
    if ($work_arr['wasvendornotes'] != $work_arr['vendornotes']) {
      $set_array[]="vendornotes='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($work_arr['vendornotes'])))."'";
    }
    if (!empty($set_array)) {
      $match_string="conid=$conid AND badgeid=$badgeid";
      $message.=update_table_element_extended_match($link, $title, "VendorAnnualInfo", $set_array, $match_string);
    }
  }

  // create/update this event's Vendor Features by walking the keys/values
  foreach ($work_arr['wasvendorfeatureid'] as $key => $value) {

    // Test to see if the inputs are actually useful and numeric
    if (is_numeric($work_arr['vendorfeatureid'][$key])) {

      // If not previously set, and is now set.
      if(($value=="0") and
	 ($work_arr['vendorfeatureid'][$key]!="0")) {
	$element_array = array('badgeid', 'vendorfeatureid','vendorfeaturecount');
	$value_array = array($badgeid,
			     $key,
			     $work_arr['vendorfeatureid'][$key]);
	$message.=submit_table_element($link, $title, "VendorHasFeature", $element_array, $value_array);
      }

      // If set previously and is now set to zero.
      elseif (($value!="0") and
	      ($work_arr['vendorfeatureid'][$key]=="0")) {
	$match_string="badgeid=".$badgeid." AND vendorfeatureid=".$key;
	$message.=delete_table_element($link, $title, "VendorHasFeature",$match_string);
      }

      // If previously set to something else.
      elseif ($work_arr['wasvendorfeatureid'][$key] !=
	      $work_arr['vendorfeatureid'][$key]) {
	$set_array=array("vendorfeaturecount=".$work_arr['vendorfeatureid'][$key]);
        $match_string="badgeid=".$badgeid." AND vendorfeatureid=".$key;
	$message.=update_table_element_extended_match($link, $title, "VendorHasFeature", $set_array, $match_string);
      }
    }
  }

  // Create/update this event's Vendor Space preferences
  // I don't really care what the "was" is, if there is anything set
  // in any of the vendorspaceranks, then, wipe these, and reset to
  // the new selections.  That way we won't end up with an attempt at
  // setting the same selection as multiple choices.

  // Check to see if I can skip this whole step
  if (($work_arr['wasvendorspacerank_1'] != $work_arr['vendorspacerank_1']) or
      ($work_arr['wasvendorspacerank_2'] != $work_arr['vendorspacerank_2']) or
      ($work_arr['wasvendorspacerank_3'] != $work_arr['vendorspacerank_3'])) {
    // Get this event's list of vendorspaceid elements and convert them to a string
    $queryVendorSpace="SELECT vendorspaceid FROM VendorSpace WHERE conid=$conid";
    list($vendorspacerows,$vendorspaceheader_array,$vendorspace_array)=queryreport($queryVendorSpace,$link,$title,$description,0);
    for ($i=1; $i<=$vendorspacerows; $i++) {
      $convendorspace_array[]=$vendorspace_array[$i]['vendorspaceid'];
    }

    // Make string
    $convendorspace_string="'" . implode("','", $convendorspace_array) . "'";

    // If there was any elements, delete them
    if (((!empty($work_arr['vendorspacerank_1'])) and
	 (is_numeric($work_arr['vendorspacerank_1']))) or
	((!empty($work_arr['vendorspacerank_2'])) and
	 (is_numeric($work_arr['vendorspacerank_2']))) or
	((!empty($work_arr['vendorspacerank_3'])) and
	 (is_numeric($work_arr['vendorspacerank_3'])))) {
      $match_string="badgeid=".$badgeid." AND vendorspaceid in (" . $convendorspace_string . ")";
      $message.=delete_table_element($link, $title, "VendorPrefSpace",$match_string);
    }

    // Set the new elements up
    if ((!empty($work_arr['vendorspacerank_1'])) and
	(is_numeric($work_arr['vendorspacerank_1']))) {
      $element_array = array('badgeid','vendorspaceid','vendorprefspacerank');
      $value_array=array($badgeid,$work_arr['vendorspacerank_1'],"1st");
      $message.=submit_table_element($link, $title, "VendorPrefSpace", $element_array, $value_array);
    }

    if ((!empty($work_arr['vendorspacerank_2'])) and
	(is_numeric($work_arr['vendorspacerank_2']))) {
      $element_array = array('badgeid','vendorspaceid','vendorprefspacerank');
      $value_array=array($badgeid,$work_arr['vendorspacerank_2'],"2nd");
      $message.=submit_table_element($link, $title, "VendorPrefSpace", $element_array, $value_array);
    }

    if ((!empty($work_arr['vendorspacerank_3'])) and
	(is_numeric($work_arr['vendorspacerank_3']))) {
      $element_array = array('badgeid','vendorspaceid','vendorprefspacerank');
      $value_array=array($badgeid,$work_arr['vendorspacerank_3'],"3rd");
      $message.=submit_table_element($link, $title, "VendorPrefSpace", $element_array, $value_array);
    }
  }

  // create/update this event's Sponsor Levels by walking the keys/values
  foreach ($work_arr['wassponsorlevelid'] as $key => $value) {

    // Test to see if the inputs are actually useful and numeric
    if (is_numeric($work_arr['sponsorlevelid'][$key])) {

      // If not previously set, and is now set.
      if(($value=="0") and
	 ($work_arr['sponsorlevelid'][$key]!="0")) {
	$element_array = array('badgeid', 'sponsorlevelid','sponsorlevelcount');
	$value_array = array($badgeid,
			     $key,
			     $work_arr['sponsorlevelid'][$key]);
	$message.=submit_table_element($link, $title, "VendorHasSponsorLevel", $element_array, $value_array);
      }

      // If set previously and is now set to zero.
      elseif (($value!="0") and
	      ($work_arr['sponsorlevelid'][$key]=="0")) {
	$match_string="badgeid=".$badgeid." AND sponsorlevelid=".$key;
	$message.=delete_table_element($link, $title, "VendorHasSponsorLevel",$match_string);
      }

      // If previously set to something else.
      elseif ($work_arr['wassponsorlevelid'][$key] !=
	      $work_arr['sponsorlevelid'][$key]) {
	$set_array=array("sponsorlevelcount=".$work_arr['sponsorlevelid'][$key]);
        $match_string="badgeid=".$badgeid." AND sponsorlevelid=".$key;
	$message.=update_table_element_extended_match($link, $title, "VendorHasSponsorLevel", $set_array, $match_string);
      }
    }
  }

  // create/update this event's Print Ads by walking the keys/values
  foreach ($work_arr['wasprintadid'] as $key => $value) {

    // Test to see if the inputs are actually useful and numeric
    if (is_numeric($work_arr['printadid'][$key])) {

      // If not previously set, and is now set.
      if(($value=="0") and
	 ($work_arr['printadid'][$key]!="0")) {
	$element_array = array('badgeid', 'printadid','printadcount');
	$value_array = array($badgeid,
			     $key,
			     $work_arr['printadid'][$key]);
	$message.=submit_table_element($link, $title, "VendorHasPrintAd", $element_array, $value_array);
      }

      // If set previously and is now set to zero.
      elseif (($value!="0") and
	      ($work_arr['printadid'][$key]=="0")) {
	$match_string="badgeid=".$badgeid." AND printadid=".$key;
	$message.=delete_table_element($link, $title, "VendorHasPrintAd",$match_string);
      }

      // If previously set to something else.
      elseif ($work_arr['wasprintadid'][$key] !=
	      $work_arr['printadid'][$key]) {
	$set_array=array("printadcount=".$work_arr['printadid'][$key]);
        $match_string="badgeid=".$badgeid." AND printadid=".$key;
	$message.=update_table_element_extended_match($link, $title, "VendorHasPrintAd", $set_array, $match_string);
      }
    }
  }

  // create/update this event's Digital Ads by walking the keys/values
  foreach ($work_arr['wasdigitaladid'] as $key => $value) {

    // Test to see if the inputs are actually useful and numeric
    if (is_numeric($work_arr['digitaladid'][$key])) {

      // If not previously set, and is now set.
      if(($value=="0") and
	 ($work_arr['digitaladid'][$key]!="0")) {
	$element_array = array('badgeid', 'digitaladid','digitaladcount');
	$value_array = array($badgeid,
			     $key,
			     $work_arr['digitaladid'][$key]);
	$message.=submit_table_element($link, $title, "VendorHasDigitalAd", $element_array, $value_array);
      }

      // If set previously and is now set to zero.
      elseif (($value!="0") and
	      ($work_arr['digitaladid'][$key]=="0")) {
	$match_string="badgeid=".$badgeid." AND digitaladid=".$key;
	$message.=delete_table_element($link, $title, "VendorHasDigitalAd",$match_string);
      }

      // If previously set to something else.
      elseif ($work_arr['wasdigitaladid'][$key] !=
	      $work_arr['digitaladid'][$key]) {
	$set_array=array("digitaladcount=".$work_arr['digitaladid'][$key]);
        $match_string="badgeid=".$badgeid." AND digitaladid=".$key;
	$message.=update_table_element_extended_match($link, $title, "VendorHasDigitalAd", $set_array, $match_string);
      }
    }
  }

  // Submit a note about what was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($badgeid,
                     $_SESSION['badgeid'],
                     "Created/Updated Vendor Application entry: $message",
		     $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "NotesOnVendors", $element_array, $value_array);

  // Make $message additive (.=) to get all the information
  $message="Database updated successfully.<br />";
  // return array ($message,$message_error);
}

/* Function edit_vendor_update ($work_arr)
   Takes an array of values (possibly from $_POST) and updates a
   vendor's persistent information.
   The array is looking for:
   . conid: number - current conid
   . wasfirstname: string - current first name
   . firstname: string - new first name
   . waslastname: string - current last name
   . lastname: string - new last name
   . wasbadgename: string - current badge name and pubs name
   . badgename: string - new badge name and pubs name
   . wasphone: string - current phone number
   . phone: string - new phone number
   . wasemail: string - current email address
   . email: string - new email address
   . waspostaddress1: string - current first line of the postal address
   . postaddress1: string - new first line of the postal address
   . waspostaddress2: string - current second line of the postal address
   . postaddress2: string - new second line of the postal address
   . waspostcity: string - current city for the postal address
   . postcity: string - new city for the postal address
   . waspostzip: string - current zip code for the postal address
   . postzip: string - new zip code for the postal address
   . waspostcountry: string - current country name for the postal address
   . postcountry: string - new country name for the postal address
   . wasregtype: string - current registration type, should match RegTypes
   . regtype: string - new registration type, should match RegTypes
   . opassword: string - current password
   . npassword: string - first pass new password
   . ncpassword: string - second pass new password
   . set of bio updates
   . wasvendortypeid[key,value]: array of keys and if they were
     checked before for the various vendor types that could be
     selected.
   . vendortypeid[key,value]: array of keys and if they were checked
     now for the various vendor types that could be selected.
*/
function edit_vendor_update ($work_arr) {
  global $link,$message,$message_error;
  $conid=$_SESSION['conid'];

  // This might want to be changed, for others to apply for one.
  $badgeid=$work_arr['badgeid'];

  // Get the various length limits
  $limit_array=getLimitArray();

  // Get a set of bioinfo, not for the info, but for the arrays.
  $bioinfo=getBioData($badgeid);

  // Test constraints.

  // Bios test moved into the add bios loop.

  // Too short/long name.
  $error_status=false;
  if (isset($limit_array['min']['web']['name'])) {
    $namemin=$limit_array['min']['web']['name'];
    if ((strlen($work_arr['firstname'])+strlen($work_arr['lastname']) < $namemin) OR
	(strlen($work_arr['badgename']) < $namemin)) {
      $message_error="All name fields are required and minimum length is $namemin characters.  <BR>\n";
      return array ($message,$message_error);
    }
  }
  if (isset($limit_array['max']['web']['name'])) {
    $namemax=$limit_array['max']['web']['name'];
    if ((strlen($work_arr['firstname'])+strlen($work_arr['lastname']) > $namemax) OR
	(strlen($work_arr['badgename']) > $namemax)) {
      $message_error="All name fields are required and maximum length is $namemax characters.  <BR>\n";
      return array ($message,$message_error);
    }
  }

  // Invalid email address.
  if (!is_email($work_arr['email'])) {
    $message_error="Email address: ".$work_arr['email']." is not valid.  <BR>\n";
    return array ($message,$message_error);
  }

  // Wrong conid
  if ($work_arr['conid'] != $_SESSION['conid']) {
    $message_error="You seem to have tried to submit this by hand, somehow, please ask for instructions.";
    RenderError($title,$message_error);
    exit();
  }

  // Brainstorm creation of a vendor, badgeid would be 100
  if ($badgeid == "100") {

    // Already existing email address.
    $query = "SELECT email FROM Participants where email like \"%".$work_arr['email']."%\"";
    $result=mysqli_query($link,$query);
    if (!$result) {
      $message_error="Unable to reach database.<BR>\n$query<BR>\n";
      RenderError($title,$message_error);
      exit();
    }
    if (mysqli_num_rows($result) > 0) {
      $message_error="There is already an entry with this email address in the system.</P>\n";
      $message_error.="<P>Please <A HREF\"doLogin.php?newconid=$conid&badgeid=".$work_arr['email']."\">log in</A>\n";
      $message_error.="instead of trying to re-create yourself.  If you have forgotton your password ";
      $message_error.="you will be prompted to have a new one sent.</P>\n";
      RenderError($title,$message_error);
      exit();
    }

    // Get next possible badgeid.
    // WAS: "SELECT MAX(badgeid) FROM Participants WHERE badgeid>='1'";
    $query = "SELECT badgeid FROM Participants ORDER BY ABS(badgeid) DESC LIMIT 1";
    $result=mysqli_query($link,$query);
    if (!$result) {
      $message_error="Unrecoverable error updating database.  Database not updated.<BR>\n";
      $message_error.=$query;
      RenderError($title,$message_error);
      exit();
    }
    if (mysqli_num_rows($result)!=1) {
      $message_error="Database query returned unexpected number of rows(1 expected).  Database not updated.<BR>\n";
      $message_error.=$query;
      RenderError($title,$message_error);
      exit();
    }
    $maxbadgeid=mysqli_fetch_object($result)->badgeid;
    sscanf($maxbadgeid,"%d",$x);
    $badgeid=sprintf("%d",$x+1); // convert to num; add 1; convert back to string

    // Create Participants entry.
    $element_array = array('badgeid', 'email', 'password', 'bestway', 'regtype', 'pubsname');
    $value_array=array($badgeid,
		       htmlspecialchars_decode($work_arr['email']),
                       "1111101",
                       "Email",
		       htmlspecialchars_decode($work_arr['regtype']),
		       htmlspecialchars_decode($work_arr['badgename']));
    $message.=submit_table_element($link, $title, "Participants", $element_array, $value_array);

    // Create CongoDump entry.
    $element_array = array('badgeid', 'firstname', 'lastname', 'badgename', 'phone', 'postaddress1', 'postaddress2', 'postcity', 'poststate', 'postzip', 'postcountry');
    $value_array=array($badgeid,
		       htmlspecialchars_decode($work_arr['firstname']),
		       htmlspecialchars_decode($work_arr['lastname']),
		       htmlspecialchars_decode($work_arr['badgename']),
		       htmlspecialchars_decode($work_arr['phone']),
		       htmlspecialchars_decode($work_arr['postaddress1']),
		       htmlspecialchars_decode($work_arr['postaddress2']),
		       htmlspecialchars_decode($work_arr['postcity']),
		       htmlspecialchars_decode($work_arr['poststate']),
		       htmlspecialchars_decode($work_arr['postzip']),
		       htmlspecialchars_decode($work_arr['postcountry']));
    $message.=submit_table_element($link, $title, "CongoDump", $element_array, $value_array);

    // Assign permissions by getting the right Permission Role
    $query="SELECT permroleid FROM PermissionRoles WHERE permrolename='Vendor'";
    list($permissionrows,$permissionheader_array,$permission_array)=queryreport($query,$link,$title,$description,0);
    if ($permissionrows != 1) {
      $message_error.="Somehow there are more or less Permission Rows maping to Vendor.";
      $message_error.="Please check your database for inconsistencies, or suggest a change.\n";
      RenderError($title,$message_error);
      exit();
    }
    $vendorpermrole=$permission_array[1]['permroleid'];

    $element_array = array('badgeid', 'permroleid', 'conid');
    $value_array=array($badgeid, $vendorpermrole, $_SESSION['conid']);
    $message.=submit_table_element($link, $title, "UserHasPermissionRole", $element_array, $value_array);

  } else {  // No longer badgeid 100

    // Participants entry updates
    $pairedvalue_array=array();

    // pubsname is tied to the badgename for vendors, so only update
    // that if the badgename has updated
    if ($work_arr['wasbadgename'] != $work_arr['badgename']) {
      $pairedvalue_array[]=("pubsname='".stripslashes(mysql_real_escape_string(stripslashes($work_arr['badgename']."'"))));
    }

    if ($work_arr['wasemail'] != $work_arr['email']) {
      $pairedvalue_array[]=("email='".stripslashes(mysql_real_escape_string(stripslashes($work_arr['email']."'"))));
    }

    // Password update
    // Check the current and the suggested current for matching then check the two entries for matching.
    if (!empty($work_arr['opassword'])) {
      if ($_SESSION['password'] != md5($work_arr['opassword'])) {
	$message_error.="Current password does not match what is expected, not updating password field.";
      } elseif (($work_arr['npassword']!="") and ($work_arr['npassword']==$work_arr['ncpassword'])) {
	$pairedvalue_array[]=("password='".md5($work_arr['ncpassword'])."'");
      } else {
	$message_error.="New passwords do not match each other.  Not updating password field.";
      }
    }

    // Update if there are changes
    if (!empty($pairedvalue_array)) {
      $message.=update_table_element($link, $title, "Participants", $pairedvalue_array, "badgeid", $badgeid);
    }

    // CongoDump update
    $pairedvalue_array=array();

    // The list of the elements
    $element_array=array('firstname','lastname','badgename','phone','postaddress1','postaddress2','postcity','poststate','postzip','postcountry');

    // Walk the element_array
    for ($i=0; $i<=count($element_array); $i++) {

      // update by key
      if ($work_arr['was'.$element_array[$i]] != $work_arr[$element_array[$i]]) {
	$pairedvalue_array[]=($element_array[$i]."='".stripslashes(mysql_real_escape_string(stripslashes($work_arr[$element_array[$i]]."'"))));
      }
    }

    // Update if there are changes
    if (!empty($pairedvalue_array)) {
      $message.=update_table_element($link, $title, "CongoDump", $pairedvalue_array, "badgeid", $badgeid);
    }
  }

  // Add or Update Bios
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

	// Clean up the biotext.
	$biotext=stripslashes(htmlspecialchars_decode($work_arr[$keyname]));

	// See if there's an update to be checked.
	if ($biotext != $bioinfo[$keyname]) {

	  // Length-check the values.
	  if ((isset($limit_array['max'][$biodest][$biotype])) and (strlen($biotext)>$limit_array['max'][$biodest][$biotype])) {
	    $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	    $message.=" too long (".strlen($biotext)." characters), the limit is ".$limit_array['max'][$biodest][$biotype]." characters.";
	  } elseif ((isset($limit_array['min'][$biodest][$biotype])) and (strlen($biotext)<$limit_array['min'][$biodest][$biotype])) {
	    $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	    $messaage.=" too short (".strlen($biotext)." characters), the limit is ".$limit_array['min'][$biodest][$biotype]." characters.";
	  } else {
	    update_bio_element($link,$title,$biotext,$badgeid,$biotype,$biolang,$biostate,$biodest);
	  }
	}
      }
    }
  }

  // Create/update VendorIs (vendortypeid) entries
  foreach ($work_arr['wasvendortypeid'] as $key => $value) {
    if (($work_arr['wasvendortypeid'][$key]=="not") and
	($work_arr['vendortypeid'][$key]=="checked")) {
      $element_array = array('badgeid', 'vendortypeid');
      $value_array = array($badgeid, $key);
      $message.=submit_table_element($link, $title, "VendorIs", $element_array, $value_array);
    }
    if (($work_arr['wasvendortypeid'][$key]=="indeed") and
	($work_arr['vendortypeid'][$key]!="checked")) {
      $match_string="badgeid=".$badgeid." AND vendortypeid=".$key;
      $message.=delete_table_element($link, $title, "VendorIs",$match_string);
    }
  }

  // Submit a note about what was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($badgeid,
                     $_SESSION['badgeid'],
                     "Created/Updated Vendor Business entry: $message",
		     $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "NotesOnVendors", $element_array, $value_array);

  // Make $message additive (.=) to get all the information
  $message="Database updated successfully with ".$work_arr["badgename"].".<BR>";
  // return array ($message,$message_error);
}

/* Hopefully this will soon go away, due to the above two
   functions doing the job entirely.  Needs testing though.
/* create_vendor and edit_vendor functions.  Need more doc. */
function create_vendor ($participant_arr) {
  global $link,$message,$message_error;

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
  $queryPreExist = "SELECT badgeid FROM Participants where email like \"%".$participant_arr['email']."%\"";
  list($preexistrows,$preexistheader_array,$preexist_array)=queryreport($queryPreExist,$link,$title,$description,0);
  if ($preexistrows > 0) {
    $_POST['badgeid']=$preexist_array[1]['badgeid'];
    $message.=edit_vendor_apply($_POST);
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
    exit();
  }

  // Get next possible badgeid.
  // WAS: "SELECT MAX(badgeid) FROM Participants WHERE badgeid>='1'";
  $query = "SELECT badgeid FROM Participants ORDER BY ABS(badgeid) DESC LIMIT 1";
  $result=mysqli_query($link,$query);
  if (!$result) {
    $message_error="Unrecoverable error updating database.  Database not updated.<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  if (mysqli_num_rows($result)!=1) {
    $message_error="Database query returned unexpected number of rows(1 expected).  Database not updated.<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  $maxbadgeid=mysqli_fetch_object($result)->badgeid;
  //error_log("Zambia: SubmitEditCreateParticipant.php: maxbadgeid: $maxbadgeid");
  sscanf($maxbadgeid,"%d",$x);
  $newbadgeid=sprintf("%d",$x+1); // convert to num; add 1; convert back to string

  // Create Participants entry.
  $element_array = array('badgeid', 'email', 'password', 'bestway', 'regtype', 'pubsname');
  $value_array=array($newbadgeid,
		     htmlspecialchars_decode($participant_arr['email']),
                     "1111101",
                     "Email",
		     htmlspecialchars_decode($participant_arr['regtype']),
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

	// Clean up the biotext.
	$biotext=stripslashes(htmlspecialchars_decode($participant_arr[$keyname]));

	// See if there's an update to be checked.
	if ($biotext != $bioinfo[$keyname]) {

	  // Length-check the values.
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
  }

  // Create CongoDump entry.
  $element_array = array('badgeid', 'firstname', 'lastname', 'badgename', 'phone', 'postaddress1', 'postaddress2', 'postcity', 'poststate', 'postzip', 'postcountry');
  $value_array=array($newbadgeid,
		     htmlspecialchars_decode($participant_arr['firstname']),
		     htmlspecialchars_decode($participant_arr['lastname']),
		     htmlspecialchars_decode($participant_arr['badgename']),
		     htmlspecialchars_decode($participant_arr['phone']),
		     htmlspecialchars_decode($participant_arr['postaddress1']),
		     htmlspecialchars_decode($participant_arr['postaddress2']),
		     htmlspecialchars_decode($participant_arr['postcity']),
		     htmlspecialchars_decode($participant_arr['poststate']),
		     htmlspecialchars_decode($participant_arr['postzip']),
		     htmlspecialchars_decode($participant_arr['postcountry']));
  $message.=submit_table_element($link, $title, "CongoDump", $element_array, $value_array);

  // Assign permissions by getting the right Permission Role
  $query="SELECT permroleid FROM PermissionRoles WHERE permrolename='Vendor'";
  list($permissionrows,$permissionheader_array,$permission_array)=queryreport($query,$link,$title,$description,0);
  if ($permissionrows != 1) {
    $message_error.="Somehow there are more or less Permission Rows maping to Vendor.";
    $message_error.="Please check your database for inconsistencies, or suggest a change.\n";
    RenderError($title,$message_error);
    exit();
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
