<?php
/* This function will output the page with the form to add or create a participant
   Variables
     action: "create" or "edit"
     participant_arr: array with all data of record to edit or defaults for create
     permrole_arr: array with possible permission roles
     conrole_arr: array with possilbe con roles
*/
function RenderEditCreateParticipant ($title, $action, $participant_arr) {
  global $link;
  $conid=$_SESSION['conid']; // make it a variable so it can be substituted
  $badgeid=$_SESSION['badgeid']; // make it a variable so it can be substituted
  $perms="'".implode("','",$_SESSION['permission_set'])."'"; // make it a variable so it can be substituted

  /* All of the permission roles, their name (for may_I match), and
     their "notes" for Human Readable match. */
  $query= <<<EOD
SELECT
    permroleid,
    notes AS permrolenotes
  FROM
      PermissionRoles
  WHERE
    permrolename in ($perms)
EOD;

  list($permrolerows,$permroleheader_array,$permrole_arr)=queryreport($query,$link,$title,$description,0);
  if (in_array("This report retrieved no results matching the criteria.",array_keys($permrole_arr))) {
    $permrole_arr=array();
  }

  /* This is the con roles for the reports for the person logged in and
     their "notes" for Human Readable match.
     This might want to become recursive for ConChair? */
  $query= <<<EOD
SELECT
    hasreport,
    conrolenotes
  FROM
      UserHasConRole
    JOIN HasReports USING (conroleid,conid)
    JOIN ConRoles CR ON (hasreport = CR.conroleid)
  WHERE
    badgeid='$badgeid' AND
    conid=$conid
  ORDER BY
    display_order
EOD;

  list($conrolerows,$conroleheader_array,$conrole_arr)=queryreport($query,$link,$title,$description,0);
  if (in_array("This report retrieved no results matching the criteria.",array_keys($conrole_arr))) {
    $conrole_arr=array();
  }

  // Get the various length limits
  $limit_array=getLimitArray();

  //error_log("Zambia: ".print_r($participant_arr,TRUE));

  //Get the bioinfo
  $bioinfo=getBioData($_SESSION['badgeid']);

  // Get the issstaff, ispresenter, and isvendor values
  $ispermlist=$participant_arr['permroleid_list'];

  if (!empty($ispermlist)) {

    $queryIsPerm= <<<EOD
SELECT
    permroleid,
    permrolename
  FROM
      PermissionRoles
  WHERE
    permroleid in ($ispermlist)
EOD;

    list($ispermrolerows,$ispermroleheader_array,$ispermrole_array)=queryreport($queryIsPerm,$link,$title,$description,0);

    $isstaff="F";
    $isvendor="F";
    $ispresenter="F";
    for ($i=1; $i<=$ispermrolerows; $i++) {
      $istest=$ispermrole_array[$i]['permrolename'];
      if ($istest == "Vendor") {
	$isvendor="T";
      }
      if (strpos($istest,"Super") === FALSE) {
      } else {
	$isstaff="T";
      }
      if (($istest == "Participant") or
	  ($istest == "Panelist") or
	  ($istest == "Host") or
	  ($istest == "Demo") or
	  ($istest == "Teacher") or
	  ($istest == "Presenter") or
	  ($istest == "Author") or
	  ($istest == "Organizer") or
	  ($istest == "Performer")) {
	$ispresenter="T";
      }
    }
  }

  ?>
    <DIV class="formbox">
        <FORM name="partform" class="bb"  method=POST action="StaffEditCreateParticipant.php">
            <INPUT type="hidden" name="action" value="<?php echo htmlspecialchars($action,ENT_COMPAT);?>">
            <INPUT type="hidden" name="partid" value="<?php echo $participant_arr["badgeid"]; ?>">
            <INPUT type="hidden" name="password" value="<?php echo $participant_arr["password"]; ?>">
            <INPUT type="hidden" name="update" value="Yes">
            <DIV style="margin: 0.5em; padding: 0em"><TABLE style="margin: 0em; padding: 0em" ><COL width=600><COL>
              <TR style="margin: 0em; padding: 0em">
                <TD style="margin: 0em; padding: 0em">&nbsp;</TD>
                <TD style="margin: 0em; padding: 0em">
                    <BUTTON class="ib" type=submit value="save">Save</BUTTON>
                    </TD></TR></TABLE>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="firstname">First Name: </LABEL><INPUT type="text" size=7 name="firstname" id="firstname" 
                     value="<?php echo htmlspecialchars($participant_arr["firstname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="lastname">Last Name: </LABEL><INPUT type="text" size=13 name="lastname" id="lastname"
                     value="<?php echo htmlspecialchars($participant_arr["lastname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN>&nbsp;<BUTTON type=button onclick="fpopdefaults()" value="noop">Populate Defaults</BUTTON>&nbsp;</SPAN>
                <SPAN><LABEL for="badgeid">Badge ID: <?php echo $participant_arr["badgeid"];?></LABEL>&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="pubsname">Name for Publications: </LABEL><INPUT type="text" size=20 name="pubsname" id="pubsname"
                     value="<?php echo htmlspecialchars($participant_arr["pubsname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="badgename">Badge Name: </LABEL><INPUT type="text" size=20 name="badgename" id="badgename"
                     value="<?php echo htmlspecialchars($participant_arr["badgename"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="interested">Will participate and attend: </LABEL><SELECT name="interested">
                    <?php populate_select_from_table("InterestedTypes", $participant_arr['interested'], " ", FALSE); ?>
                    </SELECT>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="email">Email Address: </LABEL><INPUT type="text" size=36 name="email"
                     value="<?php echo htmlspecialchars($participant_arr["email"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="bestway">Preferred way to be contacted: </LABEL><SELECT name="bestway">
                    <OPTION value="" <?php if ($participant_arr["bestway"]=="") echo "selected";?> >Did not answer</OPTION>
                    <OPTION value="Email" <?php if ($participant_arr["bestway"]=="Email") echo "selected";?> >Email</OPTION>
                    <OPTION value="Phone" <?php if ($participant_arr["bestway"]=="Phone") echo "selected";?> >Phone</OPTION>
                    <OPTION value="SMS" <?php if ($participant_arr["bestway"]=="SMS") echo "selected";?> >SMS</OPTION>
                    <OPTION value="Postal mail" <?php if ($participant_arr["bestway"]=="Postal mail") echo "selected";?> >Postal mail</OPTION>
                    <OPTION value="Twitter DM" <?php if ($participant_arr["bestway"]=="Twitter DM") echo "selected";?> >Twitter DM</OPTION>
                    <OPTION value="Fet Life" <?php if ($participant_arr["bestway"]=="Fet Life") echo "selected";?> >Fet Life</OPTION>
                    <OPTION value="Facebook" <?php if ($participant_arr["bestway"]=="Facebook") echo "selected";?> >Facebook</OPTION>
                    <OPTION value="G+" <?php if ($participant_arr["bestway"]=="G+") echo "selected";?> >G+</OPTION>
                    <OPTION value="Instant Messenger" <?php if ($participant_arr["bestway"]=="Instant Messenger") echo "selected";?> >Instant Messenger</OPTION>
                    </SELECT>
                    </SPAN>
                </DIV>
	    <DIV class="denseform">
		<SPAN><LABEL for="permroleid">Level of participation (At least one must be selected):</LABEL>
		    <?php echo populate_checkbox_block_from_array("permroleid",$participant_arr['permroleid_list'],"permroleid","permrolenotes",$permrole_arr); ?>
		</SPAN>
            </DIV>
	    <DIV class="denseform">
		<SPAN><LABEL for="conroleid">Con Role (If they have one):</LABEL>
		    <?php echo populate_checkbox_block_from_array("conroleid",$participant_arr['conroleid_list'],"hasreport","conrolenotes",$conrole_arr); ?>
		</SPAN
            </DIV>
<?php
              // On migration only give the shortest, most relevant information, the bios stuff can be done/checked later
              if ($action != "migrate") {
?>
            <DIV class="denseform">
                <SPAN><LABEL for="postaddress1">Postal Address line 1: </LABEL><INPUT type="text" size=80 name="postaddress1"
                     value="<?php echo htmlspecialchars($participant_arr["postaddress1"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="postaddress2">Postal Address line 2: </LABEL><INPUT type="text" size=80 name="postaddress2"
                     value="<?php echo htmlspecialchars($participant_arr["postaddress2"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="postcity">Postal City: </LABEL><INPUT type="text" size=20 name="postcity"
                     value="<?php echo htmlspecialchars($participant_arr["postcity"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="poststate">State: </LABEL><INPUT type="text" size=2 name="poststate"
                     value="<?php echo htmlspecialchars($participant_arr["poststate"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="postzip">Zip: </LABEL><INPUT type="text" size=10 name="postzip"
                     value="<?php echo htmlspecialchars($participant_arr["postzip"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                </DIV>
<?php
              } else {
		// values to be migrated, without being presented:
?>
            <INPUT type="hidden" name="postaddress1" value="<?php echo htmlspecialchars($participant_arr["postaddress1"],ENT_COMPAT);?>">
            <INPUT type="hidden" name="postaddress2" value="<?php echo htmlspecialchars($participant_arr["postaddress2"],ENT_COMPAT);?>">
            <INPUT type="hidden" name="postcity" value="<?php echo htmlspecialchars($participant_arr["postcity"],ENT_COMPAT);?>">
            <INPUT type="hidden" name="poststate" value="<?php echo htmlspecialchars($participant_arr["poststate"],ENT_COMPAT);?>">
            <INPUT type="hidden" name="postzip" value="<?php echo htmlspecialchars($participant_arr["postzip"],ENT_COMPAT);?>">
<?php
	      } // close migrate switch
              if ((may_I("SuperProgramming")) or
		  (may_I("SuperVendor")) or
		  (may_I("Liaison")) or
		  (may_I("SuperLiaison"))) {

		/* temporary hack, to help migrate the bios from a 3-depth to a 4-depth array */
		if ((!isset($participant_arr['name_en-us_raw_web_bio'])) or
		    ($participant_arr['name_en-us_raw_web_bio'] == "")) {
		  $participant_arr['name_en-us_raw_web_bio'] = $participant_arr['pubsname'];
		}
		if ((!isset($participant_arr['name_en-us_raw_book_bio'])) or
		    ($participant_arr['name_en-us_raw_book_bio'] == "")) {
		  $participant_arr['name_en-us_raw_book_bio'] = $participant_arr['pubsname'];
		}
		if ((!isset($participant_arr['name_en-us_raw_badge_bio'])) or
		    ($participant_arr['name_en-us_raw_badge_bio'] == "")) {
		  $participant_arr['name_en-us_raw_badge_bio'] = $participant_arr['pubsname'];
		}

		/* We are only updating the raw bios here, so only a 3-depth
		   search happens on biolang, biotypename, and biodest. */
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

		      /* // Skip the "presenter" categories, if is not a presenter
			 if (($ispresenter!="T") and ($biodest=="book")) { continue; }
			 if (($ispresenter!="T") and ($biodest=="web")) { continue; } */

		      // Skip the "staff" categories, if is not staff
		      if (($isstaff!="T") and ($biodest=="staffbook")) { continue; }
		      if (($isstaff!="T") and ($biodest=="staffweb")) { continue; }

		      // Skip the "vendor" categories, if is not vendor
		      if (($isvendor!="T") and ($biotype=="dba")) { continue; }

		      // If is vendor, skip the "pronoun" categories
		      if (($isvendor=="T") and ($biotype=="pronoun")) { continue; }

		      // Skip the badge-uri and badge-bio cateories
		      if (($biodest=="badge") and ($biotype=="uri")) { continue; }
		      if (($biodest=="badge") and ($biotype=="bio")) { continue; }
		      if (($biodest=="badge") and ($biotype=="fetlife")) { continue; }
		      if (($biodest=="badge") and ($biotype=="facebook")) { continue; }
		      if (($biodest=="badge") and ($biotype=="twitter")) { continue; }
		      if (($biodest=="badge") and ($biotype=="dba")) { continue; }

		      // Skip the non-staff, badge-picture category
		      if (($isstaff!="T") and ($biodest=="badge") and ($biotype=="picture")) { continue; }

		      if ($action != "migrate") {
			echo "            <DIV class=\"denseform\">\n";
			echo "                <SPAN><LABEL for=\"$keyname\" style=\"vertical-align: top\">";
			echo ucfirst($biotype)." $biodest ($biolang) Biography";
			$limit_string="";
			if (isset($limit_array['max'][$biodest][$biotype])) {
			  $limit_string.=" maximum ".$limit_array['max'][$biodest][$biotype];
			}
			if (isset($limit_array['min'][$biodest][$biotype])) {
			  $limit_string.=" minimum ".$limit_array['min'][$biodest][$biotype];
			}
			if ($limit_string !="") {
			  echo "<BR>(Limit".$limit_string." characters)";
			}
			echo ": </LABEL>\n";
			echo "                    <TEXTAREA class=\"textlabelarea\" cols=70 name=\"$keyname\" >";
			echo htmlspecialchars($participant_arr[$keyname],ENT_NOQUOTES)."</TEXTAREA>\n";
			echo "                    </SPAN>\n";
			echo "                </DIV>\n";
		      } else {
			echo "            <INPUT type=\"hidden\" name=\"$keyname\" value=\"";
                        echo htmlspecialchars($participant_arr[$keyname],ENT_QUOTES) . "\">\n";
		      }
		    }
		  }
		}
	      }
	      if ($action != "migrate") {
?>
            <DIV class="denseform">
                <SPAN><LABEL for="altcontact" style="vertical-align: top">Alternative ways to contact: </LABEL>
                    <TEXTAREA class="textlabelarea" cols=70 name="altcontact" ><?php echo htmlspecialchars($participant_arr["altcontact"],ENT_NOQUOTES);?></TEXTAREA>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
            <SPAN><LABEL for="note" style="vertical-align: top">Log Note:</LABEL>
		<TEXTAREA class="textlabelarea" name="note" rows=2 cols=72>Participant entry <?php echo htmlspecialchars($action);?></TEXTAREA>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="prognotes" style="vertical-align: top">Additional Participant notes: </LABEL>
                    <TEXTAREA class="textlabelarea" cols=70 name="prognotes" ><?php echo htmlspecialchars($participant_arr["prognotes"],ENT_NOQUOTES);?></TEXTAREA>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="phone">Phone: </LABEL><INPUT type="text" size=14 name="phone"
                     value="<?php echo htmlspecialchars($participant_arr["phone"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
<?php
	      } else {  // end if not migrate (and add the denseform from the above sundered div
		echo "            <INPUT type=\"hidden\" name=\"altcontact\" value=\"";
                echo htmlspecialchars($participant_arr["altcontact"],ENT_NOQUOTES) . "\">\n";
		echo "            <INPUT type=\"hidden\" name=\"prognotes\" value=\"";
                echo htmlspecialchars($participant_arr["prognotes"],ENT_NOQUOTES) . "\">\n";
		echo "            <INPUT type=\"hidden\" name=\"phone\" value=\"";
                echo htmlspecialchars($participant_arr["phone"],ENT_COMPAT) . "\">\n";
		echo "            <INPUT type=\"hidden\" name=\"note\" value=\"Migrate participant to this con-instance\">\n";
		echo "            <DIV class=\"denseform\">\n";
}
?>
                <SPAN><LABEL for="regtype">Registration Type: </LABEL><SELECT name="regtype">
                    <?php echo populate_select_from_query_inline("SELECT regtype, regtype FROM RegTypes", $participant_arr['regtype'], "SELECT", FALSE); ?>
                    </SELECT>
                </DIV>
            <DIV style="margin: 0.5em; padding: 0em"><TABLE style="margin: 0em; padding: 0em" ><COL width=600><COL>
              <TR style="margin: 0em; padding: 0em">
                <TD style="margin: 0em; padding: 0em">&nbsp;</TD>
                <TD style="margin: 0em; padding: 0em">
								<?php /*                    <BUTTON class="ib" type=reset value="reset">Reset</BUTTON> Trimmed for sanity purposes */ ?>
                    <BUTTON class="ib" type=submit value="save">Save</BUTTON>
                    </TD></TR></TABLE>
                </DIV>
      </FORM>
    </DIV>
<?php } ?>
