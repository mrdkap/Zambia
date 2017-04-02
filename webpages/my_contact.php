<?php
// initialize db, check login, set $badgeid from session
require_once('PartCommonCode.php');
global $participant,$message,$message_error,$message2,$congoinfo;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// LOCALIZATIONS
$title="My Profile";
$description="<P>This is the informaton we have for you</P>\n";
$additionalinfo="<P>Please, read, and if necessary update the below.</P>\n";
$message_error.=$message2;

//Get the reg email (switched to brainstorm coordinator)
//     conrolename like '%Registration%' AND
$query = <<<EOD
SELECT
    email
  FROM
      ConRoles
    JOIN UserHasConRole USING (conroleid)
    JOIN CongoDump USING (badgeid)
  WHERE
    conrolename like '%BrainstormCoord%' AND
    conid=$conid
EOD;

  list($rows,$header_array,$regemail_array)=queryreport($query,$link,$title,$description,0);
  for ($i=1; $i<=$rows; $i++) {
    $regemaillong.=$regemail_array[$i]['email'].",";
  }
  $regemail=rtrim($regemaillong,",");

// Get the various length limits
$limit_array=getLimitArray();

// Get the congo information.
if (getCongoData($badgeid)!=0) {
  RenderError($title,$message_error);
  exit();
}

// Get the bio data, if there is any.
$bioinfo=getBioData($badgeid);

// If there is missing blocks of bio-data, ask for it:
/* We are only presenting the edited bios here, so only a 3-depth
   search happens on biolang, biotypename and biodest to see if they
   were passed (updated) and record the update. */
$biostate='edited'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
  for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
    for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

      // Setup for keyname, to collapse all three variables into one passed name.
      $biotype=$bioinfo['biotype_array'][$i];
      $biolang=$bioinfo['biolang_array'][$j];
      //$biostate=$bioinfo['biostate_array'][$k];
      $biodest=$bioinfo['biodest_array'][$l];
      $keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";
      if (!isset($bioinfo[$keyname])) {$bioinfo[$keyname]="Unset";}
    }
  }
}

// Only do the following, if there was an update to the information.
if (isset($_POST['update'])) {

  /* We are only updating the raw bios here, so only a 3-depth
     search happens on biolang, biotypename and biodest to see if they
     were passed (updated) and record the update. */

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

	// clean up the passed information
	$biostring = stripslashes(htmlspecialchars_decode($_POST[$keyname]));

	/* If the bios are changed first reject the change if user is not allowed to edit bios now
	   and reject submitted bios that are too long otherwise update the bios directly now.*/
	if ($biostring!=$bioinfo[$keyname]) {
	  if (!may_I('EditBio')) {
	    $message_error.="You may not update your bios for publication at this time.\n";
	  } elseif ((isset($limit_array['max'][$biodest][$biotype])) and (strlen($biostring)>$limit_array['max'][$biodest][$biotype])) {
	    $message_error.=ucfirst($biotype)." ($biolang) Biography is too long: ".(strlen($biostring));
	    $message_error.=" characters (maximum limit ".$limit_array['max'][$biodest][$biotype];
	    $message_error.=" characters), so it isn't updated.  Please edit.";
	    $bioinfo[$keyname]=$biostring;
	  } elseif ((isset($limit_array['min'][$biodest][$biotype])) and (strlen($biostring)<$limit_array['min'][$biodest][$biotype])) {
	    $message_error.=ucfirst($biotype)." ($biolang) Biography is too short: ".(strlen($biostring));
	    $message_error.=" characters (minimum limit ".$limit_array['min'][$biodest][$biotype];
	    $message_error.=" characters), so it isn't updated.  Please edit.";
	    $bioinfo[$keyname]=$biostring;
	  } else {
	    update_bio_element($link,$title,$biostring,$badgeid,$biotype,$biolang,$biostate,$biodest);
	    $bioinfo[$keyname]=$biostring;
	  }
	}
      }
    }
  }

  // if the passwords are there, and don't match, reject it.
  $update_password=false;
  if (($_POST['password']!="") OR ($_POST['cpassword']!="")) {
    if ($_POST['password']==$_POST['cpassword']) {
      $update_password=true;
    } else {
      $message_error.="Passwords do not match each other.  Passwords not updated.";
    }
  }

  // If the pubsname is changed, update it.
  $update_pubsname=false;
  if ($_POST['pubsname']!=$participant['pubsname']) {
    if (!may_I('EditBio')) {
      $message_error.="You may not update your name for publication at this time.\n";
    } else {
      $update_pubsname=true;
    }
  }

  // Begin the query:
  $query_start="UPDATE Participants SET ";
  $query="";

  // ... add password ...
  if ($update_password==true) {
    $x = md5($_POST['password']);
    if ($query!="") {$query.=", ";}
    $query.="password=\"$x\"";
    $_SESSION['password']=$x;
  }

  // ... add pubsname and update the session variable ...
  if ($update_pubsname==true) {
    $x=mysql_real_escape_string(stripslashes($_POST['pubsname']),$link);
    if ($query!="") {$query.=", ";}
    $query.="pubsname=\"$x\"";
    $_SESSION['badgename']=$x;
    $participant['pubsname']=$x;
  }

  // ... add bestway ...
  if ($_POST['bestway']!=$participant['bestway']) {
    if ($query!="") {$query.=", ";}
    $query.="bestway=\"".$_POST['bestway']."\"";
    $participant['bestway']=$_POST['bestway'];
  }

  // Check to see if we are actually doing anything, and if so, do it.
  if ($query!="") {
    $query_start.=$query;
    $query=$query_start;
    $query.=" WHERE badgeid=\"".$badgeid."\"";
    if (!mysql_query($query,$link)) {
      $message_error.=$query."<BR>Error updating database.  Database not updated.";
      RenderError($title,$message_error);
      exit();
    }
    $message.="Database updated successfully with participant information.";
  }
  // Update interested if changed
  if ($_POST['interested']!=$participant['interested']) {
    $query ="UPDATE Interested SET ";
    $query.="interestedtypeid=".$_POST['interested']." ";
    $query.="WHERE badgeid=\"".$badgeid."\" AND conid=".$_SESSION['conid'];
    if (!mysql_query($query,$link)) {
      $message.=$query."<BR>Error updating Interested table.  Database not update.";
      echo "<P class=\"errmsg\">".$message."</P>\n";
      return;
    }
    ereg("Rows matched: ([0-9]*)", mysql_info($link), $r_matched);
    if ($r_matched[1]==0) {
      $element_array=array('conid','badgeid','interestedtypeid');
      $value_array=array($_SESSION['conid'], $badgeid, mysql_real_escape_string(stripslashes($_POST['interested'])));
      $message.=submit_table_element($link,"Admin Participants","Interested", $element_array, $value_array);
    } elseif ($r_matched[1]>1) {
      $message.="There might be something wrong with the table, there are multiple interested elements for this year.";
    }
    $participant['interested']=$_POST['interested'];
  }
}

// If no pubsname, copy it from badgename.
if (strlen($participant["pubsname"])<1) {
  $participant["pubsname"]=$congoinfo["badgename"];
}

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

// See if they are on the appropriate level of staff
$ispresenter="T";
if ($element_array[1]['badgeid']!=$badgeid) {$ispresenter="F";}

// Begin the page display.
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Begin the form.
?>

<FORM name="partform" method=POST action="my_contact.php">
  <div id="update_section">
<?php /*  This should not (necessarily) be modifiable by the user.  See Welocme page for more info
    <div class="divlistbox">
      <span class="spanlabcb">I am interested and able to participate in
        programming for <?php echo $_SESSION['conname']; ?>&nbsp;</span>
      <?php $int=$participant['interested']; ?>
      <span class="spanvalcb"><SELECT name=interested class="yesno">
            <OPTION value=0 <?php if ($int==0) {echo "selected";} ?> >&nbsp</OPTION>
            <OPTION value=1 <?php if ($int==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($int==2) {echo "selected";} ?> >No</OPTION></SELECT>
          </span>
      </div>
      */ ?>
    <div id="bestway">
      <span class="spanlabcb">Preferred mode of contact&nbsp;</span>
      <div id="bwbuttons">
<?php
/* For each of the possible ways to contact, email, altcontact, postal address, or phone
 if the element exists in their file, offer it up as a possibility to be their preferred
 means of contact, with whatever they have chosen before, as the checked choice. */
if (strlen($congoinfo['email'])>0) {
  echo "        <div id=\"bwemail\">\n";
  echo "          <input name=\"bestway\" id=\"bwemailRB\" value=\"Email\" type=\"radio\"";
  if ($participant["bestway"]=="Email") {echo " checked";}
  echo">\n";
  echo "          <label for=\"bwemailRB\">Email</label>\n";
  echo "          </div>\n";
}
if (strlen($participant['altcontact'])>0) {
  echo "        <div id=\"bwalt\">\n";
  echo "          <input name=\"bestway\" id=\"bwaltRB\" value=\"AltContact\" type=\"radio\"";
  if ($participant["bestway"]=="AltContact") {echo " checked";}
  echo ">\n";
  echo "          <label for=\"bwaltRB\">Alternative Contact</label>\n";
  echo "          </div>\n";
}
if (strlen($congoinfo['postaddress1'])>0) {
  echo "        <div id=\"bwpmail\">\n";
  echo "          <input name=\"bestway\" id=\"bwpmailRB\" value=\"PostalMail\" type=\"radio\"";
  if ($participant["bestway"]=="PostalMail") {echo " checked";}
  echo">\n";
  echo "          <label for=\"bwpmailRB\">Postal Mail</label>\n";
  echo "          </div>\n";
}
if (strlen($congoinfo['phone'])>0) {
  echo "        <div id=\"bwphone\">\n";
  echo "          <input name=\"bestway\" id=\"bwphoneRB\" value=\"Phone\" type=\"radio\"";
  if ($participant["bestway"]=="Phone") {echo " checked";}
  echo ">\n";
  echo "          <label for=\"bwphoneRB\">Phone</label>\n";
  echo "          </div>\n";
}
?>
        </div>
      </div>
    <div class="password">
      <span class="password2">Change Password&nbsp;</span>
      <span class="value"><INPUT type="password" size="10" name="password"></span>
      </div>
    <div class="password">
      <span class="password2">Confirm New Password&nbsp;</span>
      <span class="value"><INPUT type="password" size="10" name="cpassword"></span>
      </div>
    </div>
    <DIV >
  <div id="congo_section" class="border2222">
    <div class="congo_table">
     <div class="congo_data">
      <span class="label">Badge ID&nbsp;</span>
      <span class="value"><?php echo $badgeid; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">First Name&nbsp;</span>
      <span class="value"><?php echo $congoinfo["firstname"]; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">Last Name&nbsp;</span>
      <span class="value"><?php echo $congoinfo["lastname"]; ?></span>
      </div>
<!--    <div class="congo_data">
      <span class="label">How We Know You&nbsp;</span>
      <span class="value"><?php echo $congoinfo["badgename"]; ?></span>
      </div> -->
    <div class="congo_data">
      <span class="label">How We Know You&nbsp;</span>
      <span class="value"><?php echo $participant["pubsname"]; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">Phone Info&nbsp;</span>
      <span class="value"><?php echo $congoinfo["phone"]; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">Email Address&nbsp;</span>
      <span class="value"><?php echo $congoinfo["email"]; ?></span>
    </div>
    <div class="congo_data">
      <span class="label">Alternative Contact&nbsp;</span>
      <span class="value"><?php echo $participant["altcontact"]; ?></span>
    </div>
    <div class="congo_data">
      <span class="label">Postal Address&nbsp;</span>
      <span class="value"><?php echo $congoinfo["postaddress1"]; ?></span>
      </div>
<?php if (strlen($congoinfo['postaddress2'])>0) { ?>
    <div class="congo_data">
      <span class="label">&nbsp;</span>
      <span class="value"><?php echo $congoinfo["postaddress2"]; ?></span>
      </div>
      <?php } ?>
<?php if ((strlen($congoinfo['postcity'])>0) or (strlen($congoinfo['poststate'])>0) or (strlen($congoinfo['postzip'])>0)) { ?>
    <div class="congo_data">
      <span class="label">&nbsp;</span>
      <span class="value"><?php echo "{$congoinfo['postcity']}, {$congoinfo['poststate']} {$congoinfo['postzip']}"; ?></span>
      </div>
      <?php } ?>
<?php if (strlen($congoinfo['postcountry'])>0) { ?>
    <div class="congo_data">
      <span class="label">&nbsp;</span>
      <span class="value"><?php echo $congoinfo['postcountry']; ?></span>
      </div>
      <?php } ?>
  </div>
  <P class="congo-note">Please confirm your contact information.  If it is
not correct, contact <A href="mailto:<?php echo $regemail ?>">registration</a> with your
current information. This data is downloaded periodically from the registration database, and should be correct within a week.
</div>

<?php
/* Offer up the bio information, with, if it may be edited, the raw and the edited version, the raw in a text-box
 able to be modified.  If it is too long, the changes are retained across the submission, so they can edit from
 that, rather than starting from scratch, or what is actually in there.  If they go away, and come back, without
 fixing it, then it will be restored to what is in the database.  Currently it is limited to just web and book
 and not good, it should be broadended at some point.  If it may not be edited, it just offers up the edited bios
 so they can be seen. */
if (may_I('EditBio')) {
  echo "<HR>\n<BR>\n";
  echo "Your name as you wish us to refer to you&nbsp;&nbsp;";
  echo "<INPUT type=\"text\" size=\"20\" name=\"pubsname\" ";
  echo "value=\"".htmlspecialchars($participant["pubsname"],ENT_COMPAT)."\">\n";
  echo " (Your web and book name are set below.)\n";
  echo "<P>Note: When you update your bio, please give us a few days for our editors to get back to you.\n";
  echo "and your biography will appear immediately following your published name on the page.<BR>\n";
}

/* We are only updating the raw bios here so only a 3-depth search
   happens on biolang and biotypename with the raw offered up for
   editing and the edited offered up for comparison.  This means two
   different keys keynameraw and keynameed are set up. */
$biostateraw='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
$biostateed='edited'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
  for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
    for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

      // Setup for keyname, to collapse all three variables into one passed name.
      $biotype=$bioinfo['biotype_array'][$i];
      $biolang=$bioinfo['biolang_array'][$j];
      // $biostate=$bioinfo['biostate_array'][$k];
      $biodest=$bioinfo['biodest_array'][$l];
      $keynameraw=$biotype."_".$biolang."_".$biostateraw."_".$biodest."_bio";
      $keynameed=$biotype."_".$biolang."_".$biostateed."_".$biodest."_bio";
      $keynamebio="name_".$biolang."_edited_".$biodest."_bio";

      // Skip the "presenter" categories, if is not a presenter
      if (($ispresenter!="T") and ($biodest=="book")) { continue; }
      if (($ispresenter!="T") and ($biodest=="web")) { continue; }

      // Skip the "staff" categories, if is not on staff
      if (($isstaff!="T") and ($biodest=="staffbook")) { continue; }
      if (($isstaff!="T") and ($biodest=="staffweb")) { continue; }

      // Skip the badge-uri and badge-bio cateories
      if (($biodest=="badge") and ($biotype=="uri")) { continue; }
      if (($biodest=="badge") and ($biotype=="bio")) { continue; }

      /* If the edited bio exists, present it.  Add the appropriate
	 biotype "name" before the biotype "bio". */
      if (strlen($bioinfo[$keynameed])>0) {
	if ($biotype=="bio") {
	  echo "<P>".ucfirst($biodest)." ".ucfirst($biotype)." ($biolang): ".$bioinfo[$keynamebio].$bioinfo[$keynameed]."</P>\n";
	} else {
	  echo "<P>".ucfirst($biodest)." ".ucfirst($biotype)." ($biolang): ".$bioinfo[$keynameed]."</P>\n";
	}
      }

      // If the user is allowed to edit their bio, present the raw version for editing.
      if (may_I('EditBio')) {
	echo "<LABEL class=\"spanlabcb\" for=\"$keynameraw\">Change your $biodest $biotype ($biolang) biographical information";
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
	echo "<TEXTAREA rows=\"5\" cols=\"72\" name=\"$keynameraw\">".htmlspecialchars($bioinfo[$keynameraw],ENT_COMPAT)."</TEXTAREA>\n<BR>\n";
      }
    }
  }
}

// Block to pass the submit button and the notification that this isn't the first pass through the form.
?>
<INPUT type="hidden" name="update" value="Yes">
<DIV class="SubmitDiv"><BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON></DIV>
</form>
<?php correct_footer(); ?>
